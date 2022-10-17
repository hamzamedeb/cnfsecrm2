<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */
include_once 'modules/Events/EventsPDFController.php';
require_once 'docHabilitation.php';

class Events_SendEmailPersSpecifique_View extends Vtiger_ComposeEmail_View {

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
        $directionAppr = $request->get('direct');
        $id = $request->get('record');
        $query = "SELECT vtiger_service.servicename,vtiger_activity.formation 
                FROM vtiger_service 
                INNER JOIN vtiger_activity on vtiger_activity.formation = vtiger_service.serviceid 
                WHERE vtiger_activity.activityid = ?";
        $result = $adb->pquery($query, array($id));
        $num_rows_dates = $adb->num_rows($result);
        $nom_formation = $adb->query_result($result, '', 'servicename');
        if ($doc != "elearningdoc") {
            $sujet = $nom_formation . "_" . $appr;
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
            $pdfFileNameAvis = $recordModel->getPDFFileName($appr, 'avis');
            $fileComponentsAvis = explode('/', $pdfFileNameAvis);

            $fileNameAvis = $fileComponentsAvis[count($fileComponentsAvis) - 1];
            array_pop($fileComponentsAvis);

            $pdfFileNameAttestation = $recordModel->getPDFFileName($appr, 'attestation');
            $fileComponentsAttestation = explode('/', $pdfFileNameAttestation);
            $fileNameAttestation = $fileComponentsAttestation[count($fileComponentsAttestation) - 1];
            array_pop($fileComponentsAttestation);

            $fileNameAvis = preg_replace("/&([a-z])[a-z]+;/i", "$1", $fileNameAvis);
            $fileNameAttestation = preg_replace("/&([a-z])[a-z]+;/i", "$1", $fileNameAttestation);

            $query = "SELECT vtiger_service.servicename,servicecategory,vtiger_activity.formation 
                FROM vtiger_service 
                INNER JOIN vtiger_activity on vtiger_activity.formation = vtiger_service.serviceid 
                WHERE vtiger_activity.activityid = ?";
            $result = $adb->pquery($query, array($id));
            $num_rows_dates = $adb->num_rows($result);
            $nom_formation = $adb->query_result($result, 0, 'servicename');
            $categorieFormation = $adb->query_result($result, 0, 'servicecategory');
            exportDocHabilitation($appr, $record);

            $attachmentDetails = array(
                array(
                    'attachment' => $fileNameAvis,
                    'path' => implode('/', $fileComponentsAvis),
                    'size' => filesize($pdfFileNameAvis),
                    'type' => 'pdf',
                    'nondeletable' => true
                ), array(
                    'attachment' => $fileNameAttestation,
                    'path' => implode('/', $fileComponentsAttestation),
                    'size' => filesize($pdfFileNameAttestation),
                    'type' => 'pdf',
                    'nondeletable' => true
                )
            );
            if ($categorieFormation == 'HABILITATIONS') {
                $firstname = getSingleFieldValue("vtiger_contactdetails", "firstname", "contactid", $appr);
                $lastname = getSingleFieldValue("vtiger_contactdetails", "lastname", "contactid", $appr);
                $matricule = getSingleFieldValue("vtiger_contactscf", "cf_1318", "contactid", $appr);
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
        } else if ($doc == "sendconvocation") {
            $pdfFileName = $recordModel->getPDFFileName($appr, $doc);
            $fileComponents = explode('/', $pdfFileName);

            $fileName = $fileComponents[count($fileComponents) - 1];
            array_pop($fileComponents);
            $attachmentDetails = array(array(
                    'attachment' => $fileName,
                    'path' => implode('/', $fileComponents),
                    'size' => filesize($pdfFileName),
                    'type' => 'pdf',
                    'nondeletable' => true
            ));
            $detailTemplate = $this->getBodyEmail($record, $directionAppr, $nom_formation);
            $viewer->assign('DESCRIPTION', $detailTemplate['body']);
            $date_start = getSingleFieldValue("vtiger_activity", "date_start", "activityid", $calendarRecordId);
            $date_start = DateTime::createFromFormat("Y-m-d", $date_start);
            $date_start = $date_start->format("d/m/Y");
            $attachmentDetails = $this->addPieceJointeSpecifique($attachmentDetails, $nom_formation);
            $attachmentDetails = $this->getPlanPdf($attachmentDetails, $request);
            $date_start = getSingleFieldValue("vtiger_activity", "date_start", "activityid", $calendarRecordId);
            $date_start = DateTime::createFromFormat("Y-m-d", $date_start);
            $date_start = $date_start->format("d/m/Y");
//            var_dump($dateNow);die();
            $sujet = "Convocation " . $nom_formation . "_" . $date_start;
        } else if ($doc != 'elearningdoc') {
            $pdfFileName = $recordModel->getPDFFileName($appr, $doc);
            $fileComponents = explode('/', $pdfFileName);

            $fileName = $fileComponents[count($fileComponents) - 1];
//remove the fileName
            array_pop($fileComponents);
//unicnfsecrm_mod_51
//  $fileName = preg_replace("/&([a-z])[a-z]+;/i", "$1", $fileName);
            $attachmentDetails = array(array(
                    'attachment' => $fileName,
                    'path' => implode('/', $fileComponents),
                    'size' => filesize($pdfFileName),
                    'type' => 'pdf',
                    'nondeletable' => true
            ));
        }
        $this->populateTo($request, $email, $sujet, $directionAppr);
        $viewer->assign('ATTACHMENTS', $attachmentDetails);
        echo $viewer->view('ComposeEmailForm.tpl', 'Emails', true);
    }

