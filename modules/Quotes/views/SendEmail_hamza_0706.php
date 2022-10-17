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
/* uni_cnfsecrm - v2 - modif 156 - DEBUT */
        $sqlTypeFormation = "SELECT vtiger_service.servicecategory,tarif , cf_1480
                    FROM vtiger_service
                    INNER JOIN vtiger_inventoryproductrel on vtiger_inventoryproductrel.productid = vtiger_service.serviceid
                     INNER JOIN vtiger_servicecf on vtiger_servicecf.serviceid = vtiger_service.serviceid
                    WHERE vtiger_inventoryproductrel.id = ?";
        $paramsTypeFormation = array($inventoryRecordId);

        $resultTypeFormation = $adb->pquery($sqlTypeFormation, $paramsTypeFormation);
        $categorieFormation = $adb->query_result($resultTypeFormation, 0, 'servicecategory');
        $tarif = $adb->query_result($resultTypeFormation, 0, 'tarif');
        $pdfDevis = $adb->query_result($resultTypeFormation, 0, 'cf_1480');

        if ($pdfDevis == "https://cnfse.fr/wp-content/uploads/2018/02/3a1-methode-haccp.pdf") {
            $pdfDevis = "3A1 Méthode Haccp : <a href='https://cnfse.fr/wp-content/uploads/2018/02/3a1-methode-haccp.pdf'>https://cnfse.fr/wp-content/uploads/2018/02/3a1-methode-haccp.pdf</a>";
        } else if ($pdfDevis == "https://cnfse.fr/wp-content/uploads/2018/02/3b1-habilitation-b0-h0-h0v-elearning.pdf") {
            $pdfDevis = "3B1 Habilitation B0 H0 H0v e-learning : <a href='https://cnfse.fr/wp-content/uploads/2018/02/3b1-habilitation-b0-h0-h0v-elearning.pdf'>https://cnfse.fr/wp-content/uploads/2018/02/3b1-habilitation-b0-h0-h0v-elearning.pdf</a>";
        } else if ($pdfDevis == "https://cnfse.fr/wp-content/uploads/2018/02/3b1-habilitation-b0-h0-h0v.pdf") {
            $pdfDevis = "3B1 Habilitation B0 H0 H0v : <a href='https://cnfse.fr/wp-content/uploads/2018/02/3b1-habilitation-b0-h0-h0v.pdf'>https://cnfse.fr/wp-content/uploads/2018/02/3b1-habilitation-b0-h0-h0v.pdf</a>";
        } else if ($pdfDevis == "https://cnfse.fr/wp-content/uploads/2018/02/3b2-habilitation-bs-be-he.pdf") {
            $pdfDevis = "3B2 Habilitation BS BE HE : <a href='https://cnfse.fr/wp-content/uploads/2018/02/3b2-habilitation-bs-be-he.pdf'>https://cnfse.fr/wp-content/uploads/2018/02/3b2-habilitation-bs-be-he.pdf</a>";
        } else if ($pdfDevis == "https://cnfse.fr/wp-content/uploads/2018/02/3b3-habilitation-b1-b2-bc-br.pdf") {
            $pdfDevis = "3B3 Habilitation B1 B2 BC BR : <a href='https://cnfse.fr/wp-content/uploads/2018/02/3b3-habilitation-b1-b2-bc-br.pdf'>https://cnfse.fr/wp-content/uploads/2018/02/3b3-habilitation-b1-b2-bc-br.pdf</a>";
        } else if ($pdfDevis == "https://cnfse.fr/wp-content/uploads/2018/02/3b4-habilitation-b1-b2-bc-br-h1-h2.pdf") {
            $pdfDevis = "3B4 Habilitation B1 B2 BC BR H1 H2 : <a href='https://cnfse.fr/wp-content/uploads/2018/02/3b4-habilitation-b1-b2-bc-br-h1-h2.pdf'>https://cnfse.fr/wp-content/uploads/2018/02/3b4-habilitation-b1-b2-bc-br-h1-h2.pdf</a>";
        } else if ($pdfDevis == "https://cnfse.fr/wp-content/uploads/2018/02/3d1-aipr-operateur.pdf") {
            $pdfDevis = "3D1 AIPR Opérateur : <a href='https://cnfse.fr/wp-content/uploads/2018/02/3d1-aipr-operateur.pdf'>https://cnfse.fr/wp-content/uploads/2018/02/3d1-aipr-operateur.pdf</a>";
        } else if ($pdfDevis == "https://cnfse.fr/wp-content/uploads/2018/02/3d2-aipr-encadrant.pdf") {
            $pdfDevis = "3D2 AIPR Encadrant : <a href='https://cnfse.fr/wp-content/uploads/2018/02/3d2-aipr-encadrant.pdf'>https://cnfse.fr/wp-content/uploads/2018/02/3d2-aipr-encadrant.pdf</a>";
        } else if ($pdfDevis == "https://cnfse.fr/wp-content/uploads/2018/02/3d3-aipr-concepteur.pdf") {
            $pdfDevis = "3D3 AIPR Concepteur : <a href='https://cnfse.fr/wp-content/uploads/2018/02/3d3-aipr-concepteur.pdf'>https://cnfse.fr/wp-content/uploads/2018/02/3d3-aipr-concepteur.pdf</a>";
        } else if ($pdfDevis == "https://cnfse.fr/wp-content/uploads/2018/02/3e1-sst.pdf") {
            $pdfDevis = "3e1 SST : <a href='https://cnfse.fr/wp-content/uploads/2018/02/3e1-sst.pdf'>https://cnfse.fr/wp-content/uploads/2018/02/3e1-sst.pdf</a>";
        } else if ($pdfDevis == "https://cnfse.fr/wp-content/uploads/2018/02/3e2-sst-mac.pdf") {
            $pdfDevis = "3e2 SST MAC : <a href='https://cnfse.fr/wp-content/uploads/2018/02/3e2-sst-mac.pdf'>https://cnfse.fr/wp-content/uploads/2018/02/3e2-sst-mac.pdf</a>";
        }
        
        if ($categorieFormation == 'HABILITATIONS' || $categorieFormation == 'HYGIENE' || $categorieFormation == 'AIPR' || $categorieFormation == 'SECURITE TRAVAIL') {
            if ($categorieFormation == 'HABILITATIONS') {
                $body .= "Session Habilitation 2021 :  <a href='https://cnfse.fr/wp-content/uploads/2018/02/session-habilitation-2021.pdf'>https://cnfse.fr/wp-content/uploads/2018/02/session-habilitation-2021.pdf</a>  <br/>";
                $body .= $pdfDevis;
            } else if ($categorieFormation == 'HYGIENE') {
                $body .= "Session Haccp 2021 : <a href='https://cnfse.fr/wp-content/uploads/2018/02/session-haccp-2021.pdf'>https://cnfse.fr/wp-content/uploads/2018/02/session-haccp-2021.pdf</a> <br/>";
                $body .= $pdfDevis;
            } else if ($categorieFormation == 'AIPR') {
                $body .= "Session AIPR 2021 : <a href='https://cnfse.fr/wp-content/uploads/2018/02/session-aipr-2021.pdf'>https://cnfse.fr/wp-content/uploads/2018/02/session-aipr-2021.pdf</a> <br/>";
                $body .= $pdfDevis;
            }else if ($categorieFormation == 'SECURITE TRAVAIL') {
                $body .= "Session SST 2021 : <a href='https://cnfse.fr/wp-content/uploads/2018/02/session-sst-2021.pdf'>https://cnfse.fr/wp-content/uploads/2018/02/session-sst-2021.pdf</a> <br/>";
                $body .= $pdfDevis;
            }
            
            $pdfFileName = $recordModel->getPDFFileName();
            $fileComponents = explode('/', $pdfFileName);
            $fileName = $fileComponents[count($fileComponents) - 1];
            array_pop($fileComponents);

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

            $viewer->assign('DESCRIPTION', $body);
            $viewer->assign('ATTACHMENTS', $attachmentDetails);
            echo $viewer->view('ComposeEmailFormQuotes.tpl', 'Emails', true);
            /* uni_cnfsecrm - v2 - modif 156 - FIN */
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
