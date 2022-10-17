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
/* uni_cnfsecrm - v2 - modif 145 - FILE */

class Vtiger_PDF_LISTETITREHABILITATIONSHeaderViewer extends Vtiger_PDF_HeaderViewer {

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
            $pdf->setPrintFooter(false);
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
            $getApprenants = $modelColumn0['getApprenants'];
            //var_dump($getApprenants);        
            $nbr_apprenants = $modelColumn0['getApprenants']['nbr_apprenants'];
            for ($j = 0; $j < $nbr_apprenants; $j++) {
                if ($modelColumn0['info_apprenants'][$j]["date_start_appr"] == '0000-00-00' || $modelColumn0['info_apprenants'][0]["date_start_appr"] == '' || $modelColumn0['info_apprenants'][0]["date_start_appr"] == '1970-01-01') {
                    $date_debut = $modelColumn0["date_debut_formation"];
                } else {
                    $date_start_appr = strtotime($modelColumn0['info_apprenants'][$j]["date_start_appr"]);
                    $date_debut = date('d/m/Y', $date_start_appr);
                }
                if ($modelColumn0['info_apprenants'][$j]["date_fin_appr"] == '0000-00-00' || $modelColumn0['info_apprenants'][0]["date_fin_appr"] == '' || $modelColumn0['info_apprenants'][0]["date_fin_appr"] == '1970-01-01') {
                    $date_fin = $modelColumn0["date_fin_formations"];
                } else {
                    $date_fin_appr = strtotime($modelColumn0['info_apprenants'][$j]["date_fin_appr"]);
                    $date_fin = date('d/m/Y', $date_fin_appr);
                }
                $prenom = $modelColumn0['info_apprenants'][$j]["firstname"];
                $nom = $modelColumn0['info_apprenants'][$j]["lastname"];
                $accountid = $modelColumn0['info_apprenants'][$j]["accountid"];
                $typeFormationHab = $modelColumn0['typeFormationHab'];
                $nomForm = "Habilitation électrique ";
                $taba1b1 = "";
                if ($typeFormationHab == "B0 H0 H0v") {
                    $b0_h0_h0v_b0 = $modelColumn0['info_apprenants'][$j]["b0_h0_h0v_b0"];
                    $b0_h0_h0v_h0v = $modelColumn0['info_apprenants'][$j]["b0_h0_h0v_h0v"];
                    $nomForm .= $b0_h0_h0v_b0 == 1 ? "B0 " : "";
                    $nomForm .= $b0_h0_h0v_h0v == 1 ? "H0 H0v " : "";
                } else if ($typeFormationHab == "BS BE HE") {
                    $bs_be_he_b0 = $modelColumn0['info_apprenants'][$j]["bs_be_he_b0"];
                    $bs_be_he_h0v = $modelColumn0['info_apprenants'][$j]["bs_be_he_h0v"];
                    $bs_be_he_bs = $modelColumn0['info_apprenants'][$j]["bs_be_he_bs"];
                    $bs_be_he_manoeuvre = $modelColumn0['info_apprenants'][$j]["bs_be_he_manoeuvre"];
                    $bs_be_he_he = $modelColumn0['info_apprenants'][$j]["bs_be_he_he"];
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
                    $b1v_b2v_bc_br_b0 = $modelColumn0['info_apprenants'][$j]["b1v_b2v_bc_br_b0"];
                    $b1v_b2v_bc_br_h0v = $modelColumn0['info_apprenants'][$j]["b1v_b2v_bc_br_h0v"];
                    $b1v_b2v_bc_br_bs = $modelColumn0['info_apprenants'][$j]["b1v_b2v_bc_br_bs"];
                    $b1v_b2v_bc_br_manoeuvre = $modelColumn0['info_apprenants'][$j]["b1v_b2v_bc_br_manoeuvre"];
                    $b1v_b2v_bc_br_b1v = $modelColumn0['info_apprenants'][$j]["b1v_b2v_bc_br_b1v"];
                    $b1v_b2v_bc_br_b2v = $modelColumn0['info_apprenants'][$j]["b1v_b2v_bc_br_b2v"];
                    $b1v_b2v_bc_br_bc = $modelColumn0['info_apprenants'][$j]["b1v_b2v_bc_br_bc"];
                    $b1v_b2v_bc_br_br = $modelColumn0['info_apprenants'][$j]["b1v_b2v_bc_br_br"];
                    $b1v_b2v_bc_br_essai = $modelColumn0['info_apprenants'][$j]["b1v_b2v_bc_br_essai"];
                    $b1v_b2v_bc_br_verification = $modelColumn0['info_apprenants'][$j]["b1v_b2v_bc_br_verification"];
                    $b1v_b2v_bc_br_mesurage = $modelColumn0['info_apprenants'][$j]["b1v_b2v_bc_br_mesurage"];
                    $b1v_b2v_bc_br_he = $modelColumn0['info_apprenants'][$j]["b1v_b2v_bc_br_he"];
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
                    $b1v_b2v_bc_br_h1v_h2v_b0 = $modelColumn0['info_apprenants'][$j]["b1v_b2v_bc_br_h1v_h2v_b0"];
                    $b1v_b2v_bc_br_h1v_h2v_h0v = $modelColumn0['info_apprenants'][$j]["b1v_b2v_bc_br_h1v_h2v_h0v"];
                    $b1v_b2v_bc_br_h1v_h2v_bs = $modelColumn0['info_apprenants'][$j]["b1v_b2v_bc_br_h1v_h2v_bs"];
                    $b1v_b2v_bc_br_h1v_h2v_manoeuvre = $modelColumn0['info_apprenants'][$j]["b1v_b2v_bc_br_h1v_h2v_manoeuvre"];
                    $b1v_b2v_bc_br_h1v_h2v_b1v = $modelColumn0['info_apprenants'][$j]["b1v_b2v_bc_br_h1v_h2v_b1v"];
                    $b1v_b2v_bc_br_h1v_h2v_b2v = $modelColumn0['info_apprenants'][$j]["b1v_b2v_bc_br_h1v_h2v_b2v"];
                    $b1v_b2v_bc_br_h1v_h2v_bc = $modelColumn0['info_apprenants'][$j]["b1v_b2v_bc_br_h1v_h2v_bc"];
                    $b1v_b2v_bc_br_h1v_h2v_br = $modelColumn0['info_apprenants'][$j]["b1v_b2v_bc_br_h1v_h2v_br"];
                    $b1v_b2v_bc_br_h1v_h2v_essai = $modelColumn0['info_apprenants'][$j]["b1v_b2v_bc_br_h1v_h2v_essai"];
                    $b1v_b2v_bc_br_h1v_h2v_verification = $modelColumn0['info_apprenants'][$j]["b1v_b2v_bc_br_h1v_h2v_verification"];
                    $b1v_b2v_bc_br_h1v_h2v_mesurage = $modelColumn0['info_apprenants'][$j]["b1v_b2v_bc_br_h1v_h2v_mesurage"];
                    $b1v_b2v_bc_br_h1v_h2v_h1v = $modelColumn0['info_apprenants'][$j]["b1v_b2v_bc_br_h1v_h2v_h1v"];
                    $b1v_b2v_bc_br_h1v_h2v_h2v = $modelColumn0['info_apprenants'][$j]["b1v_b2v_bc_br_h1v_h2v_h2v"];
                    $b1v_b2v_bc_br_h1v_h2v_hc = $modelColumn0['info_apprenants'][$j]["b1v_b2v_bc_br_h1v_h2v_hc"];
                    $b1v_b2v_bc_br_h1v_h2v_he = $modelColumn0['info_apprenants'][$j]["b1v_b2v_bc_br_h1v_h2v_he"];
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

                $array_not_tab1 = array("BR", "BC", "B2v", "B1v", "BS", "HC", "H2v", "H1v", "BE manœuvre", "BE essai", "BE mesurage", "BE vérification", "BE/HE essai", "BE/HE mesurage", "BE/HE vérification", "BE/HE manœuvre");
                $taba1b1 = str_replace("Habilitation électrique", "", $nomForm);
                $taba1b1 = str_replace($array_not_tab1, "", $taba1b1);

                //variable categorie 1
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
                //
                $pdf->setPageFormat('A4', 'P');
                $pdf->SetTextColor(0, 0, 0);
                $pdf->SetFont($calibri, '', 20);
                $pdf->SetDrawColor(0, 0, 0);
                $pdf->Image('test/logo/logo-CNFSE.jpg', 15, 10, 45, 17);
                $region = 'ile de france';
                if ($accountid == 119579) {
                    $pdf->Image('test/logo/region_ile_france.png', 140, 10, 45, 17);
                }

                if ($accountid == 173172) {
                    $pdf->Image('test/logo/RegionNormandie.png', 165, 10, 20, 17);
                }

                if ($accountid == 189380) {
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

                if ($j < $nbr_apprenants - 1) {
                    $pdf->addPage();
                }
            }
        }
    }

}
