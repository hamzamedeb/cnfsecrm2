<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */
/* uni_cnfsecrm - v2 - modif 165 - FILE */

class Events_ExportIDENTIFIENTSELEARNING_Action extends Vtiger_Action_Controller {

    public function checkPermission(Vtiger_Request $request) {
        $moduleName = $request->getModule();

        if (!Users_Privileges_Model::isPermitted($moduleName, 'DetailView', $request->get('record'))) {
            throw new AppException(vtranslate('LBL_PERMISSION_DENIED', $moduleName));
        }
    }

    public function process(Vtiger_Request $request) {
        $recordId = $request->get('record');
        $listApprenant = self::listApprenants($recordId);

        // var_dump($listApprenant);die();

        header("Content-Type: text/plain");
        header("Content-disposition: attachment; filename=identifiants_elearning_$recordId.csv");

        $out = fopen('PHP://output', 'w');
        fputcsv($out, array(
            "Nom",
            utf8_decode(html_entity_decode("Prénom")),
            "Nom client",
            "Telephone",
            "Email",
            "Date de Naissance",
            "Identifiant",
            "Mot de passe",
                ), ",");

        foreach ($listApprenant as $apprenant) {
            fputcsv($out, array(
                utf8_decode(html_entity_decode($apprenant['lastname'])),
                utf8_decode(html_entity_decode($apprenant['firstname'])),
                utf8_decode(html_entity_decode($apprenant['accountname'])),
                utf8_decode(html_entity_decode($apprenant['telcontact'])),
                utf8_decode(html_entity_decode($apprenant['email'])),
                utf8_decode(html_entity_decode($apprenant['date_start'])),
                utf8_decode(html_entity_decode($apprenant['identifient'])),
                utf8_decode(html_entity_decode($apprenant['motdepasse'])),
                    ), ",");
        }
        fclose($out);
    }

    function listApprenants($recordId) {
        global $adb;
        $info_apprenants = array();
        $query = "SELECT id,salutation,firstname,lastname,vtiger_contactsubdetails.birthday,contactsubscriptionid,
            accountname,vtiger_account.phone as telclient,email,account_no,ticket_examen_test,
            type_tokens,type_tokens_test,date_start_appr , date_fin_appr , duree_jour , duree_heure,date_start,due_date,vtiger_account.accountid,vtiger_crmentity.createdtime,
            vtiger_contactdetails.phone as telcontact
            FROM vtiger_sessionsapprenantsrel 
            INNER JOIN vtiger_contactdetails on vtiger_contactdetails.contactid = vtiger_sessionsapprenantsrel.apprenantid 
            INNER JOIN vtiger_contactsubdetails on vtiger_contactsubdetails.contactsubscriptionid = vtiger_contactdetails.contactid 
            LEFT JOIN vtiger_account on vtiger_account.accountid = vtiger_contactdetails.accountid
            LEFT JOIN vtiger_activitycf on vtiger_activitycf.activityid = vtiger_sessionsapprenantsrel.id
            LEFT JOIN vtiger_activity on vtiger_activity.activityid = vtiger_sessionsapprenantsrel.id
            left JOIN vtiger_crmentity on vtiger_crmentity.crmid = vtiger_contactdetails.contactid
            WHERE vtiger_sessionsapprenantsrel.id = ?";
        $result = $adb->pquery($query, array($recordId));
        $num_rows_apprenants = $adb->num_rows($result);
        if ($num_rows_apprenants) {
            for ($i = 0; $i < $num_rows_apprenants; $i++) {
                $salutation = $adb->query_result($result, $i, 'salutation');
                $firstname = ucwords(formatString(strtolower(($adb->query_result($result, $i, 'firstname')))));
                $firstname = str_replace('"', '', $firstname);
                $firstname = str_replace('\'', '', $firstname);
                $lastname = strtoupper(formatString($adb->query_result($result, $i, 'lastname')));
                $lastname = str_replace('"', '', $lastname);
                $lastname = str_replace('\'', '', $lastname);
                $birthday_contact = $adb->query_result($result, $i, 'birthday');
                $title_contact = $adb->query_result($result, $i, 'title');
                $accountname = $adb->query_result($result, $i, 'accountname');
                $telcontact = $adb->query_result($result, $i, 'telcontact');
                $email = $adb->query_result($result, $i, 'email');
                $date_start = $adb->query_result($result, $i, 'date_start');

                $info_apprenants[$i]['salutation'] = $salutation; 
                $info_apprenants[$i]['firstname'] = $firstname;
                $info_apprenants[$i]['lastname'] = $lastname;
                $info_apprenants[$i]['birthday'] = $birthday_contact;
                $info_apprenants[$i]['title'] = $title_contact;
                $info_apprenants[$i]['accountname'] = $accountname;
                $info_apprenants[$i]['telcontact'] = $telcontact;
                $info_apprenants[$i]['email'] = $email;

                $identifientDate = strtotime($date_start);
                $identifientDate = date('dm', $identifientDate);
                
                $date_start = strtotime($date_start);
                $date_start = date('d/m/Y', $date_start);
               // var_dump($date_start);die();
                $info_apprenants[$i]['date_start'] = $date_start;

                /**/
                $identifient = strtolower($lastname . '' . $firstname.''.$identifientDate);
                $identifient = str_replace(' ', '', $identifient);
                $TB_CONVERT = array(
                    'Š' => 'S', 'š' => 's', 'Ð' => 'Dj', 'Ž' => 'Z', 'ž' => 'z', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A',
                    'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I',
                    'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U',
                    'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a',
                    'å' => 'a', 'æ' => 'a', 'ç' => 'c', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i',
                    'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ù' => 'u',
                    'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ý' => 'y', 'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y', 'ƒ' => 'f', 'œ' => 'oe'
                );

                $identifient = strtr($identifient, $TB_CONVERT);

                $identifient = preg_replace("#[^A-Z0-9\'\ ]#i", "", $identifient);
                $info_apprenants[$i]['identifient'] = $identifient;
                $info_apprenants[$i]['motdepasse'] = $identifient;
                /**/
            }
        }
        $info_apprenants['nbr_apprenants'] = $num_rows_apprenants;
        return $info_apprenants;
    }

}
