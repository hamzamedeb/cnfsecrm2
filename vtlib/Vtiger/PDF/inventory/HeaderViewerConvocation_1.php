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
            $nbr_apprenants = $modelColumn0['info_apprenants']['nbr_apprenants'];

            for ($j = 0; $j < $nbr_apprenants; $j++) {
                //echo $nbr_apprenants;
                list($imageWidth, $imageHeight, $imageType, $imageAttr) = $parent->getimagesize(
                        "test/logo/logo-CNFSE-large.png");
                //division because of mm to px conversion
                $w = $imageWidth / 3;

                $w = $imageWidth;
                if ($w > 30) {
                    $w = 60;
                }
                $h = $imageHeight;
                if ($h > 20) {
                    $h = 22;
                }
                $pdf->Image("test/logo/logo-CNFSE-large.png", 10, 5, $w, $h);

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
                $y = 25;

                //titre
                $pdf->SetFont($trebuchetbd, '', 15);
                $pdf->SetXY($x, $y);
                $pdf->MultiCell(200, 5, 'CONVOCATION', '', 'C', 0, 1);

                //stagiaire
                $x = 10;
                $pdf->SetFont($trebuchet, '', 10);
                $pdf->SetXY($x, $y += 15);
                $pdf->MultiCell(200, 5, 'Stagiaire :', '', 'L', 0, 1);

                $pdf->SetFont($trebuchetbd, '', 10);
                $pdf->SetXY($x + 40, $y);
                $nom_apprenant = $modelColumn0['info_apprenants'][$j]['firstname'];
                $prenom_apprenant = $modelColumn0['info_apprenants'][$j]['lastname'];
                $salutation_apprenant = $modelColumn0['info_apprenants'][$j]['salutation'];
                $pdf->MultiCell(200, 5, $salutation_apprenant . ' ' . $nom_apprenant . ' ' . $prenom_apprenant, '', 'L', 0, 1);

                //stagiaire
                $x = 10;
                $pdf->SetFont($trebuchetmsitalic, '', 11);
                $pdf->SetXY($x, $y += 10);
                $pdf->MultiCell(200, 5, 'Vous êtes convoqué(e) à la formation suivante :', '', 'L', 0, 1);

                //stagiaire
                $x = 10;
                $pdf->SetFont($trebuchet, '', 10);
                $pdf->SetXY($x, $y += 10);
                $pdf->MultiCell(200, 5, 'Intitule du stage :', '', 'L', 0, 1);

                $pdf->SetFont($trebuchetbd, '', 10);
                $pdf->SetXY($x + 40, $y);
                $nom_formation = $modelColumn0['subject'];
                $pdf->MultiCell(200, 5, $nom_formation, '', 'L', 0, 1);

                //Duree
                $x = 10;
                $pdf->SetFont($trebuchet, '', 10);
                $pdf->SetXY($x, $y += 10);
                $pdf->MultiCell(200, 5, 'Durée :', '', 'L', 0, 1);

                $pdf->SetFont($trebuchetbd, '', 10);
                $pdf->SetXY($x + 40, $y);
                $nbr_heures = decimalFormat($modelColumn0['info_product'][0]['nbrheures'], 1, '.', '');
                $pdf->MultiCell(200, 5, $nbr_heures.' Heures', '', 'L', 0, 1);

                //Lieu
                $x = 10;
                $pdf->SetFont($trebuchet, '', 10);
                $pdf->SetXY($x, $y += 10);
                $pdf->MultiCell(200, 5, 'Lieu :', '', 'L', 0, 1);

                $pdf->SetFont($trebuchetbd, '', 10);
                $pdf->SetXY($x + 40, $y);
                $adresse_formation = decimalFormat($modelColumn0['adresse_formation']);
                $cp_formation = decimalFormat($modelColumn0['cp_formation']);
                $ville_formation = decimalFormat($modelColumn0['ville_formation']);
                $pdf->MultiCell(200, 5, $adresse_formation . ' ' . $cp_formation . ' ' . $ville_formation, '', 'L', 0, 1);

                //date
                //date de formation
                $info_dates = $modelColumn0['info_dates'];

                $nbre_jour = count($info_dates);

                for ($i = 0; $i < $nbre_jour; $i++) {
                    $list_date[$i] = $info_dates[$i]['date_start'];
                }
                sort($list_date);
                $premier_jour = $list_date[0];
                if ($premier_jour != null) {
                    $date_debut_formation = formatDateFr($premier_jour);
                }

                $dernier_jour = $list_date[$i - 1];
                if ($dernier_jour != null) {
                    $date_fin_formation = formatDateFr($dernier_jour);
                }
                $x = 10;
                $pdf->SetFont($trebuchet, '', 10);
                $pdf->SetXY($x, $y += 10);
                $pdf->MultiCell(200, 5, 'Dates :', '', 'L', 0, 1);

                $pdf->SetFont($trebuchetbd, '', 10);
                $pdf->SetXY($x + 40, $y);
                $pdf->MultiCell(200, 5, 'Du ' . $date_debut_formation . ' au ' . $date_fin_formation, '', 'L', 0, 1);

                //Heure de début : 07:30
                $x = 10;
                $pdf->SetFont($trebuchet, '', 10);
                $pdf->SetXY($x, $y += 10);
                $pdf->MultiCell(200, 5, 'Heure de début :', '', 'L', 0, 1);

                $pdf->SetFont($trebuchetbd, '', 10);
                $pdf->SetXY($x + 40, $y);
                $heure_debut_matin = $modelColumn0['info_dates'][0]["start_matin"];
                $pdf->MultiCell(200, 5, $heure_debut_matin, '', 'L', 0, 1);

                //image maps
                $x = 10;
                $y += 10;
                $lien_image = "https://www.codeur.com/blog/wp-content/uploads/2018/06/google-maps-payant-700x423.jpg";
                $pdf->Image($lien_image, $x, $y, 180, 80);
                $pdf->SetXY($x, $y);
                $pdf->MultiCell(180, 80, '', 'TBLR', 'L', 0, 1);

                //Coordonnées GPS : Latitude 0 Longitude 0
                $x = 10;
                $pdf->SetFont($trebuchetbd, '', 8);
                $pdf->SetXY($x, $y += 80);
                $pdf->MultiCell(200, 5, 'Coordonnées GPS : Latitude 0 Longitude 0', '', 'L', 0, 1);

                //tableau des dates

                $nbr_journee = count($modelColumn0['info_dates']);
                if ($nbr_journee != 0) {
                    $pdf->SetXY($x + 10, $y += 10);
                    $tbl = '<table width="90%" border="0" align="justify" cellpadding="2" cellspacing="1" style="font-size:8pt;">
              <tbody>
              <tr>
              <td colspan="5">
              <p align="left" style="font-family: "Trebuchet MS", Helvetica, sans-serif;font-weight: normal;font-size: 8pt;">Calendrier des Journées</p>
              </td>
              </tr>
              <tr style="font-size: 8pt;">
              <th align="center" valign="middle" nowrap bgcolor="#7F9DB9">N° Journée</th>
              <th align="center" valign="middle" nowrap bgcolor="#7F9DB9">Date</th>
              <th align="center" valign="middle" nowrap bgcolor="#7F9DB9">Heures matin</th>
              <th align="center" valign="middle" nowrap bgcolor="#7F9DB9">Heures après-midi</th>
              <th align="center" valign="middle" nowrap bgcolor="#7F9DB9">Durée</th>
              </tr>';

                    for ($i = 0; $i < $nbr_journee; $i++) {
                        if ($i % 2 == 0) {
                            $couleur = "#D4E2EE";
                        } else {
                            $couleur = "#FEFFDD";
                        }
                        $num_journee = $modelColumn0['info_dates'][$i]["sequence_no"];
                        $date_formation = $modelColumn0['info_dates'][$i]["date_start"];
                        if ($date_formation != null) {
                            $date_formation = formatDateFr($date_formation);
                            //$date = new DateTime($date_formation);
                            //$date_formation = $date->format('d-m-Y');
                        }
                        $heure_debut_matin = $modelColumn0['info_dates'][$i]["start_matin"];
                        $heure_fin_matin = $modelColumn0['info_dates'][$i]["end_matin"];
                        $heure_debut_apresmidi = $modelColumn0['info_dates'][$i]["start_apresmidi"];
                        $heure_debut_apremidi = $modelColumn0['info_dates'][$i]["end_apresmidi"];
                        //var_dump($date_formation);
                        $tbl .= '<tr style = "font-size: 8pt;">
              <td align="right" bgcolor="' . $couleur . '">' . $num_journee . '</td>
              <td align="center" bgcolor="' . $couleur . '">' . $date_formation . '</td>
              <td align="center" bgcolor="' . $couleur . '">De ' . $heure_debut_matin . ' à ' . $heure_fin_matin . '</td>
              <td align="center" bgcolor="' . $couleur . '">De ' . $heure_debut_apresmidi . ' à ' . $heure_debut_apremidi . '</td>
              <td align="center" bgcolor="' . $couleur . '">7, 00</td>
              </tr>';
                    }
                    $tbl .= '</tbody>
              </table>';
                    $pdf->writeHTML($tbl, true, false, true, false, '');
                }

                if ($j != ($nbr_apprenants - 1)) {
                    $pdf->AddPage();
                }
            }

//fin tableau des date   
        }
    }
}
