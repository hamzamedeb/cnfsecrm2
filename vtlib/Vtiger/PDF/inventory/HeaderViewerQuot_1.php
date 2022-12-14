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
                    "test/logo/logo-CNFSE.png");
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
            $pdf->Image("test/logo/logo-CNFSE.png", 10, 15, $w, $h);
            //column 1 num devis

            // font trebuchet simple
            /* uni_cnfsecrm - v2 - modif 100 - DEBUT */
            $trebuchet = TCPDF_FONTS2::addTTFfont('test/font/Trebuchet.ttf', 'TrueTypeUnicode', '', 96);
            //font trebuchet italic
            $trebuchetmsitalic = TCPDF_FONTS2::addTTFfont('test/font/trebuchetmsitalic.ttf', 'TrueTypeUnicode', '', 96);
            //font trebuchet bold
            $trebuchetbd = TCPDF_FONTS2::addTTFfont('test/font/trebucbd.ttf', 'TrueTypeUnicode', '', 96);
            /* uni_cnfsecrm - v2 - modif 100 - FIN */
           
            
            $y = 70;
            //$pdf->SetXY(10, $image$yHeightInMM);
            //$pdf->MultiCell(100, 5,"test" , 0, 'L', 0, 1, 10, $y+100);
            $quote_no = $modelColumn0['quote_no'];
            $pdf->SetFont($trebuchetbd, '', 9);
            $pdf->MultiCell(100, 5, "DEVIS N?? " . $quote_no, 0, 'L', 0, 1, 10, $y);
            //column2 destination 


            $pdf->SetFont($trebuchet, '', 10);
            $salutation_contact = $modelColumn0['info_client']['salutation_contact'];
            $nom_contact = $modelColumn0['info_client']['nom_contact'];
            $date_quote = $modelColumn0['date_quote'];

            $nom_client = $modelColumn0['info_client']['accountname'];
            $adresse_client = $modelColumn0['info_client']['adresse'];
            $ville_client = $modelColumn0['info_client']['ville'];
            $cp_client = $modelColumn0['info_client']['cp'];

            $y = 60;
            $pdf->MultiCell(60, 5, $nom_client, 0, 'L', 0, 1, 110, $y);
            if ($nom_contact != "") {
                $y += 5;
                $pdf->MultiCell(60, 5, $nom_contact, 0, 'L', 0, 1, 110, $y);
            }
            $y += 5;
            $pdf->MultiCell(60, 5, $adresse_client, 0, 'L', 0, 1, 110, $y);
            $y += 5;
            $pdf->MultiCell(60, 5, $cp_client . " " . $ville_client, 0, 'L', 0, 1, 110, $y);
            $y += 10;
            $pdf->MultiCell(60, 5, "PARIS le " . $date_quote, 0, 'L', 0, 1, 110, $y);
            //sujet 
            $y = 95;
            $pdf->SetFont('times', '', 10);
            if ($salutation_contact != "") {
                $y += 5;
                $pdf->SetFont($trebuchet, '', 9);
                $pdf->MultiCell(200, 5, $salutation_contact . ",", 0, 'L', 0, 1, 10, $y);
            }
            $pdf->SetFont($trebuchet, '', 9); 
            $text_sujet = "Suite ?? votre demande, je vous prie de bien vouloir trouver notre proposition pour la formation suivante :";
            $pdf->MultiCell(200, 5, $text_sujet, 0, 'L', 0, 1, 10, $y + 5);
            $nom_formation = html_entity_decode($modelColumn0['info_product'][0]['servicename']);
            $pdf->SetFont($trebuchetbd, '', 9);
            $pdf->MultiCell(200, 5, $nom_formation, 0, 'L', 0, 1, 10, $y + 10);

            //les details 
            //bordure de tableau
            $pdf->SetDrawColor(0, 0, 0);
            $pdf->SetFillColor(255, 255, 255);

            //border 1              
            $pdf->SetXY(78, 130);
            $pdf->MultiCell(5, 35, '', 'L', 'L', 1, 1);
            //border 2              
            $pdf->SetXY(135, 130);
            $pdf->MultiCell(5, 35, '', 'L', 'L', 1, 1);

            $dates_formation_string = $modelColumn0['dates_formation_string'];
            $horaires_formation_string = $modelColumn0['horaires_formation_string'];
            $nbrjours = $modelColumn0['info_product'][0]['nbrjours'];
            $listprice = $modelColumn0['info_product'][0]['listprice'];
            $nbr_personne = intval($modelColumn0['info_product'][0]['quantity']);

            $y = 130;
            // detail column 1
            $pdf->SetXY(10, $y);
            $duree = $nbrjours;
            $horaires = 'de ' . $modelColumn0['info_dates'][0]["start_matin"] . ' h ?? ' . $modelColumn0['info_dates'][0]["end_matin"] . ' h et de ' . $modelColumn0['info_dates'][0]["start_apresmidi"] . ' h ?? ' . $modelColumn0['info_dates'][0]["end_apresmidi"] . ' h';
            $cout_journalier = $listprice;

            $dates_formation_string = ($dates_formation_string != "") ? $dates_formation_string : $modelColumn0['info_dates'][0]['date_start'];
            $lieu = $modelColumn0['ville_devis'];
            $tbl = <<<EOD
    <table border-left="1">
        <tr style="border-right: 1px solid red;"> 
            <td style="font-family: "Trebuchet MS", Helvetica, sans-serif;font-weight: normal;font-size: 8pt;" width="90">Dur??e:</td>
            <td style="font-family: "Trebuchet MS", Helvetica, sans-serif;font-weight: normal;font-size: 20pt;" width="90">$duree Jour(s)</td>
        </tr>
        
        <tr>
            <td width="90">Horaires:</td>
            <td width="90">$horaires</td>
        </tr>
        
        <tr>
            <td width="90">Cout journalier:</td>
            <td width="90">$cout_journalier  ??? /pers.</td>
        </tr>
        
        <tr>
            <td width="90">Nbre personne:</td>
            <td width="90">$nbr_personne</td>
        </tr>
        
        <tr>
            <td width="90">Dates:</td>
            <td width="90">$dates_formation_string</td>
        </tr>
        
        <tr>
            <td width="90">Lieu:</td>
            <td width="90">$lieu</td>
        </tr>
    </table>
