<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

//unicnfsecrm_gestimpaye_04 : Etat échéance
class Invoice_EtatEcheance_Action extends Vtiger_Save_Action {

    public function process(Vtiger_Request $request) {
        $adb = PearDatabase::getInstance();

        $recordId = $request->get('record');
        $datenow = date('Y-m-d');

        $r = print_r($datenow, true);
        $monfichier = fopen('debug_test.txt', 'a+');
        fputs($monfichier, "\n" . "value" . $r);
        fclose($monfichier);
        
        $sql1 = "UPDATE vtiger_invoicecf SET vtiger_invoicecf.cf_1187=? , vtiger_invoicecf.cf_1185=? WHERE invoiceid=?";
        $params1 = array($datenow,'Reporté de 7 jours', $recordId);
        $result1 = $adb->pquery($sql1, $params1);

        $info = array('reponse' => 'ok');
        $response = new Vtiger_Response();
        $response->setResult($info);
        $response->emit();
    }

        
}
