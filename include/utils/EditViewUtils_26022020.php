<?php

/* * *******************************************************************************
 * The contents of this file are subject to the SugarCRM Public License Version 1.1.2
 * ("License"); You may not use this file except in compliance with the
 * License. You may obtain a copy of the License at http://www.sugarcrm.com/SPL
 * Software distributed under the License is distributed on an  "AS IS"  basis,
 * WITHOUT WARRANTY OF ANY KIND, either express or implied. See the License for
 * the specific language governing rights and limitations under the License.
 * The Original Code is:  SugarCRM Open Source
 * The Initial Developer of the Original Code is SugarCRM, Inc.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.;
 * All Rights Reserved.
 * Contributor(s): ______________________________________.
 * ****************************************************************************** */
/* * *******************************************************************************
 * $Header: /advent/projects/wesat/vtiger_crm/sugarcrm/include/utils/EditViewUtils.php,v 1.188 2005/04/29 05:5 * 4:39 rank Exp
 * Description:  Includes generic helper functions used throughout the application.
 * Portions created by SugarCRM are Copyright (C) SugarCRM, Inc.
 * All Rights Reserved.
 * Contributor(s): ______________________________________..
 * ****************************************************************************** */

require_once('include/database/PearDatabase.php');
require_once('include/ComboUtil.php'); //new
require_once('include/utils/CommonUtils.php'); //new
require_once 'modules/PickList/DependentPickListUtils.php';

/** This function returns the vtiger_invoice object populated with the details from sales order object.
 * Param $focus - Invoice object
 * Param $so_focus - Sales order focus
 * Param $soid - sales order id
 * Return type is an object array
 */
function getConvertSoToInvoice($focus, $so_focus, $soid) {
    global $log, $current_user;
    $log->debug("Entering getConvertSoToInvoice(" . get_class($focus) . "," . get_class($so_focus) . "," . $soid . ") method ...");
    $log->info("in getConvertSoToInvoice " . $soid);
    $xyz = array('bill_street', 'bill_city', 'bill_code', 'bill_pobox', 'bill_country', 'bill_state', 'ship_street', 'ship_city', 'ship_code', 'ship_pobox', 'ship_country', 'ship_state');
    for ($i = 0; $i < count($xyz); $i++) {
        if (getFieldVisibilityPermission('SalesOrder', $current_user->id, $xyz[$i]) == '0') {
            $so_focus->column_fields[$xyz[$i]] = $so_focus->column_fields[$xyz[$i]];
        } else
            $so_focus->column_fields[$xyz[$i]] = '';
    }
    $focus->column_fields['salesorder_id'] = $soid;
    $focus->column_fields['subject'] = $so_focus->column_fields['subject'];
    $focus->column_fields['customerno'] = $so_focus->column_fields['customerno'];
    $focus->column_fields['duedate'] = $so_focus->column_fields['duedate'];
    $focus->column_fields['contact_id'] = $so_focus->column_fields['contact_id']; //to include contact name in Invoice
    $focus->column_fields['account_id'] = $so_focus->column_fields['account_id'];
    $focus->column_fields['exciseduty'] = $so_focus->column_fields['exciseduty'];
    $focus->column_fields['salescommission'] = $so_focus->column_fields['salescommission'];
    $focus->column_fields['vtiger_purchaseorder'] = $so_focus->column_fields['vtiger_purchaseorder'];
    $focus->column_fields['bill_street'] = $so_focus->column_fields['bill_street'];
    $focus->column_fields['ship_street'] = $so_focus->column_fields['ship_street'];
    $focus->column_fields['bill_city'] = $so_focus->column_fields['bill_city'];
    $focus->column_fields['ship_city'] = $so_focus->column_fields['ship_city'];
    $focus->column_fields['bill_state'] = $so_focus->column_fields['bill_state'];
    $focus->column_fields['ship_state'] = $so_focus->column_fields['ship_state'];
    $focus->column_fields['bill_code'] = $so_focus->column_fields['bill_code'];
    $focus->column_fields['ship_code'] = $so_focus->column_fields['ship_code'];
    $focus->column_fields['bill_country'] = $so_focus->column_fields['bill_country'];
    $focus->column_fields['ship_country'] = $so_focus->column_fields['ship_country'];
    $focus->column_fields['bill_pobox'] = $so_focus->column_fields['bill_pobox'];
    $focus->column_fields['ship_pobox'] = $so_focus->column_fields['ship_pobox'];
    $focus->column_fields['description'] = $so_focus->column_fields['description'];
    $focus->column_fields['terms_conditions'] = $so_focus->column_fields['terms_conditions'];
    $focus->column_fields['currency_id'] = $so_focus->column_fields['currency_id'];
    $focus->column_fields['conversion_rate'] = $so_focus->column_fields['conversion_rate'];
    $focus->column_fields['pre_tax_total'] = $so_focus->column_fields['pre_tax_total'];

    $log->debug("Exiting getConvertSoToInvoice method ...");
    return $focus;
}

/** This function returns the detailed list of vtiger_products associated to a given entity or a record.
 * Param $module - module name
 * Param $focus - module object
 * Param $seid - sales entity id
 * Return type is an object array
 */
