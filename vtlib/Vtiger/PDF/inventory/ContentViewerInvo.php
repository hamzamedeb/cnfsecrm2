<?php

/* +**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ********************************************************************************** */

include_once dirname(__FILE__) . '/../viewers/ContentViewer.php';

class Vtiger_PDF_InventoryInvoContentViewer extends Vtiger_PDF_ContentViewer {

    protected $headerRowHeight = 8;
    protected $onSummaryPage = false;

    function __construct() {
        // NOTE: General A4 PDF width ~ 189 (excluding margins on either side)

        $this->cells = array(// Name => Width
            'Code' => 19,
            'Name' => 89,
            'Quantity' => 17,
            'Price' => 26,
            //'Discount'=> 19,
            'Total' => 22,
            'Tax' => 17
        );
    }

    function initDisplay($parent) {
        //creation de tableau
        $pdf = $parent->getPDF();
        $contentFrame = $parent->getContentFrame();
        $pdf->SetDrawColor(159, 178, 193);
        //$pdf->MultiCell($contentFrame->w, $contentFrame->h, "", 1, 'L', 0, 1, $contentFrame->x, $contentFrame->y - 10);
        // Defer drawing the cell border later.
        if (!$parent->onLastPage()) {
            $this->displayWatermark($parent);
        }

        // Header	
        $offsetX = 0;
        $pdf->SetFont('', 'B');

        foreach ($this->cells as $cellName => $cellWidth) {
            $pdf->SetDrawColor(159, 178, 193);
            $pdf->SetFillColor(181, 189, 204);
            $cellLabel = ($this->labelModel) ? $this->labelModel->get($cellName, $cellName) : $cellName;
            $pdf->MultiCell($cellWidth, $this->headerRowHeight, $cellLabel, 1, 'C', 1, 1, $contentFrame->x + $offsetX, $contentFrame->y); //  - 10
            //echo $cellLabel;
            $offsetX += $cellWidth;
        }
        $pdf->SetFont('', '');
        // Reset the y to use
        $contentFrame->y += $this->headerRowHeight;
    }

    function drawCellBorder($parent, $cellHeights = False) {
        $pdf = $parent->getPDF();
        $contentFrame = $parent->getContentFrame();

        if (empty($cellHeights))
            $cellHeights = array();

        $offsetX = 0;
        foreach ($this->cells as $cellName => $cellWidth) {
            $pdf->SetDrawColor(159, 178, 193);
            $cellHeight = isset($cellHeights[$cellName]) ? $cellHeights[$cellName] : $contentFrame->h;
            $offsetY = $contentFrame->y - $this->headerRowHeight;
            //$pdf->MultiCell($cellWidth, $cellHeight, "", 1, 'L', 0, 1, $contentFrame->x + $offsetX, $offsetY - 10);
            $offsetX += $cellWidth;
        }
    }

    function display($parent) {
        $this->displayPreLastPage($parent);
        //$this->displayLastPage($parent);
    }

