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

class SuiviProspects_Module_Model extends Vtiger_Module_Model {

    /**
     * Function returns Calendar Reminder record models
     * @return <Array of Calendar_Record_Model>
     */
    public static function getSuiviProspectsReminder() {
        $db = PearDatabase::getInstance();
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        $activityReminder = $currentUserModel->getCurrentUserActivityReminderInSeconds();
        $recordModels = array();

        if ($activityReminder != '') {
            date_default_timezone_set("Europe/Paris");
            $currentTime = time();
            $date = date('Y-m-d', strtotime("+$activityReminder seconds", $currentTime));
            $time = date('H:i', strtotime("+$activityReminder seconds", $currentTime));
            $reminderActivitiesResult = "SELECT vtiger_suiviprospects.suiviprospectsid,name, 
            prospect, devis, vtiger_suiviprospects.status 
            FROM vtiger_suiviprospects 
            INNER JOIN vtiger_crmentity ON vtiger_suiviprospects.suiviprospectsid = vtiger_crmentity.crmid 
            INNER JOIN vtiger_suiviprospectscf on vtiger_suiviprospectscf.suiviprospectsid = vtiger_suiviprospects.suiviprospectsid 
            WHERE vtiger_crmentity.deleted = ? AND vtiger_suiviprospectscf.cf_1293 = ? 
            AND (DATE_FORMAT(cf_1295,'%Y-%m-%d') <= ?) AND vtiger_suiviprospects.status = ? 
            and vtiger_crmentity.smownerid = ? LIMIT 20";

            $result = $db->pquery($reminderActivitiesResult, array(0, "Désire être rappeler", $date, 0, $currentUserModel->getId()));
            $rows = $db->num_rows($result);
            for ($i = 0; $i < $rows; $i++) {
                $recordId = $db->query_result($result, $i, 'suiviprospectsid');
                $recordModels[] = Vtiger_Record_Model::getInstanceById($recordId, 'SuiviProspects');
            }
        }
        return $recordModels;
    }

}