function getAssociatedProducts($module, $focus, $seid = '', $refModuleName = false) {
    global $log;
    $log->debug("Entering getAssociatedProducts(" . $module . "," . get_class($focus) . "," . $seid . "='') method ...");
    global $adb;
    $output = '';
    global $theme, $current_user;

    $no_of_decimal_places = getCurrencyDecimalPlaces();
    $theme_path = "themes/" . $theme . "/";
    $image_path = $theme_path . "images/";
    $product_Detail = Array();
    $idInventory = $focus->id;
    $inventoryModules = getInventoryModules();
    if (in_array($module, $inventoryModules)) {
        $taxtype = getInventoryTaxType($module, $focus->id);
    }

    $additionalProductFieldsString = $additionalServiceFieldsString = '';
    $lineItemSupportedModules = array('Accounts', 'Contacts', 'Leads', 'Potentials');

    // DG 15 Aug 2006
    // Add "ORDER BY sequence_no" to retain add order on all inventoryproductrel items

    if (in_array($module, $inventoryModules)) {
        $query = "SELECT
					case when vtiger_products.productid != '' then vtiger_products.productname else vtiger_service.servicename end as productname,
					case when vtiger_products.productid != '' then vtiger_products.product_no else vtiger_service.service_no end as productcode,
					case when vtiger_products.productid != '' then vtiger_products.unit_price else vtiger_service.unit_price end as unit_price,
					case when vtiger_products.productid != '' then vtiger_products.qtyinstock else 'NA' end as qtyinstock,
					case when vtiger_products.productid != '' then 'Products' else 'Services' end as entitytype,
					vtiger_inventoryproductrel.listprice, vtiger_products.is_subproducts_viewable, 
					vtiger_inventoryproductrel.description AS product_description, vtiger_inventoryproductrel.*,
					vtiger_crmentity.deleted FROM vtiger_inventoryproductrel
					LEFT JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_inventoryproductrel.productid
					LEFT JOIN vtiger_products ON vtiger_products.productid=vtiger_inventoryproductrel.productid
					LEFT JOIN vtiger_service ON vtiger_service.serviceid=vtiger_inventoryproductrel.productid
					WHERE id=? ORDER BY sequence_no";
        $params = array($focus->id);
    } elseif (in_array($module, $lineItemSupportedModules)) {
        $query = '(SELECT vtiger_products.productid, vtiger_products.productname, vtiger_products.product_no AS productcode, vtiger_products.purchase_cost,
					vtiger_products.unit_price, vtiger_products.qtyinstock, vtiger_crmentity.deleted, "Products" AS entitytype,
					vtiger_products.is_subproducts_viewable, vtiger_crmentity.description ' . $additionalProductFieldsString . ' FROM vtiger_products
					INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_products.productid
					INNER JOIN vtiger_seproductsrel ON vtiger_seproductsrel.productid=vtiger_products.productid
					INNER JOIN vtiger_productcf ON vtiger_products.productid = vtiger_productcf.productid
					WHERE vtiger_seproductsrel.crmid=? AND vtiger_crmentity.deleted=0 AND vtiger_products.discontinued=1)
					UNION
					(SELECT vtiger_service.serviceid AS productid, vtiger_service.servicename AS productname, vtiger_service.service_no AS productcode,
					vtiger_service.purchase_cost, vtiger_service.unit_price, "NA" as qtyinstock, vtiger_crmentity.deleted,
					"Services" AS entitytype, 1 AS is_subproducts_viewable, vtiger_crmentity.description ' . $additionalServiceFieldsString . ' FROM vtiger_service
					INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_service.serviceid
					INNER JOIN vtiger_crmentityrel ON vtiger_crmentityrel.relcrmid = vtiger_service.serviceid
					INNER JOIN vtiger_servicecf ON vtiger_service.serviceid = vtiger_servicecf.serviceid
					WHERE vtiger_crmentityrel.crmid=? AND vtiger_crmentity.deleted=0 AND vtiger_service.discontinued=1)';
        $params = array($seid, $seid);
    } elseif ($module == 'Vendors') {
        $query = 'SELECT vtiger_products.productid, vtiger_products.productname, vtiger_products.product_no AS productcode, vtiger_products.purchase_cost,
					vtiger_products.unit_price, vtiger_products.qtyinstock, vtiger_crmentity.deleted, "Products" AS entitytype,
					vtiger_products.is_subproducts_viewable, vtiger_crmentity.description ' . $additionalServiceFieldsString . ' FROM vtiger_products
					INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_products.productid
					INNER JOIN vtiger_vendor ON vtiger_vendor.vendorid = vtiger_products.vendor_id
					INNER JOIN vtiger_productcf ON vtiger_products.productid = vtiger_productcf.productid
					WHERE vtiger_vendor.vendorid=? AND vtiger_crmentity.deleted=0 AND vtiger_products.discontinued=1';
        $params = array($seid);
    } elseif ($module == 'HelpDesk') {
        $query = 'SELECT vtiger_service.serviceid AS productid, vtiger_service.servicename AS productname, vtiger_service.service_no AS productcode,
					vtiger_service.purchase_cost, vtiger_service.unit_price, "NA" as qtyinstock, vtiger_crmentity.deleted,
					"Services" AS entitytype, 1 AS is_subproducts_viewable, vtiger_crmentity.description ' . $additionalServiceFieldsString . ' FROM vtiger_service
					INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_service.serviceid
					INNER JOIN vtiger_crmentityrel ON vtiger_crmentityrel.relcrmid = vtiger_service.serviceid
					INNER JOIN vtiger_servicecf ON vtiger_service.serviceid = vtiger_servicecf.serviceid
					WHERE vtiger_crmentityrel.crmid=? AND vtiger_crmentity.deleted=0 AND vtiger_service.discontinued=1';
        $params = array($seid);
    } elseif ($module == 'Products') {
        $query = 'SELECT vtiger_products.productid, vtiger_products.productname, vtiger_products.product_no AS productcode, vtiger_products.purchase_cost,
					vtiger_products.unit_price, vtiger_products.qtyinstock, vtiger_crmentity.deleted, "Products" AS entitytype,
					vtiger_products.is_subproducts_viewable, vtiger_crmentity.description ' . $additionalProductFieldsString . ' FROM vtiger_products
					INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_products.productid
					INNER JOIN vtiger_productcf ON vtiger_products.productid = vtiger_productcf.productid
					WHERE vtiger_crmentity.deleted=0 AND vtiger_products.productid=?';
        $params = array($seid);
    } elseif ($module == 'Services') {
        $query = 'SELECT vtiger_service.serviceid AS productid, vtiger_service.servicename AS productname, vtiger_service.service_no AS productcode,
					vtiger_service.purchase_cost, vtiger_service.unit_price AS unit_price, "NA" AS qtyinstock, vtiger_crmentity.deleted,
					"Services" AS entitytype, 1 AS is_subproducts_viewable, vtiger_crmentity.description ' . $additionalServiceFieldsString . ' FROM vtiger_service
					INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid=vtiger_service.serviceid
					INNER JOIN vtiger_servicecf ON vtiger_service.serviceid = vtiger_servicecf.serviceid
					WHERE vtiger_crmentity.deleted=0 AND vtiger_service.serviceid=?';
        $params = array($seid);
    }

    $result = $adb->pquery($query, $params);
    $num_rows = $adb->num_rows($result);
    for ($i = 1; $i <= $num_rows; $i++) {
        $deleted = $adb->query_result($result, $i - 1, 'deleted');
        $hdnProductId = $adb->query_result($result, $i - 1, 'productid');
        $hdnProductcode = $adb->query_result($result, $i - 1, 'productcode');
        $productname = $adb->query_result($result, $i - 1, 'productname');
        $productdescription = $adb->query_result($result, $i - 1, 'description');
        $comment = $adb->query_result($result, $i - 1, 'comment');
        $qtyinstock = $adb->query_result($result, $i - 1, 'qtyinstock');
        $qty = $adb->query_result($result, $i - 1, 'quantity');
        $unitprice = $adb->query_result($result, $i - 1, 'unit_price');
        $listprice = $adb->query_result($result, $i - 1, 'listprice');
        $entitytype = $adb->query_result($result, $i - 1, 'entitytype');
        $purchaseCost = $adb->query_result($result, $i - 1, 'purchase_cost');
        $margin = $adb->query_result($result, $i - 1, 'margin');
        $isSubProductsViewable = $adb->query_result($result, $i - 1, 'is_subproducts_viewable');
        $margin = $adb->query_result($result, $i - 1, 'margin');
        $tarif = $adb->query_result($result, $i - 1, 'tarif');
        $nbrjours = $adb->query_result($result, $i - 1, 'nbrjours');
        $nbrheures = $adb->query_result($result, $i - 1, 'nbrheures');
        $baseheures = $adb->query_result($result, $i - 1, 'baseheures');
        $naturecalcul = $adb->query_result($result, $i - 1, 'naturecalcul');
        $parpersonne = $adb->query_result($result, $i - 1, 'parpersonne');
        $listpriceinter = $adb->query_result($result, $i - 1, 'listpriceinter');
        $listpriceintra = $adb->query_result($result, $i - 1, 'listpriceintra');

        if ($purchaseCost) {
            $product_Detail[$i]['purchaseCost' . $i] = number_format($purchaseCost, $no_of_decimal_places, '.', '');
        }

        if ($margin) {
            $product_Detail[$i]['margin' . $i] = number_format($margin, $no_of_decimal_places, '.', '');
        }

        if (($deleted) || (!isset($deleted))) {
            $product_Detail[$i]['productDeleted' . $i] = true;
        } elseif (!$deleted) {
            $product_Detail[$i]['productDeleted' . $i] = false;
        }

        if (!empty($entitytype)) {
            $product_Detail[$i]['entityType' . $i] = $entitytype;
        }

        if ($listprice == '')
            $listprice = $unitprice;
        if ($qty == '')
            $qty = 1;


        /* uni_cnfsecrm */
        switch ($naturecalcul) {
            case 'jour':
                $prix_formation = $listprice * $nbrjours;
                break;

            case 'heure':
                $prix_formation = $listprice * $nbrheures;
                break;

            case 'forfait':
                $prix_formation = $listprice;
                break;
            default :
                $prix_formation = $listprice;
                break;
        }
        //echo " parpersonne ".$parpersonne;
        $qty_calcul = $qty;
        if ($parpersonne != 'on')
            $qty_calcul = 1;

        //calculate productTotal
//        echo "qt" . $qty_calcul . "<br/>";
//        echo "prix_formation" . $prix_formation . "<br/>";
        $productTotal = $qty_calcul * $prix_formation;

        //Delete link in First column
        if ($i != 1) {
            $product_Detail[$i]['delRow' . $i] = "Del";
        }

        if (in_array($module, $lineItemSupportedModules) || $module === 'Vendors' || (!$focus->mode && $seid)) {
            $subProductsQuery = 'SELECT vtiger_seproductsrel.crmid AS prod_id, quantity FROM vtiger_seproductsrel
								 INNER JOIN vtiger_crmentity ON vtiger_crmentity.crmid = vtiger_seproductsrel.crmid
								 INNER JOIN vtiger_products ON vtiger_products.productid = vtiger_seproductsrel.crmid
								 WHERE vtiger_seproductsrel.productid=? AND vtiger_seproductsrel.setype=? AND vtiger_products.discontinued=1';

            $subParams = array($seid);
            if (in_array($module, $lineItemSupportedModules) || $module === 'Vendors') {
                $subParams = array($hdnProductId);
            }
            array_push($subParams, 'Products');
        } else {
            $subProductsQuery = 'SELECT productid AS prod_id, quantity FROM vtiger_inventorysubproductrel WHERE id=? AND sequence_no=?';
            $subParams = array($focus->id, $i);
        }
        $subProductsResult = $adb->pquery($subProductsQuery, $subParams);
        $subProductsCount = $adb->num_rows($subProductsResult);

        $subprodid_str = '';
        $subprodname_str = '';

        $subProductQtyList = array();
        for ($j = 0; $j < $subProductsCount; $j++) {
            $sprod_id = $adb->query_result($subProductsResult, $j, 'prod_id');
            $sprod_name = getProductName($sprod_id);
            if (isset($sprod_name)) {
                $subQty = $adb->query_result($subProductsResult, $j, 'quantity');
                $subProductQtyList[$sprod_id] = array('name' => $sprod_name, 'qty' => $subQty);
                if (isRecordExists($sprod_id) && $_REQUEST['view'] === 'Detail') {
                    $subprodname_str .= "<a href='index.php?module=Products&view=Detail&record=$sprod_id' target='_blank'> <em> - $sprod_name ($subQty)</em><br></a>";
                } else {
                    $subprodname_str .= "<em> - $sprod_name ($subQty)</em><br>";
                }
                $subprodid_str .= "$sprod_id:$subQty,";
            }
        }
        $subprodid_str = rtrim($subprodid_str, ',');

        $product_Detail[$i]['hdnProductId' . $i] = $hdnProductId;
        $product_Detail[$i]['productName' . $i] = from_html($productname);
        /* Added to fix the issue Product Pop-up name display */
        if ($_REQUEST['action'] == 'CreateSOPDF' || $_REQUEST['action'] == 'CreatePDF' || $_REQUEST['action'] == 'SendPDFMail')
            $product_Detail[$i]['productName' . $i] = htmlspecialchars($product_Detail[$i]['productName' . $i]);
        $product_Detail[$i]['hdnProductcode' . $i] = $hdnProductcode;
        $product_Detail[$i]['productDescription' . $i] = from_html($productdescription);
        if ($module == 'Vendors' || $module == 'Products' || $module == 'Services' || in_array($module, $lineItemSupportedModules)) {
            $product_Detail[$i]['comment' . $i] = $productdescription;
        } else {
            $product_Detail[$i]['comment' . $i] = $comment;
        }

        if ($module != 'PurchaseOrder' && $focus->object_name != 'Order') {
            $product_Detail[$i]['qtyInStock' . $i] = decimalFormat($qtyinstock);
        }
        $listprice = number_format($listprice, $no_of_decimal_places, '.', '');
        $product_Detail[$i]['qty' . $i] = decimalFormat($qty);
        $product_Detail[$i]['listPrice' . $i] = $listprice;
        $product_Detail[$i]['unitPrice' . $i] = number_format($unitprice, $no_of_decimal_places, '.', '');
        $product_Detail[$i]['productTotal' . $i] = number_format($productTotal, $no_of_decimal_places, '.', '');
        $product_Detail[$i]['subproduct_ids' . $i] = $subprodid_str;
        if ($isSubProductsViewable) {
            $product_Detail[$i]['subprod_qty_list' . $i] = $subProductQtyList;
            $product_Detail[$i]['subprod_names' . $i] = $subprodname_str;
        }
        $discount_percent = decimalFormat($adb->query_result($result, $i - 1, 'discount_percent'));
        $discount_amount = $adb->query_result($result, $i - 1, 'discount_amount');
        $discount_amount = decimalFormat(number_format($discount_amount, $no_of_decimal_places, '.', ''));
        $discountTotal = 0;
        //Based on the discount percent or amount we will show the discount details
        //To avoid NaN javascript error, here we assign 0 initially to' %of price' and 'Direct Price reduction'(for Each Product)
        $product_Detail[$i]['discount_percent' . $i] = 0;
        $product_Detail[$i]['discount_amount' . $i] = 0;

        if (!empty($discount_percent)) {
            $product_Detail[$i]['discount_type' . $i] = "percentage";
            $product_Detail[$i]['discount_percent' . $i] = $discount_percent;
            $product_Detail[$i]['checked_discount_percent' . $i] = ' checked';
            $product_Detail[$i]['style_discount_percent' . $i] = ' style="visibility:visible"';
            $product_Detail[$i]['style_discount_amount' . $i] = ' style="visibility:hidden"';
            $discountTotal = $productTotal * $discount_percent / 100;
        } elseif (!empty($discount_amount)) {
            $product_Detail[$i]['discount_type' . $i] = "amount";
            $product_Detail[$i]['discount_amount' . $i] = $discount_amount;
            $product_Detail[$i]['checked_discount_amount' . $i] = ' checked';
            $product_Detail[$i]['style_discount_amount' . $i] = ' style="visibility:visible"';
            $product_Detail[$i]['style_discount_percent' . $i] = ' style="visibility:hidden"';
            $discountTotal = $discount_amount;
        } else {
            $product_Detail[$i]['checked_discount_zero' . $i] = ' checked';
        }

        /* uni_cnfsecrm */
        $product_Detail[$i]['tarif' . $i] = $tarif;
        $product_Detail[$i]['nbr_jours' . $i] = $nbrjours;
        $product_Detail[$i]['nbr_heures' . $i] = $nbrheures;
        $product_Detail[$i]['base_heure' . $i] = $baseheures;
        $product_Detail[$i]['nature_calcul' . $i] = $naturecalcul;
        $product_Detail[$i]['par_personne' . $i] = $parpersonne;
        $product_Detail[$i]['listpriceinter' . $i] = formatNumberFr($listpriceinter);
        $product_Detail[$i]['listpriceintra' . $i] = formatNumberFr($listpriceintra);

//        echo "--" . $productTotal . "<br/>";
//        echo "--" . $discountTotal . "<br/>";

        $totalAfterDiscount = $productTotal - $discountTotal;
        $totalAfterDiscount = number_format($totalAfterDiscount, $no_of_decimal_places, '.', '');
        $discountTotal = number_format($discountTotal, $no_of_decimal_places, '.', '');
        $product_Detail[$i]['discountTotal' . $i] = $discountTotal;
        $product_Detail[$i]['totalAfterDiscount' . $i] = $totalAfterDiscount;

        $taxTotal = 0;
        $taxTotal = number_format($taxTotal, $no_of_decimal_places, '.', '');
        $product_Detail[$i]['taxTotal' . $i] = $taxTotal;

        //Calculate netprice
//        echo "--" . $totalAfterDiscount . "<br/>";
//        echo "--" . $taxTotal . "<br/>";
        $netPrice = $totalAfterDiscount + $taxTotal;
        //if condition is added to call this function when we create PO/SO/Quotes/Invoice from Product module
        if (in_array($module, $inventoryModules)) {
            if ($taxtype == 'individual') {
                //Add the tax with product total and assign to netprice
                $netPrice = $netPrice + $taxTotal;
            }
        }
        $product_Detail[$i]['netPrice' . $i] = number_format($netPrice, getCurrencyDecimalPlaces(), '.', '');

        //First we will get all associated taxes as array
        $tax_details = getTaxDetailsForProduct($hdnProductId, 'all');
        $regionsList = array();
        foreach ($tax_details as $taxInfo) {
            $regionsInfo = array('default' => $taxInfo['percentage']);
            foreach ($taxInfo['productregions'] as $list) {
                if (is_array($list['list'])) {
                    foreach (array_fill_keys($list['list'], $list['value']) as $key => $value) {
                        $regionsInfo[$key] = $value;
                    }
                }
            }
            $regionsList[$taxInfo['taxid']] = $regionsInfo;
        }
        //Now retrieve the tax values from the current query with the name
        for ($tax_count = 0; $tax_count < count($tax_details); $tax_count++) {
            $tax_name = $tax_details[$tax_count]['taxname'];
            $tax_label = $tax_details[$tax_count]['taxlabel'];
            $tax_value = 0;

            $tax_value = $tax_details[$tax_count]['percentage'];
            if ($focus->id != '' && $taxtype == 'individual') {
                $lineItemId = $adb->query_result($result, $i - 1, 'lineitem_id');
                $tax_value = getInventoryProductTaxValue($focus->id, $hdnProductId, $tax_name, $lineItemId);
                $selectedRegionId = $focus->column_fields['region_id'];
                if ($selectedRegionId) {
                    $regionsList[$tax_details[$tax_count]['taxid']][$selectedRegionId] = $tax_value;
                } else {
                    $regionsList[$tax_details[$tax_count]['taxid']]['default'] = $tax_value;
                }
            }

            $product_Detail[$i]['taxes'][$tax_count]['taxname'] = $tax_name;
            $product_Detail[$i]['taxes'][$tax_count]['taxlabel'] = $tax_label;
            $product_Detail[$i]['taxes'][$tax_count]['percentage'] = $tax_value;
            $product_Detail[$i]['taxes'][$tax_count]['deleted'] = $tax_details[$tax_count]['deleted'];
            $product_Detail[$i]['taxes'][$tax_count]['taxid'] = $tax_details[$tax_count]['taxid'];
            $product_Detail[$i]['taxes'][$tax_count]['type'] = $tax_details[$tax_count]['type'];
            $product_Detail[$i]['taxes'][$tax_count]['method'] = $tax_details[$tax_count]['method'];
            $product_Detail[$i]['taxes'][$tax_count]['regions'] = $tax_details[$tax_count]['regions'];
            $product_Detail[$i]['taxes'][$tax_count]['compoundon'] = $tax_details[$tax_count]['compoundon'];
            $product_Detail[$i]['taxes'][$tax_count]['regionsList'] = $regionsList[$tax_details[$tax_count]['taxid']];
        }
    }

    //set the taxtype
    $product_Detail[1]['final_details']['taxtype'] = $taxtype;

    //Get the Final Discount, S&H charge, Tax for S&H and Adjustment values
    //To set the Final Discount details
    $finalDiscount = 0;
    $product_Detail[1]['final_details']['discount_type_final'] = 'zero';

    $subTotal = ($focus->column_fields['hdnSubTotal'] != '') ? $focus->column_fields['hdnSubTotal'] : 0;
    $subTotal = number_format($subTotal, $no_of_decimal_places, '.', '');
