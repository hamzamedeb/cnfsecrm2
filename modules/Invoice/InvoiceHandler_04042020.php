<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */
include_once 'include/Webservices/Revise.php';
include_once 'include/Webservices/Retrieve.php';
require_once('modules/Cashflow4You/Cashflow4You.php');

class InvoiceHandler extends VTEventHandler {

    function handleEvent($eventName, $entityData) {

        $moduleName = $entityData->getModuleName();
// Validate the event target
        
                $monfichier = fopen('debug_financ.txt', 'a+');
        fputs($monfichier, "\n" . "moduleName " . $moduleName);
        fclose($monfichier);
        if ($moduleName != 'Invoice') {
            return;
        }
        //Get Current User Information
        global $current_user, $currentModule;


//        $r1 = print_r($entityData, true);
//        $monfichier = fopen('debug_test4.txt', 'a+');
//        fputs($monfichier, "\n" . "valuetest " . $r. " valuetet " . $r1);
//        fclose($monfichier);

        /**
         * Adjust the balance amount against total & received amount
         * NOTE: beforesave the total amount will not be populated in event data.
         */
        if ($eventName == 'vtiger.entity.aftersave') {
         $monfichier = fopen('debug_financ.txt', 'a+');
        fputs($monfichier, "\n" . "eventName " . $eventName);
        fclose($monfichier);
// Trigger from other module (due to indirect save) need to be ignored - to avoid inconsistency.
//            if ($currentModule != 'Invoice') /* uni_cnfsecrm 03042020 */
//                return;

            $entityDelta = new VTEntityDelta();

            $oldCurrency = $entityDelta->getOldValue($entityData->getModuleName(), $entityData->getId(), 'currency_id');
            $newCurrency = $entityDelta->getCurrentValue($entityData->getModuleName(), $entityData->getId(), 'currency_id');
            $oldConversionRate = $entityDelta->getOldValue($entityData->getModuleName(), $entityData->getId(), 'conversion_rate');
            $db = PearDatabase::getInstance();
            $wsid = vtws_getWebserviceEntityId('Invoice', $entityData->getId());
            $wsrecord = vtws_retrieve($wsid, $current_user);
            if ($oldCurrency != $newCurrency && $oldCurrency != '') {
                if ($oldConversionRate != '') {
                    $wsrecord['received'] = floatval(($wsrecord['received'] / $oldConversionRate) * $wsrecord['conversion_rate']);
                }
            }
            $wsrecord['balance'] = floatval($wsrecord['hdnGrandTotal'] - $wsrecord['received']);
            if ($wsrecord['balance'] == 0) {
                $wsrecord['invoicestatus'] = 'Paid';
            }
                     $monfichier = fopen('debug_financ.txt', 'a+');
        fputs($monfichier, "\n" . "invoiceid " . $entityData->getId());
        fclose($monfichier);
            $query = "UPDATE vtiger_invoice SET balance=?,received=? WHERE invoiceid=?";
            $db->pquery($query, array($wsrecord['balance'], $wsrecord['received'], $entityData->getId()));

            /* uni_cnfsecrm - mise à jour numéro de facture */
            $invoiceid = $entityData->getId();
            $datefacture = $entityData->get('createdtime');
            $invoice_no = $entityData->get('invoice_no');
            $numero_facture = $entityData->get('cf_1033');
            $avoir = $entityData->get('cf_1078');

            $datefacture = strtotime($datefacture);
            $datefacture = date("Ymd", $datefacture);

            $salesorder_id_array = explode("x", $wsrecord['salesorder_id']);

            $financementQuery = 'SELECT financement FROM vtiger_salesorder WHERE salesorderid=?';
            $financementParams = array($salesorder_id_array[1]);
            $financementResult = $db->pquery($financementQuery, $financementParams);
            $financementCount = $db->num_rows($financementResult);
            $financement = floatval($db->query_result($financementResult, 0, 'financement'));

            $cnverfa = $_REQUEST["cnverfa"];
            $isAvoir = $_REQUEST["isAvoir"];
            $numfacture_initial = $_REQUEST["numfacture_initial"];

            if ($isAvoir != "true") {
//                $numero_facture_complet = $invoice_no . '-' . $datefacture; //VA
//                $sql = "UPDATE vtiger_invoicecf SET cf_1033=? WHERE invoiceid=?";
//                $db->pquery($sql, array($numero_facture_complet, $invoiceid));
            }

            $cnverfa1 = 1;
            if ($wsrecord['source'] != 'FINANCEUR' && ($cnverfa == 1 || $cnverfa1 == 1) && $financement > 0) {
                /* parcourir la liste des financements */
                $financeursQuery = 'SELECT vendorid,montant,tva,ttc FROM vtiger_inventoryfinanceurrel WHERE id=?';
                $financeursParams = array($entityData->getId());
                $financeursResult = $db->pquery($financeursQuery, $financeursParams);
                $financeursCount = $db->num_rows($financeursResult);

                $hdnSubTotal = $wsrecord['hdnSubTotal'];
                $adjustment = 0;
                $totalTTC = $wsrecord['hdnGrandTotal'];
                $s_h_amount = $wsrecord['hdnS_H_Amount'];
                $taxtype = $wsrecord['hdnTaxType'];
                $s_h_percent = 0;
                $description = $wsrecord['description'];
                $salesorder_id_array = explode("x", $wsrecord['salesorder_id']);
                $account_id_array = explode("x", $wsrecord['account_id']);
                $assigned_user_id_array = explode("x", $wsrecord['assigned_user_id']);
                $currency_id_array = explode("x", $wsrecord['currency_id']);
                $tax1 = $wsrecord['tax1'];
                $discount_percent = $wsrecord['hdnDiscountPercent'];
                $discount_amount = $wsrecord['hdnDiscountAmount'];

                $total_financement = 0;
                $paye_par_financeur = 0;
                for ($j = 0; $j < $financeursCount; $j++) {
                    $vendorid = $db->query_result($financeursResult, $j, 'vendorid');
                    $montant = $db->query_result($financeursResult, $j, 'montant');
                    $tva = $db->query_result($financeursResult, $j, 'tva');
                    $ttc = $db->query_result($financeursResult, $j, 'ttc');

                    $hdnSubTotal_financeur = $montant;
                    $totalTTC_financeur = $ttc;
                    $received = 0;
                    $balance = 0;
                    $tax2 = 0;
                    $tax3 = 0;

                    $montant_a_paye = $hdnSubTotal - $discount_amount;
//echo $montant_a_paye . "<br/>";
//echo $hdnSubTotal_financeur . "<br/>";
                    if (number_format($montant_a_paye, 2, '.', '') == number_format($hdnSubTotal_financeur, 2, '.', '')) {
                        $paye_par_financeur = 1;
                        $query = "UPDATE vtiger_invoicecf SET cf_1004=? WHERE invoiceid=?";
                        $db->pquery($query, array("Financeur", $entityData->getId()));
                        $query = "UPDATE vtiger_invoice SET financeur=?,pre_tax_total=?,total=?,financement=? WHERE invoiceid=?";
                        $db->pquery($query, array($vendorid, $hdnSubTotal_financeur, $totalTTC_financeur, 0, $entityData->getId()));
                        continue;
                    }
                    $montantclient = floatval($hdnSubTotal) + floatval($s_h_amount) - floatval($hdnSubTotal_financeur) - floatval($discount_amount);

                    //selectionner l'id des factures des financeur
                    $idFinanceur = array();
                    $financeurIdQuery = 'SELECT vtiger_invoice.invoiceid,received
                        FROM vtiger_invoice 
                        INNER JOIN vtiger_invoicecf on vtiger_invoicecf.invoiceid = vtiger_invoice.invoiceid
                        WHERE salesorderid=? and cf_1004 = ?';
                    $financeurIdParams = array($salesorder_id_array[1], 'Financeur');
                    $financeurIdResult = $db->pquery($financeurIdQuery, $financeurIdParams);
                    $financeurIdCount = $db->num_rows($financeurIdResult);
                    if ($financeurIdCount) {
                        for ($i = 0; $i < $financeurIdCount; $i++) {
                            $idFinanceur[$i] = $db->query_result($financeurIdResult, $i, 'invoiceid');
                            $recuFinanceur[$i] = $db->query_result($financeurIdResult, $i, 'received');
                        }
                    }
                    //fin id facture financeur

                    /* Debut création facture fournisseur $j */
                    $focus = new Invoice();
                    if ($financeurIdCount > 0) {
                        if ($idFinanceur[$j] > 0) {
                            $focus->mode = 'edit';
                            $focus->id = $idFinanceur[$j];
                        } else if ($idFinanceur[$j] == '') {
                            $focus->mode = 'create';
                            $recuFinanceur[$j] = 0;
                        }
                    } else {
                        $focus->mode = 'create';
                    }
                    $focus->column_fields['subject'] = $wsrecord['subject']; //V 
                    $focus->column_fields['salesorder_id'] = $salesorder_id_array[1]; //V                
                    $focus->column_fields['contact_id'] = $wsrecord['contact_id']; //V 
                    $focus->column_fields['invoicedate'] = $wsrecord['invoicedate'];
                    $focus->column_fields['duedate'] = $wsrecord['duedate'];
                    $focus->column_fields['cf_1026'] = $wsrecord['cf_1026'];
                    $focus->column_fields['salle'] = $wsrecord['salle'];
                    $focus->column_fields['lieu'] = $wsrecord['lieu'];
                    $focus->column_fields['cf_1028'] = $wsrecord['cf_1028'];
                    $focus->column_fields['cf_1035'] = $wsrecord['cf_1035'];
                    $focus->column_fields['txtAdjustment'] = $adjustment;
                    $focus->column_fields['salescommission'] = $wsrecord['salescommission'];
                    $focus->column_fields['exciseduty'] = $wsrecord['exciseduty'];
                    $focus->column_fields['hdnSubTotal'] = $hdnSubTotal;
                    $focus->column_fields['hdnGrandTotal'] = $totalTTC_financeur;
                    $focus->column_fields['hdnTaxType'] = $taxtype;
                    $focus->column_fields['hdnDiscountPercent'] = $discount_percent; //VA 
                    $focus->column_fields['hdnDiscountAmount'] = $discount_amount; //VA  
                    $focus->column_fields['tax1'] = $tax1; //VA
                    $focus->column_fields['tax2'] = $tax2;
                    $focus->column_fields['tax3'] = $tax3;
                    $focus->column_fields['hdnS_H_Amount'] = $s_h_amount;
                    $focus->column_fields['account_id'] = $account_id_array[1]; //V  
                    $focus->column_fields['invoicestatus'] = $wsrecord['invoicestatus'];
                    $focus->column_fields['assigned_user_id'] = $assigned_user_id_array[1];
                    $focus->column_fields['currency_id'] = $currency_id_array[1];
                    $focus->column_fields['conversion_rate'] = $wsrecord['conversion_rate'];
                    $focus->column_fields['bill_street'] = $wsrecord['bill_street']; //V
                    $focus->column_fields['bill_city'] = $wsrecord['bill_city']; //V
                    $focus->column_fields['bill_state'] = $wsrecord['bill_state']; //V
                    $focus->column_fields['bill_code'] = $wsrecord['bill_code']; //V
                    $focus->column_fields['bill_country'] = $wsrecord['bill_country']; //V
                    $focus->column_fields['bill_pobox'] = $wsrecord['bill_pobox']; //V                    
                    $focus->column_fields['description'] = $description;
                    $focus->column_fields['terms_conditions'] = $wsrecord['terms_conditions']; //V                 
                    $focus->column_fields['pre_tax_total'] = $wsrecord['pre_tax_total'];
                    $focus->column_fields['received'] = $recuFinanceur[$j];
                    $focus->column_fields['balance'] = $balance;
                    $focus->column_fields['hdnS_H_Percent'] = $s_h_percent;
                    $focus->column_fields['source'] = 'FINANCEUR';
                    $focus->column_fields['tags'] = $wsrecord['tags'];
                    $focus->column_fields['cf_1004'] = 'Financeur';
                    $focus->column_fields['financeur'] = $vendorid;

                    //$focus->save("Invoice");
                    $return_id = $focus->id;
                    $balance = $hdnSubTotal_financeur - $recuFinanceur[$j];
                    $requete_update = "update vtiger_invoice set subtotal=?,adjustment=?,total=?,taxtype=?,discount_percent=?,discount_amount=?,s_h_amount=?,pre_tax_total=?,s_h_percent=?,balance=?,financement=?,montantclient=? where invoiceid=?";
                    $result_update = $db->pquery($requete_update, array($hdnSubTotal, $adjustment, $totalTTC_financeur, $taxtype, $discount_percent, $discount_amount, $s_h_amount, $hdnSubTotal_financeur, $s_h_percent, $balance, 0, $montantclient, $return_id));

                    $serviceid = $LineItems_FinalDetails["hdnProductId"];

                    if ($serviceid != '') {
                        $LineItems_FinalDetails = $wsrecord['LineItems_FinalDetails'];
//echo "count " . count($LineItems_FinalDetails);
                        for ($i = 0; $i < count($LineItems_FinalDetails); $i++) {
                            $purchaseCost = $LineItems_FinalDetails["purchaseCost"];
                            $margin = $LineItems_FinalDetails["margin"];
                            $productDeleted = $LineItems_FinalDetails["productDeleted"];
                            $entityType = $LineItems_FinalDetails["entityType"];
                            $hdnProductId = $LineItems_FinalDetails["hdnProductId"];
                            $productName = $LineItems_FinalDetails["productName"];
                            $hdnProductcode = $LineItems_FinalDetails["hdnProductcode"];
                            $productDescription = $LineItems_FinalDetails["productDescription"];
                            $comment = $LineItems_FinalDetails["comment"];
                            $qtyInStock = $LineItems_FinalDetails["qtyInStock"];
                            $qty = $LineItems_FinalDetails["qty"];
                            $listPrice = $LineItems_FinalDetails["listPrice"];
                            $unitPrice = $LineItems_FinalDetails["unitPrice"];
                            $productTotal = $LineItems_FinalDetails["productTotal"];
                            $subproduct_ids = $LineItems_FinalDetails["subproduct_ids"];
                            $discount_percent = $LineItems_FinalDetails["discount_percent"];
                            $discount_amount = $LineItems_FinalDetails["discount_amount"];
                            $checked_discount_zero = $LineItems_FinalDetails["checked_discount_zero"];
                            $tarif = $LineItems_FinalDetails["tarif"];
                            $nbr_jours = $LineItems_FinalDetails["nbr_jours"];
                            $nbr_heures = $LineItems_FinalDetails["nbr_heures"];
                            $base_heure = $LineItems_FinalDetails["base_heure"];
                            $nature_calcul = $LineItems_FinalDetails["nature_calcul"];
                            $par_personne = $LineItems_FinalDetails["par_personne"];
                            $listpriceinter = $LineItems_FinalDetails["listpriceinter"];
                            $listpriceintra = $LineItems_FinalDetails["listpriceintra"];
                            $discountTotal = $LineItems_FinalDetails["discountTotal"];
                            $totalAfterDiscount = $LineItems_FinalDetails["totalAfterDiscount"];
                            $taxTotal = $LineItems_FinalDetails["taxTotal"];
                            $netPrice = $LineItems_FinalDetails["netPrice"];

                            $query = 'INSERT INTO vtiger_inventoryproductrel(id, productid, sequence_no, quantity, listprice, comment, description, purchase_cost, margin,nbrjours,nbrheures,baseheures,naturecalcul,parpersonne,listpriceinter,listpriceintra,tarif)
					VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
                            $qparams = array($return_id, $hdnProductId, $i, $qty, $listPrice, $comment, $description, $purchaseCost, $margin, $nbr_jours, $nbr_heures, $base_heure, $nature_calcul, $par_personne, $listpriceinter, $listpriceintra, $tarif);
                            $db->pquery($query, $qparams);

                            $lineitem_id = $db->getLastInsertID();
                        }
                        $frais_de_déplacement = $wsrecord['frais_de_déplacement'];
                        $frais_de_déplacement_shtax1 = $wsrecord['frais_de_déplacement_shtax1'];
                        $frais_de_repas = $wsrecord['frais_de_repas'];
                        $frais_de_repas_shtax1 = $wsrecord['frais_de_repas_shtax1'];
                        $frais_hébérgement = $wsrecord['frais_hébérgement'];
                        $frais_hébérgement_shtax1 = $wsrecord['frais_hébérgement_shtax1'];

                        $chargesInfo = array();
                        if (isset($LineItems_FinalDetails['chargesAndItsTaxes'])) {
                            $chargesInfo = $LineItems_FinalDetails['chargesAndItsTaxes'];
                        }
                        $db->pquery('INSERT INTO vtiger_inventorychargesrel VALUES (?, ?)', array($return_id, Zend_Json::encode($chargesInfo)));
                    }
                }
            }
            if ($avoir == "on" && $isAvoir == "true") {
                $result = $db->pquery("SELECT invoice_no,cf_1033 FROM vtiger_invoice 
                        INNER JOIN vtiger_invoicecf on vtiger_invoicecf.invoiceid = vtiger_invoice.invoiceid 
                        WHERE vtiger_invoice.invoiceid = ?", array($entityData->getId()));
                $invoice_no_select = $db->query_result($result, 0, 'invoice_no');
                $numero_facture_select = $db->query_result($result, 0, 'cf_1033');

                $invoice_no_select_avoir = str_replace("FA", "AV", $invoice_no_select);
                $dateAvoir = date("Ymd");
                $numero_facture_select_avoir = $invoice_no_select_avoir . '-' . $dateAvoir; //VA

                $query = "UPDATE vtiger_invoicecf SET cf_1039=?,cf_1033=? WHERE invoiceid=?";
                $db->pquery($query, array($numero_facture_select, $numero_facture_select_avoir, $entityData->getId()));

                $query = "UPDATE vtiger_invoice SET invoice_no=?,invoicestatus=? WHERE invoiceid=?";
                $db->pquery($query, array($invoice_no_select_avoir, 'Paid', $entityData->getId()));

                $query = "UPDATE vtiger_invoice SET invoicestatus=? WHERE invoiceid=?";
                $db->pquery($query, array('Avoir', $numfacture_initial));

                $focus_cash = new Cashflow4You();
                $focus_cash->mode = 'create';
                $Date = strtotime($dateAvoir);
                $Date = date("Y-m-d", $Date);
                $focus_cash->column_fields['assigned_user_id'] = 1;
                $focus_cash->column_fields['cashflow4youname'] = 'Paiement';
                $focus_cash->column_fields['cashflow4you_paytype'] = 'Incoming';
                $focus_cash->column_fields['paymentdate'] = $Date;
                $focus_cash->column_fields['cashflow4you_paymethod'] = "Chèque"; //VA
                $focus_cash->column_fields['description'] = "";
                $focus_cash->column_fields['relationid'] = $numfacture_initial;
                $focus_cash->column_fields['paymentamount'] = $totalTTC;
                $focus_cash->column_fields['transactionid'] = "avoir";

                $focus_cash->save("Cashflow4You");
            }

//unicnfsecrm_gestimpaye_00 : mise a jour date echeance 
            $idinvoice = $entityData->getId();

            $querydatesession = "SELECT vtiger_activity.date_start,vtiger_activity.due_date,vtiger_activity.activityid,vtiger_invoice.invoicedate 
                FROM vtiger_activity 
                INNER JOIN vtiger_salesorder on vtiger_activity.activityid = vtiger_salesorder.session 
                INNER JOIN vtiger_invoice on vtiger_salesorder.salesorderid = vtiger_invoice.salesorderid 
                WHERE vtiger_invoice.invoiceid = ?";
            $resultdatesession = $db->pquery($querydatesession, array($idinvoice));
            $date_debut_session = $db->query_result($resultdatesession, 0, 'date_start');
            $date_fin_session = $db->query_result($resultdatesession, 0, 'due_date');
            $date_invoice = $db->query_result($resultdatesession, 0, 'invoicedate');

            $date_aujourdui = date("Y-m-d");
            $date_echeance = "";
            if ($date_fin_session < $date_aujourdui) {
                $date_echeance = $date_invoice;
            } else {
                $date_echeance = $date_debut_session;
            }
            $queryinvoice = "UPDATE vtiger_invoice SET duedate=? WHERE invoiceid=?";
            $db->pquery($queryinvoice, array($date_echeance, $idinvoice));
            //fin unicnfsecrm_gestimpaye_00 : mise a jour date echeance 
            //unicnfsecrm_mod_12

            $queryDateCreate = 'SELECT vtiger_crmentity.createdtime FROM vtiger_crmentity WHERE vtiger_crmentity.crmid = ?';
            $paramsDateCreate = array($idinvoice);
            $invoiceDateCreate = $db->pquery($queryDateCreate, $paramsDateCreate);
            $a = substr($invoiceDateCreate, 12, 23);
            $dateinvoice = substr($a, 0, 11);

            $queryUpdateDate = 'UPDATE vtiger_invoice SET invoicedate=? WHERE invoiceid=?';
            $paramsUpdateDate = array($dateinvoice, $idinvoice);
            $resultUpdateDate = $db->pquery($queryUpdateDate, $paramsUpdateDate);

            // fin unicnfsecrm_mod_12
            // unicnfsecrm_mod_24
            $idConvention = $entityData->get('salesorder_id');

            if (($idConvention != null) || $idConvention != '') {
                $queryUpdateConvention = 'UPDATE vtiger_salesordercf SET vtiger_salesordercf.cf_1197=? WHERE vtiger_salesordercf.salesorderid=?';
                $paramsUpdateConvention = array('Oui', $idConvention);
                $resultUpdateConvention = $db->pquery($queryUpdateConvention, $paramsUpdateConvention);
            }
            //fin unicnfsecrm_mod_24
            //unicnfsecrm_mod_28
            if (($idConvention != null) || $idConvention != '') {
                $idinvoice = $entityData->getId();
                $querySession = 'SELECT vtiger_salesorder.session FROM vtiger_salesorder where vtiger_salesorder.salesorderid = ?';
                $paramsSession = array($idConvention);
                $resultSession = $db->pquery($querySession, $paramsSession);
                $idSession = $db->query_result($resultSession, 0, 'session');

                $queryUpdateInvoice = 'UPDATE vtiger_invoice SET vtiger_invoice.session=? WHERE vtiger_invoice.invoiceid=?';
                $paramsUpdatInvoice = array($idSession, $idinvoice);
                $resultUpdateInvoice = $db->pquery($queryUpdateInvoice, $paramsUpdatInvoice);
            }
            //fin unicnfsecrm_mod_28 
        }
    }

}

?>