    function displayPreLastPage($parent) {

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
        $models = $this->contentModels;
        $totalModels = count($models);

        $pdf = $parent->getPDF();

        $parent->createPage2();
        $contentFrame = $parent->getContentFrame();
        $contentLineX = $contentFrame->x;
        $contentLineY = $contentFrame->y; // - 10
        $overflowOffsetH = 8; // This is offset used to detect overflow to next page
        for ($index = 0; $index < $totalModels; ++$index) {

            $model = $models[$index];

            $contentHeight = 1;

            // Determine the content height to use
            foreach ($this->cells as $cellName => $cellWidth) {
                $contentString = $model->get($cellName);
                if (empty($contentString))
                    continue;
                $contentStringHeight = $pdf->GetStringHeight($contentString, $cellWidth);
                if ($contentStringHeight > $contentHeight)
                    $contentHeight = $contentStringHeight;
            }

            // Are we overshooting the height?
            if (ceil($contentLineY + $contentHeight) > ceil($contentFrame->h + $contentFrame->y)) {

                $this->drawCellBorder($parent);
                $parent->createPage2();
                //image
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
                    $h = 30;
                }
                $pdf->Image("test/logo/logo-CNFSE.png", 10, 5, $w, $h);
                //fin image
                $contentFrame = $parent->getContentFrame();
                $contentLineX = $contentFrame->x;
                $contentLineY = $contentFrame->y - 10;
            }

            //date de formation
            $info_dates = $this->contentSummaryModel->get("info_dates");
            //var_dump($info_dates);
            $nbre_jour = count($info_dates);

            for ($i = 0; $i < $nbre_jour; $i++) {
                $list_date[$i] = $info_dates[$i]['date_start'];
            }
            //sort($list_date);
            $premier_jour = $list_date[0];
            if ($premier_jour != null) {
                $premier_jour = formatDateFr($premier_jour);
                //$date = new DateTime($premier_jour);
                //$premier_jour = $date->format('d-m-Y');
            }

            $dernier_jour = $list_date[$i - 1];
            if ($dernier_jour != null) {
                $dernier_jour = formatDateFr($dernier_jour);
                //$date = new DateTime($dernier_jour);
                //$dernier_jour = $date->format('d-m-Y');
            }

            $offsetX = 0;
            foreach ($this->cells as $cellName => $cellWidth) {
                $pdf->SetDrawColor(159, 178, 193);
                $pdf->SetFillColor(238, 240, 242);
                if ($cellName == 'Name') {
                    if ($premier_jour != '' && $dernier_jour != '') {
                        $pdf->MultiCell($cellWidth, $contentHeight, 'Période : du ' . $premier_jour . ' au ' . $dernier_jour, 0, 'L', 1, 1, $contentLineX + $offsetX, $contentLineY + 15);
                    }
                }
                /* uni_cnfsecrm - supprimer référence produit de la facture */
                $val_cell = ($cellName != 'Code') ? $model->get($cellName) : "";

                $pdf->MultiCell($cellWidth, $contentHeight + 20, $val_cell, 1, 'L', 1, 1, $contentLineX + $offsetX, $contentLineY);
                //echo $contentHeight;
                $offsetX += $cellWidth;
            }

            //ligne des Apprenants
            $info_apprenants = $this->contentSummaryModel->get("info_apprenants");
            //var_dump($info_apprenants);
            //$info_apprenants = array('test test test tst app 1 test test ', 'test test test app 2 test test ', 'test app 3 test tet');
            $nbr_apprenants = $info_apprenants['nbr_apprenants'];

            $offsetX = 0;
            foreach ($this->cells as $cellName => $cellWidth) {
                $pdf->SetDrawColor(159, 178, 193);
                $pdf->SetFillColor(238, 240, 242);
                if ($cellName == 'Code') {
                    $pdf->MultiCell($cellWidth, 40, 'APP', 1, 'L', 1, 1, $contentLineX + $offsetX, $contentLineY + 26);
                } elseif ($cellName == 'Name') {
                    $tiret = '';
                    $list_apprenant = '';
                    for ($i = 0; $i < $nbr_apprenants; $i++) {
                        $list = $tiret . '' . $info_apprenants[$i]['firstname'] . ' ' . $info_apprenants[$i]['lastname'];
                        $list_apprenant .= $list;
                        $tiret = '/';
                    }
                    //var_dump($list_apprenant);
                    $pdf->SetFont($trebuchet, '', 7);
                    $pdf->MultiCell($cellWidth, 40, $list_apprenant, 'TB', 'L', 1, 1, $contentLineX + $offsetX + 0.5 + $a, $contentLineY + 26);
                    $pdf->SetFont($trebuchet, '', 9);
                } elseif ($cellName == 'Quantity') {
                    $pdf->MultiCell($cellWidth, 40, $nbr_apprenants, 1, 'L', 1, 1, $contentLineX + $offsetX, $contentLineY + 26);
                } else {
                    $pdf->MultiCell($cellWidth, 40, '', 1, 'L', 1, 1, $contentLineX + $offsetX, $contentLineY + 26);
                }
                $offsetX += $cellWidth;
                // echo $contentHeight;
            }
            // fin ligne des Apprenants

            $contentLineY = $pdf->GetY();
            $commentContent = $model->get('Comment');

            /* uni_cnfsecrm - correction problème facture pdf après remplissage des descriptions */
//            if (!empty($commentContent)) {
//                $commentCellWidth = $this->cells['Name'];
//                $offsetX = $this->cells['Code'];
//
//                $contentHeight = $pdf->GetStringHeight($commentContent, $commentCellWidth);
//                if (ceil($contentLineY + $contentHeight + $overflowOffsetH) > ceil($contentFrame->h + $contentFrame->y)) {
//
//                    $this->drawCellBorder($parent);
//                    $parent->createPage();
//                    $contentFrame = $parent->getContentFrame();
//                    $contentLineX = $contentFrame->x;
//                    $contentLineY = $contentFrame->y;
//                }
//                //$pdf->MultiCell($commentCellWidth, $contentHeight, $model->get('Comment'), 0, 'L', 0, 1, $contentLineX + $offsetX, $contentLineY - 10);
//
//                $contentLineY = $pdf->GetY();
//            }
        }

