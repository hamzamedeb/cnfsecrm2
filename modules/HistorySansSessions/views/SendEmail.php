<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */
/* uni_cnfsecrm - v2 - modif 107 - FILE */
class HistorySansSessions_SendEmail_View extends Vtiger_ComposeEmail_View {

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
        /* uni_cnfsecrm - v2 - modif 121 - DEBUT */
        $sqlEmail = "SELECT vtiger_contactdetails.email ,vtiger_account.email1
            FROM vtiger_contactdetails 
            INNER JOIN vtiger_account on vtiger_account.accountid = vtiger_contactdetails.accountid
            WHERE contactid = ? ";
        $paramsEmail = array($record);
        $resultEmail = $adb->pquery($sqlEmail, $paramsEmail);
        if ($adb->query_result($resultEmail, 0, 'email') == ''){
            $emailAppr = $adb->query_result($resultEmail, 0, 'email1');
        }else {
            $emailAppr = $adb->query_result($resultEmail, 0, 'email');
        }
        /* uni_cnfsecrm - v2 - modif 121 - FIN */
        //$recordModel = Vtiger_Record_Model::getInstanceById($inventoryRecordId, $request->getModule());
        $sqltemplate = "SELECT subject,body FROM vtiger_emailtemplates WHERE vtiger_emailtemplates.templateid = ? ";
        $paramstemplate = array(41);
        $resulttemplate = $adb->pquery($sqltemplate, $paramstemplate);
        $subject = $adb->query_result($resulttemplate, 0, 'subject');
        $description = $adb->query_result($resulttemplate, 0, 'body');

        $to[] = $emailAppr;
        $viewer->assign('TO', $to);
        $viewer->assign('SUBJECT', $subject);
        $viewer->assign('DESCRIPTION', $description);

        echo $viewer->view('ComposeEmailForm.tpl', 'Emails', true);
    }
}
