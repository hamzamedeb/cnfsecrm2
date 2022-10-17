<?php

/* uni_cnfsecrm - v2 - modif 120 - FILE */
require_once('modules/SuiviProspects/SuiviProspects.php');

class Contacts_MarquerRappelerProspects_Action extends Vtiger_Action_Controller {

    function process(Vtiger_Request $request) {
        global $adb;
        $accountId = $request->get('record');
        $devisId = $request->get('devisId');
        $type = $request->get('type');
        $response = new Vtiger_Response();

        if (!is_null($accountId)) {
            if ($type == 'marquerRappeler') {
                $focus = new SuiviProspects();
                $focus->mode = 'create';
                $focus->column_fields['name'] = "Prospects_" . $accountId;
                $focus->column_fields['cf_1291'] = date("Y-m-d");
                $focus->column_fields['devis'] = $devisId;
                $focus->column_fields['prospect'] = $accountId;
                $focus->save("SuiviProspects");
                $suiviprospectsid = $focus->id;

                $queryHistorique = "UPDATE vtiger_suivi_prospects SET suiviprospectsid = ?, rappeler = ? WHERE idclient = ? and iddevis = ? ";
                $qparamsHistorique = array($suiviprospectsid, 1, $accountId, $devisId);
                $adb->pquery($queryHistorique, $qparamsHistorique);

                $dateNow = new DateTime();
                $dateNow = date('d-m-Y', strtotime($dateNow->format("d-m-Y")));

                $queryInsertHistorique = 'INSERT INTO vtiger_rappel_prospects(id, suiviprospectsid, iddevis, idclient, date_rappel) VALUES (?, ?, ?, ?, DATE(STR_TO_DATE(?, "%d-%m-%Y")))';
                $qparamsInsertHistorique = array('', $suiviprospectsid, $devisId, $accountId, $dateNow);
                $adb->pquery($queryInsertHistorique, $qparamsInsertHistorique);

                /* uni_cnfsecrm - v2 - modif 129 */
                $queryUpdateName = 'UPDATE vtiger_suiviprospects SET name = ? WHERE suiviprospectsid = ?';
                $qparamsUpdateName = array('SUIVIPROSPECTS_'.$suiviprospectsid.'_'.$accountId, $suiviprospectsid);
                $adb->pquery($queryUpdateName, $qparamsUpdateName);
                /* uni_cnfsecrm - v2 - modif 129 */
                
                $message = "Le prospect est marqué comme 'Rappelé' ";
            } else if ($type = 'nePlusRappeler') {
                $query = "UPDATE vtiger_suivi_prospects SET neplusrappeler = ? WHERE idclient = ? and iddevis = ?";
                $qparams = array(1, $accountId, $devisId);
                $adb->pquery($query, $qparams);
                $message = "Le prospect est marqué comme 'A ne pas rappeler' ";
            }
            $result = true;
        } else {
            $message = "Problème de mise à jour";
            $result = false;
        }
        $response->setResult(array('message' => $message, 'result' => $result, 'suiviprospectsid' => $suiviprospectsid));
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
