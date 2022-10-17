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

class Vtiger_PDF_SatisfactionHeaderViewer extends Vtiger_PDF_HeaderViewer {

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
            //$apprenants = $modelColumn0['info_apprenants'];
            //var_dump($apprenants);
            $nombre_apprenant = $modelColumn0['info_apprenants']['nbr_apprenants'];

            for ($i = 0; $i < $nombre_apprenant; $i++) {
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
                $pdf->MultiCell(190, 5, 'QUESTIONNAIRE DE SATISFACTION', '', 'C', 0, 1);

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

                $x = 10;
                $pdf->SetXY($x, $y += 7);
                $pdf->SetFont($trebuchet, '', 9);
                $pdf->MultiCell(190, 5, 'Client : ', '', 'L', 0, 1);

                $pdf->SetXY($x + 20, $y);
                $pdf->SetFont($trebuchetbd, '', 9);
                $client = $modelColumn0['info_client']['accountname'];
                $pdf->MultiCell(190, 5, $client, '', 'L', 0, 1);

                $x = 10;
                $pdf->SetXY($x, $y += 7);
                $pdf->SetFont($trebuchet, '', 9);
                $pdf->MultiCell(190, 5, 'Intitulé : ', '', 'L', 0, 1);

                $pdf->SetXY($x + 20, $y);
                $pdf->SetFont($trebuchetbd, '', 9);
                $nom_formation = $modelColumn0['subject'];
                $pdf->MultiCell(190, 5, $nom_formation, '', 'L', 0, 1);

                //2eme partie
                $x = 120;
                $y = 30;
                $pdf->SetXY($x, $y);
                $pdf->SetFont($trebuchet, '', 9);
                $pdf->MultiCell(190, 5, 'Dates : ', '', 'L', 0, 1);

                $pdf->SetXY($x + 20, $y);
                $pdf->SetFont($trebuchetbd, '', 9);
                $date_debut_formation = $modelColumn0['date_debut_formation'];
                $date_fin_formations = $modelColumn0['date_fin_formations'];
                $date = 'du ' . $date_debut_formation . ' au ' . $date_fin_formations;
                $pdf->MultiCell(190, 5, $date, '', 'L', 0, 1);

                $x = 120;
                $pdf->SetXY($x, $y += 7);
                $pdf->SetFont($trebuchet, '', 9);
                $pdf->MultiCell(190, 5, 'Stagiaire : ', '', 'L', 0, 1);

                $pdf->SetXY($x + 20, $y);
                $pdf->SetFont($trebuchetbd, '', 9);
                $nom_apprenant = $modelColumn0['info_apprenants'][$i]['firstname'];
                $prenom_apprenant = $modelColumn0['info_apprenants'][$i]['lastname'];
                $pdf->MultiCell(190, 5, $nom_apprenant . ' ' . $prenom_apprenant, '', 'L', 0, 1);

                $y = 55;
                $x = 10;
                $pdf->SetXY($x, $y);
                $pdf->SetFont($trebuchet, '', 8);
                $pdf->MultiCell(190, 5, 'Sur une échelle de valeur de 0 à 10 , indiquez vos appréciations sur les sujets suivants :', '', 'L', 0, 1);

                $tableau1 = '<table style="width:780px;bordercolor:#bfbfbf" valign="middle" border="1" cellpadding="2">
                            <tr>
                                <td style="width:180px;height:20px;background-color:#e8e8e8;">Le contenu de la formation</td>
                                <td align="center" style="height:20px;width:30px;background-color:#e8e8e8;">0</td>
                                <td align="center" style="height:20px;width:30px;background-color:#e8e8e8;">1</td>
                                <td align="center" style="height:20px;width:30px;background-color:#e8e8e8;">2</td>
                                <td align="center" style="height:20px;width:30px;background-color:#e8e8e8;">3</td>
                                <td align="center" style="height:20px;width:30px;background-color:#e8e8e8;">4</td>
                                <td align="center" style="height:20px;width:30px;background-color:#e8e8e8;">5</td>
                                <td align="center" style="height:20px;width:30px;background-color:#e8e8e8;">6</td>
                                <td align="center" style="height:20px;width:30px;background-color:#e8e8e8;">7</td>
                                <td align="center" style="height:20px;width:30px;background-color:#e8e8e8;">8</td>
                                <td align="center" style="height:20px;width:30px;background-color:#e8e8e8;">9</td>
                                <td align="center" style="height:20px;width:30px;background-color:#e8e8e8;">10</td>
                            </tr>
                            
                            <tr>
                                <td style="height:20px;width:180px;">Théorie</td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                            </tr>
                            
                            <tr>
                                <td style="height:20px;width:180px;">Pratique</td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                            </tr>
                            
                            <tr>
                                <td style="width:180px;">Le rythme(durée,horaires ...)</td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                            </tr>
                        </table>

';
                $pdf->writeHTMLCell(200, 100, $x, $y += 5, $tableau1, '');
                //table 2
                $tableau2 = '<table style="width:780px;bordercolor:#bfbfbf" valign="middle" border="1" cellpadding="2">
                            <tr>
                                <td style="width:180px;height:20px;background-color:#e8e8e8;">L\'environnement de travail </td>
                                <td align="center" style="height:20px;width:30px;background-color:#e8e8e8;">0</td>
                                <td align="center" style="height:20px;width:30px;background-color:#e8e8e8;">1</td>
                                <td align="center" style="height:20px;width:30px;background-color:#e8e8e8;">2</td>
                                <td align="center" style="height:20px;width:30px;background-color:#e8e8e8;">3</td>
                                <td align="center" style="height:20px;width:30px;background-color:#e8e8e8;">4</td>
                                <td align="center" style="height:20px;width:30px;background-color:#e8e8e8;">5</td>
                                <td align="center" style="height:20px;width:30px;background-color:#e8e8e8;">6</td>
                                <td align="center" style="height:20px;width:30px;background-color:#e8e8e8;">7</td>
                                <td align="center" style="height:20px;width:30px;background-color:#e8e8e8;">8</td>
                                <td align="center" style="height:20px;width:30px;background-color:#e8e8e8;">9</td>
                                <td align="center" style="height:20px;width:30px;background-color:#e8e8e8;">10</td>
                            </tr>
                            
                            <tr>
                                <td style="height:20px;width:180px;">Accueil</td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                            </tr>
                            
                            <tr>
                                <td style="height:20px;width:180px;">Salle</td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                            </tr>
                            
                            <tr>
                                <td style="width:180px;">Nombre de participants</td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                            </tr>
                            
                            <tr>
                                <td style="width:180px;">Le support de cours</td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                            </tr>
                        </table>

';
                $pdf->writeHTMLCell(200, 100, $x, $y += 30, $tableau2, '');

                //table 3
                $tableau3 = '<table style="width:780px;bordercolor:#bfbfbf" valign="middle" border="1" cellpadding="2">
                            <tr>
                                <td style="width:180px;height:20px;background-color:#e8e8e8;">Le formateur</td>
                                <td align="center" style="height:20px;width:30px;background-color:#e8e8e8;">0</td>
                                <td align="center" style="height:20px;width:30px;background-color:#e8e8e8;">1</td>
                                <td align="center" style="height:20px;width:30px;background-color:#e8e8e8;">2</td>
                                <td align="center" style="height:20px;width:30px;background-color:#e8e8e8;">3</td>
                                <td align="center" style="height:20px;width:30px;background-color:#e8e8e8;">4</td>
                                <td align="center" style="height:20px;width:30px;background-color:#e8e8e8;">5</td>
                                <td align="center" style="height:20px;width:30px;background-color:#e8e8e8;">6</td>
                                <td align="center" style="height:20px;width:30px;background-color:#e8e8e8;">7</td>
                                <td align="center" style="height:20px;width:30px;background-color:#e8e8e8;">8</td>
                                <td align="center" style="height:20px;width:30px;background-color:#e8e8e8;">9</td>
                                <td align="center" style="height:20px;width:30px;background-color:#e8e8e8;">10</td>
                            </tr>
                            
                            <tr>
                                <td style="height:20px;width:180px;">A l\'écoute de vos attentes</td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                            </tr>
                        </table>

';
                $pdf->writeHTMLCell(200, 100, $x, $y += 37, $tableau3, '');

                //table 4
                $tableau4 = '<table style="width:780px;bordercolor:#e8e8e8" valign="middle" border="1" cellpadding="2">
                            <tr>
                                <td style="width:180px;height:20px;background-color:#e8e8e8;">Conclusion</td>
                                <td align="center" style="height:20px;width:30px;background-color:#e8e8e8;">0</td>
                                <td align="center" style="height:20px;width:30px;background-color:#e8e8e8;">1</td>
                                <td align="center" style="height:20px;width:30px;background-color:#e8e8e8;">2</td>
                                <td align="center" style="height:20px;width:30px;background-color:#e8e8e8;">3</td>
                                <td align="center" style="height:20px;width:30px;background-color:#e8e8e8;">4</td>
                                <td align="center" style="height:20px;width:30px;background-color:#e8e8e8;">5</td>
                                <td align="center" style="height:20px;width:30px;background-color:#e8e8e8;">6</td>
                                <td align="center" style="height:20px;width:30px;background-color:#e8e8e8;">7</td>
                                <td align="center" style="height:20px;width:30px;background-color:#e8e8e8;">8</td>
                                <td align="center" style="height:20px;width:30px;background-color:#e8e8e8;">9</td>
                                <td align="center" style="height:20px;width:30px;background-color:#e8e8e8;">10</td>
                            </tr>
                            
                            <tr>
                                <td style="height:20px;width:180px;">Appréciation générale</td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                            </tr>
                        </table>

';
                $pdf->writeHTMLCell(200, 100, $x, $y += 16, $tableau4, '');

                //table 5
                $tableau5 = '<table style="width:780px;bordercolor:#e8e8e8" valign="middle" border="1" cellpadding="2">
                            <tr border="1">
                                <td border="0" style="width:230px;height:20px;background-color:#e8e8e8;"></td>
                                <td border="1" align="center" style="height:20px;width:30px;background-color:#e8e8e8;">oui</td>
                                <td border="1" align="center" style="height:20px;width:60px;background-color:#e8e8e8;">Partiellement</td>
                                <td border="1" align="center" style="height:20px;width:30px;background-color:#e8e8e8;">non</td>
                            </tr>
                            
                            <tr>
                                <td style="height:20px;width:230px;">Cette formation a t-elle répondu à vos attentes ?</td>
                                <td align="center" style="height:20px;width:30px;"></td>
                                <td align="center" style="height:20px;width:60px;"></td>
                                <td align="center" style="height:20px;width:30px;"></td>
                            </tr>
                        </table>

';
                $pdf->writeHTMLCell(200, 100, $x, $y += 16, $tableau5, '');

                //table 6
                $tableau6 = '<table style="width:780px;bordercolor:#e8e8e8" valign="middle" border="1" cellpadding="2">
                            <tr border="1">
                                <td style="width:510px;height:20px;background-color:#e8e8e8;">Si non, pourquoi ?</td>
                            </tr>
                            
                            <tr>
                                <td style="height:50px;width:510px;"></td>
                            </tr>
                        </table>

';
                $pdf->writeHTMLCell(200, 100, $x, $y += 16, $tableau6, '');

                //table 7
                $tableau7 = '<table style="width:780px;bordercolor:#e8e8e8" valign="middle" border="1" cellpadding="2">
                            <tr>
                                <td style="width:510px;height:20px;background-color:#e8e8e8;">D\'après vous ,quelles sont les améliorations qui pourraient être apportées ?</td>
                            </tr>
                            
                            <tr>
                                <td style="height:55px;width:510px;"></td>
                            </tr>
                        </table>

';
                $pdf->writeHTMLCell(200, 30, $x, $y += 26.5, $tableau7, '');

                //table 8
                $tableau8 = '<table style="width:780px;bordercolor:#e8e8e8" valign="middle" border="1" cellpadding="2">
                            <tr>
                                <td style="width:510px;height:20px;background-color:#e8e8e8;">Auriez vous besoin d\'une formation complémentaire ? Si oui, laquelle ?</td>
                            </tr>
                            
                            <tr>
                                <td style="height:55px;width:510px;"></td>
                            </tr>
                        </table>

';
                $pdf->writeHTMLCell(200, 30, $x, $y += 28.5, $tableau8, '');


                $pdf->SetXY($x + 1, $y += 28);
                $pdf->MultiCell(190, 5, 'Nous vous remercions pour votre participation.', '', 'L', 0, 1);

                $nombre_apprenant = $modelColumn0['info_apprenants']['nbr_apprenants'];

                //bordure top

                $pdf->SetDrawColor(172, 172, 172);
                $pdf->SetFillColor(172, 172, 172);
                $pdf->SetXY(11, 61);
                $pdf->MultiCell(0.1, 204, '', 'L', 'L', 1, 1);
                $pdf->SetXY(11, 265);
                $pdf->MultiCell(180, 1, '', 'T', 'L', 0, 1);


                if ($i != ($nombre_apprenant - 1)) {
                    $pdf->AddPage();
                }
            }
        }
    }

}