//    echo $subTotal;

    $product_Detail[1]['final_details']['hdnSubTotal'] = $subTotal;
    $discountPercent = ($focus->column_fields['hdnDiscountPercent'] != '') ? $focus->column_fields['hdnDiscountPercent'] : 0;
    $discountAmount = ($focus->column_fields['hdnDiscountAmount'] != '') ? $focus->column_fields['hdnDiscountAmount'] : 0;
    if ($discountPercent != '0') {
        $discountAmount = ($product_Detail[1]['final_details']['hdnSubTotal'] * $discountPercent / 100);
    }

    //To avoid NaN javascript error, here we assign 0 initially to' %of price' and 'Direct Price reduction'(For Final Discount)
    $discount_amount_final = 0;
    $discount_amount_final = number_format($discount_amount_final, $no_of_decimal_places, '.', '');
    $product_Detail[1]['final_details']['discount_percentage_final'] = 0;
    $product_Detail[1]['final_details']['discount_amount_final'] = $discount_amount_final;

    $hdnDiscountPercent = (float) $focus->column_fields['hdnDiscountPercent'];
    $hdnDiscountAmount = (float) $focus->column_fields['hdnDiscountAmount'];

    if (!empty($hdnDiscountPercent)) {
        $finalDiscount = ($subTotal * $discountPercent / 100);
        $product_Detail[1]['final_details']['discount_type_final'] = 'percentage';
        $product_Detail[1]['final_details']['discount_percentage_final'] = $discountPercent;
        $product_Detail[1]['final_details']['checked_discount_percentage_final'] = ' checked';
        $product_Detail[1]['final_details']['style_discount_percentage_final'] = ' style="visibility:visible"';
        $product_Detail[1]['final_details']['style_discount_amount_final'] = ' style="visibility:hidden"';
    } elseif (!empty($hdnDiscountAmount)) {
        $finalDiscount = $focus->column_fields['hdnDiscountAmount'];
        $product_Detail[1]['final_details']['discount_type_final'] = 'amount';
        $product_Detail[1]['final_details']['discount_amount_final'] = $discountAmount;
        $product_Detail[1]['final_details']['checked_discount_amount_final'] = ' checked';
        $product_Detail[1]['final_details']['style_discount_amount_final'] = ' style="visibility:visible"';
        $product_Detail[1]['final_details']['style_discount_percentage_final'] = ' style="visibility:hidden"';
    }
    $finalDiscount = number_format($finalDiscount, $no_of_decimal_places, '.', '');
    $product_Detail[1]['final_details']['discountTotal_final'] = $finalDiscount;

    /* uni_cnfsecrm - get financement */
    $financement = 0;
    $montantclient = 0;
    if ($module == 'Invoice') {
        $financement = getSingleFieldValue("vtiger_invoice", "financement", "invoiceid", $idInventory);
        $financement = number_format($financement, $no_of_decimal_places, '.', '');

        $montantclient = getSingleFieldValue("vtiger_invoice", "montantclient", "invoiceid", $idInventory);
        $montantclient = number_format($montantclient, $no_of_decimal_places, '.', '');
    }
    if ($module == 'SalesOrder') {
        $financement = getSingleFieldValue("vtiger_salesorder", "financement", "salesorderid", $idInventory);
        $financement = number_format($financement, $no_of_decimal_places, '.', '');
    }
    $product_Detail[1]['final_details']['financement'] = $financement;
    $product_Detail[1]['final_details']['montantclient'] = $montantclient;

    //To set the Final Tax values
    //we will get all taxes. if individual then show the product related taxes only else show all taxes
    //suppose user want to change individual to group or vice versa in edit time the we have to show all taxes. so that here we will store all the taxes and based on need we will show the corresponding taxes
    //First we should get all available taxes and then retrieve the corresponding tax values
    $tax_details = getAllTaxes('available', '', 'edit', $focus->id);
    $taxDetails = array();

    for ($tax_count = 0; $tax_count < count($tax_details); $tax_count++) {
        if ($tax_details[$tax_count]['method'] === 'Deducted') {
            continue;
        }

        $tax_name = $tax_details[$tax_count]['taxname'];
        $tax_label = $tax_details[$tax_count]['taxlabel'];

        //if taxtype is individual and want to change to group during edit time then we have to show the all available taxes and their default values
        //Also taxtype is group and want to change to individual during edit time then we have to provide the asspciated taxes and their default tax values for individual products
        if ($taxtype == 'group')
            $tax_percent = $adb->query_result($result, 0, $tax_name);
        else
            $tax_percent = $tax_details[$tax_count]['percentage']; //$adb->query_result($result,0,$tax_name);

        if ($tax_percent == '' || $tax_percent == 'NULL')
            $tax_percent = 0;

        //To set the Shipping & Handling charge
        $shCharge = ($focus->column_fields['hdnS_H_Amount'] != '') ? $focus->column_fields['hdnS_H_Amount'] : 0;
        $shCharge = number_format($shCharge, $no_of_decimal_places, '.', '');
        $product_Detail[1]['final_details']['shipping_handling_charge'] = $shCharge;

        /* uni_cnfsecrm - ajouter chargesTotal - supprimer financement du montant global */

        $taxamount = ($subTotal + $shCharge - $finalDiscount - $financement - $montantclient) * $tax_percent / 100;
        list($before_dot, $after_dot) = explode('.', $taxamount);
        if ($after_dot[$no_of_decimal_places] == 5) {
            $taxamount = round($taxamount, $no_of_decimal_places, PHP_ROUND_HALF_DOWN);
        } else {
            $taxamount = number_format($taxamount, $no_of_decimal_places, '.', '');
        }

        $taxId = $tax_details[$tax_count]['taxid'];
        $taxDetails[$taxId]['taxname'] = $tax_name;
        $taxDetails[$taxId]['taxlabel'] = $tax_label;
        $taxDetails[$taxId]['percentage'] = $tax_percent;
        $taxDetails[$taxId]['amount'] = $taxamount;
        $taxDetails[$taxId]['taxid'] = $taxId;
        $taxDetails[$taxId]['type'] = $tax_details[$tax_count]['type'];
        $taxDetails[$taxId]['method'] = $tax_details[$tax_count]['method'];
        $taxDetails[$taxId]['regions'] = Zend_Json::decode(html_entity_decode($tax_details[$tax_count]['regions']));
        $taxDetails[$taxId]['compoundon'] = Zend_Json::decode(html_entity_decode($tax_details[$tax_count]['compoundon']));
    }

    $compoundTaxesInfo = getCompoundTaxesInfoForInventoryRecord($focus->id, $module);
    //Calculating compound info
    $taxTotal = 0;
    foreach ($taxDetails as $taxId => $taxInfo) {
        $compoundOn = $taxInfo['compoundon'];
        if ($compoundOn) {
            $existingCompounds = $compoundTaxesInfo[$taxId];
            if (!is_array($existingCompounds)) {
                $existingCompounds = array();
            }
            $compoundOn = array_unique(array_merge($existingCompounds, $compoundOn));
            $taxDetails[$taxId]['compoundon'] = $compoundOn;

            $amount = $subTotal - $finalDiscount;
            foreach ($compoundOn as $id) {
                $amount = (float) $amount + (float) $taxDetails[$id]['amount'];
            }
            $taxAmount = ((float) $amount * (float) $taxInfo['percentage']) / 100;
            list($beforeDot, $afterDot) = explode('.', $taxAmount);

            if ($afterDot[$no_of_decimal_places] == 5) {
                $taxAmount = round($taxAmount, $no_of_decimal_places, PHP_ROUND_HALF_DOWN);
            } else {
                $taxAmount = number_format($taxAmount, $no_of_decimal_places, '.', '');
            }

            $taxDetails[$taxId]['amount'] = $taxAmount;
        }
        $taxTotal = $taxTotal + $taxDetails[$taxId]['amount'];
    }
    $product_Detail[1]['final_details']['taxes'] = $taxDetails;
    $product_Detail[1]['final_details']['tax_totalamount'] = number_format($taxTotal, $no_of_decimal_places, '.', '');

    //To set the Shipping & Handling tax values
    //calculate S&H tax
    $shtaxtotal = 0;
    //First we should get all available taxes and then retrieve the corresponding tax values
    $shtax_details = getAllTaxes('available', 'sh', 'edit', $focus->id);

    //if taxtype is group then the tax should be same for all products in vtiger_inventoryproductrel table
    for ($shtax_count = 0; $shtax_count < count($shtax_details); $shtax_count++) {
        $shtax_name = $shtax_details[$shtax_count]['taxname'];
        $shtax_label = $shtax_details[$shtax_count]['taxlabel'];
        $shtax_percent = 0;
        //if condition is added to call this function when we create PO/SO/Quotes/Invoice from Product module
        if (in_array($module, $inventoryModules)) {
            $shtax_percent = getInventorySHTaxPercent($focus->id, $shtax_name);
        }
        $shtaxamount = $shCharge * $shtax_percent / 100;
        $shtaxtotal = $shtaxtotal + $shtaxamount;
        $product_Detail[1]['final_details']['sh_taxes'][$shtax_count]['taxname'] = $shtax_name;
        $product_Detail[1]['final_details']['sh_taxes'][$shtax_count]['taxlabel'] = $shtax_label;
        $product_Detail[1]['final_details']['sh_taxes'][$shtax_count]['percentage'] = $shtax_percent;
        $product_Detail[1]['final_details']['sh_taxes'][$shtax_count]['amount'] = $shtaxamount;
        $product_Detail[1]['final_details']['sh_taxes'][$shtax_count]['taxid'] = $shtax_details[$shtax_count]['taxid'];
        $product_Detail[1]['final_details']['sh_taxes'][$shtax_count]['type'] = $shtax_details[$shtax_count]['type'];
        $product_Detail[1]['final_details']['sh_taxes'][$shtax_count]['method'] = $shtax_details[$shtax_count]['method'];
        $product_Detail[1]['final_details']['sh_taxes'][$shtax_count]['regions'] = Zend_Json::decode(html_entity_decode($shtax_details[$shtax_count]['regions']));
        $product_Detail[1]['final_details']['sh_taxes'][$shtax_count]['compoundon'] = Zend_Json::decode(html_entity_decode($shtax_details[$shtax_count]['compoundon']));
    }
    $shtaxtotal = number_format($shtaxtotal, $no_of_decimal_places, '.', '');
    $product_Detail[1]['final_details']['shtax_totalamount'] = $shtaxtotal;

    //To set the Adjustment value
    $adjustment = ($focus->column_fields['txtAdjustment'] != '') ? $focus->column_fields['txtAdjustment'] : 0;
    $adjustment = number_format($adjustment, $no_of_decimal_places, '.', '');
    $product_Detail[1]['final_details']['adjustment'] = $adjustment;

    //To set the grand total
    $grandTotal = ($focus->column_fields['hdnGrandTotal'] != '') ? $focus->column_fields['hdnGrandTotal'] : 0;
    $grandTotal = number_format($grandTotal, $no_of_decimal_places, '.', '');
    $product_Detail[1]['final_details']['grandTotal'] = $grandTotal;

    $log->debug("Exiting getAssociatedProducts method ...");

    return $product_Detail;
}

