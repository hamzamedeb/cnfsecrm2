<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Invoice_Detail_View extends Inventory_Detail_View {
    /* uni_cnfsecrm - modif 88 - DEBUT */

    /**
     * Added to support Engagements view in Vtiger7
     * @param Vtiger_Request $request
     */
    function _showRecentActivities(Vtiger_Request $request) {
        $parentRecordId = $request->get('record');
        $pageNumber = $request->get('page');
        $limit = $request->get('limit');
        $moduleName = $request->getModule();

        if (empty($pageNumber)) {
            $pageNumber = 1;
        }

        $pagingModel = new Vtiger_Paging_Model();
        $pagingModel->set('page', $pageNumber);
        if (!empty($limit)) {
            $pagingModel->set('limit', $limit);
        }

        $recentActivities = ModTracker_Record_Model::getUpdates($parentRecordId, $pagingModel, $moduleName);
        $pagingModel->calculatePageRange($recentActivities);

        if ($pagingModel->getCurrentPage() == ModTracker_Record_Model::getTotalRecordCount($parentRecordId) / $pagingModel->getPageLimit()) {
            $pagingModel->set('nextPageExists', false);
        }
        $recordModel = Vtiger_Record_Model::getInstanceById($parentRecordId);
        $viewer = $this->getViewer($request);
        $viewer->assign('SOURCE', $recordModel->get('source'));
        /* uni_cnfsecrm - modif 88 - DEBUT */
        $recentActivitiesinvoice = $this->getRecentActivities($request);
        $viewer->assign('RECENT_ACTIVITIES_INVOICE', $recentActivitiesinvoice);
        /* uni_cnfsecrm - modif 88 - FIN */
        $viewer->assign('RECENT_ACTIVITIES', $recentActivities);
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('PAGING_MODEL', $pagingModel);
        $viewer->assign('RECORD_ID', $parentRecordId);
    }

    /**
     * Function returns recent changes made on the record
     * @param Vtiger_Request $request
     */
    function showRecentActivities(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $this->_showRecentActivities($request);
        $viewer = $this->getViewer($request);
        echo $viewer->view('RecentActivities.tpl', $moduleName, true);
    }

    function getRecentActivities($request) {
        $detailRecentActivitie = array();
        $db = PearDatabase::getInstance();
        $id = $request->get('record');
        $listQuery = "SELECT vtiger_crmentity.createdtime, smcreatorid, vtiger_users.first_name, 
            vtiger_users.last_name 
            FROM vtiger_crmentity 
            inner join vtiger_users on vtiger_users.id = vtiger_crmentity.smcreatorid 
            WHERE crmid = ?";
        $result = $db->pquery($listQuery, array($id));
        $detailRecentActivitie['createdtime'] = $db->query_result($result, 0, 'createdtime');
        $nom = $db->query_result($result, 0, 'first_name');
        $prenom = $db->query_result($result, 0, 'last_name');
        $detailRecentActivitie['nom'] = $nom . ' ' . $prenom;
        return $detailRecentActivitie;
    }

    /* uni_cnfsecrm - modif 88 - FIN */
}