        // Summary
        $cellHeights = array();

        if ($this->contentSummaryModel) {
            $summaryCellKeys = $this->contentSummaryModel->keys();
            $summaryCellCount = count($summaryCellKeys);
            $summaryCellLabelWidth = $this->cells['Price']; // + $this->cells['Total'];
            $summaryCellHeight = $pdf->GetStringHeight("TEST", $summaryCellLabelWidth); // Pre-calculate cell height
            $summaryTotalHeight = ceil(($summaryCellHeight * $summaryCellCount));


            if (($contentFrame->h + $contentFrame->y) - ($contentLineY + $overflowOffsetH) < 30) { //$overflowOffsetH is added so that last Line Item is not overlapping
                $this->drawCellBorder($parent);
                $pdf->AddPage();

                //ajout logo les autre page
                list($imageWidth, $imageHeight, $imageType, $imageAttr) = $parent->getimagesize("test/logo/logo-CNFSE.png");
                $w = $imageWidth / 3;
                $w = $imageWidth;
                if ($w > 30) {
                    $w = 70;
                }
                $h = $imageHeight;
                if ($h > 20) {
                    $h = 30;
                }
                $pdf->Image("test/logo/logo-CNFSE.png", 10, 5, $w, $h);
                //fin
                $contentFrame = $parent->getContentFrame();

                $contentLineX = $contentFrame->x;
                $contentLineY = $contentFrame->y;
            }
            $summaryLineX = $contentLineX + $this->cells['Code'] + $this->cells['Name'] + $this->cells['Quantity'];
            
            /* uni_cnfsecrm - v2 - modif 116 - DEBUT */
            $summaryLineY = ($contentFrame->h + $contentFrame->y - $this->headerRowHeight) - $summaryTotalHeight - 9;
            /* uni_cnfsecrm - v2 - modif 116 - FIN */
            
            $valeurs = $this->contentSummaryModel->get("valeurs");
            $type = $valeurs["type"];
            $mode_reglement = ($valeurs["mode_reglement"] != "") ? $valeurs["mode_reglement"] : "Chèque";
            //var_dump($type);
            // var_dump($summaryCellKeys);
            foreach ($summaryCellKeys as $key) {
                $pdf->SetFont($trebuchet, '', 9);
                $pdf->SetDrawColor(159, 178, 193);
                $pdf->SetFillColor(238, 240, 242);
                if ($key == "payments") {
                    continue;
                }
                if ($key == "info_apprenants") {
                    continue;
                }
                if ($key == "info_dates") {
                    continue;
                }
                if ($key == "valeurs") {
                    continue;
                }
                if ($type == "Financeur") {
                    if ($key == "Financement") {
                        continue;
                    }
                }

                if ($this->contentSummaryModel->get('Remise') == 0) {
                    if ($key == "Remise") {
                        continue;
                    }
                }

                if ($key == "taux_tva") {
                    continue;
                }

                if ($key == "NET A PAYER") {
                    $pdf->SetFont($trebuchetbd, '', 9);
                }


                $pdf->MultiCell($summaryCellLabelWidth, $summaryCellHeight, $key, 1, 'L', 1, 1, $summaryLineX, $summaryLineY);
                $pdf->MultiCell($contentFrame->w - $summaryLineX + 10 - $summaryCellLabelWidth, $summaryCellHeight, $this->contentSummaryModel->get($key), 1, 'R', 1, 1, $summaryLineX + $summaryCellLabelWidth, $summaryLineY);


                if ($this->contentSummaryModel->get('Remise') == 0) {
                    if ($key == "Total brut HT") {
                        $pdf->Ln(8);
                    }
                }
                if ($type == "Financeur") {
                    if ($key == "Remise") {
                        $pdf->Ln(8);
                    }
                }

                if ($key == "Financement" || $key == "TTC") {
                    $pdf->Ln(8);
                }
                if ($key == "Acompte") {
                    /* uni_cnfsecrm - v2 - modif 116 - DEBUT */
                    $pdf->Ln(17);
                    /* uni_cnfsecrm - v2 - modif 116 - FIN */
                }

                $summaryLineY = $pdf->GetY();

//                $pdf->MultiCell($summaryCellLabelWidth, $summaryCellHeight, $key, 1, 'L', 0, 1, $summaryLineX, $summaryLineY);
//                $pdf->MultiCell($contentFrame->w - $summaryLineX + 10 - $summaryCellLabelWidth, $summaryCellHeight, $this->contentSummaryModel->get($key), 1, 'R', 0, 1, $summaryLineX + $summaryCellLabelWidth, $summaryLineY);
//                $summaryLineY = $pdf->GetY();                
            }
            $cellIndex = 3;
            foreach ($this->cells as $cellName => $cellWidth) {
                if ($cellIndex < 2)
                    $cellHeights[$cellName] = $contentFrame->h;
                else
                    $cellHeights[$cellName] = $contentFrame->h - $summaryTotalHeight - 29;
                ++$cellIndex;
            }
        }


