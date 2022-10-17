<?php

/* uni_cnfsecrm - modif 82 - FILE */

class Apprenantselearning_UpdateRappelTel_Action extends Vtiger_Action_Controller {

    function process(Vtiger_Request $request) {
        global $adb;
        $recordId = $request->get('record');
        $etat = $request->get('etat');
        $typeRappel = $request->get('type');

        $response = new Vtiger_Response();
        $champ = '';
        if ($typeRappel == '7') {
            $champ = 'cf_1230';
        } else if ($typeRappel == '14') {
            $champ = 'cf_1232';
        } else if ($typeRappel == '21') {
            $champ = 'cf_1238';
        }
        if ($champ != '' && $recordId != null) {
            $query = 'update vtiger_apprenantselearningcf SET ' . $champ . ' = ? where apprenantselearningid = ?';
            $adb->pquery($query, array($etat, $recordId));
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
