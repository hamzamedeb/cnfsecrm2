<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class SalesOrder_RelationListView_Model extends Inventory_RelationListView_Model {
    /* uni_cnfsecrm */

    public function getEntries($pagingModel) {
        $db = PearDatabase::getInstance();
//        $instance = new SalesOrder_RelationListView_Model();                       
        //$parentRecordModel = Vtiger_Record_Model::getInstanceById($recordId, "SalesOrder");
        //$this->setParentRecordModel($parentRecordModel);

        if ($this->getRelationModel()) {
            $relationModule = $this->getRelationModel()->getRelationModuleModel();
            $relationModuleName = $relationModule->get('name');
        } else {
            $relationModuleName = "Calendar";
        }
        $parentModuleModel = $this->getParentRecordModel()->getModule();
        $relatedModuleModel = Vtiger_Module_Model::getInstance($relationModuleName);
        $this->setRelatedModuleModel($relatedModuleModel);
        $relationModel = Vtiger_Relation_Model::getInstance($parentModuleModel, $relatedModuleModel);
        $this->setRelationModel($relationModel);

        $relationModule = $this->getRelationModel()->getRelationModuleModel();
        $relationModuleName = $relationModule->get('name');
        $relatedColumnFields = $relationModule->getConfigureRelatedListFields();
        if (count($relatedColumnFields) <= 0) {
            $relatedColumnFields = $relationModule->getRelatedListFields();
        }

        if ($relationModuleName == 'Calendar') {
            //Adding visibility in the related list, showing records based on the visibility
            $relatedColumnFields['visibility'] = 'visibility';
        }

        if ($relationModuleName == 'PriceBooks') {
            //Adding fields in the related list
            $relatedColumnFields['unit_price'] = 'unit_price';
            $relatedColumnFields['listprice'] = 'listprice';
            $relatedColumnFields['currency_id'] = 'currency_id';
        }

        $query = $this->getRelationQuery();

        if ($this->get('whereCondition') && is_array($this->get('whereCondition'))) {
            $currentUser = Users_Record_Model::getCurrentUserModel();
            $queryGenerator = new QueryGenerator($relationModuleName, $currentUser);
            $queryGenerator->setFields(array_values($relatedColumnFields));
            $whereCondition = $this->get('whereCondition');
            foreach ($whereCondition as $fieldName => $fieldValue) {
                if (is_array($fieldValue)) {
                    $comparator = $fieldValue[1];
                    $searchValue = $fieldValue[2];
                    $type = $fieldValue[3];
                    if ($type == 'time') {
                        $searchValue = Vtiger_Time_UIType::getTimeValueWithSeconds($searchValue);
                    }
                    $queryGenerator->addCondition($fieldName, $searchValue, $comparator, "AND");
                }
            }
            $whereQuerySplit = split("WHERE", $queryGenerator->getWhereClause());
            $query .= " AND " . $whereQuerySplit[1];
        }

        $startIndex = $pagingModel->getStartIndex();
        $pageLimit = $pagingModel->getPageLimit();

        $orderBy = $this->getForSql('orderby');
        $sortOrder = $this->getForSql('sortorder');

        if ($orderBy) {

            $orderByFieldModuleModel = $relationModule->getFieldByColumn($orderBy);
            if ($orderByFieldModuleModel && $orderByFieldModuleModel->isReferenceField()) {
                //If reference field then we need to perform a join with crmentity with the related to field
                $queryComponents = $split = preg_split('/ where /i', $query);
                $selectAndFromClause = $queryComponents[0];
                $whereCondition = $queryComponents[1];
                $qualifiedOrderBy = 'vtiger_crmentity' . $orderByFieldModuleModel->get('column');
                $selectAndFromClause .= ' LEFT JOIN vtiger_crmentity AS ' . $qualifiedOrderBy . ' ON ' .
                        $orderByFieldModuleModel->get('table') . '.' . $orderByFieldModuleModel->get('column') . ' = ' .
                        $qualifiedOrderBy . '.crmid ';
                $query = $selectAndFromClause . ' WHERE ' . $whereCondition;
                $query .= ' ORDER BY ' . $qualifiedOrderBy . '.label ' . $sortOrder;
            } elseif ($orderByFieldModuleModel && $orderByFieldModuleModel->isOwnerField()) {
                $query .= ' ORDER BY COALESCE(CONCAT(vtiger_users.first_name,vtiger_users.last_name),vtiger_groups.groupname) ' . $sortOrder;
            } else {
                // Qualify the the column name with table to remove ambugity
                $qualifiedOrderBy = $orderBy;
                $orderByField = $relationModule->getFieldByColumn($orderBy);
                if ($orderByField) {
                    $qualifiedOrderBy = $relationModule->getOrderBySql($qualifiedOrderBy);
                }
                if ($qualifiedOrderBy == 'vtiger_activity.date_start' && ($relationModuleName == 'Calendar' || $relationModuleName == 'Emails')) {
                    $qualifiedOrderBy = "str_to_date(concat(vtiger_activity.date_start,vtiger_activity.time_start),'%Y-%m-%d %H:%i:%s')";
                }
                $query = "$query ORDER BY $qualifiedOrderBy $sortOrder";
            }
        } else if ($relationModuleName == 'HelpDesk' && empty($orderBy) && empty($sortOrder) && $moduleName != "Users") {
            $query .= ' ORDER BY vtiger_crmentity.modifiedtime DESC';
        }

        $limitQuery = $query . ' LIMIT ' . $startIndex . ',' . $pageLimit;
        $result = $db->pquery($limitQuery, array());
        $relatedRecordList = array();
        $currentUser = Users_Record_Model::getCurrentUserModel();
        $groupsIds = Vtiger_Util_Helper::getGroupsIdsForUsers($currentUser->getId());
        $recordsToUnset = array();
        for ($i = 0; $i < $db->num_rows($result); $i++) {
            $row = $db->fetch_row($result, $i);
            $newRow = array();
            foreach ($row as $col => $val) {
                if (array_key_exists($col, $relatedColumnFields)) {
                    $newRow[$relatedColumnFields[$col]] = $val;
                }
            }
            //To show the value of "Assigned to"
            $ownerId = $row['smownerid'];
            $newRow['assigned_user_id'] = $row['smownerid'];
            if ($relationModuleName == 'Calendar') {
                $visibleFields = array('activitytype', 'date_start', 'time_start', 'due_date', 'time_end', 'assigned_user_id', 'visibility', 'smownerid', 'parent_id');
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
                }
            }

            $record = Vtiger_Record_Model::getCleanInstance($relationModule->get('name'));
            $record->setData($newRow)->setModuleFromInstance($relationModule)->setRawData($row);
            $record->setId($row['crmid']);
            $relatedRecordList[$row['crmid']] = $record;
            if ($relationModuleName == 'Calendar' && !$currentUser->isAdminUser() && $newRow['activitytype'] == 'Task' && isToDoPermittedBySharing($row['crmid']) == 'no') {
                $recordsToUnset[] = $row['crmid'];
            }
        }
        $pagingModel->calculatePageRange($relatedRecordList);

        $nextLimitQuery = $query . ' LIMIT ' . ($startIndex + $pageLimit) . ' , 1';
        $nextPageLimitResult = $db->pquery($nextLimitQuery, array());
        if ($db->num_rows($nextPageLimitResult) > 0) {
            $pagingModel->set('nextPageExists', true);
        } else {
            $pagingModel->set('nextPageExists', false);
        }
        //setting related list view count before unsetting permission denied records - to make sure paging should not fail
        $pagingModel->set('_relatedlistcount', count($relatedRecordList));
        foreach ($recordsToUnset as $record) {
            unset($relatedRecordList[$record]);
        }

        return $relatedRecordList;
    }

    /** uni_cnfsecrm
     * Function to get Relation query
     * @return <String>
     */
    public function getRelationQuery() {
        $relationModel = $this->getRelationModel();
        if (!empty($relationModel) && $relationModel->get('name') != NULL) {
            $recordModel = $this->getParentRecordModel();
            $query = $relationModel->getQuery($recordModel);
            return $query;
        }

        $relatedModuleModel = $this->getRelatedModuleModel();
        $relatedModuleName = $relatedModuleModel->getName();

        $relatedModuleBaseTable = $relatedModuleModel->basetable;
        $relatedModuleEntityIdField = $relatedModuleModel->basetableid;

        $parentModuleModel = $relationModel->getParentModuleModel();
        $parentModuleBaseTable = $parentModuleModel->basetable;
        $parentModuleEntityIdField = $parentModuleModel->basetableid;
        $parentRecordId = $this->getParentRecordModel()->getId();
        $parentModuleDirectRelatedField = $parentModuleModel->get('directRelatedFieldName');

        $relatedModuleFields = array_keys($this->getHeaders());
        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        $queryGenerator = new QueryGenerator($relatedModuleName, $currentUserModel);
        $queryGenerator->setFields($relatedModuleFields);

        $query = $queryGenerator->getQuery();

        $queryComponents = preg_split('/ FROM /i', $query);
        $query = $queryComponents[0] . ' ,vtiger_crmentity.crmid FROM ' . $queryComponents[1];

        $whereSplitQueryComponents = preg_split('/ WHERE /i', $query);
        $joinQuery = ' INNER JOIN ' . $parentModuleBaseTable . ' ON ' . $parentModuleBaseTable . '.' . $parentModuleDirectRelatedField . " = " . $relatedModuleBaseTable . '.' . $relatedModuleEntityIdField;

        $query = "$whereSplitQueryComponents[0] $joinQuery WHERE $parentModuleBaseTable.$parentModuleEntityIdField = $parentRecordId AND $whereSplitQueryComponents[1]";

        return $query;
    }

}

?>