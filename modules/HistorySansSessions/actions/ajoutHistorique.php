<?php

/* uni_cnfsecrm - v2 - modif 107 - FILE */

class HistorySansSessions_ajoutHistorique_Action extends Vtiger_Action_Controller {

    const reponse = [1 => "Est inscrit chez nous", 2 => "Est parti à la concurrence",
        3 => "Ne veut pas faire", 4 => "Désire être rappeler"];
 
    function process(Vtiger_Request $request) { 
        global $adb;
        $commentaire = $request->get('commentaire');
        $dateRappel = $request->get('dateRappel');
        $reponse = $request->get('reponse');
        $apprenantId = $request->get('apprenantId');
        $record = $request->get('record');
        $idRow = $request->get('id');
        $typeAction = $request->get('typeAction');
        $updateModule = $request->get('updateModule'); 

        if ($reponse == 4) {
            $etreRappeler = $request->get('etreRappeler');
            $etreRappeler = new DateTime($etreRappeler);
            $etreRappeler = date('d-m-Y', strtotime($etreRappeler->format("d-m-Y")));
        } else {
            $etreRappeler = '';
        }

        $dateRappel = new DateTime($dateRappel);
        $dateRappel = date('d-m-Y', strtotime($dateRappel->format("d-m-Y")));

        $response = new Vtiger_Response();

         if (!is_null($apprenantId)) {
            if ($typeAction == "save") {
                $query = "INSERT INTO vtiger_historique_sans_session (id, apprenantid, reponse, date, etre_rappler, historysanssessionsid, commentaire)
                    VALUES (?,?,?,DATE(STR_TO_DATE(?, '%d-%m-%Y')),DATE(STR_TO_DATE(?, '%d-%m-%Y')),?,? )";
                $params = array('', $apprenantId, $reponse, $dateRappel, $etreRappeler, $record, $commentaire);
                $adb->pquery($query, $params);
                $idRow = $adb->getLastInsertID(); 
            } else if ($typeAction == "edit") {
                $query = "update vtiger_historique_sans_session SET  reponse = ?,date = DATE(STR_TO_DATE(?, '%d-%m-%Y')),commentaire = ?,etre_rappler = DATE(STR_TO_DATE(?, '%d-%m-%Y')) where id = ?";
                $adb->pquery($query, array($reponse, $dateRappel, $commentaire, $etreRappeler, $idRow));
            }
            //modifier HistoryRecyclage(module)
            if ($updateModule == 1) {
                $query = "update vtiger_historysanssessionscf SET  cf_1281 = ?,cf_1279 = DATE(STR_TO_DATE(?, '%d-%m-%Y')),cf_1283 = DATE(STR_TO_DATE(?, '%d-%m-%Y')) where historysanssessionsid = ?";
                $adb->pquery($query, array(self::reponse[$reponse], $dateRappel, $etreRappeler, $record));
            }
            
            /* uni_cnfsecrm - v2 - modif 130 - DEBUT */
            if ($reponse == 4 && $updateModule == 1){
                $adb->pquery("UPDATE vtiger_historysanssessions set status = ? where historysanssessionsid = ?", array(0, $record));
            }
            /* uni_cnfsecrm - v2 - modif 130 - FIN */
            $result = true;
        } else {
            $result = false;
        }

//        $etreRappeler = new DateTime($etreRappeler);
//        $etreRappeler = date('d-m-Y', strtotime($etreRappeler->format("d-m-Y")));
//
//        $dateRappel = new DateTime($dateRappel);
//        $dateRappel = date('d-m-Y', strtotime($dateRappel->format("d-m-Y")));

        $dataNewRow = array(
            "commentaire" => $commentaire,
            "dateRappel" => $dateRappel,
            "reponse" => self::reponse[$reponse],
            "etreRappeler" => $etreRappeler,
            "reponseId" => $reponse,
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
