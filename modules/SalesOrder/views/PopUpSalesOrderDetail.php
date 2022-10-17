<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */
/* uni_cnfsecrm - v2 - modif 94 - FILE */

class SalesOrder_PopUpSalesOrderDetail_View extends Vtiger_Index_View {

    protected $record = false;

    function __construct() {
        parent::__construct();
    }

    function process(Vtiger_Request $request) {

        $moduleName = $request->getModule();
        $viewer = $this->getViewer($request);
        $recordId = $request->get('record');
        $apprenant = $request->get('apprenant');
        $historiqueApp = $this->getHistoriqueApp($recordId, $apprenant);
        $lastAction = $this->getDernierAction($recordId, $apprenant);

        $viewer->assign('HISTORIQUE_APP', $historiqueApp);
        $viewer->assign('LAST_ACTION', $lastAction);
        $viewer->view('PopUpSalesOrderDetail.tpl', $moduleName);
    }

    public function assignNavigationRecordIds($viewer, $recordId) {
        //Navigation to next and previous records.
        $navigationInfo = ListViewSession::getListViewNavigation($recordId);
        //Intially make the prev and next records as null
        $prevRecordId = null;
        $nextRecordId = null;
        $found = false;
        if ($navigationInfo) {
            foreach ($navigationInfo as $page => $pageInfo) {
                foreach ($pageInfo as $index => $record) {
                    //If record found then next record in the interation
                    //will be next record
                    if ($found) {
                        $nextRecordId = $record;
                        break;
                    }
                    if ($record == $recordId) {
                        $found = true;
                    }
                    //If record not found then we are assiging previousRecordId
                    //assuming next record will get matched
                    if (!$found) {
                        $prevRecordId = $record;
                    }
                }
                //if record is found and next record is not calculated we need to perform iteration
                if ($found && !empty($nextRecordId)) {
                    break;
                }
            }
        }
        $viewer->assign('PREVIOUS_RECORD_ID', $prevRecordId);
        $viewer->assign('NEXT_RECORD_ID', $nextRecordId);
        $viewer->assign('NAVIGATION', true);
    }

    public function validateRequest(Vtiger_Request $request) {
        $request->validateReadAccess();
    }

    const HISTORIQUE = [1 => "Apprenant attaché à la Session ",
        2 => "Un avoir 'Lien Avoir' a été crée",
        3 => "Apprenant ajouté à la liste des 'Stagiaires sans session'"
    ];

    public function getHistoriqueApp($recordId, $apprenant) {
        global $adb;
        $historiqueApp = array();
        $query = "select * from vtiger_histoapprabsents where idapprenant = ? and idconvention = ? ORDER BY id DESC";
        $param = array($apprenant, $recordId);
        $result = $adb->pquery($query, $param);
        $count = $adb->num_rows($result);
        for ($i = 0; $i < $count; $i++) {
            if ($adb->query_result($result, $i, 'action') != 0) {
                if ($adb->query_result($result, $i, 'action') == 1) {
                    $nomSession = $this->getNomSession($adb->query_result($result, $i, 'idsession'));
                    $lien = "<a href='index.php?module=Calendar&view=Detail&record=" . $adb->query_result($result, $i, 'idsession') . "&app=SALES'>" . $nomSession . "</a> ";
                    $historiqueApp['historique'][$i] = self::HISTORIQUE[$adb->query_result($result, $i, 'action')] . " " . $lien;
                } else if ($adb->query_result($result, $i, 'action') == 2) {
                    $detailAvoir = $this->getIdAvoir($recordId, $apprenant);
                    $idAvoir = $detailAvoir[0];
                    $subject = $detailAvoir[1];
                    $lienAvoir = "<a href='index.php?module=Invoice&view=Detail&record=$idAvoir&app=INVENTORY'>" . $subject . "</a> ";
                    $historiqueApp['historique'][$i] = "Un avoir $lienAvoir a été crée";
                } else {
                    $historiqueApp['historique'][$i] = self::HISTORIQUE[$adb->query_result($result, $i, 'action')];
                }
                $historiqueApp['action'][$i] = $adb->query_result($result, $i, 'action');
            }
        }
        return $historiqueApp;
    }

    public function getNomSession($id) {
        global $adb;
        if ($id != 0) {
            $query = "select subject from vtiger_activity where activityid = $id";
            $result = $adb->pquery($query);
            return $adb->query_result($result, 0, 'subject');
        } else {
            return null;
        }
    }

    public function getDernierAction($recordId, $apprenant) {
        global $adb;
        $query = "select * from vtiger_histoapprabsents where idapprenant = ? and idconvention = ? ORDER BY id DESC";
        $param = array($apprenant, $recordId);
        $result = $adb->pquery($query, $param);
        $lastAction = $adb->query_result($result, $i, 'action');
        return $lastAction;
    }

    public function getIdAvoir($recordId, $apprenant) {
        global $adb;
        $queryAccount = "select idfacture FROM vtiger_histoapprabsents WHERE idapprenant = ? and action = ?";
        $paramAccount = array($apprenant, 2);
        $resultAccount = $adb->pquery($queryAccount, $paramAccount);
        $idfacture = $adb->query_result($resultAccount, 0, 'idfacture');

        $query = "select subject FROM vtiger_invoice where invoiceid = ?";
        $param = array($idfacture);
        $result = $adb->pquery($query, $param);
        $subject = html_entity_decode($adb->query_result($result, 0, 'subject'));
        $detailAvoir = array($idfacture, $subject);

        return $detailAvoir;
    }
}