    public function populateTo($request, $email, $sujet, $direction) {
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
        $to = $this->getListEmailByDeriction($direction, $email);
        $subject = $sujet;
//$toMailNamesList[] = 'test';
//        $to_info = 'tet';
        $viewer->assign('SUBJECT', $subject);
        $viewer->assign('TO', $to);
        $viewer->assign('TOMAIL_NAMES_LIST', json_encode($toMailNamesList, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP));
        $viewer->assign('TOMAIL_INFO', $to_info);
    }

    /* function getBodyEmail() {
      global $adb;
      $query = 'SELECT body,subject FROM vtiger_emailtemplates WHERE templateid = ?';
      $result = $adb->pquery($query, array(18));
      $detailTemplate['body'] = decode_html($adb->query_result($result, 0, 'body'));
      $detailTemplate['subject'] = decode_html($adb->query_result($result, 0, 'subject'));
      return $detailTemplate;
      } */

    function getBodyEmail($calendarRecordId, $direction, $formation) {
//        var_dump($calendarRecordId)   ; 
//        var_dump($direction);
//        var_dump($formation);
//        die();
        global $adb;

        $query = 'SELECT body,subject FROM vtiger_emailtemplates WHERE templateid = ?';
        $result = $adb->pquery($query, array(57));
        $detailTemplate['body'] = decode_html($adb->query_result($result, 0, 'body'));
        $detailTemplate['subject'] = decode_html($adb->query_result($result, 0, 'subject'));

        $listApprenantBySessions = $this->getListApprenantBySession($calendarRecordId, $direction);
//        var_dump($listApprenantBySessions);die();
        /*  $listApprenant = " <ul> ";
          //        var_dump($listApprenantBySessions);die();
          foreach ($listApprenantBySessions as $apprenant) {
          //            var_dump($apprenant);

          $listApprenant .= "<li> " . $prenom . " " . $nom . " " . $matricule . " </li> ";
          }
          $listApprenant .= "</ul> "; */
        $listApprenant = "<table align='center' border='1' cellpadding='1' cellspacing='1' style='width:500px;'>";
        $listApprenant .= "<thead>";
        $listApprenant .= "<tr style='text-align: center;'>";
        $listApprenant .= "<th>Prénom</th>";
        $listApprenant .= "<th>Nom</th>";
        $listApprenant .= "<th>Matricule</th>";
        $listApprenant .= "</tr>";
        $listApprenant .= "</thead>";
        $listApprenant .= "<tbody>";
        foreach ($listApprenantBySessions as $apprenant) {
            $prenom = $apprenant['firstname'];
            $nom = $apprenant['lastname'];
            $matricule = $apprenant['matricule'];
            $listApprenant .= "<tr style='text-align: center;'>";
            $listApprenant .= "<td>" . $prenom . " </td> <td>" . $nom . "</td> <td> " . $matricule . "</td>";
            $listApprenant .= "</tr>";
        }
        $listApprenant .= "</tbody>";
        $listApprenant .= "</table>";
        //        var_dump($listApprenant);die();
        $detailTemplate['body'] = str_replace("list_apprenant", $listApprenant, $detailTemplate['body']);
        $detailTemplate['body'] = str_replace("intitule_formation", $formation, $detailTemplate['body']);

        //         var_dump($detailTemplate['body']);die();
        return $detailTemplate;
    }

