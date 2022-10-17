<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */
/* uni_cnfsecrm - modif 101 - FILE */

class Contacts_PopUpApprenantRecyclageDetail_View extends Vtiger_Index_View {

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
        $recordId = $request->get('record');
        $sesionId = $request->get('sesion');
        //var_dump($request);
        $detail = $this->getDetailPopUp($recordId, $sesionId);
        /* uni_cnfsecrm - modif 105 - DEBUT */
        $filterList = $request->get('filterList');
        $this->assignNavigationRecordIds($viewer, $recordId, $sesionId, $filterList);
        /* uni_cnfsecrm - modif 105 - FIN */
        //var_dump($detail);
        $viewer->assign('DETAIL', $detail);

        $viewer->view('PopUpApprenantRecyclageDetail.tpl', $moduleName);
    }

    public function validateRequest(Vtiger_Request $request) {
        $request->validateReadAccess();
    }

    /* uni_cnfsecrm - v2 - modif 117 - DEBUT */
/* uni_cnfsecrm - v2 - modif 121 - DEBUT */
    public function getDetailPopUp($recordId, $sessionId) {
        $db = PearDatabase::getInstance();
        $query = "SELECT vtiger_apprenant_recyclage.recyclage, rappeler, historyrecyclageid, nePlusRappeler,
  vtiger_contactdetails.contact_no,contactid, concat(firstname,' ',lastname) as name,email,
  vtiger_contactdetails.phone, vtiger_activity.subject,activityid,date_start,due_date,vtiger_account.email1
  from vtiger_apprenant_recyclage
  inner join vtiger_contactdetails on vtiger_apprenant_recyclage.apprenantid = vtiger_contactdetails.contactid
  inner JOIN vtiger_activity on vtiger_apprenant_recyclage.sessionid = vtiger_activity.activityid
  INNER JOIN vtiger_account on vtiger_account.accountid = vtiger_contactdetails.accountid
  WHERE vtiger_apprenant_recyclage.apprenantid = ?
  and vtiger_apprenant_recyclage.sessionid = ? ";
        $params = array($recordId, $sessionId);
        $result = $db->pquery($query, $params);
        $detail['contactid'] = $db->query_result($result, 0, 'contactid');
        $detail['list']['№ apprenant'] = $db->query_result($result, 0, 'contact_no');
        $detail['list']['Nom & prenom'] = $db->query_result($result, 0, 'name');
        if ($db->query_result($result, 0, 'email') == '') {
            $detail['list']['Email'] = $db->query_result($result, 0, 'email1');
        } else {
            $detail['list']['Email'] = $db->query_result($result, 0, 'email');
        }
        $detail['list']['Telphone'] = $db->query_result($result, 0, 'phone');
        $detail['activityid'] = $db->query_result($result, 0, 'activityid');
        $detail['list']['Session'] = $db->query_result($result, 0, 'subject');
        $date_start = date("d-m-Y", strtotime($db->query_result($result, 0, 'date_start')));
        $detail['list']['Date Debut'] = $date_start;
        $due_date = date("d-m-Y", strtotime($db->query_result($result, 0, 'due_date')));
        $detail['list']['Date Fin'] = $due_date;
        $detail['list']['Recyclage'] = $db->query_result($result, 0, 'recyclage');
        $detail['apprenantid'] = $recordId;
        $detail['list']['Rappeler'] = $db->query_result($result, 0, 'rappeler');
        $detail['historyrecyclageid'] = $db->query_result($result, 0, 'historyrecyclageid');
        $detail['neplusrappeler'] = $db->query_result($result, 0, 'neplusrappeler');
        return $detail;
    }
