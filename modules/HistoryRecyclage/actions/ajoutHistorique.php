<?php

/* uni_cnfsecrm - v2 - modif 111 - FILE */

class HistoryRecyclage_ajoutHistorique_Action extends Vtiger_Action_Controller {

    const reponsePar = [1 => "Par Telephone", 2 => "Par Email"];
    const reponse = [1 => "Est inscrit chez nous", 2 => "Est parti à la concurrence",
        3 => "Ne veut pas faire", 4 => "Désire être rappeler"];

    function process(Vtiger_Request $request) {
        global $adb;
        $reponsePar = $request->get('reponsePar');
        $commentaire = $request->get('commentaire');
        $dateRappel = $request->get('dateRappel');
        $reponse = $request->get('reponse');
        $apprenantId = $request->get('apprenantId');
        $sessionId = $request->get('sessionId');
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
                $query = "INSERT INTO vtiger_rappel_recyclage (id,historyrecyclageid,sessionid,apprenantid,reponse,date_rappel,commentaire,reponse_par,date_etre_rappeler)
                    VALUES (?,?,?,?,?,DATE(STR_TO_DATE(?, '%d-%m-%Y')),?,?,DATE(STR_TO_DATE(?, '%d-%m-%Y')) )";
                $params = array('', $record, $sessionId, $apprenantId, $reponse, $dateRappel, $commentaire, $reponsePar, $etreRappeler);
                $adb->pquery($query, $params);
                $idRow = $adb->getLastInsertID();
                $message = "bien ajouter ";
            } else if ($typeAction == "edit") {
                $query = "update vtiger_rappel_recyclage SET  reponse = ?,date_rappel = DATE(STR_TO_DATE(?, '%d-%m-%Y')),commentaire = ?,reponse_par = ?,date_etre_rappeler = DATE(STR_TO_DATE(?, '%d-%m-%Y')) where id = ?";
                $adb->pquery($query, array($reponse, $dateRappel, $commentaire, $reponsePar, $etreRappeler, $idRow));
                $message = "bien modifier ";
            }
            //modifier HistoryRecyclage(module)
            if ($updateModule == 1) {
                $query = "update vtiger_historyrecyclagecf SET  cf_1254 = ?,cf_1252 = DATE(STR_TO_DATE(?, '%d-%m-%Y')),cf_1256 = DATE(STR_TO_DATE(?, '%d-%m-%Y')) where historyrecyclageid = ?";
                $adb->pquery($query, array(self::reponse[$reponse], $dateRappel, $etreRappeler, $record));
            }
            
            /* uni_cnfsecrm - v2 - modif 130 - DEBUT */
            if ($reponse == 4 && $updateModule == 1){
                $adb->pquery("UPDATE vtiger_historyrecyclage set status = ? where historyrecyclageid = ?", array(0, $record));
            }
            /* uni_cnfsecrm - v2 - modif 130 - FIN */
            
            $result = true;
        } else {
            $message = "probleme d'ajout";
            $result = false;
        }

//        $etreRappeler = new DateTime($etreRappeler);
//        $etreRappeler = date('d-m-Y', strtotime($etreRappeler->format("d-m-Y")));
//
//        $dateRappel = new DateTime($dateRappel);
//        $dateRappel = date('d-m-Y', strtotime($dateRappel->format("d-m-Y")));

        $dataNewRow = array(
            "reponsePar" => self::reponsePar[$reponsePar],
            "commentaire" => $commentaire,
            "dateRappel" => $dateRappel,
            "reponse" => self::reponse[$reponse],
            "etreRappeler" => $etreRappeler,
            "reponseId" => $reponse,
            "reponseParId" => $reponsePar,
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
