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

/* uni_cnfsecrm - v2 - modif 176 - FILE */

class Vtiger_PDF_ConvocationMPHeaderViewer extends Vtiger_PDF_HeaderViewer {

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
            $pdf->SetPrintFooter(false);
            $modelColumn0 = $modelColumns[0];
            $nbr_apprenants = $modelColumn0['info_apprenants']['nbr_apprenants'];
            for ($j = 0; $j < $nbr_apprenants; $j++) {
                $pdf->SetTextColor(0, 0, 0);
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
                $pdf->Image("test/logo/logo-CNFSE-large.png", 18, 15, $w, $h);

                $pdf->Image("test/logo/ville-de-paris.png", 165, 15, 25, 20);

                $calibri = TCPDF_FONTS2::addTTFfont('test/font/Calibri/calibri.ttf', 'TrueTypeUnicode', '', 96);
                //font calibri bold
                $calibrib = TCPDF_FONTS2::addTTFfont('test/font/Calibri/calibrib.ttf', 'TrueTypeUnicode', '', 96);
                //font calibri italic
                $calibrii = TCPDF_FONTS2::addTTFfont('test/font/Calibri/calibrii.ttf', 'TrueTypeUnicode', '', 96);
                //font calibri bold/italic
                $calibriz = TCPDF_FONTS2::addTTFfont('test/font/Calibri/calibriz.ttf', 'TrueTypeUnicode', '', 96);

                $pdf->SetFont($calibrib, '', 20);
                $x = 0;
                $y = 45;
                $pdf->SetXY($x, $y);
                $pdf->writeHTML('<p><u>La convocation vaut ordre de mission</u></p>', true, 0, true, false, 'C');

                $y += 15;
                $nom_apprenant = $modelColumn0['info_apprenants'][$j]['firstname'];
                $prenom_apprenant = $modelColumn0['info_apprenants'][$j]['lastname'];
                $matricule_apprenant = $modelColumn0['info_apprenants'][$j]['matricule'];
                $nomprenom = $nom_apprenant . " " . $prenom_apprenant;
                if ($matricule_apprenant != "") {
                    $nomprenom .= " ( " . $matricule_apprenant . " )";
                }
                $pdf->SetFont($calibrib, '', 14);
                $pdf->MultiCell(205, 20, $nomprenom, '', 'C', 0, 1, $x, $y);

                $y += 5;
                $direction = $modelColumn0['info_apprenants'][$j]['direction'];
                $pdf->SetFont($calibrib, '', 12);
                $pdf->MultiCell(205, 20, 'Ville de Paris – ' . $direction, '', 'C', 0, 1, $x, $y);

                $y += 15;
                $nom_formation = $modelColumn0['subject'];
//                $type = explode(" ", $nom_formation);
//                $type = $type[0];
//                $type_formation = "$type + intitulé catalogue DRH ville de Paris";
                $pdf->SetFont($calibri, '', 12);
                $pdf->MultiCell(205, 20, 'La Ville de Paris nous ayant confié la formation: ' . $nom_formation, '', 'C', 0, 1, $x, $y);

 /*               $y += 10;
                $info_dates = $modelColumn0['info_dates'];

                $nbre_jour = count($info_dates);

                for ($i = 0; $i < $nbre_jour; $i++) {
                    $list_date[$i] = $info_dates[$i]['date_start'];
                }
                $premier_jour = $list_date[0];
                if ($premier_jour != null) {
                    $date_debut_formation = formatDateFr($premier_jour);
                }

                $dernier_jour = $list_date[$i - 1];
                if ($dernier_jour != null) {
                    $date_fin_formation = formatDateFr($dernier_jour);
                }

                $nbr_journee = count($modelColumn0['info_dates']);
                $heure_debut_matin = $modelColumn0['info_dates'][0]["start_matin"];
                $heure_fin_apresmidi = $modelColumn0['info_dates'][$nbr_journee - 1]["end_apresmidi"];

                $pdf->SetFont($calibrib, '', 14);
                $pdf->MultiCell(205, 20, "du $date_debut_formation au $date_fin_formation de $heure_debut_matin à $heure_fin_apresmidi", '', 'C', 0, 1, $x, $y);
*/
                $y += 10;
                $pdf->SetFont($calibri, '', 12);
                $adresse_formation = $modelColumn0['adresse_formation'];
                $cp_formation = $modelColumn0['cp_formation'];
                $ville_formation = $modelColumn0['ville_formation'];
                $lieu_formation = $adresse_formation . "  " . $cp_formation . "  " . $ville_formation;
                $text = "<p>Pour celle-ci, nous avons le plaisir de vous faire parvenir votre confirmation d'inscription.<br>
                    La formation aura lieu à <strong> $lieu_formation </strong> </p>";
                $pdf->writeHTMLCell(200, 20, 20, $y, $text, 0, 0, 0, true, 'L', true);

                $y += 15;
                $pdf->SetFont($calibri, '', 12);
                $text = "<p>Vous trouverez également en pièces jointes : <br/>
                - Possibilités de restauration à proximité ou/et RIE. &nbsp; &nbsp; &nbsp; &nbsp;	-le programme <br/>
                -La liste des E.P.I. à apporter &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; -le plan d’accès </p>";
                $pdf->writeHTMLCell(200, 20, 20, $y, $text, 0, 0, 0, true, 'L', true);

                $y += 20;
                $text = "Par considération pour vos collègues et pour favoriser le bon déroulement de cette <br> formation, il est indispensable que les horaires soient respectés . Tout retard supérieur à 15 <br> minutes entrainera le refus d’accès   à la formation et vous devrez regagner votre service.";
                $pdf->SetFont($calibri, '', 12);
                $pdf->writeHTMLCell(200, 20, 20, $y, $text, 0, 0, 0, true, 'L', true);

                $y += 20;
                $text = "En cas d’indisponibilité , la place de stage pourra ainsi être proposée à un autre agent. <br/> Veuillez prévenir le référent formation de votre direction :";
                $pdf->SetFont($calibri, '', 12);
                $pdf->writeHTMLCell(200, 20, 20, $y, $text, 0, 0, 0, true, 'L', true);

                $y += 15;
                $x = 20;
                $pdf->SetFont($calibri, '', 12);
                $nbreDirection = count($modelColumn0['info_apprenants'][$j]['listDirection']);
                if ($nbreDirection != 0) {
                    for ($c = 0; $c < $nbreDirection; $c++) {
                        $nomDirection = $modelColumn0['info_apprenants'][$j]['listDirection'][$c]['firstname'];
                        $prenomDirection = $modelColumn0['info_apprenants'][$j]['listDirection'][$c]['lastname'];
                        $phoneDirection = $modelColumn0['info_apprenants'][$j]['listDirection'][$c]['phone'];
                        $pdf->MultiCell(205, 20, $nomDirection . " " . $prenomDirection, '', 'L', 0, 1, $x, $y);
                        $pdf->MultiCell(205, 20, $phoneDirection, '', 'L', 0, 1, $x + 100, $y);
                        $y += 5;
                    }
                }

                /* tableau des journees */
                $nbr_journee = count($modelColumn0['info_dates']);
                if ($nbr_journee != 0) {
                    $pdf->SetXY($x + 10, $y += 5);
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
                        $heure_fin_apresmidi = $modelColumn0['info_dates'][$i]["end_apresmidi"];
                        // modif hamza 7:00 
                        $duree_formation = $modelColumn0['info_dates'][$i]["duree_formation"];
                        $duree_formation = str_replace(".", ":", $duree_formation);

                        //var_dump($date_formation);
                        $tbl .= '<tr style = "font-size: 8pt;">
                        <td align="right" bgcolor="' . $couleur . '">' . $num_journee . '</td>
                        <td align="center" bgcolor="' . $couleur . '">' . $date_formation . '</td>
                        <td align="center" bgcolor="' . $couleur . '">De ' . $heure_debut_matin . ' à ' . $heure_fin_matin . '</td>
                        <td align="center" bgcolor="' . $couleur . '">De ' . $heure_debut_apresmidi . ' à ' . $heure_fin_apresmidi . '</td>
                        <td align="center" bgcolor="' . $couleur . '">' . $duree_formation . '</td>
                        </tr>';
                              }
                              $tbl .= '</tbody>
                        </table>';
                    $pdf->writeHTML($tbl, true, false, true, false, '');
                }
                /**/

