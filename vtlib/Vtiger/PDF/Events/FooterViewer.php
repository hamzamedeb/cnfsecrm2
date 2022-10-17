<?php

/* +**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ********************************************************************************** */
include_once dirname(__FILE__) . '/../viewers/FooterViewer.php';

class Vtiger_PDF_EventsFooterViewer extends Vtiger_PDF_FooterViewer {

    static $DESCRIPTION_DATA_KEY = '__DES__DATA__';
    static $TERMSANDCONDITION_DATA_KEY = '__TANDC__DATA__';
    static $DESCRIPTION_LABEL_KEY = '__DES_LABEL__';
    static $TERMSANDCONDITION_LABEL_KEY = '__TANDC_LABEL__';

    function totalHeight($parent) {
        if ($this->model && $this->onEveryPage()) {
            $pdf = $parent->getPDF();

            $footerTitleHeight = 8.0;

            $termsConditionText = $this->model->get(self::$TERMSANDCONDITION_DATA_KEY);
            $termsConditionHeight = $pdf->GetStringHeight($termsConditionText, $parent->getTotalWidth());

            if ($termsConditionHeight)
                $termsConditionHeight += $footerTitleHeight;

            $descriptionText = $this->model->get(self::$DESCRIPTION_DATA_KEY);
            $descriptionHeight = $pdf->GetStringHeight($descriptionText, $parent->getTotalWidth());

            if ($descriptionHeight)
                $descriptionHeight += $footerTitleHeight;

            return $termsConditionHeight + $descriptionHeight;
        }
        return parent::totalHeight($parent);
    }

    function display($parent) {

        $pdf = $parent->getPDF();
        $footerFrame = $parent->getFooterFrame();
        
        if ($this->model) {
            
            $page_2 = '0';
            $pdf->SetXY(10, $page_1 + 1);


            list($imageWidth, $imageHeight, $imageType, $imageAttr) = $parent->getimagesize(
                    "test/logo/logo-CNFSE.png");
            //division because of mm to px conversion
            $w = $imageWidth / 2;
            
            
            if ($w > 30) {
                $w = 75;
            }
            $h = $imageHeight;
            if ($h > 20) {
                $h = 25;
            }
            $pdf->Image("test/logo/logo-CNFSE.png", 10, 5, $w, $h);
             
            $pdf->SetXY(150, $page_1 + 27);
            $pdf->SetFont('times', '', 10);
            $pdf->MultiCell(50, 5, "Calendrier des journées", 0, 'L', 0, 1);

//tableau
            $date = "28/01/2019";
            $d_matin = "9:00";
            $f_matin = "13:00";
            $d_midi = "14:00";
            $f_midi = "17:00";
            $duree = "7:00";
            $commentaire = "";
            $pdf->SetXY(10, $page_1 + 35);
            $tbl = <<<EOD
<table style="border: 1px solid #FFFFFF; text-align:center;" >
    <tr style="background-color:rgb(127, 157, 185);padding: 15px;">
        <td style="padding: 15px;">N° Journée</td>
        <td style="padding: 15px;">Date</td>
        <td style="padding: 15px;">Heures matin</td>
        <td style="padding: 15px;">Heures après-midi</td>
        <td style="padding: 15px;">Durée</td>
        <td style="padding: 15px;">Commentaire</td>           
    </tr>
                   
    <tr style="background-color:rgb(212, 226, 238);"> 
        <td></td> 
        <td>$date</td>
        <td> De $d_matin à $f_matin</td>
        <td> De $d_midi à $f_midi </td>
        <td>$duree</td>
        <td>$commentaire</td>            
    </tr>
    <tr style="background-color:rgb(255, 255, 232);"> 
        <td></td> 
        <td>$date</td>
        <td> De $d_matin à $f_matin</td>
        <td> De $d_midi à $f_midi </td>
        <td>$duree</td>
        <td>$commentaire</td>            
    </tr>

</table>
EOD;

            $pdf->writeHTML($tbl, true, false, false, false, '');
//            $targetFooterHeight = ($this->onEveryPage()) ? $footerFrame->h : 0;
//            $descriptionString = $this->labelModel->get(self::$DESCRIPTION_LABEL_KEY);
//            $description = $this->model->get(self::$DESCRIPTION_DATA_KEY);
//            $descriptionHeight = $pdf->GetStringHeight($descriptionString, $footerFrame->w);
//            $pdf->SetFillColor(205, 201, 201);
//            $pdf->MultiCell($footerFrame->w, $descriptionHeight, $descriptionString, 1, 'L', 1, 1, $footerFrame->x, $footerFrame->y);
//            $pdf->MultiCell($footerFrame->w, $targetFooterHeight - $descriptionHeight, $description, 1, 'L', 0, 1, $footerFrame->x, $footerFrame->y + $descriptionHeight);
//            $termsAndConditionLabelString = $this->labelModel->get(self::$TERMSANDCONDITION_LABEL_KEY);
//            $termsAndCondition = $this->model->get(self::$TERMSANDCONDITION_DATA_KEY);
//            $offsetY = 2.0;
//            $termsAndConditionHeight = $pdf->GetStringHeight($termsAndConditionLabelString, $footerFrame->w);
//            $pdf->SetFillColor(205, 201, 201);
//            $pdf->MultiCell($footerFrame->w, $termsAndConditionHeight, $termsAndConditionLabelString, 1, 'L', 1, 1, $pdf->GetX(), $pdf->GetY() + $offsetY);
//            $pdf->MultiCell($footerFrame->w, $targetFooterHeight - $termsAndConditionHeight, $termsAndCondition, 1, 'L', 0, 1, $pdf->GetX(), $pdf->GetY());
        }
    }
}

?>