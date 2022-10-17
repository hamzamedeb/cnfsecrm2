<?php

/* +**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ********************************************************************************** */
include_once dirname(__FILE__) . '/../viewers/HeaderViewer.php';

class Vtiger_PDF_InventoryInvoHeaderViewer extends Vtiger_PDF_HeaderViewer {

    function totalHeight($parent) {
        $height = 100;

        if ($this->onEveryPage)
            return $height;
        if ($this->onFirstPage && $parent->onFirstPage())
            $height;
        return 0;
    }

    function display($parent) {
        $pdf = $parent->getPDF();
        $headerFrame = $parent->getHeaderFrame();
        if ($this->model) {
            $headerColumnWidth = $headerFrame->w / 3.0;
            $modelColumns = $this->model->get('columns');
            // Column 1
            $offsetX = 5;
            $modelColumn0 = $modelColumns[0];
            list($imageWidth, $imageHeight, $imageType, $imageAttr) = $parent->getimagesize("test/logo/logo-CNFSE.png");
            //division because of mm to px conversion
            $w = $imageWidth / 3;
            //var_dump($imageWidth);
            $w = $imageWidth;
            if ($w > 30) {
                $w = 70;
            }
            $h = $imageHeight;
            if ($h > 20) {
                $h = 24;
            }
            $pdf->Image("test/logo/logo-CNFSE-large.png", $headerFrame->x, 5, $w, $h);

            /* uni_cnfsecrm - v2 - modif 158 - DEBUT */
            $pdf->Image("test/logo/LogoQualiopi-Marianne.jpg", 135, 6, 45, 23);
            $pdf->Image("test/logo/compte.png", 180, 10, 15, 15);
            /* uni_cnfsecrm - v2 - modif 158 - FIN */

            $y = 0;

            // font trebuchet simple
            /* uni_cnfsecrm - v2 - modif 100 - DEBUT */
            $trebuchet = TCPDF_FONTS2::addTTFfont('test/font/Trebuchet.ttf', 'TrueTypeUnicode', '', 96);
            //font trebuchet italic
            $trebuchetmsitalic = TCPDF_FONTS2::addTTFfont('test/font/trebuchetmsitalic.ttf', 'TrueTypeUnicode', '', 96);
            //font trebuchet bold
            $trebuchetbd = TCPDF_FONTS2::addTTFfont('test/font/trebucbd.ttf', 'TrueTypeUnicode', '', 96);
            //font trebuchet bold/italic
            $trebuchetbi = TCPDF_FONTS2::addTTFfont('test/font/trebuchetbi.ttf', 'TrueTypeUnicode', '', 96);
            /* uni_cnfsecrm - v2 - modif 100 - FIN */
            //detail 
            //partie 2

            $pdf->SetFont($trebuchet, '', 11);
            $type = $modelColumn0['type'];
            $client = $modelColumn0['info_client'];
            $nom_client = $modelColumn0['info_client']['accountname'];
            $adresse_client = $modelColumn0['info_client']['adresse'];
            $ville_client = $modelColumn0['info_client']['ville'];
            $cp_client = $modelColumn0['info_client']['cp'];
            $numero_facture = $modelColumn0['numero_facture'];
            $type_facture = substr($numero_facture, 0, 2);
            $facture_parent = $modelColumn0['facture_parent'];
            $ref_client = "";
            $id_client = "";

            $titre_contact = html_entity_decode($modelColumn0['info_contact']['titre_contact']);
            $nom_contact = html_entity_decode($modelColumn0['info_contact']['nom_contact']);
            $prenom_contact = html_entity_decode($modelColumn0['info_contact']['prenom_contact']);
            if ($type == 'Client') {

                $pdf->SetXY(110, $y += 50);
                $contentHeight = $pdf->GetStringHeight($nom_client, 100);
                $pdf->MultiCell(100, $contentHeight, $nom_client, 0, 'L', 0, 1);
                if ($nom_contact != "" || $prenom_contact != "") {
                    if ($contentHeight == 0) {
                        $contentHeight = 5;
                    }
                    $pdf->SetXY(110, $y += $contentHeight);
                    $contentHeight = $pdf->GetStringHeight($titre_contact . ' ' . $prenom_contact . ' ' . $nom_contact, 100);

                    $salutation = (trim($titre_contact) != "") ? $titre_contact . ' ' : "";
                    $pdf->MultiCell(100, $contentHeight, $salutation . $prenom_contact . ' ' . $nom_contact, 0, 'L', 0, 1);
                }

                if ($contentHeight == 0) {
                    $contentHeight = 5;
                }
                $pdf->SetXY(110, $y += $contentHeight);
                $contentHeight = $pdf->GetStringHeight($adresse_client, 100);
                $pdf->MultiCell(100, $contentHeight, $adresse_client, 0, 'L', 0, 1);

                if ($contentHeight == 0) {
                    $contentHeight = 5;
                }
                $pdf->SetXY(110, $y += $contentHeight);
                $contentHeight = $pdf->GetStringHeight($cp_client . ' ' . $ville_client, 100);
                $pdf->MultiCell(100, $contentHeight, $cp_client . ' ' . $ville_client, 0, 'L', 0, 1);
            }

            if ($type_facture == 'AV') {
                $pdf->SetFont($trebuchetbd, '', 8);
                $pdf->SetXY(10, 40);
                $desc_avoir = "Avoir de la facture N°: " . $facture_parent;
                $contentHeight = $pdf->GetStringHeight($desc_avoir, 100);
                $pdf->MultiCell(100, $contentHeight, $desc_avoir, 0, 'L', 0, 1);

                $pdf->SetFont($trebuchetbd, '', 9);
            }
            if ($type == 'Financeur') {
                $vendor = $modelColumn0['info_vendor'];
                $nom_financeur = formatString($modelColumn0['info_vendor'][0]['vendorname']);
                $adresse_financeur = formatString($modelColumn0['info_vendor'][0]['street']);
                $ville_financeur = $modelColumn0['info_vendor'][0]['city'];
                $cp_financeur = $modelColumn0['info_vendor'][0]['postalcode'];

                $pdf->SetXY(110, $y += 50);
                $contentHeight = $pdf->GetStringHeight($nom_financeur, 100);
                $pdf->MultiCell(100, $contentHeight, $nom_financeur, 0, 'L', 0, 1);

                if ($contentHeight == 0) {
                    $contentHeight = 5;
                }
                $pdf->SetXY(110, $y += $contentHeight);
                $contentHeight = $pdf->GetStringHeight($adresse_financeur, 100);
                $pdf->MultiCell(100, $contentHeight, $adresse_financeur, 0, 'L', 0, 1);

                if ($contentHeight == 0) {
                    $contentHeight = 5;
                }
                $pdf->SetXY(110, $y += $contentHeight);
                $contentHeight = $pdf->GetStringHeight($cp_financeur . ' ' . $ville_financeur, 100);
                $pdf->MultiCell(100, $contentHeight, $cp_financeur . ' ' . $ville_financeur, 0, 'L', 0, 1);
                $pdf->SetFont($trebuchetbd, '', 8);
                $y = 0;
                $x = 0;

                $pdf->SetXY(10, $y += 40);
                $pdf->MultiCell(100, 50, 'Concerne : ', 0, 'L', 0, 1);

                if ($ref_client) {
                    $pdf->SetXY(10, $y += 4);
                    $contentHeight = $pdf->GetStringHeight($ref_client, 100);
                    $pdf->MultiCell(100, $contentHeight, 'REFERENCEMENT: ' . $ref_client, 0, 'L', 0, 1);
                }

                if ($nom_client != "") {
                    $pdf->SetXY(10, $y += 4);
                    $contentHeight = $pdf->GetStringHeight($id_client, 100);
                    $idclient = ($id_client != "") ? ' ID: ' . $id_client : "";
                    $pdf->MultiCell(100, $contentHeight, $nom_client . $idclient, 0, 'L', 0, 1);
                }

                if ($adresse_client != "") {
                    $pdf->SetXY(10, $y += 4);
                    $contentHeight = $pdf->GetStringHeight($adresse_client, 100);
                    $pdf->MultiCell(100, $contentHeight, $adresse_client, 0, 'L', 0, 1);
                }
                if ($cp_client && $ville_client) {
                    $pdf->SetXY(10, $y += 6);
                    $contentHeight = $pdf->GetStringHeight($ville_client, 100);
                    $pdf->MultiCell(100, $contentHeight, $cp_client . ' ' . $ville_client, 0, 'L', 0, 1);
                }
            }
            $pdf->SetFont($trebuchet, '', 11);


            //partie 1
            $invoice_no = $modelColumn0['invoice_no'];
            $invoice_date = $modelColumn0['invoice_date']['date_creation'];
            $invoice_date_s = new DateTime($invoice_date);
            $invoice_date_s = $invoice_date_s->format('Ymd');
            //var_dump($invoice_date);
            if ($invoice_date != null) {
                $invoice_date = formatDateFr($invoice_date);
            }

            $invoice_ref = $modelColumn0['invoice_ref'];
            $y = 70;
            $pdf->SetXY(10, $y);
            $contentHeight = $pdf->GetStringHeight($invoice_ref, 100);
            $pdf->MultiCell(100, $contentHeight, 'Réf : ' . $invoice_ref, 0, 'L', 0, 1);

            if ($contentHeight == 0) {
                $contentHeight = 10;
            }

            if ($type_facture == 'AV') {
                $type_facture = 'Avoir';
            } else {
                $type_facture = 'Facture';
            }
            $pdf->SetXY(10, $y += $contentHeight);
            $pdf->MultiCell(100, 5, $type_facture . ' N° ' . $numero_facture . ' du ' . $invoice_date, 0, 'L', 0, 1);

            $pdf->SetFont($trebuchet, '', 9);
            //$pdf->AddPage();
        }
    }

}