        // sumary1
        $cellHeights = array();
        if ($this->contentSummaryModel) {
            /* uni_cnfsecrm - changer le RIB 2021 */
//            $summary1 = array('CRÉDIT INDUSTRIEL ET COMMERCIAL', 'IBAN : FR76 3006 6106 9700 0205 9480 186', 'BIC : CMCIFRPP');
            $summary1 = array('Crédit Agricole', 'IBAN : FR76 1820 6000 5944 0040 8700 104', 'BIC : AGRIFRPP882');
            $summaryCellKeys = array_keys($summary1);
            $summaryCellCount = count($summaryCellKeys);

            $summaryCellLabelWidth = 95;
            $summaryCellHeight = $pdf->GetStringHeight("TEST", $summaryCellLabelWidth); // Pre-calculate cell height
            $summaryTotalHeight1 = ceil(($summaryCellHeight * $summaryCellCount));
            if (($contentFrame->h + $contentFrame->y) - ($contentLineY + $overflowOffsetH) < 30) { //$overflowOffsetH is added so that last Line Item is not overlapping
                $this->drawCellBorder($parent);
                $pdf->AddPage();
                $contentFrame = $parent->getContentFrame();
                $contentLineX = $contentFrame->x;
                $contentLineY = $contentFrame->y;
            }
            $summaryLineX = $contentLineX;
            $summaryLineY = ($contentFrame->h + $contentFrame->y - $this->headerRowHeight) - $summaryTotalHeight1 - 27;
            $pdf->SetDrawColor(159, 178, 193);
            $pdf->SetFillColor(181, 189, 204);
            $pdf->MultiCell($summaryCellLabelWidth, $summaryCellHeight, 'Domiciliation', 1, 'L', 1, 1, $summaryLineX, $summaryLineY);
            $summaryLineY = $pdf->GetY();

            $summaryLineX = $contentLineX;
            foreach ($summary1 as $key => $value) {
                $pdf->SetDrawColor(159, 178, 193);
                $pdf->SetFillColor(238, 240, 242);
                if ($key == 4) {
                    $pdf->MultiCell($summaryCellLabelWidth, $summaryCellHeight, $value, 'LRB', 'L', 1, 1, $summaryLineX, $summaryLineY);
                } else
                    $pdf->MultiCell($summaryCellLabelWidth, $summaryCellHeight, $value, 'LR', 'L', 1, 1, $summaryLineX, $summaryLineY);
                $summaryLineY = $pdf->GetY();
            }
            $cellIndex = 3;
            foreach ($this->cells as $cellName => $cellWidth) {
                if ($cellIndex < 2)
                    $cellHeights[$cellName] = $contentFrame->h;
                else
                    $cellHeights[$cellName] = $contentFrame->h - 83;
                ++$cellIndex;
            }
        }
        //fin sumary 1
        //sumary 2

