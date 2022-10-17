<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class SalesOrderHandler extends VTEventHandler {

    function handleEvent($eventName, $entityData) {
        global $log, $adb;
        //$db = PearDatabase::getInstance();
        if ($eventName == 'vtiger.entity.aftersave') {
            $moduleName = $entityData->getModuleName();
            if ($moduleName == 'SalesOrder') {

                /* uni_cnfsecrm - mise à jour numéro de convention */
                $salesorderid = $entityData->getId();
                $dateconvention = $entityData->get('createdtime');
                $salesorder_no = $entityData->get('salesorder_no');

                $dateconvention = strtotime($dateconvention);
                $dateconvention = date("Ymd", $dateconvention);

                $numero_convention = $salesorder_no . '-' . $dateconvention; //VA
                $sql = "UPDATE vtiger_salesordercf SET cf_982=? WHERE salesorderid=?";
                $adb->pquery($sql, array($numero_convention, $salesorderid));

                /* unicnfsecrm_15 */
                $account_id = $entityData->get('account_id');
                $adb->pquery("UPDATE vtiger_account SET account_type=? WHERE accountid=?", array('Customer', $account_id));

                // modification hamza 18/07/2019

                $id_session = $entityData->get('session');
                if ($id_session) {

                    $sequences_no = 0;
                    //sequence no session
                    $sequence_session = array();
                    $sequenceQuery = 'SELECT sequence_no FROM vtiger_sessionsapprenantsrel WHERE id=?';
                    $sequenceParams = array($id_session);
                    $sequenceResult = $adb->pquery($sequenceQuery, $sequenceParams);
                    $sequenceCount = $adb->num_rows($sequenceResult);
                    if ($sequenceCount) {
                        $sequences_no = $adb->query_result($sequenceResult, $sequenceCount - 1, 'sequence_no');
                    }

                    //sequence no session

                    $apprenant_convention = array();
                    $apprenantQuery = 'SELECT apprenantid,sequence_no,etat,resultat,inscrit,convoque
                                        FROM vtiger_inventoryapprenantsrel 
                                        WHERE id=?';
                    $apprenantParams = array($entityData->getId());
                    $apprenantResult = $adb->pquery($apprenantQuery, $apprenantParams);
                    $apprenantCount = $adb->num_rows($apprenantResult);
                    if ($apprenantCount) {
                        for ($i = 0; $i < $apprenantCount; $i++) {
                            $apprenantid = $adb->query_result($apprenantResult, $i, 'apprenantid');
                            $etat = $adb->query_result($apprenantResult, $i, 'etat');
                            $resultat = $adb->query_result($apprenantResult, $i, 'resultat');
                            $inscrit = $adb->query_result($apprenantResult, $i, 'inscrit');
                            $convoque = $adb->query_result($apprenantResult, $i, 'convoque');

                            $apprenantsQuery = 'SELECT apprenantid,sequence_no,etat,resultat,inscrit
                                        FROM vtiger_sessionsapprenantsrel 
                                        WHERE id=? and apprenantid=?';
                            $apprenantsParams = array($id_session, $apprenantid);
                            $apprenantsResult = $adb->pquery($apprenantsQuery, $apprenantsParams);
                            $apprenantsCount = $adb->num_rows($apprenantsResult);

                            if ($apprenantsCount == 0) {
                                $sequences_no += 1;
                                $adb->pquery('INSERT INTO vtiger_sessionsapprenantsrel (id,apprenantid,sequence_no,etat,resultat,inscrit) VALUES (?, ?, ?, ?, ?, ?)', array($id_session, $apprenantid, $sequences_no, $etat, $resultat, $inscrit));
                            }
                        }
                    }
                }

                /* unicnfsecrm_022020_25 - begin */
                /* Mise a jour detail de facture apres modification de la convention */
                //selectioner la facture attacher au convention
                $queryInvoice = 'select vtiger_invoice.invoiceid from vtiger_invoice where vtiger_invoice.salesorderid =?';
                $paramsInvoice = array($salesorderid);
                $resultInvoice = $adb->pquery($queryInvoice, $paramsInvoice);

                $invoiceId = $adb->query_result($resultInvoice, '0', 'invoiceid');

                $idClient = $entityData->get('account_id');
                $lieu = $entityData->get('lieu');
                $salle = $entityData->get('salle');
                $sessionId = $entityData->get('session');

                $locaux = $entityData->get('cf_860');
                $suiteAdresse = $entityData->get('cf_973');

                $adresse = $entityData->get('bill_street');
                $boitePostale = $entityData->get('bill_pobox');
                $ville = $entityData->get('bill_city');
                $etat = $entityData->get('bill_state');
                $cp = $entityData->get('bill_code');
                $pays = $entityData->get('bill_country');

                //update facture
                $queryUpdateInvoice1 = "update vtiger_invoice set accountid = ?,lieu = ?,salle=?,session=? where vtiger_invoice.invoiceid = ? ";
                $paramsUpdateInvoice1 = array($idClient, $lieu, $salle, $sessionId, $invoiceId);
                $adb->pquery($queryUpdateInvoice1, $paramsUpdateInvoice1);

                $queryUpdateInvoice2 = "update vtiger_invoicecf set cf_1028 = ?,cf_1026 = ? where invoiceid = ?";
                $paramsUpdateInvoice2 = array($locaux, $suiteAdresse, $invoiceId);
                $adb->pquery($queryUpdateInvoice2, $paramsUpdateInvoice2);

                $queryUpdateInvoice3 = "update vtiger_invoicebillads set bill_street=?,bill_pobox = ?,bill_city = ?,bill_state = ?,bill_code = ?,bill_country = ? where invoicebilladdressid = ?";
                $paramsUpdateInvoice3 = array($adresse, $boitePostale, $ville, $etat, $cp, $pays, $invoiceId);
                $adb->pquery($queryUpdateInvoice3, $paramsUpdateInvoice3);

                //journee
                $queryJournee = "SELECT sequence_no,date_start,start_matin,end_matin,start_apresmidi,end_apresmidi,duree_formation 
                            FROM vtiger_inventorydatesrel where id = ? ";
                $paramsJournee = array($salesorderid);
                $resultJournee = $adb->pquery($queryJournee, $paramsJournee);
                $countJournee = $adb->num_rows($resultJournee);
                $ListJournees = array();
                for ($i = 0; $i < $countJournee; $i++) {
                    $ListJournees[$i]['sequence'] = $adb->query_result($resultJournee, $i, 'sequence_no');
                    $ListJournees[$i]['date_start'] = $adb->query_result($resultJournee, $i, 'date_start');
                    $ListJournees[$i]['start_matin'] = $adb->query_result($resultJournee, $i, 'start_matin');
                    $ListJournees[$i]['end_matin'] = $adb->query_result($resultJournee, $i, 'end_matin');
                    $ListJournees[$i]['start_apresmidi'] = $adb->query_result($resultJournee, $i, 'start_apresmidi');
                    $ListJournees[$i]['end_apresmidi'] = $adb->query_result($resultJournee, $i, 'end_apresmidi');
                    $ListJournees[$i]['duree_formation'] = $adb->query_result($resultJournee, $i, 'duree_formation');
                }

                //fin journee
                //apprenant
                $queryApprenant = "select apprenantid , sequence_no, etat, resultat, inscrit, convoque, be_essai, be_mesurage, 
                            be_verification, be_manoeuvre, he_essai, he_mesurage, he_verification, he_manoeuvre, initiale, recyclage,
                            testprerequis, electricien from vtiger_inventoryapprenantsrel WHERE id = ?";
                $paramsApprenant = array($salesorderid);
                $resultApprenant = $adb->pquery($queryApprenant, $paramsApprenant);
                $countApprenant = $adb->num_rows($resultApprenant);
                $listApprenants = array();
                for ($i = 0; $i < $countApprenant; $i++) {
                    $listApprenants[$i]['apprenantid'] = $adb->query_result($resultApprenant, $i, 'apprenantid');
                    $listApprenants[$i]['sequence'] = $adb->query_result($resultApprenant, $i, 'sequence_no');
                    $listApprenants[$i]['etat'] = $adb->query_result($resultApprenant, $i, 'etat');
                    $listApprenants[$i]['resultat'] = $adb->query_result($resultApprenant, $i, 'resultat');
                    $listApprenants[$i]['inscrit'] = $adb->query_result($resultApprenant, $i, 'inscrit');
                    $listApprenants[$i]['convoque'] = $adb->query_result($resultApprenant, $i, 'convoque');
                    $listApprenants[$i]['be_essai'] = $adb->query_result($resultApprenant, $i, 'be_essai');
                    $listApprenants[$i]['be_mesurage'] = $adb->query_result($resultApprenant, $i, 'be_mesurage');
                    $listApprenants[$i]['be_verification'] = $adb->query_result($resultApprenant, $i, 'be_verification');
                    $listApprenants[$i]['be_manoeuvre'] = $adb->query_result($resultApprenant, $i, 'be_manoeuvre');
                    $listApprenants[$i]['he_essai'] = $adb->query_result($resultApprenant, $i, 'he_essai');
                    $listApprenants[$i]['he_mesurage'] = $adb->query_result($resultApprenant, $i, 'he_mesurage');
                    $listApprenants[$i]['he_verification'] = $adb->query_result($resultApprenant, $i, 'he_verification');
                    $listApprenants[$i]['he_manoeuvre'] = $adb->query_result($resultApprenant, $i, 'he_manoeuvre');
                    $listApprenants[$i]['initiale'] = $adb->query_result($resultApprenant, $i, 'initiale');
                    $listApprenants[$i]['recyclage'] = $adb->query_result($resultApprenant, $i, 'recyclage');
                    $listApprenants[$i]['testprerequis'] = $adb->query_result($resultApprenant, $i, 'testprerequis');
                    $listApprenants[$i]['electricien'] = $adb->query_result($resultApprenant, $i, 'electricien');
                }

                //fin apprenant
                //supprimer les anciens apprenant relier au facture
                $querySuppApprenant = 'delete from vtiger_inventoryapprenantsrel where id = ? ';
                $paramsSuppApprenant = array($invoiceId);
                $adb->pquery($querySuppApprenant, $paramsSuppApprenant);
                //fin suppresion des apprenant
                //supprimer les anciens journee relier au facture
                $querySuppJournee = 'delete from vtiger_inventorydatesrel where id = ?';
                $paramsSuppJournee = array($invoiceId);
                $adb->pquery($querySuppJournee, $paramsSuppJournee);
                //fin suppresion des journee
                foreach ($ListJournees as $ListJournee) {
                    $queryInsertJournee = "INSERT INTO vtiger_inventorydatesrel (id,sequence_no,date_start,start_matin,end_matin,start_apresmidi,
                      end_apresmidi,duree_formation) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                    $paramsJournee = array($invoiceId, $ListJournee['sequence'], $ListJournee['date_start'], $ListJournee['start_matin'],
                        $ListJournee['end_matin'], $ListJournee['start_apresmidi'], $ListJournee['end_apresmidi'],
                        $ListJournee['duree_formation']);
                    $adb->pquery($queryInsertJournee, $paramsJournee);
                }

//                    $r = print_r($listApprenants, true);
//                    $monfichier = fopen('debug_test2.txt', 'a+');
//                    fputs($monfichier, "\n" . "list apprenant " . $r);
//                    fclose($monfichier);
                foreach ($listApprenants as $listApprenant) {
                    $queryInsertApprenant = "INSERT INTO vtiger_inventoryapprenantsrel (id, apprenantid , sequence_no, etat, resultat, inscrit,
                      convoque, be_essai, be_mesurage,be_verification, be_manoeuvre, he_essai, he_mesurage, he_verification, he_manoeuvre,
                      initiale, recyclage,testprerequis, electricien) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                    $paramsApprenant = array($invoiceId, $listApprenant['apprenantid'], $listApprenant['sequence'], $listApprenant['etat'],
                        $listApprenant['resultat'], $listApprenant['inscrit'], $listApprenant['convoque'], $listApprenant['be_essai'],
                        $listApprenant['be_mesurage'], $listApprenant['be_verification'], $listApprenant['be_manoeuvre'],
                        $listApprenant['he_essai'], $listApprenant['he_mesurage'], $listApprenant['he_verification'],
                        $listApprenant['he_manoeuvre'], $listApprenant['initiale'], $listApprenant['recyclage'], $listApprenant['testprerequis'],
                        $listApprenant['electricien']);
                    $adb->pquery($queryInsertApprenant, $paramsApprenant);
                }
                //fin update facture
                /* unicnfsecrm_022020_25 - end */
            }
        }
    }

}
