<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */
vimport('~~include/utils/RecurringType.php');

class Calendar_Record_Model extends Vtiger_Record_Model {

    /**
     * Function returns the Entity Name of Record Model
     * @return <String>
     */
    function getName() {
        $name = $this->get('subject');
        if (empty($name)) {
            $name = parent::getName();
        }
        return $name;
    }

    /**
     * Function to insert details about reminder in to Database
     * @param <Date> $reminderSent
     * @param <integer> $recurId
     * @param <String> $reminderMode like edit/delete
     */
    public function setActivityReminder($reminderSent = 0, $recurId = '', $reminderMode = '') {
        $moduleInstance = CRMEntity::getInstance($this->getModuleName());
        $moduleInstance->activity_reminder($this->getId(), $this->get('reminder_time'), $reminderSent, $recurId, $reminderMode);
    }

    /**
     * Function returns the Module Name based on the activity type
     * @return <String>
     */
    function getType() {
        $activityType = $this->get('activitytype');
        if ($activityType == 'Task') {
            return 'Calendar';
        }
        return 'Events';
    }

    /**
     * Function to get the Detail View url for the record
     * @return <String> - Record Detail View Url
     */
    public function getDetailViewUrl() {
        $module = $this->getModule();
        return 'index.php?module=Calendar&view=' . $module->getDetailViewName() . '&record=' . $this->getId();
    }

    /**
     * Function returns recurring information for EditView
     * @return <Array> - which contains recurring Information
     */
    public function getRecurrenceInformation($request = false) {
        $recurringObject = $this->getRecurringObject();
        if ($request && !$request->get('id') && $request->get('repeat_frequency')) {
            $recurringObject = getrecurringObjValue();
        }
        if ($recurringObject) {
            $recurringData['recurringcheck'] = 'Yes';
            $recurringData['repeat_frequency'] = $recurringObject->getRecurringFrequency();
            $recurringData['eventrecurringtype'] = $recurringObject->getRecurringType();
            $recurringEndDate = $recurringObject->getRecurringEndDate();
            if (!empty($recurringEndDate)) {
                $recurringData['recurringenddate'] = $recurringEndDate->get_formatted_date();
            }
            $recurringInfo = $recurringObject->getUserRecurringInfo();
            if ($recurringObject->getRecurringType() == 'Weekly') {
                $noOfDays = count($recurringInfo['dayofweek_to_repeat']);
                for ($i = 0; $i < $noOfDays; ++$i) {
                    $recurringData['week' . $recurringInfo['dayofweek_to_repeat'][$i]] = 'checked';
                }
            } elseif ($recurringObject->getRecurringType() == 'Monthly') {
                $recurringData['repeatMonth'] = $recurringInfo['repeatmonth_type'];
                if ($recurringInfo['repeatmonth_type'] == 'date') {
                    $recurringData['repeatMonth_date'] = $recurringInfo['repeatmonth_date'];
                } else {
                    $recurringData['repeatMonth_daytype'] = $recurringInfo['repeatmonth_daytype'];
                    $recurringData['repeatMonth_day'] = $recurringInfo['dayofweek_to_repeat'][0];
                }
            }
        } else {
            $recurringData['recurringcheck'] = 'No';
        }
        return $recurringData;
    }

    function save() {
        //Time should changed to 24hrs format
        $_REQUEST['time_start'] = Vtiger_Time_UIType::getTimeValueWithSeconds($_REQUEST['time_start']);
        $_REQUEST['time_end'] = Vtiger_Time_UIType::getTimeValueWithSeconds($_REQUEST['time_end']);
        parent::save();
    }

    /**
     * Function to delete the current Record Model
     */
    public function delete() {
        $adb = PearDatabase::getInstance();
        $recurringEditMode = $this->get('recurringEditMode');
        $deletedRecords = array();
        if (!empty($recurringEditMode) && $recurringEditMode != 'current') {
            $recurringRecordsList = $this->getRecurringRecordsList();
            foreach ($recurringRecordsList as $parent => $childs) {
                $parentRecurringId = $parent;
                $childRecords = $childs;
            }
            if ($recurringEditMode == 'future') {
                $parentKey = array_keys($childRecords, $this->getId());
                $childRecords = array_slice($childRecords, $parentKey[0]);
            }
            foreach ($childRecords as $record) {
                $recordModel = $this->getInstanceById($record, $this->getModuleName());
                $adb->pquery("DELETE FROM vtiger_activity_recurring_info WHERE activityid=? AND recurrenceid=?", array($parentRecurringId, $record));
                $recordModel->getModule()->deleteRecord($recordModel);
                $deletedRecords[] = $record;
            }
        } else {
            if ($recurringEditMode == 'current') {
                $parentRecurringId = $this->getParentRecurringRecord();
                $adb->pquery("DELETE FROM vtiger_activity_recurring_info WHERE activityid=? AND recurrenceid=?", array($parentRecurringId, $this->getId()));
            }
            $this->getModule()->deleteRecord($this);
            $deletedRecords[] = $this->getId();
        }
        return $deletedRecords;
    }