EOD;
            $pdf->writeHTML($tbl, true, false, false, false, '');

            // detail column 2
            $pdf->SetXY(80, $y);
            $cout_support = "0,00";
            $frait_dep = $modelColumn0['frais_deplacement'];
            $frait_heb = $modelColumn0['frais_hebergement'];
            $frai_repas = $modelColumn0['frais_repas'];
            $autre_frai = $modelColumn0['autres_frais'];

            $tb2 = <<<EOD
    <table>
        <tr>
            <td width="90">Cout support (u) :</td>
            <td align="right" width="50">$cout_support ???</td>
        </tr>
        
        <tr>
            <td width="90">Frais d??pl. :</td>
            <td align="right" width="50">$frait_dep ???</td>
        </tr>
        
        <tr>
            <td width="90">Frais H??berg. :</td>
            <td align="right" width="50">$frait_heb ???</td>
        </tr>
        
        <tr>
            <td width="90">Frais Repas :</td>
            <td align="right" width="50">$frai_repas ???</td>
        </tr>
        
        <tr>
            <td width="90">Autres frais :</td>
            <td align="right" width="50">$autre_frai ???</td>
        </tr>
       
    </table>
EOD;
            $pdf->writeHTML($tb2, true, false, false, false, '');

            // detail column 3
            $pdf->SetXY(140, $y);
            $soustotalht = $modelColumn0['soustotalht'];
            $totalfrais = $modelColumn0['totalfrais'];
            $remise = $modelColumn0['discount_amount'];
            $totalht = $modelColumn0['totalht'];
            $tva = $modelColumn0['tax_totalamount'];
            $total_ttc = $modelColumn0['totalttc'];

            $total_support = "0,00";


            $tb3 = <<<EOD
    <table>
        <tr>
            <td width="80">Sous-total : </td>
            <td align="right" width="50">$soustotalht ???</td>
        </tr>
        
        <tr>
            <td width="80">Total Frais :</td>
            <td align="right" width="50">$totalfrais ???</td>
        </tr>
        
        <tr>
            <td width="80">Total Support :</td>
            <td align="right" width="50">$total_support ???</td>
        </tr>
        
        <tr>
            <td width="80">Remise :</td>
            <td align="right" width="50">$remise ???</td>
        </tr>
        
        <tr>
            <td width="80">Total HT :</td>
            <td align="right" width="50">$totalht ???</td>
        </tr>
        
         <tr>
            <td width="80">TVA :</td>
            <td align="right" width="50">$tva ???</td>
        </tr>
        
         <tr>
            <td width="80">Total TTC:</td>
            <td align="right" width="50">$total_ttc ???</td>
        </tr>
       
    </table>
