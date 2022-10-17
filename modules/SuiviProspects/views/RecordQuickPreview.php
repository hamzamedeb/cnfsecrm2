<?php

/* +**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ********************************************************************************** */
/* uni_cnfsecrm - v2 - modif 120 - FILE */

class SuiviProspects_RecordQuickPreview_View extends Vtiger_RecordQuickPreview_View {

    protected $record = false;

    function __construct() {
        parent::__construct();
    }

    function process(Vtiger_Request $request) {

        $moduleName = $request->getModule();
        $viewer = $this->getViewer($request);
        $recordId = $request->get('record');

        if (!$this->record) {
            $this->record = Vtiger_DetailView_Model::getInstance($moduleName, $recordId);
        }
        if ($request->get('navigation') == 'true') {
            $this->assignNavigationRecordIds($viewer, $recordId);
        }

        $recordModel = $this->record->getRecord();
        $recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_SUMMARY);
        $moduleModel = $recordModel->getModule();

        /* -- */
        $detail = $this->getDetailPopUp($recordId);
        $viewer->assign('DETAIL', $detail);

        $detailHistorique = $this->getDetailHistorique($recordId);
        $viewer->assign('DETAIL_HISTORIQUE', $detailHistorique);
        /* -- */

        $viewer->assign('RECORD', $recordModel);
        $viewer->assign('MODULE_MODEL', $moduleModel);
        $viewer->assign('BLOCK_LIST', $moduleModel->getBlocks());
        $viewer->assign('USER_MODEL', Users_Record_Model::getCurrentUserModel());
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('SUMMARY_RECORD_STRUCTURE', $recordStrucure->getStructure());
        $viewer->assign('$SOCIAL_ENABLED', false);
        $viewer->assign('LIST_PREVIEW', true);
        $appName = $request->get('app');
        $viewer->assign('SELECTED_MENU_CATEGORY', $appName);
        $pageNumber = 1;
        $limit = 5;

        $pagingModel = new Vtiger_Paging_Model();
        $pagingModel->set('page', $pageNumber);
        $pagingModel->set('limit', $limit);

        if ($moduleModel->isCommentEnabled()) {
            //Show Top 5
            $recentComments = ModComments_Record_Model::getRecentComments($recordId, $pagingModel);
            $viewer->assign('COMMENTS', $recentComments);
            $modCommentsModel = Vtiger_Module_Model::getInstance('ModComments');
            $viewer->assign('COMMENTS_MODULE_MODEL', $modCommentsModel);
            $currentUserModel = Users_Record_Model::getCurrentUserModel();
            $viewer->assign('CURRENTUSER', $currentUserModel);
        }

        $viewer->assign('SHOW_ENGAGEMENTS', 'false');
        $recentActivities = ModTracker_Record_Model::getUpdates($recordId, $pagingModel, $moduleName);
        //To show more button for updates if there are more than 5 records
        if (count($recentActivities) >= 5) {
            $pagingModel->set('nextPageExists', true);
        } else {
            $pagingModel->set('nextPageExists', false);
        }
        $viewer->assign('PAGING_MODEL', $pagingModel);
        $viewer->assign('RECENT_ACTIVITIES', $recentActivities);
        $viewer->view('ListViewQuickPreview.tpl', $moduleName);
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
    
    public function getDetailPopUp($recordId) {
        $db = PearDatabase::getInstance();
        $query = "SELECT vtiger_account.email1,vtiger_account.accountid,account_no,accountname,phone,
            vtiger_quotes.quoteid,subject, vtiger_users.id, 
            concat(vtiger_users.first_name,' ',vtiger_users.last_name) as user 
            FROM vtiger_suiviprospects 
            INNER JOIN vtiger_account on vtiger_account.accountid = vtiger_suiviprospects.prospect 
            INNER JOIN vtiger_quotes on vtiger_quotes.quoteid = vtiger_suiviprospects.devis 
            INNER JOIN vtiger_crmentity on vtiger_crmentity.crmid = vtiger_suiviprospects.devis 
            INNER JOIN vtiger_users on vtiger_users.id = vtiger_crmentity.smcreatorid 
            where vtiger_suiviprospects.suiviprospectsid = $recordId";
        $result = $db->pquery($query);
        $detail['recordId'] = $recordId;
        $detail['accountid'] = $db->query_result($result, 0, 'accountid');
        $detail['list']['№ Prospect'] = $db->query_result($result, 0, 'account_no');
        $detail['list']['Nom & prenom'] = $db->query_result($result, 0, 'accountname');
        $detail['list']['Email'] = $db->query_result($result, 0, 'email1');
        $detail['list']['Telphone'] = $db->query_result($result, 0, 'phone');
        $detail['quoteid'] = $db->query_result($result, 0, 'quoteid');
        $detail['list']['Devis'] = html_entity_decode($db->query_result($result, 0, 'subject'));
        $detail['list']['Nom du salarié du CNFSE '] = html_entity_decode($db->query_result($result, 0, 'user')); 
        return $detail;
    }

    const reponsePar = [1 => "Par Telephone", 2 => "Par Email"];
    const reponse = [0 => "", 1 => "Est inscrit chez nous", 2 => "Est parti à la concurrence",
        3 => "Ne veut pas faire", 4 => "Désire être rappeler"];

    public function getDetailHistorique($recordId) {
        $db = PearDatabase::getInstance();
        $query = "SELECT id, commentaire, date_rappel, reponse, date_etre_rappeler 
                    FROM vtiger_rappel_prospects 
                    WHERE vtiger_rappel_prospects.suiviprospectsid = ? order by id DESC";
        $params = array($recordId);
        $result = $db->pquery($query, $params);
        $line_nbr = $db->num_rows($result);
        $listAprnSansSession = array();
        for ($i = 0; $i < $line_nbr; $i++) {
            $detailHistorique[$i]['reponse_par'] = self::reponsePar[$db->query_result($result, $i, 'reponse_par')];
            $detailHistorique[$i]['commentaire'] = $db->query_result($result, $i, 'commentaire');
            $dateRappel = new DateTime($db->query_result($result, $i, 'date_rappel'));
            $dateRappel = date('d-m-Y', strtotime($dateRappel->format("d-m-Y")));
            $detailHistorique[$i]['date_rappel'] = $dateRappel;
            $detailHistorique[$i]['reponse'] = self::reponse[$db->query_result($result, $i, 'reponse')];
            $etreRappeler = new DateTime($db->query_result($result, $i, 'date_etre_rappeler'));
            $etreRappeler = date('d-m-Y', strtotime($etreRappeler->format("d-m-Y")));
            $detailHistorique[$i]['etreRappeler'] = $etreRappeler;
            $detailHistorique[$i]['id'] = $db->query_result($result, $i, 'id');
            $detailHistorique[$i]['reponse_parId'] = $db->query_result($result, $i, 'reponse_par');
            $detailHistorique[$i]['reponseId'] = $db->query_result($result, $i, 'reponse');
        }
        return $detailHistorique;
    }
}
