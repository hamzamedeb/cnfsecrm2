<?php

/** 	Function used to delete the Inventory product details for the passed entity
 * 	@param int $objectid - entity id to which we want to delete the product details from REQUEST values where as the entity will be Purchase Order, Sales Order, Quotes or Invoice
 * 	@param string $return_old_values - string which contains the string return_old_values or may be empty, if the string is return_old_values then before delete old values will be retrieved
 * 	@return array $ext_prod_arr - if the second input parameter is 'return_old_values' then the array which contains the productid and quantity which will be retrieved before delete the product details will be returned otherwise return empty
 */
function deleteInventoryFinanceurDetails($focus) {
    global $log, $adb;
    $log->debug("Entering into function deleteInventoryFinanceurDetails(" . $focus->id . ").");

    $adb->pquery("delete from vtiger_inventoryfinanceurrel where id=?", array($focus->id));

    $log->debug("Exit from function deleteInventoryFinanceurDetails(" . $focus->id . ")");
}

function updateInventoryFinanceurRel($entity) {
    
}

//function VirguleToPoint($val_virgule) {
//    return str_replace(",", ".", $val_virgule);
//}

/** 	Function used to save the Inventory product details for the passed entity
 * 	@param object reference $focus - object reference to which we want to save the product details from REQUEST values where as the entity will be Purchase Order, Sales Order, Quotes or Invoice
 * 	@param string $module - module name
 * 	@param $update_prod_stock - true or false (default), if true we have to update the stock for PO only
 * 	@return void
 */
function saveInventoryFinanceurDetails(&$focus) {
    global $log, $adb;
    $id = $focus->id;
    $log->debug("Entering into function saveInventoryFinanceurDetails().");
    //Added to get the convertid
    $monfichier = fopen('debug_financeur_detail.txt', 'a+');
    fputs($monfichier, "\n" . ' id ' . $id);
    fclose($monfichier);
    if (isset($_REQUEST['convert_from']) && $_REQUEST['convert_from'] != '') {
        $id = vtlib_purify($_REQUEST['return_id']);
        $monfichier = fopen('debug_financeur_detail.txt', 'a+');
        fputs($monfichier, "\n" . ' test01 ');
        fclose($monfichier);
    } else if (isset($_REQUEST['duplicate_from']) && $_REQUEST['duplicate_from'] != '') {
        $id = vtlib_purify($_REQUEST['duplicate_from']);
        $monfichier = fopen('debug_financeur_detail.txt', 'a+');
        fputs($monfichier, "\n" . ' test02 ');
        fclose($monfichier);
    }
    if ($focus->mode == 'edit') {

        deleteInventoryFinanceurDetails($focus);
    }
    $tot_financeur = $_REQUEST['totalFinanceurCount'];

//        $monfichier = fopen('debug_financeur_detail.txt', 'a+');
//        fputs($monfichier, "\n" . ' total financeur :  '.$tot_financeur);
//        fclose($monfichier);
    for ($i = 1; $i <= $tot_financeur; $i++) {

        $vendorid = $_REQUEST['vendorid' . $i];
        $pourcentage = $_REQUEST['pourcentage' . $i];
        $montant = $_REQUEST['montant' . $i];
        $tva = $_REQUEST['tva' . $i];
        $ttc = $_REQUEST['ttc' . $i];

        $query = "insert into vtiger_inventoryfinanceurrel(id,vendorid,sequence_no,pourcentage, montant, tva, ttc) values(?,?,?,?,?,?,?)";
        $qparams = array($id, $vendorid, $i, $pourcentage, $montant, $tva, $ttc);
        $adb->pquery($query, $qparams);
    }

    $log->debug("Exit from function saveInventoryFinanceurDetails().");
}

?> 