        $totaltva = $this->contentSummaryModel->get("Total TVA");
        $netHT = $this->contentSummaryModel->get("Net HT");
        $taux_tva = $this->contentSummaryModel->get("taux_tva");
        $summary2 = array('Montant HT' => $netHT, 'Taux' => $taux_tva, 'Montant TVA ' => $totaltva);

        $cellHeights = array();
        if ($this->contentSummaryModel) {
            $summaryCellKeys = array_keys($summary2);
            $summaryCellCount = count($summaryCellKeys);
            $summaryCellLabelWidth = 35;
            $summaryCellHeight = $pdf->GetStringHeight("TEST", $summaryCellLabelWidth); // Pre-calculate cell height
            $summaryTotalHeight2 = ceil(($summaryCellHeight * $summaryCellCount));
            if (($contentFrame->h + $contentFrame->y) - ($contentLineY + $overflowOffsetH) < 30) { //$overflowOffsetH is added so that last Line Item is not overlapping
                $this->drawCellBorder($parent);
                $pdf->AddPage();
                $contentFrame = $parent->getContentFrame();
                $contentLineX = $contentFrame->x;
                $contentLineY = $contentFrame->y;
            }
            $summaryLineX = $contentLineX;
            $summaryLineY = ($contentFrame->h + $contentFrame->y - $this->headerRowHeight) - $summaryTotalHeight2 - $summaryTotalHeight1 - 27; //165
            $pdf->SetDrawColor(159, 178, 193);
            $pdf->SetFillColor(181, 189, 204);
            $pdf->MultiCell(95, $summaryCellHeight, 'TVA', 1, 'L', 1, 1, $summaryLineX, $summaryLineY);
            $summaryLineY = $pdf->GetY();

            $summaryLineX = $contentLineX;
            foreach ($summary2 as $key => $value) {
                $pdf->SetDrawColor(159, 178, 193);
                $pdf->SetFillColor(238, 240, 242);
                $pdf->MultiCell($summaryCellLabelWidth, $summaryCellHeight, $key, 1, 'C', 1, 1, $summaryLineX, $summaryLineY);
                $summaryLineX = $summaryLineX + 30;
            }
            $summaryLineY = $pdf->GetY();
            $summaryLineX = $contentLineX;
            foreach ($summary2 as $key => $value) {
                $pdf->SetDrawColor(159, 178, 193);
                $pdf->SetFillColor(238, 240, 242);
                $pdf->MultiCell($summaryCellLabelWidth, $summaryCellHeight, $value, 1, 'C', 1, 1, $summaryLineX, $summaryLineY);
                $summaryLineX = $summaryLineX + 30;
            }
            $cellIndex = 3;
            foreach ($this->cells as $cellName => $cellWidth) {
                if ($cellIndex < 2)
                    $cellHeights[$cellName] = $contentFrame->h;
                else
                    $cellHeights[$cellName] = $contentFrame->h - 83;
                ++$cellIndex;
            }
        }

        //fin summary 2
        //sumary 3        
        $date_echeance = $valeurs["echeance"];

        if ($date_echeance != null) {
            $date_echeance = formatDateFr($date_echeance);
            //$date = new DateTime($date_echeance);
            //$date_echeance = $date->format('d-m-Y');
        }


        $totalttc = $this->contentSummaryModel->get("TTC");
        $NET_A_PAYER = $this->contentSummaryModel->get("NET A PAYER");
        $summary3 = array('Date' => $date_echeance, 'Montant' => $NET_A_PAYER, 'Mode de reglement' => $mode_reglement);
        $cellHeights = array();

