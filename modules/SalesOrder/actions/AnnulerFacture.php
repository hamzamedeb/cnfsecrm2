<?php

/* uni_cnfsecrm - v2 - modif 94 - FILE */
require_once('modules/Invoice/Invoice.php');
require_once('modules/Cashflow4You/Cashflow4You.php');

class SalesOrder_AnnulerFacture_Action extends Vtiger_Action_Controller {

    function process(Vtiger_Request $request) {
        global $adb;
        $response = new Vtiger_Response();

        $recordId = $request->get('record');
        $apprenantId = $request->get('apprenantId');

        if (!is_null($apprenantId) && !is_null($recordId)) {
            $query = 'SELECT invoiceid 
                    FROM vtiger_invoice 
                    INNER JOIN vtiger_account ON vtiger_account.accountid = vtiger_invoice.accountid 
                    INNER JOIN vtiger_contactdetails ON vtiger_account.accountid = vtiger_contactdetails.accountid 
                    where vtiger_invoice.salesorderid = ? and vtiger_contactdetails.contactid = ? ';
            $params = array($recordId, $apprenantId);
            $result = $adb->pquery($query, $params);
            $invoiceId = $adb->query_result($result, 0, 'invoiceid');
            if (!is_null($invoiceId)) {
                //detail facture vtiger_invoice
                $queryDetailFac = 'SELECT * FROM vtiger_invoice  where invoiceid = ?';
                $paramsDetail = array($invoiceId);
                $resultDetail = $adb->pquery($queryDetailFac, $paramsDetail);
                $invoiceIdtest = $adb->query_result($resultDetail, 0, 'invoiceid');
                $subject = $adb->query_result($resultDetail, 0, 'subject'); //
                $salesorderid = $adb->query_result($resultDetail, 0, 'salesorderid'); //
                $customerno = $adb->query_result($resultDetail, 0, 'customerno'); //
                $contactid = $adb->query_result($resultDetail, 0, 'contactid');
                $notes = $adb->query_result($resultDetail, 0, 'notes');
                $invoicedate = $adb->query_result($resultDetail, 0, 'invoicedate');
                $duedate = $adb->query_result($resultDetail, 0, 'duedate');
                $invoiceterms = $adb->query_result($resultDetail, 0, 'invoiceterms');
                $type = $adb->query_result($resultDetail, 0, 'type');
                $adjustment = $adb->query_result($resultDetail, 0, 'adjustment'); //
                $salescommission = $adb->query_result($resultDetail, 0, 'salescommission'); //
                $exciseduty = $adb->query_result($resultDetail, 0, 'exciseduty'); //
                $subtotal = $adb->query_result($resultDetail, 0, 'subtotal'); //
                $total = $adb->query_result($resultDetail, 0, 'total'); //
                $taxtype = $adb->query_result($resultDetail, 0, 'taxtype'); //
                $discount_percent = $adb->query_result($resultDetail, 0, 'discount_percent'); //
                $discount_amount = $adb->query_result($resultDetail, 0, 'discount_amount'); //
                $s_h_amount = $adb->query_result($resultDetail, 0, 's_h_amount'); //
                $shipping = $adb->query_result($resultDetail, 0, 'shipping');
                $accountid = $adb->query_result($resultDetail, 0, 'accountid'); //
                $terms_conditions = $adb->query_result($resultDetail, 0, 'terms_conditions'); //
                $purchaseorder = $adb->query_result($resultDetail, 0, 'purchaseorder');
                $invoicestatus = $adb->query_result($resultDetail, 0, 'invoicestatus'); //
                $invoice_no = $adb->query_result($resultDetail, 0, 'invoice_no');
                $currency_id = $adb->query_result($resultDetail, 0, 'currency_id'); //
                $conversion_rate = $adb->query_result($resultDetail, 0, 'conversion_rate'); //
                $compound_taxes_info = $adb->query_result($resultDetail, 0, 'compound_taxes_info');
                $pre_tax_total = $adb->query_result($resultDetail, 0, 'pre_tax_total'); //
                $received = $adb->query_result($resultDetail, 0, 'received'); //
                $balance = $adb->query_result($resultDetail, 0, 'balance'); //
                $s_h_percent = $adb->query_result($resultDetail, 0, 's_h_percent'); //
                $potential_id = $adb->query_result($resultDetail, 0, 'potential_id');
                $tags = $adb->query_result($resultDetail, 0, 'tags'); //
                $region_id = $adb->query_result($resultDetail, 0, 'region_id');
                $lieu = $adb->query_result($resultDetail, 0, 'lieu'); //
                $salle = $adb->query_result($resultDetail, 0, 'salle'); //
                $financeur = $adb->query_result($resultDetail, 0, 'financeur'); //
                $idtms = $adb->query_result($resultDetail, 0, 'idtms');
                $financement = $adb->query_result($resultDetail, 0, 'financement');
                $p_paid_amount = $adb->query_result($resultDetail, 0, 'p_paid_amount'); //
                $p_open_amount = $adb->query_result($resultDetail, 0, 'p_open_amount'); //
                $montantclient = $adb->query_result($resultDetail, 0, 'montantclient'); //
                $session = $adb->query_result($resultDetail, 0, 'session'); //

                $query = "select invoiceid from vtiger_invoicecf where cf_1039 LIKE '%" . $invoice_no . "%'";
                $result = $adb->pquery($query);
                $idInvoice = $adb->query_result($result, 0, 'invoiceid');
                if (!is_null($idInvoice)) {
                    $message = 'facture a deja un avoir';
                    $resultAvoir = false;
                } else {
                    //detail facture 
                    $queryDetailFac2 = 'SELECT * FROM vtiger_invoicecf  where invoiceid = ? ';
                    $paramsDetail2 = array($invoiceId);
                    $resultDetail2 = $adb->pquery($queryDetailFac2, $paramsDetail2);
                    $nature = $adb->query_result($resultDetail2, 0, 'cf_1004');
                    $suiteAdresseFormation = $adb->query_result($resultDetail2, 0, 'cf_1026');
                    $locaux = $adb->query_result($resultDetail2, 0, 'cf_1028'); //
                    $numeroFacture = $adb->query_result($resultDetail2, 0, 'cf_1033');
                    $type = $adb->query_result($resultDetail2, 0, 'cf_1035'); //
                    $modeReglement = $adb->query_result($resultDetail2, 0, 'cf_1083'); //
                    $etatEcheance = $adb->query_result($resultDetail2, 0, 'cf_1185');
                    $dateReport = $adb->query_result($resultDetail2, 0, 'cf_1187'); //
                    $relance7Jours = $adb->query_result($resultDetail2, 0, 'cf_1189'); //
                    $relance14Jours = $adb->query_result($resultDetail2, 0, 'cf_1191'); //
                    $relance30Jours = $adb->query_result($resultDetail2, 0, 'cf_1193'); //
                    $rectifierCompte = $adb->query_result($resultDetail2, 0, 'cf_1281');

                    //detail facture adresse
                    $queryFacAdr = 'SELECT * FROM vtiger_invoicebillads where invoicebilladdressid = ?';
                    $paramsFacAdr = array($invoiceId);
                    $resultFacAdr = $adb->pquery($queryFacAdr, $paramsFacAdr);
                    $ville = $adb->query_result($resultFacAdr, 0, 'bill_city'); //
                    $codePostal = $adb->query_result($resultFacAdr, 0, 'bill_code'); //
                    $pays = $adb->query_result($resultFacAdr, 0, 'bill_country'); //
                    $etat = $adb->query_result($resultFacAdr, 0, 'bill_state'); //
                    $adresse = $adb->query_result($resultFacAdr, 0, 'bill_street'); //
                    //creation de la facture
                    $focus = new Invoice();
                    $focus->mode = 'create';
                    $invoicedate = date("Y-m-d");
                    $focus->column_fields['subject'] = $subject; //
                    $focus->column_fields['salesorder_id'] = $salesorderid;  //                               
                    $focus->column_fields['invoicedate'] = $invoicedate; //
                    $focus->column_fields['salle'] = $salle; //
                    $focus->column_fields['lieu'] = $lieu; //
                    $focus->column_fields['cf_1028'] = $locaux; //
                    $focus->column_fields['cf_1035'] = $type; //
                    $focus->column_fields['txtAdjustment'] = $adjustment; //
                    $focus->column_fields['salescommission'] = $salescommission; //
                    $focus->column_fields['exciseduty'] = $exciseduty; //
                    $focus->column_fields['hdnGrandTotal'] = $total; //
                    $focus->column_fields['hdnTaxType'] = $taxtype; //
                    $focus->column_fields['hdnDiscountPercent'] = $discount_percent; //  
                    $focus->column_fields['hdnDiscountAmount'] = $discount_amount; //   
                    $focus->column_fields['hdnS_H_Amount'] = $s_h_amount; //
                    $focus->column_fields['account_id'] = $accountid; //
                    $focus->column_fields['invoicestatus'] = $invoicestatus; //
                    $focus->column_fields['assigned_user_id'] = $assigned_user_id;
                    $focus->column_fields['currency_id'] = $currency_id; //
                    $focus->column_fields['conversion_rate'] = $conversion_rate; //
                    $focus->column_fields['bill_street'] = $adresse; //
                    $focus->column_fields['cf_1026'] = $suiteAdresseFormation; //                               
                    $focus->column_fields['bill_city'] = $ville; //
                    $focus->column_fields['bill_state'] = $etat; //
                    $focus->column_fields['bill_code'] = $codePostal; //
                    $focus->column_fields['bill_country'] = $pays; //
                    $focus->column_fields['bill_pobox'] = $bill_pobox;
                    $focus->column_fields['description'] = $description;
                    $focus->column_fields['terms_conditions'] = $terms_conditions; //               
                    $focus->column_fields['pre_tax_total'] = $pre_tax_total; //
                    $focus->column_fields['received'] = $received; //
                    $focus->column_fields['hdnS_H_Percent'] = $s_h_percent; //
                    $focus->column_fields['tags'] = $tags; //
                    $focus->column_fields['session'] = $session; //
                    $focus->column_fields['financeur'] = $financeur;
                    $focus->column_fields['cf_1083'] = $modeReglement; //
                    $focus->column_fields['cf_1187'] = $dateReport; //
                    $focus->column_fields['cf_1189'] = $relance7Jours; //
                    $focus->column_fields['cf_1191'] = $relance14Jours; //
                    $focus->column_fields['cf_1193'] = $relance30Jours; //

                    $focus->save("Invoice");
                    $idInvoiceAvoir = $focus->id;

                    //update
                    $updateInvoiceAvoir = "update vtiger_invoice set subtotal=?,adjustment=?,total=?,taxtype=?,discount_percent=?,discount_amount=?,s_h_amount=?,pre_tax_total=?,s_h_percent=?,balance=?,financement=?,montantclient=? where invoiceid=?";
                    $adb->pquery($updateInvoiceAvoir, array($subtotal, $adjustment, $total, $taxtype, $discount_percent, $discount_amount, $s_h_amount, $pre_tax_total, $s_h_percent, $balance, $financement, $montantclient, $idInvoiceAvoir));
                    // ***
                    $resultInvoiceAvoir = $adb->pquery("SELECT invoice_no,cf_1033 FROM vtiger_invoice 
                        INNER JOIN vtiger_invoicecf on vtiger_invoicecf.invoiceid = vtiger_invoice.invoiceid 
                        WHERE vtiger_invoice.invoiceid = ?", array($idInvoiceAvoir));
                    $invoice_no_select = $adb->query_result($resultInvoiceAvoir, 0, 'invoice_no');
                    $numero_facture_select = $numeroFacture;


                    $invoice_no_select_avoir = str_replace("FA", "AV", $invoice_no_select);
                    $dateAvoir = date("Ymd");
                    $numero_facture_select_avoir = $invoice_no_select_avoir . '-' . $dateAvoir; //VA

                    $query = "UPDATE vtiger_invoicecf SET cf_1039=?,cf_1033=? WHERE invoiceid=?";
                    $adb->pquery($query, array($numero_facture_select, $numero_facture_select_avoir, $idInvoiceAvoir));

                    $query = "UPDATE vtiger_invoice SET invoice_no=?,invoicestatus=? WHERE invoiceid=?";
                    $adb->pquery($query, array($invoice_no_select_avoir, 'Paid', $idInvoiceAvoir));

                    $query = "UPDATE vtiger_invoice SET invoicestatus=? WHERE invoiceid=?";
                    $adb->pquery($query, array('Avoir', $invoiceId));

                    $focus_cash = new Cashflow4You();
                    $focus_cash->mode = 'create';
                    $Date = strtotime($dateAvoir);
                    $Date = date("Y-m-d", $Date);
                    $focus_cash->column_fields['assigned_user_id'] = 1;
                    $focus_cash->column_fields['cashflow4youname'] = 'Paiement';
                    $focus_cash->column_fields['cashflow4you_paytype'] = 'Incoming';
                    $focus_cash->column_fields['paymentdate'] = $Date;
                    $focus_cash->column_fields['cashflow4you_paymethod'] = "Chèque";
                    $focus_cash->column_fields['description'] = "";
                    $focus_cash->column_fields['relationid'] = $invoiceId;
                    $focus_cash->column_fields['paymentamount'] = $total;
                    $focus_cash->column_fields['transactionid'] = "avoir";

                    $focus_cash->save("Cashflow4You");
                    // *** 
                    //detail detail apprenantsrel
                    $queryFacApr = 'SELECT * FROM vtiger_inventoryapprenantsrel where id = ?';
                    $paramsFacApr = array($invoiceId);
                    $resultFacApr = $adb->pquery($queryFacApr, $paramsFacApr);
                    $num_rows = $adb->num_rows($resultFacApr);

                    for ($i = 0; $i <= $num_rows; $i++) {
                        if ($adb->query_result($resultFacApr, $i, 'apprenantid') != null) {
                            $apprenantidApp = $adb->query_result($resultFacApr, $i, 'apprenantid');
                            $sequence_noApp = $adb->query_result($resultFacApr, $i, 'sequence_no');
                            $etatApp = $adb->query_result($resultFacApr, $i, 'etat');
                            $resultatApp = $adb->query_result($resultFacApr, $i, 'resultat');
                            $inscritApp = $adb->query_result($resultFacApr, $i, 'inscrit');
                            $convoqueApp = $adb->query_result($resultFacApr, $i, 'convoque');
                            $be_essaiApp = $adb->query_result($resultFacApr, $i, 'be_essai');
                            $be_mesurageApp = $adb->query_result($resultFacApr, $i, 'be_mesurage');
                            $be_verificationApp = $adb->query_result($resultFacApr, $i, 'be_verification');
                            $be_manoeuvreApp = $adb->query_result($resultFacApr, $i, 'be_manoeuvre');
                            $he_essaiApp = $adb->query_result($resultFacApr, $i, 'he_essai');
                            $he_mesurageApp = $adb->query_result($resultFacApr, $i, 'he_mesurage');
                            $he_verificationApp = $adb->query_result($resultFacApr, $i, 'he_verification');
                            $he_manoeuvreApp = $adb->query_result($resultFacApr, $i, 'he_manoeuvre');
                            $initialeApp = $adb->query_result($resultFacApr, $i, 'initiale');
                            $recyclageApp = $adb->query_result($resultFacApr, $i, 'recyclage');
                            $testprerequisApp = $adb->query_result($resultFacApr, $i, 'testprerequis');
                            $electricienApp = $adb->query_result($resultFacApr, $i, 'electricien');

                            $queryInsertApr = 'INSERT INTO vtiger_inventoryapprenantsrel(id,apprenantid,sequence_no,etat,resultat,inscrit,convoque,be_essai,be_mesurage,be_verification,be_manoeuvre,he_essai,he_mesurage,he_verification,he_manoeuvre,initiale,recyclage,testprerequis,electricien)
					VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
                            $qparamsInsertApr = array($idInvoiceAvoir, $apprenantidApp, $sequence_noApp, $etatApp, $resultatApp, $inscritApp, $convoqueApp, $be_essaiApp, $be_mesurageApp, $be_verificationApp, $be_manoeuvreApp, $he_essaiApp, $he_mesurageApp, $he_verificationApp, $he_manoeuvreApp, $initialeApp, $recyclageApp, $testprerequisApp, $electricienApp);
                            $adb->pquery($queryInsertApr, $qparamsInsertApr);
                        }
                    }
                    //detail detail date rel
                    $queryFacDate = 'SELECT * FROM vtiger_inventorydatesrel where id = ?';
                    $paramsFacDate = array($invoiceId);
                    $resultFacDate = $adb->pquery($queryFacDate, $paramsFacDate);
                    $num_rows = $adb->num_rows($resultFacDate);

                    for ($i = 0; $i < $num_rows; $i++) {
                        if ($adb->query_result($resultFacDate, $i, 'date_start') != null) {
                            $sequence_noJo = $adb->query_result($resultFacDate, $i, 'sequence_no');
                            $date_startJo = $adb->query_result($resultFacDate, $i, 'date_start');
                            $start_matinJo = $adb->query_result($resultFacDate, $i, 'start_matin');
                            $end_matinJo = $adb->query_result($resultFacDate, $i, 'end_matin');
                            $start_apresmidiJo = $adb->query_result($resultFacDate, $i, 'start_apresmidi');
                            $end_apresmidiJo = $adb->query_result($resultFacDate, $i, 'end_apresmidi');
                            $duree_formationJo = $adb->query_result($resultFacDate, $i, 'duree_formation');

                            $queryInsertJo = 'INSERT INTO vtiger_inventorydatesrel(id,sequence_no,date_start,start_matin,end_matin,start_apresmidi,end_apresmidi,duree_formation)
					VALUES(?,?,?,?,?,?,?,?)';
                            $qparamsInsertJo = array($idInvoiceAvoir, $sequence_noJo, $date_startJo, $start_matinJo, $end_matinJo, $start_apresmidiJo, $end_apresmidiJo, $duree_formationJo);
                            $adb->pquery($queryInsertJo, $qparamsInsertJo);
                        }
                    }
                    //detail + ajout financeur
                    $queryFacFn = 'SELECT * FROM vtiger_inventoryfinanceurrel where id = ?';
                    $paramsFacFn = array($invoiceId);
                    $resultFacFn = $adb->pquery($queryFacFn, $paramsFacFn);
                    $num_rows = $adb->num_rows($resultFacFn);

                    for ($i = 0; $i < $num_rows; $i++) {
                        if ($adb->query_result($resultFacFn, $i, 'vendorid') != null) {
                            $vendoridFn = $adb->query_result($resultFacFn, $i, 'vendorid');
                            $sequence_noFn = $adb->query_result($resultFacFn, $i, 'sequence_no');
                            $pourcentageFn = $adb->query_result($resultFacFn, $i, 'pourcentage');
                            $montantFn = $adb->query_result($resultFacFn, $i, 'montant');
                            $tvaFn = $adb->query_result($resultFacFn, $i, 'tva');
                            $ttcFn = $adb->query_result($resultFacFn, $i, 'ttc');

                            $queryInsertFn = 'INSERT INTO vtiger_inventoryfinanceurrel(id,vendorid,sequence_no,pourcentage,montant,tva,ttc)
					VALUES(?,?,?,?,?,?,?)';
                            $qparamsInsertFn = array($idInvoiceAvoir, $vendoridFn, $sequence_noFn, $pourcentageFn, $montantFn, $tvaFn, $ttcFn);
                            $adb->pquery($queryInsertFn, $qparamsInsertFn);
                        }
                    }
                    //detail produit rel
                    $queryFacProd = 'SELECT * FROM vtiger_inventoryproductrel where id = ?';
                    $paramsFacProd = array($invoiceId);
                    $resultFacProd = $adb->pquery($queryFacProd, $paramsFacProd);

                    $productid = $adb->query_result($resultFacProd, 0, 'productid');
                    $sequence_no = $adb->query_result($resultFacProd, 0, 'sequence_no');
                    $quantity = $adb->query_result($resultFacProd, 0, 'quantity');
                    $listprice = $adb->query_result($resultFacProd, 0, 'listprice');
                    $discount_percent = $adb->query_result($resultFacProd, 0, 'discount_percent');
                    $discount_amount = $adb->query_result($resultFacProd, 0, 'discount_amount');
                    $comment = $adb->query_result($resultFacProd, 0, 'comment');
                    $description = $adb->query_result($resultFacProd, 0, 'description');
                    $incrementondel = $adb->query_result($resultFacProd, 0, 'incrementondel');
                    $lineitem_id = $adb->query_result($resultFacProd, 0, 'lineitem_id');
                    $tax1 = $adb->query_result($resultFacProd, 0, 'tax1');
                    $tax2 = $adb->query_result($resultFacProd, 0, 'tax2');
                    $tax3 = $adb->query_result($resultFacProd, 0, 'tax3');
                    $image = $adb->query_result($resultFacProd, 0, 'image');
                    $purchase_cost = $adb->query_result($resultFacProd, 0, 'purchase_cost');
                    $margin = $adb->query_result($resultFacProd, 0, 'margin');
                    $tax4 = $adb->query_result($resultFacProd, 0, 'tax4');
                    $tarif = $adb->query_result($resultFacProd, 0, 'tarif');
                    $nbrjours = $adb->query_result($resultFacProd, 0, 'nbrjours');
                    $nbrheures = $adb->query_result($resultFacProd, 0, 'nbrheures');
                    $baseheures = $adb->query_result($resultFacProd, 0, 'baseheures');
                    $naturecalcul = $adb->query_result($resultFacProd, 0, 'naturecalcul');
                    $parpersonne = $adb->query_result($resultFacProd, 0, 'parpersonne');
                    $listpriceinter = $adb->query_result($resultFacProd, 0, 'listpriceinter');
                    $listpriceintra = $adb->query_result($resultFacProd, 0, 'listpriceintra');

                    //ajout produit
                    $queryInsertPro = 'INSERT INTO vtiger_inventoryproductrel(id, productid, sequence_no, quantity, listprice,discount_percent,discount_amount,comment,description,incrementondel,lineitem_id,tax1,tax2,tax3,image,purchase_cost,margin,tax4,tarif,nbrjours,nbrheures,baseheures,naturecalcul,parpersonne,listpriceinter,listpriceintra  )
					VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)';
                    $qparamsInsertPro = array($idInvoiceAvoir, $productid, $sequence_no, $quantity, $listprice, $discount_percent, $discount_amount, html_entity_decode($comment), html_entity_decode($description), $incrementondel, '', $tax1, $tax2, $tax3, $image, $purchase_cost, $margin, $tax4, $tarif, $nbrjours, $nbrheures, $baseheures, $naturecalcul, $parpersonne, $listpriceinter, $listpriceintra);
                    $adb->pquery($queryInsertPro, $qparamsInsertPro);

                    // marquer facture parent Avoir
                    $queryUpdateStatut = 'UPDATE vtiger_invoice SET invoicestatus = ? WHERE invoiceid=?';
                    $paramsUpdatStatut = array('Avoir', $invoiceId);
                    $adb->pquery($queryUpdateStatut, $paramsUpdatStatut);
                    $message = 'Facture avoir est crée avec succès';

                    /* ajout hitorique */
                    $querySelectHis = "SELECT * FROM `vtiger_histoapprabsents` WHERE idapprenant = ? and idconvention = ?";
                    $qparamsSelectHis = array($apprenantId, $recordId);
                    $resultSelectHis = $adb->pquery($querySelectHis, $qparamsSelectHis);
                    $action = $adb->query_result($resultSelectHis, 0, 'action');
                    $id = $adb->query_result($resultSelectHis, 0, 'id');

                    if ($action == 0) {
                        $queryUpdateHis = "UPDATE vtiger_histoapprabsents SET action = ?, idfacture = ? WHERE id = ?";
                        $qparamsUpdateHis = array(2,$idInvoiceAvoir, $id);
                        $adb->pquery($queryUpdateHis, $qparamsUpdateHis);
                    } else {
                        $queryInsertHis = "INSERT INTO vtiger_histoapprabsents(id, idapprenant, idconvention, action, idfacture) VALUES (?,?,?,?,?)";
                        $qparamsInsertHis = array('', $apprenantId, $recordId, 2, $idInvoiceAvoir);
                        $adb->pquery($queryInsertHis, $qparamsInsertHis);
                    }
                    $resultAvoir = true;
                }
            } else {
                $message = "Aucune facture pour ce client";
                $resultAvoir = false;
            }
        } else {
            $message = "Impossible d'ajouter un avoir";
            $resultAvoir = false;
        }
        /* correction_modif_96 DEBUT */
        $response->setResult(array('message' => $message, 'resultAvoir' => $resultAvoir, 'idInvoiceAvoir' => $idInvoiceAvoir, 'subject' => $subject));
        /* correction_modif_96 FIN */
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