    /**
     * Function to get recurring information for the current record in detail view
     * @return <Array> - which contains Recurring Information
     */
    public function getRecurringDetails() {
        $recurringObject = $this->getRecurringObject();
        if ($recurringObject) {
            $recurringInfoDisplayData = $recurringObject->getDisplayRecurringInfo();
            $recurringEndDate = $recurringObject->getRecurringEndDate();
        } else {
            $recurringInfoDisplayData['recurringcheck'] = vtranslate('LBL_NO', $currentModule);
            $recurringInfoDisplayData['repeat_str'] = '';
        }
        if (!empty($recurringEndDate)) {
            $recurringInfoDisplayData['recurringenddate'] = $recurringEndDate->get_formatted_date();
        }
        return $recurringInfoDisplayData;
    }

    /**
     * Function to get the recurring object
     * @return Object - recurring object
     */
    public function getRecurringObject() {
        $db = PearDatabase::getInstance();
        $query = 'SELECT vtiger_recurringevents.*, vtiger_activity.date_start, vtiger_activity.time_start, vtiger_activity.due_date, vtiger_activity.time_end FROM vtiger_recurringevents
					INNER JOIN vtiger_activity ON vtiger_activity.activityid = vtiger_recurringevents.activityid
					WHERE vtiger_recurringevents.activityid = ?';
        $result = $db->pquery($query, array($this->getId()));
        if ($db->num_rows($result)) {
            return RecurringType::fromDBRequest($db->query_result_rowdata($result, 0));
        }
        return false;
    }

    /**
     * Function updates the Calendar Reminder popup's status
     */
    public function updateReminderStatus($status = 1) {
        $db = PearDatabase::getInstance();
        $db->pquery("UPDATE vtiger_activity_reminder_popup set status = ? where recordid = ?", array($status, $this->getId()));
    }

    /* uni_cnfsecrm - reminder recyclage */
    /**
     * Function updates the Calendar Reminder popup's status
     */
    /* public function updateReminderRecyclageStatus($status = 1) {
      $db = PearDatabase::getInstance();
      $db->pquery("UPDATE vtiger_rappel_recyclage set status = ? where sessionid = ?", array($status, $this->getId()));
      } */

    /**
     * Function to get parent recurring event Id
     */
    public function getParentRecurringRecord() {
        $adb = PearDatabase::getInstance();
        $recordId = $this->getId();
        $result = $adb->pquery("SELECT * FROM vtiger_activity_recurring_info WHERE activityid=? OR activityid = (SELECT activityid FROM vtiger_activity_recurring_info WHERE recurrenceid=?) LIMIT 1", array($recordId, $recordId));
        $parentRecurringId = $adb->query_result($result, 0, "activityid");
        return $parentRecurringId;
    }

    /**
     * Function to get recurring records list
     */
    public function getRecurringRecordsList() {
        $adb = PearDatabase::getInstance();
        $recurringRecordsList = array();
        $recordId = $this->getId();
        $result = $adb->pquery("SELECT * FROM vtiger_activity_recurring_info WHERE activityid=? OR activityid = (SELECT activityid FROM vtiger_activity_recurring_info WHERE recurrenceid=?)", array($recordId, $recordId));
        $noofrows = $adb->num_rows($result);
        $parentRecurringId = $adb->query_result($result, 0, "activityid");
        $childRecords = array();
        for ($i = 0; $i < $noofrows; $i++) {
            $childRecords[] = $adb->query_result($result, $i, "recurrenceid");
        }
        $recurringRecordsList[$parentRecurringId] = $childRecords;
        return $recurringRecordsList;
    }

    /**
     * Function to get recurring enabled for record
     */
    public function isRecurringEnabled() {
        $recurringInfo = $this->getRecurringDetails();
        if ($recurringInfo['recurringcheck'] == 'Yes') {
            return true;
        }
        return false;
    }

    /**
     * Function to get URL for Export the record as PDF
     * @return <type>
     */
    public function getExportPDFUrl() {
        return "index.php?module=" . $this->getModuleName() . "&action=ExportPDF&record=" . $this->getId();
    }

