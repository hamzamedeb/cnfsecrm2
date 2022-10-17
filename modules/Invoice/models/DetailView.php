<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Invoice_DetailView_Model extends Inventory_DetailView_Model {

    public function getDetailViewLinks($linkParams) {
        $linkTypes = array('DETAILVIEWBASIC', 'DETAILVIEW');
        $moduleModel = $this->getModule();
        $recordModel = $this->getRecord();

        $moduleName = $moduleModel->getName();
        $recordId = $recordModel->getId();

        $detailViewLink = array();
        $linkModelList = array();
        if (Users_Privileges_Model::isPermitted($moduleName, 'EditView', $recordId)) {
            $detailViewLinks[] = array(
                'linktype' => 'DETAILVIEWBASIC',
                'linklabel' => 'LBL_EDIT',
                'linkurl' => $recordModel->getEditViewUrl(),
                'linkicon' => ''
            );

            foreach ($detailViewLinks as $detailViewLink) {
                $linkModelList['DETAILVIEWBASIC'][] = Vtiger_Link_Model::getInstanceFromValues($detailViewLink);
            }
        }
        /* uni_cnfsecrm - v2 - modif 164 - DEBUT */
        if (Users_Privileges_Model::isPermitted($moduleName, 'EditView', $recordId)) {
            /* uni_cnfsecrm - v2 - modif 166 - DEBUT */
            $detailViewLinks1[] = array(
                'linktype' => 'DETAILVIEWTAB',
                'linklabel' => 'LBL_EXPORT_TO_PDF',
                'linkurl' => 'javascript:window.open(\'index.php?module=' . $this->getModuleName() . '&record=' . $recordId . '&action=ExportPDF \', \'_blank\')',
                'linkicon' => ''
            );
            /* uni_cnfsecrm - v2 - modif 166 - FIN */
            foreach ($detailViewLinks1 as $detailViewLink) {
                $linkModelList['DETAILVIEWBASIC'][] = Vtiger_Link_Model::getInstanceFromValues($detailViewLink);
            }
        }
        
        if (Users_Privileges_Model::isPermitted($moduleName, 'EditView', $recordId)) {
            $detailViewLinks2[] = array(
                'linktype' => 'DETAILVIEWBASIC',
                'linklabel' => 'LBL_SEND_MAIL_PDF',
                'linkurl' => 'javascript:Inventory_Detail_Js.sendEmailPDFClickHandler(\'' . $recordModel->getSendEmailPDFUrl() . '\')',
                'linkicon' => ''
            );
            foreach ($detailViewLinks2 as $detailViewLink) {
                $linkModelList['DETAILVIEWBASIC'][] = Vtiger_Link_Model::getInstanceFromValues($detailViewLink);
            }
        }
        /* uni_cnfsecrm - v2 - modif 164 - FIN */
        
        /* uni_cnfsecrm - v2 - modif 170 - DEBUT */
        if (Users_Privileges_Model::isPermitted($moduleName, 'EditView', $recordId)) {
            $detailViewLinks3[] = array(
                'linktype' => 'DETAILVIEWTAB',
                'linklabel' => vtranslate('LBL_EMAIL_FACO', $moduleName),
                'linkurl' => 'javascript:Inventory_Detail_Js.sendEmailPDFClickHandler(\'' . $recordModel->getSendEmailPDFUrl() . '&factureCo=1\')',
                'linkicon' => ''
            );
            foreach ($detailViewLinks3 as $detailViewLink) {
                $linkModelList['DETAILVIEWBASIC'][] = Vtiger_Link_Model::getInstanceFromValues($detailViewLink);
            }
        }
        /* uni_cnfsecrm - v2 - modif 170 - FIN */

        if ($moduleModel->isDuplicateOptionAllowed('CreateView', $recordId)) {
            $duplicateLinkModel = array(
                'linktype' => 'DETAILVIEWBASIC',
                'linklabel' => 'LBL_DUPLICATE',
                'linkurl' => $recordModel->getDuplicateRecordUrl(),
                'linkicon' => ''
            );
            $linkModelList['DETAILVIEW'][] = Vtiger_Link_Model::getInstanceFromValues($duplicateLinkModel);
        }

        if ($this->getModule()->isModuleRelated('Emails') && Vtiger_RecipientPreference_Model::getInstance($this->getModuleName())) {
            $emailRecpLink = array('linktype' => 'DETAILVIEW',
                'linklabel' => vtranslate('LBL_EMAIL_RECIPIENT_PREFS', $this->getModuleName()),
                'linkurl' => 'javascript:Vtiger_Index_Js.showRecipientPreferences("' . $this->getModuleName() . '");',
                'linkicon' => '');
            $linkModelList['DETAILVIEW'][] = Vtiger_Link_Model::getInstanceFromValues($emailRecpLink);
        }

        $linkModelListDetails = Vtiger_Link_Model::getAllByType($moduleModel->getId(), $linkTypes, $linkParams);
        foreach ($linkTypes as $linkType) {
            if (!empty($linkModelListDetails[$linkType])) {
                foreach ($linkModelListDetails[$linkType] as $linkModel) {
                    // Remove view history, needed in vtiger5 to see history but not in vtiger6
                    if ($linkModel->linklabel == 'View History') {
                        continue;
                    }
                    $linkModelList[$linkType][] = $linkModel;
                }
            }
            unset($linkModelListDetails[$linkType]);
        }

        $relatedLinks = $this->getDetailViewRelatedLinks();

        foreach ($relatedLinks as $relatedLinkEntry) {
            $relatedLink = Vtiger_Link_Model::getInstanceFromValues($relatedLinkEntry);
            $linkModelList[$relatedLink->getType()][] = $relatedLink;
        }

        $widgets = $this->getWidgets();
        foreach ($widgets as $widgetLinkModel) {
            $linkModelList['DETAILVIEWWIDGET'][] = $widgetLinkModel;
        }

        $currentUserModel = Users_Record_Model::getCurrentUserModel();
        if ($currentUserModel->isAdminUser()) {
            $settingsLinks = $moduleModel->getSettingLinks();
            foreach ($settingsLinks as $settingsLink) {
                $linkModelList['DETAILVIEWSETTING'][] = Vtiger_Link_Model::getInstanceFromValues($settingsLink);
            }
        }

        /* uni_cnfsecrm - modif 92 - Ne pas afficher bouton Générer avoir pour une facture qui a un avoir - DEBUT */
        $numFacture = $recordModel->get('invoice_no');
        $db = PearDatabase::getInstance();
        $query = "select invoiceid from vtiger_invoicecf where cf_1039 LIKE '%" . $numFacture . "%'";
        $result = $db->pquery($query);
        $idInvoice = $db->query_result($result, 0, 'invoiceid');
        if (is_null($idInvoice)) {
            /* uni_cnfsecrm - ajouter avoir */
            $avoirLinkModel = array(
                'linktype' => 'DETAILVIEWBASIC',
                'linklabel' => 'LBL_AVOIR',
                'linkurl' => $recordModel->getAvoirRecordUrl(),
                'linkicon' => ''
            );
            $linkModelList['DETAILVIEW'][] = Vtiger_Link_Model::getInstanceFromValues($avoirLinkModel);
        }
        /* uni_cnfsecrm - modif 92 - FIN */

        if (Users_Privileges_Model::isPermitted($moduleName, 'DetailView', $recordModel->getId())) {
/*uni_cnfsecrm - v2 - modif 166 - DEBUT*/
//            $detailViewLinks = array(
//                'linktype' => 'DETAILVIEWTAB',
//                'linklabel' => vtranslate('LBL_EXPORT_TO_PDF', $moduleName),
//                'linkurl' => $recordModel->getExportPDFURL(),
//                'linkicon' => ''
//            );
//            $linkModelList['DETAILVIEW'][] = Vtiger_Link_Model::getInstanceFromValues($detailViewLinks);
/*uni_cnfsecrm - v2 - modif 166 - FIN */
            $sendEmailLink = array(
                'linklabel' => vtranslate('LBL_SEND_MAIL_PDF', $moduleName),
                'linkurl' => 'javascript:Inventory_Detail_Js.sendEmailPDFClickHandler(\'' . $recordModel->getSendEmailPDFUrl() . '\')',
                'linkicon' => ''
            );

            $linkModelList['DETAILVIEW'][] = Vtiger_Link_Model::getInstanceFromValues($sendEmailLink);

            /* unicnfsecrm_022020_17 */
            $avoirLinkModel = array(
                'linklabel' => vtranslate('LBL_EMAIL_FACO', $moduleName),
                'linkurl' => 'javascript:Inventory_Detail_Js.sendEmailPDFClickHandler(\'' . $recordModel->getSendEmailPDFUrl() . '&factureCo=1\')',
                'linkicon' => ''
            );
            $linkModelList['DETAILVIEW'][] = Vtiger_Link_Model::getInstanceFromValues($avoirLinkModel);
        }
        return $linkModelList;
    }

}
