<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */
/* uni_cnfsecrm - v2 - modif 176 - FILE */
include_once 'modules/Events/EventsPDFController.php';
require_once 'docHabilitation.php';

class Events_SendEmailAttestationSpecifique_View extends Vtiger_ComposeEmail_View {

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
    public function composeMailData(Vtiger_Request $request) {
        parent::composeMailData($request);
        global $adb;
        $appr = $request->get('appr');
        $record = $request->get('record');
        $email = $request->get('email');
        $doc = $request->get('doc');
        $direction = $request->get('direction');
        $conventionId = $request->get('conventionId');
        if (empty($conventionId)) {
            $clientId = getSingleFieldValue("vtiger_seactivityrel", "crmid", "activityid", $record);
        } else {
            $clientId = getSingleFieldValue("vtiger_salesorder", "accountid", "salesorderid", $conventionId);
        }
        $id = $request->get('record');
        $query = "SELECT vtiger_service.servicename,vtiger_activity.formation 
                FROM vtiger_service 
                INNER JOIN vtiger_activity on vtiger_activity.formation = vtiger_service.serviceid 
                WHERE vtiger_activity.activityid = ?";
        $result = $adb->pquery($query, array($id));
        $num_rows_dates = $adb->num_rows($result);
        $nom_formation = $adb->query_result($result, '', 'servicename');
        if ($doc != "elearningdoc") {
            $sujet = $nom_formation;
        } else {
            $sqltemplate = "SELECT subject,body FROM vtiger_emailtemplates 
            WHERE vtiger_emailtemplates.templateid = ? ";
            $paramstemplate = array(34);
            $resulttemplate = $adb->pquery($sqltemplate, $paramstemplate);
            $subject = $adb->query_result($resulttemplate, 0, 'subject');
            $body = $adb->query_result($resulttemplate, 0, 'body');

            $sessionQuery = 'SELECT vtiger_activity.activityid,date_start,due_date,smownerid,cf_931,cf_933,cf_929,cf_1195,cf_927,cf_921,lieu,servicename,cf_1202
                FROM vtiger_activity  
                INNER JOIN vtiger_crmentity on vtiger_crmentity.crmid = vtiger_activity.activityid
                INNER JOIN vtiger_activitycf on vtiger_activitycf.activityid = vtiger_activity.activityid
                INNER JOIN vtiger_service on vtiger_service.serviceid = vtiger_activity.formation
                WHERE vtiger_activity.activityid = ?';
            $sessionParams = array($record);
            $sessionResult = $adb->pquery($sessionQuery, $sessionParams);
            $formation = $adb->query_result($sessionResult, 0, 'servicename');

            $sessionAppQuery = "SELECT apprenantid,firstname,lastname,email,phone,emailenligne
                FROM vtiger_sessionsapprenantsrel 
                INNER JOIN vtiger_contactdetails ON vtiger_contactdetails.contactid = vtiger_sessionsapprenantsrel.apprenantid                
                WHERE apprenantid= ? AND id = ?";

            $sessionAppParams = array($appr, $record);
            $sessionAppResult = $adb->pquery($sessionAppQuery, $sessionAppParams);
            $countsessionApp = $adb->num_rows($sessionAppResult);

            $firstname = $adb->query_result($sessionAppResult, 0, 'firstname');
            $lastname = $adb->query_result($sessionAppResult, 0, 'lastname');
            $email = $adb->query_result($sessionAppResult, 0, 'email');
            $phone = $adb->query_result($sessionAppResult, 0, 'phone');
            $emailenligne = $adb->query_result($sessionAppResult, 0, 'emailenligne');

            $subject_mail = str_replace('$nomformation$', html_entity_decode($formation) . " - E-Learning", $subject);
            $body_mail = str_replace('$formation$', $formation, $body);
            $body_mail = str_replace('$prenom$', $firstname, $body_mail);
            $body_mail = str_replace('$nom$', $lastname, $body_mail);
            $body_mail = str_replace('$email$', $email, $body_mail);
            $body_mail = str_replace('$tel$', $phone, $body_mail);
            $emailenligne = "enligne@cnfse.fr";

            $email = $emailenligne;
            $sujet = $subject_mail;
        }
        $viewer = $this->getViewer($request);
        if ($doc == "elearningdoc") {
            $viewer->assign('DESCRIPTION', $body_mail);
        }

        $calendarRecordId = $request->get('record');
        $recordModel = Vtiger_Record_Model::getInstanceById($calendarRecordId, $request->getModule());
        if ($doc == 'avisetattestation') {
            /**/
            $query = "SELECT vtiger_service.servicename,servicecategory,vtiger_activity.formation 
                FROM vtiger_service 
                INNER JOIN vtiger_activity on vtiger_activity.formation = vtiger_service.serviceid 
                WHERE vtiger_activity.activityid = ?";
            $result = $adb->pquery($query, array($id));
            $num_rows_dates = $adb->num_rows($result);
            $nom_formation = $adb->query_result($result, 0, 'servicename');
            $categorieFormation = $adb->query_result($result, 0, 'servicecategory');
            $listIdApprenantBySessions = $this->getListApprenantBySession($calendarRecordId, $direction);
            $attachmentDetails = [];
            foreach ($listIdApprenantBySessions as $apprenant) {
                $pdfFileNameAvis = $recordModel->getPDFFileName($apprenant, 'avis');
                $fileComponentsAvis = explode('/', $pdfFileNameAvis);

                $fileNameAvis = $fileComponentsAvis[count($fileComponentsAvis) - 1];
                array_pop($fileComponentsAvis);

                $pdfFileNameAttestation = $recordModel->getPDFFileName($apprenant, 'attestation');
                $fileComponentsAttestation = explode('/', $pdfFileNameAttestation);
                $fileNameAttestation = $fileComponentsAttestation[count($fileComponentsAttestation) - 1];
                array_pop($fileComponentsAttestation);
                $fileNameAvis = preg_replace("/&([a-z])[a-z]+;/i", "$1", $fileNameAvis);
                $fileNameAttestation = preg_replace("/&([a-z])[a-z]+;/i", "$1", $fileNameAttestation);
                exportDocHabilitation($apprenant, $record);
                array_push($attachmentDetails,
                        array(
                            'attachment' => $fileNameAvis,
                            'path' => implode('/', $fileComponentsAvis),
                            'size' => filesize($pdfFileNameAvis),
                            'type' => 'pdf',
                            'nondeletable' => true
                ));

                array_push($attachmentDetails,
                        array(
                            'attachment' => $fileNameAttestation,
                            'path' => implode('/', $fileComponentsAttestation),
                            'size' => filesize($pdfFileNameAttestation),
                            'type' => 'pdf',
                            'nondeletable' => true
                ));
                /* uni_cnfsecrm - v2 - modif 176 - DEBUT */
                /* TitreHabilitation_apprenant_ matricule */    
                if ($categorieFormation == 'HABILITATIONS') {
                    $firstname = html_entity_decode(getSingleFieldValue("vtiger_contactdetails", "firstname", "contactid", $apprenant)) ;
                    $lastname = html_entity_decode(getSingleFieldValue("vtiger_contactdetails", "lastname", "contactid", $apprenant)) ;
                    $matricule = getSingleFieldValue("vtiger_contactscf", "cf_1318", "contactid", $apprenant);
                    $nomDocument = "TitreHabilitation_" . $firstname . "_" . $lastname;
                    if ($matricule != "") {
                        $nomDocument .= "_" . $matricule;
                    }
                    $nomDocument .= ".pdf";

                    array_push($attachmentDetails, array(
                        'attachment' => $nomDocument,
                        'path' => 'storage/docHabilitation',
                        'size' => filesize('storage/docHabilitation/' . $nomDocument),
                        'type' => 'pdf',
                        'nondeletable' => true
                    ));
                }
                /* uni_cnfsecrm - v2 - modif 176 - FIN */
            }
        }
        $this->populateTo($request, $email, $sujet);
        $viewer->assign('ATTACHMENTS', $attachmentDetails);
        echo $viewer->view('ComposeEmailForm.tpl', 'Emails', true);
    }

