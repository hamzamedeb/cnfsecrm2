<?php

/* +**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 * ********************************************************************************** */

include_once 'include/InventoryPDFController.php';
/* wajcrmcnfse */
include_once 'vtlib/Vtiger/PDF/Events/HeaderViewerAttes.php';
include_once 'vtlib/Vtiger/PDF/Events/ContentViewerAttes.php';

include_once 'vtlib/Vtiger/PDF/Events/HeaderViewerAvis.php';
include_once 'vtlib/Vtiger/PDF/Events/ContentViewerAvis.php';
include_once 'vtlib/Vtiger/PDF/Events/HeaderViewerListeApprenants.php';
include_once 'vtlib/Vtiger/PDF/Events/HeaderViewerListeAttestations.php';
include_once 'vtlib/Vtiger/PDF/Events/HeaderViewerListeAvisFavorable.php';
include_once 'vtlib/Vtiger/PDF/Events/HeaderViewerEmargement.php';
/* unicnfsecrm_022020_15 */
include_once 'vtlib/Vtiger/PDF/Events/HeaderViewerListeSatisfaction.php';

/* unicnfsecrm_022020_20 */
include_once 'vtlib/Vtiger/PDF/Events/HeaderViewerSignaletique.php';
include_once 'vtlib/Vtiger/PDF/Events/HeaderViewerListeTOKENS.php';
include_once 'vtlib/Vtiger/PDF/Events/HeaderViewerListeTOKENSTEST.php';

/* wajcrmcnfse - fin */

/* uni_cnfsecrm - modif 81 - DEBUT */
include_once 'vtlib/Vtiger/PDF/inventory/HeaderViewerConvocation.php';
/* uni_cnfsecrm - modif 81 - FIN */

/* uni_cnfsecrm - v2 - modif 100 - DEBUT */
include_once 'vtlib/Vtiger/PDF/inventory/HeaderViewerDocHabilitation.php';
/* uni_cnfsecrm - v2 - modif 100 - FIN */

/* uni_cnfsecrm - v2 - modif 124 - DEBUT */
include_once 'vtlib/Vtiger/PDF/Events/HeaderViewerExportertout.php';
/* uni_cnfsecrm - v2 - modif 124 - FIN */

/* uni_cnfsecrm - v2 - modif 145 - DEBUT */
include_once 'vtlib/Vtiger/PDF/Events/HeaderViewerListeTitreHabilitation.php';
/* uni_cnfsecrm - v2 - modif 145 - FIN */
/* uni_cnfsecrm - v2 - modif 176 - DEBUT */
include_once 'vtlib/Vtiger/PDF/inventory/HeaderViewerMPConvocation.php';
include_once 'vtlib/Vtiger/PDF/inventory/HeaderViewerPlan.php';
/* uni_cnfsecrm - v2 - modif 176 - FIN */

class Vtiger_EventsPDFController extends Vtiger_InventoryPDFController {

    function loadRecord($id, $appr, $doc) {
        global $current_user;
        $this->focus = $focus = CRMEntity::getInstance($this->moduleName);
        $focus->retrieve_entity_info($id, $this->moduleName);
        $focus->apply_field_security();
        $focus->id = $id;
        $focus->doc = $doc;
        $focus->appr = $appr;
    }

    function getPDFGeneratorEvents() {
        return new Vtiger_PDF_Events_Generator();
    }

    function buildHeaderModel($appr) {
        $headerModel = new Vtiger_PDF_Model();
        $headerModel->set('title', $this->buildHeaderModelTitle());
        $modelColumns = array($this->buildHeaderModelColumnLeft($appr), $this->buildHeaderModelColumnCenter(), $this->buildHeaderModelColumnRight());
        $headerModel->set('columns', $modelColumns);

        return $headerModel;
    }

    function buildHeaderModelColumnLeft($appr) {
        $subject = $this->focusColumnValue('subject');
        $customerName = $this->resolveReferenceLabel($this->focusColumnValue('account_id'), 'Accounts');
        $contactName = $this->resolveReferenceLabel($this->focusColumnValue('contact_id'), 'Contacts');
        $purchaseOrder = $this->focusColumnValue('vtiger_purchaseorder');
        $quoteName = $this->resolveReferenceLabel($this->focusColumnValue('quote_id'), 'Quotes');
        $type_formation = $this->focusColumnValue('cf_977');
        $date_debut_formation = $this->focusColumnValue('date_start');
        $date = new DateTime($date_debut_formation);
        $date_debut_formation = $date->format('d/m/Y');
        $date_fin_formations = $this->focusColumnValue('due_date');
        $date = new DateTime($date_fin_formations);
        $date_fin_formations = $date->format('d/m/Y');
        $idformation = $this->focusColumnValue('formation');
        $categorie_formation = getSingleFieldValue("vtiger_service", "servicecategory", "serviceid", $idformation);
        //echo $categorie_formation;
        $infos_client = $this->buildHeaderNomClient();
        //var_dump($infos_client);
        if ($appr == "")
            $appr = $_GET["appr"];
        $monfichier = fopen('debug_email.txt', 'a+');
        fputs($monfichier, "\n" . " appr " . $appr);
        fclose($monfichier);
        $doc = $_GET["doc"];
        $date_creation = $this->getCrmentity();
        $ville = $this->focusColumnValue('cf_931');
        $info_apprenants = $this->buildHeaderApprenants($appr);
        //var_dump($info_apprenants);
        $getApprenants = $this->getApprenants();
        $getDates = $this->getDates();
        $nbr_jours = $this->focusColumnValue('cf_998');
        $nombre_heures = $this->focusColumnValue('cf_996');
        $formation = $this->focusColumnValue('formation');
        $duree = $this->focusColumnValue('cf_1010');
        $adresse_formation = $this->focusColumnValue('cf_933');
        $cp_formation = $this->focusColumnValue('cf_929');
        $ville_formation = $this->focusColumnValue('cf_931');
        //$formation = formation_display
        $nom_formation = getSingleFieldValue("vtiger_service", "servicename", "serviceid", $formation);
        $nbrheures = $this->getNbrHeures(); /* uni_cnfsecrm - modif 81 */
        /* modif 108 - DEBUT */
        $typeFormationHab = getSingleFieldValue("vtiger_servicecf", "cf_1272", "serviceid", $formation);
        /* modif 108 - FIN */
        /* unicnfsecrm_022020_20 */

        if ($doc == 'SIGNALETIQUE') {
            $detailFicheSignaletique = $this->getDetailFicheSignaletique();
        } else {
            $detailFicheSignaletique = '';
        }

        $contact_salutation = 'contact_salutation';
        $contact_firstname = 'contact_firstname';
        $contact_salutation = 'contacts_lastname';
        $categorie_formation = getSingleFieldValue("vtiger_service", "servicecategory", "serviceid", $formation);
        $formateur = $this->getFormateur();

        $modelColumn0 = array(
            'subject' => $subject,
            'info_client' => $infos_client,
            'info_apprenants' => $info_apprenants,
            'type_formation' => $type_formation,
            'date_debut_formation' => $date_debut_formation,
            'date_fin_formations' => $date_fin_formations,
            'nbr_jours' => $nbr_jours,
            'nbr_heures' => $nombre_heures,
            'doc' => $doc,
            'date_creation' => $date_creation,
            'ville' => $ville,
            'duree' => $duree,
            'nom_formation' => $nom_formation,
            'categorie_formation' => $categorie_formation,
            'formateur' => $formateur,
            'getApprenants' => $getApprenants,
            'info_dates' => $getDates,
            'ville_formation' => $ville_formation,
            'cp_formation' => $cp_formation,
            'adresse_formation' => $adresse_formation,
            'detailFicheSignaletique' => $detailFicheSignaletique, /* unicnfsecrm_022020_20 */
            'nbrheures' => $nbrheures, /* uni_cnfsecrm - modif 81 */
            /* modif 108 - DEBUT */
            'typeFormationHab' => $typeFormationHab
                /* modif 108 - FIN */
        );
        return $modelColumn0;
    }

