<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class SalesOrder_DetailView_Model extends Inventory_DetailView_Model {

    /**
     * Function to get the detail view links (links and widgets)
     * @param <array> $linkParams - parameters which will be used to calicaulate the params
     * @return <array> - array of link models in the format as below
     *                   array('linktype'=>list of link models);
     */
    public function getDetailViewLinks($linkParams) {
        $currentUserModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();

        $linkModelList = parent::getDetailViewLinks($linkParams);
        $recordModel = $this->getRecord();

        $invoiceModuleModel = Vtiger_Module_Model::getInstance('Invoice');
        /* unicnfsecrm - hide convertion convention en facture */
        /* if ($currentUserModel->hasModuleActionPermission($invoiceModuleModel->getId(), 'CreateView')) {
          $basicActionLink = array(
          'linktype' => 'DETAILVIEW',
          'linklabel' => vtranslate('LBL_CREATE') . ' ' . vtranslate($invoiceModuleModel->getSingularLabelKey(), 'Invoice'),
          'linkurl' => $recordModel->getCreateInvoiceUrl(),
          'linkicon' => ''
          );
          $linkModelList['DETAILVIEW'][] = Vtiger_Link_Model::getInstanceFromValues($basicActionLink);
          } */

        $purchaseOrderModuleModel = Vtiger_Module_Model::getInstance('PurchaseOrder');
        if ($currentUserModel->hasModuleActionPermission($purchaseOrderModuleModel->getId(), 'CreateView')) {
            $basicActionLink = array(
                'linktype' => 'DETAILVIEW',
                'linklabel' => vtranslate('LBL_CREATE') . ' ' . vtranslate($purchaseOrderModuleModel->getSingularLabelKey(), 'PurchaseOrder'),
                'linkurl' => $recordModel->getCreatePurchaseOrderUrl(),
                'linkicon' => ''
            );
            $linkModelList['DETAILVIEW'][] = Vtiger_Link_Model::getInstanceFromValues($basicActionLink);
        }

        /* Création facture financeur */
        //unicnfsecrm_mod_55
        $basicActionLink = array(
            'linktype' => 'DETAILVIEW',
            'linklabel' => 'Creer les factures financeur',
            'linkurl' => 'javascript:Inventory_Detail_Js.test(\'' . $recordModel->creationFacture() . '\')',
            'linkicon' => ''
        );
        $linkModelList['DETAILVIEW'][] = Vtiger_Link_Model::getInstanceFromValues($basicActionLink);


        /* uni_cnfsecrm - v2 - modif 164 - DEBUT */
        //if (Users_Privileges_Model::isPermitted($moduleName, 'EditView', $recordId)) {
        /*uni_cnfsecrm - v2 - modif 166 - DEBUT*/
        $moduleModel = $this->getModule();
        $recordModel = $this->getRecord();
        $moduleName = $moduleModel->getName();
        $recordId = $recordModel->getId();  
        $detailViewLinks1[] = array(
            'linktype' => 'DETAILVIEWBASIC',
            'linklabel' => 'LBL_EXPORT_TO_PDF',
            'linkurl' => 'javascript:window.open(\'index.php?module=' . $moduleName . '&record='.$recordId.'&action=ExportPDF \', \'_blank\')',
            'linkicon' => ''
        );
        /*uni_cnfsecrm - v2 - modif 166 - DEBUT*/
        foreach ($detailViewLinks1 as $detailViewLink) {
            $linkModelList['DETAILVIEWBASIC'][] = Vtiger_Link_Model::getInstanceFromValues($detailViewLink);
        }
        //}
        // if (Users_Privileges_Model::isPermitted($moduleName, 'EditView', $recordId)) {
        $detailViewLinks2[] = array(
            'linktype' => 'DETAILVIEWBASIC',
            'linklabel' => 'LBL_SEND_MAIL_PDF',
            'linkurl' => 'javascript:Inventory_Detail_Js.sendEmailPDFClickHandler(\'' . $recordModel->getSendEmailPDFUrl() . '\')',
            'linkicon' => ''
        );
        foreach ($detailViewLinks2 as $detailViewLink) {
            $linkModelList['DETAILVIEWBASIC'][] = Vtiger_Link_Model::getInstanceFromValues($detailViewLink);
        }
        // }
        /* uni_cnfsecrm - v2 - modif 164 - FIN */

        return $linkModelList;
    }

}
