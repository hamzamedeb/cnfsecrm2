<?php

/* * *******************************************************************************
 * * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *
 * ****************************************************************************** */

include_once 'vtlib/Vtiger/PDF/models/Model.php';
include_once 'vtlib/Vtiger/PDF/inventory/HeaderViewer.php';
/* wajcrmcnfse */
include_once 'vtlib/Vtiger/PDF/inventory/HeaderViewerQuot.php';
include_once 'vtlib/Vtiger/PDF/inventory/HeaderViewerInvo.php';
include_once 'vtlib/Vtiger/PDF/inventory/HeaderViewerSales.php';
include_once 'vtlib/Vtiger/PDF/inventory/ContentViewerQuot.php';
include_once 'vtlib/Vtiger/PDF/inventory/ContentViewerInvo.php';
include_once 'vtlib/Vtiger/PDF/inventory/ContentViewerSales.php';
/* wajcrmcnfse - fin */
include_once 'vtlib/Vtiger/PDF/inventory/FooterViewer.php';
include_once 'vtlib/Vtiger/PDF/inventory/ContentViewer.php';
include_once 'vtlib/Vtiger/PDF/inventory/ContentViewer2.php';
include_once 'vtlib/Vtiger/PDF/viewers/PagerViewer.php';
include_once 'vtlib/Vtiger/PDF/PDFGenerator.php';
include_once 'vtlib/Vtiger/PDF/PDFGeneratorEvents.php';
include_once 'data/CRMEntity.php';

class Vtiger_InventoryPDFController {

    protected $module;
    protected $focus = null;

    function __construct($module) {
        $this->moduleName = $module;
    }

    function loadRecord($id) {
        global $current_user;
        $this->focus = $focus = CRMEntity::getInstance($this->moduleName);
        $focus->retrieve_entity_info($id, $this->moduleName);
        $focus->apply_field_security();
        $focus->id = $id;
        $this->associated_products = getAssociatedProducts($this->moduleName, $focus);
    }

    function getPDFGenerator() {
        return new Vtiger_PDF_Generator();
    }

    function getContentViewer() {
//		if($this->focusColumnValue('hdnTaxType') == "individual") {
//			$contentViewer = new Vtiger_PDF_InventoryContentViewer();
//		} else {
//			$contentViewer = new Vtiger_PDF_InventoryTaxGroupContentViewer();
//		}

        /* wajcnfsecrm */
        $module = $this->moduleName;
        if ($module == "Quotes") {
            $contentViewer = new Vtiger_PDF_InventoryQuotContentViewer();
            $contentViewer->setContentModels($this->buildContentModels());
            $contentViewer->setSummaryModel($this->buildSummaryModel());
            $contentViewer->setLabelModel($this->buildContentLabelModel());
            $contentViewer->setWatermarkModel($this->buildWatermarkModel());
        } else if ($module == "Invoice") {
            $contentViewer = new Vtiger_PDF_InventoryInvoContentViewer();
            $contentViewer->setContentModels($this->buildContentModels());
            $contentViewer->setSummaryModel($this->buildSummaryModel());
            $contentViewer->setLabelModel($this->buildContentLabelModel());
            $contentViewer->setWatermarkModel($this->buildWatermarkModel());
        } else if ($module == "SalesOrder") {
            $contentViewer = new Vtiger_PDF_InventorySalesContentViewer();
            //  $contentViewer->setContentModels($this->buildContentModels());
            // $contentViewer->setSummaryModel($this->buildSummaryModel());
            //$contentViewer->setLabelModel($this->buildContentLabelModel());
            //$contentViewer->setWatermarkModel($this->buildWatermarkModel());
        } else {
            $contentViewer = new Vtiger_PDF_InventoryContentViewer();
            $contentViewer->setContentModels($this->buildContentModels());
            $contentViewer->setSummaryModel($this->buildSummaryModel());
            $contentViewer->setLabelModel($this->buildContentLabelModel());
            $contentViewer->setWatermarkModel($this->buildWatermarkModel());
        }
        /* wajcnfsecrm - fin */
        return $contentViewer;
    }

    function getHeaderViewer() {
        /* wajcrmcnfse */
        $module = $this->moduleName;
        if ($module == "Quotes") {
            $headerViewer = new Vtiger_PDF_InventoryQuotHeaderViewer();
        } else if ($module == "Invoice") {
            $headerViewer = new Vtiger_PDF_InventoryInvoHeaderViewer();
        } else if ($module == "SalesOrder") {
            $headerViewer = new Vtiger_PDF_InventorySalesHeaderViewer();
        } else {
            $headerViewer = new Vtiger_PDF_InventoryHeaderViewer();
        }
        /* wajcrmcnfse - fin */
        $headerViewer->setModel($this->buildHeaderModel());
        return $headerViewer;
    }

