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

class Vtiger_PDF_InventoryQuotHeaderViewer extends Vtiger_PDF_HeaderViewer {

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
            $pdf->Image("test/logo/logo-CNFSE-large.png", 10, 15, $w, $h);
            //echo $w ." ".$h;
            /* uni_cnfsecrm - v2 - modif 158 - DEBUT */
            $pdf->Image("test/logo/LogoQualiopi-Marianne.jpg", 145, 13, 32, 17);
            $pdf->Image("test/logo/compte.png", 180, 13, 15, 15);
            /* uni_cnfsecrm - v2 - modif 158 - FIN */
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

            $y = 70;
            //$pdf->SetXY(10, $image$yHeightInMM);
            //$pdf->MultiCell(100, 5,"test" , 0, 'L', 0, 1, 10, $y+100);
            $num_devis = $modelColumn0['num_devis'];
            $pdf->SetFont($trebuchetbd, '', 9);
            $pdf->MultiCell(100, 5, "DEVIS N° " . $num_devis, 0, 'L', 0, 1, 10, $y);
            //column2 destination 

            $pdf->SetFont($trebuchet, '', 10);
            $titre_contact = html_entity_decode($modelColumn0['info_contact']['titre_contact']);
            $nom_contact = html_entity_decode($modelColumn0['info_contact']['nom_contact']);
            $prenom_contact = html_entity_decode($modelColumn0['info_contact']['prenom_contact']);
            $travail_contact = html_entity_decode($modelColumn0['info_contact']['travail_contact']);
            $date_quote = formatDateFr($modelColumn0['date_quote']['date_creation']);

            $ville_quote = html_entity_decode($modelColumn0['ville_devis']);
            $nom_client = html_entity_decode($modelColumn0['info_client']['accountname']);
            $adresse_client = html_entity_decode($modelColumn0['info_client']['adresse']);
            $ville_client = html_entity_decode($modelColumn0['info_client']['ville']);
            $cp_client = html_entity_decode($modelColumn0['info_client']['cp']);

            $y = 60;
            $contentHeight = $pdf->GetStringHeight($nom_client, 80);
            $pdf->MultiCell(80, $contentHeight, $nom_client, 0, 'L', 0, 1, 110, $y);
            if ($nom_contact != "" || $prenom_contact != "") {
                $y += $contentHeight;
                $contentHeight = $pdf->GetStringHeight($titre_contact . ' ' . $prenom_contact . ' ' . $nom_contact, 80);
                $salutation = (trim($titre_contact) != "") ? $titre_contact . ' ' : "";
                $pdf->MultiCell(80, $contentHeight, $salutation . $prenom_contact . ' ' . $nom_contact, 0, 'L', 0, 1, 110, $y);
            }

            $y += $contentHeight;
            $contentHeight = $pdf->GetStringHeight($adresse_client, 80);
            $pdf->MultiCell(80, $contentHeight, $adresse_client, 0, 'L', 0, 1, 110, $y);

            $y += $contentHeight;
            $contentHeight = $pdf->GetStringHeight($cp_client . " " . $ville_client, 80);
            $pdf->MultiCell(80, $contentHeight, $cp_client . " " . $ville_client, 0, 'L', 0, 1, 110, $y);

            $y += $contentHeight;
            $ville_quote = 'PARIS';
            $pdf->MultiCell(60, 5, $ville_quote . ", le " . $date_quote, 0, 'L', 0, 1, 110, $y);

            //sujet 
            $y = 95;
            $pdf->SetFont($trebuchet, '', 9);
            if ($salutation_contact != "") {
                $y += 5;
                $pdf->SetFont($trebuchet, '', 9);
                $pdf->MultiCell(200, 5, $salutation_contact . ",", 0, 'L', 0, 1, 10, $y);
            }
            $pdf->SetFont($trebuchet, '', 9);
            $text_sujet = "Suite à votre demande, je vous prie de bien vouloir trouver notre proposition pour la formation suivante :";
            $pdf->MultiCell(200, 5, $text_sujet, 0, 'L', 0, 1, 10, $y += 5);
            $nom_formation = html_entity_decode($modelColumn0['info_product'][0]['servicename']);
            $pdf->SetFont($trebuchetbd, '', 9);
            $pdf->MultiCell(200, 5, $nom_formation, 0, 'L', 0, 1, 10, $y += 5);

