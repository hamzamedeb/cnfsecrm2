<?php

/* uni_cnfsecrm - v2 - modif 177 - FILE */
require_once('data/CRMEntity.php');
require_once('include/database/PearDatabase.php');
require_once 'includes/runtime/LanguageHandler.php';
require_once 'includes/Loader.php';
require_once 'includes/runtime/BaseModel.php';
require_once 'includes/runtime/Globals.php';
require_once 'modules/Users/models/Record.php';
require_once("config.php");
//require_once('modules/Contacts/Contacts.php');
require 'libraries/simplexlsx-master/src/SimpleXLSX.php';

use Shuchkin\SimpleXLSX;

//E:\www\cnfsecrmpreprod\libraries\simplexlsx-master\src\SimpleXLSX.php
class Contacts_ImportExcelDirection_Action extends Vtiger_Action_Controller {

    function process(Vtiger_Request $request) {
        global $adb;
        $response = new Vtiger_Response();

        global $adb;
        $fileExcel = "test/direction/Liste_direction.xlsx";
//        $fileExcel = "test/excel/3.xlsx";
        if ($xlsx = SimpleXLSX::parse($fileExcel)) {
            $header_values = $rows = [];
            foreach ($xlsx->rows() as $k => $r) {
                if ($k === 0) {
                    $header_values = $r;
                    continue;
                }
                $rows[] = array_combine($header_values, $r);
            }
        }
//        print_r($rows);
//        die();
        foreach ($rows as $row) {
            $email = trim($row['ADRESSE MAIL']);
            $query = "select vtiger_contactscf.contactid      
                from vtiger_contactscf
                inner JOIN vtiger_contactdetails on vtiger_contactdetails.contactid = vtiger_contactscf.contactid
                where cf_1316 = ? and  vtiger_contactdetails.email like ? ";
            $param = array(1, $email);
            $result = $adb->pquery($query, $param);
            $contact_id = $adb->query_result($result, 0, 'contactid');
            if (is_null($contact_id)) {
                $this->insertApprenant($row);
            }
        }

        $response->setResult("hello");
        $response->emit();
    }

    function insertApprenant($row) {
        require_once('vtlib/Vtiger/Module.php');
        require_once('modules/Contacts/Contacts.php');
        include_once('vtlib/Vtiger/Event.php');
        $direction = $row['DIRECTION'];
        $salutation = $row['Salutation'];
        $nom = $row['NOM'];
        $prenom = $row['PRENOM'];
        $adresse = $row['ADRESSE'];
        $tel = $row['TELEPHONE'];
        $email = $row['ADRESSE MAIL'];
        
        $focus = new Contacts();
        $focus->mode = 'create';
        $focus->column_fields['salutation'] = $salutation;     //salutation
        $focus->column_fields['firstname'] = $prenom;            // Prénom
        $focus->column_fields['lastname'] = $nom;                // Nom
        $focus->column_fields['cf_1320'] = $direction;           // direction
        $focus->column_fields['cf_1316'] = 1;                           // type apprenant
        $focus->column_fields['account_id'] = 189380;
        $focus->column_fields['assigned_user_id'] = 1;
        $focus->save("Contacts");
        $contact_new_id = $focus->id;
        return $contact_new_id;
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
