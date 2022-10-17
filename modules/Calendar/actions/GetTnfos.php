<?php

class Calendar_GetTnfos_Action extends Vtiger_Action_Controller {

    function process(Vtiger_Request $request) {
        $monfichier = fopen('debug_movadom_getinfos.txt', 'a+');
        fputs($monfichier, "\n" . ' test01 ');
        fclose($monfichier);
        $recordId = $request->get('record');
        $response = new Vtiger_Response();

        $idList = $request->get('idlist');
        if (!$idList) {
            $recordId = $request->get('record');
            $idList = array($recordId);
        }

        foreach ($idList as $id) {
            $recordModel = Vtiger_Record_Model::getInstanceById($id);

            $nomclient[$id] = $this->resolveReferenceLabel($recordModel->get('account_id'), 'Accounts');
            $numclient[$id] = getSingleFieldValue("vtiger_account", "account_no", "accountid", $recordModel->get('account_id'));
            $lastname[$id] = decode_html($recordModel->get('lastname'));
            $firstname[$id] = decode_html($recordModel->get('firstname'));
            $telephone[$id] = decode_html($recordModel->get('phone'));
            $email[$id] = decode_html($recordModel->get('email'));
        }

        foreach ($idList as $id) {
            $resultData = array(
                'id' => $id,
                'name' => $firstname[$id] . " " . $lastname[$id],
                'telephone' => $telephone[$id],
                'email' => $email[$id],
                'numclient' => $numclient[$id],
                'nomclient' => $nomclient[$id]
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