            $pdf->SetFont($trebuchet, '', 8);
            $tiret = "";
            $list_apprenant = "";
            $nbr_apprenants = $modelColumn0['info_apprenants']['nbr_apprenants'];
            for ($i = 0; $i < $nbr_apprenants; $i++) {
                $pdf->SetFont($trebuchetbd, '', 7);
                $nom_apprenant = html_entity_decode($modelColumn0['info_apprenants'][$i]['firstname']);
                $prenom_apprenant = html_entity_decode($modelColumn0['info_apprenants'][$i]['lastname']);
                $list = $tiret . '' . $nom_apprenant . ' ' . $prenom_apprenant;
                $tiret = " / ";
                $list_apprenant .= $list;
            }

            if ($list_apprenant != "") {
                $pdf->MultiCell(180, 5, "Apprenants : " . $list_apprenant, 0, 'L', 0, 1, 10, $y += 5);
            }

            $pdf->SetFont($trebuchet, '', 8);
            //les details 
            //bordure de tableau
            $pdf->SetDrawColor(0, 0, 0);
            $pdf->SetFillColor(255, 255, 255);
            /* uni_cnfsecrm - v2 - modif 138 - DEBUT */
            //border 1              
            $pdf->SetXY(78, 120);
            $pdf->MultiCell(5, 30, '', 'L', 'L', 1, 1);
            //border 2              
            $pdf->SetXY(135, 120);
            $pdf->MultiCell(5, 30, '', 'L', 'L', 1, 1);
            /* uni_cnfsecrm - v2 - modif 138 - FIN */

            $dates_formation_string = $modelColumn0['dates_formation_string'];
            //var_dump($dates_formation_string);
            $horaires_formation_string = $modelColumn0['horaires_formation_string'];
            $listprice = $modelColumn0['info_product'][0]['listprice'];
            $nbr_personne = intval($modelColumn0['info_product'][0]['quantity']);
            $nbr_jours = decimalFormat($modelColumn0['info_product'][0]['nbrjours'], 1, '.', '');
            $nbr_heures = decimalFormat($modelColumn0['info_product'][0]['nbrheures'], 1, '.', '');
            $naturecalcul = $modelColumn0['info_product'][0]['naturecalcul'];
            $parpersonne = $modelColumn0['info_product'][0]['parpersonne'];

            /* uni_cnfsecrm - v2 - modif 138 - DEBUT */
            $y = 120;
            /* uni_cnfsecrm - v2 - modif 138 - FIN */
            // detail column 1
            $pdf->SetXY(10, $y);
            if ($modelColumn0['info_dates'][0]["start_matin"] != "" && $modelColumn0['info_dates'][0]["end_matin"] != "" && $modelColumn0['info_dates'][0]["start_apresmidi"] != "" && $modelColumn0['info_dates'][0]["end_apresmidi"]) {
                $horaires = 'de ' . $modelColumn0['info_dates'][0]["start_matin"] . ' h à ' . $modelColumn0['info_dates'][0]["end_matin"] . ' h et de ' . $modelColumn0['info_dates'][0]["start_apresmidi"] . ' h à ' . $modelColumn0['info_dates'][0]["end_apresmidi"] . ' h';
            } else {
                $horaires = $horaires_formation_string;
            }
            $cout_journalier = $listprice;
            $label_par_personne = ($parpersonne == "on") ? '/pers' : '';
            switch ($naturecalcul) {
                case 'jour':
                    $label_prix_journalier = 'Coût journalier : ';
                    $label_nbr_journalier = 'Nombre de jours';
                    $nbr_jours_heures = $nbr_jours . " Jour(s)";
                    break;

                case 'heure':
                    $label_prix_journalier = 'Coût horaire : ';
                    $label_nbr_journalier = "Nombre d'heures";
                    $nbr_jours_heures = $nbr_heures . " Heure(s)";
                    break;

                default:
                    $label_prix_journalier = 'Coût journalier : ';
                    $label_nbr_journalier = 'Nombre de jours';
                    $nbr_jours_heures = $nbr_jours . " Jour(s)";
                    break;
            }

            $dates_formation_string = ($dates_formation_string != "") ? $dates_formation_string : $modelColumn0['info_dates'][0]['date_start'];
            //date de formation
            $info_dates = $modelColumn0['info_dates'];

            $nbre_jour = count($info_dates);

            for ($i = 0; $i < $nbre_jour; $i++) {
                $list_date[$i] = $info_dates[$i]['date_start'];
            }
            //sort($list_date);
            $premier_jour = $list_date[0];
            if ($premier_jour != null) {
                $premier_jour = formatDateFr($premier_jour);
            }

            $dernier_jour = $list_date[$i - 1];
            if ($dernier_jour != null) {
                $dernier_jour = formatDateFr($dernier_jour);
            }

