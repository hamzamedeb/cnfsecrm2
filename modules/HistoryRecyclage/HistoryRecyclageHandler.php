<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */
/* uni_cnfsecrm - modif 102 - FILE */

class HistoryRecyclageHandler extends VTEventHandler {

    function handleEvent($eventName, $entityData) {

        $moduleName = $entityData->getModuleName();

        if ($moduleName != 'HistoryRecyclage') {
            return;
        }

        global $current_user, $currentModule;
        $db = PearDatabase::getInstance();
        /**
         * Adjust the balance amount against total & received amount
         * NOTE: beforesave the total amount will not be populated in event data.
         */
        if ($eventName == 'vtiger.entity.aftersave') {
            $dateRelance = $entityData->get('cf_1252');
            $etreRappeler = $entityData->get('cf_1256');
            $session = $entityData->get('session');
            $contact = $entityData->get('contacts');
            $reponse = $entityData->get('cf_1254');
            $record = $entityData->getId();

            if ($reponse == 'Est inscrit chez nous') {
                $reponseId = 1;
            } else if ($reponse == 'Est parti à la concurrence') {
                $reponseId = 2;
            } else if ($reponse == 'Ne veut pas faire') {
                $reponseId = 3;
            } else if ($reponse == 'Désire être rappeler') {
                $reponseId = 4;
            }

            if ($reponseId != 4) {
                $etreRappeler = '';
            }

            /* Code désactivé car à chaque modification de la fiche Historique il ajoute une ligne même si les données dont les mêmes */
            /*$query = "INSERT INTO vtiger_rappel_recyclage (id,historyrecyclageid,sessionid,apprenantid,reponse,date_rappel,reponse_par,date_etre_rappeler)
                    VALUES (?,?,?,?,?,DATE(STR_TO_DATE(?, '%d-%m-%Y')),?,DATE(STR_TO_DATE(?, '%d-%m-%Y')) )";
            $params = array('', $record, $session, $contact, $reponseId, $dateRelance, 1, $etreRappeler);
            $db->pquery($query, $params);*/
        }
    }

}

?>