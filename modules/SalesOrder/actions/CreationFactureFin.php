<?php

// //creation facture financeur 
//unicnfsecrm_mod_55
include_once 'include/Webservices/Revise.php';
include_once 'include/Webservices/Retrieve.php';
require_once('modules/Cashflow4You/Cashflow4You.php');
require_once('modules/Invoice/Invoice.php');
include_once 'data/VTEntityDelta.php';

class SalesOrder_CreationFactureFin_Action extends Vtiger_Action_Controller {

    function process(Vtiger_Request $request) {
        global $adb, $current_user;
        $salesorder_id = $request->get('record');
        $response = new Vtiger_Response();

        $currentModule = 'Invoice';

        $entityDelta = new VTEntityDelta();
        $db = PearDatabase::getInstance();

        $ConventionsQuery = 'SELECT *
                        FROM vtiger_salesorder 
                        INNER JOIN vtiger_salesordercf on vtiger_salesordercf.salesorderid = vtiger_salesorder.salesorderid   
                        INNER JOIN vtiger_sobillads on vtiger_sobillads.sobilladdressid = vtiger_salesorder.salesorderid
                        INNER JOIN vtiger_crmentity on vtiger_crmentity.crmid = vtiger_salesorder.salesorderid
                        WHERE .vtiger_salesorder.salesorderid=? ';
        $ConventionsParams = array($salesorder_id);
        $ConventionsResult = $db->pquery($ConventionsQuery, $ConventionsParams);
        $ConventionsCount = $db->num_rows($ConventionsResult);
        $subject = str_replace("&amp;", "&", $db->query_result($ConventionsResult, 0, 'subject'));
        $subject = html_entity_decode($subject);
        $total = $db->query_result($ConventionsResult, 0, 'total');
        $hdnSubTotal = $db->query_result($ConventionsResult, 0, 'subtotal');
        $taxtype = $db->query_result($ConventionsResult, 0, 'taxtype');
        $discount_percent = $db->query_result($ConventionsResult, 0, 'discount_percent');
        $discount_amount = $db->query_result($ConventionsResult, 0, 'discount_amount');
        $s_h_amount = $db->query_result($ConventionsResult, 0, 's_h_amount');
        $s_h_percent = $db->query_result($ConventionsResult, 0, 's_h_percent');
        $salle = $db->query_result($ConventionsResult, 0, 'salle');
        $lieu = $db->query_result($ConventionsResult, 0, 'lieu');
        $locaux = $db->query_result($ConventionsResult, 0, 'cf_860');
        $type = $db->query_result($ConventionsResult, 0, 'cf_977');
        $description = $db->query_result($ConventionsResult, 0, 'description');
        $discount_percent = $db->query_result($ConventionsResult, 0, 'discount_percent');
        $discount_amount = $db->query_result($ConventionsResult, 0, 'discount_amount');
        $account_id = $db->query_result($ConventionsResult, 0, 'accountid');
        $assigned_user_id = $db->query_result($ConventionsResult, 0, 'swownerid');
        $currency_id = $db->query_result($ConventionsResult, 0, 'currency_id');
        $conversion_rate = $db->query_result($ConventionsResult, 0, 'conversion_rate');
        $bill_street = $db->query_result($ConventionsResult, 0, 'bill_street');
        $bill_city = $db->query_result($ConventionsResult, 0, 'bill_city');
        $bill_code = $db->query_result($ConventionsResult, 0, 'bill_code');
        $bill_state = $db->query_result($ConventionsResult, 0, 'bill_state');
        $bill_country = $db->query_result($ConventionsResult, 0, 'bill_country');
        $bill_pobox = $db->query_result($ConventionsResult, 0, 'bill_pobox');
        $terms_conditions = $db->query_result($ConventionsResult, 0, 'terms_conditions');
        $pre_tax_total = $db->query_result($ConventionsResult, 0, 'pre_tax_total');
        $tags = $db->query_result($ConventionsResult, 0, 'tags');
        $session = $db->query_result($ConventionsResult, 0, 'session');
        $salescommission = $db->query_result($ConventionsResult, 0, 'salescommission');

        /* Mise à jour numéro de la facture */

        $financementQuery = 'SELECT financement FROM vtiger_salesorder WHERE salesorderid=?';
        $financementParams = array($salesorder_id);
        $financementResult = $db->pquery($financementQuery, $financementParams);
        $financementCount = $db->num_rows($financementResult);
        $financement = floatval($db->query_result($financementResult, 0, 'financement'));

        if ($financement > 0) {
            /* parcourir la liste des financements */
            $financeursQuery = 'SELECT vendorid,montant,tva,ttc FROM vtiger_inventoryfinanceurrel WHERE id=?';
            $financeursParams = array($salesorder_id);
            $financeursResult = $db->pquery($financeursQuery, $financeursParams);
            $financeursCount = $db->num_rows($financeursResult);

            for ($j = 0; $j < $financeursCount; $j++) {
                $vendorid = $db->query_result($financeursResult, $j, 'vendorid');
                $montant = $db->query_result($financeursResult, $j, 'montant');
                $tva = $db->query_result($financeursResult, $j, 'tva');
                $ttc = $db->query_result($financeursResult, $j, 'ttc');

                $hdnSubTotal_financeur = $montant;
                $totalTTC_financeur = $ttc;
                $tax2 = 0;
                $tax3 = 0;

                $montantclient = floatval($hdnSubTotal) + floatval($s_h_amount) - floatval($hdnSubTotal_financeur) - floatval($discount_amount);

                /* Sélectionner les id des factures des financeur */
                $financeursQuery = 'SELECT vtiger_invoice.invoiceid,received,adjustment,cf_1039 as facture_parent,cf_1078 as avoir,cf_1083 as mode_reglement,cf_1187 as date_report,
                    cf_1189 as relance_7j,cf_1191 as relance_14j,cf_1193 as relance_30j
                        FROM vtiger_invoice 
                        INNER JOIN vtiger_invoicecf on vtiger_invoicecf.invoiceid = vtiger_invoice.invoiceid
                        WHERE salesorderid=? and cf_1004 = ? and financeur=?';
                $financeursParams = array($salesorder_id, 'Financeur', $vendorid);
                $financeursResult = $db->pquery($financeursQuery, $financeursParams);
                $financeursCount = $db->num_rows($financeursResult);

                /* Debut création facture fournisseur $j */
                $focus = new Invoice();
                if ($financeursCount > 0) {
                    $invoiceid = $db->query_result($financeursResult, 0, 'invoiceid');
                    $sql = "UPDATE vtiger_crmentity SET deleted=? WHERE crmid=?";
                    $db->pquery($sql, array(0, $invoiceid));
                    $received = $db->query_result($financeursResult, 0, 'received');
                    $adjustment = $db->query_result($financeursResult, 0, 'adjustment');
                    $facture_parent = $db->query_result($financeursResult, 0, 'facture_parent');
                    $avoir = $db->query_result($financeursResult, 0, 'avoir');
                    $mode_reglement = $db->query_result($financeursResult, 0, 'mode_reglement');
                    $date_report = $db->query_result($financeursResult, 0, 'date_report');
                    $relance_7j = $db->query_result($financeursResult, 0, 'relance_7j');
                    $relance_14j = $db->query_result($financeursResult, 0, 'relance_14j');
                    $relance_30j = $db->query_result($financeursResult, 0, 'relance_30j');
                    $focus->mode = 'edit';
                    $focus->id = $invoiceid;
                    $adb->pquery("delete from vtiger_inventorysubproductrel where id=?", array($invoiceid));
                    $adb->pquery("delete from vtiger_inventoryapprenantsrel where id=?", array($invoiceid));
                    $adb->pquery("delete from vtiger_inventorydatesrel where id=?", array($invoiceid));
                } else {
                    $focus->mode = 'create';
                    $received = 0;
                    $avoir = 0;
                }

                $balance = $totalTTC_financeur - $received;
                $monfichier = fopen('debug_financ.txt', 'a+');
                fputs($monfichier, "\n" . $balance);
                fclose($monfichier);

                $invoicedate = date("Y-m-d");
                $focus->column_fields['subject'] = $subject; //V 
                $focus->column_fields['salesorder_id'] = $salesorder_id; //V                                
                $focus->column_fields['invoicedate'] = $invoicedate;
                $focus->column_fields['salle'] = $salle;
                $focus->column_fields['lieu'] = $lieu;
                $focus->column_fields['cf_1028'] = $locaux;
                $focus->column_fields['cf_1035'] = $type;
                $focus->column_fields['txtAdjustment'] = $adjustment;
                $focus->column_fields['salescommission'] = $salescommission;
                $focus->column_fields['exciseduty'] = $wsrecord['exciseduty'];
                $focus->column_fields['hdnSubTotal'] = $hdnSubTotal;
                $focus->column_fields['hdnGrandTotal'] = $totalTTC_financeur;
                $focus->column_fields['hdnTaxType'] = $taxtype;
                $focus->column_fields['hdnDiscountPercent'] = $discount_percent; //VA 
                $focus->column_fields['hdnDiscountAmount'] = $discount_amount; //VA  
//                $focus->column_fields['tax1'] = $tax1; //VA
//                $focus->column_fields['tax2'] = $tax2;
//                $focus->column_fields['tax3'] = $tax3;
                $focus->column_fields['hdnS_H_Amount'] = $s_h_amount;
                $focus->column_fields['account_id'] = $account_id; //V  
                $focus->column_fields['invoicestatus'] = 'AutoCreated';
                $focus->column_fields['assigned_user_id'] = $assigned_user_id;
                $focus->column_fields['currency_id'] = $currency_id;
                $focus->column_fields['conversion_rate'] = $conversion_rate;
                $focus->column_fields['bill_street'] = $bill_street; //V
                $focus->column_fields['cf_1026'] = $wsrecord['cf_9730']; //V                                        
                $focus->column_fields['bill_city'] = $bill_city; //V
                $focus->column_fields['bill_state'] = $bill_state; //V
                $focus->column_fields['bill_code'] = $bill_code; //V
                $focus->column_fields['bill_country'] = $bill_country; //V
                $focus->column_fields['bill_pobox'] = $bill_pobox; //V                    
                $focus->column_fields['description'] = $description;
                $focus->column_fields['terms_conditions'] = $terms_conditions; //V                 
                $focus->column_fields['pre_tax_total'] = $pre_tax_total;
                $focus->column_fields['received'] = $received;
                $focus->column_fields['balance'] = $balance;
                $focus->column_fields['hdnS_H_Percent'] = $s_h_percent;
                $focus->column_fields['source'] = 'FINANCEUR';
                $focus->column_fields['tags'] = $tags;
                $focus->column_fields['cf_1004'] = 'Financeur';
                $focus->column_fields['session'] = $session;
                $focus->column_fields['financeur'] = $vendorid;
                $focus->column_fields['cf_1039'] = $facture_parent;
                $focus->column_fields['cf_1078'] = $avoir;
                $focus->column_fields['cf_1083'] = $mode_reglement;
                $focus->column_fields['cf_1187'] = $date_report;
                $focus->column_fields['cf_1189'] = $relance_7j;
                $focus->column_fields['cf_1191'] = $relance_14j;
                $focus->column_fields['cf_1193'] = $relance_30j;

                $focus->save("Invoice");
                $return_id = $focus->id;

                /* Mise à jour numéro facture */
                $detailFactureFinQuery = 'SELECT vtiger_crmentity.createdtime, invoice_no
                        FROM vtiger_invoice 
                        inner join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_invoice.invoiceid
                        WHERE invoiceid = ?';
                $detailFactureFinParams = array($return_id);
                $detailFactureFinResult = $db->pquery($detailFactureFinQuery, $detailFactureFinParams);

                $createdtime = $db->query_result($detailFactureFinResult, 0, 'createdtime');
                $invoice_no = $db->query_result($detailFactureFinResult, 0, 'invoice_no');

                $datefacture = strtotime($createdtime);
                $datefacture = date("Ymd", $datefacture);

                $numero_facture_complet = $invoice_no . '-' . $datefacture;
                $sql = "UPDATE vtiger_invoicecf SET cf_1033=? WHERE invoiceid=?";
                $db->pquery($sql, array($numero_facture_complet, $return_id));
                /* Fin Mise à jour numéro facture */

                $requete_update = "update vtiger_invoice set subtotal=?,adjustment=?,total=?,taxtype=?,discount_percent=?,discount_amount=?,s_h_amount=?,pre_tax_total=?,s_h_percent=?,balance=?,financement=?,montantclient=? where invoiceid=?";
                $result_update = $db->pquery($requete_update, array($hdnSubTotal, $adjustment, $totalTTC_financeur, $taxtype, $discount_percent, $discount_amount, $s_h_amount, $hdnSubTotal_financeur, $s_h_percent, $balance, 0, $montantclient, $return_id));

                /* Détail formation */
                $detailFormationQuery = 'SELECT productid, sequence_no, quantity, listprice, comment, description, purchase_cost, margin,nbrjours,nbrheures,baseheures,naturecalcul,parpersonne,listpriceinter,listpriceintra,tarif
                        FROM vtiger_inventoryproductrel WHERE id = ?';
                $detailFormationParams = array($salesorder_id);
                $detailFormationResult = $db->pquery($detailFormationQuery, $detailFormationParams);

                $productid = $db->query_result($detailFormationResult, 0, 'productid');
                $sequence_no = $db->query_result($detailFormationResult, 0, 'sequence_no');
                $quantity = $db->query_result($detailFormationResult, 0, 'quantity');
                $listprice = $db->query_result($detailFormationResult, 0, 'listprice');
                $comment = $db->query_result($detailFormationResult, 0, 'comment');
                $description = $db->query_result($detailFormationResult, 0, 'description');
                $purchase_cost = $db->query_result($detailFormationResult, 0, 'purchase_cost');
                $margin = $db->query_result($detailFormationResult, 0, 'margin');
                $nbrjours = $db->query_result($detailFormationResult, 0, 'nbrjours');
                $nbrheures = $db->query_result($detailFormationResult, 0, 'nbrheures');
                $baseheures = $db->query_result($detailFormationResult, 0, 'baseheures');
                $naturecalcul = $db->query_result($detailFormationResult, 0, 'naturecalcul');
                $parpersonne = $db->query_result($detailFormationResult, 0, 'parpersonne');
                $listpriceinter = $db->query_result($detailFormationResult, 0, 'listpriceinter');
                $listpriceintra = $db->query_result($detailFormationResult, 0, 'listpriceintra');
                $tarif = $db->query_result($detailFormationResult, 0, 'tarif');

                $query = 'INSERT INTO vtiger_inventoryproductrel(id, productid, sequence_no, quantity, listprice, comment, description, purchase_cost, margin,nbrjours,nbrheures,baseheures,naturecalcul,parpersonne,listpriceinter,listpriceintra,tarif)
					VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
                $qparams = array($return_id, $productid, $sequence_no, $quantity, $listprice, $comment, $description, $purchase_cost, $margin, $nbrjours, $nbrheures, $baseheures, $naturecalcul, $parpersonne, $listpriceinter, $listpriceintra, $tarif);
                $db->pquery($query, $qparams);

                /* Fin Détail formation */

                /* Détail apprenant */
                $detailApprenantQuery = 'SELECT apprenantid,sequence_no,etat,resultat,inscrit,convoque,be_essai,be_mesurage,be_verification,
                        be_manoeuvre,he_essai,he_mesurage,he_verification,he_manoeuvre,initiale,recyclage,testprerequis,electricien
                        FROM vtiger_inventoryapprenantsrel WHERE id = ?';
                $detailApprenantParams = array($salesorder_id);
                $detailApprenantResult = $db->pquery($detailApprenantQuery, $detailApprenantParams);
                $detailApprenantCount = $db->num_rows($detailApprenantResult);
                if ($detailApprenantCount) {
                    for ($i = 0; $i < $detailApprenantCount; $i++) {
                        $apprenantid = $db->query_result($detailApprenantResult, $i, 'apprenantid');
                        $sequence_no = $db->query_result($detailApprenantResult, $i, 'sequence_no');
                        $etat = $db->query_result($detailApprenantResult, $i, 'etat');
                        $resultat = $db->query_result($detailApprenantResult, $i, 'resultat');
                        $inscrit = $db->query_result($detailApprenantResult, $i, 'inscrit');
                        $convoque = $db->query_result($detailApprenantResult, $i, 'convoque');
                        $be_essai = $db->query_result($detailApprenantResult, $i, 'be_essai');
                        $be_mesurage = $db->query_result($detailApprenantResult, $i, 'be_mesurage');
                        $be_verification = $db->query_result($detailApprenantResult, $i, 'be_verification');
                        $be_manoeuvre = $db->query_result($detailApprenantResult, $i, 'be_manoeuvre');
                        $he_essai = $db->query_result($detailApprenantResult, $i, 'he_essai');
                        $he_mesurage = $db->query_result($detailApprenantResult, $i, 'he_mesurage');
                        $he_verification = $db->query_result($detailApprenantResult, $i, 'he_verification');
                        $he_manoeuvre = $db->query_result($detailApprenantResult, $i, 'he_manoeuvre');
                        $initiale = $db->query_result($detailApprenantResult, $i, 'initiale');
                        $recyclage = $db->query_result($detailApprenantResult, $i, 'recyclage');
                        $testprerequis = $db->query_result($detailApprenantResult, $i, 'testprerequis');
                        $electricien = $db->query_result($detailApprenantResult, $i, 'electricien');

                        $query = 'INSERT INTO vtiger_inventoryapprenantsrel(id, apprenantid, sequence_no, etat, resultat, inscrit, convoque, 
                            be_essai, be_mesurage,be_verification,be_manoeuvre,he_essai,he_mesurage,he_verification,he_manoeuvre,initiale,recyclage,
                            testprerequis,electricien)
				VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
                        $qparams = array($return_id, $apprenantid, $sequence_no, $etat, $resultat, $inscrit, $convoque, $be_essai,
                            $be_mesurage, $be_verification, $be_manoeuvre, $he_essai, $he_mesurage, $he_verification, $he_manoeuvre,
                            $initiale, $recyclage, $testprerequis, $electricien);
                        $db->pquery($query, $qparams);
                    }
                }
                /* FIN Détail apprenant */

                /* Détail journée */
                $detailJourneeQuery = 'SELECT sequence_no,date_start,start_matin,end_matin,start_apresmidi,end_apresmidi,duree_formation
                        FROM vtiger_inventorydatesrel WHERE id = ?';
                $detailJourneeParams = array($salesorder_id);
                $detailJourneeResult = $db->pquery($detailJourneeQuery, $detailJourneeParams);
                $detailJourneeCount = $db->num_rows($detailJourneeResult);
                if ($detailJourneeCount) {
                    for ($i = 0; $i < $detailJourneeCount; $i++) {
                        $sequence_no = $db->query_result($detailJourneeResult, $i, 'sequence_no');
                        $date_start = $db->query_result($detailJourneeResult, $i, 'date_start');
                        $start_matin = $db->query_result($detailJourneeResult, $i, 'start_matin');
                        $end_matin = $db->query_result($detailJourneeResult, $i, 'end_matin');
                        $start_apresmidi = $db->query_result($detailJourneeResult, $i, 'start_apresmidi');
                        $end_apresmidi = $db->query_result($detailJourneeResult, $i, 'end_apresmidi');
                        $duree_formation = $db->query_result($detailJourneeResult, $i, 'duree_formation');

                        // update les journee
                        $query = 'INSERT INTO vtiger_inventorydatesrel(id, sequence_no, date_start, start_matin, end_matin, start_apresmidi, end_apresmidi, duree_formation)
				VALUES(?,?,?,?,?,?,?,?)';
                        $qparams = array($return_id, $sequence_no, $date_start, $start_matin, $end_matin, $start_apresmidi, $end_apresmidi, $duree_formation);
                        $db->pquery($query, $qparams);
                        //fin update
                    }
                }
                /* FIN Détail journée */
                $reponse = 'ok';
            }
        } else {
            $reponse = 'prob_no_financeurs';
        }
//        } else {
//            $reponse = 'prob_no_fact_client';
//        }
        $info[] = array($reponse);

        $response->setResult($info);
        $response->emit();
    }

    function checkPermission(Vtiger_Request $request) {
        return;
    }

    function resolveReferenceLabel($id, $module = false) {
        if (empty($id)) {
            return '';
        }
        if ($module === false) {
            $module = getSalesEntityType($id);
        }
        $label = getEntityName($module, array($id));
        return decode_html($label[$id]);
    }

}