            if ($premier_jour != "" && $dernier_jour != "") {
                $dates = 'du ' . $premier_jour . ' à ' . $dernier_jour;
            } else {
                $dates = $dates_formation_string;
            }

            $lieu = $modelColumn0['ville_devis'];
            /*
              <tr>
              <td width="90">Horaires:</td>
              <td width="90">$horaires</td>
              </tr>
             *              */
            /* uni_cnfsecrm - v2 - modif 157 - DEBUT */
            $tbl = <<<EOD
    <table border-left="1">
        <tr style="border-right: 1px solid red;"> 
            <td style="font-family: "Trebuchet MS", Helvetica, sans-serif;font-weight: normal;font-size: 8pt;" width="90">Durée en jours:</td>
            <td style="font-family: "Trebuchet MS", Helvetica, sans-serif;font-weight: normal;font-size: 20pt;" width="90">$nbr_jours Jour(s)</td>
        </tr>
            
        <tr style="border-right: 1px solid red;"> 
            <td style="font-family: "Trebuchet MS", Helvetica, sans-serif;font-weight: normal;font-size: 8pt;" width="90">Durée en heures:</td>
            <td style="font-family: "Trebuchet MS", Helvetica, sans-serif;font-weight: normal;font-size: 20pt;" width="90">$nbr_heures Heure(s)</td>
        </tr>            
        
        <tr>
            <td width="90">$label_prix_journalier</td>
            <td width="90">$cout_journalier  €  $label_par_personne</td>
        </tr>
        
        <tr>
            <td width="90">Nbre personne:</td>
            <td width="90">$nbr_personne</td>
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
            /* uni_cnfsecrm - v2 - modif 157 - FIN */
            $pdf->writeHTML($tbl, true, false, false, false, '');

            // detail column 2
            $pdf->SetXY(80, $y);
            $cout_support = "0,00";
            $frait_dep = formatPrice($modelColumn0['frais_deplacement']);
            $frait_heb = formatPrice($modelColumn0['frais_hebergement']);
            $frai_repas = formatPrice($modelColumn0['frais_repas']);
            $autre_frai = formatPrice($modelColumn0['autres_frais']);

            $tb2 = <<<EOD
    <table>
        <tr>
            <td width="90">Cout support (u) :</td>
            <td align="right" width="50">$cout_support €</td>
        </tr>
        
        <tr>
            <td width="90">Frais dépl. :</td>
            <td align="right" width="50">$frait_dep €</td>
        </tr>
        
        <tr>
            <td width="90">Frais Héberg. :</td>
            <td align="right" width="50">$frait_heb €</td>
        </tr>
        
        <tr>
            <td width="90">Frais Repas :</td>
            <td align="right" width="50">$frai_repas €</td>
        </tr>
        
        <tr>
            <td width="90">Autres frais :</td>
            <td align="right" width="50">$autre_frai €</td>
        </tr>
       
    </table>
EOD;
            $pdf->writeHTML($tb2, true, false, false, false, '');

            // detail column 3
            $pdf->SetXY(140, $y);
            $soustotalht = formatPrice($modelColumn0['soustotalht']);
            $totalfrais = formatPrice($modelColumn0['totalfrais']);
            $discount_amount = str_replace(",", ".", $modelColumn0['discount_amount']);
            $remise = formatPrice($discount_amount);
            $totalht = formatPrice($modelColumn0['totalht']);
            $tva = formatPrice($modelColumn0['tax_totalamount']);
            $total_ttc = formatPrice($modelColumn0['totalttc']);

            $total_support = "0,00";


            $tb3 = <<<EOD
    <table>
        <tr>
            <td width="80">Sous-total : </td>
            <td align="right" width="50">$soustotalht €</td>
        </tr>
        
        <tr>
            <td width="80">Total Frais :</td>
            <td align="right" width="50">$totalfrais €</td>
        </tr>
        
        <tr>
            <td width="80">Total Support :</td>
            <td align="right" width="50">$total_support €</td>
        </tr>
        
        <tr>
            <td width="80">Remise :</td>
            <td align="right" width="50">$remise €</td>
        </tr>
        
        <tr>
            <td width="80">Total HT :</td>
            <td align="right" width="50">$totalht €</td>
        </tr>
        
         <tr>
            <td width="80">TVA :</td>
            <td align="right" width="50">$tva €</td>
        </tr>
        
         <tr>
            <td width="80">Total TTC:</td>
            <td align="right" width="50">$total_ttc €</td>
        </tr>
       
    </table>
EOD;
            $pdf->writeHTML($tb3, true, false, false, false, '');

//signiature :
            /* uni_cnfsecrm - v2 - modif 138 - DEBUT */
            $y = 160;

