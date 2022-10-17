<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Accounts_DetailView_Model extends Vtiger_DetailView_Model {

    /**
     * Function to get the detail view links (links and widgets)
     * @param <array> $linkParams - parameters which will be used to calicaulate the params
     * @return <array> - array of link models in the format as below
     *                   array('linktype'=>list of link models);
     */
    public function getDetailViewLinks($linkParams) {
        /* unicnfsecrm_022020_01 */
        $linkTypes = array('DETAILVIEWBASIC', 'DETAILVIEW');
        $currentUserModel = Users_Privileges_Model::getCurrentUserPrivilegesModel();
        $emailModuleModel = Vtiger_Module_Model::getInstance('Emails');
        $moduleModel = $this->getModule();
        $recordModel = $this->getRecord();
        $moduleName = $moduleModel->getName();
        $recordId = $recordModel->getId();
        //$linkModelList = parent::getDetailViewLinks($linkParams);

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

        if (Users_Privileges_Model::isPermitted($moduleName, 'Delete', $recordId)) {
            $deletelinkModel = array(
                'linktype' => 'DETAILVIEW',
                'linklabel' => sprintf("%s %s", getTranslatedString('LBL_DELETE', $moduleName), vtranslate('SINGLE_' . $moduleName, $moduleName)),
                'linkurl' => 'javascript:Vtiger_Detail_Js.deleteRecord("' . $recordModel->getDeleteUrl() . '")',
                'linkicon' => ''
            );
            $linkModelList['DETAILVIEW'][] = Vtiger_Link_Model::getInstanceFromValues($deletelinkModel);
        }

        if ($currentUserModel->hasModulePermission($emailModuleModel->getId())) {
            $basicActionLink = array(
                'linktype' => 'DETAILVIEWBASIC',
                'linklabel' => 'LBL_SEND_EMAIL',
                'linkurl' => 'javascript:Vtiger_Detail_Js.triggerSendEmail("index.php?module=' . $this->getModule()->getName() .
                '&view=MassActionAjax&mode=showComposeEmailForm&step=step1","Emails");',
                'linkicon' => ''
            );
            $linkModelList['DETAILVIEWBASIC'][] = Vtiger_Link_Model::getInstanceFromValues($basicActionLink);
        }

        $SMSNotifierModuleModel = Vtiger_Module_Model::getInstance('SMSNotifier');
        if (!empty($SMSNotifierModuleModel) && $currentUserModel->hasModulePermission($SMSNotifierModuleModel->getId())) {
            $basicActionLink = array(
                'linktype' => 'DETAILVIEWBASIC',
                'linklabel' => 'LBL_SEND_SMS',
                'linkurl' => 'javascript:Vtiger_Detail_Js.triggerSendSms("index.php?module=' . $this->getModule()->getName() .
                '&view=MassActionAjax&mode=showSendSMSForm","SMSNotifier");',
                'linkicon' => ''
            );
            $linkModelList['DETAILVIEW'][] = Vtiger_Link_Model::getInstanceFromValues($basicActionLink);
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

        /* unicnfsecrm_022020_20 */
        if ($moduleName == 'Accounts') {
            $detailViewLinks = array(
                'linklabel' => vtranslate('LBL_SIGNALETIQUE', $moduleName),
                'linkurl' => $recordModel->getExportSIGNALETIQUE(),
                'linkicon' => ''
            );
            $linkModelList['DETAILVIEW'][] = Vtiger_Link_Model::getInstanceFromValues($detailViewLinks);
        }

        return $linkModelList;
    }

}
