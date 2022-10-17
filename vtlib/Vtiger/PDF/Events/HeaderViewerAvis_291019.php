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

class Vtiger_PDF_EventsAvisHeaderViewer extends Vtiger_PDF_HeaderViewer {

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
//            $pdf->setPageFormat('A4', 'L');
//            $pdf->setPageOrientation('LANDSCAPE', '', 0);

            $modelColumns = $this->model->get('columns');
            // Column 1
            $offsetX = 5;
            $modelColumn0 = $modelColumns[0];
            //var_dump($modelColumn0);
            //$headerColumnWidth = $headerFrame->w / 3.0;
            //$modelColumns = $this->model->get('columns');
            // Column 1
            //page 1 
            // font calibri simple

            $calibri = TCPDF_FONTS::addTTFfont('test/font/Calibri/calibri.ttf', 'TrueTypeUnicode', '', 96);
            //font calibri bold
            $calibrib = TCPDF_FONTS::addTTFfont('test/font/Calibri/calibrib.ttf', 'TrueTypeUnicode', '', 96);
            //font calibri italic
            $calibrii = TCPDF_FONTS::addTTFfont('test/font/Calibri/calibrii.ttf', 'TrueTypeUnicode', '', 96);
            //font calibri bold/italic
            $calibriz = TCPDF_FONTS::addTTFfont('test/font/Calibri/calibriz.ttf', 'TrueTypeUnicode', '', 96);

            $nom_formation = html_entity_decode($modelColumn0['nom_formation']);
            $categorie_formation = html_entity_decode($modelColumn0['categorie_formation']);

            $array_coche = array(215, 215, 214);
            $array_noncoche = array(251, 141, 0);
            $array_coche_fill_color = "0, 0, 0";
            $array_noncoche_fill_color = "255, 255, 255";
            $be_essai = $modelColumn0['info_apprenants'][0]["be_essai"];
            $be_mesurage = $modelColumn0['info_apprenants'][0]["be_mesurage"];
            $be_verification = $modelColumn0['info_apprenants'][0]["be_verification"];
            $be_manoeuvre = $modelColumn0['info_apprenants'][0]["be_manoeuvre"];
            $he_essai = $modelColumn0['info_apprenants'][0]["he_essai"];
            $he_mesurage = $modelColumn0['info_apprenants'][0]["he_mesurage"];
            $he_verification = $modelColumn0['info_apprenants'][0]["he_verification"];
            $he_manoeuvre = $modelColumn0['info_apprenants'][0]["he_manoeuvre"];
            $initiale = $modelColumn0['info_apprenants'][0]["initiale"];
            $recyclage = $modelColumn0['info_apprenants'][0]["recyclage"];
            if ($recyclage == 0)
                $initiale = 1;

            $testprerequis = $modelColumn0['info_apprenants'][0]["testprerequis"];
            if ($testprerequis == "")
                $testprerequis = 0;
            $electricien = $modelColumn0['info_apprenants'][0]["electricien"];
            if ($electricien == "")
                $electricien = 0;
            /* savoir electricien ou non selon nom de la formation */
            if (strstr($nom_formation, "B1") || strstr($nom_formation, "B2") || strstr($nom_formation, "H1") || strstr($nom_formation, "H2") || strstr($nom_formation, "BR") || strstr($nom_formation, "BC") || strstr($nom_formation, "HC")) {
                $type_formation_elect = 1;
            } else {
                $type_formation_elect = 0;
            }

            $nom_formation_value = $nom_formation;
            $nom_formation_value .= ($be_essai == "1") ? " BE essai" : "";
            $nom_formation_value .= ($be_mesurage == "1") ? " BE mesurage" : "";
            $nom_formation_value .= ($be_verification == "1") ? " BE vérification" : "";
            $nom_formation_value .= ($be_manoeuvre == "1") ? " BE manoeuvre" : "";
            $nom_formation_value .= ($he_essai == "1") ? " HE essai" : "";
            $nom_formation_value .= ($he_mesurage == "1") ? " HE mesurage" : "";
            $nom_formation_value .= ($he_verification == "1") ? " HE vérification" : "";
            $nom_formation_value .= ($he_manoeuvre == "1") ? " HE manoeuvre" : "";

            $resultat = $modelColumn0['info_apprenants'][0]["resultat"];

            $array_initiale = array();
            $array_initiale = ($initiale == "1") ? $array_coche : $array_noncoche;
            $array_initiale_fill_color = ($initiale == "1") ? $array_coche_fill_color : $array_noncoche_fill_color;
            $array_recyclage = array();
            $array_recyclage = ($recyclage == "1") ? $array_coche : $array_noncoche;
            $array_recyclage_fill_color = ($recyclage == "1") ? $array_coche_fill_color : $array_noncoche_fill_color;

