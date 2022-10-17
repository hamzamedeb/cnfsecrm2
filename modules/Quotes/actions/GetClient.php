<?php
// unicnfsecrm_mod_42
class Quotes_GetClient_Action extends Vtiger_Action_Controller {

    function process(Vtiger_Request $request) {
        global $adb;
        $recordId = $request->get('record');
        $response = new Vtiger_Response();

        $idList = $request->get('idlist');
        if (!$idList) {
            $recordId = $request->get('record');
            $idList = array($recordId);
        }

        $query = "SELECT contact_no,salutation,firstname,lastname,email,phone
                FROM vtiger_contactdetails 
                WHERE vtiger_contactdetails.accountid = ? ";
        $result = $adb->pquery($query, array($recordId));
        $num_contact = $adb->num_rows($result);
        if ($num_contact) {
            for ($i = 0; $i < $num_contact; $i++) {
                $contact_no = $adb->query_result($result, $i, 'contact_no');
                $salutation = $adb->query_result($result, $i, 'salutation');
                $firstname = $adb->query_result($result, $i, 'firstname');
                $lastname = $adb->query_result($result, $i, 'lastname');
                $email = $adb->query_result($result, $i, 'email');
                $phone = $adb->query_result($result, $i, 'phone');

                $listContact[$i]['contact_no'] = $contact_no;
                $listContact[$i]['salutation'] = $salutation;
                $listContact[$i]['firstname'] = $firstname;
                $listContact[$i]['lastname'] = $lastname;
                $listContact[$i]['email'] = $email;
                $listContact[$i]['phone'] = $phone;
            }
        }


        foreach ($idList as $id) {
            $recordModel = Vtiger_Record_Model::getInstanceById($id);

            $nomclient = decode_html($recordModel->get('accountname'));
            $numclient = decode_html($recordModel->get('account_no'));
            $telephone = decode_html($recordModel->get('phone'));
            $email = decode_html($recordModel->get('email1'));
            $adresse = decode_html($recordModel->get('bill_street'));
            $codePostale = decode_html($recordModel->get('bill_code'));
            $ville = decode_html($recordModel->get('bill_city'));
        }
        

        foreach ($idList as $id) {
            $resultData = array(
                'id' => $id,
                'telephone' => $telephone,
                'email' => $email,
                'numclient' => $numclient,
                'nomclient' => $nomclient,
                'adresse' => $adresse,
                'codePostale' => $codePostale,
                'ville' => $ville
            );
//            $r = print_r($resultData, true);
//        $monfichier = fopen('debug_test1.txt', 'a+');
//        fputs($monfichier, "\n" . "value " . $r);
//        fclose($monfichier);

            $info[] = array($id => $resultData, 'listApprenant' => $listContact);
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
