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
            //logo
            list($imageWidth, $imageHeight, $imageType, $imageAttr) = $parent->getimagesize(
                    "test/logo/logo-CNFSE.png");
            //division because of mm to px conversion
            $w = $imageWidth / 3;

            $w = $imageWidth;
            if ($w > 30) {
                $w = 80;
            }
            $h = $imageHeight;
            if ($h > 20) {
                $h = 30;
            }
            $pdf->Image("test/logo/logo-CNFSE.png", 10, 2, $w, $h);

            
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
            $x = 10;
            $y = 30;
            //page 1
            $pdf->SetXY($x, $y);
            $pdf->SetFont($trebuchet, '', 9);
            $pdf->MultiCell(190, 5, "Déclaration d'activité enregistrée sous le numéro 11755161475 auprès de la Préfecture d\'Ile de France.Cet enregistrement ne vaut pas agrément de l'Etat.", 0, 'L', 0, 1);

            //$n_convention = "CO3698-20190122";
            $n_convention = $modelColumn0['convention_no'];
            $pdf->SetXY($x, $y + 13);
            $pdf->SetDrawColor(0, 0, 0);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetFont($trebuchetbd, '', 12);
            $pdf->MultiCell(190, 5, 'CONVENTION DE FORMATION N° ' . $n_convention, 'B', 'L', 1, 1);


            $pdf->SetXY($x, $y + 22);
            $pdf->SetFont($trebuchet, '', 8);
            $pdf->MultiCell(190, 5, 'Entre les soussignés :', 0, 'L', 0, 1);
            $pdf->SetXY($x, $y + 26);
            $pdf->MultiCell(100, 5, 'Organisme de formation', '', 'L', 0, 1);
            
            $nom_ste = $modelColumn0['organizationdetails'][0]['organizationname'];
            $adresse_ste = $modelColumn0['organizationdetails'][0]['address'];
            $code_postale_ste = $modelColumn0['organizationdetails'][0]['code'];
            $ville_ste = $modelColumn0['organizationdetails'][0]['city'];

            $nom_org = "Fréderic";
            $prenom_org = "LAMBERT";
            $poste = "Gérant";
            $pdf->SetDrawColor(0, 0, 0);
            $pdf->SetFillColor(255, 255, 255);
            
            $pdf->SetFont($trebuchetbi, '', 9);
            $pdf->SetDrawColor(0, 0, 0);
            $pdf->SetFillColor(255, 247, 230);
            $pdf->SetXY($x, $y + 32);
            $pdf->MultiCell(90, 5, $nom_ste, '', 'L', 1, 1);

            $pdf->SetXY($x, $y + 37);
            $pdf->MultiCell(90, 15, $adresse_ste, '', 'L', 1, 1);

            $pdf->SetXY($x, $y + 45);
            $pdf->MultiCell(90, 15, $code_postale_ste . ' ' . $ville_ste, '', 'L', 1, 1);

            $pdf->SetXY($x, $y + 52);
            $pdf->MultiCell(90, 5, 'Représenté par : ' . $nom_org . ' ' . $prenom_org, '', 'L', 1, 1);

            $pdf->SetXY($x, $y + 57);
            $pdf->MultiCell(90, 5, 'agissant en qualité de :' . $poste, '', 'L', 1, 1);

            //partie 2            
            $adresse_client = $modelColumn0['info_client']['adresse'];
            $code_postale = $modelColumn0['info_client']['cp'];
            $ville_client = $modelColumn0['info_client']['ville'];
            $nom_client = $modelColumn0['info_client']['accountname'];
            $titre_contact = $modelColumn0['info_client']['titre_contact'];
            $nom_contact = $modelColumn0['info_client']['nom_contact'];
            $prenom_contact = $modelColumn0['info_client']['prenom_contact'];
            $travail_contact = $modelColumn0['info_client']['travail_contact'];

            $adresse_formation = $modelColumn0['adresse_formation'];
            $cp_formation = $modelColumn0['cp_formation'];
            $ville_formation = $modelColumn0['ville_formation'];

            $pdf->SetDrawColor(0, 0, 0);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetFont($trebuchet, '', 8);

            $pdf->SetXY($x + 100, $y + 26);
            $pdf->MultiCell(100, 5, 'Identité du client', '', 'L', 0, 1);

            $pdf->SetFont($trebuchetbi, '', 9);
            $pdf->SetDrawColor(0, 0, 0);
            $pdf->SetFillColor(255, 247, 230);
            $pdf->SetXY($x + 100, $y + 32);
            $pdf->MultiCell(90, 5, $nom_client, '', 'L', 1, 1);

            $pdf->SetXY($x + 100, $y + 37);
            $pdf->MultiCell(90, 15, $adresse_client, '', 'L', 1, 1);

            $pdf->SetXY($x + 100, $y + 45);
            $pdf->MultiCell(90, 15, $code_postale . ' ' . $ville_client, '', 'L', 1, 1);

            $pdf->SetXY($x + 100, $y + 52);
            $pdf->MultiCell(90, 5, 'représenté par :' . $titre_contact . ' ' . $nom_contact . ' ' . $prenom_contact, '', 'L', 1, 1);

            $pdf->SetXY($x + 100, $y + 57);
            $pdf->MultiCell(90, 5, 'agissant en qualité de ' . $travail_contact, '', 'L', 1, 1);


            $pdf->SetDrawColor(0, 0, 0);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetFont($trebuchet, '', 8); 
            $pdf->SetXY($x, $y + 65);
            $pdf->MultiCell(200, 5, 'est conclue la convention suivante, en application du livre IX du Code du travail', '', 'L', 0, 1);

             //article 1:

            $pdf->SetFont($trebuchetbd, '', 8);
            $pdf->SetXY($x, $y + 75);
            $pdf->MultiCell(200, 5, 'ARTICLE 1 - Objet de la convention', '', 'L', 0, 1);

            $type_formation = $modelColumn0['type_formation'];
            $pdf->SetFont($trebuchet, '', 8);
            $pdf->SetXY($x, $y + 80);
            $pdf->MultiCell(200, 5, 'L\'organisme de formation organise la (ou les) action (s) de formation suivantes : ', '', 'L', 0, 1);

            $pdf->SetFont($trebuchetbd, '', 8);
            $pdf->SetXY($x + 110, $y + 80);
            $pdf->MultiCell(200, 5, $type_formation, '', 'L', 0, 1);
            //var_dump($modelColumn0);
            $nom_formation = $modelColumn0['Intitulé'];
            $pdf->SetFont($trebuchet, '', 8);
            $pdf->SetXY($x, $y + 85);
            $pdf->MultiCell(200, 5, '- Intitulé du stage : ', '', 'L', 0, 1);

            $pdf->SetFont($trebuchetbd, '', 8);
            $pdf->SetXY($x + 60, $y + 85);
            $pdf->MultiCell(200, 5, $nom_formation, '', 'L', 0, 1);


            $pdf->SetFont($trebuchet, '', 8);
            $pdf->SetXY($x, $y + 95);
            $pdf->MultiCell(200, 5, '- Objectifs, Programmes, méthodes et moyens pédagogiques : Se référer au programme de formation en annexe', '', 'L', 0, 1);

            $pdf->SetXY($x, $y + 100);
            $pdf->MultiCell(200, 5, '- Modalités de controle des connaissances et sanction de la formation : Tests d\'évaluation', '', 'L', 0, 1);

            $pdf->SetXY($x, $y + 105);
            $pdf->MultiCell(200, 5, '- Durée et lieu de l\'action :', '', 'L', 0, 1);
            
            $pdf->SetXY($x, $y + 110);
            $pdf->MultiCell(200, 5, '- Période :', '', 'L', 0, 1);
           
            $pdf->SetXY($x, $y + 115);
            $pdf->MultiCell(200, 5, '- Nombre d\'apprenants :', '', 'L', 0, 1);

            $pdf->SetXY($x, $y + 120);
            $pdf->MultiCell(200, 5, '- Noms des apprenants :', '', 'L', 0, 1);

            $duree = "14";
            $date_debut_formation = $modelColumn0['date_debut_formation'];
            $date_fin_formation = $modelColumn0['date_fin_formations'];
            $nbr_apprenants = $modelColumn0['info_apprenants']['nbr_apprenants'];

            $pdf->SetFont($trebuchetbd, '', 8);
            $pdf->SetXY($x + 50, $y + 105);
            $pdf->MultiCell(200, 5, $duree . ' heures à', '', 'L', 0, 1);
            
            $pdf->SetXY($x + 50, $y + 110);
            $pdf->MultiCell(200, 5, 'Du ' . $date_debut_formation . ' au ' . $date_fin_formation, '', 'L', 0, 1);

            $pdf->SetXY($x + 50, $y + 115);
            $pdf->MultiCell(200, 5, $nbr_apprenants, '', 'L', 0, 1);

            for ($i = 0; $i < $nbr_apprenants; $i++) {
                $titre_apprenant = $modelColumn0['info_apprenants'][$i]['titre_apprenant'];
                $nom_apprenant = $modelColumn0['info_apprenants'][$i]['nom_apprenant'];
                $prenom_apprenant = $modelColumn0['info_apprenants'][$i]['prenom_apprenant'];
                $pdf->SetXY($x + 50, $y + 120);
                $pdf->MultiCell(200, 5, $titre_apprenant . ' ' . $prenom_apprenant . ' ' . $nom_apprenant, '', 'L', 0, 1);
            }
            //article 2
            $pdf->SetFont($trebuchetbd, '', 8);
            $pdf->SetXY($x, $y + 125);
            $pdf->MultiCell(200, 5, 'ARTICLE 2 - Données financières', '', 'L', 0, 1);

            $pdf->SetFont($trebuchet, '', 8);
            $pdf->SetXY($x, $y + 130);
            $pdf->MultiCell(200, 5, 'Le montant des frais de formation à acquitter sera de :', '', 'L', 0, 1);
            
            $pdf->SetXY($x, $y + 135);
            $pdf->MultiCell(200, 5, '- Prix journalier : par pers', '', 'L', 0, 1);

            $pdf->SetXY($x, $y + 140);
            $pdf->MultiCell(200, 5, '- Nombre de jours :', '', 'L', 0, 1);

            $pdf->SetXY($x, $y + 145);
            $pdf->MultiCell(200, 5, '- Nombre d\'apprenants :', '', 'L', 0, 1);

            $pdf->SetXY($x, $y + 150);
            $pdf->MultiCell(200, 5, '- Frais de déplacement HT :', '', 'L', 0, 1);

            $pdf->SetXY($x, $y + 155);
            $pdf->MultiCell(200, 5, '- Autres frais HT :', '', 'L', 0, 1);

            $prix_jou = "174,50";
            //$nbr_jour = "2,00";
            $nbr_jour = $modelColumn0['info_product'][0]['quantity'];
            $frai_dep = $modelColumn0["frais_deplacement"];
            $autre_frai = $modelColumn0["autres_frais"];
            $pdf->SetFont($trebuchetbd, '', 8);
            $pdf->SetXY($x + 40, $y + 135);
            $pdf->MultiCell(50, 5, $prix_jou . ' €', '', 'R', 0, 1);

            $pdf->SetXY($x + 40, $y + 140);
            $pdf->MultiCell(50, 5, $nbr_jour, '', 'R', 0, 1);

            $pdf->SetXY($x + 40, $y + 145);
            $pdf->MultiCell(50, 5, $nbr_apprenants, '', 'R', 0, 1);

            $pdf->SetXY($x + 40, $y + 150);
            $pdf->MultiCell(50, 5, $frai_dep . ' €', '', 'R', 0, 1);

            $pdf->SetXY($x + 40, $y + 155);
            $pdf->MultiCell(50, 5, $autre_frai . ' €', '', 'R', 0, 1);

            //partie 2

            $pdf->SetFont($trebuchet, '', 8);
            $pdf->SetXY($x + 100, $y + 135);
            $pdf->MultiCell(200, 5, '- Sous total Formation HT :', '', 'L', 0, 1);

            $pdf->SetXY($x + 100, $y + 140);
            $pdf->MultiCell(200, 5, '- Sous total Frais HT :', '', 'L', 0, 1);

            $pdf->SetXY($x + 100, $y + 145);
            $pdf->MultiCell(200, 5, '- Support de cours HT :', '', 'L', 0, 1);

            $pdf->SetXY($x + 100, $y + 150);
            $pdf->MultiCell(200, 5, '- Remise HT :', '', 'L', 0, 1);

            $pdf->SetXY($x + 100, $y + 155);
            $pdf->MultiCell(200, 5, '- Total HT :', '', 'L', 0, 1);

            $pdf->SetXY($x + 100, $y + 160);
            $pdf->MultiCell(200, 5, '- Tva 0 % :', '', 'L', 0, 1);

            $pdf->SetXY($x + 100, $y + 165);
            $pdf->MultiCell(200, 5, '- TOTAL TTC', '', 'L', 0, 1);

            $s_t_formation = $modelColumn0['soustotalht'];
            $s_t_frais = $modelColumn0["totalfrais"];
            $support_cours = "0,00";
            $remise = $modelColumn0['discount_amount'];
            $total_ht = $modelColumn0['totalht'];
            $tva = $modelColumn0['tax_totalamount'];
            $total_ttc = $modelColumn0['totalttc'];

            $pdf->SetFont($trebuchetbd, '', 8);
            $pdf->SetXY($x + 130, $y + 135);
            $pdf->MultiCell(50, 5, $s_t_formation . ' €', '', 'R', 0, 1);

            $pdf->SetXY($x + 130, $y + 140);
            $pdf->MultiCell(50, 5, $s_t_frais . ' €', '', 'R', 0, 1);

            $pdf->SetXY($x + 130, $y + 145);
            $pdf->MultiCell(50, 5, $support_cours . ' €', '', 'R', 0, 1);

            $pdf->SetXY($x + 130, $y + 150);
            $pdf->MultiCell(50, 5, $remise . ' €', '', 'R', 0, 1);

            $pdf->SetXY($x + 130, $y + 155);
            $pdf->MultiCell(50, 5, $total_ht . ' €', '', 'R', 0, 1);

            $pdf->SetXY($x + 130, $y + 160);
            $pdf->MultiCell(50, 5, $tva . ' €', '', 'R', 0, 1);

            $pdf->SetXY($x + 130, $y + 165);
            $pdf->MultiCell(50, 5, $total_ttc . ' €', '', 'R', 0, 1);

            //article 3

            $pdf->SetFont($trebuchetbd, '', 8);
            $pdf->SetXY($x, $y + 175);
            $pdf->MultiCell(200, 5, 'ARTICLE 3 - Date d\'effet et durée de la convention', '', 'L', 0, 1);

            $pdf->SetFont($trebuchet, '', 8);
            $pdf->SetXY($x, $y + 180);
            $pdf->MultiCell(200, 5, 'La présente convention prend effet à compter de la date de la signature par l\'entreprise et prendra fin le 29/01/2019 .', '', 'L', 0, 1);

            //article 4

            $pdf->SetFont($trebuchetbd, '', 8);
            $pdf->SetXY($x, $y + 185);
            $pdf->MultiCell(200, 5, 'ARTICLE 4 - Subrogation', '', 'L', 0, 1); 

            $pdf->SetFont($trebuchet, '', 8);
            $pdf->SetXY($x, $y + 190);
            $pdf->MultiCell(200, 5, 'Non - Facturation : ' . $nom_client . ' ' . $prenom_contact, '', 'L', 0, 1);
            
            
            $date_fait = $modelColumn0['crmentity']['date_creation'];
            $pdf->SetFont($trebuchet, '', 8);
            $pdf->SetXY($x, $y + 195);
            $pdf->MultiCell(200, 5, 'Fait en triple exemplaire à ' . $ville . ' le ' . $date_fait, '', 'L', 0, 1);

            //signature
            //$pdf->RoundedRect(10, $y + 180, 100, 20, 2, '1111', 'DF', array(), array(255, 255, 255));
            // $pdf->RoundedRect($x, $y, $w, $h, $r, $round_corner='1111', $style='', $border_style=array(), $fill_color=array()) ;


            $pdf->SetFont($trebuchet, '', 8);
            $pdf->SetXY($x, $y + 200);
            $pdf->MultiCell(90, 35, 'Signature et cachet de l\'organisme de formation', 'LRBT', 'L', 1, 1);
            $pdf->Image("test/Signature/Signature.jpg", $x + 60, $y + 210, 25, 20);

            $pdf->SetFont($trebuchet, '', 8);
            $pdf->SetXY($x + 100, $y + 200);
            $pdf->MultiCell(90, 35, 'Signature et cachet de l\'employeur', 'LRBT', 'L', 1, 1);

            $pdf->SetFont($trebuchet, '', 6);
            $pdf->SetXY($x + 100, $y + 228);
            $pdf->MultiCell(90, 5, 'La signature du présent document vaut pour acceptation des conditions générales de vente.', '', 'L', 0, 1);

            $pdf->AddPage();

            //page 2 
            //logo
            list($imageWidth, $imageHeight, $imageType, $imageAttr) = $parent->getimagesize(
                    "test/logo/logo-CNFSE.png");
            //division because of mm to px conversion
            $w = $imageWidth / 3;

            $w = $imageWidth;
            if ($w > 30) {
                $w = 50;
            }
            $h = $imageHeight;
            if ($h > 20) {
                $h = 20;
            }
            $pdf->Image("test/logo/logo-CNFSE.png", 10, 2, $w, $h);

            $x = 10;
            $y = 25;
            //titre
            $pdf->SetFont($trebuchetbd, '', 16);
            $pdf->SetXY($x + 80, $y);
            $pdf->MultiCell(90, 5, 'Annexe', '', 'L', 0, 1);
            //tableau 1
            $pdf->SetFont($trebuchet, '', 9);
            $pdf->SetXY($x, $y + 30);
            $pdf->MultiCell(90, 5, 'Apprenants', '', 'L', 0, 1);

            $pdf->SetFont($trebuchetbd, '', 9);
            $pdf->SetXY($x + 50, $y + 30);
            $pdf->MultiCell(90, 5, $titre_contact . ' ' . $nom_contact . ' ' . $prenom_contact, '', 'L', 0, 1);

            $pdf->SetFont($trebuchet, '', 9);
            $pdf->SetXY($x, $y + 40);
            $pdf->MultiCell(90, 5, 'Intitule du stage :', '', 'L', 0, 1);

            $pdf->SetFont($trebuchet, '', 9);
            $pdf->SetXY($x + 50, $y + 40);
            $pdf->MultiCell(200, 5, $nom_formation, '', 'L', 0, 1);

            $pdf->SetFont($trebuchet, '', 9);
            $pdf->SetXY($x, $y + 50);
            $pdf->MultiCell(90, 5, 'Durée :', '', 'L', 0, 1);

            $duree_formation = 14;
            $pdf->SetFont($trebuchetbd, '', 9);
            $pdf->SetXY($x + 50, $y + 50);
            $pdf->MultiCell(200, 5, $duree_formation . ' Heures', '', 'L', 0, 1);

            $pdf->SetFont($trebuchet, '', 9);
            $pdf->SetXY($x, $y + 60);
            $pdf->MultiCell(90, 5, 'Lieu :', '', 'L', 0, 1);

            $pdf->SetFont($trebuchetbd, '', 9);
            $pdf->SetXY($x + 50, $y + 60);
            $pdf->MultiCell(200, 5, $adresse_formation . ',' . $cp_formation . ' ' . $ville_formation, '', 'L', 0, 1);

            $pdf->SetFont($trebuchet, '', 9);
            $pdf->SetXY($x, $y + 70);
            $pdf->MultiCell(90, 5, 'Dates :', '', 'L', 0, 1);

            $pdf->SetFont($trebuchetbd, '', 9);
            $pdf->SetXY($x + 50, $y + 70);
            $pdf->MultiCell(200, 5, 'Du ' . $date_debut_formation . ' au ' . $date_fin_formation, '', 'L', 0, 1);

            $pdf->SetFont($trebuchet, '', 9);
            $pdf->SetXY($x, $y + 80);
            $pdf->MultiCell(90, 5, 'Heure de début :', '', 'L', 0, 1);





            //$heure_debut_foration = "09:00";
            $heure_debut_matin = $modelColumn0['info_dates'][0]["start_matin"];
            $pdf->SetFont($trebuchetbd, '', 9);
            $pdf->SetXY($x + 50, $y + 80);
            $pdf->MultiCell(200, 5, $heure_debut_matin, '', 'L', 0, 1);

            //$pdf->SetXY($x + 10, $y + 90);
            $nbr_journee = count($modelColumn0['info_dates']);


            $pdf->SetXY($x + 10, $y + 90);
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
            $pdf->AddPage();

            //page 3 
            //logo
            list($imageWidth, $imageHeight, $imageType, $imageAttr) = $parent->getimagesize(
                    "test/logo/logo-CNFSE.png");
            //division because of mm to px conversion
            $w = $imageWidth / 3;

            $w = $imageWidth;
            if ($w > 30) {
                $w = 50;
            }
            $h = $imageHeight;
            if ($h > 20) {
                $h = 20;
            }
            $pdf->Image("test/logo/logo-CNFSE.png", 10, 2, $w, $h);

            $x = 10;
            $y = 3;

            $ref = "HACCP";
            $pdf->SetFont($trebuchet, '', 6);
            $pdf->SetXY($x + 170, $y);
            $pdf->MultiCell(200, 5, 'REF : ' . $ref, '', 'L', 0, 1);

            $pdf->SetFont($trebuchet, '', 7);
            $pdf->SetXY($x + 150, $y + 5);
            $pdf->MultiCell(200, 5, 'DUREE :', '', 'L', 0, 1);

            $pdf->SetDrawColor(0, 0, 0);
            $pdf->SetFillColor(218, 225, 255);
            $pdf->SetFont($trebuchet, '', 9);
            $pdf->SetXY($x + 170, $y + 5);
            $pdf->MultiCell(20, 5, $nbr_jour, '', 'L', 1, 1);


            $pdf->SetFont($trebuchet, '', 13);
            $pdf->SetXY($x + 35, $y + 20);
            $pdf->SetTextColor(0, 192, 192);
            $pdf->MultiCell(150, 5, $nom_formation, '', 'R', 0, 1);

            $pdf->SetFont($trebuchetbd, '', 8);
            $pdf->SetXY($x, $y + 40);
            $pdf->SetDrawColor(0, 0, 0);
            $pdf->SetFillColor(242, 255, 240);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->MultiCell(190, 5, 'OBJECTIFS', '', 'L', 1, 1);

            $pdf->SetFont($trebuchet, '', 8);
            $pdf->SetXY($x, $y + 50);
            $pdf->SetDrawColor(0, 0, 0);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetTextColor(0, 0, 0);
            $description_formation = $modelColumn0['info_product'][0]['description'];

            $pdf->writeHTML($description_formation, true, false, true, false, '');
            $pdf->AddPage();

            //page 4
            //condition general 
            $page_condition = 0;
            $pdf->SetXY(0, $page_condition + 3);
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
                    . '<h4>CONDITIONS D\'ANNULATION ET DE REPORT<h4>';

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
                    . '<h4>DATADOCK numéro d\'agrément : 0012160</h4>';
            $pdf->SetXY(0, $page_condition + 10);
            $pdf->SetFont($trebuchet, '', 7);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetTextColor(102, 102, 102);

            $pdf->writeHTMLCell(95, '', '', 10, $html1, 0, 0, 1, true, 'J', true);
            $pdf->writeHTMLCell(95, '', '', '', $html2, 0, 1, 1, true, 'J', true);
        }
    }

}
