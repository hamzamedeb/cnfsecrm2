<?php

/** 	Function used to delete the Inventory product details for the passed entity
 * 	@param int $objectid - entity id to which we want to delete the product details from REQUEST values where as the entity will be Purchase Order, Sales Order, Quotes or Invoice
 * 	@param string $return_old_values - string which contains the string return_old_values or may be empty, if the string is return_old_values then before delete old values will be retrieved
 * 	@return array $ext_prod_arr - if the second input parameter is 'return_old_values' then the array which contains the productid and quantity which will be retrieved before delete the product details will be returned otherwise return empty
 */
function deleteSessionsFinanceurDetails($focus) {
    global $log, $adb;
    $log->debug("Entering into function deleteInventoryDatesDetails(" . $focus->id . ").");

    $adb->pquery("delete from vtiger_sessionsfinanceurrel where id=?", array($focus->id));

    $log->debug("Exit from function deleteInventoryDatesDetails(" . $focus->id . ")");
}

function updateSessionsFinanceurRel($entity) {
    
}

/** 	Function used to save the Inventory product details for the passed entity
 * 	@param object reference $focus - object reference to which we want to save the product details from REQUEST values where as the entity will be Purchase Order, Sales Order, Quotes or Invoice
 * 	@param string $module - module name
 * 	@param $update_prod_stock - true or false (default), if true we have to update the stock for PO only
 * 	@return void
 */
function saveSessionsFinanceurDetails(&$focus) {
    global $log, $adb;
    $id = $focus->id;
    //$log->debug("Entering into function saveInventoryDatesDetails().");
    //Added to get the convertid
    if (isset($_REQUEST['convert_from']) && $_REQUEST['convert_from'] != '') {
        $id = vtlib_purify($_REQUEST['return_id']);
    } else if (isset($_REQUEST['duplicate_from']) && $_REQUEST['duplicate_from'] != '') {
        $id = vtlib_purify($_REQUEST['duplicate_from']);
    }

    if ($focus->mode == 'edit') {

        deleteSessionsFinanceurDetails($focus);
    }
    $tot_financeur = $_REQUEST['totalFinanceurCount'];
//        $monfichier = fopen('debug_session.txt', 'a+');
//        fputs($monfichier, "\n" . " total finaceur ".$tot_financeur);
//        fclose($monfichier);
    $sequence_no = 1;
    for ($i = 1; $i <= $tot_financeur; $i++) {
        $id = $focus->id;
        $vendorid = $_REQUEST['vendorid' . $i];
        $montant = $_REQUEST['montant' . $i];
        $tva = $_REQUEST['tva' . $i];
        $ttc = $_REQUEST['ttc' . $i];

        $query = "insert into vtiger_sessionsfinanceurrel(id,sequence_no,vendorid, montant, tva, ttc) values(?,?,?,?,?,?)";
        $qparams = array($id,$sequence_no, $vendorid, $montant, $tva, $ttc);
        $adb->pquery($query, $qparams);
        $sequence_no++;
    }

    // $log->debug("Exit from function saveInventoryDatesDetails()."); 
}

?>