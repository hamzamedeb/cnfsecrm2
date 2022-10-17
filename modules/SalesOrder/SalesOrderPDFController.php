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
include_once dirname(__FILE__) . '/SalesOrderPDFHeaderViewer.php';
include_once 'vtlib/Vtiger/PDF/inventory/HeaderViewerEmargement.php';
include_once 'vtlib/Vtiger/PDF/inventory/HeaderViewerSatisfaction.php';
include_once 'vtlib/Vtiger/PDF/inventory/HeaderViewerConvocation.php';

class Vtiger_SalesOrderPDFController extends Vtiger_InventoryPDFController {

    function buildHeaderModelTitle() {
        $singularModuleNameKey = 'SINGLE_' . $this->moduleName;
        $translatedSingularModuleLabel = getTranslatedString($singularModuleNameKey, $this->moduleName);
        if ($translatedSingularModuleLabel == $singularModuleNameKey) {
            $translatedSingularModuleLabel = getTranslatedString($this->moduleName, $this->moduleName);
        }
        return sprintf("%s: %s", $translatedSingularModuleLabel, $this->focusColumnValue('salesorder_no'));
    }

//	function getHeaderViewer() {
//		$headerViewer = new SalesOrderPDFHeaderViewer();
//		$headerViewer->setModel($this->buildHeaderModel());
//		return $headerViewer;
//	}

    function buildHeaderModelColumnLeft() {
        $subject = $this->focusColumnValue('subject');
        $customerName = $this->resolveReferenceLabel($this->focusColumnValue('account_id'), 'Accounts');
        $contactName = $this->resolveReferenceLabel($this->focusColumnValue('contact_id'), 'Contacts');
        $purchaseOrder = $this->focusColumnValue('vtiger_purchaseorder');
        $quoteName = $this->resolveReferenceLabel($this->focusColumnValue('quote_id'), 'Quotes');
        $type_formation = $this->focusColumnValue('cf_977');
        $date_debut_formation = $this->focusColumnValue('cf_988');
        $sessionid = $this->focusColumnValue('session');
        $date = new DateTime($date_debut_formation);
        $date_debut_formation = $date->format('d-m-Y');
        $date_fin_formations = $this->focusColumnValue('cf_990');
        $date = new DateTime($date_fin_formations);
        $date_fin_formations = $date->format('d-m-Y');
        $adresse_formation = $this->focusColumnValue('bill_street');
        $cp_formation = $this->focusColumnValue('bill_code');
        $ville_formation = $this->focusColumnValue('bill_city');
        $observations = $this->focusColumnValue('cf_1037');
        $infos_client = $this->buildHeaderNomClient();
        $info_contact = $this->buildHeaderInfosContact();
        $info_apprenants = $this->buildHeaderApprenants();
        $info_dates = $this->buildHeaderDates();
        $nbrheures = $this->getNbrHeures($sessionid);
        $associated_products = $this->associated_products;
        $final_details = $associated_products[1]['final_details'];
        $discount_amount = $final_details["discount_amount_final"];
        $discount_percent = $final_details["discount_percentage_final"];
        $taux_tva = $final_details["taxes"]["1"]["percentage"];
        $tax_totalamount = $final_details['tax_totalamount'];
        $financement = $final_details['financement'];
        $frais = $this->getFrais();
        $totalht = $discount = $handlingCharges = $handlingTaxes = 0;
        $adjustment = $grandTotal = 0;
        $formateur = $this->getFormateur();
        $elearning = $this->focusColumnValue('cf_1204');

        $productLineItemIndex = 0;
        $sh_tax_percent = 0;

        $n_convention = $this->focusColumnValue('cf_982');
        $organizationdetails = $this->getOrganizationdetails();
        $info_product = $this->buildHeaderProduct();
        $crmentity = $this->getCrmentity();
        $info_financeur = $this->getFinanceur();

        foreach ($associated_products as $productLineItem) {
            ++$productLineItemIndex;
//            echo "netPrice{$productLineItemIndex}" . " \n ";
//            echo $productLineItem["netPrice{$productLineItemIndex}"] . " \n ";
            $soustotalht += $productLineItem["netPrice{$productLineItemIndex}"];
        }

        $frais_deplacement = $frais["frais_deplacement"];
        $frais_repas = $frais["frais_repas"];
        $frais_hebergement = $frais["frais_hebergement"];
        $autres_frais = $frais_repas + $frais_hebergement;

        $totalfrais = $frais_deplacement + $autres_frais;

        $discount = 0.0;
        $discount_final_percent = '0.00';
        if ($final_details['discount_type_final'] == 'amount') {
            $discount = $discount_amount;
        } else if ($final_details['discount_type_final'] == 'percentage') {
            $discount_final_percent = $discount_percent;
            $discount = (($discount_percent * $final_details["hdnSubTotal"]) / 100);
        }
        $discount = floatval($discount);
        $totalht = $soustotalht + $totalfrais - $discount;

        $group_total_tax = '0.00';
        //To calculate the group tax amount
        if ($final_details['taxtype'] == 'group') {
            $group_tax_details = $final_details['taxes'];
            for ($i = 1; $i <= count($group_tax_details); $i++) {
                $group_total_tax += $group_tax_details[$i]['amount'];
            }
        }
        $group_total_tax = $this->formatPrice($final_details['group_total_tax']);
        $totalttc = $this->formatPrice($final_details['grandTotal']);

        $quoteNameLabel = getTranslatedString('Quote Name', $this->moduleName);
        $customerNameLabel = getTranslatedString('Customer Name', $this->moduleName);
        $contactNameLabel = getTranslatedString('Contact Name', $this->moduleName);
        $purchaseOrderLabel = getTranslatedString('Purchase Order', $this->moduleName);

        //$soustotalht = $this->formatPrice($soustotalht);
        //$totalht = $this->formatPrice($totalht);
        //$totalfrais = $this->formatPrice($totalfrais);
        //$discount = $this->formatPrice($discount);
        //$frais_deplacement = $this->formatPrice($frais_deplacement);
        //$financement = $this->formatPrice($financement);
        //$autres_frais = $this->formatPrice($autres_frais);
        $modelColumn0 = array(
            'subject' => $subject,
            'info_client' => $infos_client,
            'info_contact' => $info_contact,
            'info_apprenants' => $info_apprenants,
            'info_dates' => $info_dates,
            $contactNameLabel => $contactName,
            $purchaseOrderLabel => $purchaseOrder,
            $quoteNameLabel => $quoteName,
            'type_formation' => $type_formation,
            'date_debut_formation' => $date_debut_formation,
            'date_fin_formations' => $date_fin_formations,
            'adresse_formation' => $adresse_formation,
            'cp_formation' => $cp_formation,
            'ville_formation' => $ville_formation,
            'soustotalht' => $soustotalht,
            'frais_deplacement' => $frais_deplacement,
            'autres_frais' => $autres_frais,
            'totalfrais' => $totalfrais,
            'totalht' => $totalht,
            'taux_tva' => $taux_tva,
            'tax_totalamount' => $tax_totalamount,
            'discount_amount' => $discount,
            'totalttc' => $totalttc,
            'financement' => $financement,
            'info_product' => $info_product,
            'n_convention' => $n_convention,
            'organizationdetails' => $organizationdetails,
            'crmentity' => $crmentity,
            'observations' => $observations,
            'info_financeur' => $info_financeur,
            'formateur' => $formateur,
            'elearning' => $elearning,
            'nbrheures' => $nbrheures,
        );
        return $modelColumn0;
    }