            $pdf->SetXY(10, $y);
            $pdf->SetFont($trebuchet, '', 8);
            $annotation = $modelColumn0['annotation'];
            $pdf->MultiCell(180, 5, $annotation, 0, 'L', 0, 1);
            $annotationHeight = $pdf->GetStringHeight($annotation, 180);
            //var_dump($annotationWidth);
            $pdf->SetXY(10, $y += $annotationHeight);
            /* uni_cnfsecrm - v2 - modif 138 - FIN */
            $pdf->SetFont($trebuchet, '', 8);
            $text_s = "La signature du présent devis vaut pour acceptation des conditions générales de vente.";
            $pdf->MultiCell(200, 5, $text_s, 0, 'L', 0, 1);

            $pdf->SetXY(10, $y += 5);
            $pdf->SetFont($trebuchetbd, '', 8);
            //$pdf->MultiCell(200, 5, "Date de validité du devis :" . $date_quote, 0, 'L', 0, 1);
            $pdf->MultiCell(200, 5, "Devis valable 30 jours", 0, 'L', 0, 1);
             

//            $pdf->SetXY(10, $y += 5);
//            $pdf->SetFont($trebuchet, '', 8);
//            $text_prise_charge = "Prise en charge selon votre organisme collecteur.";
            //$pdf->MultiCell(200, 5, $text_prise_charge, 0, 'L', 0, 1);

            $pdf->SetXY(105, $y += 10);
            $pdf->SetFont($trebuchetmsitalic, '', 8);
            $text_bon_pour = "BON POUR ACCORD (cachet + signature)";
            $pdf->SetFillColor(255, 255, 255);
            $pdf->MultiCell(90, 30, $text_bon_pour, 1, 'L', 1, 1, '', '', true, 0, false, true, 30, 'T');

            /* uni_cnfsecrm - v2 - modif 156 - DEBUT */
            $pdf->SetXY(10, $y);
            $pdf->SetFont($trebuchet, '', 8);
            $text = "La signature du présent document vaut acceptation des conditions générales de vente et les conditions générales d’utilisation qui sont téléchargeables en cliquant sur les liens suivants :
                    <br/>
                    <ul>
                        <li>C.G.U. : https://cnfse.fr/wp-content/uploads/2018/02/CGU.pdf</li>
                        <li>C.G.V. : https://cnfse.fr/wp-content/uploads/2018/02/CGV.pdf</li>
                    </ul>
                    ";
            $pdf->writeHTMLCell(90, '', 10, $y, $text, 0, 0, 1, true, 'L', true);
            /* uni_cnfsecrm - v2 - modif 156 - DEBUT */
            
            $pdf->SetXY(10, $y += 30);
            $pdf->SetFont($trebuchet, '', 8);
            $text_salutation = "Je reste à votre entière disposition pour tout renseignement supplèmentaire, et vous prie d'agrèer, Madame, mes meilleures salutations. ";
            //$pdf->MultiCell(200, 5, $text_salutation, 0, 'L', 0, 1);

            $pdf->SetXY(10, $y += 5);
            $pdf->SetFont($trebuchetbd, '', 10);
            $text_signature = "SIGNATURE";
            //$pdf->MultiCell(200, 5, $text_signature, 0, 'C', 0, 1);

            $pdf->SetXY(10, $y += 5);
            $pdf->SetFont($trebuchet, '', 8);
            $text_signature_fonction = "Fonction";
            //$pdf->MultiCell(200, 5, $text_signature_fonction, 0, 'C', 0, 1);

            $pdf->Image("test/Signature/Signature.jpg", 150, $y -= 5, 25, 20);



// Add the border cell at the end
            // This is required to reset Y position for next write
            $pdf->MultiCell($headerFrame->w, $headerFrame->h - $headerFrame->y, "", 0, 'L', 0, 1, $headerFrame->x, $headerFrame->y);
//header page 2           
            $pdf->AddPage();
            $y = 1;
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
            $pdf->Image("test/logo/logo-CNFSE-large.png", 10, 1, $w, $h);
            //nom devis
            $pdf->SetXY(160, $y += 1);
            $pdf->SetFont($trebuchet, '', 8);
            $pdf->SetTextColor(255, 0, 0);
            $header = "à retourner avec le devis";
            $pdf->MultiCell(200, 5, $header, 0, 'L', 0, 1);

            $pdf->SetXY(160, $y += 10);
            $pdf->SetFont($trebuchetbd, '', 11);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->MultiCell(200, 5, $num_devis, 0, 'L', 0, 1);

            //titre page

