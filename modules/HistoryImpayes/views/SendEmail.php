<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */
/* uni_cnfsecrm - modif 102 - FILE */
class HistoryImpayes_SendEmail_View extends Vtiger_ComposeEmail_View {

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
        $viewer = $this->getViewer($request);
        $recordId = $request->get('record');
        $recordModel = Vtiger_Record_Model::getInstanceById($recordId, $request->getModule());
        $to[] = $request->get('email');
        $viewer->assign('TO', $to);
        echo $viewer->view('ComposeEmailForm.tpl', 'Emails', true);
    }
}
