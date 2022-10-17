<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Events_DetailView_Model extends Vtiger_DetailView_Model {

    public function getDetailViewLinks($linkParams) {
        $linkModelList = parent::getDetailViewLinks($linkParams);
        $recordModel = $this->getRecord();
        $moduleName = $recordModel->getmoduleName();

        //    if (Users_Privileges_Model::isPermitted($moduleName, 'DetailView', $recordModel->getId())) {
        $detailViewLinks = array(
            'linklabel' => vtranslate('LBL_EXPORT_LISTE_APPRENANTS', $moduleName),
            'linkurl' => $recordModel->getExportLISTEAPPRENANTS(),
            'linkicon' => ''
        );
        $linkModelList['DETAILVIEW'][] = Vtiger_Link_Model::getInstanceFromValues($detailViewLinks);

        $detailViewLinks = array(
            'linklabel' => vtranslate('LBL_EXPORT_LISTE_ATTESTATIONS', $moduleName),
            'linkurl' => $recordModel->getExportLISTEATTESTATIONS(),
            'linkicon' => ''
        );
        $linkModelList['DETAILVIEW'][] = Vtiger_Link_Model::getInstanceFromValues($detailViewLinks);

        $detailViewLinks = array(
            'linklabel' => vtranslate('LBL_EXPORT_LISTE_AVIS_FAVORABLES', $moduleName),
            'linkurl' => $recordModel->getExportLISTEAVISFAVORABLE(),
            'linkicon' => ''
        );
        $linkModelList['DETAILVIEW'][] = Vtiger_Link_Model::getInstanceFromValues($detailViewLinks);

        $detailViewLinks = array(
            'linklabel' => vtranslate('LBL_EXPORT_EMARGEMENT', $moduleName),
            'linkurl' => $recordModel->getExportEMARGEMENT(),
            'linkicon' => ''
        );
        $linkModelList['DETAILVIEW'][] = Vtiger_Link_Model::getInstanceFromValues($detailViewLinks);

        /* unicnfsecrm_022020_15 */
        $detailViewLinks = array(
            'linklabel' => vtranslate('LBL_EXPORT_LIST_SATISFACTION', $moduleName),
            'linkurl' => $recordModel->getExportLISTSATISFACTION(),
            'linkicon' => ''
        );
        $linkModelList['DETAILVIEW'][] = Vtiger_Link_Model::getInstanceFromValues($detailViewLinks);

        $detailViewLinks = array(
            'linklabel' => vtranslate('LBL_EXPORT_LISTE_TOKENS', $moduleName),
            'linkurl' => $recordModel->getExportLISTETOKENS(),
            'linkicon' => ''
        );
        $linkModelList['DETAILVIEW'][] = Vtiger_Link_Model::getInstanceFromValues($detailViewLinks);

        $detailViewLinks = array(
            'linklabel' => vtranslate('LBL_EXPORT_LISTE_TOKENS_TEST', $moduleName),
            'linkurl' => $recordModel->getExportLISTETOKENSTEST(),
            'linkicon' => ''
        );
        $linkModelList['DETAILVIEW'][] = Vtiger_Link_Model::getInstanceFromValues($detailViewLinks);

        /* uni_cnfsecrm - v2 - modif 145 - DEBUT */
        $detailViewLinks = array(
            'linklabel' => vtranslate('LBL_EXPORT_LISTE_TITRES HABILITATIONS', $moduleName),
            'linkurl' => $recordModel->getExportLISTETITREHABILITATIONS(),
            'linkicon' => ''
        );
        $linkModelList['DETAILVIEW'][] = Vtiger_Link_Model::getInstanceFromValues($detailViewLinks);
        /* uni_cnfsecrm - v2 - modif 145 - FIN */
        
        /*uni_cnfsecrm - v2 - modif 165 - DEBUT*/
        $detailViewLinks = array(
            'linklabel' => vtranslate('LBL_EXPORT_IDENTIFIENTS_ELEARNING', $moduleName),
            'linkurl' => $recordModel->getExportIDENTIFIENTSELEARNING(),
            'linkicon' => ''
        );
        $linkModelList['DETAILVIEW'][] = Vtiger_Link_Model::getInstanceFromValues($detailViewLinks);
        /*uni_cnfsecrm - v2 - modif 165 - FIN*/

        return $linkModelList;
    }

}
