<?php

/* +**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ********************************************************************************** */

class Contacts_Popup2_View extends Vtiger_Popup_View {

    protected $listViewEntries = false;
    protected $listViewHeaders = false;

    function checkPermission(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        $currentUserPrivilegesModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        if (!$currentUserPrivilegesModel->hasModulePermission($moduleModel->getId())) {
            throw new AppException(vtranslate($moduleName, $moduleName) . ' ' . vtranslate('LBL_NOT_ACCESSIBLE'));
        }
    }

    /**
     * Function returns the module name for which the popup should be initialized
     * @param Vtiger_request $request
     * @return <String>
     */
    function getModule(Vtiger_request $request) {
        $moduleName = $request->getModule();
        return $moduleName;
    }

    function getRecord(Vtiger_request $request) {
        $record = $request->get('record');
        return $record;
    }

    function getApprenantid(Vtiger_request $request) {
        $apprenantid = $request->get('apprenantid');
        return $apprenantid;
    }

    function getRowNum(Vtiger_request $request) {
        $row_num = $request->get('row_num');
        return $row_num;
    }

    function process(Vtiger_Request $request) {
        $viewer = $this->getViewer($request);
        $moduleName = $this->getModule($request);
        $record = $this->getRecord($request);
        $apprenantid = $this->getApprenantid($request);
        $row_num = $this->getRowNum($request);
        $companyDetails = Vtiger_CompanyDetails_Model::getInstanceById();
        $companyLogo = $companyDetails->getLogo();
        $this->initializeListViewContents($request, $viewer);
        $viewer->assign('COMPANY_LOGO', $companyLogo);
        $listApprenants = getOptionsApprenants($moduleName, $record, $apprenantid);
        /* uni_cnfsecrm - v2 - modif 134 - DEBUT */
        $detailSession = getDetailSession($record);
        /* uni_cnfsecrm - v2 - modif 134 - FIN */
        $viewer->assign('row_num', $row_num);
        $viewer->assign('be_essai', $listApprenants[0]['be_essai']);
        $viewer->assign('be_mesurage', $listApprenants[0]['be_mesurage']);
        $viewer->assign('be_verification', $listApprenants[0]['be_verification']);
        $viewer->assign('be_manoeuvre', $listApprenants[0]['be_manoeuvre']);
        $viewer->assign('he_essai', $listApprenants[0]['he_essai']);
        $viewer->assign('he_mesurage', $listApprenants[0]['he_mesurage']);
        $viewer->assign('he_verification', $listApprenants[0]['he_verification']);
        $viewer->assign('he_manoeuvre', $listApprenants[0]['he_manoeuvre']);
        $viewer->assign('initiale', $listApprenants[0]['initiale']);
        $viewer->assign('recyclage', $listApprenants[0]['recyclage']);
        $viewer->assign('testprerequis', $listApprenants[0]['testprerequis']);
        $viewer->assign('electricien', $listApprenants[0]['electricien']);
        $viewer->assign('resultat', $listApprenants[0]['resultat']);
        /* uni_cnfsecrm - modif 104 - DEBUT */
        /* uni_cnfsecrm - correction 104 - DEBUT */
        if (is_null($listApprenants[0]['b0_h0_h0v_b0'])) {
            $b0_h0_h0v_b0 = 1;
        } else {
            $b0_h0_h0v_b0 = $listApprenants[0]['b0_h0_h0v_b0'];
        }
        $viewer->assign('b0_h0_h0v_b0', $b0_h0_h0v_b0);

        if (is_null($listApprenants[0]['b0_h0_h0v_h0v'])) {
            $b0_h0_h0v_h0v = 1;
        } else {
            $b0_h0_h0v_h0v = $listApprenants[0]['b0_h0_h0v_h0v'];
        }
        $viewer->assign('b0_h0_h0v_h0v', $b0_h0_h0v_h0v);

        if (is_null($listApprenants[0]['bs_be_he_b0'])) {
            $bs_be_he_b0 = 1;
        } else {
            $bs_be_he_b0 = $listApprenants[0]['bs_be_he_b0'];
        }
        $viewer->assign('bs_be_he_b0', $bs_be_he_b0);

        if (is_null($listApprenants[0]['bs_be_he_h0v'])) {
            $bs_be_he_h0v = 1;
        } else {
            $bs_be_he_h0v = $listApprenants[0]['bs_be_he_h0v'];
        }
        $viewer->assign('bs_be_he_h0v', $bs_be_he_h0v);

        if (is_null($listApprenants[0]['bs_be_he_bs'])) {
            $bs_be_he_bs = 1;
        } else {
            $bs_be_he_bs = $listApprenants[0]['bs_be_he_bs'];
        }
        $viewer->assign('bs_be_he_bs', $bs_be_he_bs);

        if (is_null($listApprenants[0]['bs_be_he_manoeuvre'])) {
            $bs_be_he_manoeuvre = 0;
        } else {
            $bs_be_he_manoeuvre = $listApprenants[0]['bs_be_he_manoeuvre'];
        }
        $viewer->assign('bs_be_he_manoeuvre', $bs_be_he_manoeuvre);

        if (is_null($listApprenants[0]['b1v_b2v_bc_br_b0'])) {
            $b1v_b2v_bc_br_b0 = 1;
        } else {
            $b1v_b2v_bc_br_b0 = $listApprenants[0]['b1v_b2v_bc_br_b0'];
        }
        $viewer->assign('b1v_b2v_bc_br_b0', $b1v_b2v_bc_br_b0);

        if (is_null($listApprenants[0]['b1v_b2v_bc_br_h0v'])) {
            $b1v_b2v_bc_br_h0v = 1;
        } else {
            $b1v_b2v_bc_br_h0v = $listApprenants[0]['b1v_b2v_bc_br_h0v'];
        }
        $viewer->assign('b1v_b2v_bc_br_h0v', $b1v_b2v_bc_br_h0v);

        if (is_null($listApprenants[0]['b1v_b2v_bc_br_bs'])) {
            $b1v_b2v_bc_br_bs = 1;
        } else {
            $b1v_b2v_bc_br_bs = $listApprenants[0]['b1v_b2v_bc_br_bs'];
        }
        $viewer->assign('b1v_b2v_bc_br_bs', $b1v_b2v_bc_br_bs);

        if (is_null($listApprenants[0]['b1v_b2v_bc_br_manoeuvre'])) {
            $b1v_b2v_bc_br_manoeuvre = 0;
        } else {
            $b1v_b2v_bc_br_manoeuvre = $listApprenants[0]['b1v_b2v_bc_br_manoeuvre'];
        }
        $viewer->assign('b1v_b2v_bc_br_manoeuvre', $b1v_b2v_bc_br_manoeuvre);

        if (is_null($listApprenants[0]['b1v_b2v_bc_br_b1v'])) {
            $b1v_b2v_bc_br_b1v = 1;
        } else {
            $b1v_b2v_bc_br_b1v = $listApprenants[0]['b1v_b2v_bc_br_b1v'];
        }
        $viewer->assign('b1v_b2v_bc_br_b1v', $b1v_b2v_bc_br_b1v);

        if (is_null($listApprenants[0]['b1v_b2v_bc_br_b2v'])) {
            $b1v_b2v_bc_br_b2v = 1;
        } else {
            $b1v_b2v_bc_br_b2v = $listApprenants[0]['b1v_b2v_bc_br_b2v'];
        }
        $viewer->assign('b1v_b2v_bc_br_b2v', $b1v_b2v_bc_br_b2v);

        if (is_null($listApprenants[0]['b1v_b2v_bc_br_bc'])) {
            $b1v_b2v_bc_br_bc = 1;
        } else {
            $b1v_b2v_bc_br_bc = $listApprenants[0]['b1v_b2v_bc_br_bc'];
        }
        $viewer->assign('b1v_b2v_bc_br_bc', $b1v_b2v_bc_br_bc);

        if (is_null($listApprenants[0]['b1v_b2v_bc_br_br'])) {
            $b1v_b2v_bc_br_br = 1;
        } else {
            $b1v_b2v_bc_br_br = $listApprenants[0]['b1v_b2v_bc_br_br'];
        }
        $viewer->assign('b1v_b2v_bc_br_br', $b1v_b2v_bc_br_br);

        if (is_null($listApprenants[0]['b1v_b2v_bc_br_essai'])) {
            $b1v_b2v_bc_br_essai = 0;
        } else {
            $b1v_b2v_bc_br_essai = $listApprenants[0]['b1v_b2v_bc_br_essai'];
        }
        $viewer->assign('b1v_b2v_bc_br_essai', $b1v_b2v_bc_br_essai);

        if (is_null($listApprenants[0]['b1v_b2v_bc_br_verification'])) {
            $b1v_b2v_bc_br_verification = 0;
        } else {
            $b1v_b2v_bc_br_verification = $listApprenants[0]['b1v_b2v_bc_br_verification'];
        }
        $viewer->assign('b1v_b2v_bc_br_verification', $b1v_b2v_bc_br_verification);

        if (is_null($listApprenants[0]['b1v_b2v_bc_br_mesurage'])) {
            $b1v_b2v_bc_br_mesurage = 0;
        } else {
            $b1v_b2v_bc_br_mesurage = $listApprenants[0]['b1v_b2v_bc_br_mesurage'];
        }
        $viewer->assign('b1v_b2v_bc_br_mesurage', $b1v_b2v_bc_br_mesurage);

        if (is_null($listApprenants[0]['b1v_b2v_bc_br_h1v_h2v_b0'])) {
            $b1v_b2v_bc_br_h1v_h2v_b0 = 1;
        } else {
            $b1v_b2v_bc_br_h1v_h2v_b0 = $listApprenants[0]['b1v_b2v_bc_br_h1v_h2v_b0'];
        }
        $viewer->assign('b1v_b2v_bc_br_h1v_h2v_b0', $b1v_b2v_bc_br_h1v_h2v_b0);

        if (is_null($listApprenants[0]['b1v_b2v_bc_br_h1v_h2v_h0v'])) {
            $b1v_b2v_bc_br_h1v_h2v_h0v = 1;
        } else {
            $b1v_b2v_bc_br_h1v_h2v_h0v = $listApprenants[0]['b1v_b2v_bc_br_h1v_h2v_h0v'];
        }
        $viewer->assign('b1v_b2v_bc_br_h1v_h2v_h0v', $b1v_b2v_bc_br_h1v_h2v_h0v);

        if (is_null($listApprenants[0]['b1v_b2v_bc_br_h1v_h2v_bs'])) {
            $b1v_b2v_bc_br_h1v_h2v_bs = 1;
        } else {
            $b1v_b2v_bc_br_h1v_h2v_bs = $listApprenants[0]['b1v_b2v_bc_br_h1v_h2v_bs'];
        }
        $viewer->assign('b1v_b2v_bc_br_h1v_h2v_bs', $b1v_b2v_bc_br_h1v_h2v_bs);

        if (is_null($listApprenants[0]['b1v_b2v_bc_br_h1v_h2v_manoeuvre'])) {
            $b1v_b2v_bc_br_h1v_h2v_manoeuvre = 0;
        } else {
            $b1v_b2v_bc_br_h1v_h2v_manoeuvre = $listApprenants[0]['b1v_b2v_bc_br_h1v_h2v_manoeuvre'];
        }
        $viewer->assign('b1v_b2v_bc_br_h1v_h2v_manoeuvre', $b1v_b2v_bc_br_h1v_h2v_manoeuvre);

        if (is_null($listApprenants[0]['b1v_b2v_bc_br_h1v_h2v_b1v'])) {
            $b1v_b2v_bc_br_h1v_h2v_b1v = 1;
        } else {
            $b1v_b2v_bc_br_h1v_h2v_b1v = $listApprenants[0]['b1v_b2v_bc_br_h1v_h2v_b1v'];
        }
        $viewer->assign('b1v_b2v_bc_br_h1v_h2v_b1v', $b1v_b2v_bc_br_h1v_h2v_b1v);

        if (is_null($listApprenants[0]['b1v_b2v_bc_br_h1v_h2v_b2v'])) {
            $b1v_b2v_bc_br_h1v_h2v_b2v = 1;
        } else {
            $b1v_b2v_bc_br_h1v_h2v_b2v = $listApprenants[0]['b1v_b2v_bc_br_h1v_h2v_b2v'];
        }
        $viewer->assign('b1v_b2v_bc_br_h1v_h2v_b2v', $b1v_b2v_bc_br_h1v_h2v_b2v);

        if (is_null($listApprenants[0]['b1v_b2v_bc_br_h1v_h2v_bc'])) {
            $b1v_b2v_bc_br_h1v_h2v_bc = 1;
        } else {
            $b1v_b2v_bc_br_h1v_h2v_bc = $listApprenants[0]['b1v_b2v_bc_br_h1v_h2v_bc'];
        }
        $viewer->assign('b1v_b2v_bc_br_h1v_h2v_bc', $b1v_b2v_bc_br_h1v_h2v_bc);

        if (is_null($listApprenants[0]['b1v_b2v_bc_br_h1v_h2v_br'])) {
            $b1v_b2v_bc_br_h1v_h2v_br = 1;
        } else {
            $b1v_b2v_bc_br_h1v_h2v_br = $listApprenants[0]['b1v_b2v_bc_br_h1v_h2v_br'];
        }
        $viewer->assign('b1v_b2v_bc_br_h1v_h2v_br', $b1v_b2v_bc_br_h1v_h2v_br);

        if (is_null($listApprenants[0]['b1v_b2v_bc_br_h1v_h2v_essai'])) {
            $b1v_b2v_bc_br_h1v_h2v_essai = 0;
        } else {
            $b1v_b2v_bc_br_h1v_h2v_essai = $listApprenants[0]['b1v_b2v_bc_br_h1v_h2v_essai'];
        }
        $viewer->assign('b1v_b2v_bc_br_h1v_h2v_essai', $b1v_b2v_bc_br_h1v_h2v_essai);


        if (is_null($listApprenants[0]['b1v_b2v_bc_br_h1v_h2v_verification'])) {
            $b1v_b2v_bc_br_h1v_h2v_verification = 0;
        } else {
            $b1v_b2v_bc_br_h1v_h2v_verification = $listApprenants[0]['b1v_b2v_bc_br_h1v_h2v_verification'];
        }
        $viewer->assign('b1v_b2v_bc_br_h1v_h2v_verification', $b1v_b2v_bc_br_h1v_h2v_verification);

        if (is_null($listApprenants[0]['b1v_b2v_bc_br_h1v_h2v_mesurage'])) {
            $b1v_b2v_bc_br_h1v_h2v_mesurage = 0;
        } else {
            $b1v_b2v_bc_br_h1v_h2v_mesurage = $listApprenants[0]['b1v_b2v_bc_br_h1v_h2v_mesurage'];
        }
        $viewer->assign('b1v_b2v_bc_br_h1v_h2v_mesurage', $b1v_b2v_bc_br_h1v_h2v_mesurage);

        if (is_null($listApprenants[0]['b1v_b2v_bc_br_h1v_h2v_h1v'])) {
            $b1v_b2v_bc_br_h1v_h2v_h1v = 1;
        } else {
            $b1v_b2v_bc_br_h1v_h2v_h1v = $listApprenants[0]['b1v_b2v_bc_br_h1v_h2v_h1v'];
        }
        $viewer->assign('b1v_b2v_bc_br_h1v_h2v_h1v', $b1v_b2v_bc_br_h1v_h2v_h1v);

        if (is_null($listApprenants[0]['b1v_b2v_bc_br_h1v_h2v_h2v'])) {
            $b1v_b2v_bc_br_h1v_h2v_h2v = 1;
        } else {
            $b1v_b2v_bc_br_h1v_h2v_h2v = $listApprenants[0]['b1v_b2v_bc_br_h1v_h2v_h2v'];
        }
        $viewer->assign('b1v_b2v_bc_br_h1v_h2v_h2v', $b1v_b2v_bc_br_h1v_h2v_h2v);

        if (is_null($listApprenants[0]['b1v_b2v_bc_br_h1v_h2v_hc'])) {
            $b1v_b2v_bc_br_h1v_h2v_hc = 1;
        } else {
            $b1v_b2v_bc_br_h1v_h2v_hc = $listApprenants[0]['b1v_b2v_bc_br_h1v_h2v_hc'];
        }
        $viewer->assign('b1v_b2v_bc_br_h1v_h2v_hc', $b1v_b2v_bc_br_h1v_h2v_hc);
        
        /* uni_cnfsecrm - v2 - modif 115 - DEBUT */
        
        if (is_null($listApprenants[0]['bs_be_he_he'])) {
            $bs_be_he_he = 0;
        } else {
            $bs_be_he_he = $listApprenants[0]['bs_be_he_he'];
        }
        $viewer->assign('bs_be_he_he', $bs_be_he_he);
        
        if (is_null($listApprenants[0]['b1v_b2v_bc_br_he'])) {
            $b1v_b2v_bc_br_he = 0;
        } else {
            $b1v_b2v_bc_br_he = $listApprenants[0]['b1v_b2v_bc_br_he'];
        }
        $viewer->assign('b1v_b2v_bc_br_he', $b1v_b2v_bc_br_he);
        if (is_null($listApprenants[0]['b1v_b2v_bc_br_h1v_h2v_he'])) {
            $b1v_b2v_bc_br_h1v_h2v_he = 0;
        } else {
            $b1v_b2v_bc_br_h1v_h2v_he = $listApprenants[0]['b1v_b2v_bc_br_h1v_h2v_he'];
        }
        $viewer->assign('b1v_b2v_bc_br_h1v_h2v_he', $b1v_b2v_bc_br_h1v_h2v_he);
        
        /* uni_cnfsecrm - v2 - modif 115 - FIN */ 
        
        /* uni_cnfsecrm - correction 104 - FIN */
        /* uni_cnfsecrm - modif 104 - FIN */

        /* uni_cnfsecrm - v2 - modif 109 - DEBUT */
        $viewer->assign('TYPE_FORMATION', $this->getTypeFormation($record));
        /* uni_cnfsecrm - v2 - modif 109 - FIN */
        /* uni_cnfsecrm - v2 - modif 127 - DEBUT */
                $monfichier = fopen('popupdebug.txt', 'a+');
        fputs($monfichier, "\n" . ' date_start_appr ' . $listApprenants[0]['date_start_appr']);
        fclose($monfichier);
        /* uni_cnfsecrm - v2 - modif 134 - DEBUT */
//        var_dump($listApprenants[0]['date_start_appr']);
        if ($listApprenants[0]['date_start_appr'] == "01-01-1970"){
            $viewer->assign('date_start_appr', $detailSession['date_start']);
        }else {
            $viewer->assign('date_start_appr', $listApprenants[0]['date_start_appr']);
        }
        
        if ($listApprenants[0]['date_start_appr'] == "01-01-1970"){
            $viewer->assign('date_fin_appr', $detailSession['due_date']);
        }else {
            $viewer->assign('date_fin_appr', $listApprenants[0]['date_fin_appr']);
        }
        
        if ($listApprenants[0]['date_start_appr'] == "01-01-1970"){
            $viewer->assign('duree_jour', $detailSession['nbreJour']);
        }else {
            $viewer->assign('duree_jour', $listApprenants[0]['duree_jour']);
        }
        
        if ($listApprenants[0]['date_start_appr'] == "01-01-1970"){
            $viewer->assign('duree_heure', $detailSession['nbreHeure']);
        }else {
            $viewer->assign('duree_heure', $listApprenants[0]['duree_heure']);
        }
        
        
        /* uni_cnfsecrm - v2 - modif 134 - FIN */
        /* uni_cnfsecrm - v2 - modif 127 - FIN */ 
        
        $viewer->view('Popup2.tpl', $moduleName);
    }