    /**
     * Function to get the send email pdf url
     * @return <string>
     */
    public function getSendEmailPDFUrl() {
        return 'module=' . $this->getModuleName() . '&view=SendEmail&mode=composeMailData&record=' . $this->getId();
    }

    /* uni_cnfsecrm */

    function getSessionFinanceurs() {
        $listFinanceurs = getSessionAssociatedFinanceurs($this->getModuleName(), $this->getEntity());
        $financeursCount = count($listFinanceurs);
        for ($i = 1; $i <= $financeursCount; $i++) {
            $relatedSessionFinanceurs[$i]['vendorid' . $i] = $listFinanceurs[$i]['vendorid'];
            $relatedSessionFinanceurs[$i]['montant' . $i] = $listFinanceurs[$i]['montant'];
            $relatedSessionFinanceurs[$i]['vendorname' . $i] = $listFinanceurs[$i]['vendorname'];
            $relatedSessionFinanceurs[$i]['tva' . $i] = $listFinanceurs[$i]['tva'];
            $relatedSessionFinanceurs[$i]['ttc' . $i] = $listFinanceurs[$i]['ttc'];
            $relatedSessionFinanceurs[$i]['street' . $i] = $listFinanceurs[$i]['street'];
            $relatedSessionFinanceurs[$i]['city' . $i] = $listFinanceurs[$i]['city'];
            $relatedSessionFinanceurs[$i]['postalcode' . $i] = $listFinanceurs[$i]['postalcode'];
            $relatedSessionFinanceurs[$i]['phone' . $i] = $listFinanceurs[$i]['phone'];
            $relatedSessionFinanceurs[$i]['vendor_no' . $i] = $listFinanceurs[$i]['vendor_no'];
        }
//        $monfichier = fopen('debug_financeur_detail.txt', 'a+');
//        fputs($monfichier, "\n" . 'related financeur '.$relatedFinanceurs);
//        fclose($monfichier);
        return $relatedSessionFinanceurs;
    }

    /* uni_cnfsecrm - modif 104 - DEBUT  */