                $y += ($nbr_journee * 5) + 15;
                $text = "Dans l’attente du plaisir de vous retrouver très prochainement , nous vous prions d’agréer <br/> nos cordiales salutations.";
                $pdf->SetFont($calibri, '', 12);
                $pdf->writeHTMLCell(200, 20, 20, $y, $text, 0, 0, 0, true, 'L', true);

                $y += 15;
                $date_jour = new DateTime();
                $date_jour = $date_jour->format('d/m/Y');
                $text = "Paris , le $date_jour";
                $pdf->SetFont($calibri, '', 12);
                $pdf->writeHTMLCell(200, 20, 20, $y, $text, 0, 0, 0, true, 'L', true);

                $y += 5;
                $text = "<strong> Le service Formation </strong>";
                $pdf->SetFont($calibri, '', 12);
                $pdf->writeHTMLCell(200, 20, 0, $y, $text, 0, 0, 0, true, 'R', true);

                $y = 285;
                $text = "231 rue St Honoré – Paris 1er – Tél : 01.84.16.38.25- Fax : 09.72.33.02.35 – courriel : contact@cnfse.fr <br/>
                RCS Paris 482.379.302 – APE : 8559A- Sarl capital 8.000 euros – Déclaration d’activité 11.75.51614.75 ";
                $pdf->SetFont($calibri, '', 10);
                $pdf->SetTextColor(181, 181, 181);
                $pdf->writeHTMLCell(200, 20, 0, $y, $text, 0, 0, 0, true, 'C', true);
                if ($j < ($nbr_apprenants - 1)) {
                    $pdf->addPage();
                }
            }
        }
    }

}
