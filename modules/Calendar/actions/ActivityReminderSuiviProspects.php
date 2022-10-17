<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */
/* uni_cnfsecrm - reminder recyclage */
/* uni_cnfsecrm - v2 - modif 130 - FILE */

class Calendar_ActivityReminderSuiviProspects_Action extends Vtiger_Action_Controller {

    function __construct() {
        $this->exposeMethod('getReminders');
        $this->exposeMethod('postpone');
    }

    public function checkPermission(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);

        $userPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        $permission = $userPrivilegesModel->hasModulePermission($moduleModel->getId());

        if (!$permission) {
            throw new AppException(vtranslate('LBL_PERMISSION_DENIED'));
        }
    }

    public function process(Vtiger_Request $request) {
        $mode = $request->getMode();
        if (!empty($mode) && $this->isMethodExposed($mode)) {
            $this->invokeExposedMethod($mode, $request);
            return;
        }
    }

    function getReminders(Vtiger_Request $request) {
        $recordModels = SuiviProspects_Module_Model::getSuiviProspectsReminder();
        $r = print_r($recordModels, true);
        $monfichier = fopen('debug_getReminders.txt', 'a+');
        fputs($monfichier, "\n" . "value " . $r);
        fclose($monfichier);
        foreach ($recordModels as $record) { 
            $records[] = $record->getDisplayableValues();
            $record->updateReminderSuiviProspectsStatus();
        }
        $response = new Vtiger_Response();
        $response->setResult($records);
        $response->emit();
    }

    function postpone(Vtiger_Request $request) {
        $recordId = $request->get('record');
        $module = $request->getModule();
        $recordModel = Vtiger_Record_Model::getInstanceById($recordId, 'SuiviProspects');
        $recordModel->updateReminderSuiviProspectsStatus(0);
    }

}