EOD;
            $pdf->writeHTML($tb3, true, false, false, false, '');

//signiature :
            $pdf->SetXY(10, $y + 45);
            $pdf->SetFont('times', '', 9);
            $text_s = "La signature du pr??sent devis vaut pour acceptation des conditions g??n??rales de vente. Cochez la ou les formations retenues.";
            $pdf->MultiCell(200, 5, $text_s, 0, 'L', 0, 1);

            $pdf->SetXY(10, $y + 50);
            $pdf->SetFont('times', 'B', 9);
            $date_validite_devis = "23/03/2019";
            $pdf->MultiCell(200, 5, "Date de validit?? du devis :" . $date_quote, 0, 'L', 0, 1);

            $pdf->SetXY(10, $y + 55);
            $pdf->SetFont('times', '', 9);
            $text_prise_charge = "Prise en charge selon votre organisme collecteur.";
            $pdf->MultiCell(200, 5, $text_prise_charge, 0, 'L', 0, 1);

            $pdf->SetXY(105, $y + 60);
            $pdf->SetFont('times', 'I', 9);
            $text_bon_pour = "BON POUR ACCORD (cachet + signature)";
            $pdf->SetFillColor(255, 255, 255);
            $pdf->MultiCell(90, 30, $text_bon_pour, 1, 'L', 1, 1, '', '', true, 0, false, true, 30, 'T');

            $pdf->SetXY(10, $y + 90);
            $pdf->SetFont('times', '', 9);
            $text_salutation = "Je reste ?? votre enti??re disposition pour tout renseignement suppl??mentaire, et vous prie d'agr??er, Madame, mes meilleures salutations. ";
            $pdf->MultiCell(200, 5, $text_salutation, 0, 'L', 0, 1);

            $pdf->SetXY(10, $y + 97);
            $pdf->SetFont('times', 'B', 10);
            $text_signature = "SIGNATURE";
            $pdf->MultiCell(200, 5, $text_signature, 0, 'C', 0, 1);

            $pdf->SetXY(10, $y + 103);
            $pdf->SetFont('times', '', 9);
            $text_signature_fonction = "Fonction";
            $pdf->MultiCell(200, 5, $text_signature_fonction, 0, 'C', 0, 1);

            $pdf->Image("test/Signature/Signature.jpg", 150, $y + 97, 25, 20);

            $rem = 'Organisme de formation professionnelle continue exon??r?? de TVA en vertu de l\'article 202A ?? 202D de l\'annexe II du CGI "et article 261.4.4 du code g??n??ral des imp??ts';
            $pdf->SetXY(1, $y + 132);
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
            $header = "?? retourner avec le devis";
            $pdf->MultiCell(200, 5, $header, 0, 'L', 0, 1);

            $pdf->SetXY(160, $page_2 + 10);
            $pdf->SetFont('times', 'B', 12);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->MultiCell(200, 5, $quote_no, 0, 'L', 0, 1);

            //titre page

            $pdf->SetXY(0, $page_2 + 17);
            $pdf->SetFont('times', 'B', 15);
            $pdf->SetTextColor(0, 0, 0);
            $titre_page = "FICHE D'INSCRIPTION";
            $pdf->MultiCell(200, 5, $titre_page, 0, 'C', 0, 1);

            //information client

            $pdf->SetTextColor(0, 0, 0);

            //$nom_client = 
            $phone_client = $modelColumn0['info_client']['phone'];
            $email_client = $modelColumn0['info_client']['email'];
            $adresse_com = $modelColumn0['info_client']['adresscompl'];

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
            $pdf->MultiCell(40, 5, $adresse_client, 0, 'L', 0, 1);

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
            $pdf->MultiCell(40, 5, $cp_client, 0, 'L', 0, 1);

            //tel
            $pdf->SetXY(120, $page_2 + 30);
            $pdf->SetFont('times', 'B', 11);
            $pdf->MultiCell(40, 5, 'T??l :', 0, 'L', 0, 1);
            $pdf->SetFont('times', '', 11);
            $pdf->SetXY(135, $page_2 + 30);
            $pdf->MultiCell(40, 5, $phone_client, 0, 'L', 0, 1);

            //email
            $pdf->SetXY(120, $page_2 + 38);
            $pdf->SetFont('times', 'B', 11);
            $pdf->MultiCell(40, 5, 'E-mail :', 0, 'L', 0, 1);
            $pdf->SetFont('times', '', 11);
            $pdf->SetXY(135, $page_2 + 38);
            $pdf->MultiCell(40, 5, $email_client, 0, 'L', 0, 1);

            //ville
            $pdf->SetXY(120, $page_2 + 53);
            $pdf->SetFont('times', 'B', 11);
            $pdf->MultiCell(40, 5, 'Ville :', 0, 'L', 0, 1);
            $pdf->SetFont('times', '', 11);
            $pdf->SetXY(135, $page_2 + 53);
            $pdf->MultiCell(40, 5, $ville_client, 0, 'L', 0, 1);

            //nom formation 
            $pdf->SetXY(20, $page_2 + 65);
            $pdf->SetFont('times', 'B', 11);
            $pdf->MultiCell(200, 5, $nom_formation, 0, 'L', 0, 1);


            //titre formation

            $pdf->SetXY(20, $page_2 + 72);
            $pdf->SetFont('times', 'B', 11);
            $pdf->MultiCell(50, 5, 'Pr??nom', 0, 'L', 0, 1);

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

            $a = 0;
            $y = $page_2 + 82;

            for ($i = 0; $i <= 16; $i++) {
                $nom_apprenants = $modelColumn0['info_apprenants'][$i]['firstname'];
                $prenom_apprenants = $modelColumn0['info_apprenants'][$i]['lastname'];
                $birthday_apprenants = $modelColumn0['info_apprenants'][$i]['birthday'];
                $fonction = $modelColumn0['info_apprenants'][$i]['title'];
                //num
                $pdf->SetXY(10, $y + $a);
                $pdf->SetFont('times', 'B', 11);
                $pdf->MultiCell(10, 5, $i, 0, 'L', 0, 1);

                //column 1
                $pdf->SetXY(20, $y + $a);
                $pdf->SetFillColor(100, 0, 0, 0);
                $pdf->SetFont('times', '', 11);
                $pdf->MultiCell(35, 5, $nom_apprenants, 1, 'L', 0, 1);

                //column 2
                $pdf->SetXY(60, $y + $a);
                $pdf->SetFont('times', '', 11);
                $pdf->MultiCell(55, 5, $prenom_apprenants, 1, 'L', 0, 1);

                //column 3
                $pdf->SetXY(120, $y + $a);
                $pdf->SetFont('times', '', 11);
                $pdf->MultiCell(45, 5, $fonction, 1, 'L', 0, 1);

                //column 4
                $pdf->SetXY(170, $y + $a);
                $pdf->SetFont('times', '', 11);
                $pdf->MultiCell(30, 5, $birthday_apprenants, 1, 'L', 0, 1);

                $a = $a + 9;
            }

            $pdf->SetXY(20, $page_2 + 235);
            $pdf->SetFont('times', '', 10);
            $remarque = "Le client reconnait avoir pris connaissance des modalit??s d'annulation d??crites dans les Conditions G??n??rales de vente ci-jointes.";
            $pdf->MultiCell(200, 5, $remarque, 0, 'L', 0, 1);
            $pdf->AddPage();


            //condition general 
            $page_condition = 0;
            $pdf->SetXY(0, $page_condition + 3);
            $pdf->SetFont('times', 'B', 10);
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
                    . '<h4>CONDITIONS D\'ANNULATION ET DE REPORT<h4>'
            ;


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
                    . '<h4>DATADOCK num??ro d\'agr??ment : 0012160</h4>'

            ;

            $pdf->SetXY(0, $page_condition + 10);
            $pdf->SetFont('times', '', 7.9);
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetTextColor(102, 102, 102);

            $pdf->writeHTMLCell(95, '', '', 10, $html1, 0, 0, 1, true, 'J', true);
            $pdf->writeHTMLCell(95, '', '', '', $html2, 0, 1, 1, true, 'J', true);

            $pdf->AddPage();
            $pdf->SetFillColor(255, 255, 255);
            $pdf->SetTextColor(0, 0, 0);
            $nbr_journee = count($modelColumn0['info_dates']);


            $pdf->SetXY($x + 10, 10);
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
        }
    }
}