/** This function returns the data type of the vtiger_fields, with vtiger_field label, which is used for javascript validation.
 * Param $validationData - array of vtiger_fieldnames with datatype
 * Return type array
 */
function split_validationdataArray($validationData) {
    global $log;
    $log->debug("Entering split_validationdataArray(" . $validationData . ") method ...");
    $fieldName = '';
    $fieldLabel = '';
    $fldDataType = '';
    $rows = count($validationData);
    foreach ($validationData as $fldName => $fldLabel_array) {
        if ($fieldName == '') {
            $fieldName = "'" . $fldName . "'";
        } else {
            $fieldName .= ",'" . $fldName . "'";
        }
        foreach ($fldLabel_array as $fldLabel => $datatype) {
            if ($fieldLabel == '') {
                $fieldLabel = "'" . addslashes($fldLabel) . "'";
            } else {
                $fieldLabel .= ",'" . addslashes($fldLabel) . "'";
            }
            if ($fldDataType == '') {
                $fldDataType = "'" . $datatype . "'";
            } else {
                $fldDataType .= ",'" . $datatype . "'";
            }
        }
    }
    $data['fieldname'] = $fieldName;
    $data['fieldlabel'] = $fieldLabel;
    $data['datatype'] = $fldDataType;
    $log->debug("Exiting split_validationdataArray method ...");
    return $data;
}

