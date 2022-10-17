<?php

/* uni_cnfsecrm - modif 82 - FILE */

class Apprenantselearning_UpdateStatut_Action extends Vtiger_Action_Controller {

    function process(Vtiger_Request $request) {
        global $adb;
        $recordId = $request->get('record');
        $statut = $request->get('statut');
        $dateRendezVous = $request->get('dateRendezVous');
        $dateNow = new DateTime();
        $dateNow = date('d-m-Y', strtotime($dateNow->format("d-m-Y")));
        /* uni_cnfsecrm - modif 85 - DEBUT */
        $heureRendezVous = $request->get('heureRendezVous');
        /* uni_cnfsecrm - modif 85 - FIN */

        $response = new Vtiger_Response();
        if ($recordId != null && $statut != '') {
            if ($statut == 'marquerInscrit') {
                $typeStatut = 'En cours de formation';
            } else if ($statut == 'validerFormation') {
                $typeStatut = 'Fini la formation';
            } else if ($statut == 'validerTheorique') {
                $typeStatut = 'Rendez-vous à prendre';
            } else if ($statut == 'ajouterRendezVous') {
                $typeStatut = 'Rendez-vous pratique';
                /* uni_cnfsecrm - modif 84 - DEBUT */
            } else if ($statut == 'ignoreInscription') {
                $typeStatut = 'Inscription ignoré';
            }
            $query = 'update vtiger_apprenantselearning SET statut = ? where apprenantselearningid = ?';
            $adb->pquery($query, array($typeStatut, $recordId));
            if ($statut == 'ajouterRendezVous') {
                /* uni_cnfsecrm - modif 85 - DEBUT */
                $query = "update vtiger_apprenantselearningcf SET cf_1218 = DATE(STR_TO_DATE(?, '%d-%m-%Y')),cf_1246 = ? where apprenantselearningid = ?";
                $adb->pquery($query, array($dateRendezVous, $heureRendezVous, $recordId));
                /* uni_cnfsecrm - modif 85 - FIN */
            }

            if ($statut == 'validerTheorique') {
                $query = "update vtiger_apprenantselearningcf SET cf_1242 = ? where apprenantselearningid = ?";
                $adb->pquery($query, array(1, $recordId));
            }
            if ($statut == 'marquerInscrit') {
                $query = "update vtiger_apprenantselearningcf SET cf_1220 = DATE(STR_TO_DATE(?, '%d-%m-%Y')) where apprenantselearningid = ?";
                $adb->pquery($query, array($dateNow, $recordId));
            }
            if ($statut == 'validerFormation' && $dateRendezVous != null) {
                $query = "update vtiger_apprenantselearningcf SET cf_1244 = ? where apprenantselearningid = ?";
                $adb->pquery($query, array(1, $recordId));
            }
            $info = true;
        } else {
            $info = false;
        }
        $response->setResult($info);
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