    function buildHeaderModelTitle() {
        $singularModuleNameKey = 'SINGLE_' . $this->moduleName;
        $translatedSingularModuleLabel = getTranslatedString($singularModuleNameKey, $this->moduleName);
        if ($translatedSingularModuleLabel == $singularModuleNameKey) {
            $translatedSingularModuleLabel = getTranslatedString($this->moduleName, $this->moduleName);
        }
        return sprintf("%s: %s", $translatedSingularModuleLabel, $this->focusColumnValue('invoice_no'));
    }

    function buildHeaderModelColumnCenter() {
        $customerName = $this->resolveReferenceLabel($this->focusColumnValue('account_id'), 'Accounts');
        $contactName = $this->resolveReferenceLabel($this->focusColumnValue('contact_id'), 'Contacts');
        $purchaseOrder = $this->focusColumnValue('vtiger_purchaseorder');
        $salesOrder = $this->resolveReferenceLabel($this->focusColumnValue('salesorder_id'));

        $customerNameLabel = getTranslatedString('Customer Name', $this->moduleName);
        $contactNameLabel = getTranslatedString('Contact Name', $this->moduleName);
        $purchaseOrderLabel = getTranslatedString('Purchase Order', $this->moduleName);
        $salesOrderLabel = getTranslatedString('Sales Order', $this->moduleName);

        $modelColumnCenter = array(
            $customerNameLabel => $customerName,
            $purchaseOrderLabel => $purchaseOrder,
            $contactNameLabel => $contactName,
            $salesOrderLabel => $salesOrder
        );
        return $modelColumnCenter;
    }

    function buildHeaderModelColumnRight() {
        $issueDateLabel = getTranslatedString('Issued Date', $this->moduleName);
        $validDateLabel = getTranslatedString('Due Date', $this->moduleName);
        $billingAddressLabel = getTranslatedString('Billing Address', $this->moduleName);
        $shippingAddressLabel = getTranslatedString('Shipping Address', $this->moduleName);

        $modelColumnRight = array(
            'dates' => array(
                $issueDateLabel => $this->formatDate(date("Y-m-d")),
                $validDateLabel => $this->formatDate($this->focusColumnValue('duedate')),
            ),
            $billingAddressLabel => $this->buildHeaderBillingAddress(),
            $shippingAddressLabel => $this->buildHeaderShippingAddress()
        );
        return $modelColumnRight;
    }

    function buildSummaryModel() {
        $summaryModel = new Vtiger_PDF_Model();

        return $summaryModel;
    }

    function buildContentModels() {
        $contentModels = array();
        return $contentModels;
    }

    function buildFooterModel() {
        $footerModel = new Vtiger_PDF_Model();
        return $footerModel;
    }

    function getWatermarkContent() {
        return $this->focusColumnValue('invoicestatus');
    }

    function getContentViewer($doc, $appr) {

        /* wajcnfsecrm */
        $module = $this->moduleName;
        if ($doc == 'attestation') {
            $contentViewer = new Vtiger_PDF_EventsAttesContentViewer();
        }
        /* uni_cnfsecrm - modif 81 - DEBUT */ else if ($doc == 'sendconvocation') {
            $contentViewer = new Vtiger_PDF_EventsAvisContentViewer();
        }
        /* uni_cnfsecrm - modif 81 - FIN */ else {
            $contentViewer = new Vtiger_PDF_EventsAvisContentViewer();
        }
        $contentViewer->setContentModels($this->buildContentModels());
        /* wajcnfsecrm - fin */
        return $contentViewer;
    }

