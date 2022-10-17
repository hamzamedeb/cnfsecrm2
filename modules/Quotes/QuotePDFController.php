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

class Vtiger_QuotePDFController extends Vtiger_InventoryPDFController {

    function buildHeaderModelTitle() {
        $singularModuleNameKey = 'SINGLE_' . $this->moduleName;
        $translatedSingularModuleLabel = getTranslatedString($singularModuleNameKey, $this->moduleName);
        if ($translatedSingularModuleLabel == $singularModuleNameKey) {
            $translatedSingularModuleLabel = getTranslatedString($this->moduleName, $this->moduleName);
        }
        return sprintf("%s: %s", $translatedSingularModuleLabel, $this->focusColumnValue('quote_no'));
    }

    function getWatermarkContent() {
        return $this->focusColumnValue('quotestatus');
    }

    function buildHeaderModelColumnRight() {
        $issueDateLabel = getTranslatedString('Issued Date', $this->moduleName);
        $validDateLabel = getTranslatedString('Valid Date', $this->moduleName);
        $billingAddressLabel = getTranslatedString('Billing Address', $this->moduleName);
        $shippingAddressLabel = getTranslatedString('Shipping Address', $this->moduleName);

        $modelColumn2 = array(
            'dates' => array(
                $issueDateLabel => $this->formatDate(date("Y-m-d")),
                $validDateLabel => $this->formatDate($this->focusColumnValue('validtill')),
            ),
            $billingAddressLabel => $this->buildHeaderBillingAddress(),
            $shippingAddressLabel => $this->buildHeaderShippingAddress()
        );
        return $modelColumn2;
    }

    function buildHeaderModelColumnLeft() {
        $subject = $this->focusColumnValue('subject');
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
        $lieu = $this->focusColumnValue('lieu');
        $ville_devis = $this->focusColumnValue('bill_city');
        if($ville_devis == "")
        {
           $ville_devis = getSingleFieldValue("vtiger_lieucf", "cf_945", "lieuid", $lieu); 
        }
        $infos_client = $this->buildHeaderNomClient();
        $info_contact = $this->buildHeaderInfosContact();
        $info_apprenants = $this->buildHeaderApprenants();
        //var_dump($info_apprenants);
        $info_dates = $this->buildHeaderDates();
        $info_product = $this->buildHeaderProduct();
        $date_quote = $this->getCrmentity();
        $quote_no = $this->focusColumnValue('quote_no');
        $num_devis = $this->focusColumnValue('cf_919');
        $dates_formation_string = $this->focusColumnValue('cf_1002');
        $horaires_formation_string = $this->focusColumnValue('cf_907');
        $associated_products = $this->associated_products;
        $final_details = $associated_products[1]['final_details'];
        $discount_amount = $final_details["discount_amount_final"];
        $discount_percent = $final_details["discount_percentage_final"];
        $frais = $this->getFrais();
        $totalht = $discount = $handlingCharges = $handlingTaxes = 0;
        $adjustment = $grandTotal = 0;

        $productLineItemIndex = 0;
        $sh_tax_percent = 0;

        foreach ($associated_products as $productLineItem) {
            ++$productLineItemIndex;
//            var_dump($productLineItem);
//            echo "netPrice{$productLineItemIndex}" . " \n ";
//            echo $productLineItem["netPrice{$productLineItemIndex}"] . " \n ";
            $soustotalht += $productLineItem["netPrice{$productLineItemIndex}"];
        }
        $soustotalht = number_format($soustotalht, 2, '.', '');
        $frais_deplacement = $frais["frais_deplacement"];
        $frais_deplacement = number_format($frais_deplacement, 2, '.', '');
        $frais_repas = $frais["frais_repas"];
        $frais_hebergement = $frais["frais_hebergement"];
        /* uni_cnfsecrm - v2 - modif 151 - DEBUT */
        $autres_frais = 0.0;
        /* uni_cnfsecrm - v2 - modif 151 - FIN */

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
        $tax_totalamount = $final_details['tax_totalamount'];
        $group_total_tax = $final_details['group_total_tax'];
        $totalttc = $final_details['grandTotal'];

        $subjectLabel = getTranslatedString('Subject', $this->moduleName);
        $quoteNameLabel = getTranslatedString('Quote Name', $this->moduleName);
        $customerNameLabel = getTranslatedString('Customer Name', $this->moduleName);
        $contactNameLabel = getTranslatedString('Contact Name', $this->moduleName);
        $purchaseOrderLabel = getTranslatedString('Purchase Order', $this->moduleName);
        /* uni_cnfsecrm - v2 - modif 138 - DEBUT */
        $annotation = $this->focusColumnValue('cf_1317');
        /* uni_cnfsecrm - v2 - modif 138 - FIN */
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
            'num_devis' => $num_devis,
            'dates_formation_string' => $dates_formation_string,
            'horaires_formation_string' => $horaires_formation_string,
            'info_product' => $info_product,
            'ville_devis' => $ville_devis,
            'date_quote' => $date_quote,
            'soustotalht' => $soustotalht,
            'frais_deplacement' => $frais_deplacement,
            'frais_repas' => $frais_repas,
            'frais_hebergement' => $frais_hebergement,
            'autres_frais' => $autres_frais,
            'totalfrais' => $totalfrais,
            'totalht' => $totalht,
            'tax_totalamount' => $tax_totalamount,
            'discount_amount' => $discount,
            'totalttc' => $totalttc,
            /* uni_cnfsecrm - v2 - modif 138 - DEBUT */
            'annotation' => $annotation
            /* uni_cnfsecrm - v2 - modif 138 - FIN */    
        );


//        $monfichier = fopen('sales_order_detail.txt', 'a+');
//        fputs($monfichier, "\n" . ' detail :  '.$date_fin_formations);
//        fclose($monfichier);
        return $modelColumn0;
    }

}

?>
