<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

/**
 * Inventory Record Model Class
 */
class SalesOrder_Record_Model extends Inventory_Record_Model {

    function getCreateInvoiceUrl() {
        $invoiceModuleModel = Vtiger_Module_Model::getInstance('Invoice');

        return "index.php?module=" . $invoiceModuleModel->getName() . "&view=" . $invoiceModuleModel->getEditViewName() . "&salesorder_id=" . $this->getId() . "&cnverfa=1";
    }

    function getCreatePurchaseOrderUrl() {
        $purchaseOrderModuleModel = Vtiger_Module_Model::getInstance('PurchaseOrder');
        return "index.php?module=" . $purchaseOrderModuleModel->getName() . "&view=" . $purchaseOrderModuleModel->getEditViewName() . "&salesorder_id=" . $this->getId();
    }

    public function getPDF() {
        $recordId = $this->getId();
        $moduleName = $this->getModuleName();

        $controller = new Vtiger_SalesOrderPDFController($moduleName);
        $controller->loadRecord($recordId);

        $fileName = $moduleName . '_' . getModuleSequenceNumber($moduleName, $recordId);
        //$controller->Output($fileName.'.pdf', 'D');
        $controller->Output($fileName . '.pdf', 'I');
    }

    public function getEMARGEMENT() {
        $recordId = $this->getId();
        $moduleName = $this->getModuleName();

        $controller = new Vtiger_SalesOrderPDFController($moduleName);
        $controller->loadRecord($recordId);

        $fileName = $recordId . "_Feuille_Emargement";
        //$controller->Output($fileName.'.pdf', 'D');
        $controller->OutputEMARGEMENT($fileName . '.pdf', 'I');
    }

    public function getSatisfaction() {
        $recordId = $this->getId();
        $moduleName = $this->getModuleName();

        $controller = new Vtiger_SalesOrderPDFController($moduleName);
        $controller->loadRecord($recordId);

        $fileName = "Questionnaire de satisfaction" . '_' . getModuleSequenceNumber($moduleName, $recordId);
        //$controller->Output($fileName.'.pdf', 'D');
        $controller->OutputSatisfaction($fileName . '.pdf', 'I');
    }

    public function getCONVOCATION() {
        $recordId = $this->getId();
        $moduleName = $this->getModuleName();

        $controller = new Vtiger_SalesOrderPDFController($moduleName);
        $controller->loadRecord($recordId);

        $fileName = "Convocation" . '_' . getModuleSequenceNumber($moduleName, $recordId);
        //$controller->Output($fileName.'.pdf', 'D');
        $controller->OutputConvocation($fileName . '.pdf', 'I');
    }

    /* unicnfsecrm_022020_17 */

    public function getPDFFileNameCONVOCATION() {
        $moduleName = $this->getModuleName();
        $recordId = $this->getId();
        $controller = new Vtiger_SalesOrderPDFController($moduleName);
        $controller->loadRecord($recordId);

        $sequenceNo = "Convocation" . '_' . getModuleSequenceNumber($moduleName, $recordId);
        //$translatedName = vtranslate($moduleName, $moduleName);
        $filePath = "storage/" . $sequenceNo . ".pdf";
        //added file name to make it work in IE, also forces the download giving the user the option to save
        $controller->OutputConvocation($filePath, 'F');
        return $filePath;
    }
    
        
    //creation facture financeur 
    //unicnfsecrm_mod_55
    public function creationFacture() {
        return "index.php?module=" . $this->getModuleName() . "&action=CreationFactureFin&record=" . $this->getId();
    }

}
