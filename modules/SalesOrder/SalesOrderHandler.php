<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class SalesOrderHandler extends VTEventHandler {

    function handleEvent($eventName, $entityData) {
        global $log, $adb;
        /* uni_cnfsecrm - modif 67 - DE */
        if ($eventName == 'vtiger.entity.beforesave') {
            $moduleName = $entityData->getModuleName();
            if ($moduleName == 'SalesOrder') {
                $salesorderid = $entityData->getId();

                $conventionAncienSession = 'SELECT session FROM vtiger_salesorder WHERE salesorderid=?';
                $conventionAncienParams = array($salesorderid);
                $conventionAncienResult = $adb->pquery($conventionAncienSession, $conventionAncienParams);
                $sessionIdBeforeSave = $adb->query_result($conventionAncienResult, 'session');

                $queryUpdateAncienSession = "UPDATE vtiger_salesorder SET ancien_session=? WHERE salesorderid=?";
                $adb->pquery($queryUpdateAncienSession, array($sessionIdBeforeSave, $salesorderid));
            }
        }
        /* uni_cnfsecrm - modif 67 - FI */

        if ($eventName == 'vtiger.entity.aftersave') {
            $moduleName = $entityData->getModuleName();
            if ($moduleName == 'SalesOrder') {

                $salesorderid = $entityData->getId();
                $idClient = $entityData->get('account_id');
                $contactId = $entityData->get('contact_id');
                $lieu = $entityData->get('lieu');
                $salle = $entityData->get('salle');
                $sessionId = $entityData->get('session');
                $locaux = $entityData->get('cf_860');
                $suiteAdresse = $entityData->get('cf_973');
                $adresse = $entityData->get('bill_street');
                $boitePostale = $entityData->get('bill_pobox');
                $ville = $entityData->get('bill_city');
                $etat = $entityData->get('bill_state');
                $cp = $entityData->get('bill_code');
                $pays = $entityData->get('bill_country');
                $subject = $entityData->get('subject');
                $type = $entityData->get('cf_977');
                $compound_taxes_info = $entityData->get('compound_taxes_info');
                $conversion_rate = $entityData->get('conversion_rate');
                $purchaseorder = $entityData->get('purchaseorder');
                $terms_conditions = $entityData->get('terms_conditions');
                $exciseduty = $entityData->get('exciseduty');
                $salescommission = $entityData->get('salescommission');

                $conventionQuery = 'SELECT subtotal,financement,discount_amount,pre_tax_total,p_open_amount,p_paid_amount,adjustment,taxtype,
                    s_h_percent,currency_id,total
                      FROM vtiger_salesorder
                      WHERE salesorderid=?';
                $conventionParams = array($salesorderid);
                $conventionResult = $adb->pquery($conventionQuery, $conventionParams);
                $conventionCount = $adb->num_rows($conventionResult);
                $subtotal = $adb->query_result($conventionResult, 0, 'subtotal');
                $financement = $adb->query_result($conventionResult, 0, 'financement');
                $discount_amount = $adb->query_result($conventionResult, 0, 'discount_amount');
                $discount_percent = $adb->query_result($conventionResult, 0, 'discount_percent');
                $pre_tax_total = $adb->query_result($conventionResult, 0, 'pre_tax_total');
                $p_open_amount = $adb->query_result($conventionResult, 0, 'p_open_amount');
                $p_paid_amount = $adb->query_result($conventionResult, 0, 'p_paid_amount');
                $adjustment = $adb->query_result($conventionResult, 0, 'adjustment');
                $taxtype = $adb->query_result($conventionResult, 0, 'taxtype');
                $s_h_amount = $adb->query_result($conventionResult, 0, 's_h_amount');
                $s_h_percent = $adb->query_result($conventionResult, 0, 's_h_percent');
                $currency_id = $adb->query_result($conventionResult, 0, 'currency_id');
                $total = $adb->query_result($conventionResult, 0, 'total');

                $monfichier = fopen('debug_financ.txt', 'a+');
                fputs($monfichier, "\n" . "discount_amount " . $discount_amount);
                fputs($monfichier, "\n" . "discount_percent " . $discount_percent);
                fputs($monfichier, "\n" . "pre_tax_total " . $pre_tax_total);
                fputs($monfichier, "\n" . "sessionId " . $sessionId);
                fputs($monfichier, "\n" . "total " . $total);
                fputs($monfichier, "\n" . "taxtype " . $taxtype);
                fclose($monfichier);

                $montant_a_paye = $subtotal - $discount_amount;
                $monfichier = fopen('debug_financ.txt', 'a+');
                fputs($monfichier, "\n" . "financement " . $financement);
                fputs($monfichier, "\n" . "montant_a_paye " . $montant_a_paye);
                fclose($monfichier);

                if (number_format($financement, 2, '.', '') != number_format($montant_a_paye, 2, '.', '')) {
                    $monfichier = fopen('debug_financ.txt', 'a+');
                    fputs($monfichier, "\n" . "true ");
                    fclose($monfichier);
                    /* Séléctioner la facture attaché au convention */
                    $queryInvoice = 'SELECT 
                    vtiger_invoice.invoiceid 
                    FROM vtiger_invoice 
                    INNER JOIN vtiger_crmentity on vtiger_crmentity.crmid = vtiger_invoice.invoiceid
                    INNER JOIN vtiger_invoicecf on vtiger_invoicecf.invoiceid = vtiger_invoice.invoiceid
                    WHERE vtiger_invoice.salesorderid =? and deleted =0 and cf_1004 = ?';
                    $paramsInvoice = array($salesorderid, 'Client');
                    $resultInvoice = $adb->pquery($queryInvoice, $paramsInvoice);
                    $countInvoice = $adb->num_rows($resultInvoice);

                    if ($countInvoice > 0) {
                        $invoiceId = $adb->query_result($resultInvoice, '0', 'invoiceid');
                        /* Détail de la facture relié au convention */
                        $queryDetailInvoice = 'select invoice_no, cf_1078, cf_1033, createdtime, invoicestatus, received
                    FROM vtiger_invoice
                    INNER JOIN vtiger_invoicecf on vtiger_invoicecf.invoiceid = vtiger_invoice.invoiceid
                    INNER JOIN vtiger_crmentity on vtiger_crmentity.crmid = vtiger_invoice.invoiceid
                    WHERE vtiger_invoice.invoiceid = ?';
                        $paramsDetailInvoice = array($invoiceId);
                        $resultDetailInvoice = $adb->pquery($queryDetailInvoice, $paramsDetailInvoice);
                    }

                    $focus = new Invoice();
                    if (($entityData->get('mode') == '') || ($countInvoice == 0)) {
                        /* Création de la facture client */
                        $focus->mode = 'create';
                        $invoicestatus = 'AutoCreated';
                        $avoir = 0;
                    } else if ($entityData->get('mode') == 'edit') {
                        /* Mise à jour de la facture */
                        $focus->mode = 'edit';
                        $focus->id = $invoiceId;
                        $invoice_no = $adb->query_result($resultDetailInvoice, 0, 'invoice_no');
                        $avoir = $adb->query_result($resultDetailInvoice, 0, 'cf_1078');
                        $numero_facture = $adb->query_result($resultDetailInvoice, 0, 'cf_1033');
                        $datefacture = $adb->query_result($resultDetailInvoice, 0, 'createdtime');
                        $invoicestatus = $adb->query_result($resultDetailInvoice, 0, 'invoicestatus');
                        $recu = $adb->query_result($resultDetailInvoice, 0, 'received');
                        $montant_a_paye = $subtotal - $discount_amount;
                        $monfichier = fopen('debug_facture.txt', 'a+');
                        fputs($monfichier, "\n" . "datefacture " . $datefacture);
                        fclose($monfichier);
                        $datefacture = strtotime($datefacture);
                        $datefacture = date("Ymd", $datefacture);
                        $monfichier = fopen('debug_facture.txt', 'a+');
                        fputs($monfichier, "\n" . "datefacture après " . $datefacture);
                        fputs($monfichier, "\n" . "invoice_no " . $invoice_no);
                        fclose($monfichier);
                        /* Modification du numéro de facture */
                        $numero_facture_complet = $invoice_no . '-' . $datefacture;
                        $monfichier = fopen('debug_facture.txt', 'a+');
                        fputs($monfichier, "\n" . "numero_facture_complet " . $numero_facture_complet);
                        fclose($monfichier);
                    }

                    $focus->column_fields['subject'] = $subject;
                    $focus->column_fields['salesorder_id'] = $salesorderid;
                    $focus->column_fields['contact_id'] = $contactId;
                    $focus->column_fields['account_id'] = $idClient;
                    $focus->column_fields['lieu'] = $lieu;
                    $focus->column_fields['salle'] = $salle;
                    $focus->column_fields['session'] = $sessionId;
                    $focus->column_fields['cf_1028'] = $locaux;
                    $focus->column_fields['cf_1035'] = $type;
                    $focus->column_fields['bill_street'] = $adresse;
                    $focus->column_fields['bill_city'] = $ville;
                    $focus->column_fields['bill_code'] = $cp;
                    $focus->column_fields['cf_1026'] = $suiteAdresse;
                    $focus->column_fields['bill_state'] = $etat;
                    $focus->column_fields['cf_1004'] = 'Client';
                    $focus->column_fields['invoicestatus'] = $invoicestatus;
                    $focus->column_fields['cf_1033'] = $numero_facture_complet;
                    
                    //detail formation
                    $focus->column_fields['p_open_amount'] = $p_open_amount;
                    $focus->column_fields['p_paid_amount'] = $p_paid_amount;
                    $focus->column_fields['s_h_percent'] = $s_h_percent;
                    $focus->column_fields['pre_tax_total'] = $pre_tax_total;
                    $focus->column_fields['compound_taxes_info'] = $compound_taxes_info;
                    $focus->column_fields['conversion_rate'] = $conversion_rate;
                    $focus->column_fields['currency_id'] = $currency_id;
                    $focus->column_fields['purchaseorder'] = $purchaseorder;
                    $focus->column_fields['terms_conditions'] = $terms_conditions;
                    $focus->column_fields['s_h_amount'] = $s_h_amount;
                    $focus->column_fields['discount_amount'] = $discount_amount;
                    $focus->column_fields['discount_percent'] = $discount_percent;
                    $focus->column_fields['taxtype'] = $taxtype;
                    $focus->column_fields['subtotal'] = $subtotal;
                    $focus->column_fields['total'] = $total;
                    $focus->column_fields['exciseduty'] = $exciseduty;
                    $focus->column_fields['salescommission'] = $salescommission;
                    $focus->column_fields['adjustment'] = $adjustment;
                    $focus->column_fields['received'] = $recu;
                    $focus->column_fields['balance'] = $subtotal - $recu;
                    $focus->column_fields['cf_1078'] = $avoir;
                    
                    
                    //fin detail formation
                    $focus->save("Invoice");
                    $invoiceId = $focus->id;
                }
                /* Mise à jour numéro de convention */
                $dateconvention = $entityData->get('createdtime');
                $salesorder_no = $entityData->get('salesorder_no');

                $dateconvention = strtotime($dateconvention);
                $dateconvention = date("Ymd", $dateconvention);

                $numero_convention = $salesorder_no . '-' . $dateconvention; //VA
                $sql = "UPDATE vtiger_salesordercf SET cf_982=? WHERE salesorderid=?";
                $adb->pquery($sql, array($numero_convention, $salesorderid));  

                /* uni_cnfsecrm - Changer type client par Client quand on lui ajoute une convention */
                $account_id = $entityData->get('account_id');
                $adb->pquery("UPDATE vtiger_account SET account_type=? WHERE accountid=?", array('Customer', $account_id));

                $id_session = $entityData->get('session');
                if ($id_session) {
                    $sequences_no = 0;
                    /* get numéro sequence session */
                    $sequence_session = array();
                    $sequenceQuery = 'SELECT sequence_no FROM vtiger_sessionsapprenantsrel WHERE id=?';
                    $sequenceParams = array($id_session);
                    $sequenceResult = $adb->pquery($sequenceQuery, $sequenceParams);
                    $sequenceCount = $adb->num_rows($sequenceResult);
                    if ($sequenceCount > 0) {
                        $sequences_no = $adb->query_result($sequenceResult, $sequenceCount - 1, 'sequence_no');
                    }

                    $apprenant_convention = array();
                    $apprenantQuery = 'SELECT apprenantid,sequence_no,etat,resultat,inscrit,convoque
                      FROM vtiger_inventoryapprenantsrel
                      WHERE id=?';
                    $apprenantParams = array($salesorderid);
                    $apprenantResult = $adb->pquery($apprenantQuery, $apprenantParams);
                    $apprenantCount = $adb->num_rows($apprenantResult);
                    if ($apprenantCount > 0) {
                        for ($i = 0; $i < $apprenantCount; $i++) {
                            $apprenantid = $adb->query_result($apprenantResult, $i, 'apprenantid');
                            $etat = $adb->query_result($apprenantResult, $i, 'etat');
                            $resultat = $adb->query_result($apprenantResult, $i, 'resultat');
                            $inscrit = $adb->query_result($apprenantResult, $i, 'inscrit');
                            $convoque = $adb->query_result($apprenantResult, $i, 'convoque');

                            $apprenantsQuery = 'SELECT apprenantid
                                FROM vtiger_sessionsapprenantsrel
                                WHERE id=? and apprenantid=?';
                            $apprenantsParams = array($id_session, $apprenantid);
                            $apprenantsResult = $adb->pquery($apprenantsQuery, $apprenantsParams);
                            $apprenantsCount = $adb->num_rows($apprenantsResult);

                            if ($apprenantsCount == 0) {
                                $sequences_no += 1;
                                $adb->pquery('INSERT INTO vtiger_sessionsapprenantsrel (id,apprenantid,sequence_no,etat,resultat,inscrit) 
                                    VALUES (?, ?, ?, ?, ?, ?)', array($id_session, $apprenantid, $sequences_no, $etat, $resultat, $inscrit));
                            }
                        }
                    }
                }


                /* uni_cnfsecrm - modif 67 - DE */
                $conventionAncienSession = 'SELECT ancien_session FROM vtiger_salesorder WHERE salesorderid=?';
                $conventionAncienParams = array($salesorderid);
                $conventionAncienResult = $adb->pquery($conventionAncienSession, $conventionAncienParams);
                $sessionIdBeforeSave = $adb->query_result($conventionAncienResult, 'ancien_session');

                if ($sessionIdBeforeSave != $sessionId && $sessionId != '' && $sessionIdBeforeSave != '') {
                    $sessionApprenantQuery = 'SELECT apprenantid FROM vtiger_inventoryapprenantsrel WHERE id=?';
                    $sessionapprenantParams = array($salesorderid);
                    $sessionApprenantResult = $adb->pquery($sessionApprenantQuery, $sessionapprenantParams);
                    $sessionApprenantCount = $adb->num_rows($sessionApprenantResult);
                    if ($sessionApprenantCount > 0) {
                        for ($i = 0; $i < $sessionApprenantCount; $i++) {
                            $apprenantid = $adb->query_result($sessionApprenantResult, $i, 'apprenantid');

                            $queryDelete = 'DELETE FROM vtiger_sessionsapprenantsrel WHERE id=? AND apprenantid=?';
                            $paramsDelete = array($sessionIdBeforeSave, $apprenantid);
                            $adb->pquery($queryDelete, $paramsDelete);
                        }
                    }
                }
                /* uni_cnfsecrm - modif 67 - FI */
            }
        }
    }

}
