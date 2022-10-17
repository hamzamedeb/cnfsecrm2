<?php

if (!function_exists('pdfmakerGetListApprenants')) {

    function pdfmakerGetListApprenants($entityid) {
        global $adb;
        $query = "SELECT id,salutation,firstname,lastname,vtiger_contactsubdetails.birthday,title 
                FROM vtiger_inventoryapprenantsrel 
                INNER JOIN vtiger_contactdetails on vtiger_contactdetails.contactid = vtiger_inventoryapprenantsrel.apprenantid 
                INNER JOIN vtiger_contactsubdetails on vtiger_contactsubdetails.contactsubscriptionid = vtiger_contactdetails.contactid 
                WHERE vtiger_inventoryapprenantsrel.id = ?";
        $result = $adb->pquery($query, array($entityid));
        $num_rows_apprenants = $adb->num_rows($result);
        $app = '';
        if ($num_rows_apprenants) {
            for ($i = 0; $i < $num_rows_apprenants; $i++) {
                $salutation = $adb->query_result($result, $i, 'salutation');
                $firstname = ucwords(strtolower(($adb->query_result($result, $i, 'firstname'))));
                $lastname = strtoupper($adb->query_result($result, $i, 'lastname'));
                $birthday_contact = $adb->query_result($result, $i, 'birthday');
                $title_contact = $adb->query_result($result, $i, 'title');
                $app .= formatString($firstname) . " " . formatString($lastname);
                if ($i < $num_rows_apprenants - 1)
                    $app .= "/";
            }
        }

        return $app;
    }

}