    function getFooterViewer() {
        $footerViewer = new Vtiger_PDF_InventoryFooterViewer();
        $footerViewer->setModel($this->buildFooterModel());
        $footerViewer->setLabelModel($this->buildFooterLabelModel());
        $footerViewer->setOnLastPage();
        return $footerViewer;
    }

    function getPagerViewer() {
        $pagerViewer = new Vtiger_PDF_PagerViewer();
        $pagerViewer->setModel($this->buildPagermodel());
        return $pagerViewer;
    }

    function Output($filename, $type) {
        if (is_null($this->focus))
            return;

        $pdfgenerator = $this->getPDFGenerator();

        $pdfgenerator->setPagerViewer($this->getPagerViewer());
        $pdfgenerator->setHeaderViewer($this->getHeaderViewer());
        $pdfgenerator->setFooterViewer($this->getFooterViewer());
        $pdfgenerator->setContentViewer($this->getContentViewer());

        $pdfgenerator->generate($filename, $type);
    }

    // Helper methods

    function buildContentModels() {
        $associated_products = $this->associated_products;
        $contentModels = array();
        $productLineItemIndex = 0;
        $totaltaxes = 0;
        $datainvoice = array();
        $no_of_decimal_places = getCurrencyDecimalPlaces();
        foreach ($associated_products as $productLineItem) {
            ++$productLineItemIndex;

            $contentModel = new Vtiger_PDF_Model();

            $discountPercentage = 0.00;
            $total_tax_percent = 0.00;
            $producttotal_taxes = 0.00;
            $quantity = '';
            $listPrice = '';
            $discount = '';
            $taxable_total = '';
            $tax_amount = '';
            $producttotal = '';


            $quantity = $productLineItem["qty{$productLineItemIndex}"];
            $listPrice = $productLineItem["listPrice{$productLineItemIndex}"];
            $discount = $productLineItem["discountTotal{$productLineItemIndex}"];

            /* uni_cnfsecrm */
            $naturecalcul = $productLineItem["nature_calcul{$productLineItemIndex}"];
            $nbrjours = $productLineItem["nbr_jours{$productLineItemIndex}"];
            $nbrheures = $productLineItem["nbr_heures{$productLineItemIndex}"];
            $parpersonne = $productLineItem["par_personne{$productLineItemIndex}"];
            switch ($naturecalcul) {
                case 'jour':
                    $prix_formation = $listPrice * $nbrjours;
                    $quantite_affiche = $nbrjours;
                    break;

                case 'heure':
                    $prix_formation = $listPrice * $nbrheures;
                    $quantite_affiche = $nbrheures;
                    break;

                case 'forfait':
                    $prix_formation = $listPrice;
                    $quantite_affiche = 1;
                    break;
                default :
                    $prix_formation = $listPrice;
                    $quantite_affiche = 1;
                    break;
            }
            if ($parpersonne != 'on')
                $quantity = 1;

            $quantite_affiche = $quantite_affiche * $quantity;
            /* uni_cnfsecrm */
            $taxable_total = $quantity * $prix_formation - $discount;
            $taxable_total = number_format($taxable_total, $no_of_decimal_places, '.', '');
            $producttotal = $taxable_total;
            if ($this->focus->column_fields["hdnTaxType"] == "individual") {
                for ($tax_count = 0; $tax_count < count($productLineItem['taxes']); $tax_count++) {
                    $tax_percent = $productLineItem['taxes'][$tax_count]['percentage'];
                    $total_tax_percent += $tax_percent;
                    $tax_amount = (($taxable_total * $tax_percent) / 100);
                    $producttotal_taxes += $tax_amount;
                }
            }

            $producttotal_taxes = number_format($producttotal_taxes, $no_of_decimal_places, '.', '');
            $producttotal = $taxable_total + $producttotal_taxes;
            $producttotal = number_format($producttotal, $no_of_decimal_places, '.', '');
            $tax = $producttotal_taxes;
            $totaltaxes += $tax;
            $totaltaxes = number_format($totaltaxes, $no_of_decimal_places, '.', '');
            $discountPercentage = $productLineItem["discount_percent{$productLineItemIndex}"];
            $productName = decode_html($productLineItem["productName{$productLineItemIndex}"]);
            //get the sub product
            $subProducts = $productLineItem["subProductArray{$productLineItemIndex}"];
            if ($subProducts != '') {
                foreach ($subProducts as $subProduct) {
                    $productName .= "\n" . " - " . decode_html($subProduct);
                }
            }

            $contentModel->set('Name', $productName);
            $contentModel->set('Code', decode_html($productLineItem["hdnProductcode{$productLineItemIndex}"]));
            $contentModel->set('Quantity', $quantite_affiche);
            $contentModel->set('Price', $this->formatPrice($listPrice) . " €");
            $contentModel->set('Discount', $this->formatPrice($discount) . "\n ($discountPercentage%)");
            $contentModel->set('Tax', $this->formatPrice($tax));
            $contentModel->set('Total', $this->formatPrice($producttotal));
            $contentModel->set('Comment', decode_html($productLineItem["comment{$productLineItemIndex}"]));

            $contentModels[] = $contentModel;
        }
        $this->totaltaxes = $totaltaxes; //will be used to add it to the net total

        return $contentModels;
    }

