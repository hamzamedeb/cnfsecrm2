<?php
/* uni_cnfsecrm - v2 - modif 161 - DEBUT */
require_once('modules/Apprenantselearning/Apprenantselearning.php');
/* uni_cnfsecrm - v2 - modif 161 - FIN */
/** 	Function used to delete the Inventory product details for the passed entity
 * 	@param int $objectid - entity id to which we want to delete the product details from REQUEST values where as the entity will be Purchase Order, Sales Order, Quotes or Invoice
 * 	@param string $return_old_values - string which contains the string return_old_values or may be empty, if the string is return_old_values then before delete old values will be retrieved
 * 	@return array $ext_prod_arr - if the second input parameter is 'return_old_values' then the array which contains the productid and quantity which will be retrieved before delete the product details will be returned otherwise return empty
 */
function deleteInventoryApprenantDetails($focus) {
    global $log, $adb;
    $log->debug("Entering into function deleteInventoryApprenantDetails(" . $focus->id . ").");
    $adb->pquery("delete from vtiger_inventoryapprenantsrel where id=?", array($focus->id));
    $log->debug("Exit from function deleteInventoryApprenantDetails(" . $focus->id . ")");
}

function updateInventoryApprenantRel($entity) {
    
}

/** 	Function used to save the Inventory product details for the passed entity
 * 	@param object reference $focus - object reference to which we want to save the product details from REQUEST values where as the entity will be Purchase Order, Sales Order, Quotes or Invoice
 * 	@param string $module - module name
 * 	@param $update_prod_stock - true or false (default), if true we have to update the stock for PO only
 * 	@return void
 */
function saveInventoryApprenantDetails(&$focus) {
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
        deleteInventoryApprenantDetails($focus);
    }
    
    $totalapprenantsCount = $_REQUEST['totalApprenantsCount'];

