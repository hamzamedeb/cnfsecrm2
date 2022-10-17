<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */
include_once 'modules/Events/EventsPDFController.php';
//uni_hamza
class Events_ExportLISTEAVISFAVORABLE_Action extends Inventory_ExportPDF_Action {
    /* uni_cnfsecrm */

    public function process(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $recordId = $request->get('record');
        $doc = $request->get('doc'); 
        $recordModel = Vtiger_Record_Model::getInstanceById($recordId, $moduleName);
        $recordModel->getLISTEAVISFAVORABLE('', $doc); 
        
    }

}