    function buildContentLabelModel() {
        $labelModel = new Vtiger_PDF_Model();
        $labelModel->set('Code', getTranslatedString('Product Code', $this->moduleName));
        $labelModel->set('Name', getTranslatedString('Description', $this->moduleName));
        $labelModel->set('Quantity', getTranslatedString('Quantity', $this->moduleName));
        $labelModel->set('Price', getTranslatedString('Prix U HT', $this->moduleName));
        $labelModel->set('Discount', getTranslatedString('Discount', $this->moduleName));
        $labelModel->set('Tax', getTranslatedString('TVA', $this->moduleName));
        $labelModel->set('Total', getTranslatedString('Prix HT', $this->moduleName));
        $labelModel->set('Comment', getTranslatedString('Comment'), $this->moduleName);
        return $labelModel;
    }

    function buildSummaryModel() {
        $associated_products = $this->associated_products;
        $final_details = $associated_products[1]['final_details'];

        $summaryModel = new Vtiger_PDF_Model();

        $netTotal = $discount = $handlingCharges = $handlingTaxes = 0;
        $adjustment = $grandTotal = 0;

        $productLineItemIndex = 0;
        $sh_tax_percent = 0;
        foreach ($associated_products as $productLineItem) {
            ++$productLineItemIndex;
            $netTotal += $productLineItem["netPrice{$productLineItemIndex}"];
        }
        $netTotal = number_format(($netTotal + $this->totaltaxes), getCurrencyDecimalPlaces(), '.', '');
        $summaryModel->set(getTranslatedString("Net Total", $this->moduleName), $this->formatPrice($netTotal));

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
        $summaryModel->set(getTranslatedString("Discount", $this->moduleName) . "($discount_final_percent%)", $this->formatPrice($discount));

        $group_total_tax_percent = '0.00';
        //To calculate the group tax amount
        if ($final_details['taxtype'] == 'group') {
            $group_tax_details = $final_details['taxes'];
            for ($i = 0; $i < count($group_tax_details); $i++) {
                $group_total_tax_percent += $group_tax_details[$i]['percentage'];
            }
            $summaryModel->set(getTranslatedString("Tax:", $this->moduleName), $this->formatPrice($final_details['tax_totalamount']));
        }
        //Shipping & Handling taxes
        $sh_tax_details = $final_details['sh_taxes'];
        for ($i = 0; $i < count($sh_tax_details); $i++) {
            $sh_tax_percent = $sh_tax_percent + $sh_tax_details[$i]['percentage'];
        }
        //obtain the Currency Symbol
        $currencySymbol = $this->buildCurrencySymbol();

        $summaryModel->set(getTranslatedString("Shipping & Handling Charges", $this->moduleName), $this->formatPrice($final_details['shipping_handling_charge']));
        $summaryModel->set(getTranslatedString("Shipping & Handling Tax:", $this->moduleName) . "($sh_tax_percent%)", $this->formatPrice($final_details['shtax_totalamount']));
        $summaryModel->set(getTranslatedString("Adjustment", $this->moduleName), $this->formatPrice($final_details['adjustment']));
        $summaryModel->set(getTranslatedString("Grand Total:", $this->moduleName) . "(in $currencySymbol)", $this->formatPrice($final_details['grandTotal'])); // TODO add currency string

        if ($this->moduleName == 'Invoice') {
            $receivedVal = $this->focusColumnValue("received");
            if (!$receivedVal) {
                $this->focus->column_fields["received"] = 0;
            }
            //If Received value is exist then only Recieved, Balance details should present in PDF
            if ($this->formatPrice($this->focusColumnValue("received")) > 0) {
                $summaryModel->set(getTranslatedString("Received", $this->moduleName), $this->formatPrice($this->focusColumnValue("received")));
                $summaryModel->set(getTranslatedString("Balance", $this->moduleName), $this->formatPrice($this->focusColumnValue("balance")));
            }
        }
        return $summaryModel;
    }

