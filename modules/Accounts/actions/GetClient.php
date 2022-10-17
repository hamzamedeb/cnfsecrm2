<?php

// unicnfsecrm_mod_41
class Accounts_GetClient_Action extends Vtiger_Action_Controller {

    function process(Vtiger_Request $request) {
        global $adb;
        $recordId = $request->get('record');
        $response = new Vtiger_Response();
        $testNom = $request->get('testnom');
        $type = $request->get('type');
        if ($type == 'phone') {
            $point = '.';
            $espace = ' ';
            $query = "SELECT phone FROM vtiger_account where replace(replace(phone,'.', ''),' ', '') = ?";
        } else if ($type == 'accountname') {
            $query = "SELECT accountname FROM vtiger_account where accountname = ?";
        } else if ($type == 'email') {
            $query = "SELECT email1 FROM vtiger_account where email1 = ?";
        }
        $params = array($testNom);
        $result = $adb->pquery($query, $params);
        //$nomClient = $adb->query_result($result, $type);
        $res_cnt = $adb->num_rows($result);

        if ($res_cnt > 0) {
            $test = true;
        } else {
            $test = false;
        }
        $info[] = array('reponse' => $test);

        $response->setResult($info);
        $response->emit();
    }

    function checkPermission(Vtiger_Request $request) {
        return;
    }

    function resolveReferenceLabel($id, $module = false) {
        if (empty($id)) {
            return '';
        }
        if ($module === false) {
            $module = getSalesEntityType($id);
        }
        $label = getEntityName($module, array($id));
        return decode_html($label[$id]);
    }

}
