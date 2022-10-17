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

class Vtiger_PDF_EventsAttesHeaderViewer extends Vtiger_PDF_HeaderViewer {

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
            $pdf->setPageFormat('A4', 'L');
            $pdf->setPageOrientation('LANDSCAPE', '', 0);

            $modelColumns = $this->model->get('columns');
            // Column 1
            $offsetX = 5;
            $modelColumn0 = $modelColumns[0];

            // font calibri simple
            /* uni_cnfsecrm - v2 - modif 100 - DEBUT */
            $calibri = TCPDF_FONTS2::addTTFfont('test/font/Calibri/calibri.ttf', 'TrueTypeUnicode', '', 96);
            //font calibri bold
            $calibrib = TCPDF_FONTS2::addTTFfont('test/font/Calibri/calibrib.ttf', 'TrueTypeUnicode', '', 96);
            //font calibri italic
            $calibrii = TCPDF_FONTS2::addTTFfont('test/font/Calibri/calibrii.ttf', 'TrueTypeUnicode', '', 96);
            //font calibri bold/italic
            $calibriz = TCPDF_FONTS2::addTTFfont('test/font/Calibri/calibriz.ttf', 'TrueTypeUnicode', '', 96);
            /* uni_cnfsecrm - v2 - modif 100 - DEBUT */
            // Column 1
            //page 1 
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
            /* uni_cnfsecrm - v2 - modif 176 - DEBUT */
            $matricule = $modelColumn0['info_apprenants'][0]['matricule'];
            $nomprenom = $salutation . " " . $nom . " " . $prenom;
            if ($matricule != "") {
                $nomprenom .= " ( " . $matricule . " )";
            }
            /* uni_cnfsecrm - v2 - modif 176 - FIN */
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
            $testprerequis = $modelColumn0['info_apprenants'][0]["testprerequis"];
            $electricien = $modelColumn0['info_apprenants'][0]["electricien"];

            $nom_formation_value = $nom_formation;
            $nom_formation_value .= ($be_essai == "1") ? " BE essai" : "";
            $nom_formation_value .= ($be_mesurage == "1") ? " BE mesurage" : "";
            $nom_formation_value .= ($be_verification == "1") ? " BE vérification" : "";
            $nom_formation_value .= ($be_manoeuvre == "1") ? " BE manoeuvre" : "";
            $nom_formation_value .= ($he_essai == "1") ? " HE essai" : "";
            $nom_formation_value .= ($he_mesurage == "1") ? " HE mesurage" : "";
            $nom_formation_value .= ($he_verification == "1") ? " HE vérification" : "";
            $nom_formation_value .= ($he_manoeuvre == "1") ? " HE manoeuvre" : "";

            $text_haccp = "";
            //var_dump($modelColumn0);
            $formation = $modelColumn0["nom_formation"];
            $type_formation = $modelColumn0["categorie_formation"];
            $text_resultat = 'Résultats de l\'évaluation des acquis : ' . $resultat_value;
            $anniv_test = ($datenaissance != "") ? " né(e) le " . formatDateFr($datenaissance) : "";

            $address = $modelColumn0["cp_formation"];
            //recuperer le region appartir de code postale
            $data = array('address' => '', 'lat' => '', 'lng' => '', 'city' => '', 'department' => '', 'region' => '', 'country' => '', 'postal_code' => '');
            //on formate l'adresse
            $address = str_replace(" ", "+", $address);
            if ($address != "8000" && $address != "67606") {
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
            } else if ($address == "8000" || $address == "67606") {
                $data['region'] = "Grand Est";
            }

            $data['region'] = str_replace("-", " ", $data['region']);
//            var_dump($data['region']);die();
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
                    $info_etudiant = '<p style="text-align:center">' . $nomprenom . '</p>';
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
                    $nom_formation = $nomForm;