            $pdf->SetXY(0, $y += 5);
            $pdf->SetFont($trebuchetbd, '', 14);
            $pdf->SetTextColor(0, 0, 0);
            $titre_page = "FICHE D'INSCRIPTION";
            $pdf->MultiCell(200, 5, $titre_page, 0, 'C', 0, 1);

            //information client

            $pdf->SetTextColor(0, 0, 0);

            $phone_client = $modelColumn0['info_client']['phone'];
            $email_client = html_entity_decode($modelColumn0['info_client']['email']);
            $adresse_com = html_entity_decode($modelColumn0['info_client']['adresscompl']);

            //nom
            $contentHeight = $pdf->GetStringHeight($nom_client, 100);
            $pdf->SetXY(20, $y += 10);
            $pdf->SetFont($trebuchetbd, '', 10);
            $pdf->MultiCell(40, 5, 'Client :', 0, 'L', 0, 1);
            $pdf->SetFont($trebuchet, '', 10);
            $pdf->SetXY(55, $y);
            $pdf->MultiCell(60, $contentHeight, $nom_client, 0, 'L', 0, 1);

            //Adresse
            if ($contentHeight == 0) {
                $contentHeight = 6;
            }

            $pdf->SetXY(20, $y += $contentHeight);
            $contentHeight = $pdf->GetStringHeight($adresse_client, 70);
            $pdf->SetFont($trebuchetbd, '', 10);
            $pdf->MultiCell(40, 5, 'Adresse :', 0, 'L', 0, 1);
            $pdf->SetFont($trebuchet, '', 10);
            $pdf->SetXY(55, $y);
            $pdf->MultiCell(60, $contentHeight, $adresse_client, 0, 'L', 0, 1);

            //Adresse comple
            if ($contentHeight == 0) {
                $contentHeight = 6;
            }
            $pdf->SetXY(20, $y += $contentHeight);
            $contentHeight = $pdf->GetStringHeight($adresse_com, 70);
            $pdf->SetFont($trebuchetbd, '', 10);
            $pdf->MultiCell(40, 5, 'Adresse compl. :', 0, 'L', 0, 1);
            $pdf->SetFont($trebuchet, '', 10);
            $pdf->SetXY(55, $y);
            $pdf->MultiCell(140, $contentHeight, $adresse_com, 0, 'L', 0, 1);

            //code postale
            if ($contentHeight == 0) {
                $contentHeight = 6;
            }
            $pdf->SetXY(20, $y += $contentHeight);
            $contentHeight = $pdf->GetStringHeight($cp_client, 70);
            $pdf->SetFont($trebuchetbd, '', 10);
            $pdf->MultiCell(40, 5, 'Code Postal :', 0, 'L', 0, 1);
            $pdf->SetFont($trebuchet, '', 10);
            $pdf->SetXY(55, $y);
            $pdf->MultiCell(60, $contentHeight, $cp_client, 0, 'L', 0, 1);

            $y = 27;
            //tel
            $pdf->SetXY(120, $y);
            $contentHeight = $pdf->GetStringHeight($phone_client, 70);
            $pdf->SetFont($trebuchetbd, '', 10);
            $pdf->MultiCell(40, 5, 'Tél :', 0, 'L', 0, 1);
            $pdf->SetFont($trebuchet, '', 10);
            $pdf->SetXY(135, $y);
            $pdf->MultiCell(60, $contentHeight, $phone_client, 0, 'L', 0, 1);

            //email
            if ($contentHeight == 0) {
                $contentHeight = 6;
            }
            $pdf->SetXY(120, $y += $contentHeight);
            $contentHeight = $pdf->GetStringHeight($email_client, 70);
            $pdf->SetFont($trebuchetbd, '', 10);
            $pdf->MultiCell(40, $contentHeight, 'E-mail :', 0, 'L', 0, 1);
            $pdf->SetFont($trebuchet, '', 10);
            $pdf->SetXY(135, $y);
            $pdf->MultiCell(60, 5, $email_client, 0, 'L', 0, 1);

            //ville
            if ($contentHeight == 0) {
                $contentHeight = 6;
            }
            $pdf->SetXY(120, $y += $contentHeight + 8);
            $contentHeight = $pdf->GetStringHeight($ville_client, 70);
            $pdf->SetFont($trebuchetbd, '', 10);
            $pdf->MultiCell(40, 5, 'Ville :', 0, 'L', 0, 1);
            $pdf->SetFont($trebuchet, '', 10);
            $pdf->SetXY(135, $y);
            $pdf->MultiCell(60, $contentHeight, $ville_client, 0, 'L', 0, 1);

