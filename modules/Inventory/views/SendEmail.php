<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Inventory_SendEmail_View extends Vtiger_ComposeEmail_View {

    public function checkPermission(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        if (!Users_Privileges_Model::isPermitted($moduleName, 'index') || !Users_Privileges_Model::isPermitted('Emails', 'CreateView')) {
            throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
        }
    }

    /**
     * Function which will construct the compose email
     * This will handle the case of attaching the invoice pdf as attachment
     * @param Vtiger_Request $request 
     */
    //unicnfsecrm_mod_18
    public function composeMailData(Vtiger_Request $request) {
                $monfichier = fopen('debug_cnfse.txt', 'a+');
        fputs($monfichier, "\n" . ' Inventory_SendEmail_View ');
        fclose($monfichier);
        $adb = PearDatabase::getInstance();
        parent::composeMailData($request);
        $viewer = $this->getViewer($request);
        $inventoryRecordId = $request->get('record');
        $relance = $request->get('relance');
        /* unicnfsecrm_022020_17 */
        $factureCo = $request->get('factureCo');
        $recordModel = Vtiger_Record_Model::getInstanceById($inventoryRecordId, $request->getModule());

        if ($relance == 1) {

            $pdfFileName = $recordModel->getPDFFileName();
            $fileComponents = explode('/', $pdfFileName);
            $fileName = $fileComponents[count($fileComponents) - 1];
            array_pop($fileComponents);

            $sql1 = "SELECT vtiger_activity.activityid 
            FROM vtiger_activity 
            INNER JOIN vtiger_salesorder on vtiger_salesorder.session = vtiger_activity.activityid
            INNER JOIN vtiger_invoice on vtiger_invoice.salesorderid = vtiger_salesorder.salesorderid
            where vtiger_invoice.invoiceid = ?";
            $params1 = array($inventoryRecordId);
            $result1 = $adb->pquery($sql1, $params1);

            $activityid = $adb->query_result($result1, 0, 'activityid');

            $documents = array();

            $sql2 = "SELECT vtiger_senotesrel.notesid,filename
                FROM vtiger_senotesrel
                INNER JOIN vtiger_notes on vtiger_notes.notesid = vtiger_senotesrel.notesid
                INNER JOIN vtiger_crmentity on vtiger_crmentity.crmid = vtiger_senotesrel.notesid
                WHERE vtiger_senotesrel.crmid = ? and deleted = 0 ";
            $params2 = array($activityid);
            $result2 = $adb->pquery($sql2, $params2);
            $num_rows = $adb->num_rows($result2);

            for ($i = 0; $i < $num_rows; $i++) {
                $notesid = $adb->query_result($result2, $i, 'notesid');
                $nom = $adb->query_result($result2, $i, 'filename');

//                $r = print_r($nom, true);
//                $monfichier = fopen('debug_test.txt', 'a+');
//                fputs($monfichier, "\n" . "value" . $r);
//                fclose($monfichier);

                if (strstr($nom, "Feuille_Emargement.pdf")) {
                    $attachmentsid = $notesid + 1;
                } else {
                    $attachmentsid = "";
                }
            }

            if ($attachmentsid != '') {
                $sql4 = "SELECT attachmentsid,path FROM vtiger_attachments WHERE vtiger_attachments.attachmentsid = ? ";
                $params4 = array($attachmentsid);
                $result4 = $adb->pquery($sql4, $params4);
                $path = $adb->query_result($result4, 0, 'path');
            } else {
                $path = '';
            }

            $attachmentDetails = array(
                array(
                    'attachment' => $fileName,
                    'path' => implode('/', $fileComponents),
                    'size' => filesize($pdfFileName),
                    'type' => 'pdf',
                    'nondeletable' => true
                )
            );

            if ($num_rows > 0) {
                array_push($attachmentDetails, array(
                    'attachment' => $attachmentsid . '_' . $activityid . '_Feuille_Emargement.pdf',
                    'path' => $path,
                    'size' => filesize($path . $attachmentsid . '_' . $activityid . '_Feuille_Emargement.pdf'),
                    'type' => 'pdf',
                    'nondeletable' => true
                ));
            }

            $this->populateTo($request);
            $viewer->assign('ATTACHMENTS', $attachmentDetails);

            // unicnfsecrm_mod_21 inserer le template d'email facture impayer
            $sqlinvoice = "SELECT invoice_no,invoicedate,total,duedate,vtiger_invoicecf.cf_1185,vtiger_accountbillads.bill_city,vtiger_accountbillads.bill_code,vtiger_accountbillads.bill_street,vtiger_account.accountname 
                    FROM vtiger_invoice
                    INNER JOIN vtiger_invoicecf on vtiger_invoicecf.invoiceid = vtiger_invoice.invoiceid
                    INNER JOIN vtiger_accountbillads on vtiger_accountbillads.accountaddressid = vtiger_invoice.accountid
                    INNER JOIN vtiger_account on vtiger_account.accountid = vtiger_invoice.accountid 
                     WHERE vtiger_invoice.invoiceid = ? ";
            $paramsinvoice = array($inventoryRecordId);
            $resultinvoice = $adb->pquery($sqlinvoice, $paramsinvoice);

            $invoice_no = $adb->query_result($resultinvoice, 0, 'invoice_no');
            $invoicedate = $adb->query_result($resultinvoice, 0, 'invoicedate');
            $total = $adb->query_result($resultinvoice, 0, 'total');           
            $duedate = $adb->query_result($resultinvoice, 0, 'duedate');
            $etat_echeance = $adb->query_result($resultinvoice, 0, 'cf_1185');
            $etat_echeance = html_entity_decode($etat_echeance);
            $nom_client = $adb->query_result($resultinvoice, 0, 'accountname');
            $adresse_client = $adb->query_result($resultinvoice, 0, 'bill_street');
            $ville_client = $adb->query_result($resultinvoice, 0, 'bill_city');
            $cp_client = $adb->query_result($resultinvoice, 0, 'bill_code');

            
            $sqltemplate = "SELECT vtiger_emailtemplates.body FROM vtiger_emailtemplates WHERE vtiger_emailtemplates.templateid = ? ";
            /* uni_cnfsecrm - v2 - modif 141 - DEBUT */
            if ($etat_echeance == 'Dépassé de 7 jours'){
               $idTemplateEmail = 43; 
            }else if ($etat_echeance == 'Dépassé de 14 jours'){
                $idTemplateEmail = 44;
            }else if ($etat_echeance == 'Dépassé de 30 jours'){
                $idTemplateEmail = 45;
            }
            $paramstemplate = array($idTemplateEmail);
            /* uni_cnfsecrm - v2 - modif 141 - FIN */
            $resulttemplate = $adb->pquery($sqltemplate, $paramstemplate);
            $description = $adb->query_result($resulttemplate, 0, 'body');

            $invoicedate = date("d-m-Y", strtotime($invoicedate));
            $total = number_format($total, 2, '.', '');
            $duedate = date("d-m-Y", strtotime($duedate));

            $body = str_replace('num_facture', $invoice_no, $description);
            $body = str_replace('date_facture', $invoicedate, $body);
            $body = str_replace('montant_facture', $total, $body);
            $body = str_replace('date_echeance', $duedate, $body);
            $body = str_replace('nom_client', $nom_client, $body);
            $body = str_replace('adresse_client', $adresse_client, $body);
            $body = str_replace('ville_client', $ville_client, $body);
            $body = str_replace('cp_client', $cp_client, $body);

            if ($etat_echeance == 'Dépassé de 7 jours') {
                $subject = 'Facture impayée - Relance 1';
//                $sql = "UPDATE vtiger_invoicecf SET cf_1189 = ? WHERE vtiger_invoicecf.invoiceid = ? ";
//                $params = array(1, $inventoryRecordId);
//                $result = $adb->pquery($sql, $params);
            } elseif ($etat_echeance == 'Dépassé de 14 jours') {
                $subject = 'Facture impayée - Relance 2';
//                $sql = "UPDATE vtiger_invoicecf SET cf_1191 = ? WHERE vtiger_invoicecf.invoiceid = ? ";
//                $params = array(1, $inventoryRecordId);
//                $result = $adb->pquery($sql, $params);
            } elseif ($etat_echeance == 'Dépassé de 30 jours') {
                $subject = 'Facture impayée - Relance 3';
//                $sql = "UPDATE vtiger_invoicecf SET cf_1193 = ? WHERE vtiger_invoicecf.invoiceid = ? ";
//                $params = array(1, $inventoryRecordId);
//                $result = $adb->pquery($sql, $params);
            }

            $viewer->assign('SUBJECT', $subject);
            $viewer->assign('DESCRIPTION', $body);
            // fin unicnfsecrm_mod_21

            echo $viewer->view('ComposeEmailFormPayer.tpl', 'Emails', true);
        } else if ($factureCo == 1) {  /* unicnfsecrm_022020_17 */                
                //chercher la convention relative au facture
                $queryConvention = "SELECT vtiger_invoice.salesorderid 
                    FROM vtiger_invoice 
                    WHERE vtiger_invoice.invoiceid = ? ";
                $paramsConvention = array($inventoryRecordId);
                $resultConvention = $adb->pquery($queryConvention, $paramsConvention);
                $idConvention = $adb->query_result($resultConvention, 0, 'salesorderid');
                //fin recheche
                //pdf Convention
                if (($idConvention != null) || ($idConvention != '')) {
                    $recordModelConvention = Vtiger_Record_Model::getInstanceById($idConvention, 'SalesOrder');
                    $pdfFileNameConvention = $recordModelConvention->getPDFFileName();
                    $fileComponentsConvention = explode('/', $pdfFileNameConvention);
                    $fileNameConvention = $fileComponentsConvention[count($fileComponentsConvention) - 1];
                    array_pop($fileComponentsConvention);
                    //fin pdf Convention
                    //chercher la convocation
                    $recordModelConvocation = Vtiger_Record_Model::getInstanceById($idConvention, 'SalesOrder');
                    $pdfFileNameConvocation = $recordModelConvocation->getPDFFileNameCONVOCATION();
                    $fileComponentsConvocation = explode('/', $pdfFileNameConvocation);
                    $fileNameConvocation = $fileComponentsConvocation[count($fileComponentsConvocation) - 1];
                    array_pop($fileComponentsConvocation);
                    //fin chercher convocation
                    $pdfFileName = $recordModel->getPDFFileName();
                    $fileComponents = explode('/', $pdfFileName);
                    $fileName = $fileComponents[count($fileComponents) - 1];
                    array_pop($fileComponents);
                    $attachmentDetails = array(
                        array(
                            'attachment' => $fileName,
                            'path' => implode('/', $fileComponents),
                            'size' => filesize($pdfFileName),
                            'type' => 'pdf',
                            'nondeletable' => true
                        ), array(
                            'attachment' => $fileNameConvention,
                            'path' => implode('/', $fileComponentsConvention),
                            'size' => filesize($pdfFileNameConvention),
                            'type' => 'pdf',
                            'nondeletable' => true
                        ), array(
                            'attachment' => $fileNameConvocation,
                            'path' => implode('/', $fileComponentsConvocation),
                            'size' => filesize($pdfFileNameConvocation),
                            'type' => 'pdf',
                            'nondeletable' => true
                        )
                    );
                    $this->populateTo($request);
                    $viewer->assign('ATTACHMENTS', $attachmentDetails);
                    echo $viewer->view('ComposeEmailForm.tpl', 'Emails', true);
                } else {
                    $pdfFileName = $recordModel->getPDFFileName();
                    $fileComponents = explode('/', $pdfFileName);
                    $fileName = $fileComponents[count($fileComponents) - 1];
                    array_pop($fileComponents);
                    $attachmentDetails = array(
                        array(
                            'attachment' => $fileName,
                            'path' => implode('/', $fileComponents),
                            'size' => filesize($pdfFileName),
                            'type' => 'pdf',
                            'nondeletable' => true
                        )
                    );
                    $this->populateTo($request);
                    $viewer->assign('ATTACHMENTS', $attachmentDetails);
                    echo $viewer->view('ComposeEmailForm.tpl', 'Emails', true);
                }
            } else {
            $pdfFileName = $recordModel->getPDFFileName();
            $fileComponents = explode('/', $pdfFileName);
            $fileName = $fileComponents[count($fileComponents) - 1];
            array_pop($fileComponents);
            $attachmentDetails = array(
                array(
                    'attachment' => $fileName,
                    'path' => implode('/', $fileComponents),
                    'size' => filesize($pdfFileName),
                    'type' => 'pdf',
                    'nondeletable' => true
                )
            );
            $this->populateTo($request);
            $viewer->assign('ATTACHMENTS', $attachmentDetails);
            echo $viewer->view('ComposeEmailForm.tpl', 'Emails', true);
        }
    }

// fin unicnfsecrm_mod_18 

    public function populateTo($request) {
        $viewer = $this->getViewer($request);

        $inventoryRecordId = $request->get('record');
        $recordModel = Vtiger_Record_Model::getInstanceById($inventoryRecordId, $request->getModule());
        $inventoryModule = $recordModel->getModule();
        $inventotyfields = $inventoryModule->getFields();

        $toEmailConsiderableFields = array('contact_id', 'account_id', 'vendor_id');
        $db = PearDatabase::getInstance();
        $to = array();
        $to_info = array();
        $toMailNamesList = array();
        foreach ($toEmailConsiderableFields as $fieldName) {
            if (!array_key_exists($fieldName, $inventotyfields)) {
                continue;
            }
            $fieldModel = $inventotyfields[$fieldName];
            if (!$fieldModel->isViewable()) {
                continue;
            }
            $fieldValue = $recordModel->get($fieldName);
            if (empty($fieldValue)) {
                continue;
            }
            $referenceModule = Vtiger_Functions::getCRMRecordType($fieldValue);
            $fieldLabel = decode_html(Vtiger_Util_Helper::getRecordName($fieldValue));
            $referenceModuleModel = Vtiger_Module_Model::getInstance($referenceModule);
            if (!$referenceModuleModel) {
                continue;
            }
            if (isRecordExists($fieldValue)) {
                $referenceRecordModel = Vtiger_Record_Model::getInstanceById($fieldValue, $referenceModule);
                if ($referenceRecordModel->get('emailoptout')) {
                    continue;
                }
            }
            $emailFields = $referenceModuleModel->getFieldsByType('email');
            if (count($emailFields) <= 0) {
                continue;
            }

            $current_user = Users_Record_Model::getCurrentUserModel();
            $queryGenerator = new QueryGenerator($referenceModule, $current_user);
            $queryGenerator->setFields(array_keys($emailFields));
            $query = $queryGenerator->getQuery();
            $query .= ' AND crmid = ' . $fieldValue;

            $result = $db->pquery($query, array());
            $num_rows = $db->num_rows($result);
            if ($num_rows <= 0) {
                continue;
            }
            foreach ($emailFields as $fieldName => $emailFieldModel) {
                $emailValue = $db->query_result($result, 0, $fieldName);
                if (!empty($emailValue)) {
                    $to[] = $emailValue;
                    $to_info[$fieldValue][] = $emailValue;
                    $toMailNamesList[$fieldValue][] = array('label' => decode_html($fieldLabel), 'value' => $emailValue);
                    break;
                }
            }
            if (!empty($to)) {
                break;
            }
        }
        $viewer->assign('TO', $to);
        $viewer->assign('TOMAIL_NAMES_LIST', json_encode($toMailNamesList, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP));
        $viewer->assign('TOMAIL_INFO', $to_info);
    }

}