    function getHeaderViewer($doc, $appr, $elearning) { /* uni_cnfsecrm - modif 83 */
        /* wajcrmcnfse */
        $module = $this->moduleName;
        if ($doc == 'attestation') {
            $headerViewer = new Vtiger_PDF_EventsAttesHeaderViewer();
        }
        /* uni_cnfsecrm - modif 81 - DEBUT */ else if ($doc == 'sendconvocation') {
//            $headerViewer = new Vtiger_PDF_ConvocationHeaderViewer();
            /* uni_cnfsecrm - v2 - modif 176 - DEBUT */
            $accountid = $this->focusColumnValue('parent_id');
            if ($accountid == 189380) {
                $headerViewer = new Vtiger_PDF_ConvocationMPHeaderViewer();
            } else {
                $headerViewer = new Vtiger_PDF_ConvocationHeaderViewer();
            }
            /* uni_cnfsecrm - v2 - modif 176 - FIN */
        }
        /* uni_cnfsecrm - modif 81 - FIN */
        /* uni_cnfsecrm - v2 - modif 100 - DEBUT */ else if ($doc == 'docHabilitation') {
            $headerViewer = new Vtiger_PDF_EventsdocHabilitationHeaderViewer();
        }
        /* uni_cnfsecrm - v2 - modif 100 - FIN */
        /* uni_cnfsecrm - v2 - modif 124 - DEBUT */ else if ($doc == 'exportertout') {
            $headerViewer = new Vtiger_PDF_EventsExportertoutHeaderViewer();
        }
        /* uni_cnfsecrm - v2 - modif 124 - FIN */ else {
            $headerViewer = new Vtiger_PDF_EventsAvisHeaderViewer();
        }
        /* wajcrmcnfse - fin */
        $headerViewer->setModel($this->buildHeaderModel($appr));
        return $headerViewer;
    }

    function Output($filename, $type, $doc, $appr) {
        if (is_null($this->focus))
            return;
        $monfichier = fopen('debug_email.txt', 'a+');
        fputs($monfichier, "\n" . "doc " . $doc);
        fclose($monfichier);
        $pdfgenerator = $this->getPDFGeneratorEvents();

        $pdfgenerator->setPagerViewer($this->getPagerViewer());
        $pdfgenerator->setHeaderViewer($this->getHeaderViewer($doc, $appr));
        $pdfgenerator->setFooterViewer($this->getFooterViewer());
        $pdfgenerator->setContentViewer($this->getContentViewer($doc, $appr));

        $pdfgenerator->generate($filename, $type, $doc);
    }

    /* uni_cnfsecrm - v2 - modif 108 - DEBUT */
    /* uni_cnfsecrm - v2 - modif 115 - DEBUT */
    /* uni_cnfsecrm - v2 - modif 127 - DEBUT */

    /* uni_cnfsecrm - v2 - modif 176 - DEBUT */

