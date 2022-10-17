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
    //unicnfsecrm_mod_18
    public function composeMailData(Vtiger_Request $request) {
        $monfichier = fopen('debug_cnfse.txt', 'a+');
        fputs($monfichier, "\n" . ' Quotes_SendEmail_View ');
        fclose($monfichier);
        $adb = PearDatabase::getInstance();
        Vtiger_ComposeEmail_View::composeMailData($request);
        $viewer = $this->getViewer($request);
        $inventoryRecordId = $request->get('record');
        $relance = $request->get('relance');
        $recordModel = Vtiger_Record_Model::getInstanceById($inventoryRecordId, $request->getModule());
        /* unicnfsecrm_022020_07 - begin */
        $module = $request->get('module');
        $monfichier = fopen('debug_cnfse.txt', 'a+');
        fputs($monfichier, "\n" . ' module ' . $module);
        fclose($monfichier);
        $sqlTypeFormation = "SELECT vtiger_service.servicecategory,tarif 
                    FROM vtiger_service
                    INNER JOIN vtiger_inventoryproductrel on vtiger_inventoryproductrel.productid = vtiger_service.serviceid
                    WHERE vtiger_inventoryproductrel.id = ?";
        $paramsTypeFormation = array($inventoryRecordId);

        $resultTypeFormation = $adb->pquery($sqlTypeFormation, $paramsTypeFormation);
        $categorieFormation = $adb->query_result($resultTypeFormation, 0, 'servicecategory');
        $tarif = $adb->query_result($resultTypeFormation, 0, 'tarif');
        $monfichier = fopen('debug_cnfse.txt', 'a+');
        fputs($monfichier, "\n" . ' categorieFormation ' . $categorieFormation);
        fclose($monfichier);
        if ($categorieFormation == 'HABILITATIONS' || $categorieFormation == 'HYGIENE' || $categorieFormation == 'AIPR') {
            if ($categorieFormation == 'HABILITATIONS') {
                $pdfFileName = $recordModel->getPDFFileName();
                $fileComponents = explode('/', $pdfFileName);
                $fileName = $fileComponents[count($fileComponents) - 1];
                array_pop($fileComponents);
                //Devis Habilitation électrique
                //pdf 1
                $pdfFileName1 = 'documents/devis_HABILITATION/2_Tableau_différentes_habilitations.pdf';
                $fileComponents1 = explode('/', $pdfFileName1);
                $fileName1 = '2_Tableau_différentes_habilitations.pdf';
                //array_pop($fileComponents1);

                $pdfFileName11 = 'documents/devis_HABILITATION/3B1 Habilitation B0 H0 H0v e-learning.pdf';
                $fileComponents11 = explode('/', $pdfFileName11);
                $fileName11 = '3B1 Habilitation B0 H0 H0v e-learning.pdf';
                array_pop($fileComponents11);

                $pdfFileName12 = 'documents/devis_HABILITATION/3B1 Habilitation B0 H0 H0v.pdf';
                $fileComponents12 = explode('/', $pdfFileName12);
                $fileName12 = '3B1 Habilitation B0 H0 H0v.pdf';
                array_pop($fileComponents12);
                //pdf 2
                $pdfFileName2 = 'documents/devis_HABILITATION/3B2_Habilitation_BS_BE_HE.pdf';
                $fileComponents2 = explode('/', $pdfFileName2);
                $fileName2 = '3B2_Habilitation_BS_BE_HE.pdf';
                array_pop($fileComponents2);
                //pdf 3
                $pdfFileName3 = 'documents/devis_HABILITATION/3B3_Habilitation_B1_B2_BC_BR_BEvérif,essai_et_mesure.pdf';
                $fileComponents3 = explode('/', $pdfFileName3);
                $fileName3 = '3B3_Habilitation_B1_B2_BC_BR_BEvérif,essai_et_mesure.pdf';
                array_pop($fileComponents3);
                //pdf 4
                $pdfFileName4 = 'documents/devis_HABILITATION/3B4_Habilitation_B1_B2_BC_BR_H1_H2.pdf';
                $fileComponents4 = explode('/', $pdfFileName4);
                $fileName4 = '3B4_Habilitation_B1_B2_BC_BR_H1_H2.pdf';
                array_pop($fileComponents4);
                //pdf 5
                $pdfFileName5 = 'documents/devis_HABILITATION/Session_Habilitation_2021.pdf';
                $fileComponents5 = explode('/', $pdfFileName5);
                $fileName5 = 'Session_Habilitation_2021.pdf';
                array_pop($fileComponents5);

                $attachmentDetails = array(
                    array(
                        'id' => 0,
                        'attachment' => $fileName,
                        'path' => implode('/', $fileComponents),
                        'size' => filesize($pdfFileName),
                        'type' => 'pdf',
                        'nondeletable' => true
                    ),
//                    array(
//                        'id' => 1,
//                        'attachment' => $fileName1,
//                        'path' => implode('/', $fileComponents1),
//                        'size' => filesize($pdfFileName1),
//                        'type' => 'pdf',
//                        'nondeletable' => false
//                    ),
                    array(
                        'id' => 1,
                        'attachment' => $fileName11,
                        'path' => implode('/', $fileComponents11),
                        'size' => filesize($pdfFileName11),
                        'type' => 'pdf',
                        'nondeletable' => false
                    ),
                    array(
                        'id' => 1,
                        'attachment' => $fileName12,
                        'path' => implode('/', $fileComponents12),
                        'size' => filesize($pdfFileName12),
                        'type' => 'pdf',
                        'nondeletable' => false
                    ),
                    array(
                        'id' => 2,
                        'attachment' => $fileName2,
                        'path' => implode('/', $fileComponents2),
                        'size' => filesize($pdfFileName2),
                        'type' => 'pdf',
                        'nondeletable' => false
                    ),
                    array(
                        'id' => 3,
                        'attachment' => $fileName3,
                        'path' => implode('/', $fileComponents3),
                        'size' => filesize($pdfFileName3),
                        'type' => 'pdf',
                        'nondeletable' => false
                    ),
                    array(
                        'id' => 4,
                        'attachment' => $fileName4,
                        'path' => implode('/', $fileComponents4),
                        'size' => filesize($pdfFileName4),
                        'type' => 'pdf',
                        'nondeletable' => false
                    ),
                );
                if ($tarif == "inter") {
                    array_push($attachmentDetails, array(
                        'id' => 5,
                        'attachment' => $fileName5,
                        'path' => implode('/', $fileComponents5),
                        'size' => filesize($pdfFileName5),
                        'type' => 'pdf',
                        'nondeletable' => false
                    ));
                }
            } else if ($categorieFormation == 'HYGIENE') {
                $pdfFileName = $recordModel->getPDFFileName();
                $fileComponents = explode('/', $pdfFileName);
                $fileName = $fileComponents[count($fileComponents) - 1];
                array_pop($fileComponents);

                //Devis Méthode HACCP
                //pdf 1
                $pdfFileName1 = 'documents/devis_HACCP/3A1_Méthode_Haccp-_CNFSE.pdf';
                $fileComponents1 = explode('/', $pdfFileName1);
                $fileName1 = '3A1_Méthode_Haccp-_CNFSE.pdf';
                array_pop($fileComponents1);
                //pdf 2
                $pdfFileName2 = 'documents/devis_HACCP/Formation_Formateur_3j_.pdf';
                $fileComponents2 = explode('/', $pdfFileName2);
                $fileName2 = 'Formation_Formateur_3j_.pdf';
                array_pop($fileComponents2);
                //pdf 3
//                $pdfFileName3 = 'documents/devis_HACCP/Session_Formateur_&_Tuteur_2020.pdf';
//                $fileComponents3 = explode('/', $pdfFileName3);
//                $fileName3 = 'Session_Formateur_&_Tuteur_2020.pdf';
//                array_pop($fileComponents3);
                //pdf 4
                $pdfFileName4 = 'documents/devis_HACCP/Session_Haccp_2021.pdf';
                $fileComponents4 = explode('/', $pdfFileName4);
                $fileName4 = 'Session_Haccp_2021.pdf';
                array_pop($fileComponents4);

                $attachmentDetails = array(
                    array(
                        'id' => 0,
                        'attachment' => $fileName,
                        'path' => implode('/', $fileComponents),
                        'size' => filesize($pdfFileName),
                        'type' => 'pdf',
                        'nondeletable' => true
                    ),
                    array(
                        'id' => 1,
                        'attachment' => $fileName1,
                        'path' => implode('/', $fileComponents1),
                        'size' => filesize($pdfFileName1),
                        'type' => 'pdf',
                        'nondeletable' => false
                    ),
                    array(
                        'id' => 2,
                        'attachment' => $fileName2,
                        'path' => implode('/', $fileComponents2),
                        'size' => filesize($pdfFileName2),
                        'type' => 'pdf',
                        'nondeletable' => false
                ));

                if ($tarif == "inter") {
                    array_push($attachmentDetails, array(
                        'id' => 3,
                        'attachment' => $fileName3,
                        'path' => implode('/', $fileComponents3),
                        'size' => filesize($pdfFileName3),
                        'type' => 'pdf',
                        'nondeletable' => false
                    ));
                    array_push($attachmentDetails, array(
                        'id' => 4,
                        'attachment' => $fileName4,
                        'path' => implode('/', $fileComponents4),
                        'size' => filesize($pdfFileName4),
                        'type' => 'pdf',
                        'nondeletable' => false
                    ));
                }
            } else if ($categorieFormation == 'AIPR') {
                $pdfFileName = $recordModel->getPDFFileName();
                $fileComponents = explode('/', $pdfFileName);
                $fileName = $fileComponents[count($fileComponents) - 1];
                array_pop($fileComponents);
                //Devis formation AIPR
                //pdf 1
                $pdfFileName1 = 'documents/devis_AIPR/3D1_AIPR_Opérateur.pdf';
                $fileComponents1 = explode('/', $pdfFileName1);
                $fileName1 = '3D1_AIPR_Opérateur.pdf';
                array_pop($fileComponents1);
                //pdf 2
                $pdfFileName2 = 'documents/devis_AIPR/3D2_AIPR_Encadrant.pdf';
                $fileComponents2 = explode('/', $pdfFileName2);
                $fileName2 = '3D2_AIPR_Encadrant.pdf';
                array_pop($fileComponents2);
                //pdf 3
                $pdfFileName3 = 'documents/devis_AIPR/3D3_AIPR_Concepteur.pdf';
                $fileComponents3 = explode('/', $pdfFileName3);
                $fileName3 = '3D3_AIPR_Concepteur.pdf';
                array_pop($fileComponents3);
                //pdf 4
                $pdfFileName4 = 'documents/devis_AIPR/Session_AIPR_2021.pdf';
                $fileComponents4 = explode('/', $pdfFileName4);
                $fileName4 = 'Session_AIPR_2021.pdf';
                array_pop($fileComponents4);

                $attachmentDetails = array(
                    array(
                        'id' => 0,
                        'attachment' => $fileName,
                        'path' => implode('/', $fileComponents),
                        'size' => filesize($pdfFileName),
                        'type' => 'pdf',
                        'nondeletable' => true
                    ),
                    array(
                        'id' => 1,
                        'attachment' => $fileName1,
                        'path' => implode('/', $fileComponents1),
                        'size' => filesize($pdfFileName1),
                        'type' => 'pdf',
                        'nondeletable' => false
                    ),
                    array(
                        'id' => 2,
                        'attachment' => $fileName2,
                        'path' => implode('/', $fileComponents2),
                        'size' => filesize($pdfFileName2),
                        'type' => 'pdf',
                        'nondeletable' => false
                    ),
                    array(
                        'id' => 3,
                        'attachment' => $fileName3,
                        'path' => implode('/', $fileComponents3),
                        'size' => filesize($pdfFileName3),
                        'type' => 'pdf',
                        'nondeletable' => false
                    )
                );

                if ($tarif == "inter") {
                    array_push($attachmentDetails, array(
                        'id' => 4,
                        'attachment' => $fileName4,
                        'path' => implode('/', $fileComponents4),
                        'size' => filesize($pdfFileName4),
                        'type' => 'pdf',
                        'nondeletable' => false
                    ));
                }
            }
            $this->populateTo($request);
            $viewer->assign('ATTACHMENTS', $attachmentDetails);
            echo $viewer->view('ComposeEmailFormQuotes.tpl', 'Emails', true);
        } /* unicnfsecrm_022020_07 - end */ else {
            if ($relance == 1) {

                $pdfFileName = $recordModel->getPDFFileName();
                $fileComponents = explode('/', $pdfFileName);
                $fileName = $fileComponents[count($fileComponents) - 1];
                array_pop($fileComponents);

                $sql1 = "SELECT vtiger_activity.activityid 
            FROM vtiger_activity 
            INNER JOIN vtiger_salesorder on vtiger_salesorder.session = vtiger_activity.activityid
            INNER JOIN vtiger_invoice on vtiger_invoice.salesorderid = vtiger_salesorder.salesorderid
            where vtiger_invoice.invoiceid = ?";
                $params1 = array($inventoryRecordId);
                $result1 = $adb->pquery($sql1, $params1);

                $activityid = $adb->query_result($result1, 0, 'activityid');

                $documents = array();

                $sql2 = "SELECT vtiger_senotesrel.notesid,filename
                FROM vtiger_senotesrel
                INNER JOIN vtiger_notes on vtiger_notes.notesid = vtiger_senotesrel.notesid
                INNER JOIN vtiger_crmentity on vtiger_crmentity.crmid = vtiger_senotesrel.notesid
                WHERE vtiger_senotesrel.crmid = ? and deleted = 0 ";
                $params2 = array($activityid);
                $result2 = $adb->pquery($sql2, $params2);
                $num_rows = $adb->num_rows($result2);

                for ($i = 0; $i < $num_rows; $i++) {
                    $notesid = $adb->query_result($result2, $i, 'notesid');
                    $nom = $adb->query_result($result2, $i, 'filename');

//                $r = print_r($nom, true);
//                $monfichier = fopen('debug_test.txt', 'a+');
//                fputs($monfichier, "\n" . "value" . $r);
//                fclose($monfichier);

                    if (strstr($nom, "Feuille_Emargement.pdf")) {
                        $attachmentsid = $notesid + 1;
                    } else {
                        $attachmentsid = "";
                    }
                }

                if ($attachmentsid != '') {
                    $sql4 = "SELECT attachmentsid,path FROM vtiger_attachments WHERE vtiger_attachments.attachmentsid = ? ";
                    $params4 = array($attachmentsid);
                    $result4 = $adb->pquery($sql4, $params4);
                    $path = $adb->query_result($result4, 0, 'path');
                } else {
                    $path = '';
                }

                $attachmentDetails = array(
                    array(
                        'attachment' => $fileName,
                        'path' => implode('/', $fileComponents),
                        'size' => filesize($pdfFileName),
                        'type' => 'pdf',
                        'nondeletable' => true
                    )
                );

                if ($num_rows > 0) {
                    array_push($attachmentDetails, array(
                        'attachment' => $attachmentsid . '_' . $activityid . '_Feuille_Emargement.pdf',
                        'path' => $path,
                        'size' => filesize($path . $attachmentsid . '_' . $activityid . '_Feuille_Emargement.pdf'),
                        'type' => 'pdf',
                        'nondeletable' => true
                    ));
                }

                $this->populateTo($request);
                $viewer->assign('ATTACHMENTS', $attachmentDetails);

                // unicnfsecrm_mod_21 inserer le template d'email facture impayer
                $sqlinvoice = "SELECT invoice_no,invoicedate,total,duedate,vtiger_invoicecf.cf_1185,vtiger_accountbillads.bill_city,vtiger_accountbillads.bill_code,vtiger_accountbillads.bill_street,vtiger_account.accountname 
                    FROM vtiger_invoice
                    INNER JOIN vtiger_invoicecf on vtiger_invoicecf.invoiceid = vtiger_invoice.invoiceid
                    INNER JOIN vtiger_accountbillads on vtiger_accountbillads.accountaddressid = vtiger_invoice.accountid
                    INNER JOIN vtiger_account on vtiger_account.accountid = vtiger_invoice.accountid 
                     WHERE vtiger_invoice.invoiceid = ? ";
                $paramsinvoice = array($inventoryRecordId);
                $resultinvoice = $adb->pquery($sqlinvoice, $paramsinvoice);

                $invoice_no = $adb->query_result($resultinvoice, 0, 'invoice_no');
                $invoicedate = $adb->query_result($resultinvoice, 0, 'invoicedate');
                $total = $adb->query_result($resultinvoice, 0, 'total');
                $duedate = $adb->query_result($resultinvoice, 0, 'duedate');
                $etat_echeance = $adb->query_result($resultinvoice, 0, 'cf_1185');
                $etat_echeance = html_entity_decode($etat_echeance);
                $nom_client = $adb->query_result($resultinvoice, 0, 'accountname');
                $adresse_client = $adb->query_result($resultinvoice, 0, 'bill_street');
                $ville_client = $adb->query_result($resultinvoice, 0, 'bill_city');
                $cp_client = $adb->query_result($resultinvoice, 0, 'bill_code');


                $sqltemplate = "SELECT vtiger_emailtemplates.body FROM vtiger_emailtemplates WHERE vtiger_emailtemplates.templateid = ? ";
                $paramstemplate = array(31);
                $resulttemplate = $adb->pquery($sqltemplate, $paramstemplate);
                $description = $adb->query_result($resulttemplate, 0, 'body');

                $invoicedate = date("d-m-Y", strtotime($invoicedate));
                $total = number_format($total, 2, '.', '');
                $duedate = date("d-m-Y", strtotime($duedate));

                $body = str_replace('num_facture', $invoice_no, $description);
                $body = str_replace('date_facture', $invoicedate, $body);
                $body = str_replace('montant_facture', $total, $body);
                $body = str_replace('date_echeance', $duedate, $body);
                $body = str_replace('nom_client', $nom_client, $body);
                $body = str_replace('adresse_client', $adresse_client, $body);
                $body = str_replace('ville_client', $ville_client, $body);
                $body = str_replace('cp_client', $cp_client, $body);

                if ($etat_echeance == 'Dépassé de 7 jours') {
                    $subject = 'Facture impayée - Relance 1';
//                $sql = "UPDATE vtiger_invoicecf SET cf_1189 = ? WHERE vtiger_invoicecf.invoiceid = ? ";
//                $params = array(1, $inventoryRecordId);
//                $result = $adb->pquery($sql, $params);
                } elseif ($etat_echeance == 'Dépassé de 14 jours') {
                    $subject = 'Facture impayée - Relance 2';
//                $sql = "UPDATE vtiger_invoicecf SET cf_1191 = ? WHERE vtiger_invoicecf.invoiceid = ? ";
//                $params = array(1, $inventoryRecordId);
//                $result = $adb->pquery($sql, $params);
                } elseif ($etat_echeance == 'Dépassé de 30 jours') {
                    $subject = 'Facture impayée - Relance 3';
//                $sql = "UPDATE vtiger_invoicecf SET cf_1193 = ? WHERE vtiger_invoicecf.invoiceid = ? ";
//                $params = array(1, $inventoryRecordId);
//                $result = $adb->pquery($sql, $params);
                }

                $viewer->assign('SUBJECT', $subject);
                $viewer->assign('DESCRIPTION', $body);
                // fin unicnfsecrm_mod_21

                echo $viewer->view('ComposeEmailFormPayer.tpl', 'Emails', true);
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
    }

}

?>