            //nom formation 
            $pdf->SetXY(20, $y += 15);
            $pdf->SetFont($trebuchetbd, '', 10);
            $pdf->MultiCell(200, 5, $nom_formation, 0, 'L', 0, 1);


            //list des Apprenants

            $pdf->SetXY(20, $y += 10);
            $pdf->SetFont($trebuchetbd, '', 10);
            $pdf->MultiCell(50, 5, 'Prénom', 0, 'L', 0, 1);

            $pdf->SetXY(60, $y);
            $pdf->SetFont($trebuchetbd, '', 10);
            $pdf->MultiCell(50, 5, 'Nom', 0, 'L', 0, 1);

            $pdf->SetXY(120, $y);
            $pdf->SetFont($trebuchetbd, '', 10);
            $pdf->MultiCell(50, 5, 'Fonction des Apprenants', 0, 'L', 0, 1);

            $pdf->SetXY(170, $y);
            $pdf->SetFont($trebuchetbd, '', 10);
            $pdf->MultiCell(50, 5, 'Date naissance', 0, 'L', 0, 1);


            //lists

            $a = 0;
            $y += 8;

            for ($i = 1; $i <= 16; $i++) {

                $nom_apprenants = html_entity_decode($modelColumn0['info_apprenants'][$i - 1]['firstname']);
                $prenom_apprenants = html_entity_decode($modelColumn0['info_apprenants'][$i - 1]['lastname']);
                $birthday_apprenants = $modelColumn0['info_apprenants'][$i - 1]['birthday'];
                if ($birthday_apprenants != null) {
                    $birthday_apprenants = formatDateFr($birthday_apprenants);
                }
                $fonction = html_entity_decode($modelColumn0['info_apprenants'][$i - 1]['title']);

                $pdf->SetDrawColor(192, 192, 192);
                $pdf->SetFillColor(241, 244, 255);

                $pdf->SetFont($trebuchet, '', 10);
                //num
                $pdf->SetXY(10, $y + $a);
                $pdf->MultiCell(10, 5, $i, 0, 'L', 0, 1);

                //column 1
                $pdf->SetXY(20, $y + $a);
                $pdf->MultiCell(35, 5, $nom_apprenants, 1, 'L', 1, 1);

                //column 2
                $pdf->SetXY(60, $y + $a);
                $pdf->MultiCell(55, 5, $prenom_apprenants, 1, 'L', 1, 1);

                //column 3
                $pdf->SetXY(120, $y + $a);
                $pdf->MultiCell(45, 5, $fonction, 1, 'L', 1, 1);

                //column 4
                $pdf->SetXY(170, $y + $a);
                $pdf->MultiCell(30, 5, $birthday_apprenants, 1, 'L', 1, 1);

                $a = $a + 9;
            }