/* uni_cnfsecrm */

/** This function returns the detailed list of vtiger_StandardObjects associated to a given entity or a record.
 * Param $module - module name
 * Param $focus - module object
 * Param $seid - sales entity id
 * Return type is an object array
 */
function getSessionAssociatedDates($module, $focus, $seid = '', $refModuleName = false) {
    global $log;
    $log->debug("Entering getSessionAssociatedDates(" . get_class($focus) . ") method ...");
    global $adb;
    $output = '';
    global $theme, $current_user;

    $no_of_decimal_places = getCurrencyDecimalPlaces();

    $theme_path = "themes/" . $theme . "/";
    $image_path = $theme_path . "images/";
    $dates_Detail = Array();

    $query = "SELECT  vtiger_sessionsdatesrel.sequence_no,vtiger_sessionsdatesrel.date_start,vtiger_sessionsdatesrel.start_matin,vtiger_sessionsdatesrel.end_matin,
            vtiger_sessionsdatesrel.start_apresmidi,vtiger_sessionsdatesrel.end_apresmidi,vtiger_sessionsdatesrel.duree_formation,vtiger_sessionsdatesrel.id,crmentity.deleted
                               FROM vtiger_sessionsdatesrel                              
                               LEFT JOIN vtiger_crmentity AS crmentity ON crmentity.crmid=vtiger_sessionsdatesrel.id
                               WHERE vtiger_sessionsdatesrel.id=?
 		               ORDER BY vtiger_sessionsdatesrel.id ASC";
    $params = array($focus->id);
    $result = $adb->pquery($query, $params);
    $num_rows = $adb->num_rows($result);

    for ($i = 1; $i <= $num_rows; $i++) {
        $deleted = $adb->query_result($result, $i - 1, 'deleted');
        $sequence_no = $adb->query_result($result, $i - 1, 'sequence_no');
        $date_start = $adb->query_result($result, $i - 1, 'date_start');
        $start_matin = $adb->query_result($result, $i - 1, 'start_matin');
        $end_matin = $adb->query_result($result, $i - 1, 'end_matin');
        $start_apresmidi = $adb->query_result($result, $i - 1, 'start_apresmidi');
        $end_apresmidi = $adb->query_result($result, $i - 1, 'end_apresmidi');
        $duree_formation = $adb->query_result($result, $i - 1, 'duree_formation');
        $id = $adb->query_result($result, $i - 1, 'id');

        $dates_Detail[$i]['dateid' . $i] = $sequence_no;
        $dates_Detail[$i]['date_start' . $i] = from_html($date_start);
        $dates_Detail[$i]['start_matin' . $i] = from_html($start_matin);
        $dates_Detail[$i]['end_matin' . $i] = from_html($end_matin);
        $dates_Detail[$i]['start_apresmidi' . $i] = from_html($start_apresmidi);
        $dates_Detail[$i]['end_apresmidi' . $i] = from_html($end_apresmidi);
        $dates_Detail[$i]['duree_formation' . $i] = from_html($duree_formation);
    }
    $log->debug("Exiting getAssociatedDates method ...");
    return $dates_Detail;
}