                    /* uni_cnfsecrm - v2 - modif 108 - FIN */
                    $titre = '<p style="text-align:center">Attestation de Formation <br/> en Habilitation Electrique</p>';
                    $image1 = $pdf->Image('test/upload/images/gauche.jpg', 25, 16, 45, 40);
                    $logo = $pdf->Image('test/logo/logo-CNFSE-large.png', 110, 20, 70, 24);
                    $image2 = $pdf->Image('test/upload/images/droite.jpg', 230, 16, 40, 40);
                    $soussigne = '<p style="text-align:center">Nous soussigné C.N.F.S.E. attestons que, conformément aux dispositions de l\'article L.6353-1 du code du Travail :</p>';
                    $info_etudiant = '<p style="text-align:center">' . $nomprenom . '</p>';
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
                case 'BUREAUTIQUE-INFORMATIQUE':
                    $titre = '<p style="text-align:center">Attestation de Formation Bureautique informatique</p>';
                    $soussigne = '<p style="text-align:center">Nous soussigné C.N.F.S.E. certifions par la présente que :</p>';
                    $logo = $pdf->Image('test/logo/logo-CNFSE-large.png', 110, 20, 70, 24);
                    $info_etudiant = '<p style="text-align:center">' . $nomprenom . '</p>';
                    $a_suivi = '<p style="text-align:center">a suivi la formation ' . $formation . ' <br/>Cette formation s\'est déroulée du  ' . $date_debut_formation . ' au ' . $date_fin_formations . ' <br/>d\'une durée totale de ' . $nbr_heure . ' heures sur ' . $nbr_jour . ' ' . $label_jour . '.</p>';
                    $text = 'Les objectifs de formation visés sont de maîtriser les fonctionnalités importantes et utiles d\'Excel. Construire des tableaux de données et de calculs. Mettre en place des formules simples et élaborées pour automatiser les calculs. Exploiter les outils de mise en forme pour gagner du temps dans la présentation des tableaux. Définir des liaisons pour fiabiliser les mises à jour. Exploiter les outils de listes de données, les tableaux croisés dynamiques.';
                    $l1 = 55;
                    $l2 = 70;
                    $l3 = 80;
                    $l4 = 90;
                    $l5 = 105;
                    $l6 = 125;
                    $l7 = 150;
                    $l8 = 160;
                    $l9 = 175;
                    break;
                case 'HYGIENE':
                    $titre = '<p style="text-align:center">Attestation de Formation HACCP</p>';
                    $img1 = "test/upload/attestation_hygiene.png";
                    $image1 = $pdf->Image('test/upload/attestation_hygiene2.png', 25, 18, 45, 40);
                    $logo = $pdf->Image('test/logo/logo-CNFSE-large.png', 110, 20, 70, 24);
                    $soussigne = '<p style="text-align:center">Nous soussigné C.N.F.S.E. numéro enregistrement rofhya ' . $region . ' ' . $code_rofhya . ' certifions par la présente que :</p>';
                    $info_etudiant = '<p style="text-align:center">' . $nomprenom . $anniv_test . ' </p>';
                    $a_suivi = '<p style="text-align:center">a suivi la formation spécifique en hygiène alimentaire adaptée à l\'activité des établissements de restauration commerciale & collective <br/>Cette formation s\'est déroulée du ' . $date_debut_formation . ' au ' . $date_fin_formations . ', <br/>d\'une durée totale de ' . $nbr_heure . ' heures sur ' . $nbr_jour . ' ' . $label_jour . '. </p>';
                    $text = 'Les objectifs de formation visés sont d\'acquérir les capacités nécessaires pour organiser et gérer leurs activités dans des conditions d\'hygiène conformes aux attendus de la réglementation et permettant la satisfaction du client. Il est constitué d\'un référentiel de capacités qui identifie les activités que les stagiaires doivent être capables de réaliser à l\'issue de la formation.';
                    $text_haccp = "(Décret n°2011-731 du 24 juin 2011 relatif à l'obligation de formation en matière d'hygiène alimentaire de certains établissements de restauration commerciale et arrêté du 5 octobre 2011 relatif au cahier des charges de la formation spécifique en matière d'hygiène alimentaire adaptée à l'activité des établissements de restauration commerciale)";
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
                default :
                    $titre = '<p style="text-align:center">Attestation de Formation <br/> ' . $formation . '</p>';
                    //$img1 = "test/upload/attestation_hygiene.png";
                    //$image1 = $pdf->Image('test/upload/attestation_hygiene2.png', 25, 18, 45, 40);
                    $logo = $pdf->Image('test/logo/logo-CNFSE-large.png', 110, 20, 70, 24);
                    $soussigne = '<p style="text-align:center">Nous soussigné Centre de formation C.N.F.S.E. certifions par la présente que :</p>';
                    $info_etudiant = '<p style="text-align:center">' . $nomprenom . $anniv_test . ' </p>';
                    $a_suivi = '<p style="text-align:center">a suivi la formation ' . $formation . ' <br/>Cette formation s\'est déroulée du ' . $date_debut_formation . ' au ' . $date_fin_formations . ', <br/>d\'une durée totale de ' . $nbr_heure . ' heures sur ' . $nbr_jour . ' ' . $label_jour . '. </p>';
                    $text = 'Les objectifs de formation visés sont de renforcer l\'employabilité des personnes et de sécuriser leur parcours.';
                    //$text_haccp = "(Décret n°2011-731 du 24 juin 2011 relatif à l'obligation de formation en matière d'hygiène alimentaire de certains établissements de restauration commerciale et arrêté du 5 octobre 2011 relatif au cahier des charges de la formation spécifique en matière d'hygiène alimentaire adaptée à l'activité des établissements de restauration commerciale)";
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
            //$pdf->MultiCell(190, 5, $titre, 0, 'C', 0, 1);
            $pdf->writeHTML($titre, true, false, true, false, '');

