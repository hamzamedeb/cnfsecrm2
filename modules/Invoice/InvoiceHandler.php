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
        $monfichier = fopen('debug_financ.txt', 'a+');
        fputs($monfichier, "\n" . "eventName " . $eventName);
        fclose($monfichier);
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
        /**
         * Adjust the balance amount against total & received amount
         * NOTE: beforesave the total amount will not be populated in event data.
         */
        /* uni_cnfsecrm - modif 91 - DEBUT */
        if ($eventName == 'vtiger.entity.beforesave') {
            $db = PearDatabase::getInstance();
            $invoiceId = $entityData->getId();
            $totalBeforeSave = $entityData->get('hdnGrandTotal');

            $queryUpdate = "UPDATE vtiger_invoice SET ancien_total=? WHERE invoiceid=?";
            $db->pquery($queryUpdate, array($totalBeforeSave, $invoiceId));
        }
        /* uni_cnfsecrm - modif 91 - FIN */

        if ($eventName == 'vtiger.entity.aftersave') {
// Trigger from other module (due to indirect save) need to be ignored - to avoid inconsistency.
//            if ($currentModule != 'Invoice') /* uni_cnfsecrm 03042020 */
//                return;

            $entityDelta = new VTEntityDelta();
            $invoiceid = $entityData->getId();
            $oldCurrency = $entityDelta->getOldValue($moduleName, $invoiceid, 'currency_id');
            $newCurrency = $entityDelta->getCurrentValue($moduleName, $invoiceid, 'currency_id');
            $oldConversionRate = $entityDelta->getOldValue($moduleName, $invoiceid, 'conversion_rate');
            $db = PearDatabase::getInstance();
            $wsid = vtws_getWebserviceEntityId('Invoice', $invoiceid);
            $wsrecord = vtws_retrieve($wsid, $current_user);

            $queryDetailInvoice = 'select invoice_no, createdtime, cf_1078,received,conversion_rate,balance,total,invoicestatus,salesorderid
                    FROM vtiger_invoice
                    INNER JOIN vtiger_invoicecf on vtiger_invoicecf.invoiceid = vtiger_invoice.invoiceid
                    INNER JOIN vtiger_crmentity on vtiger_crmentity.crmid = vtiger_invoice.invoiceid
                    WHERE vtiger_invoice.invoiceid = ?';
            $paramsDetailInvoice = array($invoiceid);
            $resultDetailInvoice = $db->pquery($queryDetailInvoice, $paramsDetailInvoice);
            $received = $db->query_result($resultDetailInvoice, 0, 'received');
            $conversion_rate = $db->query_result($resultDetailInvoice, 0, 'conversion_rate');
            $balance = $db->query_result($resultDetailInvoice, 0, 'balance');
            $total = $db->query_result($resultDetailInvoice, 0, 'total');
            $invoicestatus = $db->query_result($resultDetailInvoice, 0, 'invoicestatus');
            $salesorderid = $db->query_result($resultDetailInvoice, 0, 'salesorderid');

            if ($oldCurrency != $newCurrency && $oldCurrency != '') {
                if ($oldConversionRate != '') {
                    $received = floatval(($received / $oldConversionRate) * $conversion_rate);
                }
            }
            $balance = floatval($total - $received);
            if ($balance == 0) {
                $invoicestatus = 'Paid';
            }

            $monfichier = fopen('debug_financ.txt', 'a+');
            fputs($monfichier, "\n" . "invoiceid " . $invoiceid);
            fclose($monfichier);

            $query = "UPDATE vtiger_invoice SET balance=?,received=? WHERE invoiceid=?";
            $db->pquery($query, array($balance, $received, $invoiceid));

            /* Mise à jour numéro de la facture */
            $totalTTC = $total;

            $invoice_no = $db->query_result($resultDetailInvoice, 0, 'invoice_no');
            $datefacture = $db->query_result($resultDetailInvoice, 0, 'createdtime');
            $avoir = $db->query_result($resultDetailInvoice, 0, 'cf_1078');

            $isAvoir = $_REQUEST["isAvoir"];
            $numfacture_initial = $_REQUEST["numfacture_initial"];

            if ($isAvoir != "true") { //Mise à jour numéro de facture non avoir
                $datefacture = strtotime($datefacture);
                $datefacture = date("Ymd", $datefacture);
                $numero_facture_complet = $invoice_no . '-' . $datefacture; //VA
                $sql = "UPDATE vtiger_invoicecf SET cf_1033=? WHERE invoiceid=?";
                $db->pquery($sql, array($numero_facture_complet, $invoiceid));
            }

            /* uni_cnfsecrm - v2 - modif 171 - DEBUT */
            $isRetro = $_REQUEST["retro"];
            if (($avoir == "1" && $isAvoir == "true") || $isRetro == "true" ) {
                $result = $db->pquery("SELECT invoice_no,cf_1033 FROM vtiger_invoice 
                        INNER JOIN vtiger_invoicecf on vtiger_invoicecf.invoiceid = vtiger_invoice.invoiceid 
                        WHERE vtiger_invoice.invoiceid = ?", array($invoiceid));
                $invoice_no_select = $db->query_result($result, 0, 'invoice_no');
                $numero_facture_select = $db->query_result($result, 0, 'cf_1033');

                $invoice_no_select_avoir = str_replace("FA", "AV", $invoice_no_select);
                $dateAvoir = date("Ymd");
                $numero_facture_select_avoir = $invoice_no_select_avoir . '-' . $dateAvoir; //VA

                $query = "UPDATE vtiger_invoicecf SET cf_1039=?,cf_1033=? WHERE invoiceid=?";
                $db->pquery($query, array($numero_facture_select, $numero_facture_select_avoir, $invoiceid));

                /* uni_cnfsecrm - v2 - modif 150 - DEBUT */
                $query = "UPDATE vtiger_invoice SET invoice_no=?,invoicestatus=? WHERE invoiceid=?";
                $db->pquery($query, array($invoice_no_select_avoir, 'Avoir', $invoiceid));
                /* uni_cnfsecrm - v2 - modif 150 - FIN */
                if ($isRetro == "true"){
                    $statut = "Paid";
                }else {
                    $statut = "Avoir";
                }
                $query = "UPDATE vtiger_invoice SET invoicestatus=? WHERE invoiceid=?";
                $db->pquery($query, array($statut, $numfacture_initial));

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
            /* uni_cnfsecrm - v2 - modif 171 - FIN */

            /* unicnfsecrm_gestimpaye_00 : mise a jour date echeance              */

            $querydatesession = "SELECT vtiger_activity.date_start,vtiger_activity.due_date,vtiger_activity.activityid,vtiger_invoice.invoicedate 
                FROM vtiger_activity 
                INNER JOIN vtiger_salesorder on vtiger_activity.activityid = vtiger_salesorder.session 
                INNER JOIN vtiger_invoice on vtiger_salesorder.salesorderid = vtiger_invoice.salesorderid 
                WHERE vtiger_invoice.invoiceid = ?";
            $resultdatesession = $db->pquery($querydatesession, array($invoiceid));
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
            $db->pquery($queryinvoice, array($date_echeance, $invoiceid));
            /* unicnfsecrm_gestimpaye_00 : FIN mise a jour date echeance */
            //unicnfsecrm_mod_12

            $queryDateCreate = 'SELECT vtiger_crmentity.createdtime FROM vtiger_crmentity WHERE vtiger_crmentity.crmid = ?';
            $paramsDateCreate = array($invoiceid);
            $invoiceDateCreate = $db->pquery($queryDateCreate, $paramsDateCreate);
            $a = substr($invoiceDateCreate, 12, 23);
            $dateinvoice = substr($a, 0, 11);

            $queryUpdateDate = 'UPDATE vtiger_invoice SET invoicedate=? WHERE invoiceid=?';
            $paramsUpdateDate = array($dateinvoice, $invoiceid);
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
                $querySession = 'SELECT session FROM vtiger_salesorder where salesorderid = ?';
                $paramsSession = array($idConvention);
                $resultSession = $db->pquery($querySession, $paramsSession);
                $idSession = $db->query_result($resultSession, 0, 'session');

                $queryUpdateInvoice = 'UPDATE vtiger_invoice SET session=? WHERE invoiceid=?';
                $paramsUpdatInvoice = array($idSession, $invoiceid);
                $resultUpdateInvoice = $db->pquery($queryUpdateInvoice, $paramsUpdatInvoice);
            }
            //fin unicnfsecrm_mod_28 

            /* uni_cnfsecrm - modif 91 - DEBUT */
            $queryAncienTotal = 'SELECT ancien_total,total FROM vtiger_invoice where invoiceid = ?';
            $paramsAncienTotal = array($invoiceid);
            $resultAncienTotal = $db->pquery($queryAncienTotal, $paramsAncienTotal);
            $total = $db->query_result($resultAncienTotal, 0, 'total');
            $ancienTotal = $db->query_result($resultAncienTotal, 0, 'ancien_total');

            $dateCreate = $entityData->get('createdtime');
            $dateCreate = new DateTime($dateCreate);
            $aneeeDateCreate = $dateCreate->format('Y');
            $jourDateCreate = $dateCreate->format('d');
            $dateNow = new DateTime();
            $aneeeDateNow = $dateNow->format('Y');
            $jourDateNow = $dateNow->format('d');
            if (($jourDateNow != $jourDateCreate) && ($aneeeDateNow == $aneeeDateCreate) && $total != $ancienTotal) {
                $queryUpdate = "UPDATE vtiger_invoicecf SET cf_1260=? WHERE invoiceid=?";
                // $db->pquery($queryUpdate, array('1', $invoiceid));
            }
            /* uni_cnfsecrm - modif 91 - FIN */

            /* uni_cnfsecrm - modif 93 - DEBUT */
            $idInvoiceParent = $_REQUEST["numfacture_initial"];
            if ($isAvoir == "true") {
                $queryUpdateStatut = 'UPDATE vtiger_invoice SET invoicestatus = ? WHERE invoiceid=?';
                $paramsUpdatStatut = array('Avoir', $idInvoiceParent);
                $resultUpdateStatut = $db->pquery($queryUpdateStatut, $paramsUpdatStatut);
            }
            /* uni_cnfsecrm - modif 93 - FIN */
        }
    }

}

?>