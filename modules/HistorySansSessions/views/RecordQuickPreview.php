<?php

/* +**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ********************************************************************************** */
/* uni_cnfsecrm - v2 - modif 107 - FILE */

class HistorySansSessions_RecordQuickPreview_View extends Vtiger_RecordQuickPreview_View {

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

        $detail = $this->getDetail($recordId);
        $viewer->assign('DETAIL', $detail);

        $detailHistorique = $this->getDetailHistorique($recordId);
        $viewer->assign('DETAIL_HISTORIQUE', $detailHistorique);

        $detailAbsent = $this->getDetailAbsent($recordId);
        $viewer->assign('DETAIL_ABSENT', $detailAbsent);


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

    /* uni_cnfsecrm - v2 - modif 121 - DEBUT */

    public function getDetail($recordId) {
        $db = PearDatabase::getInstance();
        $query = "SELECT vtiger_contactdetails.contact_no,concat(firstname,' ',lastname) as name, 
            contactid,email,vtiger_contactdetails.phone ,vtiger_account.email1
            FROM vtiger_historysanssessions 
            INNER JOIN vtiger_contactdetails ON vtiger_historysanssessions.apprenant = vtiger_contactdetails.contactid 
            INNER JOIN vtiger_account on vtiger_account.accountid = vtiger_contactdetails.accountid
            WHERE vtiger_historysanssessions.historysanssessionsid = $recordId";
        $result = $db->pquery($query);
        $detail['recordId'] = $recordId;
        $detail['contactid'] = $db->query_result($result, 0, 'contactid');
        $detail['list']['№ apprenant'] = $db->query_result($result, 0, 'contact_no');
        $detail['list']['Nom & prenom'] = $db->query_result($result, 0, 'name');
        if ($db->query_result($result, 0, 'email') == '') {
            $detail['list']['Email'] = $db->query_result($result, 0, 'email1');
        } else {
            $detail['list']['Email'] = $db->query_result($result, 0, 'email');
        }
        $detail['list']['Telphone'] = $db->query_result($result, 0, 'phone');

        return $detail;
    }

    /* uni_cnfsecrm - v2 - modif 121 - FIN */

    const reponsePar = [1 => "Par Telephone", 2 => "Par Email"];
    const reponse = [0 => "", 1 => "Est inscrit chez nous", 2 => "Est parti à la concurrence",
        3 => "Ne veut pas faire", 4 => "Désire être rappeler"];

    /* uni_cnfsecrm - v2 - modif 117 - DEBUT */

    public function getDetailHistorique($recordId) {
        $db = PearDatabase::getInstance();
        $query = "SELECT id, apprenantid, reponse, date, etre_rappler, historysanssessionsid, commentaire
                    FROM vtiger_historique_sans_session 
                    WHERE vtiger_historique_sans_session.historysanssessionsid = ? order by id DESC";
        $params = array($recordId);
        $result = $db->pquery($query, $params);
        $line_nbr = $db->num_rows($result);
        $listAprnSansSession = array();
        for ($i = 0; $i < $line_nbr; $i++) {
            $detailHistorique[$i]['commentaire'] = $db->query_result($result, $i, 'commentaire');
            $dateRappel = new DateTime($db->query_result($result, $i, 'date'));
            $dateRappel = date('d-m-Y', strtotime($dateRappel->format("d-m-Y")));
            $detailHistorique[$i]['date'] = $dateRappel;
            $detailHistorique[$i]['reponse'] = self::reponse[$db->query_result($result, $i, 'reponse')];
            $etreRappeler = new DateTime($db->query_result($result, $i, 'etre_rappler'));
            $etreRappeler = date('d-m-Y', strtotime($etreRappeler->format("d-m-Y")));
            $detailHistorique[$i]['etreRappeler'] = $etreRappeler;
            $detailHistorique[$i]['id'] = $db->query_result($result, $i, 'id');
            $detailHistorique[$i]['reponseId'] = $db->query_result($result, $i, 'reponse');
        }
        return $detailHistorique;
    }

    /* uni_cnfsecrm - v2 - modif 117 - FIN */
    /* uni_cnfsecrm - v2 - modif 117 - DEBUT */

    public function getDetailAbsent($recordId) {
        $db = PearDatabase::getInstance();

        $query = " SELECT apprenant ,vtiger_histoapprabsents.idconvention, vtiger_salesorder.session, 
            vtiger_activity.subject,activityid,date_start,due_date
            FROM vtiger_historysanssessions 
            LEFT JOIN vtiger_histoapprabsents on vtiger_histoapprabsents.idapprenant = vtiger_historysanssessions.apprenant
            LEFT JOIN vtiger_salesorder on vtiger_salesorder.salesorderid = vtiger_histoapprabsents.idconvention
            LEFT JOIN vtiger_activity on vtiger_activity.activityid = vtiger_salesorder.session
            WHERE historysanssessionsid = $recordId and vtiger_histoapprabsents.action = 3";
        $result = $db->pquery($query);

        $detail['list']['Formation'] = $db->query_result($result, 0, 'subject');
        $detail['activityid'] = $db->query_result($result, 0, 'activityid');
        $date_start = date("d-m-Y", strtotime($db->query_result($result, 0, 'date_start')));
        $detail['list']['Date de la session'] = $date_start;
        $detail['idconvention'] = $db->query_result($result, 0, 'idconvention');
        $detail['list']['Nom du salarié du CNFSE'] = $this->getUserCreate($detail['idconvention']);
        return $detail;
    }

    /* uni_cnfsecrm - v2 - modif 117 - FIN */

    public function getDebutFinDate($sessionId) {
        $db = PearDatabase::getInstance();
        $query = "SELECT date_start from vtiger_sessionsdatesrel where id = $sessionId ORDER BY sequence_no ASC";
        $result = $db->pquery($query);
        $date = $db->query_result($result, 0, 'date_start');
        return $date;
    }

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