    function buildHeaderModel() {
        $headerModel = new Vtiger_PDF_Model();
        $headerModel->set('title', $this->buildHeaderModelTitle());
        $modelColumns = array($this->buildHeaderModelColumnLeft(), $this->buildHeaderModelColumnCenter(), $this->buildHeaderModelColumnRight());
        $headerModel->set('columns', $modelColumns);

        return $headerModel;
    }

    function buildHeaderModelTitle() {
        return $this->moduleName;
    }

    function buildHeaderModelColumnLeft() {
        global $adb;

        // Company information
        $result = $adb->pquery("SELECT * FROM vtiger_organizationdetails", array());
        $num_rows = $adb->num_rows($result);
        if ($num_rows) {
            $resultrow = $adb->fetch_array($result);

            $addressValues = array();
            $addressValues[] = $resultrow['address'];
            if (!empty($resultrow['city']))
                $addressValues[] = "\n" . $resultrow['city'];
            if (!empty($resultrow['state']))
                $addressValues[] = "," . $resultrow['state'];
            if (!empty($resultrow['code']))
                $addressValues[] = $resultrow['code'];
            if (!empty($resultrow['country']))
                $addressValues[] = "\n" . $resultrow['country'];

            $additionalCompanyInfo = array();
            if (!empty($resultrow['phone']))
                $additionalCompanyInfo[] = "\n" . getTranslatedString("Phone: ", $this->moduleName) . $resultrow['phone'];
            if (!empty($resultrow['fax']))
                $additionalCompanyInfo[] = "\n" . getTranslatedString("Fax: ", $this->moduleName) . $resultrow['fax'];
            if (!empty($resultrow['website']))
                $additionalCompanyInfo[] = "\n" . getTranslatedString("Website: ", $this->moduleName) . $resultrow['website'];
            if (!empty($resultrow['vatid']))
                $additionalCompanyInfo[] = "\n" . getTranslatedString("VAT ID: ", $this->moduleName) . $resultrow['vatid'];

            $modelColumnLeft = array(
                'logo' => "test/logo/" . $resultrow['logoname'],
                'summary' => decode_html($resultrow['organizationname']),
                'content' => decode_html($this->joinValues($addressValues, ' ') . $this->joinValues($additionalCompanyInfo, ' '))
            );
        }
        return $modelColumnLeft;
    }

    function buildHeaderModelColumnCenter() {
        $customerName = $this->resolveReferenceLabel($this->focusColumnValue('account_id'), 'Accounts');
        $contactName = $this->resolveReferenceLabel($this->focusColumnValue('contact_id'), 'Contacts');

        $customerNameLabel = getTranslatedString('Customer Name', $this->moduleName);
        $contactNameLabel = getTranslatedString('Contact Name', $this->moduleName);
        $modelColumnCenter = array(
            $customerNameLabel => $customerName,
            $contactNameLabel => $contactName,
        );
        return $modelColumnCenter;
    }

    function buildHeaderModelColumnRight() {
        $issueDateLabel = getTranslatedString('Issued Date', $this->moduleName);
        $validDateLabel = getTranslatedString('Valid Date', $this->moduleName);
        $billingAddressLabel = getTranslatedString('Billing Address', $this->moduleName);
        $shippingAddressLabel = getTranslatedString('Shipping Address', $this->moduleName);

        $modelColumnRight = array(
            'dates' => array(
                $issueDateLabel => $this->formatDate(date("Y-m-d")),
                $validDateLabel => $this->formatDate($this->focusColumnValue('validtill')),
            ),
            $billingAddressLabel => $this->buildHeaderBillingAddress(),
            $shippingAddressLabel => $this->buildHeaderShippingAddress()
        );
        return $modelColumnRight;
    }

    function buildFooterModel() {
        $footerModel = new Vtiger_PDF_Model();
        $footerModel->set(Vtiger_PDF_InventoryFooterViewer::$DESCRIPTION_DATA_KEY, from_html($this->focusColumnValue('description')));
        $footerModel->set(Vtiger_PDF_InventoryFooterViewer::$TERMSANDCONDITION_DATA_KEY, from_html($this->focusColumnValue('terms_conditions')));
        return $footerModel;
    }

    function buildFooterLabelModel() {
        $labelModel = new Vtiger_PDF_Model();
        $labelModel->set(Vtiger_PDF_InventoryFooterViewer::$DESCRIPTION_LABEL_KEY, getTranslatedString('Description', $this->moduleName));
        $labelModel->set(Vtiger_PDF_InventoryFooterViewer::$TERMSANDCONDITION_LABEL_KEY, getTranslatedString('Terms & Conditions', $this->moduleName));
        return $labelModel;
    }