    function getListApprenantBySession($calendarRecordId, $direction) {
        global $adb;
        $tabApprenant = [];

        $query = "SELECT vtiger_sessionsapprenantsrel.apprenantid, vtiger_contactdetails.firstname,lastname, cf_1318
            FROM vtiger_sessionsapprenantsrel  
            INNER JOIN vtiger_contactdetails on vtiger_contactdetails.contactid = vtiger_sessionsapprenantsrel.apprenantid  
            INNER JOIN vtiger_contactscf on vtiger_contactscf.contactid = vtiger_sessionsapprenantsrel.apprenantid 
            where vtiger_sessionsapprenantsrel.id = ? AND vtiger_contactscf.cf_1320 like ? ";
        $param = array($calendarRecordId, $direction);
        $result = $adb->pquery($query, $param);
        $count = $adb->num_rows($result);
        for ($i = 0; $i < $count; $i++) {
            $tabApprenant[$i]['id'] = $adb->query_result($result, $i, 'apprenantid');
            $tabApprenant[$i]['firstname'] = $adb->query_result($result, $i, 'firstname');
            $tabApprenant[$i]['lastname'] = $adb->query_result($result, $i, 'lastname');
            $tabApprenant[$i]['matricule'] = $adb->query_result($result, $i, 'cf_1318');
        }
        return $tabApprenant;
    }

    public function getListEmailByDeriction($direction, $email) {
//        die();
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
        if ($email != "") {
            array_push($tabEmail, $email);
        }
        for ($i = 0; $i < $count; $i++) {
            $tabEmail[] = $adb->query_result($result, $i, 'email');
        }
//        array_push($tabEmail, "");
        array_push($tabEmail, "pascal.drouet@paris.fr");
        array_push($tabEmail, "marilene.duditlieux@paris.fr");
        
        
        return $tabEmail;
    }

    function getPlanPdf($attachmentDetails, $request) {
        $calendarRecordId = $request->get('record');
        $recordModel = Vtiger_Record_Model::getInstanceById($calendarRecordId, $request->getModule());
        $pdfFileName = $recordModel->getPDFPlan($apprenant, $doc);
        $fileComponents = explode('/', $pdfFileName);
        $fileName = $fileComponents[count($fileComponents) - 1];
        array_pop($fileComponents);
        $attach = array(
            'attachment' => $fileName,
            'path' => implode('/', $fileComponents),
            'size' => filesize($pdfFileName),
            'type' => 'pdf',
            'nondeletable' => true
        );
        array_push($attachmentDetails, $attach);
        return $attachmentDetails;
    }

    public function addPieceJointeSpecifique($attachmentDetails, $nom_formation) {
        $array_docs = ["EPI.pdf", "Liste des restaurants.pdf", "Protocole Sanitaire.pdf"];
        foreach ($array_docs as $doc) {
            $attachment = array(
                'attachment' => $doc,
                'path' => "test/document_convocation/1",
                'size' => filesize("test/document_convocation/1/$doc"),
                'type' => 'pdf',
                'nondeletable' => true
            );
            array_push($attachmentDetails, $attachment);
        }
        $type_formation = explode(" ", $nom_formation);
        $type_formation = $type_formation[0];

        switch ($type_formation) {
            case "SST41BE":
                $doc_programme_formation = "SST41BE Habilitation BE B0 H0v.pdf";
                break;
            case "SST42":
                $doc_programme_formation = "SST42 Habilitation B1 B2 BC BR.pdf";
                break;
            case "SST43":
                $doc_programme_formation = "SST43 Habilitation H1 H2 HC.pdf";
                break;
            case "SST46BS":
                $doc_programme_formation = "SST46BS Intervetion BT.pdf";
                break;
            case "SST44CH":
                $doc_programme_formation = "SST44CH Recyclage B0 H0 H0v.pdf";
                break;
            case "SST41BS":
                $doc_programme_formation = "SST41BS Habilitation BS B0 H0v.pdf";
                break;
            case "SST40CH":
                $doc_programme_formation = "SST40CH Habilitation B0 H0 H0v 1.5j.pdf";
                break;
            case "SST40":
                $doc_programme_formation = "SST40 Habilitation B0 H0 H0v.pdf";
                break;
            case "SST45BS":
                $doc_programme_formation = "SST45BS Recyclage BS B0 H0v.pdf";
                break;
            case "SST44BE":
                $doc_programme_formation = "SST44BE Recyclage BE B0 H0v.pdf";
                break;
            case "SST45BT":
                $doc_programme_formation = "SST45BT Recyclage B1 B2 BC BR.pdf";
                break;
            case "SST45HT":
                $doc_programme_formation = "SST45HT Recyclage B1 B2 BC BR H1 H2.pdf";
                break;
        }
        if ($doc_programme_formation != "") {
            $attachment = array(
                'attachment' => $doc_programme_formation,
                'path' => "test/document_convocation/2",
                'size' => filesize("test/document_convocation/2/$doc_programme_formation"),
                'type' => 'pdf',
                'nondeletable' => true
            );
            array_push($attachmentDetails, $attachment);
        }
        return $attachmentDetails;
    }

}
