<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */
/* uni_cnfsecrm - v2 - modif 120 - FILE */

class Contacts_PopUpProspectsDetail_View extends Vtiger_Index_View {

    protected $record = false;

    function __construct() {
        parent::__construct();
    }

    function checkPermission(Vtiger_Request $request) {
        //Return true as WebUI.php is already checking for module permission
        return true;
    }

    function process(Vtiger_Request $request) {

        $moduleName = $request->getModule();
        $viewer = $this->getViewer($request);
        $accountId = $request->get('record');
        $devisId = $request->get('quoteId');
        //var_dump($request);
        $detail = $this->getDetailPopUp($accountId, $devisId);
        $filterList = $request->get('filterList');
       // var_dump($filterList);
        $this->assignNavigationRecordIds($viewer, $accountId, $devisId, $filterList);
        //var_dump($detail);
        $viewer->assign('DETAIL', $detail);

        $viewer->view('PopUpProspectsDetail.tpl', $moduleName);
    }

    public function validateRequest(Vtiger_Request $request) {
        $request->validateReadAccess();
    }

    /* uni_cnfsecrm - v2 - modif 117 - DEBUT */

    public function getDetailPopUp($accountId, $devisId) {
        $db = PearDatabase::getInstance();
        $query = "SELECT vtiger_suivi_prospects.idclient, iddevis, rappeler, neplusrappeler, 
            suiviprospectsid,vtiger_account.account_no,accountname, email1,phone,
            vtiger_quotes.quoteid,subject
            FROM vtiger_suivi_prospects
            INNER JOIN vtiger_account on vtiger_account.accountid = vtiger_suivi_prospects.idclient
            INNER JOIN vtiger_quotes on vtiger_quotes.quoteid = vtiger_suivi_prospects.iddevis
            WHERE vtiger_suivi_prospects.idclient = ? and vtiger_suivi_prospects.iddevis = ?";
        $params = array($accountId, $devisId);
        $result = $db->pquery($query, $params);
        $detail['idclient'] = $db->query_result($result, 0, 'idclient');
        $detail['list']['№ Prospect'] = $db->query_result($result, 0, 'account_no');
        $detail['list']['Nom & prenom'] = $db->query_result($result, 0, 'accountname');
        $detail['list']['Email'] = $db->query_result($result, 0, 'email1');
        $detail['list']['Telphone'] = $db->query_result($result, 0, 'phone');
        $detail['iddevis'] = $db->query_result($result, 0, 'iddevis');
        $detail['list']['Devis'] = html_entity_decode($db->query_result($result, 0, 'subject'));
        $detail['list']['Rappeler'] = $db->query_result($result, 0, 'rappeler');
//        $date_start = date("d-m-Y", strtotime($db->query_result($result, 0, 'date_start')));
//        $detail['list']['Date Debut'] = $date_start;
        $detail['suiviprospectsid'] = $db->query_result($result, 0, 'suiviprospectsid');
        $detail['neplusrappeler'] = $db->query_result($result, 0, 'neplusrappeler');
        return $detail;
    }

    public function assignNavigationRecordIds($viewer, $accountId, $devisId, $filterList) {
        //Navigation to next and previous records.

        $navigationInfo = $this->getProspects($filterList);
        //var_dump($navigationInfo);
        //Intially make the prev and next records as null
        $prevRecordId = null;
        $nextRecordId = null;
        $prevDevisId = null;
        $nextDevisId = null;
        if ((count($navigationInfo) != 0) || (count($navigationInfo) != 1)) {
            foreach ($navigationInfo as $index => $info) {
                if (($info['idclient'] == $accountId) && $info['iddevis'] == $devisId) {
                    $indexPopUp = $index;
                }
            }
            if ($indexPopUp == 0) {
                $prevRecordId = null;
                $prevDevisId = null;
                $nextRecordId = $navigationInfo[$indexPopUp + 1]['idclient'];
                $nextDevisId = $navigationInfo[$indexPopUp + 1]['iddevis'];
            } else if ($indexPopUp == count($navigationInfo) - 1) {
                $nextRecordId = null;
                $nextDevisId = null;
                $prevRecordId = $navigationInfo[$indexPopUp - 1]['idclient'];
                $prevDevisId = $navigationInfo[$indexPopUp - 1]['iddevis'];
            } else {
                $prevRecordId = $navigationInfo[$indexPopUp - 1]['idclient'];
                $prevDevisId = $navigationInfo[$indexPopUp - 1]['iddevis'];
                $nextRecordId = $navigationInfo[$indexPopUp + 1]['idclient'];
                $nextDevisId = $navigationInfo[$indexPopUp + 1]['iddevis'];
            }
        }

        $viewer->assign('PREVIOUS_RECORD_ID', $prevRecordId);
        $viewer->assign('NEXT_RECORD_ID', $nextRecordId);
        $viewer->assign('PREVIOUS_DEVIS_ID', $prevDevisId);
        $viewer->assign('NEXT_DEVIS_ID', $nextDevisId);
        $viewer->assign('NAVIGATION', true);
        $viewer->assign('TEST', true);
    }
    public function getProspects($filter) {
        //var_dump($filter);
        $db = PearDatabase::getInstance();
        $query = "SELECT idclient, iddevis FROM vtiger_suivi_prospects";
        if (($filter == 1) || ($filter == '')) {
            $query .= ' where vtiger_suivi_prospects.rappeler = ? and vtiger_suivi_prospects.neplusrappeler = ?';
            $params = array(0, 0);
        } else if ($filter == 2) {
            $query .= ' where vtiger_suivi_prospects.rappeler = ? and vtiger_suivi_prospects.neplusrappeler = ?';
            $params = array(1, 0);
        } else if ($filter == 3) {
            $query .= ' where vtiger_suivi_prospects.neplusrappeler = ?';
            $params = array(1);
        }
        //var_dump($query);
        $result = $db->pquery($query, $params);
        $line_nbr = $db->num_rows($result);
        $listProspects = array();
        for ($i = 0; $i < $line_nbr; $i++) {
            $listProspects[$i]['idclient'] = $db->query_result($result, $i, 'idclient');
            $listProspects[$i]['iddevis'] = $db->query_result($result, $i, 'iddevis');
        }
        return $listProspects;
    }
}
