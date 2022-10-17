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

class Vtiger_PDF_InventorySalesHeaderViewer extends Vtiger_PDF_HeaderViewer {

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
            $pdf->setPageFormat('A5', 'L');

            // font calibri simple
            /* uni_cnfsecrm - v2 - modif 100 - DEBUT */  
            $calibri = TCPDF_FONTS2::addTTFfont('test/font/Calibri/calibri.ttf', 'TrueTypeUnicode', '', 96);
            //font calibri bold
            $calibrib = TCPDF_FONTS2::addTTFfont('test/font/Calibri/calibrib.ttf', 'TrueTypeUnicode', '', 96);
            //font calibri italic
            $calibrii = TCPDF_FONTS2::addTTFfont('test/font/Calibri/calibrii.ttf', 'TrueTypeUnicode', '', 96);
            //font calibri bold/italic
            $calibriz = TCPDF_FONTS2::addTTFfont('test/font/Calibri/calibriz.ttf', 'TrueTypeUnicode', '', 96);
            /* uni_cnfsecrm - v2 - modif 100 - FIN */
            $x = 0;
            $y = 10;
           
            //titre
            $pdf->SetXY($x, $y);
            $pdf->SetFont($calibrib, '', 24);
            $pdf->MultiCell(190, 5, 'C.N.F.S.E', '', 'R', 0, 1);

            //societe
            $x = 10;
            $pdf->SetXY($x, $y += 10);
            $pdf->SetFont($calibri, '', 26);
            $pdf->MultiCell(190, 5, 'Société :', '', 'L', 0, 1);

            $pdf->SetXY($x + 35, $y);
            $pdf->SetFont($calibri, '', 26);
            $societe = $modelColumn0['info_client']['accountname'];
            $pdf->MultiCell(190, 5, $societe, '', 'L', 0, 1);

            //Nom du Contact

            $x = 10;
            $pdf->SetXY($x, $y += 15);
            $pdf->SetFont($calibri, '', 18);
            $pdf->MultiCell(190, 5, 'Nom du Contact :', '', 'L', 0, 1);

            $pdf->SetXY($x + 50, $y);
            $pdf->SetFont($calibri, '', 18);
            $nom_contact = $modelColumn0['info_contact']['nom_contact'];
            $prenom_contact = $modelColumn0['info_contact']['prenom_contact'];
            $pdf->MultiCell(190, 5, $nom_contact.' '.$prenom_contact, '', 'L', 0, 1);
            
            //Portable

            $x = 10;
            $pdf->SetXY($x, $y += 10);
            $pdf->SetFont($calibri, '', 18);
            $pdf->MultiCell(190, 5, 'Port :', '', 'L', 0, 1);

            $pdf->SetXY($x + 15, $y);
            $pdf->SetFont($calibri, '', 18);
            $portable = "";
            $mobile_contact = $modelColumn0['info_contact']['phone_contact'];
            $pdf->MultiCell(190, 5, $mobile_contact, '', 'L', 0, 1);
            
            //telephone

            $x = 10;
            $pdf->SetXY($x, $y += 10);
            $pdf->SetFont($calibri, '', 18);
            $pdf->MultiCell(190, 5, 'Tél :', '', 'L', 0, 1);

            $pdf->SetXY($x + 15, $y);
            $pdf->SetFont($calibri, '', 18);
            $phone_contact = $modelColumn0['info_contact']['mobile_contact'];
            $pdf->MultiCell(190, 5, $phone_contact, '', 'L', 0, 1);
            
            //formation

            $x = 10;
            $pdf->SetXY($x, $y += 15);
            $pdf->SetFont($calibri, '', 18);
            $pdf->MultiCell(190, 5, 'Formation :', '', 'L', 0, 1);

            $pdf->SetXY($x + 32, $y);
            $pdf->SetFont($calibri, '', 18);
            $nom_formation = $modelColumn0['subject'];
            $pdf->MultiCell(190, 5, $nom_formation, '', 'L', 0, 1);
            
            //Lieu

            $x = 10;
            $pdf->SetXY($x, $y += 15);
            $pdf->SetFont($calibri, '', 18);
            $pdf->MultiCell(190, 5, 'Lieu :', '', 'L', 0, 1);
             
            $pdf->SetXY($x += 15, $y);
            $pdf->SetFont($calibri, '', 18);
            $lieu = $modelColumn0['ville_formation'];
            $pdf->MultiCell(190, 5, $lieu.';', '', 'L', 0, 1);
            //Date

            $pdf->SetXY($x += 20, $y);
            $pdf->SetFont($calibri, '', 18);
            $date_debut = $modelColumn0['date_debut_formation'];
            $date_fin = $modelColumn0['date_fin_formations'];
            $pdf->MultiCell(190, 5, 'Dates du '. $date_debut.' au '.$date_fin, '', 'L', 0, 1);
            
            //Date envoie
            $x = 9;
            $pdf->SetXY($x, $y += 10);
            $pdf->SetFont($calibri, '', 18);
            $date_envoie = $modelColumn0['date_debut_formation'];
            $pdf->MultiCell(190, 5, 'Date d’envoi convention & facture : '. $date_envoie, '', 'L', 0, 1);
            
            //choix
            $x = 16;
            $pdf->SetXY($x, $y += 15);
            $pdf->SetFont($calibri, '', 18);
            $pdf->MultiCell(190, 5, ' Convention        Convocation        Paiement      Date de Naissance :', '', 'L', 0, 1);
            
            $x = 12;
            //carreau 1
            $pdf->SetXY($x, $y + 3);
            $pdf->SetFont($calibri, '', 3);
            $pdf->MultiCell(4, 4, '', 'TBLR', 'L', 0, 1);
            
            //carreau 2
            $pdf->SetXY($x += 42, $y + 3);
            $pdf->SetFont($calibri, '', 3);
            $pdf->MultiCell(4, 4, '', 'TBLR', 'L', 0, 1);
            
            //carreau 3
            $pdf->SetXY($x += 44, $y + 3);
            $pdf->SetFont($calibri, '', 3);
            $pdf->MultiCell(4, 4, '', 'TBLR', 'L', 0, 1);
            
            //carreau 4
            $pdf->SetXY($x += 34, $y + 3);
            $pdf->SetFont($calibri, '', 3);
            $pdf->MultiCell(4, 4, '', 'TBLR', 'L', 0, 1);
        }
    }

}
