<?php

/* uni_cnfsecrm - v2 - modif 177 - FILE */
require_once('modules/Contacts/Contacts.php');
require 'libraries/simplexlsx-master/src/SimpleXLSX.php';

use Shuchkin\SimpleXLSX;

//E:\www\cnfsecrmpreprod\libraries\simplexlsx-master\src\SimpleXLSX.php
//E:\www\crmCnfseProd\test\file_excel_upload\test prod.xlsx
class Contacts_ImportExcel_Action extends Vtiger_Action_Controller {

    function process(Vtiger_Request $request) {
        global $adb;
        $response = new Vtiger_Response();
        move_uploaded_file($_FILES['file']['tmp_name'], 'test/file_excel_upload/' . $_FILES['file']['name']);
        $a = $_FILES['file']['tmp_name'];
        $b = $_FILES['file']['name'];
        $fileExcel = 'test/file_excel_upload/' . $_FILES['file']['name'];
//        $fileExcel = "test/file_excel_upload/test prod.xlsx";
        if ($xlsx = SimpleXLSX::parse($fileExcel)) {
            // Produce array keys from the array values of 1st array element
            $header_values = $rows = [];
            foreach ($xlsx->rows() as $k => $r) {
                if ($k === 0) {
                    $header_values = $r;
                    continue;
                }
                $rows[] = array_combine($header_values, $r);
            }
            
        }
       
        //insert Apprenant : 

        foreach ($rows as $row) {
            $query = "select vtiger_contactscf.cf_1318, vtiger_contactscf.contactid ,vtiger_contactdetails.accountid, 
                vtiger_contactdetails.firstname,vtiger_contactdetails.lastname, vtiger_contactdetails.email, vtiger_contactdetails.phone,
                vtiger_account.accountname, vtiger_account.account_no
                from vtiger_contactscf 
                inner JOIN vtiger_contactdetails on vtiger_contactdetails.contactid = vtiger_contactscf.contactid
                inner JOIN vtiger_account on vtiger_account.accountid = vtiger_contactdetails.accountid
                INNER JOIN vtiger_crmentity on vtiger_crmentity.crmid = vtiger_contactscf.contactid
                where cf_1318 like ? and vtiger_crmentity.deleted = 0";
            $param = array($row['Matricule FMCR']);
            $result = $adb->pquery($query, $param);
            $contact_id = $adb->query_result($result, 0, 'contactid');
            //test apprenant si existe :
            if (is_null($contact_id)) {
                $directionExist = getSingleFieldValue("vtiger_contactscf", "contactid", "cf_1320", $row['Direction']);
                if ($row['Direction'] == "" || is_null($directionExist) ) {
                    $contactError[] = $row;
                } else {
                    $contactInfo[] = $this->insertApprenant($row);
                }
            } else {
                $account_id = $adb->query_result($result, 0, 'accountid');
                $account_nom = $adb->query_result($result, 0, 'accountname');
                $account_num = $adb->query_result($result, 0, 'account_no');
                $apprenant_nom = $adb->query_result($result, 0, 'firstname');
                $apprenant_prenom = $adb->query_result($result, 0, 'lastname');
                $apprenant_email = $adb->query_result($result, 0, 'email');
                $apprenant_phone = $adb->query_result($result, 0, 'phone');

                $contactInfo[] = [
                    "apprenant_id" => $contact_id,
                    "apprenant_nom" => $apprenant_nom,
                    "apprenant_prenom" => $apprenant_prenom,
                    "apprenant_email" => $apprenant_email,
                    "apprenant_phone" => $apprenant_phone,
                    "account_id" => $account_id,
                    "account_nom" => html_entity_decode($account_nom),
                    "account_num" => $account_num,
                ];
            }
        }

        $response->setResult(
                [
                    "contactInfo" => $contactInfo,
                    "contactError" => $contactError
                ]
        );
        $response->emit();
    }

    function insertApprenant($row) {
        $focus = new Contacts();
        $focus->mode = 'create';
        $focus->column_fields['firstname'] = $row['Prénom'];            // Prénom
        $focus->column_fields['lastname'] = $row['Nom'];                // Nom 
        $focus->column_fields['cf_1320'] = $row['Direction'];                // direction
        $focus->column_fields['cf_1318'] = $row['Matricule FMCR'];      // Matricule FMCR
        $focus->column_fields['cf_986'] = 1;                            // type apprenant
        $focus->column_fields['account_id'] = 189380;
        $focus->save("Contacts");
        $contact_new_id = $focus->id;
        $contactInfo = [
            "apprenant_id" => $contact_new_id,
            "apprenant_nom" => $row['Nom'],
            "apprenant_prenom" => $row['Prénom'],
            "account_id" => 189380,
            "account_nom" => html_entity_decode("Mairie de Paris - Hôtel de Ville"),
            "account_num" => "CLI16113",
        ];
        return $contactInfo;
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