/** This function returns the detailed list of vtiger_StandardObjects associated to a given entity or a record.
 * Param $module - module name
 * Param $focus - module object
 * Param $seid - sales entity id
 * Return type is an object array
 */
function getInventoryAssociatedDates($module, $focus) {
    global $log;
    $log->debug("Entering getInventoryAssociatedDates(" . get_class($focus) . "");
    global $adb;
    $output = '';
    global $theme, $current_user;

    $no_of_decimal_places = getCurrencyDecimalPlaces();

    $theme_path = "themes/" . $theme . "/";
    $image_path = $theme_path . "images/";
    $dates_Detail = Array();

    $query = "SELECT  vtiger_inventorydatesrel.id,vtiger_inventorydatesrel.sequence_no,vtiger_inventorydatesrel.date_start,vtiger_inventorydatesrel.start_matin,vtiger_inventorydatesrel.end_matin,
            vtiger_inventorydatesrel.start_apresmidi,vtiger_inventorydatesrel.end_apresmidi,duree_formation,crmentity.deleted
                               FROM vtiger_inventorydatesrel                              
                               LEFT JOIN vtiger_crmentity AS crmentity ON crmentity.crmid=vtiger_inventorydatesrel.id
                               WHERE vtiger_inventorydatesrel.id=?
 		               ORDER BY vtiger_inventorydatesrel.sequence_no ASC";
    $params = array($focus->id);

    $result = $adb->pquery($query, $params);
    $num_rows = $adb->num_rows($result);

    for ($i = 1; $i <= $num_rows; $i++) {
        $deleted = $adb->query_result($result, $i - 1, 'deleted');
        $id = $adb->query_result($result, $i - 1, 'id');
        $sequence_no = $adb->query_result($result, $i - 1, 'sequence_no');
        $date_start = $adb->query_result($result, $i - 1, 'date_start');
        $start_matin = $adb->query_result($result, $i - 1, 'start_matin');
        $end_matin = $adb->query_result($result, $i - 1, 'end_matin');
        $start_apresmidi = $adb->query_result($result, $i - 1, 'start_apresmidi');
        $end_apresmidi = $adb->query_result($result, $i - 1, 'end_apresmidi');
        $duree_formation = $adb->query_result($result, $i - 1, 'duree_formation');

        $dates_Detail[$i]['sequence_no' . $i] = $sequence_no;
        $dates_Detail[$i]['date_start' . $i] = $date_start;
        $dates_Detail[$i]['start_matin' . $i] = from_html($start_matin);
        $dates_Detail[$i]['end_matin' . $i] = from_html($end_matin);
        $dates_Detail[$i]['start_apresmidi' . $i] = from_html($start_apresmidi);
        $dates_Detail[$i]['end_apresmidi' . $i] = from_html($end_apresmidi);
        $dates_Detail[$i]['duree_formation' . $i] = $duree_formation;
    }
    $log->debug("Exiting getAssociatedInventoryDates method ...");
    return $dates_Detail;
}