//        $monfichier = fopen('debug_session.txt', 'a+');
//        fputs($monfichier, "\n" . ' total :  '.$totalapprenantsCount);
//        fclose($monfichier);

    for ($i = 1; $i <= $totalapprenantsCount; $i++) {
        $apprenantid = $_REQUEST['apprenantid' . $i];
        $etat = $_REQUEST['etat' . $i];
        $resultat = $_REQUEST['resultat' . $i];
        $inscrit = $_REQUEST['inscrit' . $i];

        if ($inscrit == 'inscrit') {
            $inscrit = 1;
        } else {
            $inscrit = 0;
        }

        $query = "insert into vtiger_inventoryapprenantsrel(id,apprenantid,sequence_no,etat,resultat,inscrit) values(?,?,?,?,?,?)";
        $qparams = array($id, $apprenantid, $i, $etat, $resultat, $inscrit);
        $adb->pquery($query, $qparams);
        
        /* uni_cnfsecrm - v2 - modif 161 - DEBUT */
        $paye = 0;
        $financeur = 0;
        $encompte = 0;
        $date_start = $_REQUEST['cf_988'];
        $date_formation = date("d-m-Y", strtotime($date_start));
        $session = $_REQUEST['session'];
        
        //
        $sessionDetail = "SELECT vtiger_salesorder.salesorderid, vtiger_activity.subject,
            vtiger_activitycf.cf_1202 
            FROM vtiger_salesorder 
            INNER JOIN vtiger_activity on vtiger_salesorder.session = vtiger_activity.activityid 
            INNER JOIN vtiger_activitycf on vtiger_activitycf.activityid = vtiger_activity.activityid 
            where vtiger_salesorder.salesorderid = ?"; // and deleted = ?
        $resultSessionDetail = $adb->pquery($sessionDetail, array($id));
        $elearning = $adb->query_result($resultSessionDetail, 0, 'cf_1202');
        if ($elearning == 1){
            $elearning = 'on';
        }
        $subject = $adb->query_result($resultSessionDetail, 0, 'subject');
        
        //
        $requete_appr_infos = "SELECT vtiger_contactdetails.accountid,firstname,lastname,cf_1208 as encompte
                FROM vtiger_contactdetails       
                INNER JOIN vtiger_accountscf on vtiger_accountscf.accountid = vtiger_contactdetails.accountid
    where contactid = ?"; // and deleted = ?
        $result_appr_infos = $adb->pquery($requete_appr_infos, array($apprenantid));
        $num_rows_appr_infos = $adb->num_rows($result_appr_infos);

        if ($num_rows_appr_infos > 0) {
            $accountid = $adb->query_result($result_appr_infos, 0, 'accountid');
            $firstname = $adb->query_result($result_appr_infos, 0, 'firstname');
            $lastname = $adb->query_result($result_appr_infos, 0, 'lastname');
            $encompte = $adb->query_result($result_appr_infos, 0, 'encompte');

            $queryStatutFacture = 'SELECT invoiceid,invoicestatus,salesorderid 
                                    FROM vtiger_invoice 
                                    WHERE session = ? AND accountid = ?';
            $paramsStatutFacture = array($session, $accountid);
            $resultStatutFacture = $adb->pquery($queryStatutFacture, $paramsStatutFacture);
            $nbreFactures = $adb->num_rows($resultStatutFacture);

            if ($nbreFactures > 0) {
                $statutFacture = $adb->query_result($resultStatutFacture, 0, 'invoicestatus');
                if ($statutFacture == 'Paid') {
                    $paye = 1;
                } else {
                    $paye = 0;
                }
                $factureid = $adb->query_result($resultStatutFacture, 0, 'invoiceid');
                $salesorderid = $adb->query_result($resultStatutFacture, 0, 'salesorderid');
            } else {
                $queryConvention = 'SELECT salesorderid FROM vtiger_salesorder WHERE session = ? AND accountid = ?';
                $paramsConvention = array($session, $accountid);
                $resultConvention = $adb->pquery($queryConvention, $paramsConvention);
                $salesorderid = $adb->query_result($resultConvention, 0, 'salesorderid');
            }

            $queryNbreFinanceur2 = 'SELECT id FROM vtiger_inventoryfinanceurrel WHERE id = ? AND vendorid <> ?';
            $paramsNbreFinanceur2 = array($salesorderid, 0);
            $resultNbreFinanceur2 = $adb->pquery($queryNbreFinanceur2, $paramsNbreFinanceur2);
            $nbreFinanceur = $adb->num_rows($resultNbreFinanceur2);

            if ($nbreFinanceur > 0) {
                $financeur = 1;
            } else {
                $financeur = 0;
            }
        }
        //echo $elearning." ".$paye." ".$financeur." ".$encompte;echo "<br/>";die();
        if ($elearning == "on" && ($paye == 1 || $financeur == "1" || $encompte == "1" )) {
            $requete_exist_appr_e = "SELECT apprenantselearningid, statut
                FROM vtiger_apprenantselearning                
    where apprenant = ? and session = ?"; // and deleted = ?
            $result_exist_appr_e = $adb->pquery($requete_exist_appr_e, array($apprenantid, $session));
            $num_rows_exist_appr_e = $adb->num_rows($result_exist_appr_e);
            $apprenantselearningid = $adb->query_result($result_exist_appr_e, 0, 'apprenantselearningid');
            $statut = $adb->query_result($result_exist_appr_e, 0, 'statut');
            echo $apprenantselearningid." ".$statut."<br/>";
            if (html_entity_decode($statut) != "Inscription ignoré") {
                $current_user = new Users();
                $current_user->id = 1;
                $currentUser = vglobal('current_user', $current_user);

                $focus_appr_elearning = new Apprenantselearning();
                $focus_appr_elearning->column_fields['assigned_user_id'] = 57;
                $name = "Formation " . html_entity_decode($firstname . " " . $lastname);
                $focus_appr_elearning->column_fields['name'] = $name;
                $focus_appr_elearning->column_fields['apprenant'] = $apprenantid;
                $focus_appr_elearning->column_fields['session'] = $session;
                $focus_appr_elearning->column_fields['client'] = $accountid;
                $focus_appr_elearning->column_fields['statut'] = 'Stagiaire à inscrire';

                if ($num_rows_exist_appr_e <= 0) {
                    $focus_appr_elearning->mode = 'create';
                } else {
                    $requete_update = "update vtiger_crmentity set deleted = ? where crmid = ?";
                    $result_update = $adb->pquery($requete_update, array(0, $apprenantselearningid));
                    $focus_appr_elearning->mode = 'edit';
                    $focus_appr_elearning->id = $apprenantselearningid;
                }

                $focus_appr_elearning->save("Apprenantselearning");
                $return_id = $focus_appr_elearning->id;
            }
        }
        /* uni_cnfsecrm - v2 - modif 161 - FIN */
    }
    // $log->debug("Exit from function saveInventoryDatesDetails()."); 
}

?>