<?php

/* uni_cnfsecrm - v2 - modif 103 - FILE */
require_once('modules/HistoryImpayes/HistoryImpayes.php');

class HistoryImpayes_MarquerRappeler_Action extends Vtiger_Action_Controller {

    function process(Vtiger_Request $request) {
        global $adb;
        $invoiceId = $request->get('record');

        $response = new Vtiger_Response();

        $query = "SELECT accountid FROM vtiger_invoice WHERE vtiger_invoice.invoiceid = ? ";
        $result = $adb->pquery($query, array($invoiceId));
        $clientId = $adb->query_result($result, 0, 'accountid');

        $query = "SELECT historyimpayesid FROM vtiger_historyimpayes WHERE facture = ? ";
        $result = $adb->pquery($query, array($invoiceId));
        $historiqueId = $adb->query_result($result, 0, 'historyimpayesid');
        
        $query = "SELECT cf_1185 FROM vtiger_invoicecf WHERE invoiceid = ? ";
        $result = $adb->pquery($query, array($invoiceId));
        $typeRelance = html_entity_decode($adb->query_result($result, 0, 'cf_1185'));
        /*uni_cnfsecrm - v2 - modif 112 - DEBUT*/
        if ($typeRelance == "Dépassé de 7 jours"){
            $typeRelance = "Depasse de 7 jours";
        }else if ($typeRelance == "Dépassé de 14 jours"){
            $typeRelance = "Depasse de 14 jours";
        }else if ($typeRelance == "Dépassé de 30 jours"){
            $typeRelance = "Depasse de 30 jours";
        }
        /* uni_cnfsecrm - v2 - modif 112 - FIN */

        if (!is_null($clientId) && !is_null($invoiceId)) {
            if (is_null($historiqueId)) {
                $focus = new HistoryImpayes();
                $focus->mode = 'create';
                $focus->column_fields['name'] = "HistoryImpayes" . $clientId;
                $focus->column_fields['cf_1304'] = date("Y-m-d");
                $focus->column_fields['facture'] = $invoiceId;
                $focus->column_fields['client'] = $clientId;
                $focus->save("HistoryImpayes");
                $idHistoryImpayes = $focus->id;
                $queryHistorique = "UPDATE vtiger_invoicecf SET historyimpayesid = ? WHERE invoiceid = ?";
                $qparamsHistorique = array($idHistoryImpayes, $invoiceId);
                $adb->pquery($queryHistorique, $qparamsHistorique);
                
                /* uni_cnfsecrm - v2 - modif 129 */
                $queryUpdateName = 'UPDATE vtiger_historyimpayes SET name = ? WHERE historyimpayesid = ?';
                $qparamsUpdateName = array('HISTORIQUE_IMPAYES_'.$idHistoryImpayes.'_'.$clientId, $idHistoryImpayes);
                $adb->pquery($queryUpdateName, $qparamsUpdateName);
                /* uni_cnfsecrm - v2 - modif 129 */
            } else {
                $idHistoryImpayes = $historiqueId;
            }

            $dateNow = new DateTime();
            $dateNow = date('d-m-Y', strtotime($dateNow->format("d-m-Y")));

            $queryInsertHistorique = "INSERT INTO vtiger_rappel_impayes (id,historyimpayesid,clientid,factureid,reponse_par,commentaire,date_rappel,type_relance)
                    VALUES (?,?,?,?,?,?,DATE(STR_TO_DATE(?, '%d-%m-%Y')),?)";
            $qparamsInsertHistorique = array('', $idHistoryImpayes, $clientId, $invoiceId, 2, '', $dateNow, $typeRelance);
            $adb->pquery($queryInsertHistorique, $qparamsInsertHistorique);

            $message = "l'apprenant est marqué 'rappeler' ";

            $result = true;
        } else {
            $message = "probleme de mis a jour";
            $result = false;
        }
        $response->setResult(array('message' => $message, 'result' => $result, 'idHistoryImpayes' => $idHistoryImpayes));
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
