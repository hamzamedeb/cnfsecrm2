<?php
//unicnfsecrm_mod_42
class Quotes_GetAucentre_Action extends Vtiger_Action_Controller {

    function process(Vtiger_Request $request) {
        $recordId = $request->get('record');
        $response = new Vtiger_Response();

        $idList = $request->get('idlist');
        if (!$idList) {
            $recordId = $request->get('record');
            $idList = array($recordId);
        }

        foreach ($idList as $id) {
            $recordModel = Vtiger_Record_Model::getInstanceById($id);

            $idlieu[$id] = $this->resolveReferenceLabel($recordModel->get('lieuid'), 'Lieu');
            $adresse[$id] = decode_html($recordModel->get('cf_941'));
            $codePostale[$id] = decode_html($recordModel->get('cf_943'));
            $ville[$id] = decode_html($recordModel->get('cf_945'));
        }

        foreach ($idList as $id) {
            $resultData = array(
                'id' => $id,
                'adresse' => $adresse[$id],
                'codePostale' => $codePostale[$id],
                'ville' => $ville[$id]
            );

            $info[] = array($id => $resultData);
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
