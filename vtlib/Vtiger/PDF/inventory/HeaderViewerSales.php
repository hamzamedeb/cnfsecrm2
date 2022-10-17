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
            /* uni_cnfsecrm - v2 - modif 162 - DEBUT */
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
                $w = 70;
            }
            $h = $imageHeight;
            if ($h > 20) {
                $h = 24;
            }
            $pdf->Image("test/logo/logo-CNFSE-large.png", 10, 2, $w, $h);

            /* uni_cnfsecrm - v2 - modif 158 - DEBUT */
            $pdf->Image("test/logo/LogoQualiopi-Marianne.jpg", 135, 3, 45, 23);
            $pdf->Image("test/logo/compte.png", 180, 7, 15, 15);
            /* uni_cnfsecrm - v2 - modif 158 - FIN */

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
            $pdf->MultiCell(190, 5, "Déclaration d'activité enregistrée sous le numéro 11755161475 auprès de la Préfecture d'Ile de France.Cet enregistrement ne vaut pas agrément de l'Etat.", 0, 'L', 0, 1);

            $n_convention = $modelColumn0['n_convention'];
            $pdf->SetXY($x, $y += 10);
            $pdf->SetDrawColor(0, 0, 0);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetFont($trebuchetbd, '', 12);
            $pdf->MultiCell(190, 5, 'CONVENTION DE FORMATION N° ' . $n_convention, 'B', 'L', 1, 1);


            $pdf->SetXY($x, $y += 8);
            $pdf->SetFont($trebuchet, '', 8);
            $pdf->MultiCell(190, 5, 'Entre les soussignés :', 0, 'L', 0, 1);
            $pdf->SetXY($x, $y += 4);
            $pdf->MultiCell(100, 5, 'Organisme de formation', '', 'L', 0, 1);

            $nom_ste = html_entity_decode($modelColumn0['organizationdetails'][0]['organizationname']);
            $adresse_ste = html_entity_decode($modelColumn0['organizationdetails'][0]['address']);
            $code_postale_ste = $modelColumn0['organizationdetails'][0]['code'];
            $ville_ste = html_entity_decode($modelColumn0['organizationdetails'][0]['city']);

            $nom_org = "Fréderic";
            $prenom_org = "LAMBERT";
            $poste = "Gérant";
            $pdf->SetDrawColor(0, 0, 0);
            $pdf->SetFillColor(255, 255, 255);

            $y = 52;

            $pdf->SetFont($trebuchetbi, '', 9);
            $pdf->SetDrawColor(0, 0, 0);
            $pdf->SetFillColor(255, 247, 230);
            $pdf->SetXY($x, $y += 5);
            $pdf->MultiCell(90, 5, $nom_ste, '', 'L', 1, 1);

            $pdf->SetXY($x, $y += 5);
            $pdf->MultiCell(90, 15, $adresse_ste, '', 'L', 1, 1);

            $pdf->SetXY($x, $y += 7);
            $pdf->MultiCell(90, 15, $code_postale_ste . ' ' . $ville_ste, '', 'L', 1, 1);

            /* $pdf->SetXY($x, $y += 7);
              $pdf->MultiCell(90, 5, 'Représenté par : ' . $nom_org . ' ' . $prenom_org, '', 'L', 1, 1);

              $pdf->SetXY($x, $y += 5);
              $pdf->MultiCell(90, 5, 'agissant en qualité de :' . $poste, '', 'L', 1, 1); */

            //partie 2 
            $y = 52;
            $adresse_client = html_entity_decode($modelColumn0['info_client']['adresse']);
            $code_postale = $modelColumn0['info_client']['cp'];
            $ville_client = html_entity_decode($modelColumn0['info_client']['ville']);
            $nom_client = html_entity_decode($modelColumn0['info_client']['accountname']);
            $titre_contact = html_entity_decode($modelColumn0['info_contact']['titre_contact']);
            $nom_contact = html_entity_decode($modelColumn0['info_contact']['nom_contact']);
            $prenom_contact = html_entity_decode($modelColumn0['info_contact']['prenom_contact']);
            $travail_contact = html_entity_decode($modelColumn0['info_contact']['travail_contact']);

            $adresse_formation = html_entity_decode($modelColumn0['adresse_formation']);
            $adresse_formation = ucfirst(strtolower($adresse_formation));
            $cp_formation = $modelColumn0['cp_formation'];
            $ville_formation = html_entity_decode($modelColumn0['ville_formation']);

            $nbr_jours = decimalFormat($modelColumn0['info_product'][0]['nbrjours'], 1, '.', '');
            $nbr_heures = decimalFormat($modelColumn0['info_product'][0]['nbrheures'], 1, '.', '');
            $naturecalcul = $modelColumn0['info_product'][0]['naturecalcul'];
            $parpersonne = $modelColumn0['info_product'][0]['parpersonne'];
            $listprice = $modelColumn0['info_product'][0]['listprice'];
            $frai_dep = str_replace(",", ".", $modelColumn0['frais_deplacement']);
            $financement = str_replace(",", ".", $modelColumn0['financement']);
            $autre_frai = str_replace(",", ".", $modelColumn0['autres_frais']);

            $pdf->SetDrawColor(0, 0, 0);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetFont($trebuchet, '', 8);

            $pdf->SetXY($x + 100, $y);
            $pdf->MultiCell(100, 5, 'Identité du client', '', 'L', 0, 1);

            $pdf->SetFont($trebuchetbi, '', 9);
            $contentHeight = $pdf->GetStringHeight($nom_client, 100);
            $pdf->SetDrawColor(0, 0, 0);
            $pdf->SetFillColor(255, 247, 230);
            $pdf->SetXY($x + 100, $y += 5);
            $pdf->MultiCell(90, $contentHeight, $nom_client, '', 'L', 1, 1);

            if ($contentHeight == 0) {
                $contentHeight = 6;
            }
            $pdf->SetXY($x + 100, $y += $contentHeight);
            $contentHeight = $pdf->GetStringHeight($adresse_client, 100);
            $pdf->MultiCell(90, $contentHeight, $adresse_client, '', 'L', 1, 1);

            if ($contentHeight == 0) {
                $contentHeight = 6;
            }
            $pdf->SetXY($x + 100, $y += $contentHeight);
            $contentHeight = $pdf->GetStringHeight($code_postale . ' ' . $ville_client, 100);
            $pdf->MultiCell(90, $contentHeight, $code_postale . ' ' . $ville_client, '', 'L', 1, 1);

            /* uni_cnfsecrm - v2 - modif 133 - DEBUT */
            if ($nom_contact != '' && $prenom_contact != '') {
                if ($contentHeight == 0) {
                    $contentHeight = 6;
                }
                $pdf->SetXY($x + 100, $y += $contentHeight);
                $contentHeight = $pdf->GetStringHeight($titre_contact + $nom_contact + $prenom_contact, 100);
                $pdf->MultiCell(90, $contentHeight, 'représenté par :' . $titre_contact . ' ' . $prenom_contact . ' ' . $nom_contact, '', 'L', 1, 1);
            }
            if ($travail_contact != '') {
                if ($contentHeight == 0) {
                    $contentHeight = 6;
                }
                $pdf->SetXY($x + 100, $y += $contentHeight);
                $contentHeight = $pdf->GetStringHeight($travail_contact, 90);
                $pdf->MultiCell(90, $contentHeight, 'agissant en qualité de ' . $travail_contact, '', 'L', 1, 1);
            }


            /* uni_cnfsecrm - v2 - modif 133 - FIN */

            if ($contentHeight == 0) {
                $contentHeight = 6;
            }
            $pdf->SetDrawColor(0, 0, 0);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetFont($trebuchet, '', 8);
            $pdf->SetXY($x, $y += $contentHeight);
            $pdf->MultiCell(200, $contentHeight, 'est conclue la convention suivante, en application du livre IX du Code du travail', '', 'L', 0, 1);
            /* uni_cnfsecrm - v2 - modif 133 - DEBUT */
            $y += 5;
            /* uni_cnfsecrm - v2 - modif 133 - FIN */
            //article 1:
            //date de formation
            $info_dates = $modelColumn0['info_dates'];

            $nbre_jour = count($info_dates);

            for ($i = 0; $i < $nbre_jour; $i++) {
                $list_date[$i] = $info_dates[$i]['date_start'];
            }
            //sort($list_date);
            $premier_jour = $list_date[0];
            if ($premier_jour != null) {
                $date_debut_formation = formatDateFr($premier_jour);
            }

            $dernier_jour = $list_date[$i - 1];
            if ($dernier_jour != null) {
                $date_fin_formation = formatDateFr($dernier_jour);
            }

            $nbr_apprenants = $modelColumn0['info_apprenants']['nbr_apprenants'];

            $pdf->SetFont($trebuchetbd, '', 8);
            /* uni_cnfsecrm - v2 - modif 156 - DEBUT */
            $pdf->SetXY($x, $y += 5);
            /* uni_cnfsecrm - v2 - modif 156 - FIN */
            $pdf->MultiCell(200, 5, 'ARTICLE 1 - Objet de la convention', '', 'L', 0, 1);

            $type_formation = $modelColumn0['type_formation'];
            $elearning = $modelColumn0['elearning'];
            $pdf->SetFont($trebuchet, '', 8);
            $pdf->SetXY($x, $y += 5);
            $pdf->MultiCell(200, 5, 'L\'organisme de formation réalise l’action de formation suivante : ', '', 'L', 0, 1);

            $pdf->SetFont($trebuchetbd, '', 8);
            $pdf->SetXY($x + 85, $y);
            $pdf->MultiCell(200, 5, $type_formation, '', 'L', 0, 1);
            //var_dump($modelColumn0);
            $nom_formation = $modelColumn0['subject'];
            $observations = $modelColumn0['observations'];
            $pdf->SetFont($trebuchet, '', 8);
            $pdf->SetXY($x, $y += 5);
            $pdf->MultiCell(200, 5, '- Intitulé du stage : ', '', 'L', 0, 1);

            $pdf->SetFont($trebuchetbd, '', 8);
            $pdf->SetXY($x + 30, $y);
            $pdf->MultiCell(200, 5, $nom_formation, '', 'L', 0, 1);

            $contentHeight = $pdf->GetStringHeight($nom_formation, 100);

            $pdf->SetFont($trebuchet, '', 8);
            $pdf->SetXY($x, $y += $contentHeight);
            $pdf->MultiCell(200, 5, $observations, '', 'L', 0, 1);

            $contentHeight = $pdf->GetStringHeight($observations, 100);

            $pdf->SetFont($trebuchet, '', 8);
            $pdf->SetXY($x, $y += $contentHeight);
            $pdf->MultiCell(200, 5, '- Objectifs, Programmes, méthodes et moyens pédagogiques : Se référer au programme de formation en annexe', '', 'L', 0, 1);

            $pdf->SetXY($x, $y += 5);
            $pdf->MultiCell(200, 5, '- Modalités de contrôle des connaissances et sanction de la formation : Test d\'évaluation', '', 'L', 0, 1);
            $pdf->SetXY($x, $y += 5);
            $pdf->MultiCell(200, 5, "- Lieu de l'action : ", '', 'L', 0, 1);
            $pdf->SetXY($x + 25, $y);
            $pdf->SetFont($trebuchetbd, '', 8);
            $pdf->MultiCell(200, 5, "$adresse_formation , $cp_formation  $ville_formation", '', 'L', 0, 1);
            $pdf->SetFont($trebuchet, '', 8);
            $pdf->SetXY($x, $y += 5);
            $pdf->MultiCell(200, 5, '- nom des apprenants : ', '', 'L', 0, 1);
            $tiret = "";
            $list_apprenant = "";
            for ($i = 0; $i < $nbr_apprenants; $i++) {
                $pdf->SetFont($trebuchetbd, '', 7);
                $nom_apprenant = html_entity_decode($modelColumn0['info_apprenants'][$i]['firstname']);
                $prenom_apprenant = html_entity_decode($modelColumn0['info_apprenants'][$i]['lastname']);
                $list = $tiret . '' . $nom_apprenant . ' ' . $prenom_apprenant;
                $tiret = " / ";
                $list_apprenant .= $list;
            }

            $pdf->SetXY($x + 30, $y + 0.3);
            $pdf->MultiCell(150, 5, $list_apprenant, '', 'L', 0, 1);
            $pdf->SetFont($trebuchet, '', 8);
            $pdf->SetXY($x, $y += 10);
            $pdf->MultiCell(80, 5, "Période du $date_debut_formation  au  $date_fin_formation ", 'BR', 'L', 0, 1);
            $pdf->SetXY($x + 80, $y);
            $pdf->MultiCell(80, 5, "Durée : $nbr_heures heures sur $nbr_jours jour(s)  ", 'B', 'L', 0, 1);

            $pdf->SetXY($x, $y += 5);
            $pdf->MultiCell(80, 5, "Nombre d’apprenant(s) : $nbr_apprenants ", 'R', 'L', 0, 1);
            $pdf->SetXY($x + 80, $y);
            $heure_debut_matin0 = $info_dates[0]["start_matin"];
            $heure_fin_matin0 = $info_dates[0]["end_matin"];
            $heure_debut_apresmidi0 = $info_dates[0]["start_apresmidi"];
            $heure_debut_apremidi0 = $info_dates[0]["end_apresmidi"];
            $pdf->MultiCell(80, 5, "Journée de $heure_debut_matin0 h à $heure_fin_matin0 h & $heure_debut_apresmidi0 h à $heure_debut_apremidi0 h   ", '', 'L', 0, 1);

            /* if ($elearning != "1") {
              $pdf->MultiCell(200, 5, '- Durée et lieu de l\'action :', '', 'L', 0, 1);
              $pdf->SetFont($trebuchetbd, '', 8);
              $pdf->SetXY($x + 50, $y);
              $pdf->MultiCell(200, 5, $nbr_heures . ' heures à ' . $adresse_formation . ',' . $cp_formation . ' ' . $ville_formation, '', 'L', 0, 1);

              $pdf->SetXY($x, $y += 5);
              $pdf->SetFont($trebuchet, '', 8);
              $pdf->MultiCell(200, 5, '- Période :', '', 'L', 0, 1);
              $pdf->SetXY($x + 50, $y);
              $pdf->MultiCell(200, 5, 'Du ' . $date_debut_formation . ' au ' . $date_fin_formation, '', 'L', 0, 1);
              } else {
              $pdf->SetXY($x, $y += 5);
              $pdf->SetFont($trebuchet, '', 8);
              $pdf->MultiCell(200, 5, '- Période :', '', 'L', 0, 1);
              $pdf->SetXY($x + 50, $y);
              $pdf->MultiCell(200, 5, 'Formation en ligne valable 30 jours', '', 'L', 0, 1);
              }
              $pdf->SetXY($x, $y += 5);
              $pdf->SetFont($trebuchet, '', 8);
              $pdf->MultiCell(200, 5, '- Nombre d\'apprenants :', '', 'L', 0, 1);
              $pdf->SetXY($x + 50, $y);
              $pdf->MultiCell(200, 5, $nbr_apprenants, '', 'L', 0, 1);

              $pdf->SetXY($x, $y += 5);
              $pdf->SetFont($trebuchet, '', 8);
              $pdf->MultiCell(200, 5, '- Noms des apprenants :', '', 'L', 0, 1);

              $tiret = "";
              $list_apprenant = "";
              for ($i = 0; $i < $nbr_apprenants; $i++) {
              $pdf->SetFont($trebuchetbd, '', 6);
              $nom_apprenant = html_entity_decode($modelColumn0['info_apprenants'][$i]['firstname']);
              $prenom_apprenant = html_entity_decode($modelColumn0['info_apprenants'][$i]['lastname']);
              $list = $tiret . '' . $nom_apprenant . ' ' . $prenom_apprenant;
              $tiret = " / ";
              $list_apprenant .= $list;
              }

              $pdf->SetXY($x1 + 60, $y);
              $pdf->MultiCell(150, 5, $list_apprenant, '', 'L', 0, 1); */
            //article 2            

            $pdf->SetFont($trebuchetbd, '', 8);
            $pdf->SetXY($x, $y += 7);
            $pdf->MultiCell(200, 5, 'ARTICLE 2 - Données financières', '', 'L', 0, 1);

            $pdf->SetFont($trebuchet, '', 8);
            $pdf->SetXY($x, $y += 5);
            $pdf->MultiCell(200, 5, 'Le montant des frais de formation à acquitter sera de :', '', 'L', 0, 1);
            $label_par_personne = ($parpersonne == "on") ? 'par pers' : '';
            switch ($naturecalcul) {
                case 'jour':
                    $label_prix_journalier = '- Prix journalier : ' . $label_par_personne;
                    $label_nbr_journalier = 'Nombre de jours';
                    $label_duree = 'jours';
                    $nbr_jours_heures = $nbr_jours;
                    break;

                case 'heure':
                    $label_prix_journalier = '-  Prix horaire : ' . $label_par_personne;
                    $label_nbr_journalier = "Nombre d'heures";
                    $label_duree = 'heures';
                    $nbr_jours_heures = $nbr_heures;
                    break;

                default:
                    $label_prix_journalier = '- Prix journalier : ' . $label_par_personne;
                    $label_nbr_journalier = 'Nombre de jours';
                    $label_duree = 'jours';
                    $nbr_jours_heures = $nbr_jours;
                    break;
            }
            $s_t_formation = str_replace(",", ".", $modelColumn0['soustotalht']);
            $s_t_frais = str_replace(",", ".", $modelColumn0['totalfrais']);
            $support_cours = "0,00";
            $remise = str_replace(",", ".", $modelColumn0['discount_amount']);
            $total_ht = str_replace(",", ".", $modelColumn0['totalht']);
            $taux_tva = number_format($modelColumn0["taux_tva"], 0, '.', '');
            //echo ($taux_tva * $total_ht);
            $tva = (floatval($taux_tva) * floatval($total_ht)) / 100;
            $total_ttc = floatval($total_ht) + floatval($tva);
            $total_ht_financement = $total_ht - $financement;
            $tva_without_financement = $modelColumn0['tax_totalamount'];
            $total_ttc_without_financement = $modelColumn0['totalttc'];