    public function populateTo($request, $email, $sujet) {
        $viewer = $this->getViewer($request);
        $calendarRecordId = $request->get('record');
        $recordModel = Vtiger_Record_Model::getInstanceById($calendarRecordId, $request->getModule());
        $calendarModule = $recordModel->getModule();
        $inventotyfields = $calendarModule->getFields();

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
        $conventionId = $request->get('conventionId');
        $doc = $request->get('doc');
        if (empty($conventionId)) {
            $clientId = getSingleFieldValue("vtiger_seactivityrel", "crmid", "activityid", $calendarRecordId);
        } else {
            $clientId = getSingleFieldValue("vtiger_salesorder", "accountid", "salesorderid", $conventionId);
        }
//        $appr = $request->get('appr');
//        $direction = getSingleFieldValue("vtiger_contactscf", "cf_1320", "contactid", $appr);
        $direction = $request->get('direction');
        if ((($doc == "sendconvocation") || ($doc == "avisetattestation")) && ($clientId == 189380) && ($direction != "")) {
            $to = $this->getListEmailByDeriction($direction);
        } else {
            $to[] = $email;
        }

        $subject = $sujet;
        $viewer->assign('SUBJECT', $subject);
        $viewer->assign('TO', $to);
        $viewer->assign('TOMAIL_NAMES_LIST', json_encode($toMailNamesList, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP));
        $viewer->assign('TOMAIL_INFO', $to_info);
    }

    public function getListEmailByDeriction($direction) {
        global $adb;
        $tabEmail = [];
//        $appr = $request->get('appr');
//        $direction = getSingleFieldValue("vtiger_contactscf", "cf_1320", "contactid", $appr);
        $query = "select vtiger_contactdetails.email 
            from vtiger_contactdetails 
            inner join vtiger_contactscf on vtiger_contactscf.contactid = vtiger_contactdetails.contactid
            where vtiger_contactscf.cf_1320 like ? and cf_1316 = ?";
        $param = array($direction, 1);
        $result = $adb->pquery($query, $param);
        $count = $adb->num_rows($result);
        for ($i = 0; $i < $count; $i++) {
            $tabEmail[] = $adb->query_result($result, $i, 'email');
        }
        array_push($tabEmail, "pascal.drouet@paris.fr");
        array_push($tabEmail, "marilene.duditlieux@paris.fr");
        return $tabEmail;
    }

    function getListApprenantBySession($calendarRecordId, $direction) {
        global $adb;
        $tabApprenant = [];
        $query = "SELECT vtiger_sessionsapprenantsrel.apprenantid 
            FROM vtiger_sessionsapprenantsrel 
            INNER JOIN vtiger_contactscf on vtiger_contactscf.contactid = vtiger_sessionsapprenantsrel.apprenantid 
            where vtiger_sessionsapprenantsrel.id = ? AND vtiger_contactscf.cf_1320 like ?";
        $param = array($calendarRecordId, $direction);
        $result = $adb->pquery($query, $param);
        $count = $adb->num_rows($result);
        for ($i = 0; $i < $count; $i++) {
            $tabApprenant[] = $adb->query_result($result, $i, 'apprenantid');
        }
        return $tabApprenant;
    }

}