            $array_avisfavorable = array();
            $array_avisfavorable = ($resultat == "avis_favorable") ? $array_coche : $array_noncoche;

            $array_avisdefavorable = array();
            $array_avisdefavorable = ($resultat == "avis_defavorable") ? $array_coche : $array_noncoche;

            $array_avisautre = array();
            $array_avisautre = ($resultat == "autre") ? $array_coche : $array_noncoche;

            switch ($resultat) {
                case "avis_favorable":
                    $resultat_value = "réussi";
                    break;
                case "avis_defavorable":
                    $resultat_value = "";
                    break;
                case "autre":
                    $resultat_value = "";
                    break;
                default:
                    $resultat_value = "réussi";
                    break;
            }

            $civilite = $modelColumn0['info_apprenants'][0]["salutation"];
            $date_debut = $modelColumn0["date_debut_formation"];
            $date_fin = $modelColumn0["date_fin_formations"];
            $prenom = $modelColumn0['info_apprenants'][0]["firstname"];
            $nom = $modelColumn0['info_apprenants'][0]["lastname"];
            $monfichier = fopen('debug_email.txt', 'a+');
            fputs($monfichier, "\n" . "prenom " . $prenom);
            fputs($monfichier, "\n" . "nom " . $nom);
            fclose($monfichier);
            $ticket_examen = $modelColumn0['info_apprenants'][0]["ticket_examen"];
            $date_reussi = "1er janvier 2017";
            $date = $modelColumn0['date_creation']['date_creation'];
            if ($date != null) {
                $date = formatDateFr($date);
            }

            $date = Date("d/m/Y");
            $ville = $modelColumn0['ville'];
            $duree = $modelColumn0['duree'];
            $subject = $modelColumn0["subject"];
            $type_formation = $modelColumn0["categorie_formation"];
            //variable categorie 2

            $logo = $pdf->Image('http://94.23.214.76/cnfsecrm/test/upload/images/logo.png', 75, 10, 50, 20);
            $article = '<p style="text-align:center">(application de l\'article R. 554-31 du code de l\'environnement <br/> et des articles 21 et 22 de son arrêté d\'application du 15 février 2012 modifié) </p>';
            $question = '<strong>Domaine de compétence couvert par l\'attestation :</strong>';
            $soussigne = 'Je, soussigné Monsieur Frédéric LAMBERT, responsable pédagogique <br/><br/>Atteste que <strong>' . $civilite . ' ' . $prenom . ' ' . $nom . ' </strong>a suivi l\'examen tenu le ' . $date_debut . '<br/> relatif au domaine de compétences susmentionné, <br/>sous le n° de ticket d\'examen [' . $ticket_examen . '] <br/><span style="background-color:#FFF000;"><strong>a ' . $resultat_value . ' cet examen</strong></span>.';
            $attestation = '<strong>La présente attestation est valable pour une durée de 5 ans à compter de la <br/> date de réussite à l\'examen mentionnée ci-dessus, ou du ' . $date_reussi . ' si la <br/> date de réussite à l\'examen est antérieure au ' . $date_reussi . '.<br/><br/> Elle permet la délivrance par l\'employeur d\'une autorisation d\'intervention à <br/> proximité des réseaux (AIPR), dont le délai de validité ne peut dépasser celui<br/> de la présente attestation.</strong>';
            $nota = 'Nota : la présente attestation n\'a pas de valeur pour l\'application d\'autres réglementations que celle mentionnée dans le<br/> titre ; elle ne dispense pas non plus des autorisations nécessaires le cas échéant pour l\'accès aux ouvrages des exploitants.';
            $fait = 'Fait à ' . $ville . ', le ' . $date . '';
            $signature = '<img src="test/Signature/Signature.jpg" height="60" width="110">';
            $footer = '<p style="text-align:center;color:#808080">Centre National de Formation en Sécurité et Environnement<br/> 231 Rue Saint Honoré 75001 Paris - RCS : Paris 482.379.302– APE 8559A <br/> Tél : 01.84.16.38.25 - http://habilitations-electrique.fr - e-mail : contact@cnfse.fr </p>';

