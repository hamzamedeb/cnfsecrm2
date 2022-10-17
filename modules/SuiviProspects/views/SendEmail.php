<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */
/* uni_cnfsecrm - v2 - modif 120 - FILE */
class SuiviProspects_SendEmail_View extends Vtiger_ComposeEmail_View {

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
        $adb = PearDatabase::getInstance();
        parent::composeMailData($request);
        $viewer = $this->getViewer($request);
        $record = $request->get('record');
        var_dump($record);
        $sqlEmail = "SELECT email1 FROM vtiger_account WHERE accountid = ? ";
        $paramsEmail = array($record);
        $resultEmail = $adb->pquery($sqlEmail, $paramsEmail);
        $emailClient = $adb->query_result($resultEmail, 0, 'email1');
        
        //$recordModel = Vtiger_Record_Model::getInstanceById($inventoryRecordId, $request->getModule());
        $sqltemplate = "SELECT subject,body FROM vtiger_emailtemplates WHERE vtiger_emailtemplates.templateid = ? ";
        $paramstemplate = array(42);
        $resulttemplate = $adb->pquery($sqltemplate, $paramstemplate);
        $subject = $adb->query_result($resulttemplate, 0, 'subject');
        $description = $adb->query_result($resulttemplate, 0, 'body');

        $to[] = $emailClient;
        $viewer->assign('TO', $to);
        $viewer->assign('SUBJECT', $subject);
        $viewer->assign('DESCRIPTION', $description);

        echo $viewer->view('ComposeEmailForm.tpl', 'Emails', true);
    }
}
