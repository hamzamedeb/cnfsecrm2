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
class HistoryRecyclage_Module_Model extends Vtiger_Module_Model {

    /**
     * Function returns Calendar Reminder record models
     * @return <Array of Calendar_Record_Model>
     */
    public static function getRecyclageReminder() {
        $db = PearDatabase::getInstance();
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        $activityReminder = $currentUserModel->getCurrentUserActivityReminderInSeconds();
        $recordModels = array();

        if ($activityReminder != '') {
            date_default_timezone_set("Europe/Paris");
            $currentTime = time();
            $date = date('Y-m-d', strtotime("+$activityReminder seconds", $currentTime));
            $time = date('H:i', strtotime("+$activityReminder seconds", $currentTime));
            $reminderActivitiesResult = "SELECT vtiger_historyrecyclage.historyrecyclageid, name, 
            contacts, session, vtiger_historyrecyclage.status, vtiger_historyrecyclagecf.cf_1254, 
            cf_1256
            FROM vtiger_historyrecyclage 
            INNER JOIN vtiger_crmentity ON vtiger_historyrecyclage.historyrecyclageid = vtiger_crmentity.crmid 
            INNER JOIN vtiger_historyrecyclagecf on vtiger_historyrecyclagecf.historyrecyclageid = vtiger_historyrecyclage.historyrecyclageid 
            WHERE vtiger_crmentity.deleted = ? AND vtiger_historyrecyclagecf.cf_1254 = ? 
            AND (DATE_FORMAT(cf_1256,'%Y-%m-%d') <= ?) AND vtiger_crmentity.smownerid = ?
            AND vtiger_historyrecyclage.status = ? LIMIT 20"; //vtiger_crmentity.smownerid = ? $currentUserModel->getId()

            $result = $db->pquery($reminderActivitiesResult, array(0,"Désire être rappeler" ,$date,$currentUserModel->getId(), 0));
            $rows = $db->num_rows($result);
            for ($i = 0; $i < $rows; $i++) {
                $recordId = $db->query_result($result, $i, 'historyrecyclageid');
                $recordModels[] = Vtiger_Record_Model::getInstanceById($recordId, 'HistoryRecyclage');
            }
        }
        return $recordModels;
    }
    
    
}