    function buildHeaderModelColumnCenter() {
        $subject = $this->focusColumnValue('subject');
        $customerName = $this->resolveReferenceLabel($this->focusColumnValue('account_id'), 'Accounts');
        $contactName = $this->resolveReferenceLabel($this->focusColumnValue('contact_id'), 'Contacts');
        $purchaseOrder = $this->focusColumnValue('vtiger_purchaseorder');
        $quoteName = $this->resolveReferenceLabel($this->focusColumnValue('quote_id'), 'Quotes');

        $subjectLabel = getTranslatedString('Subject', $this->moduleName);
        $quoteNameLabel = getTranslatedString('Quote Name', $this->moduleName);
        $customerNameLabel = getTranslatedString('Customer Name', $this->moduleName);
        $contactNameLabel = getTranslatedString('Contact Name', $this->moduleName);
        $purchaseOrderLabel = getTranslatedString('Purchase Order', $this->moduleName);

        $modelColumn1 = array(
            $subjectLabel => $subject,
            $customerNameLabel => $customerName,
            $contactNameLabel => $contactName,
            $purchaseOrderLabel => $purchaseOrder,
            $quoteNameLabel => $quoteName
        );
        return $modelColumn1;
    }

    function buildHeaderModelColumnRight() {
        $issueDateLabel = getTranslatedString('Issued Date', $this->moduleName);
        $validDateLabel = getTranslatedString('Due Date', $this->moduleName);
        $billingAddressLabel = getTranslatedString('Billing Address', $this->moduleName);
        $shippingAddressLabel = getTranslatedString('Shipping Address', $this->moduleName);


        $modelColumn2 = array(
            'dates' => array(
                $issueDateLabel => $this->formatDate(date("Y-m-d")),
                $validDateLabel => $this->formatDate($this->focusColumnValue('duedate')),
            ),
            $billingAddressLabel => $this->buildHeaderBillingAddress(),
            $shippingAddressLabel => $this->buildHeaderShippingAddress()
        );
        return $modelColumn2;
    }

    function getWatermarkContent() {
        return $this->focusColumnValue('sostatus');
    }

    function getHeaderViewerFeuilles($type) {
        /* wajcrmcnfse */
        $module = $this->moduleName;

        switch ($type) {
            case 'Emargement':
                $headerViewer = new Vtiger_PDF_EmargementHeaderViewer();
                break;

            case 'Satisfaction':
                $headerViewer = new Vtiger_PDF_SatisfactionHeaderViewer();
                break;

            case 'Convocation':
                $headerViewer = new Vtiger_PDF_ConvocationHeaderViewer();
                break;

            default:
                $headerViewer = new Vtiger_PDF_EmargementHeaderViewer();
                break;
        }
        /* wajcrmcnfse - fin */
        $headerViewer->setModel($this->buildHeaderModel());
        return $headerViewer;
    }

    function OutputSatisfaction($filename, $type) {
        if (is_null($this->focus))
            return;

        $pdfgenerator = $this->getPDFGenerator();

        $pdfgenerator->setPagerViewer($this->getPagerViewer());
        $pdfgenerator->setHeaderViewer($this->getHeaderViewerFeuilles("Satisfaction"));
        $pdfgenerator->setFooterViewer($this->getFooterViewer());
        $pdfgenerator->setContentViewer($this->getContentViewer());

        $pdfgenerator->generate($filename, $type);
    }

    function OutputConvocation($filename, $type) {
        if (is_null($this->focus))
            return;

        $pdfgenerator = $this->getPDFGenerator();

        $pdfgenerator->setPagerViewer($this->getPagerViewer());
        $pdfgenerator->setHeaderViewer($this->getHeaderViewerFeuilles("Convocation"));
        $pdfgenerator->setFooterViewer($this->getFooterViewer());
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

}

?>