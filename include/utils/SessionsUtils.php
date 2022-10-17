<?php

/** 	Function used to delete the Inventory product details for the passed entity
 * 	@param int $objectid - entity id to which we want to delete the product details from REQUEST values where as the entity will be Purchase Order, Sales Order, Quotes or Invoice
 * 	@param string $return_old_values - string which contains the string return_old_values or may be empty, if the string is return_old_values then before delete old values will be retrieved
 * 	@return array $ext_prod_arr - if the second input parameter is 'return_old_values' then the array which contains the productid and quantity which will be retrieved before delete the product details will be returned otherwise return empty
 */
function deleteSessionsDatesDetails($focus) {
    global $log, $adb;
    $log->debug("Entering into function deleteSessionsDatesDetails(" . $focus->id . ").");

    $adb->pquery("delete from vtiger_sessionsdatesrel where id=?", array($focus->id));

    $log->debug("Exit from function deleteSessionsDatesDetails(" . $focus->id . ")");
}

function updateSessionsDatesRel($entity) {
    
}

function VirguleToPoint($val_virgule) {
    return str_replace(",", ".", $val_virgule);
}

/** 	Function used to save the Inventory product details for the passed entity
 * 	@param object reference $focus - object reference to which we want to save the product details from REQUEST values where as the entity will be Purchase Order, Sales Order, Quotes or Invoice
 * 	@param string $module - module name
 * 	@param $update_prod_stock - true or false (default), if true we have to update the stock for PO only
 * 	@return void
 */
function saveSessionsDatesDetails(&$focus) {
    global $log, $adb;
    $id = $focus->id;
    $log->debug("Entering into function saveSessionsDatesDetails().");
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
        deleteSessionsDatesDetails($focus);
    }
    $tot_dates = $_REQUEST['totalDatesCount'];
    $array_listobj = array();
    $array_equiv_list = array();
    $date_seq = 1;
    for ($i = 1; $i <= $tot_dates; $i++) {
        $date_start = $_REQUEST['date_start' . $i];
        $start_matin = $_REQUEST['start_matin' . $i];
        $end_matin = $_REQUEST['end_matin' . $i];
        $start_apresmidi = $_REQUEST['start_apresmidi' . $i];
        $end_apresmidi = $_REQUEST['end_apresmidi' . $i];

        $date_start = date("Y-m-d", strtotime($date_start));
        $due_date = date("Y-m-d", strtotime($due_date));
        $query = "insert into vtiger_sessionsdatesrel(id,sequence_no,date_start, start_matin, end_matin,start_apresmidi,end_apresmidi) values(?,?,?,?,?,?)";
        $qparams = array($focus->id, $date_start, $date_seq, $start_matin, $end_matin, $start_apresmidi, $end_apresmidi);
        $adb->pquery($query, $qparams);
        $listobj_incr = $adb->getLastInsertID();
    }
    $date_seq++;
    $log->debug("Exit from function saveSessionsDatesDetails().");
}

?> 