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

class Vtiger_InvoicePDFController extends Vtiger_InventoryPDFController {

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
        $associated_products = $this->associated_products;
        $final_details = $associated_products[1]['final_details'];
        $no_of_decimal_places = getCurrencyDecimalPlaces();
        $summaryModel = new Vtiger_PDF_Model();

        $netTotal = $discount = $handlingCharges = $handlingTaxes = 0;
        $adjustment = $grandTotal = 0;
        $frais = $this->getFrais();
        $frais_deplacement = $frais["frais_deplacement"];
        $frais_repas = $frais["frais_repas"];
        $frais_hebergement = $frais["frais_hebergement"];
        $autres_frais = $frais_repas + $frais_hebergement;

        $totalfrais = $frais_deplacement + $autres_frais;

        $productLineItemIndex = 0;
        $sh_tax_percent = 0;
        foreach ($associated_products as $productLineItem) {
            ++$productLineItemIndex;
            $netTotal += $productLineItem["netPrice{$productLineItemIndex}"];
        }

        $sign = ($this->focusColumnValue('cf_1078') == "1") ? "- " : "";
        $netTotal = number_format(($netTotal + $this->totaltaxes), getCurrencyDecimalPlaces(), '.', '');
        $netTotal_affiche = ($netTotal > 0) ? $sign . "" . $netTotal : $netTotal;
        $summaryModel->set(getTranslatedString("Total brut HT", $this->moduleName), $sign . "" . $this->formatPrice($netTotal) . " €");

        $discount_amount = $final_details["discount_amount_final"];
        $discount_percent = $final_details["discount_percentage_final"];

        $discount = 0.0;
        $discount_final_percent = '0.00';
        if ($final_details['discount_type_final'] == 'amount') {
            $discount = $discount_amount;
        } else if ($final_details['discount_type_final'] == 'percentage') {
            $discount_final_percent = $discount_percent;
            $discount = (($discount_percent * $final_details["hdnSubTotal"]) / 100);
        }
        $summaryModel->set(getTranslatedString("Discount", $this->moduleName), $this->formatPrice($discount) . " €");
        /* uni_cnfsecrm - v2 - modif 116 - DEBUT */
        $summaryModel->set(getTranslatedString("Frais", $this->moduleName), $this->formatPrice($totalfrais) . " €");
        /* uni_cnfsecrm - v2 - modif 116 - FIN */
        $group_total_tax_percent = '0.00';
        $summaryModel->set(getTranslatedString("Financement", $this->moduleName), $this->formatPrice($final_details['financement']) . " €");

        $netHT = $netTotal + $totalfrais - $final_details['financement'] - $final_details['montantclient'] - $discount;
        $netHT = $this->formatPrice($netHT);
        $netHT_affiche = ($netHT > 0) ? $sign . "" . $netHT : $netHT;
        $summaryModel->set(getTranslatedString("Net HT", $this->moduleName), $netHT_affiche . " €");
        //To calculate the group tax amount
        if ($final_details['taxtype'] == 'group') {
            $group_tax_details = $final_details['taxes'];
            for ($i = 0; $i < count($group_tax_details); $i++) {
                $group_total_tax_percent += $group_tax_details[$i]['percentage'];
            }
            $totalTva = $this->formatPrice($final_details['tax_totalamount']);
            $totalTva_affiche = ($totalTva > 0) ? $sign . "" . $totalTva : $totalTva;
            $summaryModel->set(getTranslatedString("Total TVA", $this->moduleName), $totalTva_affiche . " €");
        }
        //Shipping & Handling taxes
        $sh_tax_details = $final_details['sh_taxes'];
        for ($i = 0; $i < count($sh_tax_details); $i++) {
            $sh_tax_percent = $sh_tax_percent + $sh_tax_details[$i]['percentage'];
        }
        //obtain the Currency Symbol
        $currencySymbol = $this->buildCurrencySymbol();

        //$summaryModel->set(getTranslatedString("Adjustment", $this->moduleName), $this->formatPrice($final_details['adjustment']));
        $ttc = $this->formatPrice($final_details['grandTotal']);
        $ttc_affiche = ($ttc > 0) ? $sign . "" . $ttc : $ttc;
        $summaryModel->set(getTranslatedString("TTC", $this->moduleName), $ttc_affiche . " €"); // TODO add currency string

        if ($this->moduleName == 'Invoice') {
            $receivedVal = $this->focusColumnValue("received");
            if (!$receivedVal) {
                $this->focus->column_fields["received"] = 0;
            }
            //If Received value is exist then only Recieved, Balance details should present in PDF
            //          if ($this->formatPrice($this->focusColumnValue("received")) > 0) {
            $received = $this->focusColumnValue("received");
            $received = ($this->focusColumnValue('cf_1078') == "1") ? "0" : $received;
            $summaryModel->set(getTranslatedString("Acompte", $this->moduleName), $this->formatPrice($received) . " €");

            $netAPaye = $this->formatPrice($this->focusColumnValue("balance"));
            $netAPaye_affiche = ($netAPaye > 0) ? $sign . "" . $netAPaye : $netAPaye;
            $summaryModel->set(getTranslatedString("NET A PAYER", $this->moduleName), $netAPaye_affiche . " €");
//            }
        }

