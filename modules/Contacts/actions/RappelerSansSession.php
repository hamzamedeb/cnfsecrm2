<?php

/* uni_cnfsecrm - v2 - modif 107 - FILE */
require_once('modules/HistorySansSessions/HistorySansSessions.php');

class Contacts_RappelerSansSession_Action extends Vtiger_Action_Controller {

    function process(Vtiger_Request $request) {
        global $adb;

        $response = new Vtiger_Response();
        $apprenantId = $request->get('record');
        $type = $request->get('type');

        if (!is_null($apprenantId)) {
            if ($type == 'marquerRappeler') {
                $focus = new HistorySansSessions();
                $focus->mode = 'create';
                $focus->column_fields['name'] = "Historique_Sans_Session_" . $apprenantId;
                $focus->column_fields['cf_1279'] = date("Y-m-d");
                $focus->column_fields['apprenant'] = $apprenantId; 
                $focus->save("HistorySansSessions");
                $idHistorySansSession = $focus->id;

                $queryHistorique = "UPDATE vtiger_app_sanssession SET historysanssessionsid = ?, rappeler = ? WHERE id_apprenant = ?";
                $qparamsHistorique = array($idHistorySansSession, 1, $apprenantId);
                $adb->pquery($queryHistorique, $qparamsHistorique);

                $dateNow = new DateTime();
                $dateNow = date('d-m-Y', strtotime($dateNow->format("d-m-Y")));

                $queryInsertHistorique = 'INSERT INTO vtiger_historique_sans_session(id, apprenantid, reponse, date, etre_rappler, historysanssessionsid) VALUES (?, ?, ?, DATE(STR_TO_DATE(?, "%d-%m-%Y")),? ,?)';
                $qparamsInsertHistorique = array('', $apprenantId, 0, $dateNow, '', $idHistorySansSession);
                $adb->pquery($queryInsertHistorique, $qparamsInsertHistorique);
                
                /* uni_cnfsecrm - v2 - modif 129 */
                $queryUpdateName = 'UPDATE vtiger_historysanssessions SET name = ? WHERE historysanssessionsid = ?';
                $qparamsUpdateName = array('HISTORIQUE_SANS_SESSIONS_'.$idHistorySansSession.'_'.$apprenantId, $idHistorySansSession);
                $adb->pquery($queryUpdateName, $qparamsUpdateName);
                /* uni_cnfsecrm - v2 - modif 129 */
                
                $result = true;
                $message = "L'appenant est marqué Rappelé";
            } else if ($type = 'nePlusRappeler') {
                $query = "UPDATE vtiger_app_sanssession SET neplusrappeler = ? WHERE id_apprenant = ? ";
                $qparams = array(1, $apprenantId);
                $adb->pquery($query, $qparams);
                $message = "L'apprenant est marqué A ne pas rappeler";
            }
        } else {
            $message = "probleme de marquer l'apprenant comme rappeler";
            $result = false;
        }
        //$result = array($apprenantId, $sessionId);
        $response->setResult(array('resultat' => $result, 'message' => $message, 'idHistorySansSession'=>$idHistorySansSession));
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