    function buildHeaderApprenants($appr) {
        global $adb;
        $info_apprenants = array();
        $id = $this->focusColumnValues(array('record_id'));
        $query = "SELECT id,salutation,firstname,lastname,vtiger_contactsubdetails.birthday,resultat,
            ticket_examen,be_essai,be_mesurage,be_verification,be_manoeuvre,he_essai,he_mesurage,
            he_verification,he_manoeuvre,initiale,recyclage,testprerequis,electricien,
             b0_h0_h0v_b0, b0_h0_h0v_h0v, bs_be_he_b0, bs_be_he_h0v, bs_be_he_bs, 
             bs_be_he_manoeuvre, b1v_b2v_bc_br_b0, b1v_b2v_bc_br_h0v, b1v_b2v_bc_br_bs, 
             b1v_b2v_bc_br_manoeuvre, b1v_b2v_bc_br_b1v, b1v_b2v_bc_br_b2v, b1v_b2v_bc_br_bc, 
             b1v_b2v_bc_br_br, b1v_b2v_bc_br_essai, b1v_b2v_bc_br_verification, 
             b1v_b2v_bc_br_mesurage, b1v_b2v_bc_br_h1v_h2v_b0, b1v_b2v_bc_br_h1v_h2v_h0v, 
             b1v_b2v_bc_br_h1v_h2v_bs, b1v_b2v_bc_br_h1v_h2v_manoeuvre, b1v_b2v_bc_br_h1v_h2v_b1v,
             b1v_b2v_bc_br_h1v_h2v_b2v, b1v_b2v_bc_br_h1v_h2v_bc, b1v_b2v_bc_br_h1v_h2v_br, 
             b1v_b2v_bc_br_h1v_h2v_essai, b1v_b2v_bc_br_h1v_h2v_verification, 
             b1v_b2v_bc_br_h1v_h2v_mesurage, b1v_b2v_bc_br_h1v_h2v_h1v, b1v_b2v_bc_br_h1v_h2v_h2v,
             b1v_b2v_bc_br_h1v_h2v_hc,bs_be_he_he,b1v_b2v_bc_br_he,b1v_b2v_bc_br_h1v_h2v_he,
            accountname,vtiger_account.phone as telclient,email,account_no,ticket_examen_test,
            type_tokens,type_tokens_test,date_start_appr , date_fin_appr , duree_jour , duree_heure,date_start,due_date,
            vtiger_account.accountid, vtiger_contactscf.cf_1320, cf_1318
            FROM vtiger_sessionsapprenantsrel 
            INNER JOIN vtiger_contactdetails on vtiger_contactdetails.contactid = vtiger_sessionsapprenantsrel.apprenantid 
            INNER JOIN vtiger_contactsubdetails on vtiger_contactsubdetails.contactsubscriptionid = vtiger_contactdetails.contactid 
            LEFT JOIN vtiger_account on vtiger_account.accountid = vtiger_contactdetails.accountid
            LEFT JOIN vtiger_activitycf on vtiger_activitycf.activityid = vtiger_sessionsapprenantsrel.id
            LEFT JOIN vtiger_activity on vtiger_activity.activityid = vtiger_sessionsapprenantsrel.id
            inner join vtiger_contactscf on vtiger_contactscf.contactid = vtiger_contactdetails.contactid 
            WHERE vtiger_sessionsapprenantsrel.id = ?";
        $params = array($id);

        if ($appr != "") {
            $query .= " and vtiger_sessionsapprenantsrel.apprenantid=?";
            array_push($params, $appr);
        }
        /* uni_cnfsecrm - v2 - modif 172 - DEBUT */ else {
            $query .= " ORDER BY firstname ASC";
        }
        /* uni_cnfsecrm - v2 - modif 172 - FIN */

//        var_dump($query);die();
        $result = $adb->pquery($query, $params);
        $num_rows_apprenants = $adb->num_rows($result);
        if ($num_rows_apprenants) {
            for ($i = 0; $i < $num_rows_apprenants; $i++) {
                $salutation = $adb->query_result($result, $i, 'salutation');
                $firstname = ucwords(formatString(strtolower(($adb->query_result($result, $i, 'firstname')))));
                $lastname = strtoupper(formatString($adb->query_result($result, $i, 'lastname')));
                $birthday_contact = $adb->query_result($result, $i, 'birthday');
                $resultat = $adb->query_result($result, $i, 'resultat');
                $ticket_examen = $adb->query_result($result, $i, 'ticket_examen');
                $ticket_examen_test = $adb->query_result($result, $i, 'ticket_examen_test');
                $type_tokens = $adb->query_result($result, $i, 'type_tokens');
                $type_tokens_test = $adb->query_result($result, $i, 'type_tokens_test');
                $accountname = ucwords(strtolower(($adb->query_result($result, $i, 'accountname'))));
                $phone = $adb->query_result($result, $i, 'telclient');
                $email = $adb->query_result($result, $i, 'email');
                $account_no = $adb->query_result($result, $i, 'account_no');
                $accountid = $adb->query_result($result, $i, 'accountid');
                $direction_contact = $adb->query_result($result, $i, 'cf_1320');
                $matricule_contact = $adb->query_result($result, $i, 'cf_1318');
                switch ($salutation) {
                    case 'Mr.':
                        $salutation = "Monsieur";
                        break;
                    case 'Ms.':
                        $salutation = "Madame";
                        break;
                    default:
                        $salutation = $salutation;
                        break;
                }

                $info_apprenants[$i]['salutation'] = $salutation;
                $info_apprenants[$i]['firstname'] = $firstname;
                $info_apprenants[$i]['lastname'] = $lastname;
                $info_apprenants[$i]['birthday'] = $birthday_contact;
                $info_apprenants[$i]['resultat'] = $resultat;
                $info_apprenants[$i]['ticket_examen'] = $ticket_examen;
                $info_apprenants[$i]['ticket_examen_test'] = $ticket_examen_test;
                $info_apprenants[$i]['type_tokens'] = $type_tokens;
                $info_apprenants[$i]['type_tokens_test'] = $type_tokens_test;
                $info_apprenants[$i]['accountname'] = $accountname;
                $info_apprenants[$i]['phone'] = $phone;
                $info_apprenants[$i]['email'] = $email;
                $info_apprenants[$i]['account_no'] = $account_no;
                $info_apprenants[$i]['accountid'] = $accountid;


                $info_apprenants[$i]['be_essai'] = $adb->query_result($result, $i, 'be_essai');
                $info_apprenants[$i]['be_mesurage'] = $adb->query_result($result, $i, 'be_mesurage');
                $info_apprenants[$i]['be_verification'] = $adb->query_result($result, $i, 'be_verification');
                $info_apprenants[$i]['be_manoeuvre'] = $adb->query_result($result, $i, 'be_manoeuvre');
                $info_apprenants[$i]['he_essai'] = $adb->query_result($result, $i, 'he_essai');
                $info_apprenants[$i]['he_mesurage'] = $adb->query_result($result, $i, 'he_mesurage');
                $info_apprenants[$i]['he_verification'] = $adb->query_result($result, $i, 'he_verification');
                $info_apprenants[$i]['he_manoeuvre'] = $adb->query_result($result, $i, 'he_manoeuvre');
                $info_apprenants[$i]['initiale'] = $adb->query_result($result, $i, 'initiale');
                $info_apprenants[$i]['recyclage'] = $adb->query_result($result, $i, 'recyclage');
                $info_apprenants[$i]['testprerequis'] = $adb->query_result($result, $i, 'testprerequis');
                $info_apprenants[$i]['electricien'] = $adb->query_result($result, $i, 'electricien');

                $info_apprenants[$i]['b0_h0_h0v_b0'] = $adb->query_result($result, $i, 'b0_h0_h0v_b0');
                $info_apprenants[$i]['b0_h0_h0v_h0v'] = $adb->query_result($result, $i, 'b0_h0_h0v_h0v');
                $info_apprenants[$i]['bs_be_he_b0'] = $adb->query_result($result, $i, 'bs_be_he_b0');
                $info_apprenants[$i]['bs_be_he_h0v'] = $adb->query_result($result, $i, 'bs_be_he_h0v');
                $info_apprenants[$i]['bs_be_he_bs'] = $adb->query_result($result, $i, 'bs_be_he_bs');
                $info_apprenants[$i]['bs_be_he_manoeuvre'] = $adb->query_result($result, $i, 'bs_be_he_manoeuvre');
                $info_apprenants[$i]['b1v_b2v_bc_br_b0'] = $adb->query_result($result, $i, 'b1v_b2v_bc_br_b0');
                $info_apprenants[$i]['b1v_b2v_bc_br_h0v'] = $adb->query_result($result, $i, 'b1v_b2v_bc_br_h0v');
                $info_apprenants[$i]['b1v_b2v_bc_br_bs'] = $adb->query_result($result, $i, 'b1v_b2v_bc_br_bs');
                $info_apprenants[$i]['b1v_b2v_bc_br_manoeuvre'] = $adb->query_result($result, $i, 'b1v_b2v_bc_br_manoeuvre');
                $info_apprenants[$i]['b1v_b2v_bc_br_b1v'] = $adb->query_result($result, $i, 'b1v_b2v_bc_br_b1v');
                $info_apprenants[$i]['b1v_b2v_bc_br_b2v'] = $adb->query_result($result, $i, 'b1v_b2v_bc_br_b2v');
                $info_apprenants[$i]['b1v_b2v_bc_br_bc'] = $adb->query_result($result, $i, 'b1v_b2v_bc_br_bc');
                $info_apprenants[$i]['b1v_b2v_bc_br_br'] = $adb->query_result($result, $i, 'b1v_b2v_bc_br_br');
                $info_apprenants[$i]['b1v_b2v_bc_br_essai'] = $adb->query_result($result, $i, 'b1v_b2v_bc_br_essai');
                $info_apprenants[$i]['b1v_b2v_bc_br_verification'] = $adb->query_result($result, $i, 'b1v_b2v_bc_br_verification');
                $info_apprenants[$i]['b1v_b2v_bc_br_mesurage'] = $adb->query_result($result, $i, 'b1v_b2v_bc_br_mesurage');
                $info_apprenants[$i]['b1v_b2v_bc_br_h1v_h2v_b0'] = $adb->query_result($result, $i, 'b1v_b2v_bc_br_h1v_h2v_b0');
                $info_apprenants[$i]['b1v_b2v_bc_br_h1v_h2v_h0v'] = $adb->query_result($result, $i, 'b1v_b2v_bc_br_h1v_h2v_h0v');
                $info_apprenants[$i]['b1v_b2v_bc_br_h1v_h2v_bs'] = $adb->query_result($result, $i, 'b1v_b2v_bc_br_h1v_h2v_bs');
                $info_apprenants[$i]['b1v_b2v_bc_br_h1v_h2v_manoeuvre'] = $adb->query_result($result, $i, 'b1v_b2v_bc_br_h1v_h2v_manoeuvre');
                $info_apprenants[$i]['b1v_b2v_bc_br_h1v_h2v_b1v'] = $adb->query_result($result, $i, 'b1v_b2v_bc_br_h1v_h2v_b1v');
                $info_apprenants[$i]['b1v_b2v_bc_br_h1v_h2v_b2v'] = $adb->query_result($result, $i, 'b1v_b2v_bc_br_h1v_h2v_b2v');
                $info_apprenants[$i]['b1v_b2v_bc_br_h1v_h2v_bc'] = $adb->query_result($result, $i, 'b1v_b2v_bc_br_h1v_h2v_bc');
                $info_apprenants[$i]['b1v_b2v_bc_br_h1v_h2v_br'] = $adb->query_result($result, $i, 'b1v_b2v_bc_br_h1v_h2v_br');
                $info_apprenants[$i]['b1v_b2v_bc_br_h1v_h2v_essai'] = $adb->query_result($result, $i, 'b1v_b2v_bc_br_h1v_h2v_essai');
                $info_apprenants[$i]['b1v_b2v_bc_br_h1v_h2v_verification'] = $adb->query_result($result, $i, 'b1v_b2v_bc_br_h1v_h2v_verification');
                $info_apprenants[$i]['b1v_b2v_bc_br_h1v_h2v_mesurage'] = $adb->query_result($result, $i, 'b1v_b2v_bc_br_h1v_h2v_mesurage');
                $info_apprenants[$i]['b1v_b2v_bc_br_h1v_h2v_h1v'] = $adb->query_result($result, $i, 'b1v_b2v_bc_br_h1v_h2v_h1v');
                $info_apprenants[$i]['b1v_b2v_bc_br_h1v_h2v_h2v'] = $adb->query_result($result, $i, 'b1v_b2v_bc_br_h1v_h2v_h2v');
                $info_apprenants[$i]['b1v_b2v_bc_br_h1v_h2v_hc'] = $adb->query_result($result, $i, 'b1v_b2v_bc_br_h1v_h2v_hc');
                /* uni_cnfsecrm - v2 - modif 115 - DEBUT */
                $info_apprenants[$i]['bs_be_he_he'] = $adb->query_result($result, $i, 'bs_be_he_he');
                $info_apprenants[$i]['b1v_b2v_bc_br_he'] = $adb->query_result($result, $i, 'b1v_b2v_bc_br_he');
                $info_apprenants[$i]['b1v_b2v_bc_br_h1v_h2v_he'] = $adb->query_result($result, $i, 'b1v_b2v_bc_br_h1v_h2v_he');
                /* uni_cnfsecrm - v2 - modif 115 - FIN */

                if ($adb->query_result($result, $i, 'date_start_appr') == '0000-00-00' || $adb->query_result($result, $i, 'date_start_appr') == '' || $adb->query_result($result, $i, 'date_start_appr') == '1970-01-01') {
                    $date_start_session = strtotime($adb->query_result($result, $i, 'date_start'));
                    $info_apprenants[$i]['date_start_appr'] = date('d-m-Y', $date_start_session);
                } else {
                    $date_start_appr = strtotime($adb->query_result($result, $i, 'date_start_appr'));
                    $info_apprenants[$i]['date_start_appr'] = date('d-m-Y', $date_start_appr);
                }

                if ($adb->query_result($result, $i, 'date_fin_appr') == '0000-00-00' || $adb->query_result($result, $i, 'date_fin_appr') == '' || $adb->query_result($result, $i, 'date_fin_appr') == '1970-01-01') {
                    $date_fin_session = strtotime($adb->query_result($result, $i, 'due_date'));
                    $apprenants_Detail[$i]['date_fin_appr'] = date('d-m-Y', $date_fin_session);
                } else {
                    $date_fin_appr = strtotime($adb->query_result($result, $i, 'date_fin_appr'));
                    $apprenants_Detail[$i]['date_fin_appr'] = date('d-m-Y', $date_fin_appr);
                }

                $info_apprenants[$i]['date_fin_appr'] = $adb->query_result($result, $i, 'date_fin_appr');
                $info_apprenants[$i]['duree_jour'] = $adb->query_result($result, $i, 'duree_jour');
                $info_apprenants[$i]['duree_heure'] = $adb->query_result($result, $i, 'duree_heure');

                $info_apprenants[$i]['matricule'] = $matricule_contact;
                $info_apprenants[$i]['direction'] = $direction_contact;
                $info_apprenants[$i]['listDirection'] = $this->getListDirectionByApprenant($direction_contact);
            }
        }
        /* uni_cnfsecrm - v2 - modif 115 - FIN */

        $info_apprenants['nbr_apprenants'] = $num_rows_apprenants;
        return $info_apprenants;
    }

