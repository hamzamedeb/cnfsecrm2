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

class Vtiger_PDF_ListeTOKENSHeaderViewer extends Vtiger_PDF_HeaderViewer {

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

            //$pdf->SetAutoPageBreak(TRUE, 30);
            //$pdf->SetTopMargin(20);

            $modelColumn0 = $modelColumns[0];
            //$apprenants = $modelColumn0['info_apprenants'];
            //var_dump($apprenants);
            $nombre_apprenant = $modelColumn0['info_apprenants']['nbr_apprenants'];


            $pdf->SetDrawColor(172, 172, 172);


            list($imageWidth, $imageHeight, $imageType, $imageAttr) = $parent->getimagesize(
                    "test/logo/logo-CNFSE-large.png");
            //division because of mm to px conversion
            $w = $imageWidth / 3;

            $w = $imageWidth;
            if ($w > 30) {
                $w = 32;
            }
            $h = $imageHeight;
            if ($h > 20) {
                $h = 12;
            }
            $pdf->Image("test/logo/logo-CNFSE-large.png", 10, 7, $w, $h);
            //column 1 num devis
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
            $x = 0;
            $y = 7;

            //titre
            $pdf->SetXY($x, $y);
            $pdf->SetFont($trebuchetbd, '', 14);
            $pdf->MultiCell(210, 5, 'Liste des Tickets Examen', '', 'C', 0, 1);

            //Detail
            $x = 10;
            $y = 30;


            $pdf->SetXY($x, $y);
            $pdf->SetFont($trebuchet, '', 9);
            $pdf->MultiCell(190, 5, 'Formateur : ', '', 'L', 0, 1);

            $pdf->SetXY($x + 20, $y);
            $pdf->SetFont($trebuchetbd, '', 9);
            $formateur = $modelColumn0['formateur'];
            $pdf->MultiCell(190, 5, $formateur, '', 'L', 0, 1);

            if ($client != "") {
                $x = 10;
                $pdf->SetXY($x, $y += 7);
                $pdf->SetFont($trebuchet, '', 9);
                $pdf->MultiCell(190, 5, 'Client : ', '', 'L', 0, 1);

                $pdf->SetXY($x + 20, $y);
                $pdf->SetFont($trebuchetbd, '', 9);
                $client = $modelColumn0['info_client']['accountname'];
                $pdf->MultiCell(190, 5, $client, '', 'L', 0, 1);
            }
            //2eme partie
            $x = 120;
            $y = 30;
            $pdf->SetXY($x, $y);
            $pdf->SetFont($trebuchet, '', 9);
            $pdf->MultiCell(190, 5, 'Dates : ', '', 'L', 0, 1);

            $pdf->SetXY($x + 20, $y);
            $pdf->SetFont($trebuchetbd, '', 9);
            //date de formation
            $date_debut_formation = $modelColumn0['date_debut_formation'];
            $date_fin_formations = $modelColumn0['date_fin_formations'];
            $date = 'du ' . $date_debut_formation . ' au ' . $date_fin_formations;
            $pdf->MultiCell(190, 5, $date, '', 'L', 0, 1);

            //nom formation
            $x = 120;
            $pdf->SetXY($x, $y += 7);
            $pdf->SetFont($trebuchet, '', 9);
            $pdf->MultiCell(190, 5, 'Intitulé : ', '', 'L', 0, 1);

            $pdf->SetXY($x + 20, $y);
            $pdf->SetFont($trebuchetbd, '', 9);
            $nom_formation = $modelColumn0['subject'];
            $pdf->MultiCell(60, 5, $nom_formation, '', 'L', 0, 1);

            $y = 55;
            $x = 22;
            $pdf->SetXY($x, $y);
            $pdf->SetFont($trebuchet, '', 12);
            //$pdf->MultiCell(190, 5, 'Sur une échelle de valeur de 0 à 10 , indiquez vos appréciations sur les sujets suivants :', '', 'L', 0, 1);

            /* $tableau1 = '<table pagebreak="true" style="width:780px;bordercolor:#bfbfbf" valign="middle" border="1" cellpadding="2">
              <tr pagebreak="true">
              <td align="center" style="width:120px;height:30px;background-color:#e8e8e8;">Nom</td>
              <td align="center" style="height:30px;width:120px;background-color:#e8e8e8;">Prenom</td>
              <td align="center" style="height:30px;width:120px;background-color:#e8e8e8;">Type Ticket Examen</td>
              <td align="center" style="height:30px;width:100px;background-color:#e8e8e8;">Ticket Examen</td>
              </tr>';
              $nombre_apprenant = $modelColumn0['info_apprenants']['nbr_apprenants'];
              for ($i = 0; $i < $nombre_apprenant; $i++) {
              $nom_apprenant = $modelColumn0['info_apprenants'][$i]['lastname'];
              $prenom_apprenant = $modelColumn0['info_apprenants'][$i]['firstname'];
              $typeticketexamen = $modelColumn0['info_apprenants'][$i]['type_tokens'];
              $ticketexamen = $modelColumn0['info_apprenants'][$i]['ticket_examen'];
              $tableau1 .= '<tr pagebreak="true">
              <td align="center" style="height:30px;width:120px;">' . $nom_apprenant . '</td>
              <td align="center" style="height:30px;width:120px;">' . $prenom_apprenant . '</td>
              <td align="center" style="height:30px;width:120px;">' . $typeticketexamen . '</td>
              <td align="center" style="height:30px;width:100px;">' . $ticketexamen . '</td>
              </tr>';
              }
              $tableau1 .= '</table>';
              $pdf->writeHTMLCell(200, 100, $x, $y += 5, $tableau1, ''); */
            /* uni_cnfsecrm - v2 - modif 139 - DEBUT */
            $pdf->SetFillColor(232, 232, 232);
            $pdf->MultiCell(41, 10, "Nom", 1, 'C', 1, 1, $x, $y += 5);
            $pdf->MultiCell(41, 10, "Prenom", 1, 'C', 1, 1, $x += 41, $y);
            $pdf->MultiCell(41, 10, "Type Ticket Examen", 1, 'C', 1, 1, $x += 41, $y);
            $pdf->MultiCell(41, 10, "Ticket Examen", 1, 'C', 1, 1, $x += 41, $y);

            $nombre_apprenant = $modelColumn0['info_apprenants']['nbr_apprenants'];
            for ($i = 0; $i < $nombre_apprenant; $i++) {
                $x = 22;
                $nom_apprenant = $modelColumn0['info_apprenants'][$i]['lastname'];
                $prenom_apprenant = $modelColumn0['info_apprenants'][$i]['firstname'];
                $typeticketexamen = $modelColumn0['info_apprenants'][$i]['type_tokens'];
                $ticketexamen = $modelColumn0['info_apprenants'][$i]['ticket_examen'];

                $pdf->MultiCell(41, 10, $nom_apprenant, 1, 'C', 0, 1, $x, $y += 10);
                $pdf->MultiCell(41, 10, $prenom_apprenant, 1, 'C', 0, 1, $x += 41, $y);
                $pdf->MultiCell(41, 10, $typeticketexamen, 1, 'C', 0, 1, $x += 41, $y);
                $pdf->MultiCell(41, 10, $ticketexamen, 1, 'C', 0, 1, $x += 41, $y);
                if ($y >= 250){
                    $pdf->addPage();
                    $y = 10;
                }
            }
            /* uni_cnfsecrm - v2 - modif 139 - FIN */
        }
    }

}
