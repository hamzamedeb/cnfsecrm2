<?php
/* * *******************************************************************************
 * The content of this file is subject to the Cashflow4You license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is IT-Solutions4You s.r.o.
 * Portions created by IT-Solutions4You s.r.o. are Copyright(C) IT-Solutions4You s.r.o.
 * All Rights Reserved.
 * ****************************************************************************** */

class Cashflow4You_Detail_View extends Vtiger_Detail_View {

	function process(Vtiger_Request $request) {
            $viewer = $this->getViewer($request);
            $viewer->assign("VERSION", Cashflow4You_Version_Helper::$version);
            parent::process($request);
	}
}