        if ($this->contentSummaryModel) {
            $summaryCellKeys = array_keys($summary3);
            $summaryCellCount = count($summaryCellKeys);
            $summaryCellLabelWidth = 35;
            $summaryCellHeight = $pdf->GetStringHeight("TEST", $summaryCellLabelWidth); // Pre-calculate cell height
            $summaryTotalHeight3 = ceil(($summaryCellHeight * $summaryCellCount));
            if (($contentFrame->h + $contentFrame->y) - ($contentLineY + $overflowOffsetH) < 30) { //$overflowOffsetH is added so that last Line Item is not overlapping
                $this->drawCellBorder($parent);
                $pdf->AddPage();
                $contentFrame = $parent->getContentFrame();
                $contentLineX = $contentFrame->x;
                $contentLineY = $contentFrame->y;
            }
            $summaryLineX = $contentLineX;
            $summaryLineY = ($contentFrame->h + $contentFrame->y - $this->headerRowHeight) - $summaryTotalHeight1 - $summaryTotalHeight2 - $summaryTotalHeight3 - 27; //165

            $pdf->SetDrawColor(159, 178, 193);
            $pdf->SetFillColor(181, 189, 204);
            $pdf->MultiCell(95, $summaryCellHeight, 'Echéances', 1, 'L', 1, 1, $summaryLineX, $summaryLineY);
            $summaryLineY = $pdf->GetY();
            $summaryLineX = $contentLineX;

            foreach ($summary3 as $key => $value) {
                $pdf->SetDrawColor(159, 178, 193);
                $pdf->SetFillColor(238, 240, 242);
                $pdf->MultiCell($summaryCellLabelWidth, $summaryCellHeight, $key, 1, 'C', 1, 1, $summaryLineX, $summaryLineY);
                $summaryLineX = $summaryLineX + 30;
            }
            $summaryLineY = $pdf->GetY();
            $summaryLineX = $contentLineX;
            foreach ($summary3 as $key => $value) {
                $pdf->SetDrawColor(159, 178, 193);
                $pdf->SetFillColor(238, 240, 242);
                $pdf->MultiCell($summaryCellLabelWidth, $summaryCellHeight, $value, 1, 'C', 1, 1, $summaryLineX, $summaryLineY);
                $summaryLineX = $summaryLineX + 30;
            }
            $cellIndex = 3;
            foreach ($this->cells as $cellName => $cellWidth) {
                if ($cellIndex < 2)
                    $cellHeights[$cellName] = $contentFrame->h;
                else
                    $cellHeights[$cellName] = $contentFrame->h - 83;
                ++$cellIndex;
            }
        }


        //fin summary 3

        $this->onSummaryPage = true;
        $this->drawCellBorder($parent, $cellHeights);


        $info_payments = $this->contentSummaryModel->get("payments");
//        var_dump($info_payments);
        $nbr_payments = count($info_payments);

        if ($nbr_payments > 0) {
            //facture acquittée
            $pdf->SetFillColor(255, 255, 255);
            $y = 250;
            for ($i = 0; $i <= $nbr_payments; $i++) {
                $pdf->SetFont($trebuchetbd, '', 8);
                $date_payments = formatDateFr($info_payments[$i]['date_payments']);
                $methode_payments = html_entity_decode($info_payments[$i]['methode_payments']);
                $methode_payments = ($methode_payments != "") ? ' par ' . $methode_payments : "";
                //$facture = 'Facture acquittée le ' . $date_payments . $methode_payments;
                $facture = 'Facture acquittée le ' . $date_payments;
                if ($date_payments != '') {
                    $pdf->MultiCell(100, 5, $facture, 0, 'L', 1, 1, 135, $y);
                }
                $y += 5;
            }
        } else {
            $y = 250;
        }
        $pdf->SetFillColor(255, 255, 255);
        //remarque
        $remarque = "Organisme de formation professionnelle continue exonéré de TVA en vertu de l'article 202A à 202D de l'annexe II du CGI \"et article 261.4.4 du code général des impôts.";
        $pdf->SetFont($trebuchet, '', 6.5);
        $pdf->MultiCell(200, 5, $remarque, 0, 'C', 1, 1, 5, $y += 1);

        /* uni_cnfsecrm */
        //page des dates :

