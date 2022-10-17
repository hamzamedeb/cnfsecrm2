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
include_once 'libraries/tcpdf/tcpdf_fonts.php';
include_once 'libraries/tcpdf/tcpdf_static.php';

//unicnfsecrm_mod_40
class Vtiger_PDF_SignaletiqueHeaderViewer extends Vtiger_PDF_HeaderViewer {

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

            $pdf->setPageFormat('A5', 'L');
            $pdf->setPageOrientation('LANDSCAPE', '', 0);

            // Column 1
            $offsetX = 5;

            $modelColumn0 = $modelColumns[0];
            /* uni_cnfsecrm - v2 - modif 100 - DEBUT */
            $calibri = TCPDF_FONTS2::addTTFfont('test/font/Calibri/calibri.ttf', 'TrueTypeUnicode', '', 96);
            //font calibri bold
            $calibrib = TCPDF_FONTS2::addTTFfont('test/font/Calibri/calibrib.ttf', 'TrueTypeUnicode', '', 96);
            //font calibri italic
            $calibrii = TCPDF_FONTS2::addTTFfont('test/font/Calibri/calibrii.ttf', 'TrueTypeUnicode', '', 96);
            //font calibri bold/italic
            $calibriz = TCPDF_FONTS2::addTTFfont('test/font/Calibri/calibriz.ttf', 'TrueTypeUnicode', '', 96);
            /* uni_cnfsecrm - v2 - modif 100 - FIN */
            $x = 170;
            $y = 10;

            $pdf->SetFont($calibrib, '', 20);
            $pdf->SetXY($x, $y);
            $pdf->MultiCell(40, 5, 'C.N.F.S.E', '', 'L', 0, 1);

            $nomFormation = html_entity_decode($modelColumn0["detailFicheSignaletique"]["servicename"]);
            $nomClient = $modelColumn0["detailFicheSignaletique"]["accountname"];
            $telClient = $modelColumn0["detailFicheSignaletique"]["phoneclient"];
            $salutationContact = $modelColumn0["detailFicheSignaletique"]["salutation"];
            $nomContact = $modelColumn0['detailFicheSignaletique']['firstname'];
            $prenomContact = $modelColumn0['detailFicheSignaletique']['lastname'];
            $telContact = $modelColumn0['detailFicheSignaletique']['phonecontact'];
            $testContactPrincipal = $modelColumn0['detailFicheSignaletique']['testContactPrincipal'];
            if ($testContactPrincipal == 1) {
                $phone = $telContact;
            } else {
                $phone = $telClient;
            }

            //nom de client
            $x = 10;
            $y = 30;

            $pdf->SetXY($x, $y);
            $pdf->SetFont($calibri, '', 20);
            $pdf->MultiCell(200, 5, 'Société : ' . $nomClient, '', 'L', 0, 1);
            //nom du contact
            $pdf->SetXY($x, $y += 20);
            $pdf->MultiCell(200, 5, 'Nom du contact : ' . $salutationContact . ' ' . $nomContact . ' ' . $prenomContact, '', 'L', 0, 1);

            //num du tel
            $pdf->SetXY($x, $y += 20);
            $tel = '07 58 56 66 30';
            $pdf->MultiCell(200, 5, 'Tél : ' . $phone, '', 'L', 0, 1);

            //nom du formation
            $pdf->SetXY($x, $y += 20);
            $pdf->MultiCell(200, 5, 'Formation : ' . $nomFormation, '', 'L', 0, 1);
        }
    }

}
