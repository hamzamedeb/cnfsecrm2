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
            //test 
             
            //titre
            $pdf->SetXY(70, 10);
            $pdf->SetFont('times', 'B', 20);
            //$pdf->SetDrawColor(84, 141, 212);
            $pdf->SetFillColor(219, 229, 241);
            $pdf->SetLineStyle(array('width' => 1.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(84, 141, 212)));
            $pdf->MultiCell(70, 10, 'Plan accès métro', 1, 'C', 1, 1);

            //image 1

            $pdf->Image("test/logo/ri_2.jpeg", 15, 35, 180, 80);
            
            //texte
            $pdf->SetXY(17, 120);
            $pdf->SetFont('times', '', 17);
            
            $texte = <<<EOD
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
                <tbody>
                    <tr>
                      <td><strong>Métro ligne 8</strong> direction Point du lac Créteil</td>
                    </tr>
                    <tr>
                    <td>
                        <ol>
                      <li>Station « <strong>Charenton-Ecole</strong> »</li>
                      <li>Prendre la sortie <strong>rue Gabrielle</strong></li>
                      <li>Suivre la rue Gabrielle sur 230 mètres</li>
                      </ol>
                    </td>
                    </tr>
                </tbody>
            </table>
          
EOD;
            $pdf->writeHTML($texte, true, false, false, false, '');
                
            //image 2
            $pdf->Image("test/logo/street.jpg", 25, 160, 160, 70);
            //texte 2
            //$adresse = "7 place Henri IV – 94220 Charenton-le-Pont – 1er étage";
            $code_postal = "94220";
            $rue = "7 place Henri IV";
            $tel = "01.84.16.38.25";
            $etage = "1";
            $ville = "Charenton-le-Pont";
            $pdf->SetXY(30, 235);
            $pdf->SetFont('times', '', 17);
            $pdf->MultiCell(150, 5, ''.$rue.' – '.$code_postal.' '.$ville.' – '.$etage.'er étage Tél: '.$tel.' ', 0, 'C', 0, 1);
            
            //logo footer
            $pdf->Image("test/logo/ri_1.png", 158, 250, 30, 15);
            
            
            
            
            $pdf->SetFillColor(0, 0, 0);
            $pdf->SetDrawColor(0, 0, 0);
            $pdf->SetLineStyle(array('width' => 0, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
            $pdf->SetFont('times', '', 11);
            $pdf->AddPage();
            
        }
    }

}