/* uni_cnfsecrm - v2 - modif 163 - DEBUT */
            $pdf->SetXY($x, $y += 5);
            $pdf->MultiCell(80, 5, $label_prix_journalier, 'BR', 'L', 0, 1);
            $pdf->SetFont($trebuchetbd, '', 8);
            $pdf->SetXY($x + 35, $y);
            $pdf->MultiCell(40, 5, $listprice . " €", '', 'R', 0, 1);

            $pdf->SetFont($trebuchet, '', 8);
            $pdf->SetXY($x + 80, $y);
            $pdf->MultiCell(80, 5, "- Total HT : ", 'B', 'L', 0, 1);
            $pdf->SetFont($trebuchetbd, '', 8);
            $pdf->SetXY($x + 115, $y);
            $pdf->MultiCell(40, 5, formatPrice($total_ht) . " € ", '', 'R', 0, 1);
            
            $pdf->SetXY($x, $y += 5);
            $pdf->SetFont($trebuchet, '', 8);
            $pdf->MultiCell(80, 5, "- Total Formation HT : ", 'BR', 'L', 0, 1);
            $pdf->SetFont($trebuchetbd, '', 8);
            $pdf->SetXY($x + 35, $y);
            $pdf->MultiCell(40, 5, formatPrice($s_t_formation) . " €", '', 'R', 0, 1);

            $pdf->SetFont($trebuchet, '', 8);
            $pdf->SetXY($x + 80, $y);
            $pdf->MultiCell(80, 5, "- Tva " . $taux_tva . " % : ", 'B', 'L', 0, 1);
            $pdf->SetFont($trebuchetbd, '', 8);
            $pdf->SetXY($x + 115, $y);
            $pdf->MultiCell(40, 5, formatPrice($tva) . " € ", '', 'R', 0, 1);
            
            $pdf->SetXY($x, $y += 5);
            $pdf->SetFont($trebuchet, '', 8);
            $pdf->MultiCell(80, 5, "- Total Frais HT : ", 'BR', 'L', 0, 1);
            $pdf->SetFont($trebuchetbd, '', 8);
            $pdf->SetXY($x + 35, $y);
            $pdf->MultiCell(40, 5, formatPrice($s_t_frais) . " €", '', 'R', 0, 1);

            $pdf->SetXY($x + 80, $y);
            $pdf->MultiCell(80, 5, "- TOTAL TTC ", 'B', 'L', 0, 1);
            $pdf->SetXY($x + 115, $y);
            $pdf->MultiCell(40, 5, formatPrice($total_ttc) . " € ", '', 'R', 0, 1);
            
            $pdf->SetXY($x, $y += 5);
            $pdf->SetFont($trebuchet, '', 8);
            $pdf->MultiCell(80, 5, "- Remise HT : ", 'R', 'L', 0, 1);
            $pdf->SetFont($trebuchetbd, '', 8);
            $pdf->SetXY($x + 35, $y);
            $pdf->MultiCell(40, 5, formatPrice($remise) . " €", '', 'R', 0, 1);

            /* uni_cnfsecrm - v2 - modif 163 - FIN */

            

            /*
              $pdf->SetXY($x, $y += 5);
              $y1 = $y;
              $pdf->MultiCell(200, 5, $label_prix_journalier, '', 'L', 0, 1);
              $pdf->SetFont($trebuchetbd, '', 8);
              $pdf->SetXY($x + 40, $y);
              $pdf->MultiCell(50, 5, $listprice . ' €', '', 'R', 0, 1);

              $pdf->SetFont($trebuchet, '', 8);
              $pdf->SetXY($x, $y += 5);
              $pdf->MultiCell(200, 5, '- ' . $label_nbr_journalier . '  :', '', 'L', 0, 1);
              $pdf->SetFont($trebuchetbd, '', 8);
              $pdf->SetXY($x + 40, $y);
              $pdf->MultiCell(50, 5, number_format($nbr_jours_heures, 2, ',', ''), '', 'R', 0, 1);

              $pdf->SetFont($trebuchet, '', 8);
              $pdf->SetXY($x, $y += 5);
              $pdf->MultiCell(200, 5, '- Nombre d\'apprenants :', '', 'L', 0, 1);
              $pdf->SetFont($trebuchetbd, '', 8);
              $pdf->SetXY($x + 40, $y);
              $pdf->MultiCell(50, 5, $nbr_apprenants, '', 'R', 0, 1);

              $pdf->SetFont($trebuchet, '', 8);
              $pdf->SetXY($x, $y += 5);
              $pdf->MultiCell(200, 5, '- Frais de déplacement HT :', '', 'L', 0, 1);
              $pdf->SetFont($trebuchetbd, '', 8);
              $pdf->SetXY($x + 40, $y);
              $pdf->MultiCell(50, 5, formatPrice($frai_dep) . ' €', '', 'R', 0, 1);

              $pdf->SetFont($trebuchet, '', 8);
              $pdf->SetXY($x, $y += 5);
              $pdf->MultiCell(200, 5, '- Autres frais HT :', '', 'L', 0, 1);
              $pdf->SetFont($trebuchetbd, '', 8);
              $pdf->SetXY($x + 40, $y);
              $pdf->MultiCell(50, 5, formatPrice($autre_frai) . ' €', '', 'R', 0, 1);

              //partie 2
              $y = $y1;
              $s_t_formation = str_replace(",", ".", $modelColumn0['soustotalht']);
              $s_t_frais = str_replace(",", ".", $modelColumn0['totalfrais']);
              $support_cours = "0,00";
              $remise = str_replace(",", ".", $modelColumn0['discount_amount']);
              $total_ht = str_replace(",", ".", $modelColumn0['totalht']);
              $taux_tva = number_format($modelColumn0["taux_tva"], 0, '.', '');
              //echo ($taux_tva * $total_ht);
              $tva = (floatval($taux_tva) * floatval($total_ht)) / 100;
              $total_ttc = floatval($total_ht) + floatval($tva);
              $total_ht_financement = $total_ht - $financement;
              $tva_without_financement = $modelColumn0['tax_totalamount'];
              $total_ttc_without_financement = $modelColumn0['totalttc'];


              $pdf->SetFont($trebuchet, '', 8);
              $pdf->SetXY($x + 100, $y);
              $pdf->MultiCell(200, 5, '- Sous total Formation HT :', '', 'L', 0, 1);
              $pdf->SetFont($trebuchetbd, '', 8);
              $pdf->SetXY($x + 130, $y);
              $pdf->MultiCell(50, 5, formatPrice($s_t_formation) . ' €', '', 'R', 0, 1);

              $pdf->SetFont($trebuchet, '', 8);
              $pdf->SetXY($x + 100, $y += 5);
              $pdf->MultiCell(200, 5, '- Sous total Frais HT :', '', 'L', 0, 1);
              $pdf->SetFont($trebuchetbd, '', 8);
              $pdf->SetXY($x + 130, $y);
              $pdf->MultiCell(50, 5, formatPrice($s_t_frais) . ' €', '', 'R', 0, 1);

              $pdf->SetFont($trebuchet, '', 8);
              $pdf->SetXY($x + 100, $y += 5);
              $pdf->MultiCell(200, 5, '- Support de cours HT :', '', 'L', 0, 1);
              $pdf->SetFont($trebuchetbd, '', 8);
              $pdf->SetXY($x + 130, $y);
              $pdf->MultiCell(50, 5, formatPrice($support_cours) . ' €', '', 'R', 0, 1);

              $pdf->SetFont($trebuchet, '', 8);
              $pdf->SetXY($x + 100, $y += 5);
              $pdf->MultiCell(200, 5, '- Remise HT :', '', 'L', 0, 1);
              $pdf->SetFont($trebuchetbd, '', 8);
              $pdf->SetXY($x + 130, $y);
              $pdf->MultiCell(50, 5, formatPrice($remise) . ' €', '', 'R', 0, 1);

              $pdf->SetFont($trebuchet, '', 8);
              $pdf->SetXY($x + 100, $y += 5);
              $pdf->MultiCell(200, 5, '- Total HT :', '', 'L', 0, 1);
              $pdf->SetFont($trebuchetbd, '', 8);
              $pdf->SetXY($x + 130, $y);
              $pdf->MultiCell(50, 5, formatPrice($total_ht) . ' €', '', 'R', 0, 1);

              $pdf->SetFont($trebuchet, '', 8);
              $pdf->SetXY($x + 100, $y += 5);
              $pdf->MultiCell(200, 5, '- Tva ' . $taux_tva . ' % :', '', 'L', 0, 1);
              $pdf->SetFont($trebuchetbd, '', 8);
              $pdf->SetXY($x + 130, $y);
              $pdf->MultiCell(50, 5, formatPrice($tva) . ' €', '', 'R', 0, 1);

              $pdf->SetFont($trebuchetbd, '', 8);
              $pdf->SetXY($x + 100, $y += 5);
              $pdf->MultiCell(200, 5, '- TOTAL TTC', '', 'L', 0, 1);
              $pdf->SetXY($x + 130, $y);
              $pdf->MultiCell(50, 5, formatPrice($total_ttc) . ' €', '', 'R', 0, 1);
             */



            //article 3

            $pdf->SetFont($trebuchetbd, '', 8);
            $pdf->SetXY($x, $y += 7);
            $pdf->MultiCell(200, 5, 'ARTICLE 3 - Date d\'effet et durée de la convention', '', 'L', 0, 1);

            $pdf->SetFont($trebuchet, '', 8);
            $pdf->SetXY($x, $y += 5);
            $pdf->MultiCell(200, 5, 'La présente convention prend effet à compter de la date de la signature par l\'entreprise et prendra fin le ' . $date_fin_formation . ' .', '', 'L', 0, 1);

            //article 4

            $pdf->SetFont($trebuchetbd, '', 8);
            $pdf->SetXY($x, $y += 5);
            $pdf->MultiCell(200, 5, 'ARTICLE 4 - Subrogation', '', 'L', 0, 1);

            $pdf->SetFont($trebuchet, '', 8);
            $pdf->SetXY($x, $y += 5);
            if ($financement > 0) {
                $phrase_financement = 'Oui - (*) Détails Prise en charge et financement en annexe';
            } else {
                $phrase_financement = 'Nom de facturation : ' . $nom_client;
            }
            $pdf->MultiCell(200, 5, $phrase_financement, '', 'L', 0, 1);

            /*
              $date_fait = formatDateFr($modelColumn0['crmentity']['date_creation']);

              $pdf->SetFont($trebuchet, '', 8);
              $pdf->SetXY($x, $y += 5);
              $ville = 'PARIS';
              $pdf->MultiCell(200, 5, 'Fait en triple exemplaire à ' . $ville . ' le ' . $date_fait, '', 'L', 0, 1);
             */
            /* uni_cnfsecrm - v2 - modif 156 - DEBUT */

            $pdf->SetFont($trebuchetbd, '', 8);
            $pdf->SetXY($x, $y += 5);
            $pdf->MultiCell(200, 5, 'ARTICLE 5 - CGV & CGU', '', 'L', 0, 1);
            $pdf->SetFont($trebuchet, '', 8);
            $text = "La signature du présent document vaut acceptation des conditions générales de vente et les conditions générales d’utilisation qui sont téléchargeables en cliquant sur les liens suivants : :
                    <ul>
                        <li>C.G.U. : https://cnfse.fr/wp-content/uploads/2018/02/CGU.pdf</li>
                        <li>C.G.V. : https://cnfse.fr/wp-content/uploads/2018/02/CGV.pdf</li>
                    </ul>
                    ";
            $pdf->writeHTMLCell(190, '', $x, $y += 5, $text, 0, 0, 1, true, 'L', true);
            /* uni_cnfsecrm - v2 - modif 156 - FIN */

            //signature
            //$pdf->RoundedRect(10, $y + 180, 100, 20, 2, '1111', 'DF', array(), array(255, 255, 255));
            // $pdf->RoundedRect($x, $y, $w, $h, $r, $round_corner='1111', $style='', $border_style=array(), $fill_color=array()) ;
            /* uni_cnfsecrm - v2 - modif 156 - DEBUT */

            $y = 225;
            /* uni_cnfsecrm - v2 - modif 156 - FIN */

            $pdf->SetFont($trebuchet, '', 8);
            $pdf->SetXY($x, $y += 5);
            $pdf->MultiCell(90, 35, 'Signature et cachet de l\'organisme de formation', 'LRBT', 'L', 1, 1);
            $pdf->Image("test/Signature/Signature.jpg", $x + 60, $y += 5, 25, 20);


            /* uni_cnfsecrm - v2 - modif 156 - DEBUT */
            $y = 225;
            /* uni_cnfsecrm - v2 - modif 156 - FIN */
            $pdf->SetFont($trebuchet, '', 8);
            $pdf->SetXY($x + 100, $y += 5);
            $pdf->MultiCell(90, 35, 'Signature et cachet de l\'employeur', 'LRBT', 'L', 1, 1);

            $pdf->SetFont($trebuchet, '', 6);
            $pdf->SetXY($x + 100, $y + 27);
            $pdf->MultiCell(90, 5, 'La signature du présent document vaut pour acceptation des conditions générales de vente.', '', 'L', 0, 1);
            if ($elearning != "1") { /* unicnfsecrm - page annexe - affiche si no elearning */
                $pdf->AddPage();

                //page 2
                //logo
                list($imageWidth, $imageHeight, $imageType, $imageAttr) = $parent->getimagesize(
                        "test/logo/logo-CNFSE-large.png");
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
                $pdf->Image("test/logo/logo-CNFSE-large.png", 10, 2, $w, $h);

                $x = 10;
                $y = 25;
                //titre
                $pdf->SetFont($trebuchetbd, '', 16);
                $pdf->SetXY($x + 80, $y);
                $pdf->MultiCell(90, 5, 'Annexe', '', 'L', 0, 1);
                //tableau 1
                $pdf->SetFont($trebuchet, '', 9);
                $pdf->SetXY($x, $y += 15);
                $pdf->MultiCell(90, 5, 'Apprenants', '', 'L', 0, 1);
                //info_apprenants
                $nbr_apprenants = $modelColumn0['info_apprenants']['nbr_apprenants'];


                $tiret = "";
                $list_apprenant = "";
                $firstname_apprenants = "";
                $lastname_apprenants = "";
                for ($i = 0; $i < $nbr_apprenants; $i++) {
                    $firstname_apprenants = html_entity_decode($modelColumn0['info_apprenants'][$i]['firstname']);
                    $lastname_apprenants = html_entity_decode($modelColumn0['info_apprenants'][$i]['lastname']);
                    $list = $tiret . '' . $firstname_apprenants . ' ' . $lastname_apprenants;
                    $tiret = " / ";
                    $list_apprenant .= $list;
                }

                $pdf->SetFont($trebuchetbd, '', 6);
                $pdf->SetXY($x + 50, $y);
                $pdf->MultiCell(130, 5, $list_apprenant, '', 'L', 0, 1);


                $pdf->SetFont($trebuchet, '', 9);
                $pdf->SetXY($x, $y += 10);
                $pdf->MultiCell(90, 5, 'Intitule du stage :', '', 'L', 0, 1);

                $pdf->SetFont($trebuchet, '', 9);
                $pdf->SetXY($x + 50, $y);
                $pdf->MultiCell(200, 5, $nom_formation, '', 'L', 0, 1);

                $pdf->SetFont($trebuchet, '', 9);
                $pdf->SetXY($x, $y += 10);
                $pdf->MultiCell(90, 5, 'Durée :', '', 'L', 0, 1);

                $pdf->SetFont($trebuchetbd, '', 9);
                $pdf->SetXY($x + 50, $y);
                $pdf->MultiCell(200, 5, $nbr_heures . ' Heures', '', 'L', 0, 1);

                $pdf->SetFont($trebuchet, '', 9);
                $pdf->SetXY($x, $y += 10);
                $pdf->MultiCell(90, 5, 'Lieu :', '', 'L', 0, 1);

                $pdf->SetFont($trebuchetbd, '', 9);
                $pdf->SetXY($x + 50, $y);
                $pdf->MultiCell(200, 5, $adresse_formation . ',' . $cp_formation . ' ' . $ville_formation, '', 'L', 0, 1);

                $pdf->SetFont($trebuchet, '', 9);
                $pdf->SetXY($x, $y += 10);
                $pdf->MultiCell(90, 5, 'Dates :', '', 'L', 0, 1);

                $pdf->SetFont($trebuchetbd, '', 9);
                $pdf->SetXY($x + 50, $y);
                $pdf->MultiCell(200, 5, 'Du ' . $date_debut_formation . ' au ' . $date_fin_formation, '', 'L', 0, 1);

                $pdf->SetFont($trebuchet, '', 9);
                $pdf->SetXY($x, $y += 10);
                $pdf->MultiCell(90, 5, 'Heure de début :', '', 'L', 0, 1);

                //$heure_debut_foration = "09:00";
                $heure_debut_matin = $modelColumn0['info_dates'][0]["start_matin"];
                $pdf->SetFont($trebuchetbd, '', 9);
                $pdf->SetXY($x + 50, $y);
                $pdf->MultiCell(200, 5, $heure_debut_matin, '', 'L', 0, 1);

//tableau des dates

                $nbr_journee = count($modelColumn0['info_dates']);
                if ($nbr_journee != 0) {
                    $pdf->SetXY($x + 10, $y += 10);
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
                        $date_formation = formatDateFr($modelColumn0['info_dates'][$i]["date_start"]);

                        $heure_debut_matin = $modelColumn0['info_dates'][$i]["start_matin"];
                        $heure_fin_matin = $modelColumn0['info_dates'][$i]["end_matin"];
                        $heure_debut_apresmidi = $modelColumn0['info_dates'][$i]["start_apresmidi"];
                        $heure_debut_apremidi = $modelColumn0['info_dates'][$i]["end_apresmidi"];
                        $duree_formation = $modelColumn0['info_dates'][$i]["duree_formation"];
                        //var_dump($date_formation);
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
                }
            }
