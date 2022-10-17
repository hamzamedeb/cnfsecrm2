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
    for ($i = 1; $i <= $totalapprenantsCount; $i++) {
        $id = $focus->id;
        $session = $id;
        $apprenantid = $_REQUEST['apprenantid' . $i];
        $etat = $_REQUEST['etat' . $i];
        $resultat = $_REQUEST['resultat' . $i];
        $inscrit = $_REQUEST['inscrit' . $i];

        $be_essai = ($_REQUEST['be_essai' . $i] == 'on') ? '1' : '0';
        $be_mesurage = ($_REQUEST['be_mesurage' . $i] == 'on') ? '1' : '0';
        $be_verification = ($_REQUEST['be_verification' . $i] == 'on') ? '1' : '0';
        $be_manoeuvre = ($_REQUEST['be_manoeuvre' . $i] == 'on') ? '1' : '0';
        $he_essai = ($_REQUEST['he_essai' . $i] == 'on') ? '1' : '0';
        $he_mesurage = ($_REQUEST['he_mesurage' . $i] == 'on') ? '1' : '0';
        $he_verification = ($_REQUEST['he_verification' . $i] == 'on') ? '1' : '0';
        $he_manoeuvre = ($_REQUEST['he_manoeuvre' . $i] == 'on') ? '1' : '0';
        $initiale = ($_REQUEST['initiale' . $i] == 'on') ? '1' : '0';
        $recyclage = ($_REQUEST['recyclage' . $i] == 'on') ? '1' : '0';

        $initiale = ($recyclage == 0) ? $initiale = 1 : $initiale = 0;

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

        $query = "insert into vtiger_sessionsapprenantsrel(id,apprenantid,sequence_no,etat,resultat,ticket_examen,inscrit,be_essai,be_mesurage,be_verification,be_manoeuvre,he_essai,he_mesurage,he_verification,he_manoeuvre,initiale,recyclage,testprerequis,electricien,ticket_examen_test,type_tokens,type_tokens_test,emailenligne) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
        $qparams = array($id, $apprenantid, $i, $etat, $resultat, $ticket_examen, $inscrit, $be_essai, $be_mesurage, $be_verification, $be_manoeuvre, $he_essai, $he_mesurage, $he_verification, $he_manoeuvre, $initiale, $recyclage, $testprerequis, $electricien, $ticket_examen_test, $type_tokens, $type_tokens_test, $emailenligne);
        $adb->pquery($query, $qparams);
        $sequence_no++;

        /* uni_cnfsecrm - module apprenantselearnig - DEBUT */

        $paye = 0;
        $financeur = 0;
        $encompte = 0;
        //echo $apprenantselearningid;
        $date_start = $_REQUEST['date_start'];
        $date_formation = date("d-m-Y", strtotime($date_start));
        $subject = $_REQUEST['subject'];
        $elearning = $_REQUEST['cf_1202'];

        debugfile("------------------");
        debugfile("Session - id : " . $session . " - " . $subject);
        debugfile("elearning : " . $elearning);
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

            debugfile("Nom : " . $firstname . " " . $lastname);

            $queryStatutFacture = 'SELECT invoiceid,invoicestatus,salesorderid 
                                    FROM vtiger_invoice 
                                    WHERE session = ? AND accountid = ?';
            $paramsStatutFacture = array($session, $accountid);
            $resultStatutFacture = $adb->pquery($queryStatutFacture, $paramsStatutFacture);
            $nbreFactures = $adb->num_rows($resultStatutFacture);

            $monfichier = fopen('debug_05022021.txt', 'a+');
            fputs($monfichier, "\n" . ' ------------------ ');
            fputs($monfichier, "\n" . ' nbreFactures :  ' . $nbreFactures);
            fclose($monfichier);

            if ($nbreFactures > 0) {
                $statutFacture = $adb->query_result($resultStatutFacture, 0, 'invoicestatus');
                $factureid = $adb->query_result($resultStatutFacture, 0, 'invoiceid');
                $salesorderid = $adb->query_result($resultStatutFacture, 0, 'salesorderid');
                if ($statutFacture == 'Paid') {
                    $paye = 1;
                } else {
                    $paye = 0;
                }
            } else {
                $queryConvention = 'SELECT salesorderid FROM vtiger_salesorder WHERE session = ? AND accountid = ?';
                $paramsConvention = array($session, $accountid);
                $resultConvention = $adb->pquery($queryConvention, $paramsConvention);
                $salesorderid = $adb->query_result($resultConvention, 0, 'salesorderid');
            }

            $queryNbreFinanceur = 'SELECT id FROM vtiger_inventoryfinanceurrel WHERE id = ? AND vendorid <> ?';
            $paramsNbreFinanceur = array($salesorderid, 0);
            $resultNbreFinanceur = $adb->pquery($queryNbreFinanceur, $paramsNbreFinanceur);
            $nbreFinanceur = $adb->num_rows($resultNbreFinanceur);

            if ($nbreFinanceur > 0) {
                $financeur = 1;
            } else {
                $financeur = 0;
            }
        }

        debugfile("paye : " . $paye);
        debugfile("financeur : " . $financeur);
        debugfile("encompte : " . $encompte);

        $monfichier = fopen('debug_05022021.txt', 'a+');


        fputs($monfichier, "\n" . "accountid : " . $accountid);
        fputs($monfichier, "\n" . "paye : " . $paye);
        fputs($monfichier, "\n" . "financeur : " . $financeur);
        fputs($monfichier, "\n" . "encompte : " . $encompte);
        fclose($monfichier);

        if ($elearning == "on" && ($paye == 1 || $financeur == "1" || $encompte == "1" )) {
            $requete_exist_appr_e = "SELECT apprenantselearningid
                FROM vtiger_apprenantselearning                
    where apprenant = ? and session = ?"; // and deleted = ?
            $result_exist_appr_e = $adb->pquery($requete_exist_appr_e, array($apprenantid, $session));
            $num_rows_exist_appr_e = $adb->num_rows($result_exist_appr_e);

            $apprenantselearningid = $adb->query_result($result_exist_appr_e, 0, 'apprenantselearningid');

            $current_user = new Users();
            $current_user->id = 1;
            $currentUser = vglobal('current_user', $current_user);

            $focus_appr_elearning = new Apprenantselearning();
            $focus_appr_elearning->column_fields['assigned_user_id'] = 59;
            $name = "Formation " . html_entity_decode($firstname . " " . $lastname);
            $focus_appr_elearning->column_fields['name'] = $name;
            $focus_appr_elearning->column_fields['apprenant'] = $apprenantid;
            $focus_appr_elearning->column_fields['session'] = $session;
            $focus_appr_elearning->column_fields['client'] = $accountid;
            $focus_appr_elearning->column_fields['statut'] = 'Stagiaire à inscrire';

            if ($num_rows_exist_appr_e <= 0) {
                $focus_appr_elearning->mode = 'create';
                //echo $line_count . ' - Ajout Apprenant elearning ' . $firstname . "  " . $lastname . " pour session " . $subject . " <br/> ";
                $focus_appr_elearning->save("Apprenantselearning");
                $return_id = $focus_appr_elearning->id;
            } else {
                //echo $line_count . ' - Modification Apprenant elearning ' . $firstname . "  " . $lastname . " pour session " . $subject . " <br/> ";
                $requete_update = "update vtiger_crmentity set deleted = ? where crmid = ?";
                $result_update = $adb->pquery($requete_update, array(0, $apprenantselearningid));
                $focus_appr_elearning->mode = 'edit';
                $focus_appr_elearning->id = $apprenantselearningid;
            }
        }
        /* uni_cnfsecrm - module apprenantselearnig - FIN */
    }
    // $log->debug("Exit from function saveInventoryDatesDetails()."); 
}

?>