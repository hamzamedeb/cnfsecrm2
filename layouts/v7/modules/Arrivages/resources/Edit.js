/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("Arrivages_Wizard_Js", {

    zeroDiscountType: 'zero',
    percentageDiscountType: 'percentage',
    directAmountDiscountType: 'amount',

    individualTaxType: 'individual',
    groupTaxType: 'group',

    lineItemPopOverTemplate: '<div class="popover lineItemPopover" role="tooltip"><div class="arrow"></div>\n\
                                <h3 class="popover-title"></h3>\n\
								<div class="popover-content"></div>\n\</div>'

}, {
    //Container which stores the line item elements
    lineItemContentsContainer: false,
    //Container which stores line item result details
    lineItemResultContainer: false,
    //contains edit view form element
    editViewForm: false,
    //a variable which will be used to hold the sequence of the row
    rowSequenceHolder: false,
    //holds the element which has basic hidden row which we can clone to add rows
    basicRow: false,
    //will be having class which is used to identify the rows
    rowClass: 'lineItemRow',
    //Will have the mapping of address fields based on the modules
    addressFieldsMapping: {
        'Contacts': {
            'bill_street': 'mailingstreet',
            'ship_street': 'otherstreet',
            'bill_pobox': 'mailingpobox',
            'ship_pobox': 'otherpobox',
            'bill_city': 'mailingcity',
            'ship_city': 'othercity',
            'bill_state': 'mailingstate',
            'ship_state': 'otherstate',
            'bill_code': 'mailingzip',
            'ship_code': 'otherzip',
            'bill_country': 'mailingcountry',
            'ship_country': 'othercountry'
        },

        'Accounts': {
            'bill_street': 'bill_street',
            'ship_street': 'ship_street',
            'bill_pobox': 'bill_pobox',
            'ship_pobox': 'ship_pobox',
            'bill_city': 'bill_city',
            'ship_city': 'ship_city',
            'bill_state': 'bill_state',
            'ship_state': 'ship_state',
            'bill_code': 'bill_code',
            'ship_code': 'ship_code',
            'bill_country': 'bill_country',
            'ship_country': 'ship_country'
        },

        'Vendors': {
            'bill_street': 'street',
            'ship_street': 'street',
            'bill_pobox': 'pobox',
            'ship_pobox': 'pobox',
            'bill_city': 'city',
            'ship_city': 'city',
            'bill_state': 'state',
            'ship_state': 'state',
            'bill_code': 'postalcode',
            'ship_code': 'postalcode',
            'bill_country': 'country',
            'ship_country': 'country'
        },
        'Leads': {
            'bill_street': 'lane',
            'ship_street': 'lane',
            'bill_pobox': 'pobox',
            'ship_pobox': 'pobox',
            'bill_city': 'city',
            'ship_city': 'city',
            'bill_state': 'state',
            'ship_state': 'state',
            'bill_code': 'code',
            'ship_code': 'code',
            'bill_country': 'country',
            'ship_country': 'country'
        }
    },

    //Address field mapping between modules specific for billing and shipping
    addressFieldsMappingBetweenModules: {
        'AccountsBillMap': {
            'bill_street': 'bill_street',
            'bill_pobox': 'bill_pobox',
            'bill_city': 'bill_city',
            'bill_state': 'bill_state',
            'bill_code': 'bill_code',
            'bill_country': 'bill_country'
        },
        'AccountsShipMap': {
            'ship_street': 'ship_street',
            'ship_pobox': 'ship_pobox',
            'ship_city': 'ship_city',
            'ship_state': 'ship_state',
            'ship_code': 'ship_code',
            'ship_country': 'ship_country'
        },
        'ContactsBillMap': {
            'bill_street': 'mailingstreet',
            'bill_pobox': 'mailingpobox',
            'bill_city': 'mailingcity',
            'bill_state': 'mailingstate',
            'bill_code': 'mailingzip',
            'bill_country': 'mailingcountry'
        },
        'ContactsShipMap': {
            'ship_street': 'otherstreet',
            'ship_pobox': 'otherpobox',
            'ship_city': 'othercity',
            'ship_state': 'otherstate',
            'ship_code': 'otherzip',
            'ship_country': 'othercountry'
        },
        'LeadsBillMap': {
            'bill_street': 'lane',
            'bill_pobox': 'pobox',
            'bill_city': 'city',
            'bill_state': 'state',
            'bill_code': 'code',
            'bill_country': 'country'
        },
        'LeadsShipMap': {
            'ship_street': 'lane',
            'ship_pobox': 'pobox',
            'ship_city': 'city',
            'ship_state': 'state',
            'ship_code': 'code',
            'ship_country': 'country'
        }

    },

    //Address field mapping within module
    addressFieldsMappingInModule: {
        'bill_street': 'ship_street',
        'bill_pobox': 'ship_pobox',
        'bill_city': 'ship_city',
        'bill_state': 'ship_state',
        'bill_code': 'ship_code',
        'bill_country': 'ship_country'
    },

    dummyLineItemRow: false,
    lineItemsHolder: false,
    numOfLineItems: false,
    customLineItemFields: false,
    customFieldsDefaultValues: false,
    numOfCurrencyDecimals: false,
    taxTypeElement: false,
    regionElement: false,
    currencyElement: false,
    finalCoefficientUIEle: false,
    conversionRateEle: false,
    overAllDiscountEle: false,
    preTaxTotalEle: false,

    //final calculation elements
    netTotalEle: false,
    finalDiscountTotalEle: false,
    finalTaxEle: false,
    finalDiscountEle: false,

    chargesTotalEle: false,
    chargesContainer: false,
    chargeTaxesContainer: false,
    chargesTotalDisplay: false,
    chargeTaxesTotal: false,
    deductTaxesTotal: false,
    adjustmentEle: false,
    adjustmentTypeEles: false,
    grandTotal: false,
    groupTaxContainer: false,
    dedutTaxesContainer: false,

    lineItemDetectingClass: 'lineItemRowArticle',

    init: function () {
        this._super();
        this.initializeVariables();
    },

    initializeVariables: function () {
        console.log("initializeVariables");
        this.dummyLineItemRow = jQuery('#rowArticle0');
        this.lineItemsHolder = jQuery('#lineItemTabArticle');
        this.numOfLineItems = this.lineItemsHolder.find('.' + this.lineItemDetectingClass).length;
        if (typeof jQuery('#customFields').val() == 'undefined') {
            this.customLineItemFields = [];
        } else {
            this.customLineItemFields = JSON.parse(jQuery('#customFields').val());
        }

        if (typeof jQuery('#customFieldsDefaultValues').val() == 'undefined') {
            this.customFieldsDefaultValues = [];
        } else {
            this.customFieldsDefaultValues = JSON.parse(jQuery('#customFieldsDefaultValues').val());
        }

        this.numOfCurrencyDecimals = parseInt(jQuery('.numberOfCurrencyDecimal').val());
        this.taxTypeElement = jQuery('#taxtype');
        this.regionElement = jQuery('#region_id');
        this.currencyElement = jQuery('#currency_id');

        this.netTotalEle = jQuery('#netTotal');
        this.finalDiscountTotalEle = jQuery('#discountTotal_final');
        this.finalTaxEle = jQuery('#tax_final');
        this.finalCoefficientUIEle = jQuery('#finalCoefficientUI');
        this.finalDiscountEle = jQuery('#finalDiscount');
        this.conversionRateEle = jQuery('#conversion_rate');
        this.overAllDiscountEle = jQuery('#overallDiscount');
        this.chargesTotalEle = jQuery('#chargesTotal');
        this.preTaxTotalEle = jQuery('#preTaxTotal');
        this.chargesContainer = jQuery('#chargesBlock')
        this.chargesTotalDisplay = jQuery('#chargesTotalDisplay');
        this.chargeTaxesContainer = jQuery('#chargeTaxesBlock');
        this.chargeTaxesTotal = jQuery('#chargeTaxTotalHidden');
        this.deductTaxesTotal = jQuery('#deductTaxesTotalAmount');
        this.adjustmentEle = jQuery('#adjustment');
        this.adjustmentTypeEles = jQuery('input[name="adjustmentType"]');
        this.grandTotal = jQuery('#grandTotal');
        this.groupTaxContainer = jQuery('#group_tax_div');
        this.dedutTaxesContainer = jQuery('#deductTaxesBlock');

    },

    /**
     * Function that is used to get the line item container
     * @return : jQuery object
     */
    getLineItemContentsContainer: function () {
        if (this.lineItemContentsContainer == false) {
            this.setLineItemContainer(jQuery('#lineItemTab'));
        }
        return this.lineItemContentsContainer;
    },

    /**
     * Function to set line item container
     * @params : element - jQuery object which represents line item container
     * @return : current instance ;
     */
    setLineItemContainer: function (element) {
        this.lineItemContentsContainer = element;
        return this;
    },

    /**
     * Function to set line item result container
     * @param : element - jQuery object which represents line item result container
     * @result : current instance
     */
    setLinteItemResultContainer: function (element) {
        this.lineItemResultContainer = element;
        return this;
    },
    /**
     * Function which will copy the address details
     */
    copyAddressDetails: function (data, container, addressMap) {
        var self = this;
        var sourceModule = data['source_module'];
        var noAddress = true;
        var errorMsg;

        this.getRecordDetails(data).then(
                function (data) {
                    var response = data;
                    if (typeof addressMap != "undefined") {
                        var result = response['data'];
                        for (var key in addressMap) {
                            if (result[addressMap[key]] != "") {
                                noAddress = false;
                                break;
                            }
                        }
                        if (noAddress) {
                            if (sourceModule == "Accounts") {
                                errorMsg = 'JS_SELECTED_ACCOUNT_DOES_NOT_HAVE_AN_ADDRESS';
                            } else if (sourceModule == "Contacts") {
                                errorMsg = 'JS_SELECTED_CONTACT_DOES_NOT_HAVE_AN_ADDRESS';
                            } else if (sourceModule == "Leads") {
                                errorMsg = 'JS_SELECTED_LEAD_DOES_NOT_HAVE_AN_ADDRESS';
                            }
                            app.helper.showErrorNotification({'message': app.vtranslate(errorMsg)});
                        } else {
                            self.mapAddressDetails(addressMap, result, container);
                        }
                    } else {
                        self.mapAddressDetails(self.addressFieldsMapping[sourceModule], response['data'], container);
                        if (sourceModule == "Accounts") {
                            container.find('.accountAddress').attr('checked', 'checked');
                        } else if (sourceModule == "Contacts") {
                            container.find('.contactAddress').attr('checked', 'checked');
                        }
                    }
                },
                function (error, err) {

                });
    },

    /**
     * Function which will copy the address details of the selected record
     */
    mapAddressDetails: function (addressDetails, result, container) {
        for (var key in addressDetails) {
            container.find('[name="' + key + '"]').val(result[addressDetails[key]]);
            container.find('[name="' + key + '"]').trigger('change');
        }
    },

    /**
     * Function to copy address between fields
     * @param strings which accepts value as either odd or even
     */
    copyAddress: function (swapMode) {
        var self = this;
        var formElement = this.getForm();
        var addressMapping = this.addressFieldsMappingInModule;
        if (swapMode == "false") {
            for (var key in addressMapping) {
                var fromElement = formElement.find('[name="' + key + '"]');
                var toElement = formElement.find('[name="' + addressMapping[key] + '"]');
                toElement.val(fromElement.val());
            }
        } else if (swapMode) {
            var swappedArray = self.swapObject(addressMapping);
            for (var key in swappedArray) {
                var fromElement = formElement.find('[name="' + key + '"]');
                var toElement = formElement.find('[name="' + swappedArray[key] + '"]');
                toElement.val(fromElement.val());
            }
            toElement.val(fromElement.val());
        }
    },

    /**
     * Function to swap array
     * @param Array that need to be swapped
     */
    swapObject: function (objectToSwap) {
        var swappedArray = {};
        var newKey, newValue;
        for (var key in objectToSwap) {
            newKey = objectToSwap[key];
            newValue = key;
            swappedArray[newKey] = newValue;
        }
        return swappedArray;
    },

    getLineItemNextRowNumber: function () {
        return ++this.numOfLineItems;
    },

    formatListPrice: function (lineItemRow, listPriceValue) {
        var listPrice = parseFloat(listPriceValue).toFixed(this.numOfCurrencyDecimals);
        lineItemRow.find('.listPrice').val(listPrice);
        return this;
    },

    getLineItemRowNumber: function (itemRow) {
        return parseInt(itemRow.attr('data-row-num'));
    },

    /**
     * Function which gives quantity value
     * @params : lineItemRow - row which represents the line item
     * @return : string
     */
    getQuantityValue: function (lineItemRow) {
        return parseFloat(lineItemRow.find('.qty').val());
    },

    /**
     * Function which will get the value of cost price
     * @params : lineItemRow - row which represents the line item
     * @return : string
     */
    getPurchaseCostValue: function (lineItemRow) {
        var rowNum = this.getLineItemRowNumber(lineItemRow);
        return parseFloat(jQuery('#purchaseCost' + rowNum).val());
    },

    /**
     * Function which will set the cost price
     * @params : lineItemRow - row which represents the line item
     * @params : cost price
     * @return : current instance;
     */
    setPurchaseCostValue: function (lineItemRow, purchaseCost) {
        if (isNaN(purchaseCost)) {
            purchaseCost = 0;
        }
        var rowNum = this.getLineItemRowNumber(lineItemRow);
        jQuery('#purchaseCost' + rowNum).val(purchaseCost);
        var quantity = this.getQuantityValue(lineItemRow);
        var updatedPurchaseCost = parseFloat(quantity) * parseFloat(purchaseCost);
        lineItemRow.find('[name="purchaseCost' + rowNum + '"]').val(updatedPurchaseCost);
        lineItemRow.find('.purchaseCost').text(updatedPurchaseCost);
        return this;
    },

    /**
     * Function which will set the image
     * @params : lineItemRow - row which represents the line item
     * @params : image source
     * @return : current instance;
     */
    setImageTag: function (lineItemRow, imgSrc) {
        var imgTag = '<img src=' + imgSrc + ' height="42" width="42">';
        lineItemRow.find('.lineItemImage').html(imgTag);
        return this;
    },

    /**
     * Function which will give me list price value
     * @params : lineItemRow - row which represents the line item
     * @return : string
     */
    getListPriceValue: function (lineItemRow) {
        return parseFloat(lineItemRow.find('.listPrice').val());
    },

    setListPriceValue: function (lineItemRow, listPriceValue) {
        var listPrice = parseFloat(listPriceValue).toFixed(this.numOfCurrencyDecimals);
        lineItemRow.find('.listPrice').val(listPrice);
        return this;
    },

    /**
     * Function which will set the line item total value excluding tax and discount
     * @params : lineItemRow - row which represents the line item
     *			 lineItemTotalValue - value which has line item total  (qty*listprice)
     * @return : current instance;
     */
    setLineItemTotal: function (lineItemRow, lineItemTotalValue) {
        lineItemRow.find('.productTotal').text(lineItemTotalValue);
        return this;
    },

    /**
     * Function which will get the value of line item total (qty*listprice)
     * @params : lineItemRow - row which represents the line item
     * @return : string
     */
    getLineItemTotal: function (lineItemRow) {
        var lineItemTotal = this.getLineItemTotalElement(lineItemRow).text();
        if (lineItemTotal)
            return parseFloat(lineItemTotal);
        return 0;
    },

    /**
     * Function which will get the line item total element
     * @params : lineItemRow - row which represents the line item
     * @return : jQuery element
     */
    getLineItemTotalElement: function (lineItemRow) {
        return lineItemRow.find('.productTotal');
    },

    getCodeBarre: function (lineItemRow) {
        return parseFloat(jQuery('.codebarre', lineItemRow).val());
    },
    setCodeBarre: function (lineItemRow, lineItemTotalValue) {
        jQuery('.codebarre', lineItemRow).text(lineItemTotalValue);
        return this;
    },

    isAdjustMentAddType: function () {
        var adjustmentSelectElement = this.adjustmentTypeEles;
        var selectionOption;
        adjustmentSelectElement.each(function () {
            if (jQuery(this).is(':checked')) {
                selectionOption = jQuery(this);
            }
        })
        if (typeof selectionOption != "undefined") {
            if (selectionOption.val() == '+') {
                return true;
            }
        }
        return false;
    },

    isAdjustMentDeductType: function () {
        var adjustmentSelectElement = this.adjustmentTypeEles;
        var selectionOption;
        adjustmentSelectElement.each(function () {
            if (jQuery(this).is(':checked')) {
                selectionOption = jQuery(this);
            }
        })
        if (typeof selectionOption != "undefined") {
            if (selectionOption.val() == '-') {
                return true;
            }
        }
        return false;
    },

    /**
     * Function which will give the closest line item row element
     * @return : jQuery object
     */
    getClosestLineItemRow: function (element) {
        return element.closest('tr.' + this.lineItemDetectingClass);
    },

    isProductSelected: function (element) {
        var parentRow = element.closest('tr');
        var productField = parentRow.find('.codebarre');
        var response = productField.valid();
        return response;
    },

    checkLineItemRow: function () {
        var numRows = this.lineItemsHolder.find('.' + this.lineItemDetectingClass).length;
        console.log("numRows " + numRows);
        if (numRows > 1) {
            this.showLineItemsDeleteIcon();
        } else {
            this.hideLineItemsDeleteIcon();
        }
    },

    showLineItemsDeleteIcon: function () {
        this.lineItemsHolder.find('.deleteRowArticle').show();
    },

    hideLineItemsDeleteIcon: function () {
        this.lineItemsHolder.find('.deleteRowArticle').hide();
    },

    clearLineItemDetails: function (parentElem) {
        console.log("clearLineItemDetails");
        var lineItemRow = this.getClosestLineItemRow(parentElem);
        jQuery('input.codebarre', lineItemRow).val('');
        jQuery('input.qty', lineItemRow).val('0');
        jQuery('input.productName', lineItemRow).val('');
        this.quantityChangeActions(lineItemRow);
    },

    saveProductCount: function () {
        jQuery('#totalProductCount').val(this.lineItemsHolder.find('tr.' + this.lineItemDetectingClass).length);
    },

    saveSubTotalValue: function () {
        jQuery('#subtotal').val(this.getPreTaxTotal());
    },

    saveTotalValue: function () {
        jQuery('#total').val(this.getGrandTotal());
    },

    /**
     * Function to save the pre tax total value
     */
    savePreTaxTotalValue: function () {
        jQuery('#pre_tax_total').val(this.getPreTaxTotal());
    },

    updateRowNumberForRow: function (lineItemRow, expectedSequenceNumber, currentSequenceNumber) {
        if (typeof currentSequenceNumber == 'undefined') {
            //by default there will zero current sequence number
            currentSequenceNumber = 0;
        }

        var idFields = new Array('codebarre', 'nomproduit', 'hdnProductId', 'purchaseCost', 'margin',
                'comment', 'qty', 'listPrice', 'discount_div', 'discount_type', 'discount_percentage',
                'discount_amount', 'lineItemType', 'searchIcon', 'netPrice', 'subprod_names',
                'productTotal', 'discountTotal', 'totalAfterDiscount', 'taxTotal', 'infosproduit');

        var classFields = new Array('taxPercentage');
        //To handle variable tax ids
        for (var classIndex in classFields) {
            var className = classFields[classIndex];
            jQuery('.' + className, lineItemRow).each(function (index, domElement) {
                var idString = domElement.id
                //remove last character which will be the row number
                idFields.push(idString.slice(0, (idString.length - 1)));
            });
        }

        var expectedRowId = 'rowArticle' + expectedSequenceNumber;
        for (var idIndex in idFields) {
            var elementId = idFields[idIndex];
            var actualElementId = elementId + currentSequenceNumber;
            var expectedElementId = elementId + expectedSequenceNumber;
            lineItemRow.find('#' + actualElementId).attr('id', expectedElementId)
                    .filter('[name="' + actualElementId + '"]').attr('name', expectedElementId);
        }

        var nameFields = new Array('discount', 'purchaseCost', 'margin');
        for (var nameIndex in nameFields) {
            var elementName = nameFields[nameIndex];
            var actualElementName = elementName + currentSequenceNumber;
            var expectedElementName = elementName + expectedSequenceNumber;
            lineItemRow.find('[name="' + actualElementName + '"]').attr('name', expectedElementName);
        }

        lineItemRow.attr('id', expectedRowId).attr('data-row-num', expectedSequenceNumber);
        lineItemRow.find('input.rowNumber').val(expectedSequenceNumber);

        return lineItemRow;
    },

    updateLineItemElementByOrder: function () {
        var self = this;
        var checkedDiscountElements = {};
        var lineItems = this.lineItemsHolder.find('tr.' + this.lineItemDetectingClass);
        lineItems.each(function (index, domElement) {
            var lineItemRow = jQuery(domElement);
            var actualRowId = lineItemRow.attr('id');

            var discountContianer = lineItemRow.find('div.discountUI');
            var element = discountContianer.find('input.discounts').filter(':checked');
            checkedDiscountElements[actualRowId] = element.data('discountType');
        });

        lineItems.each(function (index, domElement) {
            var lineItemRow = jQuery(domElement);
            var expectedRowIndex = (index + 1);
            var expectedRowId = 'rowArticle' + expectedRowIndex;
            var actualRowId = lineItemRow.attr('id');
            if (expectedRowId != actualRowId) {
                var actualIdComponents = actualRowId.split('rowArticle');
                self.updateRowNumberForRow(lineItemRow, expectedRowIndex, actualIdComponents[1]);

                var discountContianer = lineItemRow.find('div.discountUI');
                discountContianer.find('input.discounts').each(function (index1, discountElement) {
                    var discountElement = jQuery(discountElement);
                    var discountType = discountElement.data('discountType');
                    if (discountType == checkedDiscountElements[actualRowId]) {
                        discountElement.attr('checked', true);
                    }
                });
            }
        });
    },

    /**
     * Function which will initialize line items custom fields with default values if exists 
     */
    initializeLineItemRowCustomFields: function (lineItemRow, rowNum) {
        var lineItemType = lineItemRow.find('input.lineItemType').val();
        for (var cfName in this.customLineItemFields) {
            var elementName = cfName + rowNum;
            var element = lineItemRow.find('[name="' + elementName + '"]');

            var cfDataType = this.customLineItemFields[cfName];
            if (cfDataType == 'picklist' || cfDataType == 'multipicklist') {

                (cfDataType == 'multipicklist') && (element = lineItemRow.find('[name="' + elementName + '[]"]'));

                var picklistValues = element.data('productPicklistValues');
                (lineItemType == 'Services') && (picklistValues = element.data('servicePicklistValues'));
                var options = '';
                (cfDataType == 'picklist') && (options = '<option value="">' + app.vtranslate('JS_SELECT_OPTION') + '</option>');

                for (var picklistName in picklistValues) {
                    var pickListValue = picklistValues[picklistName];
                    options += '<option value="' + picklistName + '">' + pickListValue + '</option>';
                }
                element.html(options);
                element.addClass('select2');
            }

            var defaultValueInfo = this.customFieldsDefaultValues[cfName];
            if (defaultValueInfo) {
                var defaultValue = defaultValueInfo;
                if (typeof defaultValueInfo == 'object') {
                    defaultValue = defaultValueInfo['productFieldDefaultValue'];
                    (lineItemType == 'Services') && (defaultValue = defaultValueInfo['serviceFieldDefaultValue'])
                }

                if (cfDataType === 'multipicklist') {
                    if (defaultValue.length > 0) {
                        defaultValue = defaultValue.split(" |##| ");
                        var setDefaultValue = function (picklistElement, values) {
                            for (var index in values) {
                                var picklistVal = values[index];
                                picklistElement.find('option[value="' + picklistVal + '"]').prop('selected', true);
                            }
                        }(element, defaultValue)
                    }
                } else {
                    element.val(defaultValue);
                }
            } else {
                defaultValue = '';
                element.val(defaultValue);
            }
        }

        return lineItemRow;
    },

    getLineItemSetype: function (row) {
        return row.find('.lineItemType').val();
    },

    getNewLineItem: function (params) {
        var currentTarget = params.currentTarget;
        var itemType = currentTarget.data('moduleName');
        var newRow = this.dummyLineItemRow.clone(true).removeClass('hide').addClass(this.lineItemDetectingClass).removeClass('lineItemCloneCopy');
        newRow.find('.lineItemPopupArticle').filter(':not([data-module-name="' + itemType + '"])').remove();
        newRow.find('.lineItemType').val(itemType);
        var newRowNum = this.getLineItemNextRowNumber();
        this.updateRowNumberForRow(newRow, newRowNum);
        this.initializeLineItemRowCustomFields(newRow, newRowNum);
        return newRow
    },

    lineItemRowCalculations: function (lineItemRow) {

    },

    lineItemToTalResultCalculations: function () {
        console.log("lineItemToTalResultCalculations");

    },

    lineItemDeleteActions: function () {
        this.lineItemToTalResultCalculations();
    },   

    /**
     * Function which will handle the actions that need to be preformed once the qty is changed like below
     *  - calculate line item total -> discount and tax -> net price of line item -> grand total
     * @params : lineItemRow - element which will represent lineItemRow
     */
    quantityChangeActions: function (lineItemRow) {
        console.log("quantityChangeActions");
        var purchaseCost = this.getPurchaseCostValue(lineItemRow);
        this.setPurchaseCostValue(lineItemRow, purchaseCost);
        this.lineItemRowCalculations(lineItemRow);
        this.lineItemToTalResultCalculations();
    },

    getTaxDiv: function (taxObj, parentRow) {
        var rowNumber = jQuery('input.rowNumber', parentRow).val();
        var loopIterator = 1;
        var taxDiv =
                '<div class="taxUI hide" id="tax_div' + rowNumber + '">' +
                '<p class="popover_title hide"> Set Tax for : <span class="variable"></span></p>';
        if (!jQuery.isEmptyObject(taxObj)) {
            taxDiv +=
                    '<div class="individualTaxDiv">' +
                    '<table width="100%" border="0" cellpadding="5" cellspacing="0" class="table table-nobordered popupTable" id="tax_table' + rowNumber + '">';

            for (var taxName in taxObj) {
                var taxInfo = taxObj[taxName];
                taxDiv +=
                        '<tr>' +
                        '<td>  ' + taxInfo.taxlabel + '</td>' +
                        '<td style="text-align: right;">' +
                        '<input type="text" name="' + taxName + '_percentage' + rowNumber + '" data-rule-positive=true data-rule-inventory_percentage=true  id="' + taxName + '_percentage' + rowNumber + '" value="' + taxInfo.taxpercentage + '" class="taxPercentage" data-compound-on=' + taxInfo.compoundOn + ' data-regions-list="' + taxInfo.regionsList + '">&nbsp;%' +
                        '</td>' +
                        '<td style="text-align: right; padding-right: 10px;">' +
                        '<input type="text" name="popup_tax_row' + rowNumber + '" class="cursorPointer span1 taxTotal taxTotal' + taxInfo.taxid + '" value="0.0" readonly>' +
                        '</td>' +
                        '</tr>';
                loopIterator++;
            }
            taxDiv +=
                    '</table>' +
                    '</div>';
        } else {
            taxDiv +=
                    '<div class="textAlignCenter">' +
                    '<span>' + app.vtranslate('JS_NO_TAXES_EXISTS') + '</span>' +
                    '</div>';
        }

        taxDiv += '</div>';
        return jQuery(taxDiv);
    },

    mapResultsToFields: function (parentRow, responseData) {
        var lineItemNameElment = jQuery('input.productName', parentRow);
        var referenceModule = this.getLineItemSetype(parentRow);
        var lineItemRowNumber = parentRow.data('rowNum');
        for (var id in responseData) {
            var recordId = id;
            var recordData = responseData[id];
            var selectedName = recordData.name;
            console.log(recordData);
            var nomproduit = recordData.nomproduit;
            console.log(nomproduit);
            jQuery('input.nomproduit', parentRow).val(nomproduit);

        }
    },

    showLineItemPopup: function (callerParams) {
        var params = {
            'module': this.getModuleName(),
            'multi_select': true,
            'currency_id': this.currencyElement.val()
        };

        params = jQuery.extend(params, callerParams);
        var popupInstance = Vtiger_Popup_Js.getInstance();
        popupInstance.showPopup(params, 'post.LineItemPopupSelection.click');

    },

    registerAddProductService: function () {
        var self = this;
        var addLineItemEventHandler = function (e, data) {
            console.log("addLineItemEventHandler");
            var currentTarget = jQuery(e.currentTarget);
            var params = {'currentTarget': currentTarget}
            var newLineItem = self.getNewLineItem(params);
            console.log(self.lineItemsHolder)
            newLineItem = newLineItem.appendTo(self.lineItemsHolder);
            newLineItem.find('input.codebarre').addClass('autoComplete');
            newLineItem.find('.ignore-ui-registration').removeClass('ignore-ui-registration');
            vtUtils.applyFieldElementsView(newLineItem);
            app.event.trigger('post.lineItem.New', newLineItem);
            self.checkLineItemRow();
            console.log(newLineItem);
            self.registerLineItemAutoComplete(newLineItem);
            if (typeof data != "undefined") {
                self.mapResultsToFields(newLineItem, data);
            }
        }
        jQuery('#addArticle').on('click', addLineItemEventHandler);
    },

    registerDeleteLineItemEvent: function () {
        var self = this;

        this.lineItemsHolder.on('click', '.deleteRowArticle', function (e) {
            var element = jQuery(e.currentTarget);
            //removing the row
            self.getClosestLineItemRow(element).remove();
            self.checkLineItemRow();
            self.lineItemDeleteActions();
        });
    },

    registerClearLineItemSelection: function () {
        console.log("registerClearLineItemSelection");
        var self = this;

        this.lineItemsHolder.on('click', '.clearLineItemArticle', function (e) {
            var elem = jQuery(e.currentTarget);
            var parentElem = elem.closest('td');
            self.clearLineItemDetails(parentElem);
            parentElem.find('input.codebarre').removeAttr('disabled').val('');
            e.preventDefault();
        });
    },

    registerSubmitEvent: function () {
        var self = this;
        var editViewForm = this.getForm();
        //this._super();
        editViewForm.submit(function (e) {
            var deletedItemInfo = jQuery('.deletedItem', editViewForm);
            if (deletedItemInfo.length > 0) {
                e.preventDefault();
                var msg = app.vtranslate('JS_PLEASE_REMOVE_LINE_ITEM_THAT_IS_DELETED');
                app.helper.showErrorNotification({"message": msg});
                editViewForm.removeData('submit');
                return false;
            } else if (jQuery('.lineItemRowArticle').length <= 0) {
                e.preventDefault();
                msg = app.vtranslate('JS_NO_LINE_ITEM');
                app.helper.showErrorNotification({"message": msg});
                editViewForm.removeData('submit');
                return false;
            }
            self.updateLineItemElementByOrder();
            self.saveArticlesCount();
            return true;
        })
    },

    makeLineItemsSortable: function () {
        var self = this;
        this.lineItemsHolder.sortable({
            'containment': this.lineItemsHolder,
            'items': 'tr.' + this.lineItemDetectingClass,
            'revert': true,
            'tolerance': 'pointer',
            'helper': function (e, ui) {
                //while dragging helper elements td element will take width as contents width
                //so we are explicity saying that it has to be same width so that element will not
                //look like distrubed
                ui.children().each(function (index, element) {
                    element = jQuery(element);
                    element.width(element.width());
                })
                return ui;
            }
        }).mousedown(function (event) {
            //TODO : work around for issue of mouse down even hijack in sortable plugin
            self.getClosestLineItemRow(jQuery(event.target)).find('input:focus').trigger('focusout');
        });
    },

    registerLineItemAutoComplete: function (container) {
        var self = this;
        if (typeof container == 'undefined') {
            container = this.lineItemsHolder;
        }
        container.find('input.autoComplete').autocomplete({
            'minLength': '3',
            'source': function (request, response) {
                console.log("test01");
                //element will be array of dom elements
                //here this refers to auto complete instance
                var inputElement = jQuery(this.element[0]);
                var tdElement = inputElement.closest('td');
                console.log(tdElement);
                var trElement = inputElement.closest('tr');
                console.log(trElement);
                var searchValue = request.term;
                var params = {};
                var searchModule = tdElement.find('.lineItemPopup').data('moduleName');
                params.search_module = searchModule
                params.search_value = searchValue;
//                self.searchModuleNames(params).then(function (data) {
//                    var reponseDataList = new Array();
//                    var serverDataFormat = data;
//                    if (serverDataFormat.length <= 0) {
//                        serverDataFormat = new Array({
//                            'label': app.vtranslate('JS_NO_RESULTS_FOUND'),
//                            'type': 'no results'
//                        });
//                    }
//                    for (var id in serverDataFormat) {
//                        var responseData = serverDataFormat[id];
//                        reponseDataList.push(responseData);
//                    }
//                    response(reponseDataList);
//                });
                var element = jQuery(self);
                element.attr('disabled', 'disabled');
                var tdElement = element.closest('td');
                var selectedModule = tdElement.find('.lineItemPopup').data('moduleName');
                var popupElement = tdElement.find('.lineItemPopup');
                var dataUrl = "index.php?module=Arrivages&action=GetTaxesBycodeBarre&codebarre=" + searchValue;
                console.log(dataUrl);
                app.request.get({'url': dataUrl}).then(
                        function (error, data) {
                            console.log(data);
                            if (error == null) {
                                console.log(element);
                                self.mapResultsToFields(trElement, data[0]);
                            }
                        },
                        function (error, err) {

                        }
                );
            },
            'select': function (event, ui) {
            },
            'change': function (event, ui) {
                var element = jQuery(this);
                //if you dont have disabled attribute means the user didnt select the item
                if (element.attr('disabled') == undefined) {
                    element.closest('td').find('.clearLineItem').trigger('click');
                }
            }
        });
    },

    /**
     * Function which will register event for Reference Fields Selection
     */
    registerReferenceSelectionEvent: function (container) {
        var self = this;

        jQuery('input[name="contact_id"]', container).on(Vtiger_Edit_Js.referenceSelectionEvent, function (e, data) {
            self.referenceSelectionEventHandler(data, container);
        });
    },

    /**
     * Reference Fields Selection Event Handler
     */
    referenceSelectionEventHandler: function (data, container) {
        var self = this;
        if (data['selectedName']) {
            var message = app.vtranslate('OVERWRITE_EXISTING_MSG1') + app.vtranslate('SINGLE_' + data['source_module']) + ' (' + data['selectedName'] + ') ' + app.vtranslate('OVERWRITE_EXISTING_MSG2');
            app.helper.showConfirmationBox({'message': message}).then(
                    function (e) {
                        self.copyAddressDetails(data, container);
                    },
                    function (error, err) {
                    });
        }
    },

    registerPopoverCancelEvent: function () {
        this.getForm().on('click', '.popover .popoverCancel', function (e) {
            e.preventDefault();
            var element = jQuery(e.currentTarget);
            var popOverEle = element.closest('.popover');
            var validate = popOverEle.find('input').valid();
            if (!validate) {
                popOverEle.find('.input-error').val(0).valid();
            }
            popOverEle.css('opacity', 0).css('z-index', '-1');

        });
    },
    registerBasicEvents: function (container) {
        this._super(container);
        this.registerAddProductService();        
        this.checkLineItemRow();
        this.registerSubmitEvent();
        this.makeLineItemsSortable();
        this.registerLineItemAutoComplete();
        this.registerReferenceSelectionEvent(this.getForm());
        this.registerPopoverCancelEvent();
    },

    saveArticlesCount: function () {
        console.log(this.lineItemsHolder.find('tr.' + this.lineItemDetectingClass).length);
        jQuery('#totalArticlesCount').val(this.lineItemsHolder.find('tr.' + this.lineItemDetectingClass).length);
    },

});