    /* uni_cnfsecrm - v2 - modif 176 - FIN */

    /* uni_cnfsecrm - v2 - modif 127 - FIN */
    /* uni_cnfsecrm - v2 - modif 108 - FIN */

    function getHeaderViewerFeuilles($type) {
        /* wajcrmcnfse */
        $module = $this->moduleName;

        switch ($type) {
            case 'Emargement':
                $headerViewer = new Vtiger_PDF_EmargementHeaderViewer();
                break;

            case 'ListeApprenants':
                $headerViewer = new Vtiger_PDF_ListeApprenantsHeaderViewer();
                break;

            case 'ListeAttestations':
                $headerViewer = new Vtiger_PDF_ListeAttestationsHeaderViewer();
                break;

            case 'ListeAvisFavorable':
                $headerViewer = new Vtiger_PDF_ListeAvisFavorableHeaderViewer();
                break;

            case 'Satisfaction':
                $headerViewer = new Vtiger_PDF_SatisfactionHeaderViewer();
                break;
            /* unicnfsecrm_022020_15 */
            case 'LISTSATISFACTION':
                $headerViewer = new Vtiger_PDF_ListeSatisfactionHeaderViewer();
                break;
            /* unicnfsecrm_022020_20 */
            case 'SIGNALETIQUE':
                $headerViewer = new Vtiger_PDF_SignaletiqueHeaderViewer();
                break;

            case 'ListeTOKEN':
                $headerViewer = new Vtiger_PDF_ListeTOKENSHeaderViewer();
                break;

            case 'ListeTOKENTEST':
                $headerViewer = new Vtiger_PDF_ListeTOKENSTESTHeaderViewer();
                break;
            /* uni_cnfsecrm - v2 - modif 145 - DEBUT */
            case 'LISTETITRHABILITATIONS':
                $headerViewer = new Vtiger_PDF_LISTETITREHABILITATIONSHeaderViewer();
                break;
            /* uni_cnfsecrm - v2 - modif 145 - FIN */
            /* uni_cnfsecrm - v2 - modif 176 - DEBUT */
            case 'plan':
                $headerViewer = new Vtiger_PDF_PlanHeaderViewer();
                break;
            /* uni_cnfsecrm - v2 - modif 176 - FIN */

            default:
                $headerViewer = new Vtiger_PDF_EmargementHeaderViewer();
                break;
        }
        /* wajcrmcnfse - fin */
        $headerViewer->setModel($this->buildHeaderModel());
        return $headerViewer;
    }

