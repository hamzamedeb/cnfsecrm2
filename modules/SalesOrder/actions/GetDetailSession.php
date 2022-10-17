<?php

// unicnfsecrm_mod_50
class SalesOrder_GetDetailSession_Action extends Vtiger_Action_Controller {

    function process(Vtiger_Request $request) {
        global $adb;
        $recordId = $request->get('record');
        $response = new Vtiger_Response();

        $query = "SELECT date_start,due_date,formation,cf_921,cf_933,cf_931,cf_929,cf_1195,vtiger_activity.salle as idsalle,vtiger_activity.lieu as idlieu,
                    vtiger_lieu.libelle as nomlieu,vtiger_salle.libelle as nomsalle,vtiger_crmentity.smownerid,cf_854,cf_998,cf_996,subject,cf_1202
                    FROM vtiger_activity
                    left join vtiger_crmentity on vtiger_crmentity.crmid = vtiger_activity.activityid 
                    left join vtiger_salle on vtiger_salle.salleid = vtiger_activity.salle 
                    left join vtiger_lieu on vtiger_lieu.lieuid = vtiger_activity.lieu 
                    left join vtiger_activitycf on vtiger_activitycf.activityid = vtiger_activity.activityid 
                    where vtiger_activity.activityid = ?";
        $result = $adb->pquery($query, array($recordId));

        $dateStart = $adb->query_result($result, 0, 'date_start');
        $dateStart = date("d-m-Y", strtotime($dateStart));
        $dueDate = $adb->query_result($result, 0, 'due_date');
        $dueDate = date("d-m-Y", strtotime($dueDate));
        $formation = $adb->query_result($result, 0, 'formation');
        $locaux = $adb->query_result($result, 0, 'cf_921');
        $adresse = $adb->query_result($result, 0, 'cf_933');
        $ville = $adb->query_result($result, 0, 'cf_931');
        $cp = $adb->query_result($result, 0, 'cf_929');
        $salle = $adb->query_result($result, 0, 'nomsalle');
        $lieu = $adb->query_result($result, 0, 'nomlieu');
        $idSalle = $adb->query_result($result, 0, 'idsalle');
        $idLieu = $adb->query_result($result, 0, 'idlieu');
        $formateur = $adb->query_result($result, 0, 'smownerid');
        $region = $adb->query_result($result, 0, 'cf_1195');
        $type = $adb->query_result($result, 0, 'cf_854');
        $nbrJours = $adb->query_result($result, 0, 'cf_998');
        $nbrHeures = $adb->query_result($result, 0, 'cf_996');
        $subject = html_entity_decode($adb->query_result($result, 0, 'subject'));
        $elearning = $adb->query_result($result, 0, 'cf_1202');

        /* uni_cnfsecrm - modif 80 - DEBUT */
        $queryNbreApprenant = "select count(id) as nbreapprenant 
                from vtiger_sessionsapprenantsrel 
                where id = ? and apprenantid != ?";
        $result = $adb->pquery($queryNbreApprenant, array($recordId, 0));
        $nbreApprenant = $adb->query_result($result, 0, 'nbreapprenant');
        /* uni_cnfsecrm - modif 80 - FIN */

        $resultData = array(
            'dateStart' => $dateStart,
            'dueDate' => $dueDate,
            'formation' => $formation,
            'locaux' => $locaux,
            'adresse' => html_entity_decode($adresse),
            'ville' => $ville,
            'cp' => $cp,
            'idsalle' => $idSalle,
            'idlieu' => $idLieu,
            'nomSalle' => $salle,
            'nomLieu' => $lieu,
            'formateur' => $formateur,
            'region' => html_entity_decode($region),
            'type' => $type,
            'nbrJours' => $nbrJours,
            'nbrHeures' => $nbrHeures,
            'subject' => $subject,
            'elearning' => $elearning,
            /* uni_cnfsecrm - modif 80 - DEBUT */
            'nbreApprenant' => $nbreApprenant
                /* uni_cnfsecrm - modif 80 - FIN */
        );
        $response->setResult($resultData);
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
