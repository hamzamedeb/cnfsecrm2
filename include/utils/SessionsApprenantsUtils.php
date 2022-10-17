<?php

require_once('modules/Apprenantselearning/Apprenantselearning.php');

function deleteSessionsApprenantDetails($focus) {
    global $log, $adb;
    $log->debug("Entering into function deleteInventoryDatesDetails(" . $focus->id . ").");

    $adb->pquery("delete from vtiger_sessionsapprenantsrel where id=?", array($focus->id));

    $log->debug("Exit from function deleteInventoryDatesDetails(" . $focus->id . ")");
}

function updateSessionsApprenantRel($entity) {
    
}

function saveSessionsApprenantDetails(&$focus) {
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

        deleteSessionsApprenantDetails($focus);
    }
//    $i = 1;
    $totalapprenantsCount = $_REQUEST['totalApprenantsCount'];

//    $monfichier = fopen('debug_session.txt', 'a+');
//    fputs($monfichier, "\n" . ' id 1:  ' .$apprenantid);
//    fclose($monfichier);
    $sequence_no = 1;
    //echo $totalapprenantsCount . "</br>";
    //var_dump($_REQUEST);die();
    for ($i = 1; $i <= $totalapprenantsCount; $i++) {
        $id = $focus->id;
            $monfichier = fopen('debug_apprenants.txt', 'a+');
            fputs($monfichier, "\n" . " id " . $id);
    fputs($monfichier, "\n" . " i " . $i);
    fclose($monfichier);
        $session = $id;
        $apprenantid = $_REQUEST['apprenantid' . $i];
         $monfichier = fopen('debug_apprenants.txt', 'a+');
    fputs($monfichier, "\n" . " apprenantid " . $apprenantid);
    fclose($monfichier);
        $etat = $_REQUEST['etat' . $i];
        $resultat = $_REQUEST['resultat' . $i];
        $inscrit = $_REQUEST['inscrit' . $i];

        // uni_cnfsecrm - modif 104 - DEBUT
        /* $be_essai = ($_REQUEST['be_essai' . $i] == 'on') ? '1' : '0';
          $be_mesurage = ($_REQUEST['be_mesurage' . $i] == 'on') ? '1' : '0';
          $be_verification = ($_REQUEST['be_verification' . $i] == 'on') ? '1' : '0';
          $be_manoeuvre = ($_REQUEST['be_manoeuvre' . $i] == 'on') ? '1' : '0';
          $he_essai = ($_REQUEST['he_essai' . $i] == 'on') ? '1' : '0';
          $he_mesurage = ($_REQUEST['he_mesurage' . $i] == 'on') ? '1' : '0';
          $he_verification = ($_REQUEST['he_verification' . $i] == 'on') ? '1' : '0';
          $he_manoeuvre = ($_REQUEST['he_manoeuvre' . $i] == 'on') ? '1' : '0'; */
        // uni_cnfsecrm - modif 104 - FIN 
        $initiale = ($_REQUEST['initiale' . $i] == 'on') ? '1' : '0';
        $recyclage = ($_REQUEST['recyclage' . $i] == 'on') ? '1' : '0';

        $initiale = ($recyclage == 0) ? $initiale = 1 : $initiale = 0;

        // uni_cnfsecrm - modif 104 - DEBUT 
        $b0_h0_h0v_b0 = ($_REQUEST['b0_h0_h0v_b0' . $i] == 'on') ? '1' : '0';
        $b0_h0_h0v_h0v = ($_REQUEST['b0_h0_h0v_h0v' . $i] == 'on') ? '1' : '0';
        $bs_be_he_b0 = ($_REQUEST['bs_be_he_b0' . $i] == 'on') ? '1' : '0';
        $bs_be_he_h0v = ($_REQUEST['bs_be_he_h0v' . $i] == 'on') ? '1' : '0';
        $bs_be_he_bs = ($_REQUEST['bs_be_he_bs' . $i] == 'on') ? '1' : '0';
        $bs_be_he_manoeuvre = ($_REQUEST['bs_be_he_manoeuvre' . $i] == 'on') ? '1' : '0';
        $b1v_b2v_bc_br_b0 = ($_REQUEST['b1v_b2v_bc_br_b0' . $i] == 'on') ? '1' : '0';
        $b1v_b2v_bc_br_h0v = ($_REQUEST['b1v_b2v_bc_br_h0v' . $i] == 'on') ? '1' : '0';
        $b1v_b2v_bc_br_bs = ($_REQUEST['b1v_b2v_bc_br_bs' . $i] == 'on') ? '1' : '0';
        $b1v_b2v_bc_br_manoeuvre = ($_REQUEST['b1v_b2v_bc_br_manoeuvre' . $i] == 'on') ? '1' : '0';
        $b1v_b2v_bc_br_b1v = ($_REQUEST['b1v_b2v_bc_br_b1v' . $i] == 'on') ? '1' : '0';
        $b1v_b2v_bc_br_b2v = ($_REQUEST['b1v_b2v_bc_br_b2v' . $i] == 'on') ? '1' : '0';
        $b1v_b2v_bc_br_bc = ($_REQUEST['b1v_b2v_bc_br_bc' . $i] == 'on') ? '1' : '0';
        $b1v_b2v_bc_br_br = ($_REQUEST['b1v_b2v_bc_br_br' . $i] == 'on') ? '1' : '0';
        $b1v_b2v_bc_br_essai = ($_REQUEST['b1v_b2v_bc_br_essai' . $i] == 'on') ? '1' : '0';
        $b1v_b2v_bc_br_verification = ($_REQUEST['b1v_b2v_bc_br_verification' . $i] == 'on') ? '1' : '0';
        $b1v_b2v_bc_br_mesurage = ($_REQUEST['b1v_b2v_bc_br_mesurage' . $i] == 'on') ? '1' : '0';
        $b1v_b2v_bc_br_h1v_h2v_b0 = ($_REQUEST['b1v_b2v_bc_br_h1v_h2v_b0' . $i] == 'on') ? '1' : '0';
        $b1v_b2v_bc_br_h1v_h2v_h0v = ($_REQUEST['b1v_b2v_bc_br_h1v_h2v_h0v' . $i] == 'on') ? '1' : '0';
        $b1v_b2v_bc_br_h1v_h2v_bs = ($_REQUEST['b1v_b2v_bc_br_h1v_h2v_bs' . $i] == 'on') ? '1' : '0';
        $b1v_b2v_bc_br_h1v_h2v_manoeuvre = ($_REQUEST['b1v_b2v_bc_br_h1v_h2v_manoeuvre' . $i] == 'on') ? '1' : '0';
        $b1v_b2v_bc_br_h1v_h2v_b1v = ($_REQUEST['b1v_b2v_bc_br_h1v_h2v_b1v' . $i] == 'on') ? '1' : '0';
        $b1v_b2v_bc_br_h1v_h2v_b2v = ($_REQUEST['b1v_b2v_bc_br_h1v_h2v_b2v' . $i] == 'on') ? '1' : '0';
        $b1v_b2v_bc_br_h1v_h2v_bc = ($_REQUEST['b1v_b2v_bc_br_h1v_h2v_bc' . $i] == 'on') ? '1' : '0';
        $b1v_b2v_bc_br_h1v_h2v_br = ($_REQUEST['b1v_b2v_bc_br_h1v_h2v_br' . $i] == 'on') ? '1' : '0';
        $b1v_b2v_bc_br_h1v_h2v_essai = ($_REQUEST['b1v_b2v_bc_br_h1v_h2v_essai' . $i] == 'on') ? '1' : '0';
        $b1v_b2v_bc_br_h1v_h2v_verification = ($_REQUEST['b1v_b2v_bc_br_h1v_h2v_verification' . $i] == 'on') ? '1' : '0';
        $b1v_b2v_bc_br_h1v_h2v_mesurage = ($_REQUEST['b1v_b2v_bc_br_h1v_h2v_mesurage' . $i] == 'on') ? '1' : '0';
        $b1v_b2v_bc_br_h1v_h2v_h1v = ($_REQUEST['b1v_b2v_bc_br_h1v_h2v_h1v' . $i] == 'on') ? '1' : '0';
        $b1v_b2v_bc_br_h1v_h2v_h2v = ($_REQUEST['b1v_b2v_bc_br_h1v_h2v_h2v' . $i] == 'on') ? '1' : '0';
        $b1v_b2v_bc_br_h1v_h2v_hc = ($_REQUEST['b1v_b2v_bc_br_h1v_h2v_hc' . $i] == 'on') ? '1' : '0';
        /* uni_cnfsecrm - modif 104 - FIN */

        /* uni_cnfsecrm - v2 - modif 115 - DEBUT */
        $bs_be_he_he = ($_REQUEST['bs_be_he_he' . $i] == 'on') ? '1' : '0';
        $b1v_b2v_bc_br_h1v_h2v_he = ($_REQUEST['b1v_b2v_bc_br_h1v_h2v_he' . $i] == 'on') ? '1' : '0';
        $b1v_b2v_bc_br_he = ($_REQUEST['b1v_b2v_bc_br_he' . $i] == 'on') ? '1' : '0';
        //echo $bs_be_he_he." ".$b1v_b2v_bc_br_h1v_h2v_he." ".$b1v_b2v_bc_br_he."<br/>";
        /* uni_cnfsecrm - v2 - modif 115 - FIN */
        /* uni_cnfsecrm - v2 - modif 127 - DEBUT */
        $date_start_appr = $_REQUEST['date_start_appr' . $i];
        $date_fin_appr = $_REQUEST['date_fin_appr' . $i];
        $duree_jour = $_REQUEST['duree_jour' . $i];
        $duree_heure = $_REQUEST['duree_heure' . $i];
//        echo $date_start_appr . ' ' . $date_fin_appr . ' ' . $duree_jour . ' ' . $duree_heure;
//        echo '<br/>';
        /* uni_cnfsecrm - v2 - modif 127 - FIN */
//        echo $b0_h0_h0v_b0." ".$b0_h0_h0v_h0v." ".$bs_be_he_b0." ".$bs_be_he_h0v ." ".$bs_be_he_bs ." ".$bs_be_he_manoeuvre ." ".$b1v_b2v_bc_br_b0 ." ".$b1v_b2v_bc_br_h0v ." ".$b1v_b2v_bc_br_bs ." ".$b1v_b2v_bc_br_manoeuvre ." ".$b1v_b2v_bc_br_b1v ." ".$b1v_b2v_bc_br_b2v ." ".$b1v_b2v_bc_br_bc ." ".$b1v_b2v_bc_br_br ." ".$b1v_b2v_bc_br_essai ." ".$b1v_b2v_bc_br_verification ." ".$b1v_b2v_bc_br_mesurage ." ".$b1v_b2v_bc_br_h1v_h2v_b0 ." ".$b1v_b2v_bc_br_h1v_h2v_h0v." ".$b1v_b2v_bc_br_h1v_h2v_bs ." ".$b1v_b2v_bc_br_h1v_h2v_manoeuvre ." ".$b1v_b2v_bc_br_h1v_h2v_b1v ." ".$b1v_b2v_bc_br_h1v_h2v_b2v ." ".$b1v_b2v_bc_br_h1v_h2v_bc ." ".$b1v_b2v_bc_br_h1v_h2v_br." ".$b1v_b2v_bc_br_h1v_h2v_essai ." ".$b1v_b2v_bc_br_h1v_h2v_verification ." ".$b1v_b2v_bc_br_h1v_h2v_mesurage ." ".$b1v_b2v_bc_br_h1v_h2v_h1v ." ".$b1v_b2v_bc_br_h1v_h2v_h2v ." ".$b1v_b2v_bc_br_h1v_h2v_hc ;
//        echo "<br/>";

        $testprerequis = ($_REQUEST['testprerequis' . $i] == 'oui') ? '1' : '0';
        $electricien = ($_REQUEST['electricien' . $i] == 'oui') ? '1' : '0';

        if ($inscrit == 'inscrit') {
            $inscrit = 1;
        } else {
            $inscrit = 0;
        }
        $ticket_examen = $_REQUEST['ticket_examen' . $i];
        $ticket_examen_test = $_REQUEST['ticket_examen_test' . $i];
        $type_tokens = $_REQUEST['type_tokens' . $i];
        $type_tokens_test = $_REQUEST['type_tokens_test' . $i];

        $emailenligne = $_REQUEST['emailenligne' . $i];
        if ($emailenligne != 1)
            $emailenligne = 0;

        /* uni_cnfsecrm - modif 104 - DEBUT */
        /* uni_cnfsecrm - v2 - modif 115 - DEBUT */
        /* uni_cnfsecrm - v2 - modif 127 - DEBUT */
        $query = "insert into vtiger_sessionsapprenantsrel(id,apprenantid,sequence_no,etat,resultat,ticket_examen,inscrit,initiale,recyclage,testprerequis,electricien,ticket_examen_test,type_tokens,type_tokens_test ,b0_h0_h0v_b0, b0_h0_h0v_h0v, bs_be_he_b0, bs_be_he_h0v, bs_be_he_bs, bs_be_he_manoeuvre ,b1v_b2v_bc_br_b0, b1v_b2v_bc_br_h0v, b1v_b2v_bc_br_bs, b1v_b2v_bc_br_manoeuvre, b1v_b2v_bc_br_b1v, b1v_b2v_bc_br_b2v, b1v_b2v_bc_br_bc, b1v_b2v_bc_br_br, b1v_b2v_bc_br_essai, b1v_b2v_bc_br_verification, b1v_b2v_bc_br_mesurage, b1v_b2v_bc_br_h1v_h2v_b0, b1v_b2v_bc_br_h1v_h2v_h0v, b1v_b2v_bc_br_h1v_h2v_bs, b1v_b2v_bc_br_h1v_h2v_manoeuvre,b1v_b2v_bc_br_h1v_h2v_b1v, b1v_b2v_bc_br_h1v_h2v_b2v, b1v_b2v_bc_br_h1v_h2v_bc, b1v_b2v_bc_br_h1v_h2v_br, b1v_b2v_bc_br_h1v_h2v_essai, b1v_b2v_bc_br_h1v_h2v_verification, b1v_b2v_bc_br_h1v_h2v_mesurage, b1v_b2v_bc_br_h1v_h2v_h1v, b1v_b2v_bc_br_h1v_h2v_h2v, b1v_b2v_bc_br_h1v_h2v_hc,bs_be_he_he,b1v_b2v_bc_br_h1v_h2v_he,b1v_b2v_bc_br_he,date_start_appr , date_fin_appr , duree_jour , duree_heure) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,DATE(STR_TO_DATE(?, '%d-%m-%Y')),DATE(STR_TO_DATE(?, '%d-%m-%Y')),?,?)";
        $qparams = array($session, $apprenantid, $i, $etat, $resultat, $ticket_examen, $inscrit, $initiale, $recyclage, $testprerequis, $electricien, $ticket_examen_test, $type_tokens, $type_tokens_test, $b0_h0_h0v_b0, $b0_h0_h0v_h0v, $bs_be_he_b0, $bs_be_he_h0v, $bs_be_he_bs, $bs_be_he_manoeuvre, $b1v_b2v_bc_br_b0, $b1v_b2v_bc_br_h0v, $b1v_b2v_bc_br_bs, $b1v_b2v_bc_br_manoeuvre, $b1v_b2v_bc_br_b1v, $b1v_b2v_bc_br_b2v, $b1v_b2v_bc_br_bc, $b1v_b2v_bc_br_br, $b1v_b2v_bc_br_essai, $b1v_b2v_bc_br_verification, $b1v_b2v_bc_br_mesurage, $b1v_b2v_bc_br_h1v_h2v_b0, $b1v_b2v_bc_br_h1v_h2v_h0v, $b1v_b2v_bc_br_h1v_h2v_bs, $b1v_b2v_bc_br_h1v_h2v_manoeuvre, $b1v_b2v_bc_br_h1v_h2v_b1v, $b1v_b2v_bc_br_h1v_h2v_b2v, $b1v_b2v_bc_br_h1v_h2v_bc, $b1v_b2v_bc_br_h1v_h2v_br, $b1v_b2v_bc_br_h1v_h2v_essai, $b1v_b2v_bc_br_h1v_h2v_verification, $b1v_b2v_bc_br_h1v_h2v_mesurage, $b1v_b2v_bc_br_h1v_h2v_h1v, $b1v_b2v_bc_br_h1v_h2v_h2v, $b1v_b2v_bc_br_h1v_h2v_hc, $bs_be_he_he, $b1v_b2v_bc_br_h1v_h2v_he, $b1v_b2v_bc_br_he, $date_start_appr, $date_fin_appr, $duree_jour, $duree_heure);
        $adb->pquery($query, $qparams);
        $sequence_no++;
        /* uni_cnfsecrm - v2 - modif 127 - FIN */
        /* uni_cnfsecrm - v2 - modif 115 - FIN */
        // uni_cnfsecrm - modif 104 - FIN 
        /* uni_cnfsecrm - module apprenantselearnig - DEBUT */

        $paye = 0;
        $financeur = 0;
        $encompte = 0;
        //echo $apprenantselearningid;
        $date_start = $_REQUEST['date_start'];
        $date_formation = date("d-m-Y", strtotime($date_start));
        $subject = $_REQUEST['subject'];
        $elearning = $_REQUEST['cf_1202'];
        //debugfile("------------------");
        //debugfile("Session - id : " . $session . " - " . $subject);
        //debugfile("elearning : " . $elearning);
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
                if($apprenantid == 136130){
//                    echo "statutFacture1 ".$statutFacture;echo "<br/>";
                }
                if ($statutFacture == 'Paid') {
                    $paye = 1;
                } else {
                    $paye = 0;
                }
                $statutFacture = $adb->query_result($resultStatutFacture, 0, 'invoicestatus');
                
                if($apprenantid == 136130){
//                    echo "statutFacture2 ".$statutFacture;echo "<br/>";
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

        //debugfile("paye : " . $paye);
        // debugfile("financeur : " . $financeur);
        // debugfile("encompte : " . $encompte);
/* uni_cnfsecrm - v2 - modif 160 - DEBUT */
//        if($apprenantid == 136130){
//            echo $elearning."  ".$paye."  ".$financeur."  ".$encompte;die(); 
//        }
        
        if ($elearning == "on" && ($paye == 1 || $financeur == "1" || $encompte == "1" )) {
            $requete_exist_appr_e = "SELECT apprenantselearningid, statut
                FROM vtiger_apprenantselearning                
    where apprenant = ? and session = ?"; // and deleted = ?
            $result_exist_appr_e = $adb->pquery($requete_exist_appr_e, array($apprenantid, $session));
            $num_rows_exist_appr_e = $adb->num_rows($result_exist_appr_e);

            $apprenantselearningid = $adb->query_result($result_exist_appr_e, 0, 'apprenantselearningid');
            $statut = $adb->query_result($result_exist_appr_e, 0, 'statut');
//            echo $apprenantselearningid." ".$statut."<br/>";
            if (html_entity_decode($statut) != "Inscription ignoré") {
                //echo $apprenantselearningid." ".$statut."<br/>";
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
                    //echo $line_count . ' - Ajout Apprenant elearning ' . $firstname . "  " . $lastname . " pour session " . $subject . " <br/> ";
                } else {
                    //echo $line_count . ' - Modification Apprenant elearning ' . $firstname . "  " . $lastname . " pour session " . $subject . " <br/> ";
                    $requete_update = "update vtiger_crmentity set deleted = ? where crmid = ?";
                    $result_update = $adb->pquery($requete_update, array(0, $apprenantselearningid));
                    $focus_appr_elearning->mode = 'edit';
                    $focus_appr_elearning->id = $apprenantselearningid;
                }

                $focus_appr_elearning->save("Apprenantselearning");
                $return_id = $focus_appr_elearning->id;
            }
        }
        /* uni_cnfsecrm - v2 - modif 160 - FIN */
        /* uni_cnfsecrm - module apprenantselearnig - FIN */
    }
//die();
    // $log->debug("Exit from function saveInventoryDatesDetails()."); 
}

?>