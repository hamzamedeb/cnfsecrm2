<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
/* uni_cnfsecrm - v2 - modif 110 - FILE */
class Contacts_PopupAjax_View extends Contacts_Popup_View {
	
	function __construct() {
		parent::__construct();
		$this->exposeMethod('getListViewCount');
		$this->exposeMethod('getRecordsCount');
		$this->exposeMethod('getPageCount');
	}
	 
	/**
	 * Function returns module name for which Popup will be initialized
	 * @param type $request
	 */
	public function getModule($request) {
                return 'Contacts'; 
	}
	
	function preProcess(Vtiger_Request $request) {
		return true;
	}

	function postProcess(Vtiger_Request $request) {
		return true;
	}

	function process (Vtiger_Request $request) {
                $mode = $request->get('mode');
		if(!empty($mode)) {
			$this->invokeExposedMethod($mode, $request);
			return;
		}
		$viewer = $this->getViewer ($request);
                $this->initializeListViewContents($request, $viewer);
		$moduleName = 'Contacts';
		$viewer->assign('MODULE_NAME',$moduleName);
		echo $viewer->view('PopupContents.tpl', $moduleName, true);
	}
}