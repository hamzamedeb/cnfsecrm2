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

class InvoiceHandler extends VTEventHandler {

    function handleEvent($eventName, $entityData) {

        $moduleName = $entityData->getModuleName();

// Validate the event target
        if ($moduleName != 'Invoice') {
            return;
        }

//Get Current User Information
        global $current_user, $currentModule;

        /**
         * Adjust the balance amount against total & received amount
         * NOTE: beforesave the total amount will not be populated in event data.
         */
        if ($eventName == 'vtiger.entity.aftersave') {
// Trigger from other module (due to indirect save) need to be ignored - to avoid inconsistency.
            if ($currentModule != 'Invoice')
                return;
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
            $query = "UPDATE vtiger_invoice SET balance=?,received=? WHERE invoiceid=?";
            $db->pquery($query, array($wsrecord['balance'], $wsrecord['received'], $entityData->getId()));

            $cnverfa = $_REQUEST["cnverfa"];
            if ($wsrecord['source'] != 'FINANCEUR' && $cnverfa == 1) {
                /* parcourir la liste des financements */
                $financeursQuery = 'SELECT vendorid,montant,tva,ttc FROM vtiger_inventoryfinanceurrel WHERE id=?';
                $financeursParams = array($entityData->getId());
                $financeursResult = $db->pquery($financeursQuery, $financeursParams);
                $financeursCount = $db->num_rows($financeursResult);

                $hdnSubTotal = $wsrecord['hdnSubTotal'];
                $adjustment = 0;
                $totalTTC = $wsrecord['hdnGrandTotal'];
                $taxtype = $wsrecord['hdnTaxType'];
                $s_h_amount = 0;
                $s_h_percent = 0;
                $description = $wsrecord['description'];
                $salesorder_id_array = explode("x", $wsrecord['salesorder_id']);
                $account_id_array = explode("x", $wsrecord['account_id']);
                $assigned_user_id_array = explode("x", $wsrecord['assigned_user_id']);
                $currency_id_array = explode("x", $wsrecord['currency_id']);

                $hdnSubTotal_client = $hdnSubTotal;
                $totalTTC_client = $totalTTC;
                $total_financement = 0;
                for ($j = 0; $j < $financeursCount; $j++) {
                    $vendorid = $db->query_result($financeursResult, $j, 'vendorid');
                    $montant = $db->query_result($financeursResult, $j, 'montant');
                    $tva = $db->query_result($financeursResult, $j, 'tva');
                    $ttc = $db->query_result($financeursResult, $j, 'ttc');
                    /* Debut création facture fournisseur $j */
                    $focus = new Invoice();
                    $focus->mode = 'create';
                    $focus->column_fields['subject'] = $wsrecord['subject']; //V
                    $focus->column_fields['salesorder_id'] = $salesorder_id_array[1]; //V                
                    $focus->column_fields['contact_id'] = $wsrecord['contact_id']; //V 
                    $focus->column_fields['invoicedate'] = $wsrecord['invoicedate'];
                    $focus->column_fields['duedate'] = $wsrecord['duedate'];

                    $hdnSubTotal_financeur = $montant;
                    $totalTTC_financeur = $ttc;
                    $total_financement += $hdnSubTotal_financeur;
                    $received = 0;
                    $balance = $totalTTC_financeur;
                    //echo " Création Facture financeur avec le montant " . $hdnSubTotal_financeur . " HT - " . $totalTTC_financeur . " TTC " . "<br/>";
                    $hdnSubTotal_client = $hdnSubTotal_client;
                    $discount_percent = $wsrecord['hdnDiscountPercent'];
                    $discount_amount = $wsrecord['hdnDiscountAmount'];
                    $pre_tax_total = $hdnSubTotal_client - floatval($hdnSubTotal_financeur);
                    $totalTTC_client = $totalTTC_client - floatval($totalTTC_financeur);

                    $focus->column_fields['txtAdjustment'] = $adjustment;
                    $focus->column_fields['salescommission'] = $wsrecord['salescommission'];
                    $focus->column_fields['exciseduty'] = $wsrecord['exciseduty'];
                    $focus->column_fields['hdnSubTotal'] = $hdnSubTotal_financeur;
                    $focus->column_fields['hdnGrandTotal'] = $totalTTC_financeur;
                    $focus->column_fields['hdnTaxType'] = $taxtype;
                    $focus->column_fields['hdnDiscountPercent'] = $discount_percent; //V 
                    $focus->column_fields['hdnDiscountAmount'] = $discount_amount; //V  
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
                    $focus->column_fields['received'] = $received;
                    $focus->column_fields['balance'] = $balance;
                    $focus->column_fields['hdnS_H_Percent'] = $s_h_percent;
                    $focus->column_fields['source'] = 'FINANCEUR';
                    $focus->column_fields['tags'] = $wsrecord['tags'];
                    $focus->column_fields['cf_1004'] = 'Financeur';
                    $focus->column_fields['financeur'] = $vendorid;

                    $focus->save("Invoice");
                    $return_id = $focus->id;
                    $requete_update = "update vtiger_invoice set subtotal=?,adjustment=?,total=?,taxtype=?,discount_percent=?,discount_amount=?,s_h_amount=?,pre_tax_total=?,s_h_percent=?,balance=? where invoiceid=?";
                    $result_update = $db->pquery($requete_update, array($hdnSubTotal_financeur, $adjustment, $totalTTC_financeur, $taxtype, $discount_percent, $discount_amount, $s_h_amount, $hdnSubTotal_financeur, $s_h_percent, $balance, $return_id));

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

                //echo " Mise à jour Facture Client avec le montant " . $hdnSubTotal_client . " HT - " . $totalTTC_client . " TTC " . "<br/>";
                $query = "UPDATE vtiger_invoice SET subtotal=?,pre_tax_total=?,total=?,financement=?,balance=? WHERE invoiceid=?";
                $db->pquery($query, array($hdnSubTotal_client, $pre_tax_total, $totalTTC_client, $total_financement, $totalTTC_client, $entityData->getId()));
            }
        }
    }

}

?>