            if ($type_formation == "HABILITATIONS") {
                //variable categorie 1
                $text = '<p><strong>' . $civilite . ' ' . $prenom . ' ' . $nom . ' </strong>a suivi la formation habilitation électrique du  <strong>' . $date_debut . '</strong> au<br/> <strong>' . $date_fin . '</strong>, pour une durée de <strong>' . $duree . '</strong> jour, au sein de notre organisme de formation <br/> Formation au vue d\'une <strong> ' . $nom_formation_value . ' </strong> <br/> Au cours de cette formation<strong> ' . $civilite . ' ' . $prenom . ' ' . $nom . ' </strong>a acquis les connaissances et le<br/> savoir-faire nécessaires pour prendre en compte le risque électrique dans le cadre<br/> d\'opérations d\'ordre électriques ou non électrique et se prémunir de tout accident susceptible <br/> d\'être encouru.</p>';
                $text2 = 'Au vu de cet avis et compte tenu des prescriptions de la norme NF C 18-510 l\'employeur peut<br/> délivrer à<strong> ' . $civilite . ' ' . $prenom . ' ' . $nom . '</strong> l\'habilitation mentionnée ci-dessous';
                //  echo "e".$electricien."t".$type_formation."r".$testprerequis."<br/>";
                if ($type_formation_elect == 1 && $electricien == 1 && $type_formation == "HABILITATIONS" && $testprerequis == "1") {
                    //   echo "t0";
                    $text3 = 'Test de prérequis réussi conformément à la NFC 18-510.';
                } elseif ($type_formation_elect == 1 && $electricien == 1 && $type_formation == "HABILITATIONS" && $testprerequis == "0") {
                    //   echo "t1";
                    $text3 = 'Au vue des résultats du test de prérequis effectué en début de la formation, nous préconisons une mise à jour "électricité professionnelle"  § 4.5.2 et annexe NFC 18-510.';
                } elseif ($type_formation_elect == 1 && $electricien == 0 && $type_formation == "HABILITATIONS" && $testprerequis == "1") {
                    //   echo "t1";
                    $text3 = 'Nous préconisons conformément à la NF C 18-510 § 4.5.2 une formation "électricité professionnelle".';
                } elseif ($type_formation_elect == 1 && $electricien == 0 && $type_formation == "HABILITATIONS" && $testprerequis == "0") {
                    //   echo "t1";
                    $text3 = 'Au vue des résultats du test de prérequis effectué en début de la formation, nous préconisons une mise à jour "électricité professionnelle"  § 4.5.2 et annexe NFC 18-510.';
                } else {
                    $text3 = '';
                    //  echo "t2";
                }

                $titre = '<p style="text-align:center">AVIS APRES FORMATION <br/> HABILITATION ELECTRIQUE</p>';
                $l1 = 45;
                $l2 = 70;
                $l3 = 80;
                $l4 = 122;
                $l5 = 130;
                $l9 = 142;
                $l6 = 155;
                $l7 = 250;
                $l8 = 275;
                $array_a1 = array();
                $array_a2 = array();
                if (strstr($nom_formation, "B0")) {
                    array_push($array_a1, "B0");
                    array_push($array_a2, "BT");
                }
                if (strstr($nom_formation, "H0")) {
                    array_push($array_a1, "H0");
                    array_push($array_a2, "HTA");
                }
                if (strstr($nom_formation, "H0V")) {
                    array_push($array_a1, "H0V");
                    if (!in_array("HTA", $array_a2))
                        array_push($array_a2, "HTA");
                }
                $a1 = implode("-", $array_a1);
                $a2 = implode("-", $array_a2);
                $a3 = 'Accès Toutes installations électriques';
                $a4 = '';

                $array_b1 = array();
                $array_b2 = array();
                if (strstr($nom_formation, "B0")) {
                    array_push($array_b1, "B0");
                    array_push($array_b2, "BT");
                }
                if (strstr($nom_formation, "H0")) {
                    array_push($array_b1, "H0");
                    array_push($array_b2, "HTA");
                }
                if (strstr($nom_formation, "H0V")) {
                    array_push($array_b1, "H0V");
                    if (!in_array("HTA", $array_b2))
                        array_push($array_b2, "HTA");
                }
                $b1 = implode("-", $array_b1);
                $b2 = implode("-", $array_b2);
                $b3 = 'Accès Toutes installations électriques';
                $b4 = '';

                $array_c1 = array();
                $array_c2 = array();
                $array_liste_c_b = array("B1V");
                $array_liste_c_h = array("H1V");
                foreach ($array_liste_c_b as $key => $value) {
                    if (strstr($nom_formation, $value)) {
                        array_push($array_c1, $value);
                        if (!in_array("BT", $array_c2))
                            array_push($array_c2, "BT");
                    }
                }

                foreach ($array_liste_c_h as $key => $value) {
                    if (strstr($nom_formation, $value)) {
                        array_push($array_c1, $value);
                        if (!in_array("HTA", $array_c2))
                            array_push($array_c2, "HTA");
                    }
                }

                $c1 = implode("-", $array_c1);
                $c2 = implode("-", $array_c2);
                $c3 = (count($array_c1) > 0) ? "Tout ouvrage ou installation électrique" : "";
                $c4 = '';

                $array_d1 = array();
                $array_d2 = array();
                $array_liste_c_b = array("B2V");
                $array_liste_c_h = array("H2V");
                foreach ($array_liste_c_b as $key => $value) {
                    if (strstr($nom_formation, $value)) {
                        array_push($array_d1, $value);
                        if (!in_array("BT", $array_d2))
                            array_push($array_d2, "BT");
                    }
                }

                foreach ($array_liste_c_h as $key => $value) {
                    if (strstr($nom_formation, $value)) {
                        array_push($array_d1, $value);
                        if (!in_array("HTA", $array_d2))
                            array_push($array_d2, "HTA");
                    }
                }

                $d1 = implode("-", $array_d1);
                $d2 = implode("-", $array_d2);
                $d3 = (count($array_d1) > 0) ? "Tout ouvrage ou installation électrique" : "";
                $d4 = '';

                $array_e1 = array();
                $array_e2 = array();
                $array_liste_c_b = array("BR");
                foreach ($array_liste_c_b as $key => $value) {
                    if (strstr($nom_formation, $value)) {
                        array_push($array_e1, $value);
                        if (!in_array("BT", $array_e2))
                            array_push($array_e2, "BT");
                    }
                }

                $e1 = implode("-", $array_e1);
                $e2 = implode("-", $array_e2);
                $e3 = (count($array_e1) > 0) ? "Tout ouvrage ou installation électrique" : "";
                $e4 = '';

                $array_f1 = array();
                $array_f2 = array();
                $array_liste_c_b = array("BC");
                $array_liste_c_h = array("HC");
                foreach ($array_liste_c_b as $key => $value) {
                    if (strstr($nom_formation, $value)) {
                        array_push($array_f1, $value);
                        if (!in_array("BT", $array_f2))
                            array_push($array_f2, "BT");
                    }
                }

                foreach ($array_liste_c_h as $key => $value) {
                    if (strstr($nom_formation, $value)) {
                        array_push($array_f1, $value);
                        if (!in_array("HTA", $array_f2))
                            array_push($array_f2, "HTA");
                    }
                }

                $f1 = implode("-", $array_f1);
                $f2 = implode("-", $array_f2);
                $f3 = (count($array_f1) > 0) ? "Tout ouvrage ou installation électrique" : "";
                $f4 = '';

                $array_g1 = array();
                $array_g2 = array();
                $array_liste_c_b = array("BE essai", "BE mesurage", "BE vérification", "BE manoeuvre");
                $array_liste_c_h = array("HE essai", "HE mesurage", "HE vérification", "HE manoeuvre");

                foreach ($array_liste_c_b as $key => $value) {
                    if (strstr($nom_formation_value, $value)) {
                        array_push($array_g1, $value);
                        if (!in_array("BT", $array_g2))
                            array_push($array_g2, "BT");
                    }
                }

                foreach ($array_liste_c_h as $key => $value) {
                    if (strstr($nom_formation_value, $value)) {
                        array_push($array_g1, $value);
                        if (!in_array("HTA", $array_g2))
                            array_push($array_g2, "HTA");
                    }
                }
                $g1 = implode(" / ", $array_g1);
                $g2 = implode("-", $array_g2);
                $g3 = (count($array_g1) > 0) ? "Opérations sur appareillage électrique" : "";
                $g4 = '';

                $h1 = '';
                $h2 = '';
                $h3 = '';
                $h4 = '';

                // Logo 
                $x = 0;
                $y = 0;
                $pdf->SetXY($x, $y);
                $logo;
                // titre
                $pdf->SetFont($calibri, '', 22);
                $pdf->writeHTMLCell(200, 100, $x, $y + $l1, $titre, '');

                $pdf->SetFont($calibrib, '', 13);
                $pdf->writeHTMLCell(200, 100, $x + 30, $y + $l2, 'Formation initiale', '');
                //$pdf->writeHTMLCell(7, 5, $x + 70, $y + $l2, '', 1);
                $pdf->SetFont($calibri, '', 5);
                $pdf->SetXY($x + 72, $y + $l2 + 2);
                if ($initiale == "1")
                    $pdf->SetFillColor(0, 0, 0);
                else
                    $pdf->SetFillColor(255, 255, 255);
                $pdf->SetLineStyle(array('width' => 1.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $array_initiale));
                $pdf->MultiCell(5, 5, '', 1, 'C', 1, 1);

                $pdf->SetFont($calibrib, '', 13);
                $pdf->writeHTMLCell(200, 100, $x + 110, $y + $l2, 'Formation de Recyclage', '');
                //$pdf->writeHTMLCell(7, 5, $x + 165, $y + $l2, '', 1);
                $pdf->SetFont($calibri, '', 5);
                $pdf->SetXY($x + 165, $y + $l2 + 2);
                if ($recyclage == "1")
                    $pdf->SetFillColor(0, 0, 0);
                else
                    $pdf->SetFillColor(255, 255, 255);
                $pdf->SetLineStyle(array('width' => 1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $array_recyclage));
                $pdf->MultiCell(5, 5, '', 1, 'C', 1, 1);

                //test 1
                $pdf->SetFont($calibri, '', 11);
                $pdf->writeHTMLCell(180, 100, $x + 20, $y + $l3, $text, '');

                $pdf->SetFont($calibri, '', 12);
                $pdf->writeHTMLCell(200, 100, $x + 20, $y + $l4, 'Avis favorable', '');
                $pdf->SetFont($calibri, '', 5);
                $pdf->SetXY($x + 50, $y + $l4 + 2);
                if ($resultat == "avis_favorable")
                    $pdf->SetFillColor(0, 0, 0);
                else
                    $pdf->SetFillColor(255, 255, 255);
                $pdf->SetLineStyle(array('width' => 1.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $array_avisfavorable));
                $pdf->MultiCell(5, 5, '', 1, 'C', 1, 1);

                $pdf->SetFont($calibri, '', 12);
                $pdf->writeHTMLCell(200, 100, $x + 70, $y + $l4, 'Avis Défavorable', '');
                $pdf->SetFont($calibri, '', 5);
                $pdf->SetXY($x + 105, $y + $l4 + 2);
                if ($resultat == "avis_defavorable")
                    $pdf->SetFillColor(0, 0, 0);
                else
                    $pdf->SetFillColor(255, 255, 255);
                $pdf->SetLineStyle(array('width' => 1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $array_avisdefavorable));
                $pdf->MultiCell(5, 5, '', 1, 'C', 1, 1);

                $pdf->SetFont($calibri, '', 12);
                $pdf->writeHTMLCell(200, 100, $x + 130, $y + $l4, 'Autre proposition', '');
                $pdf->SetFont($calibri, '', 5);
                $pdf->SetXY($x + 165, $y + $l4 + 2);
                if ($resultat == "autre")
                    $pdf->SetFillColor(0, 0, 0);
                else
                    $pdf->SetFillColor(255, 255, 255);
                $pdf->SetLineStyle(array('width' => 1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $array_avisautre));
                $pdf->MultiCell(5, 5, '', 1, 'C', 1, 1);

                $pdf->SetFillColor(255, 255, 255);
                $pdf->SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));

                $pdf->SetFont($calibri, '', 12);
                $pdf->writeHTMLCell(200, 100, $x + 20, $y + $l5, $text2, '');

                $pdf->SetFont($calibri, '', 12);
                $pdf->writeHTMLCell(180, 100, $x + 20, $y + $l9, $text3, '');
            }

            if ($nom_formation == "AIPR CONCEPTEUR" || $nom_formation == "AIPR Concepteur") {
                $titre = '<p style="text-align:center">Attestation de compétences relative à <br/> l\'intervention à proximité des réseaux</p>';
                $rep1 = '<strong>Préparation et conduite de projet (Concepteur)</strong>';
                $rep2 = '<del>Encadrement de chantiers de travaux (Encadrant)</del>';
                $rep3 = '<del><strong>Conduite d\'engins</strong> ou <strong>Réalisation de travaux urgents (Opérateur)</strong></del>';
                $l1 = 45;
                $l2 = 65;
                $l3 = 85;
                $l4 = 165;
                $l5 = 210;
                $l6 = 230;
                $l7 = 275;

                //Logo
                $pdf->SetXY($x, $y);
                $logo;
                //titre
                $pdf->SetXY($x, $y + $l1);
                $pdf->SetFont($calibri, '', 24);
                $pdf->writeHTML($titre, true, false, true, false, '');
                //article
                $pdf->SetXY($x, $y + $l2);
                $pdf->SetFont($calibri, '', 12);
                $pdf->writeHTML($article, true, false, true, false, '');
                //list
                $pdf->SetFont($calibrib, '', 13);
                $pdf->writeHTMLCell(200, 100, $x + 20, $y + $l3, $question, '');

                $pdf->SetFont($calibri, '', 5);
                $pdf->SetXY($x + 22, $y + $l3 + 10);
                $pdf->SetFillColor(255, 255, 255);
                $pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
                $pdf->MultiCell(5, 5, '', 1, 'C', 1, 1);

                $pdf->SetFont($calibrib, '', 13);
                $pdf->writeHTMLCell(200, 100, $x + 27, $y + $l3 + 10, $rep1, '');


                $pdf->SetFont($calibri, '', 5);
                $pdf->SetXY($x + 22, $y + $l3 + 20);
                $pdf->SetFillColor(255, 255, 255);
                $pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
                $pdf->MultiCell(5, 5, '', 1, 'C', 1, 1);

                $pdf->SetFont($calibrib, '', 13);
                $pdf->writeHTMLCell(200, 100, $x + 27, $y + $l3 + 20, $rep2, '');

                $pdf->SetFont($calibri, '', 5);
                $pdf->SetXY($x + 22, $y + $l3 + 30);
                $pdf->SetFillColor(255, 255, 255);
                $pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
                $pdf->MultiCell(5, 5, '', 1, 'C', 1, 1);

                $pdf->SetFont($calibri, '', 13);
                $pdf->writeHTMLCell(200, 100, $x + 27, $y + $l3 + 30, $rep3, '');

                $pdf->SetFont($calibri, '', 14);
                $pdf->writeHTMLCell(200, 100, $x + 20, $y + $l3 + 40, $soussigne, '');

                $pdf->SetFont($calibri, '', 14);
                $pdf->writeHTMLCell(200, 100, $x + 20, $y + $l4, $attestation, '');

                $pdf->SetFont($calibri, '', 10);
                $pdf->writeHTMLCell(200, 100, $x + 20, $y + $l5, $nota, '');

                $pdf->SetFont($calibri, '', 15);
                $pdf->writeHTMLCell(200, 100, $x + 20, $y + $l6, $fait, '');

                $pdf->writeHTMLCell(200, 100, $x + 130, $y + $l6, $signature, '');

                $pdf->SetFont($calibri, '', 9);
                $pdf->writeHTMLCell(200, 100, $x, $y + $l7, $footer, '');
            } elseif ($nom_formation == "AIPR ENDADRANT" || $nom_formation == "AIPR Encadrant") {
                $titre = '<p style="text-align:center">Attestation de compétences relative à <br/> l\'intervention à proximité des réseaux</p>';
                $rep1 = '<del><strong>Préparation et conduite de projet (Concepteur)</strong></del>';
                $rep2 = 'Encadrement de chantiers de travaux (Encadrant)';
                $rep3 = '<del><strong>Conduite d\'engins</strong> ou <strong>Réalisation de travaux urgents (Opérateur)</strong></del>';
                $l1 = 45;
                $l2 = 65;
                $l3 = 85;
                $l4 = 165;
                $l5 = 210;
                $l6 = 230;
                $l7 = 275;

                //Logo
                $pdf->SetXY($x, $y);
                $logo;
                //titre
                $pdf->SetXY($x, $y + $l1);
                $pdf->SetFont($calibri, '', 24);
                $pdf->writeHTML($titre, true, false, true, false, '');
                //article
                $pdf->SetXY($x, $y + $l2);
                $pdf->SetFont($calibri, '', 12);
                $pdf->writeHTML($article, true, false, true, false, '');
                //list
                $pdf->SetFont($calibrib, '', 13);
                $pdf->writeHTMLCell(200, 100, $x + 20, $y + $l3, $question, '');

                $pdf->SetFont($calibri, '', 5);
                $pdf->SetXY($x + 22, $y + $l3 + 10);
                $pdf->SetFillColor(255, 255, 255);
                $pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
                $pdf->MultiCell(5, 5, '', 1, 'C', 1, 1);

                $pdf->SetFont($calibrib, '', 13);
                $pdf->writeHTMLCell(200, 100, $x + 27, $y + $l3 + 10, $rep1, '');


                $pdf->SetFont($calibri, '', 5);
                $pdf->SetXY($x + 22, $y + $l3 + 20);
                $pdf->SetFillColor(255, 255, 255);
                $pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
                $pdf->MultiCell(5, 5, '', 1, 'C', 1, 1);

                $pdf->SetFont($calibrib, '', 13);
                $pdf->writeHTMLCell(200, 100, $x + 27, $y + $l3 + 20, $rep2, '');

                $pdf->SetFont($calibri, '', 5);
                $pdf->SetXY($x + 22, $y + $l3 + 30);
                $pdf->SetFillColor(255, 255, 255);
                $pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
                $pdf->MultiCell(5, 5, '', 1, 'C', 1, 1);

                $pdf->SetFont($calibri, '', 13);
                //$pdf->writeHTML($rep3, true, false, true, false, '');
                $pdf->writeHTMLCell(200, 100, $x + 27, $y + $l3 + 30, $rep3, '');

                $pdf->SetFont($calibri, '', 14);
                $pdf->writeHTMLCell(200, 100, $x + 20, $y + $l3 + 40, $soussigne, '');

                $pdf->SetFont($calibri, '', 14);
                $pdf->writeHTMLCell(200, 100, $x + 20, $y + $l4, $attestation, '');

                $pdf->SetFont($calibri, '', 10);
                $pdf->writeHTMLCell(200, 100, $x + 20, $y + $l5, $nota, '');

                $pdf->SetFont($calibri, '', 15);
                $pdf->writeHTMLCell(200, 100, $x + 20, $y + $l6, $fait, '');

                $pdf->writeHTMLCell(200, 100, $x + 130, $y + $l6, $signature, '');

                $pdf->SetFont($calibri, '', 9);
                $pdf->writeHTMLCell(200, 100, $x, $y + $l7, $footer, '');
            } elseif ($nom_formation == "AIPR OPERATEUR" || $nom_formation == "AIPR Opérateur") {

                $titre = '<p style="text-align:center">Attestation de compétences relative à <br/> l\'intervention à proximité des réseaux</p>';
                $rep1 = '<del><strong>Préparation et conduite de projet (Concepteur)</strong></del>';
                $rep2 = '<del>Encadrement de chantiers de travaux (Encadrant)</del>';
                $rep3 = '<strong>Conduite d\'engins</strong> ou <strong>Réalisation de travaux urgents (Opérateur)</strong>';
                $l1 = 45;
                $l2 = 65;
                $l3 = 85;
                $l4 = 165;
                $l5 = 210;
                $l6 = 230;
                $l7 = 275;

                //Logo
                $pdf->SetXY($x, $y);
                $logo;
                //titre
                $pdf->SetXY($x, $y + $l1);
                $pdf->SetFont($calibri, '', 24);
                $pdf->writeHTML($titre, true, false, true, false, '');
                //article
                $pdf->SetXY($x, $y + $l2);
                $pdf->SetFont($calibri, '', 12);
                $pdf->writeHTML($article, true, false, true, false, '');
                //list
                $pdf->SetFont($calibrib, 'B', 13);
                $pdf->writeHTMLCell(200, 100, $x + 20, $y + $l3, $question, '');

                $pdf->SetFont($calibri, '', 5);
                $pdf->SetXY($x + 22, $y + $l3 + 10);
                $pdf->SetFillColor(255, 255, 255);
                $pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
                $pdf->MultiCell(5, 5, '', 1, 'C', 1, 1);

                $pdf->SetFont($calibrib, '', 13);
                $pdf->writeHTMLCell(200, 100, $x + 27, $y + $l3 + 10, $rep1, '');


                $pdf->SetFont($calibri, '', 5);
                $pdf->SetXY($x + 22, $y + $l3 + 20);
                $pdf->SetFillColor(255, 255, 255);
                $pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
                $pdf->MultiCell(5, 5, '', 1, 'C', 1, 1);

                $pdf->SetFont($calibrib, '', 13);
                $pdf->writeHTMLCell(200, 100, $x + 27, $y + $l3 + 20, $rep2, '');

                $pdf->SetFont($calibri, '', 5);
                $pdf->SetXY($x + 22, $y + $l3 + 30);
                $pdf->SetFillColor(255, 255, 255);
                $pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
                $pdf->MultiCell(5, 5, '', 1, 'C', 1, 1);

                $pdf->SetFont($calibri, '', 13);
                //$pdf->writeHTML($rep3, true, false, true, false, '');
                $pdf->writeHTMLCell(200, 100, $x + 27, $y + $l3 + 30, $rep3, '');

                $pdf->SetFont($calibri, '', 14);
                $pdf->writeHTMLCell(200, 100, $x + 20, $y + $l3 + 40, $soussigne, '');

                $pdf->SetFont($calibri, '', 14);
                $pdf->writeHTMLCell(200, 100, $x + 20, $y + $l4, $attestation, '');

                $pdf->SetFont($calibri, '', 10);
                $pdf->writeHTMLCell(200, 100, $x + 20, $y + $l5, $nota, '');

                $pdf->SetFont($calibri, '', 15);
                $pdf->writeHTMLCell(200, 100, $x + 20, $y + $l6, $fait, '');

                $pdf->writeHTMLCell(200, 100, $x + 130, $y + $l6, $signature, '');

                $pdf->SetFont($calibri, '', 9);
                $pdf->writeHTMLCell(200, 100, $x, $y + $l7, $footer, '');
            }

            if ($type_formation == "HABILITATIONS") {

                $tableau = '<table style="width:450px" border="1">
                                <tr valign="middle" align="center" style="background-color:#CBCBCB;">
                                    <td style="height:10px;vertical-align: middle" colspan="2">INDICE D\'HABILITATION</td>
                                    <td style="height:10px;" rowspan="2">DOMAINE(S) DE TENSION</td>
                                    <td style="height:10px;" rowspan="2">OUVRAGE(S) CONCERNE(S)</td>
                                    <td style="height:10px;" rowspan="2">INDICATIONS SUPPLEMENTAIRES</td>
                                </tr>
                                <tr style="text-align:center;background-color:#CBCBCB;">
                                    <td style="height:10px;">PERSONNEL</td>
                                    <td style="height:10px;">SYMBOLE</td>
                                </tr>
                                <tr style="text-align:center;background-color:#CBCBCB;">
                                    <td style="height:10px;" colspan="5">OPERATIONS D\'ORDRE NON ELECTRIQUE</td>
                                </tr>
                                <tr style="text-align:center">
                                    <td style="height:20px;">Exécutant</td>
                                    <td style="height:20px;">' . $a1 . '</td>
                                    <td style="height:20px;">' . $a2 . '</td>
                                    <td style="height:20px;">' . $a3 . '</td>
                                    <td style="height:20px;">' . $a4 . '</td>
                                </tr>
                                <tr style="text-align:center">
                                    <td style="height:20px;">Chargé de Chantier</td>
                                    <td style="height:20px;">' . $b1 . '</td>
                                    <td style="height:20px;">' . $b2 . '</td>
                                    <td style="height:20px;">' . $b3 . '</td>
                                    <td style="height:20px;">' . $b4 . '</td>
                                </tr>
                                <tr style="text-align:center;background-color:#CBCBCB;">
                                    <td style="height:10px;" colspan="5">OPERATIONS D\'ORDRE ELECTRIQUE</td>
                                </tr>
                                <tr style="text-align:center">
                                    <td style="height:20px;">Exécutant</td>
                                    <td style="height:20px;">' . $c1 . '</td>
                                    <td style="height:20px;">' . $c2 . '</td>
                                    <td style="height:20px;">' . $c3 . '</td>
                                    <td style="height:20px;">' . $c4 . '</td>
                                </tr>
                                <tr style="text-align:center">
                                    <td style="height:20px;">Chargé de travaux</td>
                                    <td style="height:20px;">' . $d1 . '</td>
                                    <td style="height:20px;">' . $d2 . '</td>
                                    <td style="height:20px;">' . $d3 . '</td>
                                    <td style="height:20px;">' . $d4 . '</td>
                                </tr>
                                <tr style="text-align:center">
                                    <td style="height:20px;">Chargé d\'intervention</td>
                                    <td style="height:20px;">' . $e1 . '</td>
                                    <td style="height:20px;">' . $e2 . '</td>
                                    <td style="height:20px;">' . $e3 . '</td>
                                    <td style="height:20px;">' . $e4 . '</td>
                                </tr>
                                <tr style="text-align:center">
                                    <td style="height:20px;">Chargé de consignation</td>
                                    <td style="height:20px;">' . $f1 . '</td>
                                    <td style="height:20px;">' . $f2 . '</td>
                                    <td style="height:20px;">' . $f3 . '</td>
                                    <td style="height:20px;">' . $f4 . '</td>
                                </tr>
                                <tr style="text-align:center">
                                    <td style="height:20px;">Chargé d\'opérations spécifiques</td>
                                    <td style="height:20px;">' . $g1 . '</td>
                                    <td style="height:20px;">' . $g2 . '</td>
                                    <td style="height:20px;">' . $g3 . '</td>
                                    <td style="height:20px;">' . $g4 . '</td>
                                </tr>
                                <tr style="text-align:center">
                                    <td style="height:20px;">Habilité spécial</td>
                                    <td style="height:20px;">' . $h1 . '</td>
                                    <td style="height:20px;">' . $h2 . '</td>
                                    <td style="height:20px;">' . $h3 . '</td>
                                    <td style="height:20px;">' . $h4 . '</td>
                                </tr>
                            </table>';

                $pdf->SetFont($calibri, '', 9);
                $pdf->writeHTMLCell(200, 100, $x + 20, $y + $l6, $tableau, '');
                //signature
                $pdf->writeHTMLCell(200, 100, $x + 150, $y + $l7, $signature, '');
                //footer
                $pdf->writeHTMLCell(200, 100, $x, $y + $l8, $footer, '');
            }
        }
    }

}
