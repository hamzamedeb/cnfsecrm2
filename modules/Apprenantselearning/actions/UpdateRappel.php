<?php

/* uni_cnfsecrm - modif 82 - FILE */

class Apprenantselearning_UpdateRappel_Action extends Vtiger_Action_Controller {

    function process(Vtiger_Request $request) {
        global $adb;
        $recordId = $request->get('record');
        $typeRappel = $request->get('typeRappel');

        $response = new Vtiger_Response();
        $champ = '';
        if ($typeRappel == '7') {
            $champ = 'cf_1224';
        } else if ($typeRappel == '14') {
            $champ = 'cf_1226';
        } else if ($typeRappel == '21') {
            $champ = 'cf_1236';
        }
        $dateNow = new DateTime();
        $dateNow = date('d-m-Y', strtotime($dateNow->format("d-m-Y")));

        if ($champ != '' && $recordId != null) {
            $query = "update vtiger_apprenantselearningcf SET  $champ  = ?,cf_1222=? where apprenantselearningid = ?";
            $adb->pquery($query, array(1, 'Pas de rappel', $recordId));
            $query = "update vtiger_apprenantselearningcf SET cf_1240 = DATE(STR_TO_DATE(?, '%d-%m-%Y')) where apprenantselearningid = ?";
            $adb->pquery($query, array($dateNow, $recordId));
            $info = true;
        } else {
            $info = false;
        }

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