    function OutputLISTEAPPRENANTS($filename, $type) {
        if (is_null($this->focus))
            return;

        $pdfgenerator = $this->getPDFGenerator();

        $pdfgenerator->setPagerViewer($this->getPagerViewer());
        $pdfgenerator->setHeaderViewer($this->getHeaderViewerFeuilles("ListeApprenants"));
        $pdfgenerator->setFooterViewer($this->getFooterViewer());
        $pdfgenerator->setContentViewer($this->getContentViewer());

        $pdfgenerator->generate($filename, $type);
    }

    function OutputLISTEATTESTATIONS($filename, $type) {
        if (is_null($this->focus))
            return;

        $pdfgenerator = $this->getPDFGenerator();

        $pdfgenerator->setPagerViewer($this->getPagerViewer());
        $pdfgenerator->setHeaderViewer($this->getHeaderViewerFeuilles("ListeAttestations"));
        $pdfgenerator->setFooterViewer($this->getFooterViewer());
        $pdfgenerator->setContentViewer($this->getContentViewer());

        $pdfgenerator->generate($filename, $type);
    }

    function OutputLISTEAVISFAVORABLE($filename, $type) {
        if (is_null($this->focus))
            return;

        $pdfgenerator = $this->getPDFGeneratorEvents();

        $pdfgenerator->setPagerViewer($this->getPagerViewer());
        $pdfgenerator->setHeaderViewer($this->getHeaderViewerFeuilles("ListeAvisFavorable"));
        //$pdfgenerator->setFooterViewer($this->getFooterViewer());
        $pdfgenerator->setContentViewer($this->getContentViewer());

        $pdfgenerator->generate($filename, $type);
    }

    function OutputEMARGEMENT($filename, $type) {
        if (is_null($this->focus))
            return;

        $pdfgenerator = $this->getPDFGenerator();

        $pdfgenerator->setPagerViewer($this->getPagerViewer());
        $pdfgenerator->setHeaderViewer($this->getHeaderViewerFeuilles("Emargement"));
        $pdfgenerator->setFooterViewer($this->getFooterViewer());
        $pdfgenerator->setContentViewer($this->getContentViewer());

        $pdfgenerator->generate($filename, $type);
    }

    function buildHeaderNomClient() {
        global $adb;
        $info_client = array();
        $accountid = $this->focusColumnValue('parent_id');
        $query = "SELECT accountname,bill_street,bill_city,bill_code,phone,email1
            FROM vtiger_account
            INNER JOIN vtiger_accountbillads on vtiger_accountbillads.accountaddressid = vtiger_account.accountid
            INNER JOIN vtiger_accountscf on vtiger_accountscf.accountid = vtiger_account.accountid 
            WHERE vtiger_account.accountid = ?";
        $result = $adb->pquery($query, array($accountid));
        $num_rows = $adb->num_rows($result);
        if ($num_rows) {
            $accountname = ucwords(formatString(strtolower(($adb->query_result($result, 0, 'accountname')))));
            $bill_street = ucwords(strtolower(($adb->query_result($result, 0, 'bill_street'))));
            $bill_city = strtoupper($adb->query_result($result, 0, 'bill_city'));
            $bill_code = $adb->query_result($result, 0, 'bill_code');
            $phone = $adb->query_result($result, 0, 'phone');
            $email = $adb->query_result($result, 0, 'email1');
            $adresscompl = $adb->query_result($result, 0, 'ship_street');

            $account_no = $adb->query_result($result, 0, 'account_no');
            $account_no = str_replace("CLI", "", $account_no);

            $query_contact = "SELECT salutation,firstname,lastname,cf_871 
            FROM vtiger_contactdetails vcontactdetails
            INNER JOIN vtiger_contactscf on vtiger_contactscf.contactid = vcontactdetails.contactid 
            where vcontactdetails.accountid = ? and cf_984 = ?";
            $result_contact = $adb->pquery($query_contact, array($accountid, 1));
            $num_rows_contact = $adb->num_rows($result_contact);
            if ($num_rows_contact) {
                $titre_contact = $adb->query_result($result, 0, 'titre_contact');
                $nom_contact = $adb->query_result($result, 0, 'nom_contact');
                $prenom_contact = $adb->query_result($result, 0, 'prenom_contact');
                $travail_contact = $adb->query_result($result, 0, 'cf_871');
                $salutation_contact = $adb->query_result($result, 0, 'salutation');
                $info_client['titre_contact'] = $titre_contact;
                $info_client['nom_contact'] = formatString($nom_contact);
                $info_client['prenom_contact'] = formatString($prenom_contact);
                $info_client['travail_contact'] = $travail_contact;
                $info_client['salutation_contact'] = $salutation_contact;
            }
        }
        $info_client['accountname'] = $accountname;
        $info_client['adresse'] = formatString($bill_street);
        $info_client['adresscompl'] = formatString($adresscompl);
        $info_client['ville'] = formatString($bill_city);
        $info_client['cp'] = $bill_code;
        $info_client['phone'] = $phone;
        $info_client['email'] = $email;
        return $info_client;
    }

