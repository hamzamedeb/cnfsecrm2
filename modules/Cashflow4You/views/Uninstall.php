<?php
/* * *******************************************************************************
 * The content of this file is subject to the Cashflow 4 You license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is IT-Solutions4You s.r.o.
 * Portions created by IT-Solutions4You s.r.o. are Copyright(C) IT-Solutions4You s.r.o.
 * All Rights Reserved.
 * ****************************************************************************** */

class Cashflow4You_Uninstall_View extends Vtiger_List_View {

    function checkPermission(Vtiger_Request $request) {
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        if(!$currentUserModel->isAdminUser()) {
                throw new AppException(vtranslate('LBL_PERMISSION_DENIED', 'Vtiger'));
        }
    }

    public function process(Vtiger_Request $request) {
        $viewer = $this->getViewer($request);
        $mode = $request->get('mode');
        $viewer->assign("MODE", $mode);             
        $viewer->view('Uninstall.tpl', 'Cashflow4You');         
    }
    
     function getHeaderScripts(Vtiger_Request $request) {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $jsFileNames = array(
            "layouts.v7.modules.Cashflow4You.resources.Uninstall"
        );
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        return $headerScriptInstances;
    } 
}     