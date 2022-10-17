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
            /* uni_cnfsecrm - v2 - modif 100 - DEBUT */
            $calibri = TCPDF_FONTS2::addTTFfont('test/font/Calibri/calibri.ttf', 'TrueTypeUnicode', '', 96);
            //font calibri bold
            $calibrib = TCPDF_FONTS2::addTTFfont('test/font/Calibri/calibrib.ttf', 'TrueTypeUnicode', '', 96);
            //font calibri italic
            $calibrii = TCPDF_FONTS2::addTTFfont('test/font/Calibri/calibrii.ttf', 'TrueTypeUnicode', '', 96);
            //font calibri bold/italic
            $calibriz = TCPDF_FONTS2::addTTFfont('test/font/Calibri/calibriz.ttf', 'TrueTypeUnicode', '', 96);
            /* uni_cnfsecrm - v2 - modif 100 - FIN */
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
            /* uni_cnfsecrm - v2 - modif 176 - DEBUT */
            $matricule = $modelColumn0['info_apprenants'][0]['matricule'];
            $nomprenom = $civilite . " " . $nom . " " . $prenom;
            if ($matricule != "") {
                $nomprenom .= " ( " . $matricule . " )";
            }
            /* uni_cnfsecrm - v2 - modif 176 - FIN */
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
            //$logo = $pdf->Image('http://94.23.214.76/cnfsecrm/test/upload/images/logo.png', 75, 10, 50, 20);
            $article = '<p style="text-align:center">(application de l\'article R. 554-31 du code de l\'environnement <br/> et des articles 21 et 22 de son arrêté d\'application du 15 février 2012 modifié) </p>';
            $question = '<strong>Domaine de compétence couvert par l\'attestation :</strong>';
            $soussigne = 'Je soussigné ' . $formateur . ' Formateur, <br/><br/>Atteste que<strong>' . $nomprenom . ' </strong>a suivi l\'examen tenu le ' . $date_debut . '<br/> relatif au domaine de compétences susmentionné, <br/>sous le n° de ticket d\'examen [' . $ticket_examen . '] <span style="background-color:#FFF000;"><strong>a ' . $resultat_value . ' cet examen</strong></span>.';
            $attestation = '<strong>La présente attestation est valable pour une durée de 5 ans à compter de la <br/> date de réussite à l\'examen mentionnée ci-dessus, ou du ' . $date_reussi . ' si la <br/> date de réussite à l\'examen est antérieure au ' . $date_reussi . '.<br/><br/> Elle permet la délivrance par l\'employeur d\'une autorisation d\'intervention à <br/> proximité des réseaux (AIPR), dont le délai de validité ne peut dépasser celui<br/> de la présente attestation.</strong>';
            $nota = 'Nota : la présente attestation n\'a pas de valeur pour l\'application d\'autres réglementations que celle mentionnée dans le<br/> titre ; elle ne dispense pas non plus des autorisations nécessaires le cas échéant pour l\'accès aux ouvrages des exploitants.';
            $fait = 'Fait à ' . $ville . ', le ' . $date . '';
            $signature = '<img src="test/Signature/Signature.jpg" height="60" width="110">';
            $footer = '<p style="text-align:center;color:#808080">Centre National de Formation en Sécurité et Environnement<br/> 231 Rue Saint Honoré 75001 Paris - RCS : Paris 482.379.302– APE 8559A <br/> Tél : 01.84.16.38.25 - http://habilitations-electrique.fr - e-mail : contact@cnfse.fr </p>';

            if ($type_formation == "HABILITATIONS") {

                /* uni_cnfsecrm - v2 - modif 108 - DEBUT */
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
                    /* uni_cnfsecrm - v2 - modif 115 - DEBUT */
                    $bs_be_he_he = $modelColumn0['info_apprenants'][0]["bs_be_he_he"];
                    /* uni_cnfsecrm - v2 - modif 115 - FIN */

                    $nomForm .= $bs_be_he_b0 == 1 ? "B0 " : "";
                    $nomForm .= $bs_be_he_h0v == 1 ? "H0 H0v " : "";
                    $nomForm .= $bs_be_he_bs == 1 ? "BS " : "";

                    /* uni_cnfsecrm - v2 - modif 115 - DEBUT */
                    if ($bs_be_he_manoeuvre == 1) {
                        if ($bs_be_he_he == 1) {
                            $nomForm .= "BE/HE manœuvre";
                        } else {
                            $nomForm .= "BE manœuvre";
                        }
                    }
                    /* uni_cnfsecrm - v2 - modif 115 - FIN */
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

                    /* uni_cnfsecrm - v2 - modif 115 - DEBUT */
                    $b1v_b2v_bc_br_he = $modelColumn0['info_apprenants'][0]["b1v_b2v_bc_br_he"];
                    /* uni_cnfsecrm - v2 - modif 115 - FIN */

                    $nomForm .= $b1v_b2v_bc_br_b0 == 1 ? "B0 " : "";
                    $nomForm .= $b1v_b2v_bc_br_h0v == 1 ? "H0 H0v " : "";
                    $nomForm .= $b1v_b2v_bc_br_bs == 1 ? "BS " : "";

                    /* uni_cnfsecrm - v2 - modif 115 - DEBUT */
                    if ($b1v_b2v_bc_br_manoeuvre == 1) {
                        if ($b1v_b2v_bc_br_he == 1) {
                            $nomForm .= "BE/HE manœuvre ";
                        } else {
                            $nomForm .= "BE manœuvre ";
                        }
                    }
                    /* uni_cnfsecrm - v2 - modif 115 - FIN */

                    $nomForm .= $b1v_b2v_bc_br_b1v == 1 ? "B1v " : "";
                    $nomForm .= $b1v_b2v_bc_br_b2v == 1 ? "B2v " : "";
                    $nomForm .= $b1v_b2v_bc_br_bc == 1 ? "BC " : "";
                    $nomForm .= $b1v_b2v_bc_br_br == 1 ? "BR " : "";

                    /* uni_cnfsecrm - v2 - modif 115 - DEBUT */
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
                    /* uni_cnfsecrm - v2 - modif 115 - FIN */
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

                    /* uni_cnfsecrm - v2 - modif 115 - DEBUT */
                    $b1v_b2v_bc_br_h1v_h2v_he = $modelColumn0['info_apprenants'][0]["b1v_b2v_bc_br_h1v_h2v_he"];
                    /* uni_cnfsecrm - v2 - modif 115 - FIN */

                    $nomForm .= $b1v_b2v_bc_br_h1v_h2v_b0 == 1 ? "B0 " : "";
                    $nomForm .= $b1v_b2v_bc_br_h1v_h2v_h0v == 1 ? "H0 H0v " : "";
                    $nomForm .= $b1v_b2v_bc_br_h1v_h2v_bs == 1 ? "BS " : "";
                    /* uni_cnfsecrm - v2 - modif 115 - DEBUT */
                    if ($b1v_b2v_bc_br_h1v_h2v_manoeuvre == 1) {
                        if ($b1v_b2v_bc_br_h1v_h2v_he == 1) {
                            $nomForm .= "BE/HE manœuvre ";
                        } else {
                            $nomForm .= "BE manœuvre ";
                        }
                    }
                    /* uni_cnfsecrm - v2 - modif 115 - FIN */
                    $nomForm .= $b1v_b2v_bc_br_h1v_h2v_b1v == 1 ? "B1v " : "";
                    $nomForm .= $b1v_b2v_bc_br_h1v_h2v_b2v == 1 ? "B2v " : "";
                    $nomForm .= $b1v_b2v_bc_br_h1v_h2v_bc == 1 ? "BC " : "";
                    $nomForm .= $b1v_b2v_bc_br_h1v_h2v_br == 1 ? "BR " : "";

                    /* uni_cnfsecrm - v2 - modif 115 - DEBUT */
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
                    /* uni_cnfsecrm - v2 - modif 115 - FIN */
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

                /* uni_cnfsecrm - v2 - modif 108 - FIN */

                //variable categorie 1
                $text = '<p><strong>' . $nomprenom . ' </strong>a suivi la formation habilitation électrique du  <strong>' . $date_debut . '</strong> au<br/> <strong>' . $date_fin . '</strong>, pour une durée de <strong>' . $nbr_jours . '</strong> jour, au sein de notre organisme de formation <br/> Formation au vue d\'une <strong> ' . $nom_formation_value . ' </strong> <br/> Au cours de cette formation<strong> ' . $civilite . ' ' . $prenom . ' ' . $nom . ' </strong>a acquis les connaissances et le<br/> savoir-faire nécessaires pour prendre en compte le risque électrique dans le cadre<br/> d\'opérations d\'ordre électriques ou non électrique et se prémunir de tout accident susceptible <br/> d\'être encouru.</p>';
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
//                    if (strstr($nom_formation, "B0")) {
//                        array_push($array_a1, "B0");
//                        array_push($array_a2, "BT");
//                    }
//                    if (strstr($nom_formation, "H0")) {
//                        array_push($array_a1, "H0");
//                        array_push($array_a2, "HTA");
//                    }
//                    if (strstr($nom_formation, "H0V")) {
//                        array_push($array_a1, "H0V");
//                        if (!in_array("HTA", $array_a2))
//                            array_push($array_a2, "HTA");
//                    }

                /* uni_cnfsecrm - v2 - modif 108 - DEBUT */
                $a1 = $taba1b1;
                /* uni_cnfsecrm - v2 - modif 108 - FIN */
//                $a2 = "BT-TBT-HTA";
                /* test-01-debut */
//                $a2 = "BT-TBT-HTA";
                if ( $b0_h0_h0v_h0v == 1 || $b1v_b2v_bc_br_h1v_h2v_he == 1 || $b1v_b2v_bc_br_h1v_h2v_hc == 1 || $b1v_b2v_bc_br_h1v_h2v_h2v == 1 || $b1v_b2v_bc_br_h1v_h2v_h1v == 1 || $b1v_b2v_bc_br_h1v_h2v_h0v == 1) {
                    $a2 = "TBT-BT-HTA";
                } else {
                    $a2 = "TBT-BT";
                }
                /* test-01-fin */
                $a3 = 'Accès Toutes installations électriques';
                $a4 = '';

                $array_b1 = array();
                $array_b2 = array();
//                if (strstr($nom_formation, "B0")) {
//                    array_push($array_b1, "B0");
//                    array_push($array_b2, "BT-TBT");
//                }
//                if (strstr($nom_formation, "H0")) {
//                    array_push($array_b1, "H0");
//                    array_push($array_b2, "HTA");
//                }
//                if (strstr($nom_formation, "H0V")) {
//                    array_push($array_b1, "H0V");
//                    if (!in_array("HTA", $array_b2))
//                        array_push($array_b2, "HTA");
//                }
                /* uni_cnfsecrm - v2 - modif 108 - DEBUT */
                $b1 = $taba1b1;
                /* uni_cnfsecrm - v2 - modif 108 - FIN */
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
                /* uni_cnfsecrm - v2 - modif 144 - DEBUT */
                $array_c1 = array();
                $array_c2 = array();
                $array_liste_c_b = array("B1V", "B1v");
                $array_liste_c_h = array("H1V", "H1v");
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

                foreach ($array_liste_c_h as $key => $value) {
                    if (strstr($nom_formation_value, $value)) {
                        array_push($array_f1, $value);
                        if (!in_array("HTA", $array_f2))
                            array_push($array_f2, "HTA");
                    }
                }
                /* uni_cnfsecrm - v2 - modif 144 - FIN */
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
        }
    }

}