    function buildPagerModel() {
        $footerModel = new Vtiger_PDF_Model();
        $footerModel->set('format', '-%s-');
        return $footerModel;
    }

    function getWatermarkContent() {
        return '';
    }

    function buildWatermarkModel() {
        $watermarkModel = new Vtiger_PDF_Model();
        $watermarkModel->set('content', $this->getWatermarkContent());
        return $watermarkModel;
    }

    function buildHeaderBillingAddress() {
        $billPoBox = $this->focusColumnValues(array('bill_pobox'));
        $billStreet = $this->focusColumnValues(array('bill_street'));
        $billCity = $this->focusColumnValues(array('bill_city'));
        $billState = $this->focusColumnValues(array('bill_state'));
        $billCountry = $this->focusColumnValues(array('bill_country'));
        $billCode = $this->focusColumnValues(array('bill_code'));
        $address = $this->joinValues(array($billPoBox, $billStreet), ' ');
        $address .= "\n" . $this->joinValues(array($billCity, $billState), ',') . " " . $billCode;
        $address .= "\n" . $billCountry;
        return $address;
    }

    function buildHeaderShippingAddress() {
        $shipPoBox = $this->focusColumnValues(array('ship_pobox'));
        $shipStreet = $this->focusColumnValues(array('bill_street'));
        $shipCity = $this->focusColumnValues(array('bill_city'));
        $shipState = $this->focusColumnValues(array('ship_state'));
        $shipCountry = $this->focusColumnValues(array('ship_country'));
        $shipCode = $this->focusColumnValues(array('bill_code'));
        $address = $this->joinValues(array($shipPoBox, $shipStreet), ' ');
        $address .= "\n" . $this->joinValues(array($shipCity, $shipState), ',') . " " . $shipCode;
        $address .= "\n" . $shipCountry;
        return $address;
    }

    function buildCurrencySymbol() {
        global $adb;
        $currencyId = $this->focus->column_fields['currency_id'];
        if (!empty($currencyId)) {
            $result = $adb->pquery("SELECT currency_symbol FROM vtiger_currency_info WHERE id=?", array($currencyId));
            return decode_html($adb->query_result($result, 0, 'currency_symbol'));
        }
        return false;
    }

    function focusColumnValues($names, $delimeter = "\n") {
        if (!is_array($names)) {
            $names = array($names);
        }
        $values = array();
        foreach ($names as $name) {
            $value = $this->focusColumnValue($name, false);
            if ($value !== false) {
                $values[] = $value;
            }
        }
        return $this->joinValues($values, $delimeter);
    }

    function focusColumnValue($key, $defvalue = '') {
        $focus = $this->focus;
        if (isset($focus->column_fields[$key])) {
            return decode_html($focus->column_fields[$key]);
        }
        return $defvalue;
    }

    function resolveReferenceLabel($id, $module = false) {
        if (empty($id)) {
            return '';
        }
        if ($module === false) {
            $module = getSalesEntityType($id);
        }
        $label = getEntityName($module, array($id));
        return decode_html($label[$id]);
    }

    function joinValues($values, $delimeter = "\n") {
        $valueString = '';
        foreach ($values as $value) {
            if (empty($value))
                continue;
            $valueString .= $value . $delimeter;
        }
        return rtrim($valueString, $delimeter);
    }

    function formatNumber($value) {
        return number_format($value);
    }

    function formatPrice($value, $decimal = 2) {
        $currencyField = new CurrencyField($value);
        return $currencyField->getDisplayValue(null, true);
    }

    function formatDate($value) {
        return DateTimeField::convertToUserFormat($value);
    }

    function formatString($value) {
        $value = str_replace("&#039;", "'", $value);
        $value = html_entity_decode($value);
        return $value;
    }

    /* uni_cnfsecrm */

