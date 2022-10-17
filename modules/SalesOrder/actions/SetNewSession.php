<?php

/* uni_cnfsecrm - v2 - modif 94 - FILE */

class SalesOrder_SetNewSession_Action extends Vtiger_Action_Controller {

    function process(Vtiger_Request $request) {
        global $adb;
        $recordId = $request->get('record');
        $response = new Vtiger_Response();

        $apprenantId = $request->get('apprenantId');
        $sessionId = $request->get('sessionId');
        if (!is_null($sessionId) && !is_null($apprenantId)) {
            $querySelect = "SELECT MAX(sequence_no) as sequence FROM vtiger_sessionsapprenantsrel WHERE id = ?";
            $paramSelect = array($sessionId);
            $resultSelect = $adb->pquery($querySelect, $paramSelect);
            $sequence = $adb->query_result($resultSelect, 0, 'sequence');
            if (is_null($sequence)) {
                $i = 1;
            } else {
                $i = $sequence + 1;
            }
            $query = "insert into vtiger_sessionsapprenantsrel(id,apprenantid,sequence_no,etat,resultat,ticket_examen,inscrit,be_essai,be_mesurage,be_verification,be_manoeuvre,he_essai,he_mesurage,he_verification,he_manoeuvre,initiale,recyclage,testprerequis,electricien,ticket_examen_test,type_tokens,type_tokens_test) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
            $qparams = array($sessionId, $apprenantId, $i, $etat, $resultat, $ticket_examen, $inscrit, $be_essai, $be_mesurage, $be_verification, $be_manoeuvre, $he_essai, $he_mesurage, $he_verification, $he_manoeuvre, $initiale, $recyclage, $testprerequis, $electricien, $ticket_examen_test, $type_tokens, $type_tokens_test);
            $adb->pquery($query, $qparams);

            // ajout hitorique 
            $querySelectHis = "SELECT * FROM `vtiger_histoapprabsents` WHERE idapprenant = ? and idconvention = ?";
            $qparamsSelectHis = array($apprenantId, $recordId);
            $resultSelectHis = $adb->pquery($querySelectHis, $qparamsSelectHis);
            $action = $adb->query_result($resultSelectHis, 0, 'action');
            $id = $adb->query_result($resultSelectHis, 0, 'id');

            if ($action == 0) {
                $queryUpdateHis = "UPDATE vtiger_histoapprabsents SET action = ?, idsession = ? WHERE id = ?";
                $qparamsUpdateHis = array(1, $sessionId, $id);
                $adb->pquery($queryUpdateHis, $qparamsUpdateHis);
            } else {
                $queryInsertHis = "INSERT INTO vtiger_histoapprabsents(id, idapprenant, idconvention, action, idsession) VALUES (?,?,?,?,?)";
                $qparamsInsertHis = array('', $apprenantId, $recordId, 1, $sessionId);
                $adb->pquery($queryInsertHis, $qparamsInsertHis);
            }

            /**/

            $result = true;
        } else {
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
