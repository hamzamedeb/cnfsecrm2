<?php

/* uni_cnfsecrm - v2 - modif 94 - FILE */

class SalesOrder_neSaiPas_Action extends Vtiger_Action_Controller {

    function process(Vtiger_Request $request) {
        global $adb;
        $recordId = $request->get('record');
        $response = new Vtiger_Response();

        $apprenantId = $request->get('apprenantId');
        $sessionId = $request->get('sessionId');
        if (!is_null($sessionId) && !is_null($apprenantId)) {
            /* ajout hitorique */
            $querySelectHis = "SELECT * FROM `vtiger_histoapprabsents` WHERE idapprenant = ? and idconvention = ?";
            $qparamsSelectHis = array($apprenantId, $recordId);
            $resultSelectHis = $adb->pquery($querySelectHis, $qparamsSelectHis);
            $action = $adb->query_result($resultSelectHis, 0, 'action');
            $id = $adb->query_result($resultSelectHis, 0, 'id');

            if ($action == 0) {
                $queryUpdateHis = "UPDATE vtiger_histoapprabsents SET action = ? WHERE id = ?";
                $qparamsUpdateHis = array(3, $id);
                $adb->pquery($queryUpdateHis, $qparamsUpdateHis);
            } else {
                $queryInsertHis = "INSERT INTO `vtiger_histoapprabsents`(`id`, `idapprenant`, `idconvention`, `action`) VALUES (?,?,?,?)";
                $qparamsInsertHis = array('', $apprenantId, $recordId, 3);
                $adb->pquery($queryInsertHis, $qparamsInsertHis);
            }

            $queryInsertAbsent = "INSERT INTO vtiger_app_sanssession (id, id_apprenant) VALUES (?,?)";
            $qparamsInsertAbsent = array('', $apprenantId);
            $adb->pquery($queryInsertAbsent, $qparamsInsertAbsent);
            
            $message = "L'apprenant est ajouté à la liste des stagiaires sans session";
            $result = true;
        } else {
            $message = "Impossible d'ajouter l'apprenant à la liste des stagiaires sans session";
            $result = false;
        }
        $response->setResult(array('message' => $message, 'result' => $result));
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
