<?php

class Arrivages_Wizard_View extends Vtiger_Index_View {

    protected $record = false;

    function __construct() {
        parent::__construct();
    }

    // function preProcess(Vtiger_Request $request, $display=true) {}

    function process(Vtiger_Request $request) {        
        $viewer = $this->getViewer($request);
        $moduleName = $request->getModule();

        $moduleModel = Vtiger_Module_Model::getInstance($moduleName);

        // $moduleVendors = Vtiger_Module_Model::getInstance("Vendors");
        $VendorSelectors = Vtiger_Field_Model::getInstance("fournisseur", $moduleModel);
        //$VendorSelectors->set('fieldvalue', 'value');

        $this->viewName = $request->get('viewname');
        $record = $request->get('record');

        if (!empty($record)) {
            $recordModel = $this->record ? $this->record : Vtiger_Record_Model::getInstanceById($record, $moduleName);
            $viewer->assign('RECORD_ID', $record);
            $viewer->assign('MODE', 'edit');
        } else {
            $recordModel = Vtiger_Record_Model::getCleanInstance($moduleName);
            $viewer->assign('MODE', '');
        }

        $viewer->assign('VENDORS', $this->getVendorsList());
        $viewer->assign('MAX_UPLOAD_LIMIT_MB', Vtiger_Util_Helper::getMaxUploadSize());
        $viewer->assign('MAX_UPLOAD_LIMIT', vglobal('upload_maxsize'));

        if ($request->get('displayMode') == 'overlay') {            
            $viewer->assign('SCRIPTS', $this->getOverlayHeaderScripts($request));
            $viewer->view('OverlayEditView.tpl', $moduleName);
        } else {            
            $viewer->assign('SCRIPTS', $this->getOverlayHeaderScripts($request));
            $viewer->view('WizardView.tpl', $moduleName);
        }
    }

    public function getOverlayHeaderScripts(Vtiger_Request $request) {
        $moduleName = $request->getModule();
        $jsFileNames = array(
            'modules.Vtiger.resources.Popup',
            'modules.Vtiger.resources.List',
            'modules.Vtiger.resources.Edit',
            			'modules.Vtiger.resources.Vtiger',
			"modules.$moduleName.resources.$moduleName",
			"~libraries/jquery/jquery.stickytableheaders.min.js",
            "modules.$moduleName.resources.Edit",
        );
        $jsScriptInstances = $this->checkAndConvertJsScripts($jsFileNames);
        return $jsScriptInstances;
    }

    private function getVendorsList() {
        $Vendors = Vtiger_Record_Model::getSearchResult("", "Vendors");
        $Vendors = $Vendors["Vendors"];
        if (is_null($Vendors)) {
            return array();
        }
        $VendorsArray = array();

        foreach ($Vendors as $Vendor) {
            $Data = $Vendor->getData();
            $id = $Data["id"];
            $name = $Data["label"];
            $VendorsArray[$id] = $name;
        }

        return $VendorsArray;
    }

    //function postProcess(Vtiger_Request $request) {} 
}
