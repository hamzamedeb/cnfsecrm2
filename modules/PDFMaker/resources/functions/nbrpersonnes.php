<?php

if (!function_exists('pdfmakerGetNbrPersonnes')) {

    function pdfmakerGetNbrPersonnes($entityid) {
        global $adb;
        $query = "SELECT id,quantity 
                FROM vtiger_inventoryproductrel                 
                WHERE vtiger_inventoryproductrel.id = ?";
        $result = $adb->pquery($query, array($entityid));
        $num_rows_apprenants = $adb->num_rows($result);
        $app = '';
        if ($num_rows_apprenants) {
            for ($i = 0; $i < $num_rows_apprenants; $i++) {
                $quantity = $adb->query_result($result, $i, 'quantity');                
            }
        }

        return intval($quantity);
    }

}
