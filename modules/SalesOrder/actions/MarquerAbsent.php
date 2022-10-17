<?php
/* uni_cnfsecrm - v2 - modif 94 - FILE */
class SalesOrder_MarquerAbsent_Action extends Vtiger_Action_Controller {

    function process(Vtiger_Request $request) {
        global $adb;
        $recordId = $request->get('record');
        $response = new Vtiger_Response();

        $apprenantId = $request->get('apprenantId');
        $sessionId = $request->get('sessionId');
        if (!is_null($sessionId)&&!is_null($apprenantId)){
            $adb->pquery("delete from vtiger_sessionsapprenantsrel where id=? and apprenantid=?", array($sessionId,$apprenantId));
           
            $query = "INSERT INTO `vtiger_histoapprabsents`(`id`, `idapprenant`, `idconvention`, `action`) VALUES (?,?,?,?)";
            $qparams = array('', $apprenantId, $recordId, '');
            $adb->pquery($query, $qparams);
            
            $result = true;
        }else {
            $result = false;
        }
        $response->setResult($result);
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