        $info_dates = $this->contentSummaryModel->get("info_dates");
        $nbr_journee = count($info_dates);
        //echo $nbr_journee;
        if ($nbr_journee != 0) {

            $pdf->AddPage();
            //logo
            list($imageWidth, $imageHeight, $imageType, $imageAttr) = $parent->getimagesize("test/logo/logo-CNFSE-large.png");
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
            $pdf->Image("test/logo/logo-CNFSE-large.png", 10, 5, $w, $h);

            $y = 0;
            $x = 0;
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetTextColor(0, 0, 0);



            $pdf->SetXY($x += 10, $y += 35);
            $tbl = '<table width="90%" border="0" align="justify" cellpadding="2" cellspacing="1" style="font-size:8pt;">
                    <tbody>
                       <tr>
                            <td colspan="5">
                                <p align="left" style="font-family: "Trebuchet MS", Helvetica, sans-serif;font-weight: normal;font-size: 8pt;">Calendrier des Journées</p>
                            </td>
                        </tr>
                       <tr style="font-size: 8pt;">
                            <th width=60 align="center" valign="middle" nowrap bgcolor="#7F9DB9">N° Journée</th>
                            <th width=90 align="center" valign="middle" nowrap bgcolor="#7F9DB9">Date</th>
                            <th width=90 align="center" valign="middle" nowrap bgcolor="#7F9DB9">Heures matin</th>
                            <th width=90 align="center" valign="middle" nowrap bgcolor="#7F9DB9">Heures après-midi</th>
                            <th width=90 align="center" valign="middle" nowrap bgcolor="#7F9DB9">Durée</th>
                            <th width=90 align="center" valign="middle" nowrap bgcolor="#7F9DB9">Commentaire</th>
                        </tr>';

            for ($i = 0; $i < $nbr_journee; $i++) {
                if ($i % 2 == 0) {
                    $couleur = "#D4E2EE";
                } else {
                    $couleur = "#FEFFDD";
                }
                $num_journee = $info_dates[$i]["sequence_no"];
                $date_formation = $info_dates[$i]["date_start"];
                $date_formation = formatDateFr($date_formation);
                $heure_debut_matin = $info_dates[$i]["start_matin"];
                $heure_fin_matin = $info_dates[$i]["end_matin"];
                $heure_debut_apresmidi = $info_dates[$i]["start_apresmidi"];
                $heure_debut_apremidi = $info_dates[$i]["end_apresmidi"];
                $duree_formation = $info_dates[$i]["duree_formation"];
                //var_dump($date_formation);
                $tbl .= '<tr style = "font-size: 8pt;">
                <td width=60 align="right" bgcolor="' . $couleur . '">' . $num_journee . '</td> 
                <td width=90 align="center" bgcolor="' . $couleur . '">' . $date_formation . '</td>
                <td width=90 align="center" bgcolor="' . $couleur . '">De ' . $heure_debut_matin . ' à ' . $heure_fin_matin . '</td>
                <td width=90 align="center" bgcolor="' . $couleur . '">De ' . $heure_debut_apresmidi . ' à ' . $heure_debut_apremidi . '</td>
                <td width=90 align="center" bgcolor="' . $couleur . '">' . $duree_formation . '</td>
                <td width=90 align="center" bgcolor="' . $couleur . '"></td>    
                </tr>';
            }
            $tbl .= '</tbody>
            </table>';
            $pdf->writeHTML($tbl, true, false, true, false, '');
        }
    }

    function displayLastPage($parent) {
        // Add last page to take care of footer display
        if ($parent->createLastPage()) {
            $this->onSummaryPage = false;
        }
    }

    function drawStatusWaterMark($parent) {
        $pdf = $parent->getPDF();

        $waterMarkPositions = array("30", "180");
        $waterMarkRotate = array("45", "50", "180");
//        $pdf->SetFont('Arial', 'B', 50);
//        $pdf->SetTextColor(230, 230, 230);
//        $pdf->Rotate($waterMarkRotate[0], $waterMarkRotate[1], $waterMarkRotate[2]);
//        $pdf->Text($waterMarkPositions[0], $waterMarkPositions[1], 'created');
//        $pdf->Rotate(0);
//        $pdf->SetTextColor(0, 0, 0);
    }

}
