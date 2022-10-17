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

class Vtiger_PDF_EnergetiqueHeaderViewer extends Vtiger_PDF_HeaderViewer {

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
        //$pdf->AddPage('L', 'LEGAL');
        $headerFrame = $parent->getHeaderFrame();

        if ($this->model) {
            $pdf->setPageFormat('A4', 'L');
            $headerColumnWidth = $headerFrame->w / 3.0;
            $modelColumns = $this->model->get('columns');
            // Column 1
            $offsetX = 5;
            $modelColumn0 = $modelColumns[0];
            //logo
            list($imageWidth, $imageHeight, $imageType, $imageAttr) = $parent->getimagesize(
                    "test/logo/logo-CNFSE.png");
            //division because of mm to px conversion
            $w = $imageWidth / 3;

            $w = $imageWidth;
            if ($w > 30) {
                $w = 35;
            }
            $h = $imageHeight;
            if ($h > 20) {
                $h = 12;
            }
            $pdf->Image("test/logo/logo-CNFSE-large.png", 10, 10, $w, $h);


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
            $x = 50;
            $y = 10;
            //titre
            $pdf->SetXY($x, $y);
            $pdf->SetFont($trebuchetbd, '', 17);
            $pdf->MultiCell(190, 5, 'FEUILLE DE PRESENCE', '', 'C', 0, 1);

            //nom formation 

            $x = 10;
            $y = 30;
            $nom_formation = $modelColumn0['subject'];
            $pdf->SetXY($x, $y);
            $pdf->SetFont($trebuchet, '', 10);
            $pdf->MultiCell(250, 5, 'Intitulé de la formation : ', '', 'L', 0, 1);

            $pdf->SetXY($x + 40, $y);
            $pdf->SetFont($trebuchetbd, '', 9);
            $pdf->MultiCell(250, 5, $nom_formation, '', 'L', 0, 1);

            //nom client   
            $nom_client = html_entity_decode($modelColumn0['info_client']['accountname']);
            $pdf->SetXY($x, $y += 10);
            $pdf->SetFont($trebuchet, '', 10);
            $pdf->MultiCell(250, 5, 'Client : ', '', 'L', 0, 1);

            $pdf->SetXY($x + 40, $y);
            $pdf->SetFont($trebuchetbd, '', 9);
            $pdf->MultiCell(250, 5, $nom_client, '', 'L', 0, 1);

            //Lieu de la formation
            $adresse_formation = html_entity_decode($modelColumn0['adresse_formation']);
            $cp_formation = $modelColumn0['cp_formation'];
            $ville_formation = html_entity_decode($modelColumn0['ville_formation']);

            $lieu_formation = "7 place henri IV 94220 CHARENTON LE PONT";
            $pdf->SetXY($x, $y += 8);
            $pdf->SetFont($trebuchet, '', 10);
            $pdf->MultiCell(250, 5, 'Lieu de la formation : ', '', 'L', 0, 1);

            $pdf->SetXY($x + 40, $y);
            $pdf->SetFont($trebuchetbd, '', 9);
            $pdf->MultiCell(250, 5, $adresse_formation . ' ' . $cp_formation . ' ' . $ville_formation, '', 'L', 0, 1);

            $pdf->SetFont($trebuchet, '', 8);
            $tableau = '<table style="width:780px;bordercolor:#bfbfbf" valign="middle" border="1">';
            $tableau .= '<tr valign="middle" align="center" style="border-color:#bfbfbf;">
                                <td style="width:70px;height:20px;background-color:#b7bece;"rowspan="4">Prènom/NOM</td>';
            for ($i = 0; $i < 6; $i++) {
                $date_formation = $modelColumn0['info_dates'][$i]["date_start"];
                if ($date_formation != null) {
                    $date = new DateTime($date_formation);
                    $date_formation = $date->format('d-m-Y');
                }
                if ($date_formation == '') {
                    $date_formation = '';
                } else {
                    $date_formation = 'Date : ' . $date_formation;
                }
                $tableau .= '       <td style="height:20px;" colspan="2" valign="middle">' . $date_formation . '</td>';
            }
            $tableau .= '       </tr>
                                <tr valign="middle" align="center">';
            for ($i = 0; $i < 6; $i++) {
                $heure_debut_matin = $modelColumn0['info_dates'][$i]["start_matin"];
                $heure_debut_apresmidi = $modelColumn0['info_dates'][$i]["start_apresmidi"];
                $hmatin = "Matin";
                $hmidi = "Après-midi";
                if ($heure_debut_matin == '') {
                    $hmatin = "";
                }
                if ($heure_debut_apresmidi == '') {
                    $hmidi = "";
                }
                $tableau .= '       <td style="height:15px;" valign="middle">' . $hmatin . '</td>
                                <td style="height:15px;" valign="middle">' . $hmidi . '</td>';
            }
            $tableau .= '       </tr>
                                <tr valign="middle" align="center">';
            for ($i = 0; $i < 6; $i++) {
                $heure_debut_matin = $modelColumn0['info_dates'][$i]["start_matin"];
                $heure_fin_matin = $modelColumn0['info_dates'][$i]["end_matin"];
                $heure_debut_apresmidi = $modelColumn0['info_dates'][$i]["start_apresmidi"];
                $heure_fin_apresmidi = $modelColumn0['info_dates'][$i]["end_apresmidi"];
                $matin = $heure_debut_matin . ' à ' . $heure_fin_matin;
                $midi = $heure_debut_apresmidi . ' à ' . $heure_fin_apresmidi;
                if ($heure_debut_matin == '') {
                    $matin = "";
                }
                if ($heure_debut_apresmidi == '') {
                    $midi = "";
                }


                $tableau .= '       <td style="height:15px;">' . $matin . '</td>
                                <td style="height:15px;">' . $midi . '</td>';
            }
            $tableau .= '       </tr>
                                <tr valign="middle" align="center">';
            for ($i = 0; $i < 6; $i++) {
                $heure_debut_matin = $modelColumn0['info_dates'][$i]["start_matin"];
                $heure_fin_matin = $modelColumn0['info_dates'][$i]["end_matin"];
                $heure_debut_apresmidi = $modelColumn0['info_dates'][$i]["start_apresmidi"];
                $heure_fin_apresmidi = $modelColumn0['info_dates'][$i]["end_apresmidi"];
                $matin = $heure_debut_matin . ' à ' . $heure_fin_matin;
                $midi = $heure_debut_apresmidi . ' à ' . $heure_fin_apresmidi;
                $d1 = new DateTime($heure_fin_matin);
                $d2 = new DateTime($heure_debut_matin);
                $interval = $d2->diff($d1);
                $duree_matin = $interval->format('%H:%I');


                $d1 = new DateTime($heure_fin_apresmidi);
                $d2 = new DateTime($heure_debut_apresmidi);
                $interval = $d2->diff($d1);
                $duree_midi = $interval->format('%H:%I');
                if ($duree_matin == '00:00') {
                    $duree_matin = "";
                }
                if ($duree_midi == '00:00') {
                    $duree_midi = "";
                }
                $tableau .= '       <td style="height:15px;">' . $duree_matin . '</td>
                                <td style="height:15px;">' . $duree_midi . '</td>';
            }
            $tableau .= '       </tr>';
            $nbr_apprenants = $modelColumn0['info_apprenants']['nbr_apprenants'];
            for ($a = 0; $a < $nbr_apprenants; $a++) {
                if ($a % 2 == 0) {
                    $color = "#eef0f2";
                } else {
                    $color = "#ffffe8";
                }
                $nom_apprenant = html_entity_decode($modelColumn0['info_apprenants'][$a]['firstname']);
                $prenom_apprenant = html_entity_decode($modelColumn0['info_apprenants'][$a]['lastname']);
                $tableau .= '<tr valign="middle" align="left">
                                    <td style="width:70px;height:40px;background-color:' . $color . ';">' . $nom_apprenant . ' ' . $prenom_apprenant . '</td>';
                for ($j = 0; $j < 6; $j++) {
                    $tableau .= '   <td style="height:40px;background-color:' . $color . ';"></td>
                                    <td style="height:40px;background-color:' . $color . ';"></td>';
                }
                $tableau .= '       </tr>';
            }
            $nom_formateur = $modelColumn0['formateur'];
            if ($a % 2 == 0) {
                $color = "#eef0f2";
            } else {
                $color = "#ffffe8";
            }
            $tableau .= '<tr valign="middle" align="left"> 
            <td style="width:70px;height:40px;background-color:' . $color . ';">formateur: ' . $nom_formateur . '</td>';
            for ($i = 0; $i < 6; $i++) {
                if ($a % 2 == 0) {
                    $color = "#eef0f2";
                } else {
                    $color = "#ffffe8";
                }
                $tableau .= '   <td style="height:40px;background-color:' . $color . ';"></td>
                                    <td style="height:40px;background-color:' . $color . ';"></td>';
            }
            $tableau .= '       </tr>
             </table> ';

            $pdf->writeHTMLCell(200, 100, $x, $y += 10, $tableau, '');


            $footer1 = "C.N.F.S.E. - Organisme de formation - 231 rue St Honoré 75001 PARIS ";
            $footer2 = "SAS au capital de 8000 € - SIRET : 482 379 302 00029 N° TVA Intracommunautaire : 8559A FR59482379302 - Code NAF : 8559A - Téléphone : 01.84.16.38.25 Télécopie :";
            $footer3 = "09.72.33.02.35 ";
            $pdf->SetFont($trebuchet, '', 7);

            $pdf->MultiCell(250, 5, $footer1, 0, 'C', 0, 1, 20, 190);
            $pdf->MultiCell(250, 5, $footer2, 0, 'C', 0, 1, 20, 193);
            $pdf->MultiCell(250, 5, $footer3, 0, 'C', 0, 1, 20, 196);
        }
    }

}