/* uni_cnfsecrm - v2 - modif 121 - FIN */
    /* uni_cnfsecrm - v2 - modif 117 - FIN */

    public function getDebutFinDate($sessionId, $date) {
        $db = PearDatabase::getInstance();
        $query = "SELECT date_start from vtiger_sessionsdatesrel where id = $sessionId ORDER BY sequence_no $date";
        $result = $db->pquery($query);
        $date = $db->query_result($result, 0, 'date_start');
        return $date;
    }

    /* uni_cnfsecrm - modif 105 - DEBUT */

    public function assignNavigationRecordIds($viewer, $recordId, $sesionId, $filterList) {
        //Navigation to next and previous records.

        $navigationInfo = $this->getApprenantRecyclage($filterList);
        //var_dump($navigationInfo);
        //Intially make the prev and next records as null
        $prevRecordId = null;
        $nextRecordId = null;
        $prevSessionId = null;
        $nextSessionId = null;
        if ((count($navigationInfo) != 0) || (count($navigationInfo) != 1)) {
            foreach ($navigationInfo as $index => $info) {
                if (($info['apprenantid'] == $recordId) && $info['sessionid'] == $sesionId) {
                    $indexPopUp = $index;
                }
            }
            if ($indexPopUp == 0) {
                $prevRecordId = null;
                $prevSessionId = null;
                $nextRecordId = $navigationInfo[$indexPopUp + 1]['apprenantid'];
                $nextSessionId = $navigationInfo[$indexPopUp + 1]['sessionid'];
            } else if ($indexPopUp == count($navigationInfo) - 1) {
                $nextRecordId = null;
                $nextSessionId = null;
                $prevRecordId = $navigationInfo[$indexPopUp - 1]['apprenantid'];
                $prevSessionId = $navigationInfo[$indexPopUp - 1]['sessionid'];
            } else {
                $prevRecordId = $navigationInfo[$indexPopUp - 1]['apprenantid'];
                $prevSessionId = $navigationInfo[$indexPopUp - 1]['sessionid'];
                $nextRecordId = $navigationInfo[$indexPopUp + 1]['apprenantid'];
                $nextSessionId = $navigationInfo[$indexPopUp + 1]['sessionid'];
            }
        }

        $viewer->assign('PREVIOUS_RECORD_ID', $prevRecordId);
        $viewer->assign('NEXT_RECORD_ID', $nextRecordId);
        $viewer->assign('PREVIOUS_SESSION_ID', $prevSessionId);
        $viewer->assign('NEXT_SESSION_ID', $nextSessionId);
        $viewer->assign('NAVIGATION', true);
        $viewer->assign('TEST', true);
    }

    /* uni_cnfsecrm - v2 - modif 117 - DEBUT */

    public function getApprenantRecyclage($filter) {
        //var_dump($filter);
        $db = PearDatabase::getInstance();
        $query = "select apprenantid,sessionid from vtiger_apprenant_recyclage ";
        if (($filter == 1) || ($filter == '')) {
            $query .= ' where vtiger_apprenant_recyclage.rappeler = ? and vtiger_apprenant_recyclage.nePlusRappeler = ?';
            $params = array(0, 0);
        } else if ($filter == 2) {
            $query .= ' where vtiger_apprenant_recyclage.rappeler = ? and vtiger_apprenant_recyclage.nePlusRappeler = ?';
            $params = array(1, 0);
        } else if ($filter == 3) {
            $query .= ' where vtiger_apprenant_recyclage.nePlusRappeler = ?';
            $params = array(1);
        }
        //var_dump($query);
        $result = $db->pquery($query, $params);
        $line_nbr = $db->num_rows($result);
        $listAprnSansSession = array();
        for ($i = 0; $i < $line_nbr; $i++) {
            $listAprnSansSession[$i]['apprenantid'] = $db->query_result($result, $i, 'apprenantid');
            $listAprnSansSession[$i]['sessionid'] = $db->query_result($result, $i, 'sessionid');
        }
        return $listAprnSansSession;
    }

    /* uni_cnfsecrm - v2 - modif 117 - FIN */
    /* uni_cnfsecrm - modif 105 - FIN */
}
