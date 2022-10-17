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
class Invoice_marquerRelance_Action extends Vtiger_Save_Action {

    public function process(Vtiger_Request $request) {
        $adb = PearDatabase::getInstance();

        $recordId = $request->get('record');
        $etat_echeance = $request->get('etat_echeance');

        if ($etat_echeance == 'Dépassé de 7 jours') {
            $subject = 'Facture impayée - Relance 1';
            $sql = "UPDATE vtiger_invoicecf SET cf_1189 = ? WHERE vtiger_invoicecf.invoiceid = ? ";
            $params = array(1, $recordId);
            $result = $adb->pquery($sql, $params);
        } elseif ($etat_echeance == 'Dépassé de 14 jours') {
            $subject = 'Facture impayée - Relance 2';
            $sql = "UPDATE vtiger_invoicecf SET cf_1191 = ? WHERE vtiger_invoicecf.invoiceid = ? ";
            $params = array(1, $recordId);
            $result = $adb->pquery($sql, $params);
        } elseif ($etat_echeance == 'Dépassé de 30 jours') {
            $subject = 'Facture impayée - Relance 3';
            $sql = "UPDATE vtiger_invoicecf SET cf_1193 = ? WHERE vtiger_invoicecf.invoiceid = ? ";
            $params = array(1, $recordId);
            $result = $adb->pquery($sql, $params);
        }

        $info = array('reponse' => 'ok');
        $response = new Vtiger_Response();
        $response->setResult($info);
        $response->emit();
    }

}
