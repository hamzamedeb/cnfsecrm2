<?php

/*uni_cnfsecrm - v2 - modif 137 - FILE*/
class Calendar_GetNomFormation_Action extends Vtiger_Action_Controller {

    function process(Vtiger_Request $request) {
        global $adb;
        $recordId = $request->get('record');
        $response = new Vtiger_Response();
        $query = "select servicename FROM vtiger_service where serviceid = ?";
        $result = $adb->pquery($query, array($recordId));
        $nom = html_entity_decode($adb->query_result($result, 0, 'servicename'));
        $info = array('nom' => $nom);

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