    function getApprenants() {
        global $adb;
        $info_apprenants = array();
        $id = $this->focusColumnValues(array('record_id'));
        $query = "SELECT id,salutation,firstname,lastname,vtiger_contactsubdetails.birthday,resultat,ticket_examen
                FROM vtiger_sessionsapprenantsrel 
                INNER JOIN vtiger_contactdetails on vtiger_contactdetails.contactid = vtiger_sessionsapprenantsrel.apprenantid 
                INNER JOIN vtiger_contactsubdetails on vtiger_contactsubdetails.contactsubscriptionid = vtiger_contactdetails.contactid 
                WHERE vtiger_sessionsapprenantsrel.id = ?";
        $params = array($id);

        $result = $adb->pquery($query, $params);
        $num_rows_apprenants = $adb->num_rows($result);
        if ($num_rows_apprenants) {
            for ($i = 0; $i < $num_rows_apprenants; $i++) {
                $salutation = $adb->query_result($result, $i, 'salutation');
                $firstname = ucwords(strtolower(($adb->query_result($result, $i, 'firstname'))));
                $lastname = strtoupper(formatString($adb->query_result($result, $i, 'lastname')));
                $birthday_contact = $adb->query_result($result, $i, 'birthday');
                $resultat = $adb->query_result($result, $i, 'resultat');
                $ticket_examen = $adb->query_result($result, $i, 'ticket_examen');

                switch ($salutation) {
                    case 'Mr.':
                        $salutation = "Monsieur";
                        break;
                    case 'Ms.':
                        $salutation = "Madame";
                        break;
                    default:
                        $salutation = $salutation;
                        break;
                }
                $info_apprenants[$i]['salutation'] = $salutation;
                $info_apprenants[$i]['firstname'] = $firstname;
                $info_apprenants[$i]['lastname'] = $lastname;
                $info_apprenants[$i]['birthday'] = $birthday_contact;
                $info_apprenants[$i]['resultat'] = $resultat;
                $info_apprenants[$i]['ticket_examen'] = $ticket_examen;
            }
        }
        $info_apprenants['nbr_apprenants'] = $num_rows_apprenants;
        return $info_apprenants;
    }

    function getDates() {
        global $adb;
        $info_dates = array();
        $id = $this->focusColumnValues(array('record_id'));
        $query = "SELECT id,sequence_no,date_start,start_matin,end_matin,start_apresmidi,end_apresmidi,duree_formation
            FROM vtiger_sessionsdatesrel
            WHERE vtiger_sessionsdatesrel.id = ?";
        $result = $adb->pquery($query, array($id));
        $num_rows_dates = $adb->num_rows($result);
        if ($num_rows_dates) {
            for ($i = 0; $i < $num_rows_dates; $i++) {
                $info_dates[$i]['sequence_no'] = $adb->query_result($result, $i, 'sequence_no');
                $info_dates[$i]['date_start'] = $adb->query_result($result, $i, 'date_start');
                $info_dates[$i]['start_matin'] = $adb->query_result($result, $i, 'start_matin');
                $info_dates[$i]['end_matin'] = $adb->query_result($result, $i, 'end_matin');
                $info_dates[$i]['start_apresmidi'] = $adb->query_result($result, $i, 'start_apresmidi');
                $info_dates[$i]['end_apresmidi'] = $adb->query_result($result, $i, 'end_apresmidi');
                $info_dates[$i]['duree_formation'] = $adb->query_result($result, $i, 'duree_formation');
            }
        }
//        $monfichier = fopen('SalesOrder_info.txt', 'a+');
//        fputs($monfichier, "\n" . ' test1 :  '.$info_dates[0]['end_apresmidi']);
//        fclose($monfichier);
        return $info_dates;
    }

    /* unicnfsecrm_022020_15 */

    function OutputLISTSATISFACTION($filename, $type) {
        if (is_null($this->focus))
            return;

        $pdfgenerator = $this->getPDFGenerator();

        $pdfgenerator->setPagerViewer($this->getPagerViewer());
        $pdfgenerator->setHeaderViewer($this->getHeaderViewerFeuilles("LISTSATISFACTION"));
        $pdfgenerator->setFooterViewer($this->getFooterViewer());
        $pdfgenerator->setContentViewer($this->getContentViewer());

        $pdfgenerator->generate($filename, $type);
    }

    /* unicnfsecrm_022020_20 */

    function OutputSIGNALETIQUE($filename, $type) {
        if (is_null($this->focus))
            return;

        $pdfgenerator = $this->getPDFGenerator();

        $pdfgenerator->setPagerViewer($this->getPagerViewer());
        $pdfgenerator->setHeaderViewer($this->getHeaderViewerFeuilles("SIGNALETIQUE"));
        $pdfgenerator->setFooterViewer($this->getFooterViewer());
        $pdfgenerator->setContentViewer($this->getContentViewer());

        $pdfgenerator->generate($filename, $type);
    }

    function OutputLISTETOKEN($filename, $type) {
        if (is_null($this->focus))
            return;

        $pdfgenerator = $this->getPDFGenerator();

        $pdfgenerator->setPagerViewer($this->getPagerViewer());
        $pdfgenerator->setHeaderViewer($this->getHeaderViewerFeuilles("ListeTOKEN"));
        $pdfgenerator->setFooterViewer($this->getFooterViewer());
        $pdfgenerator->setContentViewer($this->getContentViewer());

        $pdfgenerator->generate($filename, $type);
    }