    /* uni_cnfsecrm - v2 - modif 176 - DEBUT */
    function getSessionApprenants() {
        $listApprenants = getSessionAssociatedApprenants($this->getModuleName(), $this->getEntity());
        $apprenantsCount = count($listApprenants);
        for ($i = 1; $i <= $apprenantsCount; $i++) {
            $relatedApprenants[$i]['contactName' . $i] = $listApprenants[$i]['contactname'];
            $relatedApprenants[$i]['resultat' . $i] = $listApprenants[$i]['resultat'];
            $relatedApprenants[$i]['ticket_examen' . $i] = $listApprenants[$i]['ticket_examen'];
            $relatedApprenants[$i]['ticket_examen_test' . $i] = $listApprenants[$i]['ticket_examen_test'];
            $relatedApprenants[$i]['inscrit' . $i] = $listApprenants[$i]['inscrit'];
            $relatedApprenants[$i]['etat' . $i] = $listApprenants[$i]['etat'];
            $relatedApprenants[$i]['apprenantid' . $i] = $listApprenants[$i]['apprenantid'];
            $relatedApprenants[$i]['email' . $i] = $listApprenants[$i]['email'];
            $relatedApprenants[$i]['telephone' . $i] = $listApprenants[$i]['phone'];
            $relatedApprenants[$i]['numclient' . $i] = $listApprenants[$i]['numclient'];
            $relatedApprenants[$i]['nomclient' . $i] = $listApprenants[$i]['nomclient'];
            $relatedApprenants[$i]['accountid' . $i] = $listApprenants[$i]['accountid'];

            /* unicnfsecrm_022020_13 */
            $relatedApprenants[$i]['statutfacture' . $i] = $listApprenants[$i]['statutfacture'];
            //unicnfsecrm_mod_56
            $relatedApprenants[$i]['type_tokens' . $i] = $listApprenants[$i]['type_tokens'];
            $relatedApprenants[$i]['type_tokens_test' . $i] = $listApprenants[$i]['type_tokens_test'];
            $relatedApprenants[$i]['token' . $i] = $listApprenants[$i]['token'];
            /* uni_cnfsecrm - v2 - modif 142 - DEBUT */
            $relatedApprenants[$i]['type_tokens_reaffecter' . $i] = $listApprenants[$i]['type_tokens_reaffecter'];
            $relatedApprenants[$i]['ticket_examen_reaffecter' . $i] = $listApprenants[$i]['ticket_examen_reaffecter'];
            /* uni_cnfsecrm - v2 - modif 142 - FIN */

            /* // uni_cnfsecrm - modif 104 correction ancien travail */
            $relatedApprenants[$i]['testprerequis' . $i] = $listApprenants[$i]['testprerequis'];
            $relatedApprenants[$i]['electricien' . $i] = $listApprenants[$i]['electricien'];
            $relatedApprenants[$i]['initiale' . $i] = $listApprenants[$i]['initiale'];
            $relatedApprenants[$i]['recyclage' . $i] = $listApprenants[$i]['recyclage'];
            /* fin modif ancien */
            /* modif */
            $relatedApprenants[$i]['b0_h0_h0v_b0' . $i] = $listApprenants[$i]['b0_h0_h0v_b0'];
            $relatedApprenants[$i]['b0_h0_h0v_h0v' . $i] = $listApprenants[$i]['b0_h0_h0v_h0v'];
            $relatedApprenants[$i]['bs_be_he_b0' . $i] = $listApprenants[$i]['bs_be_he_b0'];
            $relatedApprenants[$i]['bs_be_he_h0v' . $i] = $listApprenants[$i]['bs_be_he_h0v'];
            $relatedApprenants[$i]['bs_be_he_bs' . $i] = $listApprenants[$i]['bs_be_he_bs'];
            $relatedApprenants[$i]['bs_be_he_manoeuvre' . $i] = $listApprenants[$i]['bs_be_he_manoeuvre'];
            $relatedApprenants[$i]['b1v_b2v_bc_br_b0' . $i] = $listApprenants[$i]['b1v_b2v_bc_br_b0'];
            $relatedApprenants[$i]['b1v_b2v_bc_br_h0v' . $i] = $listApprenants[$i]['b1v_b2v_bc_br_h0v'];
            $relatedApprenants[$i]['b1v_b2v_bc_br_bs' . $i] = $listApprenants[$i]['b1v_b2v_bc_br_bs'];
            $relatedApprenants[$i]['b1v_b2v_bc_br_manoeuvre' . $i] = $listApprenants[$i]['b1v_b2v_bc_br_manoeuvre'];
            $relatedApprenants[$i]['b1v_b2v_bc_br_b1v' . $i] = $listApprenants[$i]['b1v_b2v_bc_br_b1v'];
            $relatedApprenants[$i]['b1v_b2v_bc_br_b2v' . $i] = $listApprenants[$i]['b1v_b2v_bc_br_b2v'];
            $relatedApprenants[$i]['b1v_b2v_bc_br_bc' . $i] = $listApprenants[$i]['b1v_b2v_bc_br_bc'];
            $relatedApprenants[$i]['b1v_b2v_bc_br_br' . $i] = $listApprenants[$i]['b1v_b2v_bc_br_br'];
            $relatedApprenants[$i]['b1v_b2v_bc_br_essai' . $i] = $listApprenants[$i]['b1v_b2v_bc_br_essai'];
            $relatedApprenants[$i]['b1v_b2v_bc_br_verification' . $i] = $listApprenants[$i]['b1v_b2v_bc_br_verification'];
            $relatedApprenants[$i]['b1v_b2v_bc_br_mesurage' . $i] = $listApprenants[$i]['b1v_b2v_bc_br_mesurage'];
            $relatedApprenants[$i]['b1v_b2v_bc_br_h1v_h2v_b0' . $i] = $listApprenants[$i]['b1v_b2v_bc_br_h1v_h2v_b0'];
            $relatedApprenants[$i]['b1v_b2v_bc_br_h1v_h2v_h0v' . $i] = $listApprenants[$i]['b1v_b2v_bc_br_h1v_h2v_h0v'];
            $relatedApprenants[$i]['b1v_b2v_bc_br_h1v_h2v_bs' . $i] = $listApprenants[$i]['b1v_b2v_bc_br_h1v_h2v_bs'];
            $relatedApprenants[$i]['b1v_b2v_bc_br_h1v_h2v_manoeuvre' . $i] = $listApprenants[$i]['b1v_b2v_bc_br_h1v_h2v_manoeuvre'];
            $relatedApprenants[$i]['b1v_b2v_bc_br_h1v_h2v_b1v' . $i] = $listApprenants[$i]['b1v_b2v_bc_br_h1v_h2v_b1v'];
            $relatedApprenants[$i]['b1v_b2v_bc_br_h1v_h2v_b2v' . $i] = $listApprenants[$i]['b1v_b2v_bc_br_h1v_h2v_b2v'];
            $relatedApprenants[$i]['b1v_b2v_bc_br_h1v_h2v_bc' . $i] = $listApprenants[$i]['b1v_b2v_bc_br_h1v_h2v_bc'];
            $relatedApprenants[$i]['b1v_b2v_bc_br_h1v_h2v_br' . $i] = $listApprenants[$i]['b1v_b2v_bc_br_h1v_h2v_br'];
            $relatedApprenants[$i]['b1v_b2v_bc_br_h1v_h2v_essai' . $i] = $listApprenants[$i]['b1v_b2v_bc_br_h1v_h2v_essai'];
            $relatedApprenants[$i]['b1v_b2v_bc_br_h1v_h2v_verification' . $i] = $listApprenants[$i]['b1v_b2v_bc_br_h1v_h2v_verification'];
            $relatedApprenants[$i]['b1v_b2v_bc_br_h1v_h2v_mesurage' . $i] = $listApprenants[$i]['b1v_b2v_bc_br_h1v_h2v_mesurage'];
            $relatedApprenants[$i]['b1v_b2v_bc_br_h1v_h2v_h1v' . $i] = $listApprenants[$i]['b1v_b2v_bc_br_h1v_h2v_h1v'];
            $relatedApprenants[$i]['b1v_b2v_bc_br_h1v_h2v_h2v' . $i] = $listApprenants[$i]['b1v_b2v_bc_br_h1v_h2v_h2v'];
            $relatedApprenants[$i]['b1v_b2v_bc_br_h1v_h2v_hc' . $i] = $listApprenants[$i]['b1v_b2v_bc_br_h1v_h2v_hc'];
            /* uni_cnfsecrm - v2 - modif 115 - DEBUT */
            $relatedApprenants[$i]['bs_be_he_he' . $i] = $listApprenants[$i]['bs_be_he_he'];
            $relatedApprenants[$i]['b1v_b2v_bc_br_he' . $i] = $listApprenants[$i]['b1v_b2v_bc_br_he'];
            $relatedApprenants[$i]['b1v_b2v_bc_br_h1v_h2v_he' . $i] = $listApprenants[$i]['b1v_b2v_bc_br_h1v_h2v_he'];
            /* uni_cnfsecrm - v2 - modif 115 - FIN */
            /* uni_cnfsecrm - v2 - modif 127 - DEBUT */
            $relatedApprenants[$i]['date_start_appr' . $i] = $listApprenants[$i]['date_start_appr'];
            $relatedApprenants[$i]['date_fin_appr' . $i] = $listApprenants[$i]['date_fin_appr'];
            $relatedApprenants[$i]['duree_jour' . $i] = $listApprenants[$i]['duree_jour'];
            $relatedApprenants[$i]['duree_heure' . $i] = $listApprenants[$i]['duree_heure'];
            $relatedApprenants[$i]['direction' . $i] = $listApprenants[$i]['direction'];
            
            /* uni_cnfsecrm - v2 - modif 127 - FIN */
            /* fin modif */
        }
        //var_dump($relatedApprenants);die();
        return $relatedApprenants;
    }
    /* uni_cnfsecrm - v2 - modif 176 - FIN */

