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
/* uni_cnfsecrm - v2 - modif 124 - FILE */

class Vtiger_PDF_EventsExportertoutHeaderViewer extends Vtiger_PDF_HeaderViewer {

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
            $calibri = TCPDF_FONTS2::addTTFfont('test/font/Calibri/calibri.ttf', 'TrueTypeUnicode', '', 96);
            $calibrib = TCPDF_FONTS2::addTTFfont('test/font/Calibri/calibrib.ttf', 'TrueTypeUnicode', '', 96);
            $calibrii = TCPDF_FONTS2::addTTFfont('test/font/Calibri/calibrii.ttf', 'TrueTypeUnicode', '', 96);
            $calibriz = TCPDF_FONTS2::addTTFfont('test/font/Calibri/calibriz.ttf', 'TrueTypeUnicode', '', 96);

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
                    $resultat_value = "échoué";
                    break;
                case "autre":
                    $resultat_value = "échoué";
                    break;
                default:
                    $resultat_value = "réussi";
                    break;
            }
            $civilite = $modelColumn0['info_apprenants'][0]["salutation"];
            /* uni_cnfsecrm - v2 - modif 127 - DEBUT */

            if ($modelColumn0['info_apprenants'][0]["date_start_appr"] == '0000-00-00' || $modelColumn0['info_apprenants'][0]["date_start_appr"] == '' || $modelColumn0['info_apprenants'][0]["date_start_appr"] == '1970-01-01') {
                $date_debut = $modelColumn0["date_debut_formation"];
            } else {
                $date_start_appr = strtotime($modelColumn0['info_apprenants'][0]["date_start_appr"]);
                $date_debut = date('d/m/Y', $date_start_appr);
            }
            if ($modelColumn0['info_apprenants'][0]["date_fin_appr"] == '0000-00-00' || $modelColumn0['info_apprenants'][0]["date_fin_appr"] == '' || $modelColumn0['info_apprenants'][0]["date_fin_appr"] == '1970-01-01') {
                $date_fin = $modelColumn0["date_fin_formations"];
            } else {
                $date_fin_appr = strtotime($modelColumn0['info_apprenants'][0]["date_fin_appr"]);
                $date_fin = date('d/m/Y', $date_fin_appr);
            }
            /* uni_cnfsecrm - v2 - modif 127 - FIN */
            $prenom = $modelColumn0['info_apprenants'][0]["firstname"];
            $nom = $modelColumn0['info_apprenants'][0]["lastname"];
            $accountid = $modelColumn0['info_apprenants'][0]["accountid"];
            $ticket_examen = $modelColumn0['info_apprenants'][0]["ticket_examen"];
            $date_reussi = "1er janvier 2017";
            $date = $modelColumn0['date_creation']['date_creation'];
            if ($date != null) {
                $date = formatDateFr($date);
            }
            $date = Date("d/m/Y");
            $ville = $modelColumn0['ville'];
            $duree = $modelColumn0['duree'];
            /* uni_cnfsecrm - v2 - modif 127 - DEBUT */
            if ($modelColumn0['info_apprenants'][0]["duree_jour"] != 0) {
                $nbr_jours = $modelColumn0['info_apprenants'][0]["duree_jour"];
            } else {
                $nbr_jours = $modelColumn0['nbr_jours'];
            }
            /* uni_cnfsecrm - v2 - modif 127 - FIN */
            $subject = $modelColumn0["subject"];
            $type_formation = $modelColumn0["categorie_formation"];
            //variable categorie 2
            $formateur = $modelColumn0['formateur'];
            $logo = $pdf->Image('test/logo/logo-CNFSE-large.png', 60, 10, 70, 24);
            $article = '<p style="text-align:center">(application de l\'article R. 554-31 du code de l\'environnement <br/> et des articles 21 et 22 de son arrêté d\'application du 15 février 2012 modifié) </p>';
            $question = '<strong>Domaine de compétence couvert par l\'attestation :</strong>';
            $soussigne = 'Je soussigné ' . $formateur . ' Formateur, <br/><br/>Atteste que<strong>' . $civilite . ' ' . $prenom . ' ' . $nom . ' </strong>a suivi l\'examen tenu le ' . $date_debut . '<br/> relatif au domaine de compétences susmentionné, <br/>sous le n° de ticket d\'examen [' . $ticket_examen . '] <span style="background-color:#FFF000;"><strong>a ' . $resultat_value . ' cet examen</strong></span>.';
            $attestation = '<strong>La présente attestation est valable pour une durée de 5 ans à compter de la <br/> date de réussite à l\'examen mentionnée ci-dessus, ou du ' . $date_reussi . ' si la <br/> date de réussite à l\'examen est antérieure au ' . $date_reussi . '.<br/><br/> Elle permet la délivrance par l\'employeur d\'une autorisation d\'intervention à <br/> proximité des réseaux (AIPR), dont le délai de validité ne peut dépasser celui<br/> de la présente attestation.</strong>';
            $nota = 'Nota : la présente attestation n\'a pas de valeur pour l\'application d\'autres réglementations que celle mentionnée dans le<br/> titre ; elle ne dispense pas non plus des autorisations nécessaires le cas échéant pour l\'accès aux ouvrages des exploitants.';
            $fait = 'Fait à ' . $ville . ', le ' . $date . '';
            $signature = '<img src="test/Signature/Signature.jpg" height="60" width="110">';
            $footer = '<p style="text-align:center;color:#808080">Centre National de Formation en Sécurité et Environnement<br/> 231 Rue Saint Honoré 75001 Paris - RCS : Paris 482.379.302– APE 8559A <br/> Tél : 01.84.16.38.25 - http://habilitations-electrique.fr - e-mail : contact@cnfse.fr </p>';
            if ($type_formation == "HABILITATIONS") {
                $typeFormationHab = $modelColumn0['typeFormationHab'];
                $nomForm = "Habilitation électrique ";
                $taba1b1 = "";
                if ($typeFormationHab == "B0 H0 H0v") {
                    $b0_h0_h0v_b0 = $modelColumn0['info_apprenants'][0]["b0_h0_h0v_b0"];
                    $b0_h0_h0v_h0v = $modelColumn0['info_apprenants'][0]["b0_h0_h0v_h0v"];
                    $nomForm .= $b0_h0_h0v_b0 == 1 ? "B0 " : "";
                    $nomForm .= $b0_h0_h0v_h0v == 1 ? "H0 H0v " : "";
                } else if ($typeFormationHab == "BS BE HE") {
                    $bs_be_he_b0 = $modelColumn0['info_apprenants'][0]["bs_be_he_b0"];
                    $bs_be_he_h0v = $modelColumn0['info_apprenants'][0]["bs_be_he_h0v"];
                    $bs_be_he_bs = $modelColumn0['info_apprenants'][0]["bs_be_he_bs"];
                    $bs_be_he_manoeuvre = $modelColumn0['info_apprenants'][0]["bs_be_he_manoeuvre"];
                    $bs_be_he_he = $modelColumn0['info_apprenants'][0]["bs_be_he_he"];
                    $nomForm .= $bs_be_he_b0 == 1 ? "B0 " : "";
                    $nomForm .= $bs_be_he_h0v == 1 ? "H0 H0v " : "";
                    $nomForm .= $bs_be_he_bs == 1 ? "BS " : "";
                    if ($bs_be_he_manoeuvre == 1) {
                        if ($bs_be_he_he == 1) {
                            $nomForm .= "BE/HE manœuvre";
                        } else {
                            $nomForm .= "BE manœuvre";
                        }
                    }
                } else if ($typeFormationHab == "B1v B2v BC BR") {
                    $b1v_b2v_bc_br_b0 = $modelColumn0['info_apprenants'][0]["b1v_b2v_bc_br_b0"];
                    $b1v_b2v_bc_br_h0v = $modelColumn0['info_apprenants'][0]["b1v_b2v_bc_br_h0v"];
                    $b1v_b2v_bc_br_bs = $modelColumn0['info_apprenants'][0]["b1v_b2v_bc_br_bs"];
                    $b1v_b2v_bc_br_manoeuvre = $modelColumn0['info_apprenants'][0]["b1v_b2v_bc_br_manoeuvre"];
                    $b1v_b2v_bc_br_b1v = $modelColumn0['info_apprenants'][0]["b1v_b2v_bc_br_b1v"];
                    $b1v_b2v_bc_br_b2v = $modelColumn0['info_apprenants'][0]["b1v_b2v_bc_br_b2v"];
                    $b1v_b2v_bc_br_bc = $modelColumn0['info_apprenants'][0]["b1v_b2v_bc_br_bc"];
                    $b1v_b2v_bc_br_br = $modelColumn0['info_apprenants'][0]["b1v_b2v_bc_br_br"];
                    $b1v_b2v_bc_br_essai = $modelColumn0['info_apprenants'][0]["b1v_b2v_bc_br_essai"];
                    $b1v_b2v_bc_br_verification = $modelColumn0['info_apprenants'][0]["b1v_b2v_bc_br_verification"];
                    $b1v_b2v_bc_br_mesurage = $modelColumn0['info_apprenants'][0]["b1v_b2v_bc_br_mesurage"];
                    $b1v_b2v_bc_br_he = $modelColumn0['info_apprenants'][0]["b1v_b2v_bc_br_he"];
                    $nomForm .= $b1v_b2v_bc_br_b0 == 1 ? "B0 " : "";
                    $nomForm .= $b1v_b2v_bc_br_h0v == 1 ? "H0 H0v " : "";
                    $nomForm .= $b1v_b2v_bc_br_bs == 1 ? "BS " : "";
                    if ($b1v_b2v_bc_br_manoeuvre == 1) {
                        if ($b1v_b2v_bc_br_he == 1) {
                            $nomForm .= "BE/HE manœuvre ";
                        } else {
                            $nomForm .= "BE manœuvre ";
                        }
                    }
                    $nomForm .= $b1v_b2v_bc_br_b1v == 1 ? "B1v " : "";
                    $nomForm .= $b1v_b2v_bc_br_b2v == 1 ? "B2v " : "";
                    $nomForm .= $b1v_b2v_bc_br_bc == 1 ? "BC " : "";
                    $nomForm .= $b1v_b2v_bc_br_br == 1 ? "BR " : "";
                    if ($b1v_b2v_bc_br_essai == 1) {
                        if ($b1v_b2v_bc_br_he == 1) {
                            $nomForm .= "BE/HE essai ";
                        } else {
                            $nomForm .= "BE essai ";
                        }
                    }
                    if ($b1v_b2v_bc_br_verification == 1) {
                        if ($b1v_b2v_bc_br_he == 1) {
                            $nomForm .= "BE/HE vérification ";
                        } else {
                            $nomForm .= "BE vérification ";
                        }
                    }
                    if ($b1v_b2v_bc_br_mesurage == 1) {
                        if ($b1v_b2v_bc_br_he == 1) {
                            $nomForm .= "BE/HE mesurage ";
                        } else {
                            $nomForm .= "BE mesurage ";
                        }
                    }
                } else if ($typeFormationHab == "B1v B2v BC BR H1v H2v") {
                    $b1v_b2v_bc_br_h1v_h2v_b0 = $modelColumn0['info_apprenants'][0]["b1v_b2v_bc_br_h1v_h2v_b0"];
                    $b1v_b2v_bc_br_h1v_h2v_h0v = $modelColumn0['info_apprenants'][0]["b1v_b2v_bc_br_h1v_h2v_h0v"];
                    $b1v_b2v_bc_br_h1v_h2v_bs = $modelColumn0['info_apprenants'][0]["b1v_b2v_bc_br_h1v_h2v_bs"];
                    $b1v_b2v_bc_br_h1v_h2v_manoeuvre = $modelColumn0['info_apprenants'][0]["b1v_b2v_bc_br_h1v_h2v_manoeuvre"];
                    $b1v_b2v_bc_br_h1v_h2v_b1v = $modelColumn0['info_apprenants'][0]["b1v_b2v_bc_br_h1v_h2v_b1v"];
                    $b1v_b2v_bc_br_h1v_h2v_b2v = $modelColumn0['info_apprenants'][0]["b1v_b2v_bc_br_h1v_h2v_b2v"];
                    $b1v_b2v_bc_br_h1v_h2v_bc = $modelColumn0['info_apprenants'][0]["b1v_b2v_bc_br_h1v_h2v_bc"];
                    $b1v_b2v_bc_br_h1v_h2v_br = $modelColumn0['info_apprenants'][0]["b1v_b2v_bc_br_h1v_h2v_br"];
                    $b1v_b2v_bc_br_h1v_h2v_essai = $modelColumn0['info_apprenants'][0]["b1v_b2v_bc_br_h1v_h2v_essai"];
                    $b1v_b2v_bc_br_h1v_h2v_verification = $modelColumn0['info_apprenants'][0]["b1v_b2v_bc_br_h1v_h2v_verification"];
                    $b1v_b2v_bc_br_h1v_h2v_mesurage = $modelColumn0['info_apprenants'][0]["b1v_b2v_bc_br_h1v_h2v_mesurage"];
                    $b1v_b2v_bc_br_h1v_h2v_h1v = $modelColumn0['info_apprenants'][0]["b1v_b2v_bc_br_h1v_h2v_h1v"];
                    $b1v_b2v_bc_br_h1v_h2v_h2v = $modelColumn0['info_apprenants'][0]["b1v_b2v_bc_br_h1v_h2v_h2v"];
                    $b1v_b2v_bc_br_h1v_h2v_hc = $modelColumn0['info_apprenants'][0]["b1v_b2v_bc_br_h1v_h2v_hc"];
                    $b1v_b2v_bc_br_h1v_h2v_he = $modelColumn0['info_apprenants'][0]["b1v_b2v_bc_br_h1v_h2v_he"];
                    $nomForm .= $b1v_b2v_bc_br_h1v_h2v_b0 == 1 ? "B0 " : "";
                    $nomForm .= $b1v_b2v_bc_br_h1v_h2v_h0v == 1 ? "H0 H0v " : "";
                    $nomForm .= $b1v_b2v_bc_br_h1v_h2v_bs == 1 ? "BS " : "";
                    if ($b1v_b2v_bc_br_h1v_h2v_manoeuvre == 1) {
                        if ($b1v_b2v_bc_br_h1v_h2v_he == 1) {
                            $nomForm .= "BE/HE manœuvre ";
                        } else {
                            $nomForm .= "BE manœuvre ";
                        }
                    }
                    $nomForm .= $b1v_b2v_bc_br_h1v_h2v_b1v == 1 ? "B1v " : "";
                    $nomForm .= $b1v_b2v_bc_br_h1v_h2v_b2v == 1 ? "B2v " : "";
                    $nomForm .= $b1v_b2v_bc_br_h1v_h2v_bc == 1 ? "BC " : "";
                    $nomForm .= $b1v_b2v_bc_br_h1v_h2v_br == 1 ? "BR " : "";
                    if ($b1v_b2v_bc_br_h1v_h2v_essai == 1) {
                        if ($b1v_b2v_bc_br_h1v_h2v_he == 1) {
                            $nomForm .= "BE/HE essai ";
                        } else {
                            $nomForm .= "BE essai ";
                        }
                    }
                    if ($b1v_b2v_bc_br_h1v_h2v_verification == 1) {
                        if ($b1v_b2v_bc_br_h1v_h2v_he == 1) {
                            $nomForm .= "BE/HE vérification ";
                        } else {
                            $nomForm .= "BE vérification ";
                        }
                    }
                    if ($b1v_b2v_bc_br_h1v_h2v_mesurage == 1) {
                        if ($b1v_b2v_bc_br_h1v_h2v_he == 1) {
                            $nomForm .= "BE/HE mesurage ";
                        } else {
                            $nomForm .= "BE mesurage ";
                        }
                    }
                    $nomForm .= $b1v_b2v_bc_br_h1v_h2v_h1v == 1 ? "H1v " : "";
                    $nomForm .= $b1v_b2v_bc_br_h1v_h2v_h2v == 1 ? "H2v " : "";
                    $nomForm .= $b1v_b2v_bc_br_h1v_h2v_hc == 1 ? "HC " : "";
                }
                $nom_formation_value = $nomForm;
                /* uni_cnfsecrm - v2 - modif 144 - DEBUT */
                $array_not_tab1 = array("BR", "BC", "B2v", "B1v", "BS", "HC", "H2v", "H1v", "BE manœuvre", "BE essai", "BE mesurage", "BE vérification", "BE/HE essai", "BE/HE mesurage", "BE/HE vérification", "BE/HE manœuvre");
                $taba1b1 = str_replace("Habilitation électrique", "", $nomForm);
                $taba1b1 = str_replace($array_not_tab1, "", $taba1b1);
                /* uni_cnfsecrm - v2 - modif 144 - FIN */

                //variable categorie 1
                $text = '<p><strong>' . $civilite . ' ' . $prenom . ' ' . $nom . ' </strong>a suivi la formation habilitation électrique du  <strong>' . $date_debut . '</strong> au<br/> <strong>' . $date_fin . '</strong>, pour une durée de <strong>' . $nbr_jours . '</strong> jour, au sein de notre organisme de formation <br/> Formation au vue d\'une <strong> ' . $nom_formation_value . ' </strong> <br/> Au cours de cette formation<strong> ' . $civilite . ' ' . $prenom . ' ' . $nom . ' </strong>a acquis les connaissances et le<br/> savoir-faire nécessaires pour prendre en compte le risque électrique dans le cadre<br/> d\'opérations d\'ordre électriques ou non électrique et se prémunir de tout accident susceptible <br/> d\'être encouru.</p>';
                $text2 = 'Au vu de cet avis et compte tenu des prescriptions de la norme NF C 18-510 l\'employeur peut<br/> délivrer à<strong> ' . $civilite . ' ' . $prenom . ' ' . $nom . '</strong> l\'habilitation mentionnée ci-dessous';
                if ($type_formation_elect == 1 && $electricien == 1 && $type_formation == "HABILITATIONS" && $testprerequis == "1") {
                    $text3 = 'Test de prérequis réussi conformément à la NFC 18-510.';
                } elseif ($type_formation_elect == 1 && $electricien == 1 && $type_formation == "HABILITATIONS" && $testprerequis == "0") {
                    $text3 = 'Au vue des résultats du test de prérequis effectué en début de la formation, nous préconisons une mise à jour "électricité professionnelle"  § 4.5.2 et annexe NFC 18-510.';
                } elseif ($type_formation_elect == 1 && $electricien == 0 && $type_formation == "HABILITATIONS" && $testprerequis == "1") {
                    $text3 = 'Nous préconisons conformément à la NF C 18-510 § 4.5.2 une formation "électricité professionnelle".';
                } elseif ($type_formation_elect == 1 && $electricien == 0 && $type_formation == "HABILITATIONS" && $testprerequis == "0") {
                    $text3 = 'Au vue des résultats du test de prérequis effectué en début de la formation, nous préconisons une mise à jour "électricité professionnelle"  § 4.5.2 et annexe NFC 18-510.';
                } else {
                    $text3 = '';
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
                $a1 = $taba1b1;
//                $a2 = "BT-TBT-HTA";
                /* test-01-debut */
                //                $a2 = "BT-TBT-HTA";
                if ($b0_h0_h0v_h0v == 1 || $b1v_b2v_bc_br_h1v_h2v_he == 1 || $b1v_b2v_bc_br_h1v_h2v_hc == 1 || $b1v_b2v_bc_br_h1v_h2v_h2v == 1 || $b1v_b2v_bc_br_h1v_h2v_h1v == 1 || $b1v_b2v_bc_br_h1v_h2v_h0v == 1) {
                    $a2 = "TBT-BT-HTA";
                } else {
                    $a2 = "TBT-BT";
                }
                /* test-01-fin */
                $a3 = 'Accès Toutes installations électriques';
                $a4 = '';
                $array_b1 = array();
                $array_b2 = array();
                $b1 = $taba1b1;
//                $b2 = "BT-TBT-HTA";
                /* test-01-debut */
//                $b2 = "BT-TBT-HTA";                
                if ($b0_h0_h0v_h0v == 1 || $b1v_b2v_bc_br_h1v_h2v_he == 1 || $b1v_b2v_bc_br_h1v_h2v_hc == 1 || $b1v_b2v_bc_br_h1v_h2v_h2v == 1 || $b1v_b2v_bc_br_h1v_h2v_h1v == 1 || $b1v_b2v_bc_br_h1v_h2v_h0v == 1) {
                    $b2 = "TBT-BT-HTA";
                } else {
                    $b2 = "TBT-BT";
                }
                /* test-01-fin */
                $b3 = 'Accès Toutes installations électriques';
                $b4 = '';
                $array_c1 = array();
                $array_c2 = array();
                $array_liste_c_b = array("B1V", "B1v");
                $array_liste_c_h = array("H1V", "H1v");
                /* uni_cnfsecrm - v2 - modif 144 - DEBUT */
                foreach ($array_liste_c_b as $key => $value) {
                    if (strstr($nom_formation_value, $value)) {
                        array_push($array_c1, $value);
                        if (!in_array("TBT-BT", $array_c2))
                            array_push($array_c2, "TBT-BT");
                    }
                }
                foreach ($array_liste_c_h as $key => $value) {
                    if (strstr($nom_formation_value, $value)) {
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
                $array_liste_c_b = array("B2V", "B2v");
                $array_liste_c_h = array("H2V", "H2v");
                foreach ($array_liste_c_b as $key => $value) {
                    if (strstr($nom_formation_value, $value)) {
                        array_push($array_d1, $value);
                        if (!in_array("TBT-BT", $array_d2))
                            array_push($array_d2, "TBT-BT");
                    }
                }
                foreach ($array_liste_c_h as $key => $value) {
                    if (strstr($nom_formation_value, $value)) {
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
                $array_liste_c_b = array("BR", "Br", "BS", "Bs");
                //var_dump($nom_formation);
                foreach ($array_liste_c_b as $key => $value) {
                    if (strstr($nom_formation_value, $value)) {
                        array_push($array_e1, $value);
                        if (!in_array("TBT-BT", $array_e2))
                            array_push($array_e2, "TBT-BT");
                    }
                }
                $e1 = implode("-", $array_e1);
                $e2 = implode("-", $array_e2);
                $e3 = (count($array_e1) > 0) ? "Tout ouvrage ou installation électrique" : "";
                $e4 = '';
                $array_f1 = array();
                $array_f2 = array();
                $array_liste_c_b = array("BC", "Bc");
                $array_liste_c_h = array("HC", "Hc");
                foreach ($array_liste_c_b as $key => $value) {
                    if (strstr($nom_formation_value, $value)) {
                        array_push($array_f1, $value);
                        if (!in_array("TBT-BT", $array_f2))
                            array_push($array_f2, "TBT-BT");
                    }
                }
                /* uni_cnfsecrm - v2 - modif 144 - DEBUT */
                foreach ($array_liste_c_h as $key => $value) {
                    if (strstr($nom_formation_value, $value)) {
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
                /* uni_cnfsecrm - v2 - modif 144 - DEBUT */
                $array_liste_c_b = array("BE manœuvre", "BE essai", "BE mesurage", "BE vérification", "BE/HE essai", "BE/HE mesurage", "BE/HE vérification", "BE/HE manœuvre");
//                $array_liste_c_h = array("BE manœuvre", "BE essai", "BE mesurage", "BE vérification", "BE/HE essai", "BE/HE mesurage", "BE/HE vérification", "BE/HE manoeuvre");
                /* test-01-debut */
                //$array_liste_c_h = array("BE manœuvre", "BE essai", "BE mesurage", "BE vérification", "BE/HE essai", "BE/HE mesurage", "BE/HE vérification", "BE/HE manoeuvre");
                $array_liste_c_h = array("BE/HE manœuvre", "BE/HE essai", "BE/HE vérification", "BE/HE mesurage");
                /* test-01-fin */
                /* uni_cnfsecrm - v2 - modif 144 - FIN */
                foreach ($array_liste_c_b as $key => $value) {
                    if (strstr($nom_formation_value, $value)) {
                        array_push($array_g1, $value);
                        if (!in_array("TBT-BT", $array_g2))
                            array_push($array_g2, "TBT-BT");
                    }
                }
                foreach ($array_liste_c_h as $key => $value) {
                    if (strstr($nom_formation_value, $value)) {
                        /* uni_cnfsecrm - v2 - modif 144 - DEBUT */
                        //array_push($array_g1, $value);
                        /* uni_cnfsecrm - v2 - modif 144 - FIN */
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

            if ($type_formation == "AIPR") {
                $titre = '<p style="text-align:center">Attestation de compétences relative à <br/> l\'intervention à proximité des réseaux</p>';
                $rep1 = '<strong>Préparation et conduite de projet (Concepteur)</strong>';
                $rep2 = 'Encadrement de chantiers de travaux (Encadrant)';
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
                $pdf->SetFont($calibrib, '', 13);
                $pdf->writeHTMLCell(200, 100, $x + 20, $y + $l3, $question, '');

                $pdf->SetFont($calibri, '', 5);
                $pdf->SetXY($x + 22, $y + $l3 + 10);
                $array_coche = array(215, 215, 214);
                $array_noncoche = array(251, 141, 0);

                if ($nom_formation == "AIPR CONCEPTEUR" || $nom_formation == "AIPR Concepteur") {
                    $array1 = array(215, 215, 214);
                    $array2 = array(251, 141, 0);
                    $array3 = array(251, 141, 0);
                    $formation_active = 1;
                    $width1 = 1.5;
                    $width2 = 1;
                    $width3 = 1;
                }
                if ($nom_formation == "AIPR ENCADRANT" || $nom_formation == "AIPR Encadrant") {
                    $array1 = array(251, 141, 0);
                    $array2 = array(215, 215, 214);
                    $array3 = array(251, 141, 0);
                    $formation_active = 2;
                    $width1 = 1;
                    $width2 = 1.5;
                    $width3 = 1;
                }
                if ($nom_formation == "AIPR OPERATEUR" || $nom_formation == "AIPR Opérateur") {
                    $array1 = array(251, 141, 0);
                    $array2 = array(251, 141, 0);
                    $array3 = array(215, 215, 214);
                    $formation_active = 3;
                    $width1 = 1;
                    $width2 = 1;
                    $width3 = 1.5;
                }
                if ($formation_active == 1) {
                    $pdf->SetFillColor(0, 0, 0);
                } else {
                    $pdf->SetFillColor(255, 255, 2550);
                }
                $pdf->SetLineStyle(array('width' => $width1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $array1));
                $pdf->MultiCell(5, 5, '', 1, 'C', 1, 1);

                $pdf->SetFont($calibrib, '', 13);
                $pdf->writeHTMLCell(200, 100, $x + 30, $y + $l3 + 9, $rep1, '');


                $pdf->SetFont($calibri, '', 5);
                $pdf->SetXY($x + 22, $y + $l3 + 20);
                if ($formation_active == 2) {
                    $pdf->SetFillColor(0, 0, 0);
                } else {
                    $pdf->SetFillColor(255, 255, 255);
                }
                $pdf->SetLineStyle(array('width' => $width2, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $array2));
                $pdf->MultiCell(5, 5, '', 1, 'C', 1, 1);

                $pdf->SetFont($calibrib, '', 13);
                $pdf->writeHTMLCell(200, 100, $x + 30, $y + $l3 + 19, $rep2, '');

                $pdf->SetFont($calibri, '', 5);
                $pdf->SetXY($x + 22, $y + $l3 + 30);
                if ($formation_active == 3) {
                    $pdf->SetFillColor(0, 0, 0);
                } else {
                    $pdf->SetFillColor(255, 255, 255);
                }
                $pdf->SetLineStyle(array('width' => $width3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $array3));
                $pdf->MultiCell(5, 5, '', 1, 'C', 1, 1);

                $pdf->SetFont($calibri, '', 13);
                $pdf->writeHTMLCell(200, 100, $x + 30, $y + $l3 + 29, $rep3, '');

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
            //attestation debut
            $pdf->addPage();
            $pdf->setPageFormat('A4', 'L');
            $logo = "";
            $image1 = "";
            $image2 = "";
            /* uni_cnfsecrm - v2 - modif 127 - DEBUT */
//            $date_debut_formation = $modelColumn0["date_debut_formation"];
//            $date_fin_formations = $modelColumn0["date_fin_formations"];
            if ($modelColumn0['info_apprenants'][0]["date_start_appr"] == '0000-00-00' || $modelColumn0['info_apprenants'][0]["date_start_appr"] == '' || $modelColumn0['info_apprenants'][0]["date_start_appr"] == '1970-01-01') {
                $date_debut_formation = $modelColumn0["date_debut_formation"];
            } else {
                $date_start_appr = strtotime($modelColumn0['info_apprenants'][0]["date_start_appr"]);
                $date_debut_formation = date('d/m/Y', $date_start_appr);
            }
            if ($modelColumn0['info_apprenants'][0]["date_fin_appr"] == '0000-00-00' || $modelColumn0['info_apprenants'][0]["date_fin_appr"] == '' || $modelColumn0['info_apprenants'][0]["date_fin_appr"] == '1970-01-01') {
                $date_fin_formations = $modelColumn0["date_fin_formations"];
            } else {
                $date_fin_appr = strtotime($modelColumn0['info_apprenants'][0]["date_fin_appr"]);
                $date_fin_formations = date('d/m/Y', $date_fin_appr);
            }

            if ($modelColumn0['info_apprenants'][0]["duree_heure"] == 0) {
                $nbr_heure = $modelColumn0["nbr_heures"];
            } else {
                $nbr_heure = $modelColumn0['info_apprenants'][0]["duree_heure"];
            }
            if ($modelColumn0['info_apprenants'][0]["duree_jour"] == 0) {
                $nbr_jour = $modelColumn0["nbr_jours"];
            } else {
                $nbr_jour = $modelColumn0['info_apprenants'][0]["duree_jour"];
            }
            /* uni_cnfsecrm - v2 - modif 127 - FIN */
            $label_jour = ($nbr_jour == 1) ? "Jour" : "Jours";
            $rue = "231 Rue St Honoré ";
            $ville = "Paris 1er";
            $tel = "01.84.16.38.25";
            $fax = "09.72.33.02.35";
            $email = "contact@cnfse.fr";
            $info = "RCS Paris 482.379.302 – APE : 8559A- SAS capital 8.000 euros – Déclaration d'activité 11.75.51614.75";
            $salutation = $modelColumn0['info_apprenants'][0]["salutation"];
            $prenom = $modelColumn0['info_apprenants'][0]["firstname"];
            $nom = $modelColumn0['info_apprenants'][0]["lastname"];
            $datenaissance = $modelColumn0['info_apprenants'][0]["birthday"];
            $date = "contacts-birthday";
            $resultat = $modelColumn0['info_apprenants'][0]["resultat"];
            switch ($resultat) {
                case "avis_favorable":
                    $resultat_value = "Avis favorable";
                    break;

                case "avis_defavorable":
                    $resultat_value = "Avis défavorable";
                    break;

                default:
                    $resultat_value = "Autre";
                    break;
            }
            $nom_formation = $modelColumn0['subject'];
            $text_haccp = "";
            //var_dump($modelColumn0);
            $formation = $modelColumn0["nom_formation"];

            $text_resultat = 'Résultats de l\'évaluation des acquis : ' . $resultat_value;
            $anniv_test = ($datenaissance != "") ? " né(e) le " . formatDateFr($datenaissance) : "";

            $address = $modelColumn0["cp_formation"];
            //recuperer le region appartir de code postale
            $data = array('address' => '', 'lat' => '', 'lng' => '', 'city' => '', 'department' => '', 'region' => '', 'country' => '', 'postal_code' => '');
            //on formate l'adresse
            $address = str_replace(" ", "+", $address);
            if ($address != "8000") {
                //on fait l'appel à l'API google map pour géocoder cette adresse
                $json = file_get_contents("https://maps.google.com/maps/api/geocode/json?key=AIzaSyBwGYE4Ag3fK0bSNNwEkEbj3RkO91AX9y0&address=$address&sensor=false&region=fr&components=country:FR");
                $json = json_decode($json);
                //on enregistre les résultats recherchés
                if ($json->status == 'OK' && count($json->results) > 0) {
                    $res = $json->results[0];
                    //adresse complète et latitude/longitude
                    $data['address'] = $res->formatted_address;
                    $data['lat'] = $res->geometry->location->lat;
                    $data['lng'] = $res->geometry->location->lng;
                    foreach ($res->address_components as $component) {
                        //ville
                        if ($component->types[0] == 'locality') {
                            $data['city'] = $component->long_name;
                        }
                        //départment
                        if ($component->types[0] == 'administrative_area_level_2') {
                            $data['department'] = $component->long_name;
                        }
                        //région
                        if ($component->types[0] == 'administrative_area_level_1') {
                            $data['region'] = $component->long_name;
                        }
                        //pays
                        if ($component->types[0] == 'country') {
                            $data['country'] = $component->long_name;
                        }
                        //code postal
                        if ($component->types[0] == 'postal_code') {
                            $data['postal_code'] = $component->long_name;
                        }
                    }
                }
            } else if ($address == "8000") {
                $data['region'] = "Grand Est";
            }

            $data['region'] = str_replace("-", " ", $data['region']);
//http://maps.google.com/maps/api/geocode/json?components=country:AU|postal_code:2340&sensor=false
            // echo $data['region'];
            //recuperer le code ROFHYA
            switch ($data['region']) {
                case 'IdF':
                case 'Île de France':
                    $region = 'Ile de France';
                    $code_rofhya = '11 0333 10 2014';
                    break;
                case 'Auvergne Rhône Alpes':
                    $region = 'Auvergne Rhône Alpes';
                    $code_rofhya = '84 0305 41 2017';
                    break;
                case 'Bourgogne Franche Comté':
                    $region = 'Bourgogne Franche Comté';
                    $code_rofhya = '27 0181 42 2017';
                    break;
                case 'Bretagne':
                case 'Brittany':
                    $region = 'Bretagne';
                    $code_rofhya = '53 0126 08 2015';
                    break;
                case 'Centre Val de Loire':
                    $region = 'Centre Val de Loire';
                    $code_rofhya = '24 0107 09 2014';
                    break;
                case 'Grand Est':
                    $region = 'Grand Est';
                    $code_rofhya = '44 0052 14 2017';
                    break;
                case 'Hauts de France':
                    $region = 'Hauts de France';
                    $code_rofhya = '32 0042 19 2017';
                    break;
                case 'Normandy':
                    $region = 'Normandie';
                    $code_rofhya = '23 0000 08 2015';
                    break;
                case 'Nouvelle Aquitaine':
                    $region = 'Nouvelle Aquitaine';
                    $code_rofhya = '75 0085 41 2017';
                    break;
                case 'Occitanie':
                    $region = 'Occitanie';
                    $code_rofhya = '76 0069 50 2017';
                    break;
                case 'Pays de la Loire':
                    $region = 'Pays de la Loire';
                    $code_rofhya = '52 0137 02 2016';
                    break;
                case 'PACA':
                case "Provence Alpes Côte d'Azur":
                    $region = 'PACA';
                    $code_rofhya = '93 0223 12 2014';
                    break;
            }

            switch ($type_formation) {
                case 'AIPR':
                    $titre = '<p style="text-align:center">Attestation de Formation <br/> relative à l\'intervention à proximité des réseaux</p>';
                    $image1 = $pdf->Image('test/upload/aipr.png', 27, 17, 50, 30);
                    $logo = $pdf->Image('test/logo/logo-CNFSE-large.png', 110, 20, 70, 24);
                    $image2 = $pdf->Image('test/upload/img_intervention.png', 220, 16, 50, 30);
                    $soussigne = '<p style="text-align:center">Nous soussigné C.N.F.S.E. Centre d\'Examen n° 645 reconnu par le Ministère de la Transition Écologique et Solidaire <br/> Attestons que, <br/> conformément aux dispositions de l\'article L.6353-1 du code du Travail : </p>';
                    $info_etudiant = '<p style="text-align:center">' . $salutation . ' ' . $prenom . ' ' . $nom . '</p>';
                    $a_suivi = '<p style="text-align:center">a suivi la formation ' . $nom_formation . ' <br/> Cette formation s\'est déroulée le' . $date_fin_formations . ' <br/> d\'une durée totale de ' . $nbr_heure . ' heures sur ' . $nbr_jour . ' ' . $label_jour . '</p>';
                    $text = 'Les objectifs de formation visés sont d\'acquérir, apprendre la réglementation liée aux travaux à proximité des réseaux, ses connaissances du Guide technique, identifier les risques métier pour adapter vos méthodes de travail, préparer et obtenir l\'examen AIPR sous forme de QCM';
                    $l1 = 55;
                    $l2 = 70;
                    $l3 = 80;
                    $l4 = 100;
                    $l5 = 110;
                    $l6 = 130;
                    $l7 = 150;
                    $l8 = 160;
                    $l9 = 175;
                    break;
                case 'HABILITATIONS':
                    $nom_formation = $nomForm;
                    $titre = '<p style="text-align:center">Attestation de Formation <br/> en Habilitation Electrique</p>';
                    $image1 = $pdf->Image('test/upload/images/gauche.jpg', 25, 16, 45, 40);
                    $logo = $pdf->Image('test/logo/logo-CNFSE-large.png', 110, 20, 70, 24);
                    $image2 = $pdf->Image('test/upload/images/droite.jpg', 230, 16, 40, 40);
                    $soussigne = '<p style="text-align:center">Nous soussigné C.N.F.S.E. attestons que, conformément aux dispositions de l\'article L.6353-1 du code du Travail :</p>';
                    $info_etudiant = '<p style="text-align:center">' . $salutation . ' ' . $prenom . ' ' . $nom . '</p>';
                    $a_suivi = '<p style="text-align:center">a suivi la formation ' . $nom_formation . ' <br/> Cette formation s\'est déroulée du  ' . $date_debut_formation . ' au ' . $date_fin_formations . ' <br/> d\'une durée totale de ' . $nbr_heure . ' heures sur ' . $nbr_jour . ' ' . $label_jour . '.</p>';
                    $text = 'Les objectifs de formation visés sont d\'acquérir, apprendre la réglementation en matière selon la norme NF C 18-510. Appliquer les consignes de sécurité en BT et HT liées aux consignations, aux interventions générales, aux travaux hors tension ou au voisinage effectué sur des ouvrages ou des installations électriques Délivrance d\'un titre d\'habilitation pré-renseigné des symboles proposés par le formateur.';
                    $l1 = 55;
                    $l2 = 70;
                    $l3 = 80;
                    $l4 = 90;
                    $l5 = 100;
                    $l6 = 120;
                    $l7 = 150;
                    $l8 = 160;
                    $l9 = 175;
                    break;
                default :
                    $titre = '<p style="text-align:center">Attestation de Formation <br/> ' . $formation . '</p>';
                    $logo = $pdf->Image('test/logo/logo-CNFSE-large.png', 110, 20, 70, 24);
                    $soussigne = '<p style="text-align:center">Nous soussigné Centre de formation C.N.F.S.E. certifions par la présente que :</p>';
                    $info_etudiant = '<p style="text-align:center">' . $salutation . ' ' . $prenom . ' ' . $nom . $anniv_test . ' </p>';
                    $a_suivi = '<p style="text-align:center">a suivi la formation ' . $formation . ' <br/>Cette formation s\'est déroulée du ' . $date_debut_formation . ' au ' . $date_fin_formations . ', <br/>d\'une durée totale de ' . $nbr_heure . ' heures sur ' . $nbr_jour . ' ' . $label_jour . '. </p>';
                    $text = 'Les objectifs de formation visés sont de renforcer l\'employabilité des personnes et de sécuriser leur parcours.';
                    $l1 = 55;
                    $l2 = 70;
                    $l3 = 90;
                    $l4 = 100;
                    $l5 = 110;
                    $l6 = 133;
                    $l7 = 150;
                    $l8 = 160;
                    $l9 = 180;
                    break;
            }

            //image font
            $pdf->SetAlpha(0.1);
            $image_font = '<img style="text-align:center; opacity: 0.1; filter: alpha(opacity=10);" src="test/upload/image_font.jpg" width="550" height="615">';
            $pdf->writeHTMLCell(200, 100, 47, 0, $image_font, '');
            $pdf->SetAlpha(1);
            //fin image font                        

            $pdf->SetDrawColor(49, 132, 155);
            $pdf->SetFillColor(255, 255, 255);

            //bordure top
            $pdf->SetXY($x, $y + 10);
            $pdf->MultiCell(297, 5, '', 'T', 'L', 1, 1);

            $pdf->SetXY($x, $y + 13);
            $pdf->MultiCell(297, 5, '', 'T', 'L', 1, 1);

            //bordure left
            $pdf->SetXY($x + 20, $y + 13);
            $pdf->MultiCell(0.5, 184, '', 'L', 'L', 1, 1);

            $pdf->SetXY($x + 23, $y + 13);
            $pdf->MultiCell(0.5, 184, '', 'L', 'L', 1, 1);
//            //bordure right
            $pdf->SetXY($x + 273, $y + 13);
            $pdf->MultiCell(0.5, 184, '', 'R', 'L', 1, 1);

            $pdf->SetXY($x + 276, $y + 13);
            $pdf->MultiCell(0.5, 184, '', 'R', 'L', 1, 1);
            //bordure bottom
            $pdf->SetXY($x, $y + 196.5);
            $pdf->MultiCell(297, 0.5, '', 'T', 'L', 1, 1);

            $pdf->SetXY($x, $y + 199.5);
            $pdf->MultiCell(297, 0.5, '', 'T', 'L', 1, 1);
            //border 2
            //top cellule 2
            $pdf->SetXY($x + 20, $y + 0);
            $pdf->MultiCell(256, 9.5, '', 'LR', 'C', 1, 1);

            $pdf->SetXY($x + 23, $y + 0);
            $pdf->MultiCell(250, 9.5, '', 'LR', 'C', 1, 1);
            //bottom cellule 2
            $pdf->SetXY($x + 20, $y + 200);
            $pdf->MultiCell(256, 9.5, '', 'LR', 'C', 1, 1);

            $pdf->SetXY($x + 23, $y + 200);
            $pdf->MultiCell(250, 9.5, '', 'LR', 'C', 1, 1);
            //fin bordure
//image 
            $logo;
            $image1;
            $image2;
//fin image

            $x = 0;
            $y = 0;

            $pdf->SetXY($x, $y + $l1);
            $pdf->SetFont($calibrib, '', 25);
            $pdf->writeHTML($titre, true, false, true, false, '');

            //text haccp
            $pdf->SetXY($x + 40, $y + $l2);
            $pdf->SetFont($calibrib, '', 12);
            $pdf->SetTextColor(127, 127, 127);
            $pdf->MultiCell(220, 5, $text_haccp, 0, 'L', 0, 1);
            $pdf->SetXY($x, $y + $l3);
            $pdf->SetFont($calibrib, '', 12);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->writeHTML($soussigne, true, false, true, false, '');

            $pdf->SetXY($x, $y + $l4);
            $pdf->SetTextColor(47, 84, 150);
            $pdf->SetFont($calibrib, '', 20);
            $pdf->writeHTML($info_etudiant, true, false, true, false, '');

            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont($calibrib, '', 12);
            $pdf->writeHTMLCell(200, 100, $x + 50, $y + $l5, $a_suivi, '');

            $pdf->SetXY($x + 40, $y + $l6);
            $pdf->SetTextColor(128, 128, 128);
            $pdf->SetFont($calibrib, '', 11);
            $pdf->MultiCell(210, 5, $text, 0, 'L', 0, 1);

            $pdf->SetXY($x + 40, $y + $l7);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont($calibrib, '', 14);
            $pdf->writeHTML($text_resultat, true, false, true, false, 'L');

            //signature
            $pdf->Image("test/Signature/Signature.jpg", 40, $l8, 40, 20);

            $pdf->SetXY($x + 215, $y + $l8);
            $pdf->SetFont($calibrib, '', 12);
            $pdf->MultiCell(30, 5, 'PARIS,', 0, 'L', 0, 1);

            $pdf->SetXY($x + 215, $y + $l8 + 5);
            $pdf->SetFont($calibrib, '', 12);
            $pdf->MultiCell(30, 5, 'le ' . $date_fin_formations . '', 0, 'L', 0, 1);

            $pdf->SetXY($x + 90, $y + $l9);
            $pdf->SetTextColor(128, 128, 128);
            $pdf->SetFont($calibrib, '', 8);
            $pdf->MultiCell(200, 5, $rue . ' - ' . $ville . ' – Tél : ' . $tel . ' - Fax : ' . $fax . ' – courriel : ' . $email, 0, 'L', 0, 1);

            $pdf->SetXY($x + 90, $y + $l9 + 5);
            $pdf->SetFont($calibrib, '', 8);
            $pdf->MultiCell(200, 5, $info, 0, 'L', 0, 1);
            //attestation fin
            //doc Habilitation debut
            $pdf->addPage();
            $pdf->setPageFormat('A4', 'P');
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont($calibri, '', 20);
            $pdf->SetDrawColor(0, 0, 0);
            $pdf->Image('test/logo/logo-CNFSE.jpg', 15, 10, 45, 17);
            $region = 'ile de france';
            if ($accountid == 119579) {
                $pdf->Image('test/logo/region_ile_france.png', 140, 10, 45, 17);
            } else if ($accountid == 189380) {
                $pdf->Image('test/logo/ville-de-paris.png', 168, 10, 15, 15);
            }

            $x = 5;
            $y = 25;

            $pdf->MultiCell(210, 5, html_entity_decode("TITRE Habilitation électrique"), '', 'C', 0, 1, 5, $y += 5);
            /* -- */

            $pdf->SetFont($calibri, '', 10);
            $pdf->MultiCell(190, 5, '(EXTRAIT DE LA NORME NFC 18510)', '', 'C', 0, 1, $x, $y += 10);

            $x = 10;
            $pdf->SetFont($calibri, '', 12);
            $pdf->MultiCell(190, 5, 'Titulaire : ' . html_entity_decode($nom) . " " . html_entity_decode($prenom), '', 'L', 0, 1, $x, $y += 10);
            //var_dump($date_debut);
            $dateDelivrance = $date_fin;
            $date = DateTime::createFromFormat('d/m/Y', $date_fin);
            $date = date('Y-m-d', strtotime($date->format("Y-m-d H:i:s") . ' + 3 years'));
            $date = DateTime::createFromFormat('Y-m-d', $date);
            $dateValidite = $date->format("d/m/Y");

            $pdf->MultiCell(190, 5, 'Date de délivrance : ' . $dateDelivrance . ';   Date de fin validité : ' . $dateValidite, '', 'L', 0, 1, $x, $y += 10);

            //contenu de tableau
            //tableau
            //row1
            $x = 15;
            $pdf->SetFont($calibrib, '', 11.5);
            $pdf->SetFillColor(83, 129, 53);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->MultiCell(35, 18, 'Champs d’application', 1, 'C', 1, 1, $x, $y += 10, true, 0, false, true, 0, 'M', true);
            $pdf->MultiCell(35, 18, 'Symbole d’habilitation et attribut', 1, 'C', 1, 1, $x += 35, $y, true, 0, false, true, 0, 'M', true);
            $pdf->MultiCell(35, 18, 'Domaine de tension ou tension concernées', 1, 'C', 1, 1, $x += 35, $y, true, 0, false, true, 0, 'M', true);
            $pdf->MultiCell(35, 18, 'Ouvrage ou installation concernés', 1, 'C', 1, 1, $x += 35, $y, true, 0, false, true, 0, 'M', true);
            $pdf->MultiCell(35, 18, 'Indications supplémentaires', 1, 'C', 1, 1, $x += 35, $y, true, 0, false, true, 0, 'M', true);
            //row2
            $x = 15;
            $pdf->SetFont($calibri, '', 9.5);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->MultiCell(35, 7, '', 1, 'C', 1, 1, $x, $y += 18);
            $pdf->SetFillColor(168, 208, 141);
            $pdf->MultiCell(140, 7, 'Travaux d’ordre non électrique', 1, 'C', 1, 1, $x += 35, $y, true, 0, false, true, 0, 'M', true);
            //row3
            $x = 15;
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetTextColor(83, 129, 53);
            $pdf->SetFont($calibri, '', 10.5);
            $pdf->MultiCell(35, 10, 'Exécutant', 1, 'C', 1, 1, $x, $y += 7, true, 0, false, true, 0, 'M', true);
            $pdf->SetFont($calibri, '', 9.5);
            $pdf->MultiCell(35, 10, $a1, 1, 'C', 1, 1, $x += 35, $y, true, 0, false, true, 0, 'M', true);
            $pdf->MultiCell(35, 10, $a2, 1, 'C', 1, 1, $x += 35, $y, true, 0, false, true, 0, 'M', true);
            $pdf->MultiCell(35, 10, $a3, 1, 'C', 1, 1, $x += 35, $y, true, 0, false, true, 0, 'M', true);
            $pdf->MultiCell(35, 10, $a4, 1, 'C', 1, 1, $x += 35, $y, true, 0, false, true, 0, 'M', true);
            //row4
            $x = 15;
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetFont($calibri, '', 10.5);
            $pdf->MultiCell(35, 10, 'Chargé de chantier', 1, 'C', 1, 1, $x, $y += 10, true, 0, false, true, 0, 'M', true);
            $pdf->SetFont($calibri, '', 9.5);
            $pdf->MultiCell(35, 10, $b1, 1, 'C', 1, 1, $x += 35, $y, true, 0, false, true, 0, 'M', true);
            $pdf->MultiCell(35, 10, $b2, 1, 'C', 1, 1, $x += 35, $y, true, 0, false, true, 0, 'M', true);
            $pdf->MultiCell(35, 10, $b3, 1, 'C', 1, 1, $x += 35, $y, true, 0, false, true, 0, 'M', true);
            $pdf->MultiCell(35, 10, $b4, 1, 'C', 1, 1, $x += 35, $y, true, 0, false, true, 0, 'M', true);
            //row5
            $x = 15;
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->MultiCell(35, 7, '', 1, 'C', 1, 1, $x, $y += 10, true, 0, false, true, 0, 'M', true);
            $pdf->SetFillColor(168, 208, 141);
            $pdf->MultiCell(140, 7, 'Opérations d’ordre électrique', 1, 'C', 1, 1, $x += 35, $y, true, 0, false, true, 0, 'M', true);
            //row6
            $x = 15;
            $pdf->SetTextColor(83, 129, 53);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetFont($calibri, '', 10.5);
            $pdf->MultiCell(35, 10, 'Exécutant', 1, 'C', false, 1, $x, $y += 7, true, 0, false, true, 0, 'M', true);
            $pdf->SetFont($calibri, '', 9.5);
            $pdf->MultiCell(35, 10, $c1, 1, 'C', 1, 1, $x += 35, $y, true, 0, false, true, 0, 'M', true);
            $pdf->MultiCell(35, 10, $c2, 1, 'C', 1, 1, $x += 35, $y, true, 0, false, true, 0, 'M', true);
            $pdf->MultiCell(35, 10, $c3, 1, 'C', 1, 1, $x += 35, $y, true, 0, false, true, 0, 'M', true);
            $pdf->MultiCell(35, 10, $c4, 1, 'C', 1, 1, $x += 35, $y, true, 0, false, true, 0, 'M', true);
            //row7
            $x = 15;
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetFont($calibri, '', 10.5);
            $pdf->MultiCell(35, 10, 'Chargé de chantier', 1, 'C', 1, 1, $x, $y += 10, true, 0, false, true, 0, 'M', true);
            $pdf->SetFont($calibri, '', 9.5);
            $pdf->MultiCell(35, 10, $d1, 1, 'C', 1, 1, $x += 35, $y, true, 0, false, true, 0, 'M', true);
            $pdf->MultiCell(35, 10, $d2, 1, 'C', 1, 1, $x += 35, $y, true, 0, false, true, 0, 'M', true);
            $pdf->MultiCell(35, 10, $d3, 1, 'C', 1, 1, $x += 35, $y, true, 0, false, true, 0, 'M', true);
            $pdf->MultiCell(35, 10, $d4, 1, 'C', 1, 1, $x += 35, $y, true, 0, false, true, 0, 'M', true);
            //row8
            $x = 15;
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetFont($calibri, '', 10.5);
            $pdf->MultiCell(35, 10, 'Chargé d’intervention BT', 1, 'C', 1, 1, $x, $y += 10, true, 0, false, true, 0, 'M', true);
            $pdf->SetFont($calibri, '', 9.5);
            $pdf->MultiCell(35, 10, $e1, 1, 'C', 1, 1, $x += 35, $y, true, 0, false, true, 0, 'M', true);
            $pdf->MultiCell(35, 10, $e2, 1, 'C', 1, 1, $x += 35, $y, true, 0, false, true, 0, 'M', true);
            $pdf->MultiCell(35, 10, $e3, 1, 'C', 1, 1, $x += 35, $y, true, 0, false, true, 0, 'M', true);
            $pdf->MultiCell(35, 10, $e4, 1, 'C', 1, 1, $x += 35, $y, true, 0, false, true, 0, 'M', true);
            //row9
            $x = 15;
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetFont($calibri, '', 10.5);
            $pdf->MultiCell(35, 10, 'Chargé de consignation', 1, 'C', 1, 1, $x, $y += 10, true, 0, false, true, 0, 'M', true);
            $pdf->SetFont($calibri, '', 9.5);
            $pdf->MultiCell(35, 10, $f1, 1, 'C', 1, 1, $x += 35, $y, true, 0, false, true, 0, 'M', true);
            $pdf->MultiCell(35, 10, $f2, 1, 'C', 1, 1, $x += 35, $y, true, 0, false, true, 0, 'M', true);
            $pdf->MultiCell(35, 10, $f3, 1, 'C', 1, 1, $x += 35, $y, true, 0, false, true, 0, 'M', true);
            $pdf->MultiCell(35, 10, $f4, 1, 'C', 1, 1, $x += 35, $y, true, 0, false, true, 0, 'M', true);
            //row9
            $x = 15;
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetFont($calibri, '', 10.5);
            $pdf->MultiCell(35, 10, 'Chargé d’opérations spécifiques', 1, 'C', 1, 1, $x, $y += 10, true, 0, false, true, 0, 'M', true);
            $pdf->SetFont($calibri, '', 9.5);
            $pdf->MultiCell(35, 10, $g1, 1, 'C', 1, 1, $x += 35, $y, true, 0, false, true, 0, 'M', true);
            $pdf->MultiCell(35, 10, $g2, 1, 'C', 1, 1, $x += 35, $y, true, 0, false, true, 0, 'M', true);
            $pdf->MultiCell(35, 10, $g3, 1, 'C', 1, 1, $x += 35, $y, true, 0, false, true, 0, 'M', true);
            $pdf->MultiCell(35, 10, $g4, 1, 'C', 1, 1, $x += 35, $y, true, 0, false, true, 0, 'M', true);
            //row10
            $x = 15;
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetFont($calibri, '', 10.5);
            $pdf->MultiCell(35, 10, 'Habilité spécial', 1, 'C', 1, 1, $x, $y += 10, true, 0, false, true, 0, 'M', true);
            $pdf->SetFont($calibri, '', 9.5);
            $pdf->MultiCell(35, 10, $h1, 1, 'C', 1, 1, $x += 35, $y, true, 0, false, true, 0, 'M', true);
            $pdf->MultiCell(35, 10, $h2, 1, 'C', 1, 1, $x += 35, $y, true, 0, false, true, 0, 'M', true);
            $pdf->MultiCell(35, 10, $h3, 1, 'C', 1, 1, $x += 35, $y, true, 0, false, true, 0, 'M', true);
            $pdf->MultiCell(35, 10, $h4, 1, 'C', 1, 1, $x += 35, $y, true, 0, false, true, 0, 'M', true);
            //fin table
            $x = 85;
            $pdf->SetTextColor(123, 123, 123);
            $pdf->SetFont($calibri, '', 11);
            $pdf->MultiCell(35, 5, 'AVIS', 0, 'C', 0, 1, $x, $y += 11);
            $pdf->SetTextColor(0, 0, 0);
            $x = 15;
            $pdf->SetFont($calibri, '', 9.5);
            $parg1 = "Le <strong>titre d’habilitation</strong> est <strong>valable</strong> que s’il est <strong>établi</strong> et <strong>signé</strong> par <strong>l’Employeur</strong> et remis à l’intéressé qui doit également le signer.Ce titre est <strong>strictement personnel</strong> & ne peut être utilisé par un 1/3.Le titulaire doit être porteur de ce titre pendant les heures de travail ou le conserver à sa portée é être en mesure de le présenter sur demande motivée.La perte éventuelle de ce doit être signalée immédiatement au supérieur hiérarchique.Ce titre doit comporter les indications précises correspondant aux 3 caractéristiques de l’attribut";
            $parg2 = "composant le symbole de chaque habilitation et celles relatives aux activités que le personnel sera autorisé à pratiquer. La rubrique « indications supplémentaires » doit obligatoirement être remplie <br/> <strong>Cette habilitation n’autorise pas à elle seule son titulaire à effectuer de son propre chef les opérations pour lesquelles il est habilité. Il doit, en outre, être désigné par son responsable hiérarchique pour l’exécution de ces opérations.</strong>";

            $pdf->writeHTMLCell(85, $h, $x, $y += 6, $parg1, 0, 0, false, true, 'L', true);
            $pdf->writeHTMLCell(85, $h, 110, $y, $parg2, 0, 0, false, true, 'L', true);

            $x = 85;
            $pdf->SetFont($calibri, '', 11);
            $pdf->SetTextColor(123, 123, 123);
            $pdf->MultiCell(35, 5, 'DELIVRE PAR :', 0, 'C', 0, 1, $x, $y += 39);

            $x = 10;
            $pdf->SetFont($calibri, '', 11);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->MultiCell(190, 5, '', 'T', 'C', 0, 1, $x, 263);
            $pdf->MultiCell(5, 40, '', 'R', 'C', 0, 1, 100, 240);
            $x = 15;
            $y1 = $y;
            $pdf->SetFont($calibrib, '', 12);
            $pdf->MultiCell(35, 5, 'Employeur :', 0, 'L', 0, 1, $x, $y += 5);
            $pdf->TextField('employeur', 50, 5, '', '', $x + 35, $y);
            $pdf->SetFont($calibri, '', 12);
            $pdf->MultiCell(35, 5, 'Nom signataire :', 0, 'L', 0, 1, $x, $y += 7);
            $pdf->TextField('nomsignataire', 50, 5, '', '', $x + 35, $y);
            $pdf->MultiCell(35, 5, 'Adresse :', 0, 'L', 0, 1, $x, $y += 7);
            $pdf->TextField('adresse', 50, 5, '', '', $x + 35, $y);
            $pdf->MultiCell(35, 5, 'CP & Ville :', 0, 'L', 0, 1, $x, $y += 7);
            $pdf->TextField('cpville', 50, 5, '', '', $x + 35, $y);


            $y = $y1;
            $pdf->SetFont($calibrib, '', 12);
            $pdf->MultiCell(70, 5, 'Pour :', 0, 'C', 0, 1, $x += 100, $y += 5);
            $pdf->SetFont($calibri, '', 12);
            $x = 107;
            //TextField($name, $w, $h, $prop = array(), $opt = array(), $x = '', $y = '', $js = false)
            $pdf->MultiCell(35, 5, 'Nom & Prénom :', 0, 'L', 0, 1, $x, $y += 5);
            $pdf->MultiCell(190, 5, html_entity_decode($nomApp) . " " . html_entity_decode($prenomApp), '', 'L', 0, 1, $x + 35, $y);
            $pdf->MultiCell(35, 5, 'Fonction :', 0, 'L', 0, 1, $x, $y += 7);
            $pdf->TextField('fonction', 50, 5, '', '', $x + 35, $y);

            $pdf->MultiCell(100, 5, 'Signature responsable :', 0, 'L', 0, 1, 40, 262);
            $pdf->MultiCell(100, 5, 'Signature Salarié :', 0, 'L', 0, 1, 135, 262);

            //footer
            $x = 50;
            $pdf->SetTextColor(138, 138, 138);
            $pdf->SetFont($calibri, '', 10);
            $pdf->MultiCell(100, 5, '231 rue St Honoré – 75001 PARIS Tél. : 01.84.16.38.25', 0, 'C', 0, 1, $x, 285);
            $pdf->MultiCell(100, 5, 'www.habilitations-electrique.fr', 0, 'C', 0, 1, $x, 290);
            //doc Habilitation fin
        }
    }

}