            $pdf->SetXY(20, $y += 165);
            $pdf->SetFont($trebuchet, '', 8);
            $remarque = "Le client reconnait avoir pris connaissance des modalités d'annulation décrites dans les Conditions Générales de vente ci-jointes.";
            $pdf->MultiCell(200, 5, $remarque, 0, 'L', 0, 1);
            /* uni_cnfsecrm - v2 - modif 156 - DEBUT */
            /*$pdf->AddPage();


            //condition general 
            $y = 0;
            $pdf->SetXY(0, $y += 3);
            $pdf->SetFont($trebuchetbd, '', 9);
            $titre = "CONDITIONS GENERALES DE VENTE";
            $pdf->MultiCell(200, 5, $titre, 0, 'C', 0, 1);



            $html1 = '<h4>essai</h4>'
                    . '<h4>DEFINITIONS</h4> '
                    . '-Stages et cycles interentreprises : Formation sur catalogue réalisée dans nos locaux <br/>'
                    . '-Formation intra-entreprise : Formation réalisée sur mesure pour le compte d\'un Client ou d\'un groupe. <br/>'
                    . '-Centre National de Formation en Sécurité et Environnement sera remplacé dans le texte suivant par C.N.F.S.E. <br/>'
                    . '<h4>OBJET ET CHAMP D\'APPLICATION</h4>'
                    . 'Toute commande de formation implique l\'acceptation sans réserve par l\'acheteur et son adhésion pleine et entière aux présentes conditions générales de vente qui prévalent sur tout autre document de l\'acheteur, et notamment sur toutes conditions générales d\'achat.<br/>'
                    . '<h4>DOCUMENTS CONTRACTUELS</h4>'
                    . 'C.N.F.S.E. fait parvenir au client, en double exemplaire, une convention de formation professionnelle continue telle que prévue par la loi. Le client s\'engage à retourner dans les plus brefs délais à C.N.F.S.E. un exemplaire signé et portant son cachet commercial. Une attestation de présence est adressée au Client aprés chaque formation, cycle ou parcours.<br/>'
                    . '<h4>PRIX, FACTURATION ET REGLEMENTS</h4>'
                    . 'Tous nos prix sont indiqués hors taxes. Ils sont à majorer de la TVA au taux en vigueur. Tout stage, cycle ou parcours commencé est d˚ en entier <br/>'
                    . '-Pour les formations interentreprises : <br/>'
                    . 'L\'acceptation de C.N.F.S.E. est conditionnée par le paiement intégral de la facture, C.N.F.S.E. se réserve le droit de disposer librement des places retenues par le client, tant que les frais d\'inscription n\'auront pas été réglés. Les factures sont payables, sans escompte et à l\'ordre de C.N.F.S.E. à réception de facture. <br/>Les repas ne sont pas compris dans le prix du stage.<br/>'
                    . '-Pour les formations intra-entreprise :<br/>'
                    . 'L\'acceptation de C.N.F.S.E. est conditionnée par le réglement d\'un acompte dans les conditions prévues ci-dessous.<br/>'
                    . 'Les factures sont payables, sans escompte et à l\'ordre de C.N.F.S.E :<br/>'
                    . '-Un acompte de 30 % est versé à la commande. Cet acompte restera acquis à C.N.F.S.E. si le client renonce à la formation.<br/>'
                    . '-Le complément est d˚ à réception des différentes factures émises au fur et à mesure de l\'avancement des formations.<br/>'
                    . '-En cas de non-paiement intÈgral d\'une facture venue à échéance, après mise en demeure restée sans effet dans les 5 jours ouvrables, C.N.F.S.E. se rÈserve la faculté de suspendre toute formation en cours et /ou à venir.<br/>'
                    . '<h4>REGLEMENT PAR UN OPCA</h4>'
                    . 'Si le client souhaite que le règlement soit émis par l\'OPCA dont il dépend, il lui appartient :<br/>'
                    . '-de faire une demande de prise en charge avant le début de la formation et de s\'assurer de la bonne fin de cette demande ;<br/>'
                    . '-de l\'indiquer explicitement sur son bulletin d\'inscription ou sur son bon de commande ;<br/>'
                    . '-de s\'assurer de la bonne fin du paiement par l\'organisme qu\'il aura désigné. Si l\'OPCA ne prend en charge que partiellement le cout de la formation, le reliquat sera facturé au Client.<br/>'
                    . 'Si C.N.F.S.E. n\'a pas reÁu la prise en charge de l\'OPCA au 1er jour de la formation, le client sera facturé de l\'intégralité du co˚t du stage. En cas de non-paiement par l\'OPCA, pour quelque motif que ce soit, le Client sera redevable de l\'intégralité du co˚t de la formation et sera facturé du montant correspondant.<br/>'
                    . '<h4>PENALITE DE RETARD</h4>'
                    . 'Toute somme non payée à l\'échéance donnera lieu au paiement par le Client de pénalités de retard fixées à trois fois le taux d\'intéré Ces pénalités sont exigibles de plein droit, dès réception de l\'avis informant le Client qu\'elles ont été portées à son débit. Une indemnité forfaitaire de 40 à est due de plein droit (L. 441-6, I, 12) en cas de retard de paiement de toute créance.<br/>'
                    . '<h4>REFUS DE COMMANDE</h4>'
                    . 'Dans le cas ou un Client passerait une commande à C.N.F.S.E., sans avoir procédé au paiement de la (des) commande(s) précédente(s), C.N.F.S.E. pourra refuser d\'honorer la commande et de délivrer les formations concernées, sans que le Client puisse prétendre à une quelconque indemnité, pour quelque raison que ce soit.<br/>'
                    . '<h4>CONDITIONS D\'ANNULATION ET DE REPORT<h4>'
            ;


            $html2 = '<h4>Stages intra et inter entreprise(s)</h4> '
                    . 'Toute annulation par le Client doit Ítre communiquée par écrit. En cas d\'annulation par le client d\'une session de formation planifiée, des indemnités compensatrices sont dues dans les conditions suivantes fut-ce en cas de force majeure :<br/>'
                    . 'Report ou annulation communiqué au moins 30 jours ouvrés avant la session : aucune indemnité <br/>'
                    . 'Report ou annulation moins de 30 jours et au moins 10 jours ouvrés avant la session : 30% des honoraires seront facturés au client. <br/>'
                    . 'Report ou annulation communiqué moins de 10 jours ouvrés avant la session : 70 % des honoraires seront facturés au client.'
                    . 'C.N.F.S.E. ne pourra être tenue responsable à l\'Ègard du client en cas d\'inexécution de ses obligations résultant d\'un évènement de force majeure. Dans le cas ou le nombre de participants serait pédagogiquement insuffisant pour le bon déroulement de la session, l\'organisme de formation se réserve le droit d\'annuler la formation au plus tard une semaine avant la date prévue.<br/>'
                    . '<h4>INFORMATIQUE ET LIBERTES</h4>'
                    . 'Les informations à caractére personnel qui sont communiquées par le Client à C.N.F.S.E. en application et dans l\'exécution des commandes et/ou ventes pourront étre communiquées aux partenaires contractuels de C.N.F.S.E. pour les besoins desdites commandes.<br/>'
                    . 'Conformément à la réglementation française qui est applicable à ces fichiers, le Client peut écrire à C.N.F.S.E. pour s\'opposer à une telle communication des informations le concernant. Il peut également à tout moment exercer ses droits d\'accés et de rectification dans le fichier de C.N.F.S.E.. <br/>'
                    . '<h4>RENONCIATION</h4>'
                    . 'Le fait pour C.N.F.S.E. de ne pas se prévaloir à un moment donné de l\'une quelconque des clauses des présentes, ne peut valoir renonciation à se prévaloir ultéieurement de ces même clauses. <br/>'
                    . '<h4>LOI APPLICABLE</h4>'
                    . 'Les Conditions Générales et tous les rapports entre C.N.F.S.E. et ses Clients relèvent de la Loi française.<br/>'
                    . '<h4>ATTRIBUTION DE COMPETENCES</h4>'
                    . 'Tous litiges qui ne pourraient être réglés à l\'amiable seront de la COMPETENCE EXCLUSIVE DU TRIBUNAL DE COMMERCE DE PARIS quel que soit le siège ou la résidence du Client, nonobstant pluralité de défendeurs ou appel en garantie. Cette clause attributive de compétence ne s\'appliquera pas au cas de litige avec un Client non professionnel pour lequel les règles lègales de compétence matérielle et géographique s\'appliqueront. <br/>'
                    . 'La présente clause est stipulée dans l\'intérêt de la société L C.N.F.S.E. qui se réserve le droit d\'y renoncer si bon lui semble. <br/>'
                    . '<h4>ELECTION DE DOMICILE</h4>'
                    . 'L\'élection de domicile est faite par C.N.F.S.E. à son siège social 231 rue St Honoré - 75001 PARIS <br/>'
                    . '<h4>DATADOCK numéro d\'agrément : 0012160</h4>'

            ;

            $pdf->SetXY(0, $y += 10);
            $pdf->SetFont($trebuchet, '', 7);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetTextColor(102, 102, 102);

            $pdf->writeHTMLCell(95, '', '', 10, $html1, 0, 0, 1, true, 'J', true);
            $pdf->writeHTMLCell(95, '', '', '', $html2, 0, 1, 1, true, 'J', true);*/
            /* uni_cnfsecrm - v2 - modif 156 - FIN */

