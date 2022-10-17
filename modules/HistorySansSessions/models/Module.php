<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */
vimport('~~/vtlib/Vtiger/Module.php');

/* uni_cnfsecrm - v2 - modif 130 - FILE */
class HistorySansSessions_Module_Model extends Vtiger_Module_Model {

    /**
     * Function returns Calendar Reminder record models
     * @return <Array of Calendar_Record_Model>
     */
    public static function getSansSessionReminder() {
        $db = PearDatabase::getInstance();
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        $activityReminder = $currentUserModel->getCurrentUserActivityReminderInSeconds();
        $recordModels = array();

        if ($activityReminder != '') {
            date_default_timezone_set("Europe/Paris");
            $currentTime = time();
            $date = date('Y-m-d', strtotime("+$activityReminder seconds", $currentTime));
            $time = date('H:i', strtotime("+$activityReminder seconds", $currentTime));
            $reminderActivitiesResult = "SELECT vtiger_historysanssessions.historysanssessionsid, name, apprenant, vtiger_historysanssessions.status
                FROM vtiger_historysanssessions 
                INNER JOIN vtiger_crmentity ON vtiger_historysanssessions.historysanssessionsid = vtiger_crmentity.crmid 
                INNER JOIN vtiger_historysanssessionscf on vtiger_historysanssessionscf.historysanssessionsid = vtiger_historysanssessions.historysanssessionsid 
                WHERE vtiger_crmentity.deleted = ? AND vtiger_historysanssessionscf.cf_1281 = ?
                AND (DATE_FORMAT(cf_1283,'%Y-%m-%d') <= ?) 
                AND vtiger_historysanssessions.status = ? and vtiger_crmentity.smownerid = ? LIMIT 20";  

            $result = $db->pquery($reminderActivitiesResult, array(0,"Désire être rappeler" ,$date, 0, $currentUserModel->getId() ));
            $rows = $db->num_rows($result);
            for ($i = 0; $i < $rows; $i++) {
                $recordId = $db->query_result($result, $i, 'historysanssessionsid');
                $recordModels[] = Vtiger_Record_Model::getInstanceById($recordId, 'HistorySansSessions');
            }
        }
        return $recordModels;
    }
    
    
}