function getAssociatedFinanceurs($module, $focus, $seid = '', $refModuleName = false) {
    global $adb;
    $financeurs_Detail = Array();
    $query = 'SELECT vtiger_vendor.vendorname, vtiger_inventoryfinanceurrel.vendorid,vtiger_inventoryfinanceurrel.pourcentage,vtiger_inventoryfinanceurrel.montant,
        vtiger_inventoryfinanceurrel.tva,vtiger_inventoryfinanceurrel.ttc,postalcode,street,city,country,phone,email
        FROM vtiger_inventoryfinanceurrel
        INNER JOIN vtiger_vendor ON vtiger_vendor.vendorid =vtiger_inventoryfinanceurrel.vendorid
        WHERE id=?';
    $params = array($focus->id);
    $result = $adb->pquery($query, $params);
    $num_rows = $adb->num_rows($result);

    for ($i = 1; $i <= $num_rows; $i++) {
        $financeurs_Detail[$i]['vendorid'] = $adb->query_result($result, $i - 1, 'vendorid');
        $financeurs_Detail[$i]['vendorname'] = $adb->query_result($result, $i - 1, 'vendorname');
        $financeurs_Detail[$i]['pourcentage'] = $adb->query_result($result, $i - 1, 'pourcentage');
        $financeurs_Detail[$i]['montant'] = formatPriceWithDecimal($adb->query_result($result, $i - 1, 'montant'));
        $financeurs_Detail[$i]['tva'] = formatPriceWithDecimal($adb->query_result($result, $i - 1, 'tva'));
        $financeurs_Detail[$i]['ttc'] = formatPriceWithDecimal($adb->query_result($result, $i - 1, 'ttc'));
        $financeurs_Detail[$i]['street'] = $adb->query_result($result, $i - 1, 'street');
        $financeurs_Detail[$i]['city'] = $adb->query_result($result, $i - 1, 'city');
        $financeurs_Detail[$i]['postalcode'] = $adb->query_result($result, $i - 1, 'postalcode');
        $financeurs_Detail[$i]['phone'] = $adb->query_result($result, $i - 1, 'phone');
        $financeurs_Detail[$i]['vendor_no'] = $adb->query_result($result, $i - 1, 'vendor_no');
    }
//    $monfichier = fopen('debug_financeur_detail.txt', 'a+');
//    fputs($monfichier, "\n" . 'financeur nombre'.$financeurs_Detail['vendorid'][5]);
//    fclose($monfichier);     
    return $financeurs_Detail;
}

function getAssociatedApprenants($module, $focus, $seid = '', $refModuleName = false) {
    global $adb;
    $apprenants_Detail = Array();
    $query = 'SELECT resultat, inscrit,etat, vtiger_inventoryapprenantsrel.id,apprenantid,
            CONCAT(vtiger_contactdetails.firstname, " ", vtiger_contactdetails.lastname) AS contactName,vtiger_contactdetails.email,vtiger_contactdetails.phone,
            vtiger_contactdetails.contact_no,vtiger_contactdetails.lastname,vtiger_account.account_no,accountname,email1
            FROM vtiger_inventoryapprenantsrel
            INNER JOIN vtiger_contactdetails ON vtiger_contactdetails.contactid =vtiger_inventoryapprenantsrel.apprenantid
            LEFT JOIN vtiger_account ON vtiger_account.accountid =vtiger_contactdetails.accountid
            WHERE id=?';

    $params = array($focus->id);
    $result = $adb->pquery($query, $params);
    $num_rows = $adb->num_rows($result);

    for ($i = 1; $i <= $num_rows; $i++) {
        $apprenants_Detail[$i]['contactname'] = $adb->query_result($result, $i - 1, 'contactname');
        $apprenants_Detail[$i]['resultat'] = $adb->query_result($result, $i - 1, 'resultat');
        $apprenants_Detail[$i]['inscrit'] = $adb->query_result($result, $i - 1, 'inscrit');
        $apprenants_Detail[$i]['etat'] = $adb->query_result($result, $i - 1, 'etat');
        $apprenants_Detail[$i]['apprenantid'] = $adb->query_result($result, $i - 1, 'apprenantid');
        $apprenants_Detail[$i]['email'] = $adb->query_result($result, $i - 1, 'email');
        $apprenants_Detail[$i]['phone'] = $adb->query_result($result, $i - 1, 'phone');
        $apprenants_Detail[$i]['contact_no'] = $adb->query_result($result, $i - 1, 'contact_no');
        $apprenants_Detail[$i]['nomclient'] = $adb->query_result($result, $i - 1, 'accountname');
        $apprenants_Detail[$i]['numclient'] = $adb->query_result($result, $i - 1, 'account_no');
    }

//    $monfichier = fopen('debug_apprenant_detail.txt', 'a+');
//    fputs($monfichier, "\n" . 'apprenant nombre '.$apprenants_Detail[1]['id_apprenant']);
//    fclose($monfichier);     
    return $apprenants_Detail;
}

function getSessionAssociatedApprenants($module, $focus, $seid = '', $refModuleName = false) {
    global $adb;
    $apprenants_Detail = Array();

    /* unicnfsecrm_022020_13 */
    $facture_payer = array();
    $facture_impayer = array();
    $queryinvoice = 'select vtiger_invoice.invoicestatus,vtiger_invoice.accountid 
                FROM vtiger_invoice 
                INNER JOIN vtiger_salesorder on vtiger_invoice.salesorderid = vtiger_salesorder.salesorderid 
                INNER JOIN vtiger_activity on vtiger_salesorder.session = vtiger_activity.activityid 
                WHERE vtiger_activity.activityid = ?';
    $paramsinvoice = array($focus->id);
    $resultinvoice = $adb->pquery($queryinvoice, $paramsinvoice);
    $num = $adb->num_rows($resultinvoice);
    for ($i = 0; $i <= $num; $i++) {
        $account = $adb->query_result($resultinvoice, $i, 'accountid');
        $statut = $adb->query_result($resultinvoice, $i, 'invoicestatus');
        if ($statut == 'Paid') {
            $facture_payer[] = $account;
        } else {
            $facture_impayer[] = $account;
        }
    }

    $query = 'SELECT resultat,ticket_examen, inscrit,etat, vtiger_sessionsapprenantsrel.id,apprenantid,
            CONCAT(vtiger_contactdetails.firstname, " ", vtiger_contactdetails.lastname) AS contactName,vtiger_contactdetails.email,vtiger_contactdetails.phone,
            vtiger_contactdetails.contact_no,vtiger_contactdetails.lastname,vtiger_account.account_no,accountname,be_essai,be_mesurage,be_verification,be_manoeuvre,he_essai,he_mesurage,he_verification,he_manoeuvre,initiale,recyclage,testprerequis,electricien
        FROM vtiger_sessionsapprenantsrel
        INNER JOIN vtiger_contactdetails ON vtiger_contactdetails.contactid = vtiger_sessionsapprenantsrel.apprenantid
        INNER JOIN vtiger_account ON vtiger_account.accountid = vtiger_contactdetails.accountid
        WHERE id=?';
    $params = array($focus->id);
    $result = $adb->pquery($query, $params);
    $num_rows = $adb->num_rows($result);

    for ($i = 1; $i <= $num_rows; $i++) {
        $apprenants_Detail[$i]['contactname'] = $adb->query_result($result, $i - 1, 'contactname');
        $apprenants_Detail[$i]['resultat'] = $adb->query_result($result, $i - 1, 'resultat');
        $apprenants_Detail[$i]['ticket_examen'] = $adb->query_result($result, $i - 1, 'ticket_examen');
        $apprenants_Detail[$i]['inscrit'] = $adb->query_result($result, $i - 1, 'inscrit');
        $apprenants_Detail[$i]['etat'] = $adb->query_result($result, $i - 1, 'etat');
        $apprenants_Detail[$i]['apprenantid'] = $adb->query_result($result, $i - 1, 'apprenantid');
        $apprenants_Detail[$i]['email'] = $adb->query_result($result, $i - 1, 'email');
        $apprenants_Detail[$i]['phone'] = $adb->query_result($result, $i - 1, 'phone');
        $apprenants_Detail[$i]['contact_no'] = $adb->query_result($result, $i - 1, 'contact_no');
        $apprenants_Detail[$i]['nomclient'] = $adb->query_result($result, $i - 1, 'accountname');
        $apprenants_Detail[$i]['numclient'] = $adb->query_result($result, $i - 1, 'account_no');

        $apprenants_Detail[$i]['be_essai'] = $adb->query_result($result, $i - 1, 'be_essai');
        $apprenants_Detail[$i]['be_mesurage'] = $adb->query_result($result, $i - 1, 'be_mesurage');
        $apprenants_Detail[$i]['be_verification'] = $adb->query_result($result, $i - 1, 'be_verification');
        $apprenants_Detail[$i]['be_manoeuvre'] = $adb->query_result($result, $i - 1, 'be_manoeuvre');
        $apprenants_Detail[$i]['he_essai'] = $adb->query_result($result, $i - 1, 'he_essai');
        $apprenants_Detail[$i]['he_mesurage'] = $adb->query_result($result, $i - 1, 'he_mesurage');
        $apprenants_Detail[$i]['he_verification'] = $adb->query_result($result, $i - 1, 'he_verification');
        $apprenants_Detail[$i]['he_manoeuvre'] = $adb->query_result($result, $i - 1, 'he_manoeuvre');
        $apprenants_Detail[$i]['initiale'] = $adb->query_result($result, $i - 1, 'initiale');
        $apprenants_Detail[$i]['recyclage'] = $adb->query_result($result, $i - 1, 'recyclage');
        $apprenants_Detail[$i]['testprerequis'] = $adb->query_result($result, $i - 1, 'testprerequis');
        $apprenants_Detail[$i]['electricien'] = $adb->query_result($result, $i - 1, 'electricien');

        /* unicnfsecrm_022020_13 */
        if (in_array($apprenants_Detail[$i]['accountid'], $facture_payer)) {
            $apprenants_Detail[$i]['statutfacture'] = 'PAYE';
        } else if (in_array($apprenants_Detail[$i]['accountid'], $facture_impayer)) {
            $apprenants_Detail[$i]['statutfacture'] = 'NON PAYE';
        } else {
            $apprenants_Detail[$i]['statutfacture'] = 'NON FACTURE';
        }
    }

    return $apprenants_Detail;
}