    function postProcess(Vtiger_Request $request) {
        $viewer = $this->getViewer($request);
        $moduleName = $this->getModule($request);
        $viewer->view('PopupFooter.tpl', $moduleName);
    }

    /**
     * Function to get the list of Script models to be included
     * @param Vtiger_Request $request
     * @return <Array> - List of Vtiger_JsScript_Model instances
     */
    function getHeaderScripts(Vtiger_Request $request) {
        $headerScriptInstances = parent::getHeaderScripts($request);
        $moduleName = $request->getModule();
        $jsFileNames = array(
            'libraries.bootstrap.js.eternicode-bootstrap-datepicker.js.bootstrap-datepicker',
            '~libraries/bootstrap/js/eternicode-bootstrap-datepicker/js/locales/bootstrap-datepicker.' . Vtiger_Language_Handler::getShortLanguageName() . '.js',
            '~libraries/jquery/timepicker/jquery.timepicker.min.js',
            'modules.Vtiger.resources.Popup',
            "modules.$moduleName.resources.Popup",
            'modules.Vtiger.resources.BaseList',
            "modules.$moduleName.resources.BaseList",
            'libraries.jquery.jquery_windowmsg',
            'modules.Vtiger.resources.validator.BaseValidator',
            'modules.Vtiger.resources.validator.FieldValidator',
            "modules.$moduleName.resources.validator.FieldValidator"
        );
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        $headerScriptInstances = array_merge($headerScriptInstances, $jsScriptInstances);
        return $headerScriptInstances;
    }