            //page des dates :
            $y = 0;
            $x = 0;

            $nbr_journee = count($modelColumn0['info_dates']);
            /* if ($nbr_journee != 0) {
              $pdf->AddPage();
              $pdf->SetFillColor(255, 255, 255);
              $pdf->SetTextColor(0, 0, 0);



              $pdf->SetXY($x += 10, $y += 10);
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
              $heure_debut_matin = $modelColumn0['info_dates'][$i]["start_matin"];
              $heure_fin_matin = $modelColumn0['info_dates'][$i]["end_matin"];
              $heure_debut_apresmidi = $modelColumn0['info_dates'][$i]["start_apresmidi"];
              $heure_debut_apremidi = $modelColumn0['info_dates'][$i]["end_apresmidi"];
              $duree_formation = $modelColumn0['info_dates'][$i]["duree_formation"];
              $tbl .= '<tr style = "font-size: 8pt;">
              <td align="right" bgcolor="' . $couleur . '">' . $num_journee . '</td>
              <td align="center" bgcolor="' . $couleur . '">' . $date_formation . '</td>
              <td align="center" bgcolor="' . $couleur . '">De ' . $heure_debut_matin . ' à ' . $heure_fin_matin . '</td>
              <td align="center" bgcolor="' . $couleur . '">De ' . $heure_debut_apresmidi . ' à ' . $heure_debut_apremidi . '</td>
              <td align="center" bgcolor="' . $couleur . '">' . $duree_formation . '</td>
              </tr>';
              }
              $tbl .= '</tbody>
              </table>';
              $pdf->writeHTML($tbl, true, false, true, false, '');
              } */
        }
    }

}
