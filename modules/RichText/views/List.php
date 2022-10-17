<?php
class RichText_List_View extends Vtiger_Index_View	
{	
	public function process(Vtiger_Request $request)	{
		global $adb, $current_user;
		$viewer = $this->getViewer ($request);
		$moduleName = $request->getModule();
		require_once("modules/{$moduleName}/config.php");

		$viewer->assign('MODULE_NAME', $moduleName);
		//$viewer->view('MailBadgeSettings.tpl', $moduleName);
	}
}
