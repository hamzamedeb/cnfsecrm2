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
            $pdf->MultiCell(100, 5, "DEVIS N° " . $quote_no, 0, 'L', 0, 1, 10, $y);
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
            $text_sujet = "Suite à votre demande, je vous prie de bien vouloir trouver notre proposition pour la formation suivante :";
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
            $horaires = 'de ' . $modelColumn0['info_dates'][0]["start_matin"] . ' h à ' . $modelColumn0['info_dates'][0]["end_matin"] . ' h et de ' . $modelColumn0['info_dates'][0]["start_apresmidi"] . ' h à ' . $modelColumn0['info_dates'][0]["end_apresmidi"] . ' h';
            $cout_journalier = $listprice;

            $dates_formation_string = ($dates_formation_string != "") ? $dates_formation_string : $modelColumn0['info_dates'][0]['date_start'];
            $lieu = $modelColumn0['ville_devis'];
            $tbl = <<<EOD
    <table border-left="1">
        <tr style="border-right: 1px solid red;"> 
            <td style="font-family: "Trebuchet MS", Helvetica, sans-serif;font-weight: normal;font-size: 8pt;" width="90">Durée:</td>
            <td style="font-family: "Trebuchet MS", Helvetica, sans-serif;font-weight: normal;font-size: 20pt;" width="90">$duree Jour(s)</td>
        </tr>
        
        <tr>
            <td width="90">Horaires:</td>
            <td width="90">$horaires</td>
        </tr>
        
        <tr>
            <td width="90">Cout journalier:</td>
            <td width="90">$cout_journalier  € /pers.</td>
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
            $pdf->SetXY(10, $y + 45);
            $pdf->SetFont('times', '', 9);
            $text_s = "La signature du présent devis vaut pour acceptation des conditions générales de vente. Cochez la ou les formations retenues.";
            $pdf->MultiCell(200, 5, $text_s, 0, 'L', 0, 1);

            $pdf->SetXY(10, $y + 50);
            $pdf->SetFont('times', 'B', 9);
            $date_validite_devis = "23/03/2019";
            $pdf->MultiCell(200, 5, "Date de validité du devis :" . $date_quote, 0, 'L', 0, 1);

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
            $text_salutation = "Je reste à votre entiËre disposition pour tout renseignement supplèmentaire, et vous prie d'agrèer, Madame, mes meilleures salutations. ";
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

            $rem = 'Organisme de formation professionnelle continue exonéré de TVA en vertu de l\'article 202A à 202D de l\'annexe II du CGI "et article 261.4.4 du code général des impôts';
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
            $header = "à retourner avec le devis";
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
            $pdf->MultiCell(40, 5, 'Tél :', 0, 'L', 0, 1);
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
            $pdf->MultiCell(50, 5, 'Prénom', 0, 'L', 0, 1);

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
            $remarque = "Le client reconnait avoir pris connaissance des modalités d'annulation décrites dans les Conditions Générales de vente ci-jointes.";
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
        }
    }
}