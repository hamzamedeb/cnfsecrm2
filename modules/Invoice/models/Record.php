<?php
/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

/**
 * Inventory Record Model Class
 */
class Invoice_Record_Model extends Inventory_Record_Model {

	public function getCreatePurchaseOrderUrl() {
		$purchaseOrderModuleModel = Vtiger_Module_Model::getInstance('PurchaseOrder');
		return "index.php?module=".$purchaseOrderModuleModel->getName()."&view=".$purchaseOrderModuleModel->getEditViewName()."&invoice_id=".$this->getId();
	}
        
        	/**
	 * Function to get this record and details as PDF
	 */
	public function getPDF() {
		$recordId = $this->getId();
		$moduleName = $this->getModuleName();

		$controller = new Vtiger_InvoicePDFController($moduleName);
		$controller->loadRecord($recordId);

		$fileName = $moduleName.'_'.getModuleSequenceNumber($moduleName, $recordId);
		//$controller->Output($fileName.'.pdf', 'D');
                $controller->Output($fileName . '.pdf', 'I');
	}
        
        public function getExportPdfUrlInvoice() {
        $module = $this->getModule();
        //index.php?module=Invoice&action=ExportPDF&record=148313&app=INVENTORY
        return 'index.php?module=' . $this->getModuleName() . '&action=ExportPDF&record=' . $this->getId();
    }
}
