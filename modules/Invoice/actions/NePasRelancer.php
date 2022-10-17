<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

// unicnfsecrm_mod_19
class Invoice_NePasRelancer_Action extends Vtiger_Save_Action {

    public function process(Vtiger_Request $request) {
        $adb = PearDatabase::getInstance();

        $recordId = $request->get('record');




        $sql1 = "UPDATE vtiger_invoicecf SET vtiger_invoicecf.cf_1185=? WHERE invoiceid=?";
        $params1 = array('A ne pas relancer', $recordId);
        $result1 = $adb->pquery($sql1, $params1);

        $info = array('reponse' => 'ok');
        $response = new Vtiger_Response(); 
        $response->setResult($info);
        $response->emit();
    }

}
