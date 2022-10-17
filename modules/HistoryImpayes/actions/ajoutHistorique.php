<?php

/* uni_cnfsecrm - v2 - modif 103 - FILE */

class HistoryImpayes_ajoutHistorique_Action extends Vtiger_Action_Controller {

    const reponsePar = [1 => "Par Telephone", 2 => "Par Email"];

    function process(Vtiger_Request $request) {
        global $adb;
        $reponsePar = $request->get('reponsePar');
        $commentaire = $request->get('commentaire');
        $dateRappel = $request->get('dateRappel');
        $dateEcheance = $request->get('dateEcheance');
        $clientid = $request->get('clientid');
        $invoiceid = $request->get('invoiceid');
        $record = $request->get('record');
        $idRow = $request->get('id');
        $typeAction = $request->get('typeAction');
        $updateModule = $request->get('updateModule');
        $typeRelance = html_entity_decode($request->get('typeRelance'));


        $monfichier = fopen('debug_historiqueimpayee.txt', 'a+');
        fputs($monfichier, "\n" . "reponsePar " . $reponsePar . "commentaire " . $commentaire
                . "dateRappel " . $dateRappel . "dateEcheance " . $dateEcheance . "clientid " . $clientid
                . "invoiceid " . $invoiceid . "record " . $record . "idRow " . $idRow . "typeAction " . $typeAction
                . "updateModule " . $updateModule . "typeRelance " . $typeRelance);
        fclose($monfichier);

        $dateRappel = new DateTime($dateRappel);
        $dateRappel = date('d-m-Y', strtotime($dateRappel->format("d-m-Y")));

        $dateEcheance = new DateTime($dateEcheance);
        $dateEcheance = date('d-m-Y', strtotime($dateEcheance->format("d-m-Y")));

        $response = new Vtiger_Response();

        if (!is_null($clientid)) {
            if ($typeAction == "save") {
                $query = "INSERT INTO vtiger_rappel_impayes (id,historyimpayesid,clientid,factureid,reponse_par,commentaire,date_rappel,date_echeance, type_relance)
          VALUES (?,?,?,?,?,?,DATE(STR_TO_DATE(?, '%d-%m-%Y')),DATE(STR_TO_DATE(?, '%d-%m-%Y')),? )";
                $params = array('', $record, $clientid, $invoiceid, $reponsePar, $commentaire, $dateRappel, $dateEcheance, $typeRelance);
                $adb->pquery($query, $params);
                $idRow = $adb->getLastInsertID();
                $message = "bien ajouter ";
            } else if ($typeAction == "edit") {
                $query = "update vtiger_rappel_impayes SET reponse_par = ?,commentaire = ?,date_rappel = DATE(STR_TO_DATE(?, '%d-%m-%Y')),date_echeance = DATE(STR_TO_DATE(?, '%d-%m-%Y')),type_relance = ? where id = ?";
                $adb->pquery($query, array($reponsePar, $commentaire, $dateRappel, $dateEcheance, $typeRelance, $idRow));
                $message = "bien modifier ";
            }
            //modifier HistoryRecyclage(module)
            /* uni_cnfsecrm - v2 - modif 103 DEBUT */
            if ($updateModule == 1) {
                $query = "update vtiger_historyimpayescf SET cf_1268 = DATE(STR_TO_DATE(?, '%d-%m-%Y')),cf_1270 = DATE(STR_TO_DATE(?, '%d-%m-%Y')) where historyimpayesid = ?";
                $adb->pquery($query, array($dateRappel, $dateEcheance, $record));
            }
            /* uni_cnfsecrm - v2 - modif 103 - FIN */
            /* uni_cnfsecrm - v2 - modif 130 - DEBUT */
            if ($updateModule == 1){
                $adb->pquery("UPDATE vtiger_historyimpayes set status = ? where historyimpayesid = ?", array(0, $record));
            }
            /* uni_cnfsecrm - v2 - modif 130 - FIN */
            $result = true;
        } else {
            $message = "probleme d'ajout";
            $result = false;
        }

        $dataNewRow = array(
            "reponsePar" => self::reponsePar[$reponsePar],
            "commentaire" => $commentaire,
            "dateRappel" => $dateRappel,
            "dateEcheance" => $dateEcheance,
            "reponseParId" => $reponsePar,
            "typeRelance" => $typeRelance,
            "id" => $idRow
        );

        $response->setResult(array('message' => $message, 'result' => $result, 'dataNewRow' => $dataNewRow));
        $response->emit();
    }

    function checkPermission(Vtiger_Request $request) {
        return;
    }

    function resolveReferenceLabel($id, $module = false) {
        if (empty($id)) {
            return '';
        }
        if ($module === false) {
            $module = getSalesEntityType($id);
        }
        $label = getEntityName($module, array($id));
        return decode_html($label[$id]);
    }

}
