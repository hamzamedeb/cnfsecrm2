<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Contacts_Module_Model extends Vtiger_Module_Model {

    /**
     * Function to get the Quick Links for the module
     * @param <Array> $linkParams
     * @return <Array> List of Vtiger_Link_Model instances
     */
    public function getSideBarLinks($linkParams) {
        $parentQuickLinks = parent::getSideBarLinks($linkParams);

        $quickLink = array(
            'linktype' => 'SIDEBARLINK',
            'linklabel' => 'LBL_DASHBOARD',
            'linkurl' => $this->getDashBoardUrl(),
            'linkicon' => '',
        );

        //Check profile permissions for Dashboards
        $moduleModel = Vtiger_Module_Model::getInstance('Dashboard');
        $userPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        $permission = $userPrivilegesModel->hasModulePermission($moduleModel->getId());
        if ($permission) {
            $parentQuickLinks['SIDEBARLINK'][] = Vtiger_Link_Model::getInstanceFromValues($quickLink);
        }

        return $parentQuickLinks;
    }

    /**
     * Function returns the Calendar Events for the module
     * @param <Vtiger_Paging_Model> $pagingModel
     * @return <Array>
     */
    public function getCalendarActivities($mode, $pagingModel, $user, $recordId = false) {
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $db = PearDatabase::getInstance();

        if (!$user) {
            $user = $currentUser->getId();
        }

        $nowInUserFormat = Vtiger_Datetime_UIType::getDisplayDateTimeValue(date('Y-m-d H:i:s'));
        $nowInDBFormat = Vtiger_Datetime_UIType::getDBDateTimeValue($nowInUserFormat);
        list($currentDate, $currentTime) = explode(' ', $nowInDBFormat);

        $query = "SELECT vtiger_crmentity.crmid, crmentity2.crmid AS contact_id, vtiger_crmentity.smownerid, vtiger_crmentity.setype, vtiger_activity.* FROM vtiger_activity
					INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_activity.activityid
					INNER JOIN vtiger_cntactivityrel ON vtiger_cntactivityrel.activityid = vtiger_activity.activityid
					INNER JOIN vtiger_crmentity AS crmentity2 ON vtiger_cntactivityrel.contactid = crmentity2.crmid AND crmentity2.deleted = 0 AND crmentity2.setype = ?
					LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid";

        $query .= Users_Privileges_Model::getNonAdminAccessControlQuery('Calendar');

        $query .= " WHERE vtiger_crmentity.deleted=0
					AND (vtiger_activity.activitytype NOT IN ('Emails'))
					AND (vtiger_activity.status is NULL OR vtiger_activity.status NOT IN ('Completed', 'Deferred'))
					AND (vtiger_activity.eventstatus is NULL OR vtiger_activity.eventstatus NOT IN ('Held'))";

        if (!$currentUser->isAdminUser()) {
            $moduleFocus = CRMEntity::getInstance('Calendar');
            $condition = $moduleFocus->buildWhereClauseConditionForCalendar();
            if ($condition) {
                $query .= ' AND ' . $condition;
            }
        }

        if ($recordId) {
            $query .= " AND vtiger_cntactivityrel.contactid = ?";
        } elseif ($mode === 'upcoming') {
            $query .= " AND CASE WHEN vtiger_activity.activitytype='Task' THEN due_date >= '$currentDate' ELSE CONCAT(due_date,' ',time_end) >= '$nowInDBFormat' END";
        } elseif ($mode === 'overdue') {
            $query .= " AND CASE WHEN vtiger_activity.activitytype='Task' THEN due_date < '$currentDate' ELSE CONCAT(due_date,' ',time_end) < '$nowInDBFormat' END";
        }

        $params = array($this->getName());
        if ($recordId) {
            array_push($params, $recordId);
        }

        if ($user != 'all' && $user != '') {
            $query .= " AND vtiger_crmentity.smownerid = ?";
            array_push($params, $user);
        }

        $query .= " ORDER BY date_start, time_start LIMIT " . $pagingModel->getStartIndex() . ", " . ($pagingModel->getPageLimit() + 1);

        $result = $db->pquery($query, $params);
        $numOfRows = $db->num_rows($result);

        $groupsIds = Vtiger_Util_Helper::getGroupsIdsForUsers($currentUser->getId());
        $activities = array();
        $recordsToUnset = array();
        for ($i = 0; $i < $numOfRows; $i++) {
            $newRow = $db->query_result_rowdata($result, $i);
            $model = Vtiger_Record_Model::getCleanInstance('Calendar');
            $ownerId = $newRow['smownerid'];
            $currentUser = Users_Record_Model::getCurrentUserModel();
            $visibleFields = array('activitytype', 'date_start', 'time_start', 'due_date', 'time_end', 'assigned_user_id', 'visibility', 'smownerid', 'crmid');
            $visibility = true;
            if (in_array($ownerId, $groupsIds)) {
                $visibility = false;
            } else if ($ownerId == $currentUser->getId()) {
                $visibility = false;
            }
            if (!$currentUser->isAdminUser() && $newRow['activitytype'] != 'Task' && $newRow['visibility'] == 'Private' && $ownerId && $visibility) {
                foreach ($newRow as $data => $value) {
                    if (in_array($data, $visibleFields) != -1) {
                        unset($newRow[$data]);
                    }
                }
                $newRow['subject'] = vtranslate('Busy', 'Events') . '*';
            }
            if ($newRow['activitytype'] == 'Task') {
                unset($newRow['visibility']);

                $due_date = $newRow["due_date"];
                $dayEndTime = "23:59:59";
                $EndDateTime = Vtiger_Datetime_UIType::getDBDateTimeValue($due_date . " " . $dayEndTime);
                $dueDateTimeInDbFormat = explode(' ', $EndDateTime);
                $dueTimeInDbFormat = $dueDateTimeInDbFormat[1];
                $newRow['time_end'] = $dueTimeInDbFormat;
            }

            $model->setData($newRow);
            $model->setId($newRow['crmid']);
            $activities[$newRow['crmid']] = $model;
            if (!$currentUser->isAdminUser() && $newRow['activitytype'] == 'Task' && isToDoPermittedBySharing($newRow['crmid']) == 'no') {
                $recordsToUnset[] = $newRow['crmid'];
            }
        }

        $pagingModel->calculatePageRange($activities);
        if ($numOfRows > $pagingModel->getPageLimit()) {
            array_pop($activities);
            $pagingModel->set('nextPageExists', true);
        } else {
            $pagingModel->set('nextPageExists', false);
        }
        //after setting paging model, unsetting the records which has no permissions
        foreach ($recordsToUnset as $record) {
            unset($activities[$record]);
        }
        return $activities;
    }

    /**
     * Function returns query for module record's search
     * @param <String> $searchValue - part of record name (label column of crmentity table)
     * @param <Integer> $parentId - parent record id
     * @param <String> $parentModule - parent module name
     * @return <String> - query
     */
    function getSearchRecordsQuery($searchValue, $searchFields, $parentId = false, $parentModule = false) {
        if ($parentId && $parentModule == 'Accounts') {
            $query = "SELECT " . implode(',', $searchFields) . " FROM vtiger_crmentity
						INNER JOIN vtiger_contactdetails ON vtiger_contactdetails.contactid = vtiger_crmentity.crmid
						WHERE deleted = 0 AND vtiger_contactdetails.accountid = $parentId AND label like '%$searchValue%'";
            return $query;
        } else if ($parentId && $parentModule == 'Potentials') {
            $query = "SELECT " . implode(',', $searchFields) . " FROM vtiger_crmentity
						INNER JOIN vtiger_contactdetails ON vtiger_contactdetails.contactid = vtiger_crmentity.crmid
						LEFT JOIN vtiger_contpotentialrel ON vtiger_contpotentialrel.contactid = vtiger_contactdetails.contactid
						LEFT JOIN vtiger_potential ON vtiger_potential.contact_id = vtiger_contactdetails.contactid
						WHERE deleted = 0 AND (vtiger_contpotentialrel.potentialid = $parentId OR vtiger_potential.potentialid = $parentId)
						AND label like '%$searchValue%'";

            return $query;
        } else if ($parentId && $parentModule == 'HelpDesk') {
            $query = "SELECT " . implode(',', $searchFields) . " FROM vtiger_crmentity
                        INNER JOIN vtiger_contactdetails ON vtiger_contactdetails.contactid = vtiger_crmentity.crmid
                        INNER JOIN vtiger_troubletickets ON vtiger_troubletickets.contact_id = vtiger_contactdetails.contactid
                        WHERE deleted=0 AND vtiger_troubletickets.ticketid  = $parentId  AND label like '%$searchValue%'";

            return $query;
        } else if ($parentId && $parentModule == 'Campaigns') {
            $query = "SELECT " . implode(',', $searchFields) . " FROM vtiger_crmentity
                        INNER JOIN vtiger_contactdetails ON vtiger_contactdetails.contactid = vtiger_crmentity.crmid
                        INNER JOIN vtiger_campaigncontrel ON vtiger_campaigncontrel.contactid = vtiger_contactdetails.contactid
                        WHERE deleted=0 AND vtiger_campaigncontrel.campaignid = $parentId AND label like '%$searchValue%'";

            return $query;
        } else if ($parentId && $parentModule == 'Vendors') {
            $query = "SELECT " . implode(',', $searchFields) . " FROM vtiger_crmentity
                        INNER JOIN vtiger_contactdetails ON vtiger_contactdetails.contactid = vtiger_crmentity.crmid
                        INNER JOIN vtiger_vendorcontactrel ON vtiger_vendorcontactrel.contactid = vtiger_contactdetails.contactid
                        WHERE deleted=0 AND vtiger_vendorcontactrel.vendorid = $parentId AND label like '%$searchValue%'";

            return $query;
        } else if ($parentId && $parentModule == 'Quotes') {
            $query = "SELECT " . implode(',', $searchFields) . " FROM vtiger_crmentity
                        INNER JOIN vtiger_contactdetails ON vtiger_contactdetails.contactid = vtiger_crmentity.crmid
                        INNER JOIN vtiger_quotes ON vtiger_quotes.contactid = vtiger_contactdetails.contactid
                        WHERE deleted=0 AND vtiger_quotes.quoteid  = $parentId  AND label like '%$searchValue%'";

            return $query;
        } else if ($parentId && $parentModule == 'PurchaseOrder') {
            $query = "SELECT " . implode(',', $searchFields) . " FROM vtiger_crmentity
                        INNER JOIN vtiger_contactdetails ON vtiger_contactdetails.contactid = vtiger_crmentity.crmid
                        INNER JOIN vtiger_purchaseorder ON vtiger_purchaseorder.contactid = vtiger_contactdetails.contactid
                        WHERE deleted=0 AND vtiger_purchaseorder.purchaseorderid  = $parentId  AND label like '%$searchValue%'";

            return $query;
        } else if ($parentId && $parentModule == 'SalesOrder') {
            $query = "SELECT " . implode(',', $searchFields) . " FROM vtiger_crmentity
                        INNER JOIN vtiger_contactdetails ON vtiger_contactdetails.contactid = vtiger_crmentity.crmid
                        INNER JOIN vtiger_salesorder ON vtiger_salesorder.contactid = vtiger_contactdetails.contactid
                        WHERE deleted=0 AND vtiger_salesorder.salesorderid  = $parentId  AND label like '%$searchValue%'";

            return $query;
        } else if ($parentId && $parentModule == 'Invoice') {
            $query = "SELECT " . implode(',', $searchFields) . " FROM vtiger_crmentity
                        INNER JOIN vtiger_contactdetails ON vtiger_contactdetails.contactid = vtiger_crmentity.crmid
                        INNER JOIN vtiger_invoice ON vtiger_invoice.contactid = vtiger_contactdetails.contactid
                        WHERE deleted=0 AND vtiger_invoice.invoiceid  = $parentId  AND label like '%$searchValue%'";

            return $query;
        }

        return parent::getSearchRecordsQuery($searchValue, $searchFields, $parentId, $parentModule);
    }

    /**
     * Function to get relation query for particular module with function name
     * @param <record> $recordId
     * @param <String> $functionName
     * @param Vtiger_Module_Model $relatedModule
     * @return <String>
     */
    public function getRelationQuery($recordId, $functionName, $relatedModule, $relationId) {
        if ($functionName === 'get_activities') {
            $userNameSql = getSqlForNameInDisplayFormat(array('first_name' => 'vtiger_users.first_name', 'last_name' => 'vtiger_users.last_name'), 'Users');

            $query = "SELECT CASE WHEN (vtiger_users.user_name not like '') THEN $userNameSql ELSE vtiger_groups.groupname END AS user_name,
						vtiger_cntactivityrel.contactid, vtiger_seactivityrel.crmid AS parent_id,
						vtiger_crmentity.*, vtiger_activity.activitytype, vtiger_activity.subject, vtiger_activity.date_start, vtiger_activity.time_start,
						vtiger_activity.recurringtype, vtiger_activity.due_date, vtiger_activity.time_end, vtiger_activity.visibility,
						CASE WHEN (vtiger_activity.activitytype = 'Task') THEN (vtiger_activity.status) ELSE (vtiger_activity.eventstatus) END AS status
						FROM vtiger_activity
						INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_activity.activityid
						INNER JOIN vtiger_cntactivityrel ON vtiger_cntactivityrel.activityid = vtiger_activity.activityid
						LEFT JOIN vtiger_seactivityrel ON vtiger_seactivityrel.activityid = vtiger_activity.activityid
						LEFT JOIN vtiger_users ON vtiger_users.id = vtiger_crmentity.smownerid
						LEFT JOIN vtiger_groups ON vtiger_groups.groupid = vtiger_crmentity.smownerid
							WHERE vtiger_cntactivityrel.contactid = " . $recordId . " AND vtiger_crmentity.deleted = 0
								AND vtiger_activity.activitytype <> 'Emails'";

            $relatedModuleName = $relatedModule->getName();
            $query .= $this->getSpecificRelationQuery($relatedModuleName);
            $nonAdminQuery = $this->getNonAdminAccessControlQueryForRelation($relatedModuleName);
            if ($nonAdminQuery) {
                $query = appendFromClauseToQuery($query, $nonAdminQuery);

                if (trim($nonAdminQuery)) {
                    $relModuleFocus = CRMEntity::getInstance($relatedModuleName);
                    $condition = $relModuleFocus->buildWhereClauseConditionForCalendar();
                    if ($condition) {
                        $query .= ' AND ' . $condition;
                    }
                }
            }
        } else {
            $query = parent::getRelationQuery($recordId, $functionName, $relatedModule, $relationId);
        }

        return $query;
    }

    /**
     * Function to get list view query for popup window
     * @param <String> $sourceModule Parent module
     * @param <String> $field parent fieldname
     * @param <Integer> $record parent id
     * @param <String> $listQuery
     * @return <String> Listview Query
     */
    public function getQueryByModuleField($sourceModule, $field, $record, $listQuery) {
        if (in_array($sourceModule, array('Campaigns', 'Potentials', 'Vendors', 'Products', 'Services', 'Emails')) || ($sourceModule === 'Contacts' && $field === 'contact_id' && $record)) {
            switch ($sourceModule) {
                case 'Campaigns' : $tableName = 'vtiger_campaigncontrel';
                    $fieldName = 'contactid';
                    $relatedFieldName = 'campaignid';
                    break;
                case 'Potentials' : $tableName = 'vtiger_contpotentialrel';
                    $fieldName = 'contactid';
                    $relatedFieldName = 'potentialid';
                    break;
                case 'Vendors' : $tableName = 'vtiger_vendorcontactrel';
                    $fieldName = 'contactid';
                    $relatedFieldName = 'vendorid';
                    break;
                case 'Products' : $tableName = 'vtiger_seproductsrel';
                    $fieldName = 'crmid';
                    $relatedFieldName = 'productid';
                    break;
            }

            if ($sourceModule === 'Services') {
                $condition = " vtiger_contactdetails.contactid NOT IN (SELECT relcrmid FROM vtiger_crmentityrel WHERE crmid = '$record' UNION SELECT crmid FROM vtiger_crmentityrel WHERE relcrmid = '$record') ";
            } elseif ($sourceModule === 'Emails') {
                $condition = ' vtiger_contactdetails.emailoptout = 0';
            } elseif ($sourceModule === 'Contacts' && $field === 'contact_id') {
                $condition = " vtiger_contactdetails.contactid != '$record'";
            } else {
                $condition = " vtiger_contactdetails.contactid NOT IN (SELECT $fieldName FROM $tableName WHERE $relatedFieldName = '$record')";
            }

            $position = stripos($listQuery, 'where');
            if ($position) {
                $split = preg_split('/where/i', $listQuery);
                $overRideQuery = $split[0] . ' WHERE ' . $split[1] . ' AND ' . $condition;
            } else {
                $overRideQuery = $listQuery . ' WHERE ' . $condition;
            }
            return $overRideQuery;
        }
    }

    public function getDefaultSearchField() {
        return "lastname";
    }

    /* uni_cnfsecrm - modif 97 - DEBUT */

//    public function getApprenantSansSession() {
//        $db = PearDatabase::getInstance();
//        $query = "SELECT DISTINCT(vtiger_histoapprabsents.idapprenant), vtiger_contactdetails.contactid , contact_no, concat(firstname,' ',lastname) as name,email,phone,vtiger_contactscf.rappeler 
//                   from vtiger_histoapprabsents 
//                   inner join vtiger_contactdetails on vtiger_contactdetails.contactid = vtiger_histoapprabsents.idapprenant 
//                   inner JOIN vtiger_contactscf on vtiger_contactscf.contactid = vtiger_histoapprabsents.idapprenant 
//                   where vtiger_contactscf.rappeler = 0";
//        $result = $db->pquery($query);
//        $line_nbr = $db->num_rows($result);
//        $listAprnSansSession = array();
//        for ($i = 0; $i < $line_nbr; $i++) {
//            $listAprnSansSession[$i]['contactid'] = $db->query_result($result, $i, 'contactid');
//            $listAprnSansSession[$i]['contact_no'] = $db->query_result($result, $i, 'contact_no');
//            $listAprnSansSession[$i]['name'] = $db->query_result($result, $i, 'name');
//            $listAprnSansSession[$i]['email'] = $db->query_result($result, $i, 'email');
//            $listAprnSansSession[$i]['phone'] = $db->query_result($result, $i, 'phone');
//        }
//        return $listAprnSansSession;
//    }

    /* uni_cnfsecrm - modif 97 - FIN */


    /* uni_cnfsecrm - modif 101 - DEBUT */
    /* uni_cnfsecrm - v2 - modif 117 - DEBUT */
    /* uni_cnfsecrm - v2 - modif 121 - DEBUT */
    public function getApprenantRecyclage($filter) {
        //var_dump($filter);
        $db = PearDatabase::getInstance();
        $query = "select vtiger_apprenant_recyclage.apprenantid,vtiger_contactdetails.contact_no,
            contactid, concat(firstname,' ',lastname) as name,email,vtiger_contactdetails.phone,
            vtiger_activity.subject,activityid, vtiger_account.email1 
            from vtiger_apprenant_recyclage 
            inner join vtiger_contactdetails on vtiger_apprenant_recyclage.apprenantid = vtiger_contactdetails.contactid 
            inner JOIN vtiger_activity on vtiger_apprenant_recyclage.sessionid = vtiger_activity.activityid 
            INNER JOIN vtiger_account on vtiger_account.accountid = vtiger_contactdetails.accountid";
        // var_dump($query);
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
        // var_dump($query);
        $result = $db->pquery($query, $params);
        $line_nbr = $db->num_rows($result);
        $listAprnSansSession = array();
        for ($i = 0; $i < $line_nbr; $i++) {
            $listAprnSansSession[$i]['contactid'] = $db->query_result($result, $i, 'contactid');
            $listAprnSansSession[$i]['contact_no'] = $db->query_result($result, $i, 'contact_no');
            $listAprnSansSession[$i]['name'] = $db->query_result($result, $i, 'name');
            if ($db->query_result($result, $i, 'email') == '') {
                $listAprnSansSession[$i]['email'] = $db->query_result($result, $i, 'email1');
            } else {
                $listAprnSansSession[$i]['email'] = $db->query_result($result, $i, 'email');
            }

            $listAprnSansSession[$i]['phone'] = $db->query_result($result, $i, 'phone');
            $listAprnSansSession[$i]['activityid'] = $db->query_result($result, $i, 'activityid');
            $listAprnSansSession[$i]['subject'] = $db->query_result($result, $i, 'subject');
        }
        /* uni_cnfsecrm - v2 - modif 114 - DEBUT */
        $listAprnSansSession['nbreAppr'] = $line_nbr;
        /* uni_cnfsecrm - v2 - modif 114 - FIN */
        return $listAprnSansSession;
    }

    /* uni_cnfsecrm - v2 - modif 121 - FIN */
    /* uni_cnfsecrm - v2 - modif 117 - FIN */
    /* uni_cnfsecrm - modif 101 - FIN */

    /* uni_cnfsecrm - v2 - modif 107 - DEBUT */
    /* uni_cnfsecrm - v2 - modif 117 - DEBUT */
    /* uni_cnfsecrm - v2 - modif 121 - DEBUT */

    public function getApprenantSansSession($filter) {
        $db = PearDatabase::getInstance();
        $query = "SELECT vtiger_app_sanssession.id_apprenant, vtiger_contactdetails.contactid , 
            contact_no, concat(firstname,' ',lastname) as name,email,vtiger_contactdetails.phone, 
            vtiger_account.email1
                   from vtiger_app_sanssession 
                   inner join vtiger_contactdetails on vtiger_contactdetails.contactid = vtiger_app_sanssession.id_apprenant 
                   inner JOIN vtiger_contactscf on vtiger_contactscf.contactid = vtiger_app_sanssession.id_apprenant
                   INNER JOIN vtiger_account on vtiger_account.accountid = vtiger_contactdetails.accountid ";

        if (($filter == 1) || ($filter == '')) {
            $query .= ' where vtiger_app_sanssession.rappeler = ? and vtiger_app_sanssession.neplusrappeler = ?';
            $params = array(0, 0);
        } else if ($filter == 2) {
            $query .= ' where vtiger_app_sanssession.rappeler = ? and vtiger_app_sanssession.neplusrappeler = ?';
            $params = array(1, 0);
        } else if ($filter == 3) {
            $query .= ' where vtiger_app_sanssession.neplusrappeler = ?';
            $params = array(1);
        }
        $result = $db->pquery($query, $params);
        $line_nbr = $db->num_rows($result);
        $listAprnSansSession = array();
        for ($i = 0; $i < $line_nbr; $i++) {
            $listAprnSansSession[$i]['contactid'] = $db->query_result($result, $i, 'contactid');
            $listAprnSansSession[$i]['contact_no'] = $db->query_result($result, $i, 'contact_no');
            $listAprnSansSession[$i]['name'] = $db->query_result($result, $i, 'name');
            if ($db->query_result($result, $i, 'email') == '') {
                $listAprnSansSession[$i]['email'] = $db->query_result($result, $i, 'email1');
            } else {
                $listAprnSansSession[$i]['email'] = $db->query_result($result, $i, 'email');
            }

            $listAprnSansSession[$i]['phone'] = $db->query_result($result, $i, 'phone');
        }
        /* uni_cnfsecrm - v2 - modif 114 - DEBUT */
        $listAprnSansSession['nbreAppr'] = $line_nbr;
        /* uni_cnfsecrm - v2 - modif 114 - FIN */
        return $listAprnSansSession;
    }

    /* uni_cnfsecrm - v2 - modif 121 - FIN */

    /* uni_cnfsecrm - v2 - modif 117 - FIN */
    /* uni_cnfsecrm - v2 - modif 107 - FIN */

    /* uni_cnfsecrm - v2 - modif 120 - DEBUT */

    public function getProspects($filter) {
        $db = PearDatabase::getInstance();
        $query = "select vtiger_account.accountid,accountname,account_no,email1,phone,vtiger_quotes.subject,quoteid
            from vtiger_suivi_prospects         
            inner join vtiger_account on vtiger_account.accountid = vtiger_suivi_prospects.idclient
            inner JOIN vtiger_quotes on vtiger_quotes.quoteid = vtiger_suivi_prospects.iddevis";
        // var_dump($query);
        if (($filter == 1) || ($filter == '')) {
            $query .= ' where vtiger_suivi_prospects.rappeler = ? and vtiger_suivi_prospects.nePlusRappeler = ?';
            $params = array(0, 0);
        } else if ($filter == 2) {
            $query .= ' where vtiger_suivi_prospects.rappeler = ? and vtiger_suivi_prospects.nePlusRappeler = ?';
            $params = array(1, 0);
        } else if ($filter == 3) {
            $query .= ' where vtiger_suivi_prospects.nePlusRappeler = ?';
            $params = array(1);
        }
        // var_dump($query);
        $result = $db->pquery($query, $params);
        $line_nbr = $db->num_rows($result);
        $listProspects = array();
        for ($i = 0; $i < $line_nbr; $i++) {
            $listProspects[$i]['accountid'] = $db->query_result($result, $i, 'accountid');
            $listProspects[$i]['account_no'] = $db->query_result($result, $i, 'account_no');
            $listProspects[$i]['name'] = $db->query_result($result, $i, 'accountname');
            $listProspects[$i]['email'] = $db->query_result($result, $i, 'email1');
            $listProspects[$i]['phone'] = $db->query_result($result, $i, 'phone');
            $listProspects[$i]['quoteid'] = $db->query_result($result, $i, 'quoteid');
            $listProspects[$i]['subject'] = html_entity_decode($db->query_result($result, $i, 'subject'));
        }
        $listProspects['nbreAppr'] = $line_nbr;
        return $listProspects;
    }

    /* uni_cnfsecrm - v2 - modif 120 - FIN */
}