    /*
     * Function to initialize the required data in smarty to display the List View Contents
     */

    public function initializeListViewContents(Vtiger_Request $request, Vtiger_Viewer $viewer) {
        $moduleName = $this->getModule($request);
        $cvId = $request->get('cvid');
        $pageNumber = $request->get('page');
        $orderBy = $request->get('orderby');
        $sortOrder = $request->get('sortorder');
        $sourceModule = $request->get('src_module');
        $sourceField = $request->get('src_field');
        $sourceRecord = $request->get('src_record');
        $searchKey = $request->get('search_key');
        $searchValue = $request->get('search_value');
        $currencyId = $request->get('currency_id');
        $relatedParentModule = $request->get('related_parent_module');
        $relatedParentId = $request->get('related_parent_id');
        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);
        $searchParams = $request->get('search_params');
        $relationId = $request->get('relationId');
        //To handle special operation when selecting record from Popup
        $getUrl = $request->get('get_url');
        $autoFillModule = $moduleModel->getAutoFillModule($moduleName);
        //Check whether the request is in multi select mode
        $multiSelectMode = $request->get('multi_select');
        if (empty($multiSelectMode)) {
            $multiSelectMode = false;
        }
        if (empty($getUrl) && !empty($sourceField) && !empty($autoFillModule) && !$multiSelectMode) {
            $getUrl = 'getParentPopupContentsUrl';
        }
        if (empty($cvId)) {
            $cvId = '0';
        }
        if (empty($pageNumber)) {
            $pageNumber = '1';
        }
        $pagingModel = new Vtiger_Paging_Model();
        $pagingModel->set('page', $pageNumber);
        $recordStructureInstance = Vtiger_RecordStructure_Model::getInstanceForModule($moduleModel);
        $isRecordExists = Vtiger_Util_Helper::checkRecordExistance($relatedParentId);
        if ($isRecordExists) {
            $relatedParentModule = '';
            $relatedParentId = '';
        } else if ($isRecordExists === NULL) {
            $relatedParentModule = '';
            $relatedParentId = '';
        }
        if (!empty($relatedParentModule) && !empty($relatedParentId)) {
            $parentRecordModel = Vtiger_Record_Model::getInstanceById($relatedParentId, $relatedParentModule);
            $listViewModel = Vtiger_RelationListView_Model::getInstance($parentRecordModel, $moduleName, $label, $relationId);
            $searchModuleModel = $listViewModel->getRelatedModuleModel();
        } else {
            $listViewModel = Vtiger_ListView_Model::getInstanceForPopup($moduleName);
            $searchModuleModel = $listViewModel->getModule();
        }
        if ($moduleName == 'Documents' && $sourceModule == 'Emails') {
            $listViewModel->extendPopupFields(array('filename' => 'filename'));
        }
        if (!empty($orderBy)) {
            $listViewModel->set('orderby', $orderBy);
            $listViewModel->set('sortorder', $sortOrder);
        }
        if (!empty($sourceModule)) {
            $listViewModel->set('src_module', $sourceModule);
            $listViewModel->set('src_field', $sourceField);
            $listViewModel->set('src_record', $sourceRecord);
        }
        if ((!empty($searchKey)) && (!empty($searchValue))) {
            $listViewModel->set('search_key', $searchKey);
            $listViewModel->set('search_value', $searchValue);
        }
        $listViewModel->set('relationId', $relationId);
        if (!empty($searchParams)) {
            $transformedSearchParams = $this->transferListSearchParamsToFilterCondition($searchParams, $searchModuleModel);
            $listViewModel->set('search_params', $transformedSearchParams);
        }
        if (!empty($relatedParentModule) && !empty($relatedParentId)) {
            $this->listViewHeaders = $listViewModel->getHeaders();
            $models = $listViewModel->getEntries($pagingModel);
            $noOfEntries = count($models);
            foreach ($models as $recordId => $recordModel) {
                foreach ($this->listViewHeaders as $fieldName => $fieldModel) {
                    $recordModel->set($fieldName, $recordModel->getDisplayValue($fieldName));
                }
                $models[$recordId] = $recordModel;
            }
            $this->listViewEntries = $models;
            if (count($this->listViewEntries) > 0) {
                $parent_related_records = true;
            }
        } else {
            $this->listViewHeaders = $listViewModel->getListViewHeaders();
            $this->listViewEntries = $listViewModel->getListViewEntries($pagingModel);
        }
        // If there are no related records with parent module then, we should show all the records
        if (!$parent_related_records && !empty($relatedParentModule) && !empty($relatedParentId)) {
            $relatedParentModule = null;
            $relatedParentId = null;
            $listViewModel = Vtiger_ListView_Model::getInstanceForPopup($moduleName);
            if (!empty($orderBy)) {
                $listViewModel->set('orderby', $orderBy);
                $listViewModel->set('sortorder', $sortOrder);
            }
            if (!empty($sourceModule)) {
                $listViewModel->set('src_module', $sourceModule);
                $listViewModel->set('src_field', $sourceField);
                $listViewModel->set('src_record', $sourceRecord);
            }
            if ((!empty($searchKey)) && (!empty($searchValue))) {
                $listViewModel->set('search_key', $searchKey);
                $listViewModel->set('search_value', $searchValue);
            }
            if (!empty($searchParams)) {
                $transformedSearchParams = $this->transferListSearchParamsToFilterCondition($searchParams, $searchModuleModel);
                $listViewModel->set('search_params', $transformedSearchParams);
            }
            $this->listViewHeaders = $listViewModel->getListViewHeaders();
            $this->listViewEntries = $listViewModel->getListViewEntries($pagingModel);
        }
        // End  
        if (empty($searchParams)) {
            $searchParams = array();
        }
        //To make smarty to get the details easily accesible
        foreach ($searchParams as $fieldListGroup) {
            foreach ($fieldListGroup as $fieldSearchInfo) {
                $fieldSearchInfo['searchValue'] = $fieldSearchInfo[2];
                $fieldSearchInfo['fieldName'] = $fieldName = $fieldSearchInfo[0];
                $fieldSearchInfo['comparator'] = $fieldSearchInfo[1];
                $searchParams[$fieldName] = $fieldSearchInfo;
            }
        }
        $noOfEntries = count($this->listViewEntries);
        if (empty($sortOrder)) {
            $sortOrder = "ASC";
        }
        if ($sortOrder == "ASC") {
            $nextSortOrder = "DESC";
            $sortImage = "icon-chevron-down";
            $faSortImage = "fa-sort-desc";
        } else {
            $nextSortOrder = "ASC";
            $sortImage = "icon-chevron-up";
            $faSortImage = "fa-sort-asc";
        }
        $viewer->assign('MODULE', $moduleName);
        $viewer->assign('RELATED_MODULE', $moduleName);
        $viewer->assign('MODULE_NAME', $moduleName);
        $viewer->assign('SOURCE_MODULE', $sourceModule);
        $viewer->assign('SOURCE_FIELD', $sourceField);
        $viewer->assign('SOURCE_RECORD', $sourceRecord);
        $viewer->assign('RELATED_PARENT_MODULE', $relatedParentModule);
        $viewer->assign('RELATED_PARENT_ID', $relatedParentId);
        $viewer->assign('SEARCH_KEY', $searchKey);
        $viewer->assign('SEARCH_VALUE', $searchValue);
        $viewer->assign('RELATION_ID', $relationId);
        $viewer->assign('ORDER_BY', $orderBy);
        $viewer->assign('SORT_ORDER', $sortOrder);
        $viewer->assign('NEXT_SORT_ORDER', $nextSortOrder);
        $viewer->assign('SORT_IMAGE', $sortImage);
        $viewer->assign('FASORT_IMAGE', $faSortImage);
        $viewer->assign('GETURL', $getUrl);
        $viewer->assign('CURRENCY_ID', $currencyId);
        $viewer->assign('RECORD_STRUCTURE_MODEL', $recordStructureInstance);
        $viewer->assign('RECORD_STRUCTURE', $recordStructureInstance->getStructure());
        $viewer->assign('PAGING_MODEL', $pagingModel);
        $viewer->assign('PAGE_NUMBER', $pageNumber);
        $viewer->assign('LISTVIEW_ENTRIES_COUNT', $noOfEntries);
        $viewer->assign('LISTVIEW_HEADERS', $this->listViewHeaders);
        $viewer->assign('LISTVIEW_ENTRIES', $this->listViewEntries);
        $viewer->assign('SEARCH_DETAILS', $searchParams);
        $viewer->assign('MODULE_MODEL', $moduleModel);
        $viewer->assign('VIEW', $request->get('view'));
        if (PerformancePrefs::getBoolean('LISTVIEW_COMPUTE_PAGE_COUNT', false)) {
            if (!$this->listViewCount) {
                $this->listViewCount = $listViewModel->getListViewCount();
            }
            $totalCount = $this->listViewCount;
            $pageLimit = $pagingModel->getPageLimit();
            $pageCount = ceil((int) $totalCount / (int) $pageLimit);
            if ($pageCount == 0) {
                $pageCount = 1;
            }
            $viewer->assign('PAGE_COUNT', $pageCount);
            $viewer->assign('LISTVIEW_COUNT', $totalCount);
        }
        $viewer->assign('MULTI_SELECT', $multiSelectMode);
        $viewer->assign('CURRENT_USER_MODEL', Users_Record_Model::getCurrentUserModel());
    }

    /**
     * Function to get listView count
     * @param Vtiger_Request $request
     */
    function getListViewCount(Vtiger_Request $request) {
        $moduleName = $this->getModule($request);
        $sourceModule = $request->get('src_module');
        $sourceField = $request->get('src_field');
        $sourceRecord = $request->get('src_record');
        $orderBy = $request->get('orderby');
        $sortOrder = $request->get('sortorder');
        $currencyId = $request->get('currency_id');
        $searchKey = $request->get('search_key');
        $searchValue = $request->get('search_value');
        $searchParams = $request->get('search_params');
        $relatedParentModule = $request->get('related_parent_module');
        $relatedParentId = $request->get('related_parent_id');
        if (!empty($relatedParentModule) && !empty($relatedParentId)) {
            $parentRecordModel = Vtiger_Record_Model::getInstanceById($relatedParentId, $relatedParentModule);
            $listViewModel = Vtiger_RelationListView_Model::getInstance($parentRecordModel, $moduleName, $label);
        } else {
            $listViewModel = Vtiger_ListView_Model::getInstanceForPopup($moduleName);
        }
        if (!empty($sourceModule)) {
            $listViewModel->set('src_module', $sourceModule);
            $listViewModel->set('src_field', $sourceField);
            $listViewModel->set('src_record', $sourceRecord);
            $listViewModel->set('currency_id', $currencyId);
        }
        if (!empty($orderBy)) {
            $listViewModel->set('orderby', $orderBy);
            $listViewModel->set('sortorder', $sortOrder);
        }
        if ((!empty($searchKey)) && (!empty($searchValue))) {
            $listViewModel->set('search_key', $searchKey);
            $listViewModel->set('search_value', $searchValue);
        }
        if (!empty($searchParams)) {
            $transformedSearchParams = $this->transferListSearchParamsToFilterCondition($searchParams, $listViewModel->getModule());
            $listViewModel->set('search_params', $transformedSearchParams);
        }
        if (!empty($relatedParentModule) && !empty($relatedParentId)) {
            $count = $listViewModel->getRelatedEntriesCount();
        } else {
            $count = $listViewModel->getListViewCount();
        }
        return $count;
    }

    /**
     * Function to get the page count for list
     * @return total number of pages
     */
    function getPageCount(Vtiger_Request $request) {
        $listViewCount = $this->getListViewCount($request);
        $pagingModel = new Vtiger_Paging_Model();
        $pageLimit = $pagingModel->getPageLimit();
        $pageCount = ceil((int) $listViewCount / (int) $pageLimit);
        if ($pageCount == 0) {
            $pageCount = 1;
        }
        $result = array();
        $result['page'] = $pageCount;
        $result['numberOfRecords'] = $listViewCount;
        $response = new Vtiger_Response();
        $response->setResult($result);
        $response->emit();
    }

    public function transferListSearchParamsToFilterCondition($listSearchParams, $moduleModel) {
        return Vtiger_Util_Helper::transferListSearchParamsToFilterCondition($listSearchParams, $moduleModel);
    }

    /* uni_cnfsecrm - v2 - modif 109 - DEBUT */

    public function getTypeFormation($record) {
        $db = PearDatabase::getInstance();
        $query = "SELECT vtiger_activity.formation,vtiger_servicecf.cf_1272 
            from vtiger_activity 
            INNER JOIN vtiger_servicecf on vtiger_servicecf.serviceid = vtiger_activity.formation
            WHERE vtiger_activity.activityid = $record";
        $result = $db->pquery($query);
        $type = $db->query_result($result, 0, 'cf_1272');
        return $type; 
    }

    /* uni_cnfsecrm - v2 - modif 109 - FIN */
}