    /* uni_cnfsecrm - modif 104 - FIN  */

    function getSessionDates() {
        $relatedDates = array();
        $listDates = getSessionAssociatedDates($this->getModuleName(), $this->getEntity());
        $datescount = count($listDates);
        for ($i = 1; $i <= $datescount; $i++) {
            $relatedDates[$i]['sequence_no' . $i] = $listDates[$i]['sequence_no' . $i];
            $relatedDates[$i]['date_start' . $i] = $listDates[$i]['date_start' . $i];
            $relatedDates[$i]['start_matin' . $i] = $listDates[$i]['start_matin' . $i];
            $relatedDates[$i]['end_matin' . $i] = $listDates[$i]['end_matin' . $i];
            $relatedDates[$i]['start_apresmidi' . $i] = $listDates[$i]['start_apresmidi' . $i];
            $relatedDates[$i]['end_apresmidi' . $i] = $listDates[$i]['end_apresmidi' . $i];
            $relatedDates[$i]['duree_formation' . $i] = $listDates[$i]['duree_formation' . $i];
        }
        return $relatedDates;
    }

    function getNomformation() {
        $nom_formation = getNomformation($this->getModuleName(), $this->getEntity());
        return $nom_formation;
    }

    function getCategorieformation() {
        $servicecategory = getCategorieformation($this->getModuleName(), $this->getEntity());
        return $servicecategory;
    }

    /* uni_cnfsecrm - v2 - modif 176 - DEBUT */
    function getListDirectionSession() {
        $listDirection = getListDirection($this->getModuleName(), $this->getEntity());
        return $listDirection;
    }
    /* uni_cnfsecrm - v2 - modif 176 - FIN */
}