    function OutputLISTETOKENTEST($filename, $type) {
        if (is_null($this->focus))
            return;

        $pdfgenerator = $this->getPDFGenerator();

        $pdfgenerator->setPagerViewer($this->getPagerViewer());
        $pdfgenerator->setHeaderViewer($this->getHeaderViewerFeuilles("ListeTOKENTEST"));
        $pdfgenerator->setFooterViewer($this->getFooterViewer());
        $pdfgenerator->setContentViewer($this->getContentViewer());

        $pdfgenerator->generate($filename, $type);
    }

    /* uni_cnfsecrm - v2 - modif 145 - DEBUT */

    function OutputLISTETITREHABILITATIONS($filename, $type) {
        if (is_null($this->focus))
            return;

        $pdfgenerator = $this->getPDFGenerator();

        $pdfgenerator->setPagerViewer($this->getPagerViewer());
        $pdfgenerator->setHeaderViewer($this->getHeaderViewerFeuilles("LISTETITRHABILITATIONS"));
        $pdfgenerator->setFooterViewer($this->getFooterViewer());
        $pdfgenerator->setContentViewer($this->getContentViewer());

        $pdfgenerator->generate($filename, $type);
    }

    /* uni_cnfsecrm - v2 - modif 145 - FIN */

    /* unicnfsecrm_022020_20 */

    function getDetailFicheSignaletique() {
        global $adb;
        $detailClient = array();
        $id = $this->focusColumnValues(array('record_id'));
        $query = "SELECT servicename,vtiger_account.phone as phoneclient,accountname,createdtime,
            salutation,firstname,lastname,
            vtiger_contactdetails.phone as phonecontact,cf_984
            FROM vtiger_service
            INNER JOIN vtiger_inventoryproductrel on vtiger_inventoryproductrel.productid = vtiger_service.serviceid
            INNER JOIN vtiger_salesorder on vtiger_salesorder.salesorderid = vtiger_inventoryproductrel.id
            INNER JOIN vtiger_crmentity on vtiger_crmentity.crmid = vtiger_salesorder.salesorderid
            INNER JOIN vtiger_account on vtiger_account.accountid = vtiger_salesorder.accountid
            LEFT join vtiger_contactdetails on vtiger_contactdetails.accountid = vtiger_account.accountid
            LEFT join vtiger_contactscf on vtiger_contactdetails.contactid = vtiger_contactscf.contactid
            WHERE vtiger_salesorder.accountid = ? ORDER BY vtiger_crmentity.createdtime ASC LIMIT 1";
        $result = $adb->pquery($query, array($id));
        $numRows = $adb->num_rows($result);

        $detailClient['servicename'] = $adb->query_result($result, 0, 'servicename');
        $detailClient['phoneclient'] = $adb->query_result($result, 0, 'phoneclient');
        $detailClient['accountname'] = $adb->query_result($result, 0, 'accountname');
        $detailClient['salutation'] = $adb->query_result($result, 0, 'salutation');
        $detailClient['firstname'] = $adb->query_result($result, 0, 'firstname');
        $detailClient['lastname'] = $adb->query_result($result, 0, 'lastname');
        $detailClient['phonecontact'] = $adb->query_result($result, 0, 'phonecontact');
        $detailClient['testContactPrincipal'] = $adb->query_result($result, 0, 'cf_984');

        return $detailClient;
    }

    /* uni_cnfsecrm - modif 81 - DEBUT */

    function getNbrHeures() {
        global $adb;

        $id = $this->focusColumnValues(array('record_id'));
        $query = "SELECT cf_996 as nbrheures
            FROM vtiger_activitycf
            WHERE activityid=?";
        $result = $adb->pquery($query, array($id));
        $nbrheures = $adb->query_result($result, 0, 'nbrheures');

        return $nbrheures;
    }

    /* uni_cnfsecrm - modif 81 - FIN */

    /* uni_cnfsecrm - v2 - modif 176 - DEBUT */

    function OutputPlan($filename, $type) {
        if (is_null($this->focus))
            return;

        $pdfgenerator = $this->getPDFGenerator();

        $pdfgenerator->setPagerViewer($this->getPagerViewer());
        $pdfgenerator->setHeaderViewer($this->getHeaderViewerFeuilles("plan"));
        $pdfgenerator->setFooterViewer($this->getFooterViewer());
        $pdfgenerator->setContentViewer($this->getContentViewer());

        $pdfgenerator->generate($filename, $type);
    }
    public function getListDirectionByApprenant($direction_apprenant) {
//        die();
        global $adb;
        $listDirection = array();
        $id = $this->focusColumnValues(array('record_id'));
        $query = "select vtiger_contactdetails.email,phone,lastname,firstname,vtiger_contactdetails.contactid 
            from vtiger_contactdetails 
            inner JOIN vtiger_contactscf on vtiger_contactscf.contactid = vtiger_contactdetails.contactid 
            INNER JOIN vtiger_crmentity on vtiger_crmentity.crmid = vtiger_contactdetails.contactid 
            where vtiger_contactscf.cf_1316 = ? and vtiger_contactscf.cf_1320 like ? and vtiger_crmentity.deleted = ?";
        $result = $adb->pquery($query, array(1, $direction_apprenant, 0));
        $num_rows_apprenants = $adb->num_rows($result);

        if ($num_rows_apprenants) {
            for ($i = 0; $i < $num_rows_apprenants; $i++) {
                $listDirection[$i]['firstname'] = ucwords(formatString(strtolower(($adb->query_result($result, $i, 'firstname')))));
                $listDirection[$i]['lastname'] = ucwords(formatString(strtolower(($adb->query_result($result, $i, 'lastname')))));
                $listDirection[$i]['email'] = $adb->query_result($result, $i, 'email');
                $listDirection[$i]['phone'] = $adb->query_result($result, $i, 'phone');
            }
        }
        return $listDirection;
    }
        /* uni_cnfsecrm - v2 - modif 176 - FIN */

}

?>
