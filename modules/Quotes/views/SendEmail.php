<?php

/* +***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * *********************************************************************************** */

class Quotes_SendEmail_View extends Inventory_SendEmail_View {

    /**
     * Function which will construct the compose email
     * This will handle the case of attaching the invoice pdf as attachment
     * @param Vtiger_Request $request 
     */
    /* uni_cnfsecrm - v2 - modif 159 - DEBUT */
    public function composeMailData(Vtiger_Request $request) {
        $adb = PearDatabase::getInstance();
        Vtiger_ComposeEmail_View::composeMailData($request);
        $viewer = $this->getViewer($request);
        $inventoryRecordId = $request->get('record');
        $recordModel = Vtiger_Record_Model::getInstanceById($inventoryRecordId, $request->getModule());
        $module = $request->get('module');
        $sqlTypeFormation = "SELECT vtiger_service.servicecategory,tarif ,cf_1312
                    FROM vtiger_service
                    INNER JOIN vtiger_inventoryproductrel on vtiger_inventoryproductrel.productid = vtiger_service.serviceid
                     INNER JOIN vtiger_servicecf on vtiger_servicecf.serviceid = vtiger_service.serviceid
                    WHERE vtiger_inventoryproductrel.id = ?";
        $paramsTypeFormation = array($inventoryRecordId);

        $resultTypeFormation = $adb->pquery($sqlTypeFormation, $paramsTypeFormation);
        $categorieFormation = $adb->query_result($resultTypeFormation, 0, 'servicecategory');
        $tarif = $adb->query_result($resultTypeFormation, 0, 'tarif');
        $templateDevis = $adb->query_result($resultTypeFormation, 0, 'cf_1312');

        if (!is_null($templateDevis)) {
            $pdfFileName = $recordModel->getPDFFileName();
            $fileComponents = explode('/', $pdfFileName);
            $fileName = $fileComponents[count($fileComponents) - 1];
            array_pop($fileComponents);
            $detailTemplate = $this->getBodyEmail($templateDevis);
            $attachmentDetails = array(
                array(
                    'id' => 0,
                    'attachment' => $fileName,
                    'path' => implode('/', $fileComponents),
                    'size' => filesize($pdfFileName),
                    'type' => 'pdf',
                    'nondeletable' => true
                ),
            );
            $this->populateTo($request);

            $viewer->assign('DESCRIPTION', $detailTemplate['body']);
            $viewer->assign('SUBJECT', $detailTemplate['subject']);
            $viewer->assign('ATTACHMENTS', $attachmentDetails);
            echo $viewer->view('ComposeEmailFormQuotes.tpl', 'Emails', true);
            /* uni_cnfsecrm - v2 - modif 156 - FIN */
        } else {

            $pdfFileName = $recordModel->getPDFFileName();
            $fileComponents = explode('/', $pdfFileName);
            $fileName = $fileComponents[count($fileComponents) - 1];
            array_pop($fileComponents);
            $attachmentDetails = array(
                array(
                    'attachment' => $fileName,
                    'path' => implode('/', $fileComponents),
                    'size' => filesize($pdfFileName),
                    'type' => 'pdf',
                    'nondeletable' => true
                )
            );
            $this->populateTo($request);
            $viewer->assign('ATTACHMENTS', $attachmentDetails);
            echo $viewer->view('ComposeEmailForm.tpl', 'Emails', true);
        }
    }

    function getBodyEmail($templateDevis) {
        global $adb;
        switch ($templateDevis) {
            case 'AIPR':
                $idTemplate = 46;
                break;
            case 'HACCP':
                $idTemplate = 52;
                break;
            case 'B0 H0 en ligne':
                $idTemplate = 47;
                break;
            case 'B0 H0':
                $idTemplate = 48;
                break;
            case 'B1 B2 BC BR':
                $idTemplate = 49;
                break;
            case 'BS BE':
                $idTemplate = 50;
                break;
            case 'BT et HT':
                $idTemplate = 51;
                break;
            case 'SST en initial':
                $idTemplate = 53;
                break;
            case 'SST MAC':
                $idTemplate = 54;
                break;
            case 'AUTRES':
                $idTemplate = 55;
                break;
        }

        $query = 'SELECT body,subject FROM vtiger_emailtemplates WHERE templateid = ?';

        $result = $adb->pquery($query, array($idTemplate));
        $detailTemplate['body'] = decode_html($adb->query_result($result, 0, 'body'));
        $detailTemplate['subject'] = decode_html($adb->query_result($result, 0, 'subject'));
        return $detailTemplate;
    }
    /* uni_cnfsecrm - v2 - modif 159 - FIN */

}

?>
