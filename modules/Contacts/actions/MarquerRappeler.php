<?php

/* uni_cnfsecrm - v2 - modif 111 - FILE */
require_once('modules/HistoryRecyclage/HistoryRecyclage.php');

class Contacts_MarquerRappeler_Action extends Vtiger_Action_Controller {

    function process(Vtiger_Request $request) {
        global $adb;
        $apprenantId = $request->get('record');
        $sessionId = $request->get('sessionId');
        $type = $request->get('type');
        $response = new Vtiger_Response();

        if (!is_null($apprenantId)) {
            if ($type == 'marquerRappeler') {
                $focus = new HistoryRecyclage();
                $focus->mode = 'create';
                $focus->column_fields['name'] = "Historique_" . $apprenantId;
                $focus->column_fields['cf_1284'] = date("Y-m-d");
                $focus->column_fields['session'] = $sessionId;
                $focus->column_fields['contacts'] = $apprenantId;
                $focus->save("HistoryRecyclage");
                $idHistoryRecyclage = $focus->id;

                $queryHistorique = "UPDATE vtiger_apprenant_recyclage SET historyrecyclageid = ?, rappeler = ? WHERE apprenantid = ? and sessionid = ? ";
                $qparamsHistorique = array($idHistoryRecyclage, 1, $apprenantId, $sessionId);
                $adb->pquery($queryHistorique, $qparamsHistorique);

                $dateNow = new DateTime();
                $dateNow = date('d-m-Y', strtotime($dateNow->format("d-m-Y")));

                $queryInsertHistorique = 'INSERT INTO vtiger_rappel_recyclage(id, historyrecyclageid, sessionid, apprenantid, reponse_par, date_rappel) VALUES (?, ?, ?, ?, ?, DATE(STR_TO_DATE(?, "%d-%m-%Y")))';
                $qparamsInsertHistorique = array('', $idHistoryRecyclage, $sessionId, $apprenantId, 1, $dateNow);
                $adb->pquery($queryInsertHistorique, $qparamsInsertHistorique);

                /* uni_cnfsecrm - v2 - modif 129 */
                $queryUpdateName = 'UPDATE vtiger_historyrecyclage SET 	name = ? WHERE historyrecyclageid = ?';
                $qparamsUpdateName = array('HISTORIQUE_RECYCLAGES_'.$idHistoryRecyclage.'_'.$apprenantId, $idHistoryRecyclage);
                $adb->pquery($queryUpdateName, $qparamsUpdateName);
                /* uni_cnfsecrm - v2 - modif 129 */
                $message = "L'Apprenant est marqué comme 'Rappelé' ";
            } else if ($type = 'nePlusRappeler') {
                $query = "UPDATE vtiger_apprenant_recyclage SET neplusrappeler = ? WHERE apprenantid = ? and sessionid = ?";
                $qparams = array(1, $apprenantId, $sessionId);
                $adb->pquery($query, $qparams);
                $message = "L'Apprenant est marqué comme 'A ne pas rappeler' ";
            }
            $result = true;
        } else {
            $message = "Problème de mise à jour";
            $result = false;
        }
        $response->setResult(array('message' => $message, 'result' => $result, 'idHistoryRecyclage' => $idHistoryRecyclage));
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
