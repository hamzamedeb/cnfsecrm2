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
            $pdf->MultiCell(190, 5, "D??claration d'activit?? enregistr??e sous le num??ro 11755161475 aupr??s de la Pr??fecture d\'Ile de France.Cet enregistrement ne vaut pas agr??ment de l'Etat.", 0, 'L', 0, 1);

            //$n_convention = "CO3698-20190122";
            $n_convention = $modelColumn0['convention_no'];
            $pdf->SetXY($x, $y + 13);
            $pdf->SetDrawColor(0, 0, 0);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetFont($trebuchetbd, '', 12);
            $pdf->MultiCell(190, 5, 'CONVENTION DE FORMATION N?? ' . $n_convention, 'B', 'L', 1, 1);


            $pdf->SetXY($x, $y + 22);
            $pdf->SetFont($trebuchet, '', 8);
            $pdf->MultiCell(190, 5, 'Entre les soussign??s :', 0, 'L', 0, 1);
            $pdf->SetXY($x, $y + 26);
            $pdf->MultiCell(100, 5, 'Organisme de formation', '', 'L', 0, 1);
            
            $nom_ste = $modelColumn0['organizationdetails'][0]['organizationname'];
            $adresse_ste = $modelColumn0['organizationdetails'][0]['address'];
            $code_postale_ste = $modelColumn0['organizationdetails'][0]['code'];
            $ville_ste = $modelColumn0['organizationdetails'][0]['city'];

            $nom_org = "Fr??deric";
            $prenom_org = "LAMBERT";
            $poste = "G??rant";
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
            $pdf->MultiCell(90, 5, 'Repr??sent?? par : ' . $nom_org . ' ' . $prenom_org, '', 'L', 1, 1);

            $pdf->SetXY($x, $y + 57);
            $pdf->MultiCell(90, 5, 'agissant en qualit?? de :' . $poste, '', 'L', 1, 1);

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
            $pdf->MultiCell(100, 5, 'Identit?? du client', '', 'L', 0, 1);

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
            $pdf->MultiCell(90, 5, 'repr??sent?? par :' . $titre_contact . ' ' . $nom_contact . ' ' . $prenom_contact, '', 'L', 1, 1);

            $pdf->SetXY($x + 100, $y + 57);
            $pdf->MultiCell(90, 5, 'agissant en qualit?? de ' . $travail_contact, '', 'L', 1, 1);


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
            $nom_formation = $modelColumn0['Intitul??'];
            $pdf->SetFont($trebuchet, '', 8);
            $pdf->SetXY($x, $y + 85);
            $pdf->MultiCell(200, 5, '- Intitul?? du stage : ', '', 'L', 0, 1);

            $pdf->SetFont($trebuchetbd, '', 8);
            $pdf->SetXY($x + 60, $y + 85);
            $pdf->MultiCell(200, 5, $nom_formation, '', 'L', 0, 1);


            $pdf->SetFont($trebuchet, '', 8);
            $pdf->SetXY($x, $y + 95);
            $pdf->MultiCell(200, 5, '- Objectifs, Programmes, m??thodes et moyens p??dagogiques : Se r??f??rer au programme de formation en annexe', '', 'L', 0, 1);

            $pdf->SetXY($x, $y + 100);
            $pdf->MultiCell(200, 5, '- Modalit??s de controle des connaissances et sanction de la formation : Tests d\'??valuation', '', 'L', 0, 1);

            $pdf->SetXY($x, $y + 105);
            $pdf->MultiCell(200, 5, '- Dur??e et lieu de l\'action :', '', 'L', 0, 1);
            
            $pdf->SetXY($x, $y + 110);
            $pdf->MultiCell(200, 5, '- P??riode :', '', 'L', 0, 1);
           
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
            $pdf->MultiCell(200, 5, $duree . ' heures ??', '', 'L', 0, 1);
            
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
            $pdf->MultiCell(200, 5, 'ARTICLE 2 - Donn??es financi??res', '', 'L', 0, 1);

            $pdf->SetFont($trebuchet, '', 8);
            $pdf->SetXY($x, $y + 130);
            $pdf->MultiCell(200, 5, 'Le montant des frais de formation ?? acquitter sera de :', '', 'L', 0, 1);
            
            $pdf->SetXY($x, $y + 135);
            $pdf->MultiCell(200, 5, '- Prix journalier : par pers', '', 'L', 0, 1);

            $pdf->SetXY($x, $y + 140);
            $pdf->MultiCell(200, 5, '- Nombre de jours :', '', 'L', 0, 1);

            $pdf->SetXY($x, $y + 145);
            $pdf->MultiCell(200, 5, '- Nombre d\'apprenants :', '', 'L', 0, 1);

            $pdf->SetXY($x, $y + 150);
            $pdf->MultiCell(200, 5, '- Frais de d??placement HT :', '', 'L', 0, 1);

            $pdf->SetXY($x, $y + 155);
            $pdf->MultiCell(200, 5, '- Autres frais HT :', '', 'L', 0, 1);

            $prix_jou = "174,50";
            //$nbr_jour = "2,00";
            $nbr_jour = $modelColumn0['info_product'][0]['quantity'];
            $frai_dep = $modelColumn0["frais_deplacement"];
            $autre_frai = $modelColumn0["autres_frais"];
            $pdf->SetFont($trebuchetbd, '', 8);
            $pdf->SetXY($x + 40, $y + 135);
            $pdf->MultiCell(50, 5, $prix_jou . ' ???', '', 'R', 0, 1);

            $pdf->SetXY($x + 40, $y + 140);
            $pdf->MultiCell(50, 5, $nbr_jour, '', 'R', 0, 1);

            $pdf->SetXY($x + 40, $y + 145);
            $pdf->MultiCell(50, 5, $nbr_apprenants, '', 'R', 0, 1);

            $pdf->SetXY($x + 40, $y + 150);
            $pdf->MultiCell(50, 5, $frai_dep . ' ???', '', 'R', 0, 1);

            $pdf->SetXY($x + 40, $y + 155);
            $pdf->MultiCell(50, 5, $autre_frai . ' ???', '', 'R', 0, 1);

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
            $pdf->MultiCell(50, 5, $s_t_formation . ' ???', '', 'R', 0, 1);

            $pdf->SetXY($x + 130, $y + 140);
            $pdf->MultiCell(50, 5, $s_t_frais . ' ???', '', 'R', 0, 1);

            $pdf->SetXY($x + 130, $y + 145);
            $pdf->MultiCell(50, 5, $support_cours . ' ???', '', 'R', 0, 1);

            $pdf->SetXY($x + 130, $y + 150);
            $pdf->MultiCell(50, 5, $remise . ' ???', '', 'R', 0, 1);

            $pdf->SetXY($x + 130, $y + 155);
            $pdf->MultiCell(50, 5, $total_ht . ' ???', '', 'R', 0, 1);

            $pdf->SetXY($x + 130, $y + 160);
            $pdf->MultiCell(50, 5, $tva . ' ???', '', 'R', 0, 1);

            $pdf->SetXY($x + 130, $y + 165);
            $pdf->MultiCell(50, 5, $total_ttc . ' ???', '', 'R', 0, 1);

            //article 3

            $pdf->SetFont($trebuchetbd, '', 8);
            $pdf->SetXY($x, $y + 175);
            $pdf->MultiCell(200, 5, 'ARTICLE 3 - Date d\'effet et dur??e de la convention', '', 'L', 0, 1);

            $pdf->SetFont($trebuchet, '', 8);
            $pdf->SetXY($x, $y + 180);
            $pdf->MultiCell(200, 5, 'La pr??sente convention prend effet ?? compter de la date de la signature par l\'entreprise et prendra fin le 29/01/2019 .', '', 'L', 0, 1);

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
            $pdf->MultiCell(200, 5, 'Fait en triple exemplaire ?? ' . $ville . ' le ' . $date_fait, '', 'L', 0, 1);

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
            $pdf->MultiCell(90, 5, 'La signature du pr??sent document vaut pour acceptation des conditions g??n??rales de vente.', '', 'L', 0, 1);

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
            $pdf->MultiCell(90, 5, 'Dur??e :', '', 'L', 0, 1);

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
            $pdf->MultiCell(90, 5, 'Heure de d??but :', '', 'L', 0, 1);





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
                                <p align="left" style="font-family: "Trebuchet MS", Helvetica, sans-serif;font-weight: normal;font-size: 8pt;">Calendrier des Journ??es</p>
                            </td>
                        </tr>
                       <tr style="font-size: 8pt;">
                            <th align="center" valign="middle" nowrap bgcolor="#7F9DB9">N?? Journ??e</th>
                            <th align="center" valign="middle" nowrap bgcolor="#7F9DB9">Date</th>
                            <th align="center" valign="middle" nowrap bgcolor="#7F9DB9">Heures matin</th>
                            <th align="center" valign="middle" nowrap bgcolor="#7F9DB9">Heures apr??s-midi</th>
                            <th align="center" valign="middle" nowrap bgcolor="#7F9DB9">Dur??e</th>
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
                <td align="center" bgcolor="' . $couleur . '">De ' . $heure_debut_matin . ' ?? ' . $heure_fin_matin . '</td>
                <td align="center" bgcolor="' . $couleur . '">De ' . $heure_debut_apresmidi . ' ?? ' . $heure_debut_apremidi . '</td>
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
                    . '-Stages et cycles interentreprises : Formation sur catalogue r??alis??e dans nos locaux <br/>'
                    . '-Formation intra-entreprise : Formation r??alis??e sur mesure pour le compte d\'un Client ou d\'un groupe. <br/>'
                    . '-Centre National de Formation en S??curit?? et Environnement sera remplac?? dans le texte suivant par C.N.F.S.E. <br/>'
                    . '<h4>OBJET ET CHAMP D\'APPLICATION</h4>'
                    . 'Toute commande de formation implique l\'acceptation sans r??serve par l\'acheteur et son adh??sion pleine et enti??re aux pr??sentes conditions g??n??rales de vente qui pr??valent sur tout autre document de l\'acheteur, et notamment sur toutes conditions g??n??rales d\'achat.<br/>'
                    . '<h4>DOCUMENTS CONTRACTUELS</h4>'
                    . 'C.N.F.S.E. fait parvenir au client, en double exemplaire, une convention de formation professionnelle continue telle que pr??vue par la loi. Le client s\'engage ?? retourner dans les plus brefs d??lais ?? C.N.F.S.E. un exemplaire sign?? et portant son cachet commercial. Une attestation de pr??sence est adress??e au Client apr??s chaque formation, cycle ou parcours.<br/>'
                    . '<h4>PRIX, FACTURATION ET REGLEMENTS</h4>'
                    . 'Tous nos prix sont indiqu??s hors taxes. Ils sont ?? majorer de la TVA au taux en vigueur. Tout stage, cycle ou parcours commenc?? est d?? en entier <br/>'
                    . '-Pour les formations interentreprises : <br/>'
                    . 'L\'acceptation de C.N.F.S.E. est conditionn??e par le paiement int??gral de la facture, C.N.F.S.E. se r??serve le droit de disposer librement des places retenues par le client, tant que les frais d\'inscription n\'auront pas ??t?? r??gl??s. Les factures sont payables, sans escompte et ?? l\'ordre de C.N.F.S.E. ?? r??ception de facture. <br/>Les repas ne sont pas compris dans le prix du stage.<br/>'
                    . '-Pour les formations intra-entreprise :<br/>'
                    . 'L\'acceptation de C.N.F.S.E. est conditionn??e par le r??glement d\'un acompte dans les conditions pr??vues ci-dessous.<br/>'
                    . 'Les factures sont payables, sans escompte et ?? l\'ordre de C.N.F.S.E :<br/>'
                    . '-Un acompte de 30 % est vers?? ?? la commande. Cet acompte restera acquis ?? C.N.F.S.E. si le client renonce ?? la formation.<br/>'
                    . '-Le compl??ment est d?? ?? r??ception des diff??rentes factures ??mises au fur et ?? mesure de l\'avancement des formations.<br/>'
                    . '-En cas de non-paiement int??gral d\'une facture venue ?? ??ch??ance, apr??s mise en demeure rest??e sans effet dans les 5 jours ouvrables, C.N.F.S.E. se r??serve la facult?? de suspendre toute formation en cours et /ou ?? venir.<br/>'
                    . '<h4>REGLEMENT PAR UN OPCA</h4>'
                    . 'Si le client souhaite que le r??glement soit ??mis par l\'OPCA dont il d??pend, il lui appartient :<br/>'
                    . '-de faire une demande de prise en charge avant le d??but de la formation et de s\'assurer de la bonne fin de cette demande ;<br/>'
                    . '-de l\'indiquer explicitement sur son bulletin d\'inscription ou sur son bon de commande ;<br/>'
                    . '-de s\'assurer de la bonne fin du paiement par l\'organisme qu\'il aura d??sign??. Si l\'OPCA ne prend en charge que partiellement le cout de la formation, le reliquat sera factur?? au Client.<br/>'
                    . 'Si C.N.F.S.E. n\'a pas re??u la prise en charge de l\'OPCA au 1er jour de la formation, le client sera factur?? de l\'int??gralit?? du co??t du stage. En cas de non-paiement par l\'OPCA, pour quelque motif que ce soit, le Client sera redevable de l\'int??gralit?? du co??t de la formation et sera factur?? du montant correspondant.<br/>'
                    . '<h4>PENALITE DE RETARD</h4>'
                    . 'Toute somme non pay??e ?? l\'??ch??ance donnera lieu au paiement par le Client de p??nalit??s de retard fix??es ?? trois fois le taux d\'int??r?? Ces p??nalit??s sont exigibles de plein droit, d??s r??ception de l\'avis informant le Client qu\'elles ont ??t?? port??es ?? son d??bit. Une indemnit?? forfaitaire de 40 ?? est due de plein droit (L. 441-6, I, 12) en cas de retard de paiement de toute cr??ance.<br/>'
                    . '<h4>REFUS DE COMMANDE</h4>'
                    . 'Dans le cas ou un Client passerait une commande ?? C.N.F.S.E., sans avoir proc??d?? au paiement de la (des) commande(s) pr??c??dente(s), C.N.F.S.E. pourra refuser d\'honorer la commande et de d??livrer les formations concern??es, sans que le Client puisse pr??tendre ?? une quelconque indemnit??, pour quelque raison que ce soit.<br/>'
                    . '<h4>CONDITIONS D\'ANNULATION ET DE REPORT<h4>';

            $html2 = '<h4>Stages intra et inter entreprise(s)</h4> '
                    . 'Toute annulation par le Client doit ??tre communiqu??e par ??crit. En cas d\'annulation par le client d\'une session de formation planifi??e, des indemnit??s compensatrices sont dues dans les conditions suivantes fut-ce en cas de force majeure :<br/>'
                    . 'Report ou annulation communiqu?? au moins 30 jours ouvr??s avant la session : aucune indemnit?? <br/>'
                    . 'Report ou annulation moins de 30 jours et au moins 10 jours ouvr??s avant la session : 30% des honoraires seront factur??s au client. <br/>'
                    . 'Report ou annulation communiqu?? moins de 10 jours ouvr??s avant la session : 70 % des honoraires seront factur??s au client.'
                    . 'C.N.F.S.E. ne pourra ??tre tenue responsable ?? l\'??gard du client en cas d\'inex??cution de ses obligations r??sultant d\'un ??v??nement de force majeure. Dans le cas ou le nombre de participants serait p??dagogiquement insuffisant pour le bon d??roulement de la session, l\'organisme de formation se r??serve le droit d\'annuler la formation au plus tard une semaine avant la date pr??vue.<br/>'
                    . '<h4>INFORMATIQUE ET LIBERTES</h4>'
                    . 'Les informations ?? caract??re personnel qui sont communiqu??es par le Client ?? C.N.F.S.E. en application et dans l\'ex??cution des commandes et/ou ventes pourront ??tre communiqu??es aux partenaires contractuels de C.N.F.S.E. pour les besoins desdites commandes.<br/>'
                    . 'Conform??ment ?? la r??glementation fran??aise qui est applicable ?? ces fichiers, le Client peut ??crire ?? C.N.F.S.E. pour s\'opposer ?? une telle communication des informations le concernant. Il peut ??galement ?? tout moment exercer ses droits d\'acc??s et de rectification dans le fichier de C.N.F.S.E.. <br/>'
                    . '<h4>RENONCIATION</h4>'
                    . 'Le fait pour C.N.F.S.E. de ne pas se pr??valoir ?? un moment donn?? de l\'une quelconque des clauses des pr??sentes, ne peut valoir renonciation ?? se pr??valoir ult??ieurement de ces m??me clauses. <br/>'
                    . '<h4>LOI APPLICABLE</h4>'
                    . 'Les Conditions G??n??rales et tous les rapports entre C.N.F.S.E. et ses Clients rel??vent de la Loi fran??aise.<br/>'
                    . '<h4>ATTRIBUTION DE COMPETENCES</h4>'
                    . 'Tous litiges qui ne pourraient ??tre r??gl??s ?? l\'amiable seront de la COMPETENCE EXCLUSIVE DU TRIBUNAL DE COMMERCE DE PARIS quel que soit le si??ge ou la r??sidence du Client, nonobstant pluralit?? de d??fendeurs ou appel en garantie. Cette clause attributive de comp??tence ne s\'appliquera pas au cas de litige avec un Client non professionnel pour lequel les r??gles l??gales de comp??tence mat??rielle et g??ographique s\'appliqueront. <br/>'
                    . 'La pr??sente clause est stipul??e dans l\'int??r??t de la soci??t?? L C.N.F.S.E. qui se r??serve le droit d\'y renoncer si bon lui semble. <br/>'
                    . '<h4>ELECTION DE DOMICILE</h4>'
                    . 'L\'??lection de domicile est faite par C.N.F.S.E. ?? son si??ge social 231 rue St Honor?? - 75001 PARIS <br/>'
                    . '<h4>DATADOCK num??ro d\'agr??ment : 0012160</h4>';
            $pdf->SetXY(0, $page_condition + 10);
            $pdf->SetFont($trebuchet, '', 7);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetTextColor(102, 102, 102);

            $pdf->writeHTMLCell(95, '', '', 10, $html1, 0, 0, 1, true, 'J', true);
            $pdf->writeHTMLCell(95, '', '', '', $html2, 0, 1, 1, true, 'J', true);
        }
    }

}
