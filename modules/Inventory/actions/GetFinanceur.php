<?php

class Inventory_GetFinanceur_Action extends Vtiger_Action_Controller {

    function process(Vtiger_Request $request) {

        $recordId = $request->get('record');


        $idList = $request->get('idlist');

        $response = new Vtiger_Response();
        $recordModel = Vtiger_Record_Model::getInstanceById($recordId);

        $vendorname = $this->resolveReferenceLabel($recordModel->get('vendorid'), 'Vendors');
        $vendorname = decode_html($recordModel->get('vendorname'));
        $response->setResult(array(
            $recordId => array(
                'id' => $recordId,
                'vendorid' => $recordId,
                'vendorname' => $vendorname,
                'phone' => decode_html($recordModel->get('phone')),
                'email' => decode_html($recordModel->get('email')),
                'street' => decode_html($recordModel->get('street')),
                'city' => decode_html($recordModel->get('city')),
                'country' => decode_html($recordModel->get('country')),
                'postalcode' => decode_html($recordModel->get('postalcode')),                
        )));
        $monfichier = fopen('debug_test1.txt', 'a+');
        fputs($monfichier, "\n" . ' postalcode ' . $recordModel->get('postalcode'));
        fclose($monfichier);
        $response->emit();
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