        $info_apprenants = $this->buildHeaderApprenants();
        //var_dump($info_apprenants);
        $summaryModel->set('info_apprenants', $info_apprenants);

        $info_dates = $this->buildHeaderDates();
        $summaryModel->set('info_dates', $info_dates);

        $taux_tva = $final_details["taxes"]["1"]["percentage"];
        $taux_tva = number_format($taux_tva, $no_of_decimal_places, '.', '');
        $summaryModel->set('taux_tva', $taux_tva);

        $echeance = $this->focusColumnValue('duedate');
        $type = $this->focusColumnValue('cf_1004');
        $mode_reglement = $this->focusColumnValue('cf_1083');
        $valeurs = array('echeance' => $echeance, 'type' => $type, 'mode_reglement' => $mode_reglement);

        $summaryModel->set('valeurs', $valeurs);
        $payments = $this->getPayments();
        $summaryModel->set('payments', $payments);

        //var_dump($summaryModel);
        return $summaryModel;
    }

    function getWatermarkContent() {
        return $this->focusColumnValue('invoicestatus');
    }

    function buildHeaderModelColumnLeft() {
        $subject = $this->focusColumnValue('subject');
        $invoice_no = $this->focusColumnValue('invoice_no');
        $invoice_date = $this->getCrmentity();
        $invoice_ref = $this->focusColumnValue('customerno');
        $numero_facture = $this->focusColumnValue('cf_1033');
        $facture_parent = $this->focusColumnValue('cf_1039');
        $customerName = $this->resolveReferenceLabel($this->focusColumnValue('account_id'), 'Accounts');
        $contactName = $this->resolveReferenceLabel($this->focusColumnValue('contact_id'), 'Contacts');
        $purchaseOrder = $this->focusColumnValue('vtiger_purchaseorder');
        $quoteName = $this->resolveReferenceLabel($this->focusColumnValue('quote_id'), 'Quotes');
        $type_formation = $this->focusColumnValue('cf_977');
        $date_debut_formation = $this->focusColumnValue('cf_988');
        $date = new DateTime($date_debut_formation);
        $date_debut_formation = $date->format('d-m-Y');
        $date_fin_formations = $this->focusColumnValue('cf_990');
        $date = new DateTime($date_fin_formations);
        $date_fin_formations = $date->format('d-m-Y');
        $ville_devis = $this->focusColumnValue('bill_city');
        $lieu = $this->focusColumnValue('lieu');
        if ($ville_devis == "") {
            $ville_devis = getSingleFieldValue("vtiger_lieucf", "cf_945", "lieuid", $lieu);
        }
        $infos_client = $this->buildHeaderNomClient();
        $info_contact = $this->buildHeaderInfosContact();
        $info_apprenants = $this->buildHeaderApprenants();
        $info_dates = $this->buildHeaderDates();
        $info_product = $this->buildHeaderProduct();
        $quote_no = $this->focusColumnValue('quote_no');
        $subjectLabel = getTranslatedString('Subject', $this->moduleName);
        $quoteNameLabel = getTranslatedString('Quote Name', $this->moduleName);
        $customerNameLabel = getTranslatedString('Customer Name', $this->moduleName);
        $contactNameLabel = getTranslatedString('Contact Name', $this->moduleName);
        $purchaseOrderLabel = getTranslatedString('Purchase Order', $this->moduleName);
        $type = $this->focusColumnValue('cf_1004');
        $info_vendor = $this->getVendor($this->focusColumnValue('financeur'));
        $echeance = $this->focusColumnValue('duedate');

        $modelColumn0 = array(
            $subjectLabel => $subject,
            'info_client' => $infos_client,
            'info_contact' => $info_contact,
            'info_apprenants' => $info_apprenants,
            $contactNameLabel => $contactName,
            $purchaseOrderLabel => $purchaseOrder,
            $quoteNameLabel => $quoteName,
            'type_formation' => $type_formation,
            'date_debut_formation' => $date_debut_formation,
            'date_fin_formations' => $date_fin_formations,
            'info_dates' => $info_dates,
            'quote_no' => $quote_no,
            'info_product' => $info_product,
            'invoice_ref' => $invoice_ref,
            'invoice_date' => $invoice_date,
            'invoice_no' => $invoice_no,
            'numero_facture' => $numero_facture,
            'facture_parent' => $facture_parent,
            'echeance' => $echeance,
            'type' => $type,
            'info_vendor' => $info_vendor,
        );


//        $monfichier = fopen('sales_order_detail.txt', 'a+');
//        fputs($monfichier, "\n" . ' detail :  '.$date_fin_formations);
//        fclose($monfichier);
        return $modelColumn0;
    }

}

?>
