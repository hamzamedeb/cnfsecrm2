<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */
/* uni_cnfsecrm - v2 - modif 107 - FILE */

class Contacts_PopUpApprenantSansSessionDetail_View extends Vtiger_Index_View {

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

        $detail = $this->getDetailPopUp($recordId);
        $detailAbsent = $this->getDetailAbsent($recordId);

        $filterList = $request->get('filterList');

        $this->assignNavigationRecordIds($viewer, $recordId, $filterList);
        $viewer->assign('DETAIL', $detail);
        $viewer->assign('DETAIL_ABSENT', $detailAbsent);


        $viewer->view('PopUpApprenantSansSessionDetail.tpl', $moduleName);
    }

    public function validateRequest(Vtiger_Request $request) {
        $request->validateReadAccess();
    }

    /* uni_cnfsecrm - v2 - modif 117 - DEBUT */
    /* uni_cnfsecrm - v2 - modif 121 - DEBUT */
    public function getDetailPopUp($recordId) {
        $db = PearDatabase::getInstance();
        $query = "SELECT vtiger_app_sanssession.id_apprenant,rappeler,neplusrappeler, historysanssessionsid,
            vtiger_contactdetails.contactid , contact_no, concat(firstname,' ',lastname) as name,
            email,vtiger_contactdetails.phone ,vtiger_account.email1
            from vtiger_app_sanssession 
            inner join vtiger_contactdetails on vtiger_contactdetails.contactid = vtiger_app_sanssession.id_apprenant 
            INNER JOIN vtiger_account on vtiger_account.accountid = vtiger_contactdetails.accountid
            where id_apprenant = ?";
        $params = array($recordId);
        $result = $db->pquery($query, $params);

        $detail['contactid'] = $db->query_result($result, 0, 'contactid');
        $detail['rappeler'] = $db->query_result($result, 0, 'rappeler');
        $detail['neplusrappeler'] = $db->query_result($result, 0, 'neplusrappeler');
        $detail['historysanssessionsid'] = $db->query_result($result, 0, 'historysanssessionsid');
        $detail['list']['№ apprenant'] = $db->query_result($result, 0, 'contact_no');
        $detail['list']['Nom & prenom'] = $db->query_result($result, 0, 'name');
        if ($db->query_result($result, 0, 'email') == ''){
            $detail['list']['Email'] = $db->query_result($result, 0, 'email1');
        }else {
            $detail['list']['Email'] = $db->query_result($result, 0, 'email');
        }
        $detail['list']['Telphone'] = $db->query_result($result, 0, 'phone');
        //var_dump($detail);
        return $detail;
    }
    /* uni_cnfsecrm - v2 - modif 121 - FIN */
    /* uni_cnfsecrm - v2 - modif 117 - FIN */

    public function assignNavigationRecordIds($viewer, $recordId, $filterList) {
        $navigationInfo = $this->getApprenantAbsent($filterList);
        //var_dump($navigationInfo);
        //var_dump($navigationInfo);
        //Intially make the prev and next records as null
        $prevRecordId = null;
        $nextRecordId = null;
        if ((count($navigationInfo) != 0) || (count($navigationInfo) != 1)) {
            foreach ($navigationInfo as $index => $info) {
                if (($info['apprenantid'] == $recordId) && $info['sessionid'] == $sesionId) {
                    $indexPopUp = $index;
                }
            }
            if ($indexPopUp == 0) {
                $prevRecordId = null;
                $nextRecordId = $navigationInfo[$indexPopUp + 1]['apprenantid'];
            } else if ($indexPopUp == count($navigationInfo) - 1) {
                $nextRecordId = null;
                $prevRecordId = $navigationInfo[$indexPopUp - 1]['apprenantid'];
            } else {
                $prevRecordId = $navigationInfo[$indexPopUp - 1]['apprenantid'];
                $nextRecordId = $navigationInfo[$indexPopUp + 1]['apprenantid'];
            }
        }
//        var_dump($prevRecordId);
//        var_dump($nextRecordId);
        $viewer->assign('PREVIOUS_RECORD_ID', $prevRecordId);
        $viewer->assign('NEXT_RECORD_ID', $nextRecordId);
        $viewer->assign('NAVIGATION', true);
        $viewer->assign('TEST', true);
    }

    /* uni_cnfsecrm - v2 - modif 117 - DEBUT */

    public function getApprenantAbsent($filterList) {
        //var_dump($filter);
        $db = PearDatabase::getInstance();
        $query = " SELECT id_apprenant from vtiger_app_sanssession ";
        if (($filterList == 1) || ($filterList == '')) {
            $query .= ' where vtiger_app_sanssession.rappeler = ? and vtiger_app_sanssession.neplusrappeler = ?';
            $params = array(0, 0);
        } else if ($filterList == 2) {
            $query .= ' where vtiger_app_sanssession.rappeler = ? and vtiger_app_sanssession.neplusrappeler = ?';
            $params = array(1, 0);
        } else if ($filterList == 3) {
            $query .= ' where vtiger_app_sanssession.neplusrappeler = ?';
            $params = array(1);
        }
        //var_dump($query);
        $result = $db->pquery($query, $params);
        $line_nbr = $db->num_rows($result);
        $listAprnSansSession = array();
        for ($i = 0; $i < $line_nbr; $i++) {
            $listAprnSansSession[$i]['apprenantid'] = $db->query_result($result, $i, 'id_apprenant');
        }
        return $listAprnSansSession;
    }

    /* uni_cnfsecrm - v2 - modif 117 - FIN */

    /* uni_cnfsecrm - v2 - modif 117 - DEBUT */

    public function getDetailAbsent($recordId) {
        $db = PearDatabase::getInstance();
        $query = " SELECT vtiger_histoapprabsents.idconvention,idapprenant, vtiger_salesorder.session, 
            vtiger_activity.subject,activityid, date_start
            FROM vtiger_histoapprabsents 
            LEFT JOIN vtiger_salesorder on vtiger_salesorder.salesorderid = vtiger_histoapprabsents.idconvention
            LEFT JOIN vtiger_activity on vtiger_activity.activityid = vtiger_salesorder.session
            WHERE vtiger_histoapprabsents.idapprenant = ? and vtiger_histoapprabsents.action = ?";
        $params = array($recordId, 3);
        $result = $db->pquery($query, $params);

        $detail['list']['Formation'] = $db->query_result($result, 0, 'subject');
        $detail['activityid'] = $db->query_result($result, 0, 'activityid');
        $date_start = date("d-m-Y", strtotime($db->query_result($result, 0, 'date_start')));
        $detail['list']['Date de la session'] = $date_start;
        $detail['idconvention'] = $db->query_result($result, 0, 'idconvention');
        $detail['list']['Nom du salarié du CNFSE'] = $this->getUserCreate($detail['idconvention']);
        return $detail;
    }

    /* uni_cnfsecrm - v2 - modif 117 - FIN */

    /* uni_cnfsecrm - v2 - modif 117 - DEBUT */

    public function getUserCreate($idconvention) {
        $db = PearDatabase::getInstance();
        $query = "SELECT  vtiger_crmentity.smcreatorid,CONCAT(vtiger_users.first_name,' ',last_name) as user
            FROM vtiger_crmentity 
            LEFT JOIN vtiger_users on vtiger_users.id = smcreatorid
            WHERE  vtiger_crmentity.crmid = ?";
        $params = array($idconvention);
        $result = $db->pquery($query, $params);
        $user = $db->query_result($result, 0, 'user');
        return $user;
    }

    /* uni_cnfsecrm - v2 - modif 117 - FIN */
}
