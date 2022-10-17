<?php

/* +**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ********************************************************************************** */

class Invoice_RecordQuickPreview_View extends Vtiger_RecordQuickPreview_View {

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

            // $this->assignNavigationRecordIds($viewer, $recordId); /* comment after modif */
            //unicnfsecrm_mod_15 ( quickView )                   
            $filterTab = $request->get('filtertab');
            if ($filterTab != '' || $filterTab != null) {
                $this->assignNavigationRecordIdsFilter($viewer, $recordId, $filterTab);
            } else {
                $this->assignNavigationRecordIds($viewer, $recordId);
            }
            //fin unicnfsecrm_mod_15 ( quickView )
        }

        $recordModel = $this->record->getRecord();
        $recordStrucure = Vtiger_RecordStructure_Model::getInstanceFromRecordModel($recordModel, Vtiger_RecordStructure_Model::RECORD_STRUCTURE_MODE_SUMMARY);
        $moduleModel = $recordModel->getModule();

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
        /* uni_cnfsecrm - modif 103 - DEBUT */
        $rappel = $this->getRappel($recordId);
        $viewer->assign('RAPPEL', $rappel); /*  Envoyer l'id de l'historique Impayé */
        /* uni_cnfsecrm - modif 103 - FIN */
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

        //unicnfsecrm_gestimpaye_01 : commentaires
        //record id
        $viewer->assign('RECORDID', $recordId);
        $detailViewLinkParams = array('MODULE' => $moduleName, 'RECORD' => $recordId);
        $detailViewLinks = $this->record->getDetailViewLinks($detailViewLinkParams);
        $viewer->assign('DETAILVIEW_LINKS', $detailViewLinks);
        //fin unicnfsecrm_gestimpaye_01 : commentaires
        // unicnfsecrm_gestimpaye_02 : envoi mail relance
        $adb = PearDatabase::getInstance();
        $sql = "SELECT vtiger_account.email1,vtiger_account.accountid,vtiger_account.accountname 
                FROM vtiger_account
                INNER JOIN vtiger_invoice on vtiger_account.accountid = vtiger_invoice.accountid 
                where vtiger_invoice.invoiceid = ?";
        $params = array($recordId);
        $result = $adb->pquery($sql, $params);

        $accountid = $adb->query_result($result, 0, 'accountid');
        $email = $adb->query_result($result, 0, 'email1');
        $accountname = $adb->query_result($result, 0, 'accountname');

        $toMailNamesList = '{"' . $accountid . '":[{"label":"' . $accountname . '","value":"' . $email . '"}]}';
        $viewer->assign('TOMAILNAMESLIST', $toMailNamesList);

        $TOMAIL_INFO = array($accountid => array($email));
        $viewer->assign('TOMAIL_INFO', $TOMAIL_INFO);

        $viewer->assign('EMAIL', $email);

        //test attachement
        $inventoryRecordId = $recordId;
        $module = "Invoice";
        $recordModel = Vtiger_Record_Model::getInstanceById($inventoryRecordId, $module);
        $pdfFileName = $recordModel->getPDFFileName();
        $fileComponents = explode('/', $pdfFileName);
        $fileName = $fileComponents[count($fileComponents) - 1];
        array_pop($fileComponents);

        //test documment attacher 
        //chercher convention id

        $sql1 = "SELECT vtiger_activity.activityid 
            FROM vtiger_activity 
            INNER JOIN vtiger_salesorder on vtiger_salesorder.session = vtiger_activity.activityid
            INNER JOIN vtiger_invoice on vtiger_invoice.salesorderid = vtiger_salesorder.salesorderid
            where vtiger_invoice.invoiceid = ?";
        $params1 = array($recordId);
        $result1 = $adb->pquery($sql1, $params1);

        $monfichier = fopen('correct_debug.txt', 'a+');
        fputs($monfichier, "\n" . ' factureid ' . $recordId);
        fclose($monfichier);
        $activityid = $adb->query_result($result1, 0, 'activityid');
        //chercher la relation avec document 

        $documents = array();
        $monfichier = fopen('correct_debug.txt', 'a+');
        fputs($monfichier, "\n" . ' activityid ' . $activityid);
        fclose($monfichier);
        $sql2 = "SELECT vtiger_senotesrel.notesid,filename
                FROM vtiger_senotesrel
                INNER JOIN vtiger_notes on vtiger_notes.notesid = vtiger_senotesrel.notesid
                INNER JOIN vtiger_crmentity on vtiger_crmentity.crmid = vtiger_senotesrel.notesid
                WHERE vtiger_senotesrel.crmid = ? and deleted = 0 ";
        $params2 = array($activityid);
        $result2 = $adb->pquery($sql2, $params2);
        $num_rows = $adb->num_rows($result2);
        for ($i = 0; $i < $num_rows; $i++) {
            $notesid = $adb->query_result($result2, $i, 'notesid');
            $nom = $adb->query_result($result2, $i, 'filename');

            $monfichier = fopen('correct_debug.txt', 'a+');
            fputs($monfichier, "\n" . ' notesid ' . $notesid);
            fputs($monfichier, "\n" . ' nom ' . $nom);
            fclose($monfichier);
            if (strstr($nom, "Feuille_Emargement.pdf")) {
                $attachmentsid = $notesid + 1;
            } else {
                $attachmentsid = "";
            }
        }

        $monfichier = fopen('correct_debug.txt', 'a+');
        fputs($monfichier, "\n" . ' attachmentsid ' . $attachmentsid);
        fclose($monfichier);

        if ($attachmentsid != '') {
            $sql4 = "SELECT attachmentsid,path FROM vtiger_attachments WHERE vtiger_attachments.attachmentsid = ? ";
            $params4 = array($attachmentsid);
            $result4 = $adb->pquery($sql4, $params4);
            $path = $adb->query_result($result4, 0, 'path');
        } else {
            $path = '';
        }

        $monfichier = fopen('correct_debug.txt', 'a+');
        fputs($monfichier, "\n" . ' path ' . $path);
        fputs($monfichier, "\n" . ' f1 ' . $path . $attachmentsid . '_' . $activityid . '_Feuille_Emargement.pdf');
        fputs($monfichier, "\n" . ' pdfFileName ' . $pdfFileName);
        fclose($monfichier);

        $attachmentDetails = array(
            array(
                'attachment' => $fileName,
                'path' => implode('/', $fileComponents),
                'size' => filesize($pdfFileName),
                'type' => 'pdf',
                'nondeletable' => true
            ),
            array(
                'attachment' => $attachmentsid . '_' . $activityid . '_Feuille_Emargement.pdf',
                'path' => $path,
                'size' => filesize($path . $attachmentsid . '_' . $activityid . '_Feuille_Emargement.pdf'),
                'type' => 'pdf',
                'nondeletable' => true
            )
        );
        //fin test attachement
        $viewer->assign('ATTACHMENTS', $attachmentDetails);
        //fin unicnfsecrm_gestimpaye_02 : envoi mail relance
        //unicnfsecrm_mod_08

        $sql5 = "SELECT vtiger_invoice.invoicedate,vtiger_invoice.duedate,vtiger_invoice.accountid,
                vtiger_account.phone ,vtiger_invoice.balance,cf_1185
          FROM vtiger_invoice
          INNER JOIN vtiger_invoicecf on vtiger_invoicecf.invoiceid = vtiger_invoice.invoiceid
          INNER JOIN vtiger_account on vtiger_account.accountid = vtiger_invoice.accountid
          WHERE vtiger_invoice.invoiceid = ?";
        $params5 = array($recordId);
        $result5 = $adb->pquery($sql5, $params5);
        $date_facture = $adb->query_result($result5, 0, 'invoicedate');
        $date_echeance = $adb->query_result($result5, 0, 'duedate');
        $telephone_client = $adb->query_result($result5, 0, 'phone');
        $balance_facture = $adb->query_result($result5, 0, 'balance');

        $date_facture = date("d-m-Y", strtotime($date_facture));
        $date_echeance = date("d-m-Y", strtotime($date_echeance));
        $balance_facture = number_format($balance_facture, 2, '.', '');
        $etat_echeance = $adb->query_result($result5, 0, 'cf_1185');

        $viewer->assign('DATE_FACTURE', $date_facture);
        $viewer->assign('DATE_ECHEANCE', $date_echeance);
        $viewer->assign('TELEPHONE_CLIENT', $telephone_client);
        $viewer->assign('BALENCE_FACTURE', $balance_facture);
        $viewer->assign('etat_echeance', $etat_echeance);

        //fin unicnfsecrm_mod_08        
    }

    public function validateRequest(Vtiger_Request $request) {
        $request->validateReadAccess();
    }

    //unicnfsecrm_mod_15 ( quickView )
    public function assignNavigationRecordIdsFilter($viewer, $recordId, $filterTab) {
        $adb = PearDatabase::getInstance();
        //Navigation to next and previous records.
        //$navigationInfo = ListViewSession::getListViewNavigation($recordId);

        $navigationInfos = array();
        if ($filterTab == '7jours') {
            $echeance = 'Dépassé de 7 jours';
        } else if ($filterTab == '14jours') {
            $echeance = 'Dépassé de 14 jours';
        } else if ($filterTab == '30jours') {
            $echeance = 'Dépassé de 30 jours';
        }

        $query = "SELECT vtiger_invoice.invoiceid
          FROM vtiger_invoice
          INNER JOIN vtiger_invoicecf on vtiger_invoicecf.invoiceid = vtiger_invoice.invoiceid
          where vtiger_invoicecf.cf_1185 = ? and vtiger_invoice.invoicestatus NOT IN (?,?) ";

        if ($echeance == 'Dépassé de 7 jours') {
            $query .= 'AND vtiger_invoicecf.cf_1189 = ?';
        } else if ($echeance == 'Dépassé de 14 jours') {
            $query .= 'AND vtiger_invoicecf.cf_1191 = ?';
        } else if ($echeance == 'Dépassé de 30 jours') {
            $query .= 'AND vtiger_invoicecf.cf_1193 = ?';
        }

        $params = array($echeance, 'Paid', 'Cancel', 0);
        $result = $adb->pquery($query, $params);
        $num_rows = $adb->num_rows($result);
        if ($num_rows) {
            for ($i = 0; $i < $num_rows; $i++) {
                $invoiceId = $adb->query_result($result, $i, 'invoiceid');
                $navigationInfos[$i] = $invoiceId;
            }
        }

        $navigationInfo = array();
        $navigationInfo[1] = $navigationInfos;

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

        //record id
        $viewer->assign('RECORDID', $recordId);


        $detailViewLinkParams = array('MODULE' => $moduleName, 'RECORD' => $recordId);
        $detailViewLinks = $this->record->getDetailViewLinks($detailViewLinkParams);
        $viewer->assign('DETAILVIEW_LINKS', $detailViewLinks);


        $sql = "SELECT vtiger_account.email1,vtiger_account.accountid,vtiger_account.accountname 
                FROM vtiger_account
                INNER JOIN vtiger_invoice on vtiger_account.accountid = vtiger_invoice.accountid 
                where vtiger_invoice.invoiceid = ?";
        $params = array($recordId);
        $result = $adb->pquery($sql, $params);

        $accountid = $adb->query_result($result, 0, 'accountid');
        $email = $adb->query_result($result, 0, 'email1');
        $accountname = $adb->query_result($result, 0, 'accountname');

        $toMailNamesList = '{"' . $accountid . '":[{"label":"' . $accountname . '","value":"' . $email . '"}]}';
        $viewer->assign('TOMAILNAMESLIST', $toMailNamesList);

        $TOMAIL_INFO = array($accountid => array($email));
        $viewer->assign('TOMAIL_INFO', $TOMAIL_INFO);

        $viewer->assign('EMAIL', $email);

        //test attachement
        $inventoryRecordId = $recordId;
        $module = "Invoice";
        $recordModel = Vtiger_Record_Model::getInstanceById($inventoryRecordId, $module);
        $pdfFileName = $recordModel->getPDFFileName();
        $fileComponents = explode('/', $pdfFileName);
        $fileName = $fileComponents[count($fileComponents) - 1];
        array_pop($fileComponents);

        //test documment attacher 
        //chercher convention id

        $sql1 = "SELECT vtiger_activity.activityid 
            FROM vtiger_activity 
            INNER JOIN vtiger_salesorder on vtiger_salesorder.session = vtiger_activity.activityid
            INNER JOIN vtiger_invoice on vtiger_invoice.salesorderid = vtiger_salesorder.salesorderid
            where vtiger_invoice.invoiceid = ?";
        $params1 = array($recordId);
        $result1 = $adb->pquery($sql1, $params1);

        $activityid = $adb->query_result($result1, 0, 'activityid');
        //chercher la relation avec document 

        $documents = array();
        $sql2 = "SELECT vtiger_senotesrel.notesid FROM vtiger_senotesrel WHERE vtiger_senotesrel.crmid = ? ";
        $params2 = array($activityid);
        $result2 = $adb->pquery($sql2, $params2);
        $num_rows = $adb->num_rows($result2);
        if ($num_rows) {
            for ($i = 0; $i < $num_rows; $i++) {
                $documentid = $adb->query_result($result2, $i, 'notesid');
                $documents[$i]['notesid'] = $documentid;
            }
        }
        foreach ($documents as $document) {
            $nomdocuments = array();
            $sql3 = "SELECT vtiger_notes.filename,vtiger_notes.notesid FROM vtiger_notes WHERE vtiger_notes.notesid = ? ";
            $params3 = array($document['notesid']);
            $result3 = $adb->pquery($sql3, $params3);
            $num_rows = $adb->num_rows($result3);
            if ($num_rows) {
                for ($i = 0; $i < $num_rows; $i++) {
                    $nom = $adb->query_result($result3, $i, 'filename');
                    $notesid = $adb->query_result($result3, $i, 'notesid');
                    if ($nom == "Feuille_Emargement.pdf") {
                        $notesid = $notesid + 1;
                    } else {
                        $notesid = "";
                    }
                }
            }
        }
        if ($notesid != '') {
            $sql4 = "SELECT attachmentsid,path FROM vtiger_attachments WHERE vtiger_attachments.attachmentsid = ? ";
            $params4 = array($notesid);
            $result4 = $adb->pquery($sql4, $params4);
            $path = $adb->query_result($result4, 0, 'path');
        } else {
            $path = '';
        }

        $attachmentDetails = array(
            array(
                'attachment' => $fileName,
                'path' => implode('/', $fileComponents),
                'size' => filesize($pdfFileName),
                'type' => 'pdf',
                'nondeletable' => true
            ),
            array(
                'attachment' => $notesid . '_Feuille_Emargement.pdf',
                'path' => $path,
                'size' => filesize($pdfFileName),
                'type' => 'pdf',
                'nondeletable' => true
            )
        );

        $viewer->assign('ATTACHMENTS', $attachmentDetails);
        $sql5 = "SELECT vtiger_invoice.invoicedate,vtiger_invoice.duedate,vtiger_invoice.accountid,vtiger_account.accountname,vtiger_account.phone 
            ,vtiger_invoice.balance ,cf_1185
          FROM vtiger_invoice
          INNER JOIN vtiger_invoicecf on vtiger_invoicecf.invoiceid = vtiger_invoice.invoiceid
          INNER JOIN vtiger_account on vtiger_account.accountid = vtiger_invoice.accountid
          WHERE vtiger_invoice.invoiceid = ?";
        $params5 = array($recordId);
        $result5 = $adb->pquery($sql5, $params5);
        $date_facture = $adb->query_result($result5, 0, 'invoicedate');
        $date_echeance = $adb->query_result($result5, 0, 'duedate');
        $nom_client = $adb->query_result($result5, 0, 'accountname');
        $telephone_client = $adb->query_result($result5, 0, 'phone');
        $balance_facture = $adb->query_result($result5, 0, 'balance');

        $date_facture = date("d-m-Y", strtotime($date_facture));
        $date_echeance = date("d-m-Y", strtotime($date_echeance));
        $balance_facture = number_format($balance_facture, 2, '.', '');
        $etat_echeance = $adb->query_result($result5, 0, 'cf_1185');

        $viewer->assign('DATE_FACTURE', $date_facture);
        $viewer->assign('DATE_ECHEANCE', $date_echeance);
        $viewer->assign('TELEPHONE_CLIENT', $telephone_client);
        $viewer->assign('BALENCE_FACTURE', $balance_facture);
        $viewer->assign('etat_echeance', $etat_echeance);
    }

//unicnfsecrm_mod_15 ( quickView )

    /* uni_cnfsecrm - modif 103 - DEBUT */
    public function getRappel($recordId) {
        $db = PearDatabase::getInstance();
        $query = "SELECT historyimpayesid FROM vtiger_invoicecf WHERE vtiger_invoicecf.invoiceid = $recordId";
        $result = $db->pquery($query);
        $rappel = $db->query_result($result, 0, 'historyimpayesid');
        return $rappel;
    }

    /* uni_cnfsecrm - modif 103 - FIN */
}
