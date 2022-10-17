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

class Vtiger_PDF_InventoryHeaderViewer extends Vtiger_PDF_HeaderViewer {

    function totalHeight($parent) {
        $height = 100;

        if ($this->onEveryPage)
            return $height;
        if ($this->onFirstPage && $parent->onFirstPage())
            $height;
        return 0;
    }

    function display($parent) {
        echo "test01";
        $pdf = $parent->getPDF();
        $headerFrame = $parent->getHeaderFrame();
        if ($this->model) {
            $headerColumnWidth = $headerFrame->w / 3.0;

            $modelColumns = $this->model->get('columns');

            // Column 1
            $offsetX = 5;

            $modelColumn0 = $modelColumns[0];

            list($imageWidth, $imageHeight, $imageType, $imageAttr) = $parent->getimagesize(
                    "test/logo/logo-CNFSE.png");
            //division because of mm to px conversion
            $w = $imageWidth / 3;
            
            $w = $imageWidth;
            if ($w > 30) {
                $w = 43;
            }
            $h = $imageHeight;
            if ($h > 20) {
                $h = 15;
            }
            $pdf->Image("test/logo/logo-CNFSE.png", $headerFrame->x, $headerFrame->y, $w, $h);
            //column 1 num devis

            $imageHeightInMM = 70;
            //$pdf->SetXY(10, $imageHeightInMM);
            //$pdf->MultiCell(100, 5,"test" , 0, 'L', 0, 1, 10, $imageHeightInMM+100);

            $n_devis = "DV9651-20190122";
            $pdf->SetFont('times', 'B', 10);
            $pdf->MultiCell(100, 5, "DEVIS N° " . $n_devis, 0, 'L', 0, 1, 10, $imageHeightInMM);
            //column2 destination
            $pdf->SetFont('times', '', 10);
            $nom_ste = "AEIS MECS GODARD";
            $distinataire = "Madame Teresa DOS SANTOS";
            $rue = "14 rue carton";
            $code_postale = "33200";
            $ville = "BORDEAUX";
            $pays = "PARIS";
            $date = "22/01/2019";
            $pdf->MultiCell(60, 5, $nom_ste, 0, 'L', 0, 1, 110, $imageHeightInMM - 10);
            $pdf->MultiCell(60, 5, $distinataire, 0, 'L', 0, 1, 110, $imageHeightInMM - 5);
            $pdf->MultiCell(60, 5, $rue, 0, 'L', 0, 1, 110, $imageHeightInMM);
            $pdf->MultiCell(60, 5, $code_postale . " " . $ville, 0, 'L', 0, 1, 110, $imageHeightInMM + 5);
            $pdf->MultiCell(60, 5, $pays . " le " . $date, 0, 'L', 0, 1, 110, $imageHeightInMM + 15);
            //sujet 
            $imageHeightInMM = 100;
            $pdf->SetFont('times', '', 10);
            $pdf->MultiCell(200, 5, "Madame,", 0, 'L', 0, 1, 10, $imageHeightInMM);
            $text_sujet = "Suite à votre demande, je vous prie de bien vouloir trouver notre proposition pour la formation suivante :";
            $pdf->MultiCell(200, 5, $text_sujet, 0, 'L', 0, 1, 10, $imageHeightInMM + 5);
            $nom_formation = "Hygiéne alimentaire en restauration commerciale et collective- HACCP(cpf:181478)";
            $pdf->SetFont('times', 'B', 9.5);
            $pdf->MultiCell(200, 5, $nom_formation, 0, 'L', 0, 1, 10, $imageHeightInMM + 10);

            //les details 
            $imageHeightInMM = 130;
            // detail column 1
            $pdf->SetXY(10, $imageHeightInMM);
            $dure = "2";
            $horaires = "de 09:30 h à 13:00 h et de 14:00 h à 17:30 h";
            $cout_journalier = "170,00";
            $nbre_personne = "4";
            $dates = "à convenir";
            $lieu = "BORDEAUX 04";
            $tbl = <<<EOD
    <table>
        <tr style="border-right: 1px solid red;"> 
            <td width="90">Durée:</td>
            <td width="90">$dure Jour(s)</td>
        </tr>
        
        <tr>
            <td width="90">Horaires:</td>
            <td width="90">$horaires</td>
        </tr>
        
        <tr>
            <td width="90">Cout journalier:</td>
            <td width="90">$cout_journalier  € /pers.</td>
        </tr>
        
        <tr>
            <td width="90">Nbre personne:</td>
            <td width="90">$nbre_personne</td>
        </tr>
        
        <tr>
            <td width="90">Dates:</td>
            <td width="90">$dates</td>
        </tr>
        
        <tr>
            <td width="90">Lieu:</td>
            <td width="90">$lieu</td>
        </tr>
    </table>
EOD;
            $pdf->writeHTML($tbl, true, false, false, false, '');



            // detail column 2
            $pdf->SetXY(80, $imageHeightInMM);
            $cout_support = "0,00";
            $frait_dep = "0,00";
            $frait_heb = "0,00";
            $frai_repas = "0,00";
            $autre_frai = "0,00";

            $tb2 = <<<EOD
    <table>
        <tr>
            <td width="90">Cout support (u) :</td>
            <td width="90">$cout_support €</td>
        </tr>
        
        <tr>
            <td width="90">Frais dépl. :</td>
            <td width="90">$frait_dep €</td>
        </tr>
        
        <tr>
            <td width="90">Frais Héberg. :</td>
            <td width="90">$frait_heb €</td>
        </tr>
        
        <tr>
            <td width="90">Frais Repas :</td>
            <td width="90">$frai_repas €</td>
        </tr>
        
        <tr>
            <td width="90">Autres frais :</td>
            <td width="90">$autre_frai €</td>
        </tr>
       
    </table>
EOD;
            $pdf->writeHTML($tb2, true, false, false, false, '');





            // detail column 3
            $pdf->SetXY(130, $imageHeightInMM);
            $sout_total = "1 360,00";
            $total_frai = "0,00";
            $total_support = "0,00";
            $remise = "664,00";
            $total_ht = "696,00";
            $tva = "0,00";
            $total_ttc = "696,00";

            $tb3 = <<<EOD
    <table>
        <tr>
            <td width="90">Sous-total : </td>
            <td align="right" style="text-align: left;" width="90">$sout_total €</td>
        </tr>
        
        <tr>
            <td width="90">Total Frais :</td>
            <td align="right" width="90">$total_frai €</td>
        </tr>
        
        <tr>
            <td width="90">Total Support :</td>
            <td align="right" width="90">$total_support €</td>
        </tr>
        
        <tr>
            <td width="90">Remise :</td>
            <td align="right" width="90">$remise €</td>
        </tr>
        
        <tr>
            <td width="90">Total HT :</td>
            <td align="right" width="90">$total_ht €</td>
        </tr>
        
         <tr>
            <td width="90">TVA :</td>
            <td align="right" width="90">$tva €</td>
        </tr>
        
         <tr>
            <td width="90">Total TTC:</td>
            <td align="right" width="90">$total_ttc €</td>
        </tr>
       
    </table>
EOD;
            $pdf->writeHTML($tb3, true, false, false, false, '');


//signiature :

            $pdf->SetXY(10, $imageHeightInMM + 45);
            $pdf->SetFont('times', '', 9);
            $text_s = "La signature du présent devis vaut pour acceptation des conditions générales de vente. Cochez la ou les formations retenues.";
            $pdf->MultiCell(200, 5, $text_s, 0, 'L', 0, 1);

            $pdf->SetXY(10, $imageHeightInMM + 50);
            $pdf->SetFont('times', 'B', 9);
            $date_validite_devis = "23/03/2019";
            $pdf->MultiCell(200, 5, "Date de validité du devis :" . $date_validite_devis, 0, 'L', 0, 1);


            $pdf->SetXY(10, $imageHeightInMM + 55);
            $pdf->SetFont('times', '', 9);
            $text_prise_charge = "Prise en charge selon votre organisme collecteur.";
            $pdf->MultiCell(200, 5, $text_prise_charge, 0, 'L', 0, 1);

            $pdf->SetXY(105, $imageHeightInMM + 60);
            $pdf->SetFont('times', 'I', 9);
            $text_bon_pour = "BON POUR ACCORD (cachet + signature)";
            $pdf->SetFillColor(255, 255, 255);
            $pdf->MultiCell(90, 30, $text_bon_pour, 1, 'L', 1, 1, '', '', true, 0, false, true, 30, 'T');


            $pdf->SetXY(10, $imageHeightInMM + 90);
            $pdf->SetFont('times', '', 9);
            $text_salutation = "Je reste à votre entiËre disposition pour tout renseignement supplèmentaire, et vous prie d'agrèer, Madame, mes meilleures salutations. ";
            $pdf->MultiCell(200, 5, $text_salutation, 0, 'L', 0, 1);

            $pdf->SetXY(10, $imageHeightInMM + 97);
            $pdf->SetFont('times', 'B', 10);
            $text_signature = "SIGNATURE";
            $pdf->MultiCell(200, 5, $text_signature, 0, 'C', 0, 1);

            $pdf->SetXY(10, $imageHeightInMM + 103);
            $pdf->SetFont('times', '', 9);
            $text_signature_fonction = "Fonction";
            $pdf->MultiCell(200, 5, $text_signature_fonction, 0, 'C', 0, 1);

            $pdf->SetXY(10, $imageHeightInMM + 97);
            $cachet = "image cachet";
            $pdf->MultiCell(180, 5, $cachet, 0, 'R', 0, 1);

            $rem = 'Organisme de formation professionnelle continue exonéré de TVA en vertu de l\'article 202A à 202D de l\'annexe II du CGI "et article 261.4.4 du code général des impôts';
            $pdf->SetXY(1, $imageHeightInMM + 132);
            $pdf->SetFont('times', '', 7);
            $pdf->MultiCell(200, 5, $rem, 0, 'C', 0, 1);


// Add the border cell at the end
            // This is required to reset Y position for next write
            $pdf->MultiCell($headerFrame->w, $headerFrame->h - $headerFrame->y, "", 0, 'L', 0, 1, $headerFrame->x, $headerFrame->y);
//header page 2           
            $pdf->AddPage();
            $page_2 = '1';
            //logo

            //division because of mm to px conversion
            $w = $imageWidth / 3;
            
            $w = $imageWidth;
            if ($w > 30) {
                $w = 52;
            }
            $h = $imageHeight;
            if ($h > 20) {
                $h = 18;
            }            
            $pdf->Image("test/logo/logo-CNFSE.png", 10, 1, $w, $h);
            //nom devis
            $pdf->SetXY(160, $page_2 + 1);
            $pdf->SetFont('times', '', 9);
            $pdf->SetTextColor(255, 0, 0);
            $header = "à retourner avec le devis";
            $pdf->MultiCell(200, 5, $header, 0, 'L', 0, 1);

            $pdf->SetXY(160, $page_2 + 10);
            $pdf->SetFont('times', 'B', 12);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->MultiCell(200, 5, $n_devis, 0, 'L', 0, 1);

            //titre page

            $pdf->SetXY(0, $page_2 + 17);
            $pdf->SetFont('times', 'B', 15);
            $pdf->SetTextColor(0, 0, 0);
            $titre_page = "FICHE D'INSCRIPTION";
            $pdf->MultiCell(200, 5, $titre_page, 0, 'C', 0, 1);

            //information client


            $pdf->SetTextColor(0, 0, 0);
            $nom_client = "AEIS MECS GODARD";
            $adresse = "14 rue carton";
            $tel = "0556128993";
            $email = "p.marty@aeis.fr";
            $adresse_com = "................................................................";
            $code_postale = "33200";
            $ville = "BORDEAUX";

            //nom
            $pdf->SetXY(20, $page_2 + 30);
            $pdf->SetFont('times', 'B', 11);
            $pdf->MultiCell(40, 5, 'Client :', 0, 'L', 0, 1);
            $pdf->SetFont('times', '', 11);
            $pdf->SetXY(55, $page_2 + 30);
            $pdf->MultiCell(40, 5, $nom_client, 0, 'L', 0, 1);

            //Adresse
            $pdf->SetXY(20, $page_2 + 38);
            $pdf->SetFont('times', 'B', 11);
            $pdf->MultiCell(40, 5, 'Adresse :', 0, 'L', 0, 1);
            $pdf->SetFont('times', '', 11);
            $pdf->SetXY(55, $page_2 + 38);
            $pdf->MultiCell(40, 5, $adresse, 0, 'L', 0, 1);

            //Adresse comple
            $pdf->SetXY(20, $page_2 + 46);
            $pdf->SetFont('times', 'B', 11);
            $pdf->MultiCell(40, 5, 'Adresse compl. :', 0, 'L', 0, 1);
            $pdf->SetFont('times', '', 11);
            $pdf->SetXY(55, $page_2 + 46);
            $pdf->MultiCell(100, 5, $adresse_com, 0, 'L', 0, 1);

            //code postale
            $pdf->SetXY(20, $page_2 + 53);
            $pdf->SetFont('times', 'B', 11);
            $pdf->MultiCell(40, 5, 'Code Postal :', 0, 'L', 0, 1);
            $pdf->SetFont('times', '', 11);
            $pdf->SetXY(55, $page_2 + 53);
            $pdf->MultiCell(40, 5, $code_postale, 0, 'L', 0, 1);

            //tel
            $pdf->SetXY(120, $page_2 + 30);
            $pdf->SetFont('times', 'B', 11);
            $pdf->MultiCell(40, 5, 'Tél :', 0, 'L', 0, 1);
            $pdf->SetFont('times', '', 11);
            $pdf->SetXY(135, $page_2 + 30);
            $pdf->MultiCell(40, 5, $tel, 0, 'L', 0, 1);

            //email
            $pdf->SetXY(120, $page_2 + 38);
            $pdf->SetFont('times', 'B', 11);
            $pdf->MultiCell(40, 5, 'E-mail :', 0, 'L', 0, 1);
            $pdf->SetFont('times', '', 11);
            $pdf->SetXY(135, $page_2 + 38);
            $pdf->MultiCell(40, 5, $email, 0, 'L', 0, 1);

            //ville
            $pdf->SetXY(120, $page_2 + 53);
            $pdf->SetFont('times', 'B', 11);
            $pdf->MultiCell(40, 5, 'Ville :', 0, 'L', 0, 1);
            $pdf->SetFont('times', '', 11);
            $pdf->SetXY(135, $page_2 + 53);
            $pdf->MultiCell(40, 5, $ville, 0, 'L', 0, 1);

            //nom formation 
            $pdf->SetXY(20, $page_2 + 65);
            $pdf->SetFont('times', 'B', 11);
            $pdf->MultiCell(200, 5, $nom_formation, 0, 'L', 0, 1);


            //titre formation

            $pdf->SetXY(20, $page_2 + 72);
            $pdf->SetFont('times', 'B', 11);
            $pdf->MultiCell(50, 5, 'Prénom', 0, 'L', 0, 1);

            $pdf->SetXY(60, $page_2 + 72);
            $pdf->SetFont('times', 'B', 11);
            $pdf->MultiCell(50, 5, 'Nom', 0, 'L', 0, 1);

            $pdf->SetXY(120, $page_2 + 72);
            $pdf->SetFont('times', 'B', 11);
            $pdf->MultiCell(50, 5, 'Fonction des Apprenants', 0, 'L', 0, 1);

            $pdf->SetXY(170, $page_2 + 72);
            $pdf->SetFont('times', 'B', 11);
            $pdf->MultiCell(50, 5, 'Date naissance', 0, 'L', 0, 1);


            //lists
            $prenom = '';
            $nom = '';
            $fonction = '';
            $date_naissance = '';
            $a = 0;
            $y = $page_2 + 82;

            for ($i = 1; $i <= 16; $i++) {

                //num
                $pdf->SetXY(10, $y + $a);
                $pdf->SetFont('times', 'B', 11);
                $pdf->MultiCell(10, 5, $i, 0, 'L', 0, 1);

                //column 1
                $pdf->SetXY(20, $y + $a);
                $pdf->SetFillColor(100, 0, 0, 0);
                $pdf->SetFont('times', '', 11);
                $pdf->MultiCell(35, 5, $prenom, 1, 'L', 0, 1);

                //column 2
                $pdf->SetXY(60, $y + $a);
                $pdf->SetFont('times', '', 11);
                $pdf->MultiCell(55, 5, $nom, 1, 'L', 0, 1);

                //column 3
                $pdf->SetXY(120, $y + $a);
                $pdf->SetFont('times', '', 11);
                $pdf->MultiCell(45, 5, $fonction, 1, 'L', 0, 1);

                //column 4
                $pdf->SetXY(170, $y + $a);
                $pdf->SetFont('times', '', 11);
                $pdf->MultiCell(30, 5, $date_naissance, 1, 'L', 0, 1);

                $a = $a + 9;
            }

            $pdf->SetXY(20, $page_2 + 235);
            $pdf->SetFont('times', '', 10);
            $remarque = "Le client reconnait avoir pris connaissance des modalités d'annulation décrites dans les Conditions Générales de vente ci-jointes.";
            $pdf->MultiCell(200, 5, $remarque, 0, 'L', 0, 1);
            $pdf->AddPage();
            
            
            
            
            
           
        }
    }

}