            //text haccp
            $pdf->SetXY($x + 40, $y + $l2);
            $pdf->SetFont($calibrib, '', 12);
            $pdf->SetTextColor(127, 127, 127);
            $pdf->MultiCell(220, 5, $text_haccp, 0, 'L', 0, 1);
            //$pdf->writeHTML($text_haccp, true, false, true, false, '');

            $pdf->SetXY($x, $y + $l3);
            $pdf->SetFont($calibrib, '', 12);
            $pdf->SetTextColor(0, 0, 0);
            //$pdf->MultiCell(170, 5, $soussigne, 0, 'C', 0, 1);
            $pdf->writeHTML($soussigne, true, false, true, false, '');

            $pdf->SetXY($x, $y + $l4);
            $pdf->SetTextColor(47, 84, 150);
            $pdf->SetFont($calibrib, '', 20);
            //$pdf->MultiCell(190, 5, $salutation . ' ' . $prenom . ' ' . $nom, 0, 'C', 0, 1);
            $pdf->writeHTML($info_etudiant, true, false, true, false, '');

            //$pdf->SetXY($x, $y + $l5);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont($calibrib, '', 12);
            //$pdf->MultiCell(150, 5, 'a suivi la formation ', 0, 'C', 0, 1);
            //$pdf->writeHTML($a_suivi, true, false, true, false, '');
            $pdf->writeHTMLCell(200, 100, $x + 50, $y + $l5, $a_suivi, '');

            $pdf->SetXY($x + 40, $y + $l6);
            $pdf->SetTextColor(128, 128, 128);
            $pdf->SetFont($calibrib, '', 11);
            $pdf->MultiCell(210, 5, $text, 0, 'L', 0, 1);

//            if ($electricien == 0 && $type_formation == "HABILITATIONS" && $testprerequis == "1") {
//                $text_non_electricien = 'Nous préconisons conformément à la NF C 18-510 § 4.5.2 une formation "électricité professionnelle".';
//                $pdf->SetXY($x + 40, $y + $l7 - 10);
//                $y = $y + 2;
//                $pdf->SetTextColor(128, 128, 128);
//                $pdf->SetFont($calibrib, '', 11);
//                $pdf->MultiCell(210, 5, $text_non_electricien, 0, 'L', 0, 1);
//            } elseif ($electricien == 0 && $type_formation == "HABILITATIONS" && $testprerequis == "0") {
//                $text_non_electricien = 'Au vue des résultats du test de prérequis effectué en début de la formation, nous préconisons une mise à jour "électricité professionnelle"  § 4.5.2 et annexe NFC 18-510.';
//                $pdf->SetXY($x + 40, $y + $l7 - 10);
//                $y = $y + 2;
//                $pdf->SetTextColor(128, 128, 128);
//                $pdf->SetFont($calibrib, '', 11);
//                $pdf->MultiCell(210, 5, $text_non_electricien, 0, 'L', 0, 1);
//            } elseif ($electricien == 1 && $type_formation == "HABILITATIONS" && $testprerequis == "1") {
//                $text_non_electricien = 'Test de prérequis réussi conformément à la NFC 18-510.';
//                $pdf->SetXY($x + 40, $y + $l7 - 10);
//                $y = $y + 2;
//                $pdf->SetTextColor(128, 128, 128);
//                $pdf->SetFont($calibrib, '', 11);
//                $pdf->MultiCell(210, 5, $text_non_electricien, 0, 'L', 0, 1);
//            } elseif ($electricien == 1 && $type_formation == "HABILITATIONS" && $testprerequis == "0") {
//                $text_non_electricien = 'Au vue des résultats du test de prérequis effectué en début de la formation, nous préconisons une mise à jour "électricité professionnelle"  § 4.5.2 et annexe NFC 18-510.';
//                $pdf->SetXY($x + 40, $y + $l7 - 10);
//                $y = $y + 2;
//                $pdf->SetTextColor(128, 128, 128);
//                $pdf->SetFont($calibrib, '', 11);
//                $pdf->MultiCell(210, 5, $text_non_electricien, 0, 'L', 0, 1);
//            }

            $pdf->SetXY($x + 40, $y + $l7);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont($calibrib, '', 14);
            //$pdf->MultiCell(180, 5, 'Résultats de l\'évaluation des acquis : ' . $resultat, 0, 'L', 0, 1);
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
        }
    }

}
