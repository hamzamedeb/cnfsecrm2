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

class Vtiger_PDF_EmargementHeaderViewer extends Vtiger_PDF_HeaderViewer {

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
            $pdf->setPageFormat('A4', 'P');
            $headerColumnWidth = $headerFrame->w / 3.0;
            $modelColumns = $this->model->get('columns');
            // Column 1
            $offsetX = 5;
            $modelColumn0 = $modelColumns[0];
            $nbr_apprenants = $modelColumn0['info_apprenants']['nbr_apprenants'];
            $x_apprenant = 10;
            $y_apprenant = 81;
            for ($i = 0; $i < $nbr_apprenants; $i++) {
                $pdf->SetDrawColor(191, 191, 191);
                if ($i % 2 == 0) {
                    $pdf->SetFillColor(238, 240, 242);
                } else {
                    $pdf->SetFillColor(255, 255, 232);
                }

                if ($i % 10 == 0) {
                    if ($i != 0) {
                        $pdf->addPage();
                    }
                    $x_apprenant = 10;
                    $y_apprenant = 81;
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

                    $pdf->SetXY($x, $y += 8);
                    $pdf->SetFont($trebuchet, '', 10);
                    $pdf->MultiCell(250, 5, 'Lieu de la formation : ', '', 'L', 0, 1);

                    $pdf->SetXY($x + 40, $y);
                    $pdf->SetFont($trebuchetbd, '', 9);
                    $pdf->MultiCell(250, 5, $adresse_formation . ' ' . $cp_formation . ' ' . $ville_formation, '', 'L', 0, 1);
//1er ligne
                    $x = 10;
                    $y = 55;
                    $pdf->SetFont($trebuchet, '', 10);
                    $pdf->SetFillColor(177, 184, 200);
                    $pdf->MultiCell(35, 26, 'Prénom/NOM', 'LTBR', 'L', 1, 1, $x, $y);
                    if ($i % 2 == 0) {
                        $pdf->SetFillColor(238, 240, 242);
                    } else {
                        $pdf->SetFillColor(255, 255, 232);
                    }
                    $x1 = 0;
                    $x2 = 0;
                    for ($j = 0; $j < 4; $j++) {
                        $date_formation = $modelColumn0['info_dates'][$j]["date_start"];
                        $heure_debut_matin = $modelColumn0['info_dates'][$j]["start_matin"];
                        $heure_fin_matin = $modelColumn0['info_dates'][$j]["end_matin"];
                        $heure_debut_apresmidi = $modelColumn0['info_dates'][$j]["start_apresmidi"];
                        $heure_fin_apresmidi = $modelColumn0['info_dates'][$j]["end_apresmidi"];
                        if ($date_formation != null) {
                            $date = new DateTime($date_formation);
                            $date_formation = $date->format('d-m-Y');
                        }
                        if ($date_formation == '') {
                            $date_formation = '';
                        } else {
                            $date_formation = 'Date : ' . $date_formation;
                        }
                        $hmatin = "Matin";
                        $hmidi = "Après-midi";
                        if ($heure_debut_matin == '') {
                            $hmatin = "";
                        }
                        if ($heure_debut_apresmidi == '') {
                            $hmidi = "";
                        }
                        $matin = $heure_debut_matin . ' à ' . $heure_fin_matin;
                        if ($heure_debut_matin) {
                            $matin = $heure_debut_matin . ' à ' . $heure_fin_matin;
                        } else {
                            $matin = '';
                        }
                        $midi = $heure_debut_apresmidi . ' à ' . $heure_fin_apresmidi;
                        if ($heure_debut_apresmidi) {
                            $midi = $heure_debut_apresmidi . ' à ' . $heure_fin_apresmidi;
                        } else {
                            $midi = '';
                        }
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
                        $pdf->SetFont($trebuchet, '', 9);

                        $pdf->MultiCell(40, 6.5, $date_formation, 'LTBR', 'C', 0, 1, $x1 + 45, $y);

                        $pdf->SetFont($trebuchet, '', 8);

                        $pdf->MultiCell(20, 6.5, $hmatin, 'LTBR', 'C', 0, 1, $x1 + 45, $y + 6.5);
                        $pdf->MultiCell(20, 6.5, $hmidi, 'LTBR', 'C', 0, 1, $x2 + 65, $y + 6.5);

                        $pdf->MultiCell(20, 6.5, $matin, 'LTBR', 'C', 0, 1, $x1 + 45, $y + 13);
                        $pdf->MultiCell(20, 6.5, $midi, 'LTBR', 'C', 0, 1, $x2 + 65, $y + 13);

                        $pdf->MultiCell(20, 6.5, $duree_matin, 'LTBR', 'C', 0, 1, $x1 + 45, $y + 19.5);
                        $pdf->MultiCell(20, 6.5, $duree_midi, 'LTBR', 'C', 0, 1, $x2 + 65, $y + 19.5);
                        $pdf->SetFont($trebuchet, '', 10);
                        $x1 += 40;
                        $x2 += 40;
                        //fin 1er ligne
                    }                  
                }
//ligne
                //lignes apprenant

                $nom_apprenant = html_entity_decode($modelColumn0['info_apprenants'][$i]['firstname']);
                $prenom_apprenant = html_entity_decode($modelColumn0['info_apprenants'][$i]['lastname']);
                //$pdf->Cell(30, 0, 'Baseline-Center', 1, $ln=0, 'C', 0, '', 0, false, 'L', 'C');
                $pdf->MultiCell(35, 15, $nom_apprenant . ' ' . $prenom_apprenant, 'LTBR', 'L', 1, 1, $x_apprenant, $y_apprenant);
                $l = 45;
                for ($s = 0; $s < 8; $s++) {
                    $pdf->MultiCell(20, 15, '', 'LTBR', 'L', 1, 1, $l, $y_apprenant);
                    $l += 20;
                }
                $y_apprenant += 15;
                //fin ligne apprenant
            }
            //formateur

            if ($i % 2 == 0) {
                $pdf->SetFillColor(238, 240, 242);
            } else {
                $pdf->SetFillColor(255, 255, 232);
            }
            $nom_formateur = $modelColumn0['formateur']  ;          
            $pdf->MultiCell(35, 15, 'Formateur : ' . $nom_formateur, 'LTBR', 'L', 1, 1, $x, $y_apprenant);
            $x_formateur = 45;
            for ($j_formateur = 0; $j_formateur < 8; $j_formateur++) {
                $pdf->MultiCell(20, 15, '', 'LTBR', 'L', 1, 1, $x_formateur, $y_apprenant);
                $x_formateur += 20;
            }
        }
    }

}