function getOptionsApprenants($module, $sessionid, $apprenantid) {
    global $adb;
    $apprenants_Detail = Array();
    $query = 'SELECT resultat,ticket_examen, inscrit,etat, vtiger_sessionsapprenantsrel.id,apprenantid,
            CONCAT(vtiger_contactdetails.firstname, " ", vtiger_contactdetails.lastname) AS contactName,vtiger_contactdetails.email,vtiger_contactdetails.phone,
            vtiger_contactdetails.contact_no,vtiger_contactdetails.lastname,vtiger_account.account_no,accountname,be_essai,be_mesurage,be_verification,be_manoeuvre,he_essai,he_mesurage,he_verification,he_manoeuvre,initiale,recyclage,testprerequis,electricien
        FROM vtiger_sessionsapprenantsrel
        INNER JOIN vtiger_contactdetails ON vtiger_contactdetails.contactid = vtiger_sessionsapprenantsrel.apprenantid
        INNER JOIN vtiger_account ON vtiger_account.accountid = vtiger_contactdetails.accountid
        WHERE id=? and vtiger_sessionsapprenantsrel.apprenantid = ?';
    $params = array($sessionid, $apprenantid);
    $result = $adb->pquery($query, $params);
    $num_rows = $adb->num_rows($result);

    $apprenants_Detail[0]['be_essai'] = $adb->query_result($result, 0, 'be_essai');
    $apprenants_Detail[0]['be_mesurage'] = $adb->query_result($result, 0, 'be_mesurage');
    $apprenants_Detail[0]['be_verification'] = $adb->query_result($result, 0, 'be_verification');
    $apprenants_Detail[0]['be_manoeuvre'] = $adb->query_result($result, 0, 'be_manoeuvre');
    $apprenants_Detail[0]['he_essai'] = $adb->query_result($result, 0, 'he_essai');
    $apprenants_Detail[0]['he_mesurage'] = $adb->query_result($result, 0, 'he_mesurage');
    $apprenants_Detail[0]['he_verification'] = $adb->query_result($result, 0, 'he_verification');
    $apprenants_Detail[0]['he_manoeuvre'] = $adb->query_result($result, 0, 'he_manoeuvre');
    $apprenants_Detail[0]['initiale'] = $adb->query_result($result, 0, 'initiale');
    $apprenants_Detail[0]['recyclage'] = $adb->query_result($result, 0, 'recyclage');
    $apprenants_Detail[0]['testprerequis'] = $adb->query_result($result, 0, 'testprerequis');
    $apprenants_Detail[0]['electricien'] = $adb->query_result($result, 0, 'electricien');
    $apprenants_Detail[0]['resultat'] = $adb->query_result($result, 0, 'resultat');

    return $apprenants_Detail;
}

function getSessionAssociatedFinanceurs($module, $focus, $seid = '', $refModuleName = false) {
    global $adb;
    $financeurs_Detail = Array();
    $query = 'SELECT vtiger_vendor.vendorname, vtiger_sessionsfinanceurrel.vendorid,vtiger_sessionsfinanceurrel.montant,
        vtiger_sessionsfinanceurrel.tva,vtiger_sessionsfinanceurrel.ttc,postalcode,street,city,country,phone,email,vendor_no
        FROM vtiger_sessionsfinanceurrel
        INNER JOIN vtiger_vendor ON vtiger_vendor.vendorid =vtiger_sessionsfinanceurrel.vendorid
        WHERE id=?';
    $params = array($focus->id);
    $result = $adb->pquery($query, $params);
    $num_rows = $adb->num_rows($result);

    for ($i = 1; $i <= $num_rows; $i++) {
        $financeurs_Detail[$i]['vendorid'] = $adb->query_result($result, $i - 1, 'vendorid');
        $financeurs_Detail[$i]['vendorname'] = $adb->query_result($result, $i - 1, 'vendorname');
        $financeurs_Detail[$i]['montant'] = $adb->query_result($result, $i - 1, 'montant');
        $financeurs_Detail[$i]['tva'] = $adb->query_result($result, $i - 1, 'tva');
        $financeurs_Detail[$i]['ttc'] = $adb->query_result($result, $i - 1, 'ttc');
        $financeurs_Detail[$i]['street'] = $adb->query_result($result, $i - 1, 'street');
        $financeurs_Detail[$i]['city'] = $adb->query_result($result, $i - 1, 'city');
        $financeurs_Detail[$i]['postalcode'] = $adb->query_result($result, $i - 1, 'postalcode');
        $financeurs_Detail[$i]['phone'] = $adb->query_result($result, $i - 1, 'phone');
        $financeurs_Detail[$i]['vendor_no'] = $adb->query_result($result, $i - 1, 'vendor_no');
    }

//    $monfichier = fopen('debug_financeur_detail.txt', 'a+');
//    fputs($monfichier, "\n" . 'financeur nombre'.$financeurs_Detail['vendorid'][5]);
//    fclose($monfichier);     
    return $financeurs_Detail;
}

function formatPriceWithDecimal($value) {
    $value = number_format($value, 2, '.', '');
    return $value;
}

function getNomformation($module, $focus, $seid = '', $refModuleName = false) {
    global $adb;
    $query = 'SELECT vtiger_service.servicename,vtiger_activity.formation 
                FROM vtiger_service 
                INNER JOIN vtiger_activity on vtiger_activity.formation = vtiger_service.serviceid 
                WHERE vtiger_activity.activityid = ?';
    $params = array($focus->id);
    $result = $adb->pquery($query, array($params));
    $nom_formation = $adb->query_result($result, '', 'servicename');

    return $nom_formation;
}

function getCategorieformation($module, $focus, $seid = '', $refModuleName = false) {
    global $adb;
    $query = 'SELECT vtiger_service.servicecategory,vtiger_activity.formation 
                FROM vtiger_service 
                INNER JOIN vtiger_activity on vtiger_activity.formation = vtiger_service.serviceid 
                WHERE vtiger_activity.activityid = ?';
    $params = array($focus->id);
    $result = $adb->pquery($query, array($params));
    $servicecategory = $adb->query_result($result, '', 'servicecategory');

    return $servicecategory;
}

?>