    function buildHeaderNomClient() {
        global $adb;
        $info_client = array();
        $accountid = $this->focusColumnValue('account_id');
        $query = "SELECT accountname,bill_street,bill_city,bill_code,phone,email1
            FROM vtiger_account
            INNER JOIN vtiger_accountbillads on vtiger_accountbillads.accountaddressid = vtiger_account.accountid
            INNER JOIN vtiger_accountscf on vtiger_accountscf.accountid = vtiger_account.accountid 
            WHERE vtiger_account.accountid = ?";
        $result = $adb->pquery($query, array($accountid));
        $num_rows = $adb->num_rows($result);
        if ($num_rows) {
            $accountname = $adb->query_result($result, 0, 'accountname');
            $bill_street = $adb->query_result($result, 0, 'bill_street');
            $bill_city = $adb->query_result($result, 0, 'bill_city');
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
        $info_client['accountname'] = ucwords(strtolower((formatString($accountname))));
        $info_client['adresse'] = ucwords(strtolower((formatString($bill_street))));
        $info_client['adresscompl'] = formatString($adresscompl);
        $info_client['ville'] = strtoupper(formatString($bill_city));
        $info_client['cp'] = $bill_code;
        $info_client['phone'] = $phone;
        $info_client['email'] = $email;
        return $info_client;
    }

    function buildHeaderInfosContact() {
        global $adb;
        $info_contact = array();
        $contactid = $this->focusColumnValue('contact_id');
        $query = "SELECT salutation,firstname,lastname,cf_871 
            FROM vtiger_contactdetails vcontactdetails
            INNER JOIN vtiger_contactscf on vtiger_contactscf.contactid = vcontactdetails.contactid 
            where vcontactdetails.contactid = ?";
        $result = $adb->pquery($query, array($contactid));
        $num_rows_contact = $adb->num_rows($result);
        if ($num_rows_contact) {
            $titre_contact = $adb->query_result($result, 0, 'salutation');
            $nom_contact = strtoupper($adb->query_result($result, 0, 'lastname'));
            $prenom_contact = ucwords(strtolower(($adb->query_result($result, 0, 'firstname'))));
            $travail_contact = getTranslatedString($adb->query_result($result, 0, 'cf_871'));
            $salutation_contact = $adb->query_result($result, 0, 'salutation');
        }
        $info_contact['titre_contact'] = $titre_contact;
        $info_contact['nom_contact'] = $nom_contact;
        $info_contact['prenom_contact'] = $prenom_contact;
        $info_contact['travail_contact'] = $travail_contact;
        $info_contact['salutation_contact'] = $salutation_contact;
        return $info_contact;
    }

    function buildHeaderApprenants() {
        global $adb;
        $info_apprenants = array();
        $id = $this->focusColumnValues(array('record_id'));
        $query = "SELECT id,salutation,firstname,lastname,vtiger_contactsubdetails.birthday,title 
                FROM vtiger_inventoryapprenantsrel 
                INNER JOIN vtiger_contactdetails on vtiger_contactdetails.contactid = vtiger_inventoryapprenantsrel.apprenantid 
                INNER JOIN vtiger_contactsubdetails on vtiger_contactsubdetails.contactsubscriptionid = vtiger_contactdetails.contactid 
                WHERE vtiger_inventoryapprenantsrel.id = ?";
        $result = $adb->pquery($query, array($id));
        $num_rows_apprenants = $adb->num_rows($result);
        if ($num_rows_apprenants) {
            for ($i = 0; $i < $num_rows_apprenants; $i++) {
                $salutation = $adb->query_result($result, $i, 'salutation');
                $firstname = ucwords(formatString(strtolower(($adb->query_result($result, $i, 'firstname')))));
                $lastname = strtoupper(formatString($adb->query_result($result, $i, 'lastname')));
                $birthday_contact = $adb->query_result($result, $i, 'birthday');
                $title_contact = $adb->query_result($result, $i, 'title');
                $info_apprenants[$i]['salutation'] = $salutation;
                $info_apprenants[$i]['firstname'] = $firstname;
                $info_apprenants[$i]['lastname'] = $lastname;
                $info_apprenants[$i]['birthday'] = $birthday_contact;
                $info_apprenants[$i]['title'] = $title_contact;
            }
        }
        $info_apprenants['nbr_apprenants'] = $num_rows_apprenants;
        return $info_apprenants;
    }

    function buildHeaderDates() {
        global $adb;
        $info_dates = array();
        $id = $this->focusColumnValues(array('record_id'));
        $query = "SELECT id,sequence_no,date_start,start_matin,end_matin,start_apresmidi,end_apresmidi,duree_formation
            FROM vtiger_inventorydatesrel
            WHERE vtiger_inventorydatesrel.id = ? order by sequence_no ASC";
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

    /* uni_cnfsecrm - modif 81 - DEBUT */
    function getNbrHeures($sessionid) {
        global $adb;
        $info_dates = array();
        $id = $this->focusColumnValues(array('record_id'));
        $query = "SELECT cf_996 as nbrheures
            FROM vtiger_activitycf
            WHERE activityid=?";
        $result = $adb->pquery($query, array($sessionid));
        $nbrheures = $adb->query_result($result, 0, 'nbrheures');

        return $nbrheures;
    }
    /* uni_cnfsecrm - modif 81 - FIN */

    function buildHeaderProduct() {
        global $adb;
        $info_product = array();
        $id = $this->focusColumnValues(array('record_id'));
        $query = "SELECT vtiger_inventoryproductrel.productid,vtiger_inventoryproductrel.quantity,
                vtiger_inventoryproductrel.listprice,vtiger_service.servicename,nbrjours,nbrheures,parpersonne,
                naturecalcul,vtiger_crmentity.description,vtiger_servicecf.cf_1080 as programme
                FROM vtiger_inventoryproductrel 
                INNER JOIN vtiger_service on vtiger_inventoryproductrel.productid = vtiger_service.serviceid 
                INNER JOIN vtiger_servicecf on vtiger_servicecf.serviceid = vtiger_service.serviceid
                INNER JOIN vtiger_crmentity on vtiger_crmentity.crmid = vtiger_service.serviceid 
                WHERE vtiger_inventoryproductrel.id = ?";
        $result = $adb->pquery($query, array($id));
        $num_rows_product = $adb->num_rows($result);
        if ($num_rows_product) {
            for ($i = 0; $i < $num_rows_product; $i++) {
                $info_product[$i]['servicename'] = $adb->query_result($result, $i, 'servicename');
                $info_product[$i]['quantity'] = $adb->query_result($result, $i, 'quantity');
                $info_product[$i]['listprice'] = number_format($adb->query_result($result, $i, 'listprice'), 2, '.', '');
                $info_product[$i]['nbrjours'] = $adb->query_result($result, $i, 'nbrjours');
                $info_product[$i]['nbrheures'] = $adb->query_result($result, $i, 'nbrheures');
                $info_product[$i]['parpersonne'] = $adb->query_result($result, $i, 'parpersonne');
                $info_product[$i]['naturecalcul'] = $adb->query_result($result, $i, 'naturecalcul');
                $info_product[$i]['description'] = $adb->query_result($result, $i, 'description');
                $info_product[$i]['programme'] = $adb->query_result($result, $i, 'programme');
            }
        }

//        $monfichier = fopen('SalesOrder_info.txt', 'a+');
//        fputs($monfichier, "\n" . ' test1 :  '.$info_dates[0]['end_apresmidi']);
//        fclose($monfichier);
        return $info_product;
    }

    function getOrganizationdetails() {
        global $adb;
        $organizationdetails = array();
        $query = "SELECT organizationname,address,city,country,code FROM vtiger_organizationdetails";
        $result = $adb->pquery($query, array());
        $num_rows_organization = $adb->num_rows($result);
        if ($num_rows_organization) {
            for ($i = 0; $i < $num_rows_organization; $i++) {
                $organizationdetails[$i]['organizationname'] = $adb->query_result($result, $i, 'organizationname');
                $organizationdetails[$i]['address'] = $adb->query_result($result, $i, 'address');
                $organizationdetails[$i]['city'] = $adb->query_result($result, $i, 'city');
                $organizationdetails[$i]['country'] = $adb->query_result($result, $i, 'country');
                $organizationdetails[$i]['code'] = $adb->query_result($result, $i, 'code');
            }
        }
        return $organizationdetails;
    }

    function getCrmentity() {
        global $adb;
        $module = $this->moduleName;
        $id = $this->focusColumnValues(array('record_id'));
        $query = "SELECT vtiger_crmentity.createdtime
                FROM vtiger_crmentity 
                WHERE vtiger_crmentity.crmid = ? and vtiger_crmentity.setype = ?";
        $result = $adb->pquery($query, array($id, $module));
        $crmentity['date_creation'] = $adb->query_result($result, 'createdtime');

        return $crmentity;
    }

    /* unicom fin modification hamza */

    function getFrais() {
        global $adb;
        $frais = array();
        $id = $this->focusColumnValues(array('record_id'));

        $result = $adb->pquery('SELECT * FROM vtiger_inventorychargesrel WHERE recordid = ?', array($id));
        while ($rowData = $adb->fetch_array($result)) {
            $chargesAndItsTaxes = Zend_Json::decode(html_entity_decode($rowData['charges']));
        }

        $frais['frais_deplacement'] = $chargesAndItsTaxes[1]["value"];
        $frais['frais_repas'] = $chargesAndItsTaxes[2]["value"];
        $frais['frais_hebergement'] = $chargesAndItsTaxes[3]["value"];
        return $frais;
    }

    function getVendor($vendorid) {
        global $adb;
        $vendor = array();
        $query = "SELECT vtiger_vendor.phone, vtiger_vendor.vendorid,vtiger_vendor.vendorname,
            vtiger_vendor.email,vtiger_vendor.street,vtiger_vendor.city,vtiger_vendor.country,
            vtiger_vendor.postalcode            
            FROM vtiger_vendor
            where vendorid = ?";
        $result = $adb->pquery($query, array($vendorid));
        $countvendor = $adb->num_rows($result);
        for ($i = 0; $i < $countvendor; $i++) {
            $vendor[$i]['vendorid'] = $adb->query_result($result, $i, 'vendorid');
            $vendor[$i]['vendorname'] = ucwords(strtolower(($adb->query_result($result, $i, 'vendorname'))));
            $vendor[$i]['phone'] = $adb->query_result($result, $i, 'phone');
            $vendor[$i]['email'] = $adb->query_result($result, $i, 'email');
            $vendor[$i]['street'] = ucwords(strtolower(($adb->query_result($result, $i, 'street'))));
            $vendor[$i]['city'] = strtoupper($adb->query_result($result, $i, 'city'));
            $vendor[$i]['country'] = $adb->query_result($result, $i, 'country');
            $vendor[$i]['postalcode'] = $adb->query_result($result, $i, 'postalcode');
        }
        return $vendor;
    }

    function getFinanceur() {
        global $adb;
        $id = $this->focusColumnValues(array('record_id'));
        $financeur = array();
        $query = "SELECT vtiger_vendor.phone, vtiger_vendor.vendorid,vtiger_vendor.vendorname,vtiger_vendor.email,vtiger_vendor.street,vtiger_vendor.city,vtiger_vendor.country,vtiger_vendor.postalcode,vtiger_inventoryfinanceurrel.montant,vtiger_inventoryfinanceurrel.tva,vtiger_inventoryfinanceurrel.ttc
                FROM vtiger_vendor
                INNER JOIN vtiger_inventoryfinanceurrel on vtiger_inventoryfinanceurrel.vendorid = vtiger_vendor.vendorid 
                WHERE vtiger_inventoryfinanceurrel.id = ?";
        $result = $adb->pquery($query, array($id));
        $countfinanceur = $adb->num_rows($result);
        for ($i = 0; $i < $countfinanceur; $i++) {
            $financeur[$i]['vendorid'] = $adb->query_result($result, $i, 'vendorid');
            $financeur[$i]['vendorname'] = ucwords(strtolower((formatString($adb->query_result($result, $i, 'vendorname')))));
            $financeur[$i]['phone'] = $adb->query_result($result, $i, 'phone');
            $financeur[$i]['email'] = $adb->query_result($result, $i, 'email');
            $financeur[$i]['street'] = ucwords(strtolower((formatString($adb->query_result($result, $i, 'street')))));
            $financeur[$i]['city'] = strtoupper(formatString($adb->query_result($result, $i, 'city')));
            $financeur[$i]['country'] = $adb->query_result($result, $i, 'country');
            $financeur[$i]['postalcode'] = $adb->query_result($result, $i, 'postalcode');
            $financeur[$i]['montant'] = $adb->query_result($result, $i, 'montant');
            $financeur[$i]['tva'] = $adb->query_result($result, $i, 'tva');
            $financeur[$i]['ttc'] = $adb->query_result($result, $i, 'ttc');
        }
        return $financeur;
    }

    function getFormateur() {
        global $adb;
        $id = $this->focusColumnValues(array('record_id'));
        $query = "SELECT concat(vtiger_users.first_name,' ',vtiger_users.last_name) AS formateur
                FROM vtiger_users 
                INNER JOIN vtiger_crmentity on vtiger_crmentity.smownerid = vtiger_users.id 
                WHERE vtiger_crmentity.crmid = ? ";
        $result = $adb->pquery($query, array($id));
        $formateur = $adb->query_result($result, 'formateur');
        return $formateur;
    }

    function getPayments() {
        global $adb;
        $payments = array();
        $id = $this->focusColumnValues(array('record_id'));
        $query = "SELECT its4you_cashflow4you.paymentdate,its4you_cashflow4you.relationid,its4you_cashflow4you.cashflow4you_paymethod
                FROM its4you_cashflow4you 
                WHERE its4you_cashflow4you.relationid = ? ";
        $result = $adb->pquery($query, array($id));
        $num_rows_payments = $adb->num_rows($result);
        if ($num_rows_payments) {
            for ($i = 0; $i < $num_rows_payments; $i++) {
                $date_payments = $adb->query_result($result, $i, 'paymentdate');
                $methode_payments = $adb->query_result($result, $i, 'cashflow4you_paymethod');
                $payments[$i]['date_payments'] = $date_payments;
                $payments[$i]['methode_payments'] = $methode_payments;
            }
        }
        return $payments;
    }

}

?>