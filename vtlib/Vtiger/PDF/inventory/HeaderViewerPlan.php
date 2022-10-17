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

class Vtiger_PDF_PlanHeaderViewer extends Vtiger_PDF_HeaderViewer {

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
        $pdf->setPageFormat('A5', 'L');
        $headerFrame = $parent->getHeaderFrame();
        if ($this->model) {
            $headerColumnWidth = $headerFrame->w / 3.0;
            $modelColumns = $this->model->get('columns');
            // Column 1
            $offsetX = 5;
            $pdf->SetPrintFooter(false);
            $modelColumn0 = $modelColumns[0];
            $calibri = TCPDF_FONTS2::addTTFfont('test/font/Calibri/calibri.ttf', 'TrueTypeUnicode', '', 96);
            $pdf->SetFont($calibrib, '', 20);
            $pdf->MultiCell(200, 5, "Plan d'accès", '', 'C', 0, 1, 0, 10);

            $adresse_formation = decimalFormat($modelColumn0['adresse_formation']);
            $cp_formation = decimalFormat($modelColumn0['cp_formation']);
            $ville_formation = decimalFormat($modelColumn0['ville_formation']);

            //recuperer latitude et longitude
            $adresse = $adresse_formation . ", " . $cp_formation . ", " . $ville_formation;
            $adresse = str_replace(" ", "+", $adresse);
            $url = "https://maps.google.com/maps/api/geocode/json?sensor=false&key=AIzaSyBwGYE4Ag3fK0bSNNwEkEbj3RkO91AX9y0&address=$adresse";
            $response = file_get_contents($url);
            $json = json_decode($response, TRUE);
            $latitude = ($json['results'][0]['geometry']['location']['lat']) ? $json['results'][0]['geometry']['location']['lat'] : '--';
            $longitude = ($json['results'][0]['geometry']['location']['lng']) ? $json['results'][0]['geometry']['location']['lng'] : '--';

            $image = "https://maps.googleapis.com/maps/api/staticmap?center=" . $adresse_formation . " " . $cp_formation . " " . $ville_formation . "&zoom=10&size=345x150&key=AIzaSyBwGYE4Ag3fK0bSNNwEkEbj3RkO91AX9y0";
            $image1 = 'https://maps.googleapis.com/maps/api/staticmap?center=' . $adresse_formation . ' ' . $cp_formation . ' ' . $ville_formation . '&markers=color:red%7Clabel:C%7C' . $latitude . ',' . $longitude . '&zoom=16&size=550x250&key=AIzaSyBwGYE4Ag3fK0bSNNwEkEbj3RkO91AX9y0';
//            var_dump($image1)   ;die(); 
//                echo $image1;
//                die();
            $pdf->Image($image1, 12.5, 40, 230, 85, 'PNG', '', '', false, 300, '', false, false, 0, 'C', false, false);
            
        }
    }

}
