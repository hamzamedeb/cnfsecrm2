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

class EventsHandler extends VTEventHandler {

    function handleEvent($eventName, $entityData) {

        $moduleName = $entityData->getModuleName();

// Validate the event target
        if ($moduleName != 'Events') {
            return;
        }

//Get Current User Information
        global $current_user, $currentModule, $db;
        global $HELPDESK_SUPPORT_EMAIL_ID, $HELPDESK_SUPPORT_NAME;
        /**
         * Adjust the balance amount against total & received amount
         * NOTE: beforesave the total amount will not be populated in event data.
         */
        if ($eventName == 'vtiger.entity.aftersave') {
// Trigger from other module (due to indirect save) need to be ignored - to avoid inconsistency.
            if ($currentModule != 'Events')
                return;
            $monfichier = fopen('debug_test.txt', 'a+');
            fputs($monfichier, "\n" . "vtiger.entity.aftersave Events");
            fclose($monfichier);
            $entityDelta = new VTEntityDelta();
            $eventid = $entityData->getId();

            $db = PearDatabase::getInstance();

            /* uni_cnfsecrm - Remplir Nombre heures & Nombre jours automatiquement - begin */
            $duree_formations = array();
            $dateQuery = 'SELECT vtiger_sessionsdatesrel.id,vtiger_sessionsdatesrel.duree_formation 
                FROM vtiger_sessionsdatesrel 
                WHERE vtiger_sessionsdatesrel.id = ? ';
            $dateParams = array($eventid);
            $dateResult = $db->pquery($dateQuery, $dateParams);
            $dateCount = $db->num_rows($dateResult);
            if ($dateCount) {
                for ($i = 0; $i < $dateCount; $i++) {
                    $duree_formations[$i] = $db->query_result($dateResult, $i, 'duree_formation');
                    $count += $duree_formations[$i];
                }
            }
            /* uni_cnfsecrm - v2 - modif 128 - DEBUT */
//            $query = "UPDATE vtiger_activitycf 
//                SET vtiger_activitycf.cf_996=?,vtiger_activitycf.cf_998=? 
//                WHERE vtiger_activitycf.activityid=?";
//            $db->pquery($query, array($count, $dateCount, $entityData->getId())); 
            /* uni_cnfsecrm - v2 - modif 128 - FIN */
            /* uni_cnfsecrm - Remplir Nombre heures & Nombre jours automatiquement - end */

            /* unicnfsecrm_022020_08 - begin */
            $eventid = $entityData->getId();
            $sessionQuery = 'SELECT vtiger_activity.activityid,date_start,due_date,smownerid,cf_931,cf_933,cf_929,cf_1195,cf_927,cf_921,lieu,servicename,cf_1202
                FROM vtiger_activity  
                INNER JOIN vtiger_crmentity on vtiger_crmentity.crmid = vtiger_activity.activityid
                INNER JOIN vtiger_activitycf on vtiger_activitycf.activityid = vtiger_activity.activityid
                INNER JOIN vtiger_service on vtiger_service.serviceid = vtiger_activity.formation
                WHERE vtiger_activity.activityid = ?';
            $sessionParams = array($eventid);
            $sessionResult = $db->pquery($sessionQuery, $sessionParams);

            $date_start = $db->query_result($sessionResult, 0, 'date_start');
            $due_date = $db->query_result($sessionResult, 0, 'due_date');
            $id_formateur = $db->query_result($sessionResult, 0, 'smownerid');


            $ville = $db->query_result($sessionResult, 0, 'cf_931');
            $adresse = $db->query_result($sessionResult, 0, 'cf_933');
            $cp = $db->query_result($sessionResult, 0, 'cf_929');
            $region = $db->query_result($sessionResult, 0, 'cf_1195');
            $addresse_suite = $db->query_result($sessionResult, 0, 'cf_927');
            $locaux = $db->query_result($sessionResult, 0, 'cf_921');
            $lieu = $db->query_result($sessionResult, 0, 'lieu');
            $formation = $db->query_result($sessionResult, 0, 'servicename');
            $elearning = $db->query_result($sessionResult, 0, 'cf_1202');

            /* Récupérer les conventions liées à la session */
            $conventionQuery = 'SELECT vtiger_salesorder.salesorderid 
                FROM vtiger_salesorder 
                WHERE vtiger_salesorder.session = ?';
            $conventionParams = array($eventid);
            $conventionResult = $db->pquery($conventionQuery, $conventionParams);
            $conventionCount = $db->num_rows($conventionResult);
            $listconventions = array();
            if ($conventionCount) {

                /* Récupérer les journées liées à la session */
                $sessionjourneeQuery = 'SELECT id,sequence_no,date_start,start_matin,end_matin,start_apresmidi,end_apresmidi,duree_formation
                FROM vtiger_sessionsdatesrel  
                WHERE vtiger_sessionsdatesrel.id = ?';
                $sessionjourneeParams = array($eventid);
                $sessionjourneeResult = $db->pquery($sessionjourneeQuery, $sessionjourneeParams);
                $countDateJournee = $db->num_rows($sessionjourneeResult);
                $detail_journee_sessions = array();
                if ($countDateJournee) {
                    for ($i = 0; $i < $countDateJournee; $i++) {
                        $detail_journee_sessions[$i]['sequence_no'] = $db->query_result($sessionjourneeResult, $i, 'sequence_no');
                        $detail_journee_sessions[$i]['date_start'] = $db->query_result($sessionjourneeResult, $i, 'date_start');
                        $detail_journee_sessions[$i]['start_matin'] = $db->query_result($sessionjourneeResult, $i, 'start_matin');
                        $detail_journee_sessions[$i]['end_matin'] = $db->query_result($sessionjourneeResult, $i, 'end_matin');
                        $detail_journee_sessions[$i]['start_apresmidi'] = $db->query_result($sessionjourneeResult, $i, 'start_apresmidi');
                        $detail_journee_sessions[$i]['end_apresmidi'] = $db->query_result($sessionjourneeResult, $i, 'end_apresmidi');
                        $detail_journee_sessions[$i]['duree_formation'] = $db->query_result($sessionjourneeResult, $i, 'duree_formation');
                    }
                }

                for ($i = 0; $i < $conventionCount; $i++) {
                    $conventionid = $db->query_result($conventionResult, $i, 'salesorderid');

                    $querydate = "UPDATE vtiger_salesorder SET lieu=? WHERE salesorderid=?";
                    $db->pquery($querydate, array($lieu, $conventionid));

                    $querydate = "UPDATE vtiger_salesordercf SET cf_988=?,cf_990=?,cf_973=?,cf_860=? WHERE salesorderid=?";
                    $db->pquery($querydate, array($date_start, $due_date, $addresse_suite, $locaux, $conventionid));
                    //adresse
                    $queryadresse = "UPDATE vtiger_sobillads SET bill_city=?,bill_code=?,bill_street=?,bill_state=? WHERE sobilladdressid=?";
                    $db->pquery($queryadresse, array($ville, $cp, $adresse, $region, $conventionid));
                    //id formateur
                    $queryformateur = "UPDATE vtiger_crmentity SET smownerid=? WHERE crmid=?";
                    $db->pquery($queryformateur, array($id_formateur, $conventionid));

                    /* Supprimer les anciennes journées des convention */
                    $queryDelete = 'DELETE FROM vtiger_inventorydatesrel WHERE id=?';
                    $paramsDelete = array($conventionid);
                    $db->pquery($queryDelete, $paramsDelete);

                    /* Ajouter les nouveaux journées des convention */
                    foreach ($detail_journee_sessions as $detail_journee_session) {
                        $queryinsertjournee = "INSERT INTO vtiger_inventorydatesrel (id,sequence_no,date_start,start_matin,end_matin,start_apresmidi,end_apresmidi,duree_formation) VALUES (?,?, ?, ?, ?, ?, ?, ?)";
                        $paramsjournee = array($conventionid, $detail_journee_session['sequence_no'], $detail_journee_session['date_start'], $detail_journee_session['start_matin'], $detail_journee_session['end_matin'], $detail_journee_session['start_apresmidi'], $detail_journee_session['end_apresmidi'], $detail_journee_session['duree_formation']);
                        $db->pquery($queryinsertjournee, $paramsjournee);
                    }
                }
            }
            $monfichier = fopen('debug_test.txt', 'a+');
            fputs($monfichier, "\n" . "avant mode");
            fclose($monfichier);
            $mode = $entityData->get('mode');

            $monfichier = fopen('debug_test.txt', 'a+');
            fputs($monfichier, "\n" . "mode" . $mode);
            fclose($monfichier);

            //if ($mode == "edit" && $elearning == "1" && $eventid == "82872") {
            if ($elearning == "1") {
                $sqltemplate = "SELECT subject,body FROM vtiger_emailtemplates 
            WHERE vtiger_emailtemplates.templateid = ? ";
                $paramstemplate = array(34);
                $resulttemplate = $db->pquery($sqltemplate, $paramstemplate);
                $subject = $db->query_result($resulttemplate, 0, 'subject');
                $body = $db->query_result($resulttemplate, 0, 'body');

                $sessionAppQuery = "SELECT apprenantid,firstname,lastname,email,phone,emailenligne
                FROM vtiger_sessionsapprenantsrel 
                INNER JOIN vtiger_contactdetails ON vtiger_contactdetails.contactid = vtiger_sessionsapprenantsrel.apprenantid                
                WHERE emailenligne != ? AND id = ?";
//                    $monfichier = fopen('debug_test001.txt', 'a+');
//                fputs($monfichier, "\n" . 'SELECT apprenantid,firstname,lastname,email,phone,emailenligne
//                FROM vtiger_sessionsapprenantsrel 
//                INNER JOIN vtiger_contactdetails ON vtiger_contactdetails.contactid = vtiger_sessionsapprenantsrel.apprenantid                
//                WHERE id = '.$eventid.' and emailenligne != 1');
//                fclose($monfichier);
                $sessionAppParams = array(1, $eventid);
                $sessionAppResult = $db->pquery($sessionAppQuery, $sessionAppParams);
                $countsessionApp = $db->num_rows($sessionAppResult);
                $monfichier = fopen('debug_test001.txt', 'a+');
                fputs($monfichier, "\n" . "countsession " . $countsessionApp);
                fclose($monfichier);
                for ($j = 0; $j < $countsessionApp; $j++) {
                    $monfichier = fopen('debug_test001.txt', 'a+');
                    fputs($monfichier, "\n" . "j " . $j);
                    fclose($monfichier);
                    $apprenantid = $db->query_result($sessionAppResult, $j, 'apprenantid');
                    $monfichier = fopen('debug_test001.txt', 'a+');
                    fputs($monfichier, "\n" . "apprenantid " . $apprenantid);
                    fclose($monfichier);
                    $firstname = $db->query_result($sessionAppResult, $j, 'firstname');
                    $lastname = $db->query_result($sessionAppResult, $j, 'lastname');
                    $email = $db->query_result($sessionAppResult, $j, 'email');
                    $phone = $db->query_result($sessionAppResult, $j, 'phone');
                    $emailenligne = $db->query_result($sessionAppResult, $j, 'emailenligne');

                    $monfichier = fopen('debug_test.txt', 'a+');
                    fputs($monfichier, "\n" . "firstname" . $firstname);
                    //fputs($monfichier, "\n" . "subject" . $subject);
                    // fputs($monfichier, "\n" . "body" . $body);
                    fputs($monfichier, "\n" . "emailenligne" . $emailenligne);
                    fclose($monfichier);

                    $subject_mail = str_replace('$nomformation$', html_entity_decode($formation) . " - E-Learning", $subject);
                    $body_mail = str_replace('$formation$', $formation, $body);
                    $body_mail = str_replace('$prenom$', $firstname, $body_mail);
                    $body_mail = str_replace('$nom$', $lastname, $body_mail);
                    $body_mail = str_replace('$email$', $email, $body_mail);
                    $body_mail = str_replace('$tel$', $phone, $body_mail);

                    $emailenligne = "enligne@cnfse.fr";
                    //$emailenligne = "wajdi.bouasker@comsys.fr";

                    $monfichier = fopen('debug_test.txt', 'a+');
                    fputs($monfichier, "\n" . "avant envoi sur " . $emailenligne);
                    fclose($monfichier);

                    $querysession = "UPDATE vtiger_sessionsapprenantsrel SET emailenligne=? WHERE id=? and apprenantid =?";
                    $db->pquery($querysession, array(1, $eventid, $apprenantid));
                    //$bcc = array("wajdi.bouasker@comsys.fr"); 
                    //send_mail('Contacts', $emailenligne, $HELPDESK_SUPPORT_NAME, $HELPDESK_SUPPORT_EMAIL_ID, $subject_mail, $body_mail, '', '', '', '', '', true);
                    $monfichier = fopen('debug_test.txt', 'a+');
                    fputs($monfichier, "\n" . "après envoi ");
                    fclose($monfichier);
                }
            }

            /* unicnfsecrm_022020_08 - fin */
        }
    }

}

?>