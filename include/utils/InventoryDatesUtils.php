<?php

/** 	Function used to delete the Inventory product details for the passed entity
 * 	@param int $objectid - entity id to which we want to delete the product details from REQUEST values where as the entity will be Purchase Order, Sales Order, Quotes or Invoice
 * 	@param string $return_old_values - string which contains the string return_old_values or may be empty, if the string is return_old_values then before delete old values will be retrieved
 * 	@return array $ext_prod_arr - if the second input parameter is 'return_old_values' then the array which contains the productid and quantity which will be retrieved before delete the product details will be returned otherwise return empty
 */
function deleteInventoryDatesDetails($focus) {
    global $log, $adb;
    $log->debug("Entering into function deleteInventoryDatesDetails(" . $focus->id . ").");

    $adb->pquery("delete from vtiger_inventorydatesrel where id=?", array($focus->id));

    $log->debug("Exit from function deleteInventoryDatesDetails(" . $focus->id . ")");
}

function updateInventoryDatesRel($entity) {
    
}

/** 	Function used to save the Inventory product details for the passed entity
 * 	@param object reference $focus - object reference to which we want to save the product details from REQUEST values where as the entity will be Purchase Order, Sales Order, Quotes or Invoice
 * 	@param string $module - module name
 * 	@param $update_prod_stock - true or false (default), if true we have to update the stock for PO only
 * 	@return void
 */
function saveInventoryDatesDetails(&$focus) {
    global $log, $adb;
    $id = $focus->id;
    $log->debug("Entering into function saveInventoryDatesDetails().");
    //Added to get the convertid
    if (isset($_REQUEST['convert_from']) && $_REQUEST['convert_from'] != '') {
        $id = vtlib_purify($_REQUEST['return_id']);
    } else if (isset($_REQUEST['duplicate_from']) && $_REQUEST['duplicate_from'] != '') {
        $id = vtlib_purify($_REQUEST['duplicate_from']);
    }

    if ($focus->mode == 'edit') {
        $monfichier = fopen('debug_save_needs.txt', 'a+');
        fputs($monfichier, "\n" . ' Test4 ');
        fclose($monfichier);
        deleteInventoryDatesDetails($focus);
    }
    $tot_dates = $_REQUEST['totalDatesCount'];
    // var_dump($_REQUEST);
    for ($i = 1; $i <= $tot_dates; $i++) {
        var_dump($_REQUEST['date_start' . $i]);
      //  echo "date_start  $i";
        $date_start = $_REQUEST['date_start' . $i];
        $start_matin = $_REQUEST['start_matin' . $i];
        $end_matin = $_REQUEST['end_matin' . $i];
        $start_apresmidi = $_REQUEST['start_apresmidi' . $i];
        $end_apresmidi = $_REQUEST['end_apresmidi' . $i];
        $duree_formation = $_REQUEST['duree_formation' . $i];

        $query = "insert into vtiger_inventorydatesrel(id,sequence_no,date_start, start_matin, end_matin,start_apresmidi,end_apresmidi,duree_formation) values(?,?,?,?,?,?,?,?)";
        $qparams = array($id, $i, $date_start, $start_matin, $end_matin, $start_apresmidi, $end_apresmidi,$duree_formation);
        $adb->pquery($query, $qparams);
    }
//die();
    $log->debug("Exit from function saveInventoryDatesDetails().");
}
?>