//fin tableau des date   
            //page de description
            /* uni_cnfsecrm - v2 - modif 156 - DEBUT */
            /* $description_formation = html_entity_decode($modelColumn0['info_product'][0]['programme']);
              if ($description_formation != "") {
              $pdf->AddPage();

              //page 3
              //logo
              list($imageWidth, $imageHeight, $imageType, $imageAttr) = $parent->getimagesize(
              "test/logo/logo-CNFSE-large.png");
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
              $pdf->Image("test/logo/logo-CNFSE-large.png", 10, 2, $w, $h);

              $x = 10;
              $y = 3;

              $ref = "HACCP";
              $pdf->SetFont($trebuchet, '', 6);
              $pdf->SetXY($x + 170, $y);
              $pdf->MultiCell(200, 5, 'REF : ' . $ref, '', 'L', 0, 1);

              $pdf->SetFont($trebuchet, '', 7);
              $pdf->SetXY($x + 150, $y += 5);
              $pdf->MultiCell(200, 5, 'DUREE :', '', 'L', 0, 1);

              $pdf->SetDrawColor(0, 0, 0);
              $pdf->SetFillColor(218, 225, 255);
              $pdf->SetFont($trebuchet, '', 9);
              $pdf->SetXY($x + 170, $y);
              $pdf->MultiCell(20, 5, $nbr_jours_heures . " " . $label_duree, '', 'L', 1, 1);


              $pdf->SetFont($trebuchet, '', 13);
              $pdf->SetXY($x + 35, $y += 10);
              $pdf->SetTextColor(13, 192, 195);
              $pdf->MultiCell(150, 5, $nom_formation, '', 'R', 0, 1);

              $pdf->SetFont($trebuchetbd, '', 8);
              $pdf->SetXY($x, $y += 10);
              $pdf->SetDrawColor(0, 0, 0);
              $pdf->SetFillColor(242, 242, 242);
              $pdf->SetTextColor(0, 0, 0);
              $pdf->MultiCell(190, 5, 'OBJECTIFS', '', 'L', 1, 1);

              $pdf->SetFont($trebuchet, '', 8);
              $pdf->SetXY($x, $y += 10);
              $pdf->SetDrawColor(0, 0, 0);
              $pdf->SetFillColor(255, 255, 255);
              $pdf->SetTextColor(0, 0, 0);
              $description_formation = formatString($modelColumn0['info_product'][0]['programme']);
              $pdf->MultiCell(190, 5, $description_formation, '', 'L', 1, 1);
              }

              //fin page de description
              $pdf->AddPage();

              //page 4
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
              //writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)
              $pdf->SetXY(10, $y += 10);
              $pdf->SetFont($trebuchet, '', 7);
              $pdf->SetFillColor(255, 255, 255);
              $pdf->SetTextColor(102, 102, 102);
              $pdf->writeHTMLCell(95, '', 10, 10, $html1, 0, 0, 1, true, 'J', true);
              $pdf->writeHTMLCell(95, '', '', '', $html2, 0, 1, 1, true, 'J', true); */
            /* uni_cnfsecrm - v2 - modif 156 - FIN */
            /* uni_cnfsecrm - afficher le bloc Récaptitulatif du financement */
            $info_financeur = $modelColumn0['info_financeur'];
            $nbr_financeur = count($info_financeur);
            if ($nbr_financeur != 0) {


                $pdf->AddPage();
                $pdf->SetTextColor(0, 0, 0);
                $x = 0;
                $y = 0;

                $pdf->SetXY($x + 10, $y += 20);
                $pdf->SetFont($trebuchetbd, '', 18);
                $pdf->MultiCell(200, 5, 'Récaptitulatif du financement', 0, 'L', 0, 1);
                $y += 10;


                //echo $nbr_financeur;
                //var_dump($info_financeur);

                for ($i = 0; $i < $nbr_financeur; $i++) {

                    $nom_financeur = $modelColumn0['info_financeur'][$i]['vendorname'];
                    $adresse_financeur = $modelColumn0['info_financeur'][$i]['street'];
                    $ville_financeur = $modelColumn0['info_financeur'][$i]['city'];
                    $cp_financeur = $modelColumn0['info_financeur'][$i]['postalcode'];
                    $montant_financeur = $modelColumn0['info_financeur'][$i]['montant'];
                    $tva_financeur = $modelColumn0['info_financeur'][$i]['tva'];
                    $ttc_financeur = $modelColumn0['info_financeur'][$i]['ttc'];

                    $contentHeight = $pdf->GetStringHeight($nom_financeur, 30);
                    while ($contentHeight > 26) {
                        $nom_financeur = substr($nom_financeur, 0, -1);
                        $contentHeight = $pdf->GetStringHeight($nom_financeur, 30);
                    }

                    $contentHeight = $pdf->GetStringHeight($adresse_financeur, 30);
                    while ($contentHeight > 26) {
                        $adresse_financeur = substr($adresse_financeur, 0, -1);
                        $contentHeight = $pdf->GetStringHeight($adresse_financeur, 30);
                    }

                    $contentHeight = $pdf->GetStringHeight($ville_financeur, 20);
                    while ($contentHeight > 26) {
                        $ville_financeur = substr($ville_financeur, 0, -1);
                        $contentHeight = $pdf->GetStringHeight($ville_financeur, 20);
                    }

                    $pdf->SetDrawColor(212, 224, 238);
                    $pdf->SetFillColor(212, 226, 238);

                    $pdf->SetFont($trebuchet, '', 8);

                    $pdf->SetXY(10, $y);
                    $pdf->MultiCell(40, 5, $nom_financeur, 0, 'C', 1, 1);

                    $pdf->SetXY(51, $y);
                    $pdf->MultiCell(50, 5, $adresse_financeur, 0, 'C', 1, 1);

                    $pdf->SetXY(102, $y);
                    $pdf->MultiCell(10, 5, $cp_financeur, 0, 'C', 1, 1);

                    $pdf->SetXY(113, $y);
                    $pdf->MultiCell(30, 5, $ville_financeur, 0, 'C', 1, 1);

                    $pdf->SetXY(144, $y);
                    $pdf->MultiCell(20, 5, formatPrice($montant_financeur) . ' €', 0, 'C', 1, 1);

                    $pdf->SetXY(165, $y);
                    $pdf->MultiCell(20, 5, formatPrice($tva_financeur) . ' €', 0, 'C', 1, 1);

                    $pdf->SetXY(186, $y);
                    $pdf->MultiCell(20, 5, formatPrice($ttc_financeur) . ' €', 0, 'C', 1, 1);

                    $y += 6;
                    $montant_financeur_total += $montant_financeur;
                    $tva_financeur_total += $tva_financeur;
                    $ttc_financeur_total += $ttc_financeur;
                }
                // 2 eme ligne de tableau
                $pdf->SetDrawColor(127, 157, 185);
                $pdf->SetFillColor(127, 157, 185);

                $pdf->SetXY(10, $y);
                $pdf->SetFont($trebuchetbd, '', 8);
                $pdf->MultiCell(40, 5, 'Totaux ', 0, 'C', 1, 1);

                $pdf->SetXY(51, $y);
                $pdf->MultiCell(92, 5, ' ', 0, 'C', 1, 1);

                $pdf->SetDrawColor(212, 224, 238);
                $pdf->SetFillColor(212, 226, 238);
                $pdf->SetXY(144, $y);
                $pdf->MultiCell(20, 5, formatPrice($montant_financeur_total) . ' €', 0, 'C', 1, 1);

                $pdf->SetXY(165, $y);
                $pdf->MultiCell(20, 5, formatPrice($tva_financeur_total) . ' €', 0, 'C', 1, 1);

                $pdf->SetXY(186, $y);
                $pdf->MultiCell(20, 5, formatPrice($ttc_financeur_total) . ' €', 0, 'C', 1, 1);

                //3 eme ligne de tableau
                $y += 6;

                $pdf->SetDrawColor(127, 157, 185);
                $pdf->SetFillColor(127, 157, 185);

                $pdf->SetXY(105, $y);
                $pdf->MultiCell(38, 5, 'Restant à charge', 0, 'C', 1, 1);

                $pdf->SetDrawColor(212, 224, 238);
                $pdf->SetFillColor(212, 226, 238);

                $pdf->SetXY(144, $y);
                $pdf->MultiCell(20, 5, $total_ht_financement . ' €', 0, 'C', 1, 1);

                $pdf->SetXY(165, $y);
                $pdf->MultiCell(20, 5, $tva_without_financement . ' €', 0, 'C', 1, 1);

                $pdf->SetXY(186, $y);
                $pdf->MultiCell(20, 5, $total_ttc_without_financement . ' €', 0, 'C', 1, 1);
            }
            /* uni_cnfsecrm - v2 - modif 162 - FIN */
        }
    }

}
