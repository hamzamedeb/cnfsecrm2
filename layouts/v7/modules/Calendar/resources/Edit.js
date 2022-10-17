/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("Calendar_Edit_Js", {

    uploadAndParse: function () {
        if (Vtiger_Import_Js.validateFilePath()) {
            var form = jQuery("form[name='importBasic']"); 
            jQuery('[name="mode"]').val('importResult');
            var data = new FormData(form[0]);
            var postParams = {
                data: data,
                contentType: false,
                processData: false
            };
            app.helper.showProgress();
            app.request.post(postParams).then(function (err, response) {
                app.helper.loadPageContentOverlay(response);
                app.helper.hideProgress();
            });
        }
        return false;
    },

    handleFileTypeChange: function () {
        var fileType = jQuery('[name="type"]').filter(':checked').val();
        var currentPage = jQuery('#group2');
        var selectedRecords = jQuery('#group1');

        if (fileType == 'ics') {
            currentPage.prop('disabled', true).prop('checked', false);
            selectedRecords.prop('disabled', true).prop('checked', false);
            jQuery('#group3').prop('checked', true);
        } else {
            currentPage.removeAttr('disabled');
            if (jQuery('.isSelectedRecords').val() == 1) {
                selectedRecords.removeAttr('disabled');
            }
        }
    },

    userChangedTimeDiff: false

}, {

    relatedContactElement: false,

    recurringEditConfirmation: false,

    getRelatedContactElement: function (form) {
        if (typeof form == "undefined") {
            form = this.getForm();
        }
        this.relatedContactElement = jQuery('#contact_id_display', form);
        return this.relatedContactElement;
    },

    /* uni-cnfsecrm - debut financeur */
    lineItemDetectingClassFinanceur: 'lineItemRowFinanceur',
    basicRowFinanceur: false,
    lineItemContentsContainerFinanceur: false,
    rowClassFinanceur: 'lineItemRowFinanceur',
    rowSequenceHolderFinanceur: false,
    dummyLineItemRowFinanceur: false,
    lineItemsHolderFinanceur: false,
    numOfLineItemsFinanceur: false,

    /* uni-cnfsecrm - fin financeur */

    /* uni-cnfsecrm - debut init date */
    lineItemDetectingClassDate: 'lineItemRowDate',
    basicRowDate: false,
    lineItemContentsContainerDate: false,
    rowClassDate: 'lineItemRowDate',
    rowSequenceHolderDate: false,
    dummyLineItemRowDate: false,
    lineItemsHolderDate: false,
    numOfLineItemsDate: false,
    /* uni-cnfsecrm - fin date */

    /* uni-cnfsecrm - debut init Apprenant */
    lineItemDetectingClassApprenant: 'lineItemRowApprenant',
    basicRowApprenant: false,
    lineItemContentsContainerApprenant: false,
    rowClassApprenant: 'lineItemRowApprenant',
    rowSequenceHolderApprenant: false,
    dummyLineItemRowApprenant: false,
    lineItemsHolderApprenant: false,
    numOfLineItemsApprenant: false,
    /* uni-cnfsecrm - fin Apprenant */


    /* uni-cnfsecrm - debut */
    /* uni-cnfsecrm - debut */
    initializeVariablesFinanceur: function () {
        this.dummyLineItemRowFinanceur = jQuery('#rowfinanceur0');
        //console.log(this.dummyLineItemRowFinanceur);
        this.lineItemsHolderFinanceur = jQuery('#lineItemTabFinanceur');
        this.numOfLineItemsFinanceur = this.lineItemsHolderFinanceur.find('.' + this.lineItemDetectingClassFinanceur).length;
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
    },

    registerAddingNewFinanceur: function () {
        var thisInstance = this;
        var lineItemTableFinanceur = this.getLineItemContentsContainerFinanceur();
        jQuery('.addFinanceur').on('click', function () {
            var newRow = thisInstance.getBasicRowFinanceur().addClass(thisInstance.rowClassFinanceur);
            var sequenceNumber = thisInstance.getNextLineItemRowNumberFinanceur();
            var id_bouton = this.id;
            newRow = newRow.appendTo(jQuery('#lineItemTabFinanceur'));
            thisInstance.checkLineItemRowFinanceur();
            newRow.find('input.rowNumberFinanceurs').val(sequenceNumber);
            thisInstance.updateRowNumberForRowFinanceur(newRow, sequenceNumber);
//            jQuery('.dateFieldTempFinanceur', newRow).addClass('dateFieldFinanceur');
//            jQuery('.dateFieldFinanceur', newRow).removeClass('dateFieldTempFinanceur');
            //console.log('test add finaceur');

        });
    },

    getLineItemContentsContainerFinanceur: function () {
        if (this.lineItemContentsContainerFinanceur == false) {
            this.setLineItemContainerFinanceur(jQuery('#lineItemTabglobalFinanceur'));
        }
        return this.lineItemContentsContainerFinanceur;
    },

    setLineItemContainerFinanceur: function (element) {
        this.lineItemContentsContainerFinanceur = element;
        return this;
    },

    getNextLineItemRowNumberFinanceur: function () {
        if (this.rowSequenceHolderFinanceur == false) {
            this.loadRowSequenceNumberFinanceur();
        }
        return ++this.rowSequenceHolderFinanceur;
    },

    loadRowSequenceNumberFinanceur: function () {
        if (this.rowSequenceHolderFinanceur == false) {
            this.rowSequenceHolderFinanceur = jQuery('.' + this.rowClassFinanceur, this.getLineItemContentsContainerFinanceur()).length;
        }
        return this;
    },

    checkLineItemRowFinanceur: function () {
        var lineItemTableFinanceur = this.getLineItemContentsContainerFinanceur();
        var noRow = lineItemTableFinanceur.find('.lineItemRowFinanceur').length;
        if (noRow > 1) {
            this.showLineItemsDeleteIconFinanceur();
        } else {
            this.hideLineItemsDeleteIconFinanceur();
        }
    },

    showLineItemsDeleteIconFinanceur: function () {
        var lineItemTableFinanceur = this.getLineItemContentsContainerFinanceur();
        lineItemTableFinanceur.find('.deleteRowFinanceur').show();
    },

    updateRowNumberForRowFinanceur: function (lineItemRowFinanceur, expectedSequenceNumber, currentSequenceNumber) {
        if (typeof currentSequenceNumber == 'undefined') {
            //by default there will zero current sequence number
            currentSequenceNumber = 0;
        }

        var idFields = new Array('vendorid', 'vendorname', 'pourcentage', 'montant', 'tva', 'ttc', 'adresse', 'code_postal', 'identite', 'ville', 'telephone');

        var nameFields = new Array('');
        var expectedRowId = 'row' + expectedSequenceNumber;
        for (var idIndex in idFields) {
            var elementId = idFields[idIndex];
            var actualElementId = elementId + currentSequenceNumber;
            var expectedElementId = elementId + expectedSequenceNumber;
            lineItemRowFinanceur.find('#' + actualElementId).attr('id', expectedElementId)
                    .filter('[name="' + actualElementId + '"]').attr('name', expectedElementId);
        }

        for (var nameIndex in nameFields) {
            var elementName = nameFields[nameIndex];
            var actualElementName = elementName + currentSequenceNumber;
            var expectedElementName = elementName + expectedSequenceNumber;
            lineItemRowFinanceur.find('[name="' + actualElementName + '"]').attr('name', expectedElementName);
        }

        var parent_table = lineItemRowFinanceur.closest('table');
        var id_list = parent_table.find('.listobj_id').val();
        lineItemRowFinanceur.find('.listobj_num').val(id_list);

        return lineItemRowFinanceur.attr('id', expectedRowId);
    },

    updateLineItemElementByOrderFinanceur: function () {
        var self = this;
        var checkedDiscountElements = {};
        var lineItems = this.lineItemsHolderFinanceur.find('tr.' + this.lineItemDetectingClassFinanceur);
        lineItems.each(function (index, domElement) {
            var lineItemRow = jQuery(domElement);
            var actualRowId = lineItemRow.attr('id');
        });

        lineItems.each(function (index, domElement) {
            var lineItemRow = jQuery(domElement);
            var expectedRowIndex = (index + 1);
            var expectedRowId = 'rowfinanceur' + expectedRowIndex;
            var actualRowId = lineItemRow.attr('id');
            if (expectedRowId != actualRowId) {
                var actualIdComponents = actualRowId.split('rowfinanceur');
                self.updateRowNumberForRowFinanceur(lineItemRow, expectedRowIndex, actualIdComponents[1]);
            }
        });
    },

    registerDeleteLineItemEventFinanceur: function () {
        var thisInstance = this;
        var lineItemTableFinanceur = this.getLineItemContentsContainerFinanceur();
        lineItemTableFinanceur.on('click', '.deleteRowFinanceur', function (e) {
            var element = jQuery(e.currentTarget);
            element.closest('tr.' + thisInstance.rowClassFinanceur).remove();
            thisInstance.checkLineItemRowFinanceur();
        });
    },

    makeLineItemsSortableFinanceur: function () {
        var thisInstance = this;
        var lineItemTableFinanceur = this.getLineItemContentsContainerFinanceur();
        lineItemTableFinanceur.sortable({
            'containment': lineItemTableFinanceur,
            'items': 'tr.' + this.rowClassFinanceur,
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
            thisInstance.getClosestLineItemRowFinanceur(jQuery(event.target)).find('input:focus').trigger('focusout');
        });
    },

    getClosestLineItemRowFinanceur: function (element) {
        return element.closest('tr.' + this.rowClassFinanceur);
    },

    updateLineFinanceur: function (lineItemRowFinanceur, expectedSequenceNumber, currentSequenceNumber) {
        if (typeof currentSequenceNumber == 'undefined') {
            //by default there will zero current sequence number
            currentSequenceNumber = 0;
        }
        if (typeupdate != 'notempty')
        {

            jQuery('input.montant', lineItemRowFinanceur).val('');
            jQuery('input.tva', lineItemRowFinanceur).val('');
            jQuery('input.ttc', lineItemRowFinanceur).val('');
            jQuery('input.adresse', lineItemRowFinanceur).val('');
            jQuery('input.code_postal', lineItemRowFinanceur).val('');
            jQuery('input.identite', lineItemRowFinanceur).val('');
            jQuery('input.ville', lineItemRowFinanceur).val('');
            jQuery('input.telephone', lineItemRowFinanceur).val('');
        }
    },

    getBasicRowFinanceur: function () {
        if (this.basicRowFinanceur == false) {
            var lineItemTableFinanceur = this.getLineItemContentsContainerFinanceur();
            this.basicRowFinanceur = jQuery('.lineItemCloneCopyFinanceur', lineItemTableFinanceur)
        }
        var newRow = this.basicRowFinanceur.clone(true, true);
        return newRow.removeClass('hide lineItemCloneCopyFinanceur');
    },

    hideLineItemsDeleteIconFinanceur: function () {
        var lineItemTableFinanceur = this.getLineItemContentsContainerFinanceur();
        lineItemTableFinanceur.find('.deleteRowFinanceur').hide();
    },

    registerLineItemAutoCompleteFinanceur: function (container) {
        var self = this;
        if (typeof container == 'undefined') {
            container = this.lineItemsHolderFinanceur;
        }
        container.find('input.autoCompleteFinanceur').autocomplete({
            'minLength': '3',
            'source': function (request, response) {
                //element will be array of dom elements
                //here this refers to auto complete instance
                var inputElement = jQuery(this.element[0]);
                var tdElement = inputElement.closest('td');
                var searchValue = request.term;
                var params = {};
                var searchModule = tdElement.find('.lineItemPopupFinanceur').data('moduleName');
                params.search_module = searchModule
                params.search_value = searchValue;
                self.searchModuleNames(params).then(function (data) {
                    var reponseDataList = new Array();
                    var serverDataFormat = data;
                    if (serverDataFormat.length <= 0) {
                        serverDataFormat = new Array({
                            'label': app.vtranslate('JS_NO_RESULTS_FOUND'),
                            'type': 'no results'
                        });
                    }
                    for (var id in serverDataFormat) {
                        var responseData = serverDataFormat[id];
                        reponseDataList.push(responseData);
                    }
                    response(reponseDataList);
                });
            },
            'select': function (event, ui) {
                var selectedItemData = ui.item;
                //To stop selection if no results is selected
                if (typeof selectedItemData.type != 'undefined' && selectedItemData.type == "no results") {
                    return false;
                }
                var element = jQuery(this);
                element.attr('disabled', 'disabled');
                var tdElement = element.closest('td');
                var selectedModule = tdElement.find('.lineItemPopupFinanceur').data('moduleName');
                var popupElement = tdElement.find('.lineItemPopupFinanceur');
                var dataUrl = "index.php?module=Inventory&action=GetFinanceur&record=" + selectedItemData.id + "&currency_id=" + jQuery('#currency_id option:selected').val() + "&sourceModule=" + app.getModuleName();
                app.request.get({'url': dataUrl}).then(
                        function (error, data) {
                            if (error == null) {
                                var itemRow = self.getClosestLineItemRowFinanceur(element)
                                itemRow.find('.lineItemTypeFinanceur').val(selectedModule);
                                console.log("finan01");
                                self.mapResultsToFieldsFinanceur(itemRow, data);
                            }
                        },
                        function (error, err) {

                        }
                );
            },
            'change': function (event, ui) {
                var element = jQuery(this);
                //if you dont have disabled attribute means the user didnt select the item
                if (element.attr('disabled') == undefined) {
                    element.closest('td').find('.clearLineItemFinanceur').trigger('click');
                }
            }
//		}).each(function() {
//			jQuery(this).data('autocomplete')._renderItem = function(ul, item) {
//				var term = this.element.val();
//				var regex = new RegExp('('+term+')', 'gi');
//				var htmlContent = item.label.replace(regex, '<b>$&</b>');
//				return jQuery('<li></li>').data('item.autocomplete', item).append(jQuery('<a></a>').html(htmlContent)).appendTo(ul);
//			};
        });
    },

    getLineItemSetypeFinanceur: function (row) {
        return row.find('.lineItemTypeFinanceur').val();
    },

    showLineItemPopupFinanceur: function (callerParams) {
        var params = {
            'module': this.getModuleName(),
            'multi_select': true,
        };

        params = jQuery.extend(params, callerParams);
        var popupInstance = Vtiger_Popup_Js.getInstance();
        popupInstance.showPopup(params, 'post.LineItemPopupSelection.click');

    },

    registerProductAndServiceSelectorFinanceur: function () {
        var self = this;

        this.lineItemsHolderFinanceur.on('click', '.lineItemPopupFinanceur', function (e) {
            var triggerer = jQuery(e.currentTarget);
            self.showLineItemPopupFinanceur({'view': triggerer.data('popup')});
            var popupReferenceModuleFinanceur = triggerer.data('moduleName');
            var postPopupHandler = function (e, data) {
                console.log(data);
                data = JSON.parse(data);
                if (!$.isArray(data)) {
                    data = [data];
                }
                self.postLineItemSelectionActionsFinanceur(triggerer.closest('tr'), data, popupReferenceModuleFinanceur);
            }
            app.event.off('post.LineItemPopupSelection.click');
            app.event.one('post.LineItemPopupSelection.click', postPopupHandler);
        });
    },

    postLineItemSelectionActionsFinanceur: function (itemRow, selectedLineItemsDataFinanceur, lineItemSelectedModuleNameFinanceur) {
        for (var index in selectedLineItemsDataFinanceur) {
            if (index != 0) {
                if (lineItemSelectedModuleNameFinanceur == 'Vendors') {
                    jQuery('#addFinanceur').trigger('click', selectedLineItemsDataFinanceur[index]);
                }
            } else {
                itemRow.find('.lineItemTypeFinanceur').val(lineItemSelectedModuleNameFinanceur);
                console.log("finan04");
                this.mapResultsToFieldsFinanceur(itemRow, selectedLineItemsDataFinanceur[index]);
            }
        }
    },

    mapResultsToFieldsFinanceur: function (parentRow, responseData) {
        var lineItemNameElment = jQuery('input.vendorname', parentRow);
        var referenceModule = this.getLineItemSetypeFinanceur(parentRow);
        var lineItemRowNumber = parentRow.data('rowNum');
        for (var id in responseData) {
            var recordId = id;
            var recordData = responseData[id];
            console.log(recordData);
            var vendorid = recordData.vendorid;
            var vendorname = recordData.vendorname;
            var phone = recordData.phone;
            var postalcode = recordData.postalcode;
            var street = recordData.street;
            var city = recordData.city;
            var selectedName = recordData.name;
            jQuery('input.selectedModuleId', parentRow).val(recordId);
            jQuery('input.lineItemType', parentRow).val(referenceModule);
            lineItemNameElment.val(selectedName);
            lineItemNameElment.attr('disabled', 'disabled');
            jQuery('input.vendorid', parentRow).val(vendorid);
            jQuery('input.vendorname', parentRow).val(vendorname);
            jQuery('input.adresse', parentRow).val(street);
            jQuery('input.code_postal', parentRow).val(postalcode);
            jQuery('input.ville', parentRow).val(city);
            jQuery('input.telephone', parentRow).val(phone);
        }
        jQuery('.montant', parentRow).trigger('focusout');
    },

    clearLineItemDetailsFinanceur: function (parentElem) {
        var lineItemRow = this.getClosestLineItemRow(parentElem);
//        jQuery('[id*="purchaseCost"]', lineItemRow).val('0');
//        jQuery('.lineItemImage', lineItemRow).html('');
//        jQuery('input.selectedModuleId', lineItemRow).val('');
//        jQuery('input.listPrice', lineItemRow).val('0');
//        jQuery('.lineItemCommentBox', lineItemRow).val('');
//        jQuery('.subProductIds', lineItemRow).val('');
//        jQuery('.subProductsContainer', lineItemRow).html('');
//        this.quantityChangeActions(lineItemRow);
    },

    saveFinanceurCount: function () {
        jQuery('#totalFinanceurCount').val(this.lineItemsHolderFinanceur.find('tr.' + this.lineItemDetectingClassFinanceur).length);
    },

    registerClearLineItemSelectionFinanceur: function () {
        var self = this;
        this.lineItemsHolderFinanceur.on('click', '.clearLineItemFinanceur', function (e) {
            console.log("registerClearLineItemSelectionFinanceur")
            var elem = jQuery(e.currentTarget);
            var parentElem = elem.closest('td');
            self.clearLineItemDetailsFinanceur(parentElem);
            parentElem.find('input.vendorname').removeAttr('disabled').val('');
            e.preventDefault();
        });
    },

    /* uni-cnfsecrm - date */

    initializeVariablesDate: function () {
        this.dummyLineItemRowDate = jQuery('#rowdate0');
        //console.log(this.dummyLineItemRowDate);
        this.lineItemsHolderDate = jQuery('#lineItemTabDate');
        this.numOfLineItemsDate = this.lineItemsHolderDate.find('.' + this.lineItemDetectingClassDate).length;
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
    },

    getLineItemContentsContainerDate: function () {
        if (this.lineItemContentsContainerDate == false) {
            this.setLineItemContainerDate(jQuery('#lineItemTabglobalDate'));
        }
        return this.lineItemContentsContainerDate;
    },

    setLineItemContainerDate: function (element) {
        this.lineItemContentsContainerDate = element;
        return this;
    },

    getNextLineItemRowNumberDate: function () {
        if (this.rowSequenceHolderDate == false) {
            this.loadRowSequenceNumberDate();
        }
        return ++this.rowSequenceHolderDate;
    },

    loadRowSequenceNumberDate: function () {
        if (this.rowSequenceHolderDate == false) {
            console.log(jQuery('.' + this.rowClassDate, this.getLineItemContentsContainerDate()).length);
            this.rowSequenceHolderDate = jQuery('.' + this.rowClassDate, this.getLineItemContentsContainerDate()).length;
        }
        return this;
    },

    checkLineItemRowDate: function () {
        console.log("checkLineItemRowDate");
        var lineItemTableDate = this.getLineItemContentsContainerDate();
        var noRow = lineItemTableDate.find('.lineItemRowDate').length;
        console.log("noRow" + noRow);
        if (noRow > 1) {
            this.showLineItemsDeleteIconDate();
        } else {
            this.hideLineItemsDeleteIconDate();
        }
    },

    showLineItemsDeleteIconDate: function () {
        var lineItemTableDate = this.getLineItemContentsContainerDate();
        lineItemTableDate.find('.deleteRowDate').show();
    },

    updateLineItemsElementWithSequenceNumberDate: function (lineItemRowDate, expectedSequenceNumber, currentSequenceNumber, typeupdate) {
        if (typeof currentSequenceNumber == 'undefined') {
            //by default there will zero current sequence number
            currentSequenceNumber = 0;
        }
        var d = new Date();
        var datestart = d.getDate() + "-" + (d.getMonth() + 1) + "-" + d.getFullYear();
        /* uni_cnfsecrm - v2 - modif 186 - DEBUT */
        var time_start_session = this.fixTimeStartSessionByPlace();
        /* uni_cnfsecrm - v2 - modif 186 - FIN */
        jQuery('input.date_start', lineItemRowDate).val(datestart);
        /* uni_cnfsecrm - v2 - modif 186 - DEBUT */
        jQuery('input.start_matin', lineItemRowDate).val(time_start_session);
        /* uni_cnfsecrm - v2 - modif 186 - FIN */
        jQuery('input.end_matin', lineItemRowDate).val('12:30');
        jQuery('input.start_apresmidi', lineItemRowDate).val('13:30');
        jQuery('input.end_apresmidi', lineItemRowDate).val('17:30');
        jQuery('input.duree_formation', lineItemRowDate).val('7:00');

        var idFields = new Array('date_start', 'start_matin', 'end_matin', 'start_apresmidi', 'end_apresmidi', 'duree_formation', 'lineItemType');

        var nameFields = new Array('');
        var classFields = new Array('taxPercentage');
        //To handle variable tax ids
        for (var classIndex in classFields) {
            var className = classFields[classIndex];
            jQuery('.' + className, lineItemRowDate).each(function (index, domElement) {
                var idString = domElement.id
                //remove last character which will be the row number
                idFields.push(idString.slice(0, (idString.length - 1)));
            });
        }

        var expectedRowId = 'row' + expectedSequenceNumber;
        for (var idIndex in idFields) {
            var elementId = idFields[idIndex];
            var actualElementId = elementId + currentSequenceNumber;
            var expectedElementId = elementId + expectedSequenceNumber;
            lineItemRowDate.find('#' + actualElementId).attr('id', expectedElementId)
                    .filter('[name="' + actualElementId + '"]').attr('name', expectedElementId);
        }

        for (var nameIndex in nameFields) {
            var elementName = nameFields[nameIndex];
            var actualElementName = elementName + currentSequenceNumber;
            var expectedElementName = elementName + expectedSequenceNumber;
            lineItemRowDate.find('[name="' + actualElementName + '"]').attr('name', expectedElementName);
        }
        lineItemRowDate.attr('id', expectedRowId).attr('data-row-num', expectedSequenceNumber);

        return lineItemRowDate.attr('id', expectedRowId);
    },
    
        /* uni_cnfsecrm - v2 - modif 186 - DEBUT */
    fixTimeStartSessionByPlace: function () {
        var time_start_session = "09:00";
        var lieu_id = jQuery("input[name='lieu']").val();
        if (lieu_id == 11827) {
            time_start_session = "09:00";
        } else {
            time_start_session = "09:30";
        }
        $("#lineItemTabDate tbody tr").each(function () {
            var start_matin_val = $(this).find("input.start_matin").val();
            if (start_matin_val != undefined) {
                $(this).find("input.start_matin").val(time_start_session);
            }
        });
        return time_start_session;
    },
    /* uni_cnfsecrm - v2 - modif 186 - FIN */

    registerDeleteLineItemEventDate: function () {
        var thisInstance = this;
        var lineItemTableDate = this.getLineItemContentsContainerDate();
        lineItemTableDate.on('click', '.deleteRowDate', function (e) {
            var element = jQuery(e.currentTarget);

            element.closest('tr.' + thisInstance.rowClassDate).remove();
            thisInstance.checkLineItemRowDate();
            /* uni_cnfsecrm - v2 - modif 126 - DEBUT */
            thisInstance.setDateSession();
            /* uni_cnfsecrm - v2 - modif 126 - FIN */
        });

    },

    makeLineItemsSortableDate: function () {
        var thisInstance = this;
        var lineItemTableDate = this.getLineItemContentsContainerDate();
        lineItemTableDate.sortable({
            'containment': lineItemTableDate,
            'items': 'tr.' + this.rowClassDate,
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
            thisInstance.getClosestLineItemRowDate(jQuery(event.target)).find('input:focus').trigger('focusout');
        });
    },

    getClosestLineItemRowDate: function (element) {
        return element.closest('tr.' + this.rowClassDate);
    },

    updateLineDate: function (lineItemRowDate, expectedSequenceNumber, currentSequenceNumber, typeupdate) {
        if (typeof currentSequenceNumber == 'undefined') {
            //by default there will zero current sequence number
            currentSequenceNumber = 0;
        }
        if (typeupdate != 'notempty')
        {
            var d = new Date();
            var datestart = d.getDate() + "-" + (d.getMonth() + 1) + "-" + d.getFullYear();

            jQuery('input.date_start', lineItemRowDate).val(datestart);
            jQuery('input.start_matin', lineItemRowDate).val('09:30');
            jQuery('input.end_matin', lineItemRowDate).val('12:30');
            jQuery('input.start_apresmidi', lineItemRowDate).val('13:30');
            jQuery('input.end_apresmidi', lineItemRowDate).val('17:30');
            jQuery('input.duree_formation', lineItemRowDate).val('7');
        }
    },

    getBasicRowDate: function () {
        if (this.basicRowDate == false) {
            var lineItemTableDate = this.getLineItemContentsContainerDate();
            this.basicRowDate = jQuery('.lineItemCloneCopyDates', lineItemTableDate)
        }
        var newRow = this.basicRowDate.clone(true, true);
        console.log(newRow);
        return newRow.removeClass('hide lineItemCloneCopyDates');
    },

    hideLineItemsDeleteIconDate: function () {
        var lineItemTableDate = this.getLineItemContentsContainerDate();
        lineItemTableDate.find('.deleteRowDate').hide();
    },

    registerLineItemAutoCompleteDate: function (container) {
        var self = this;
        if (typeof container == 'undefined') {
            container = this.lineItemsHolderDate;
        }
        container.find('input.autoCompleteDate').autocomplete({
            'minLength': '3',
            'source': function (request, response) {
                //element will be array of dom elements
                //here this refers to auto complete instance
                var inputElement = jQuery(this.element[0]);
                var tdElement = inputElement.closest('td');
                var searchValue = request.term;
                var params = {};
                var searchModule = tdElement.find('.lineItemPopupDate').data('moduleName');
                params.search_module = searchModule
                params.search_value = searchValue;
                self.searchModuleNames(params).then(function (data) {
                    var reponseDataList = new Array();
                    var serverDataFormat = data;
                    if (serverDataFormat.length <= 0) {
                        serverDataFormat = new Array({
                            'label': app.vtranslate('JS_NO_RESULTS_FOUND'),
                            'type': 'no results'
                        });
                    }
                    for (var id in serverDataFormat) {
                        var responseData = serverDataFormat[id];
                        reponseDataList.push(responseData);
                    }
                    response(reponseDataList);
                });
            },
            'select': function (event, ui) {
                var selectedItemData = ui.item;
                //To stop selection if no results is selected
                if (typeof selectedItemData.type != 'undefined' && selectedItemData.type == "no results") {
                    return false;
                }
                var element = jQuery(this);
                element.attr('disabled', 'disabled');
                var tdElement = element.closest('td');
                var selectedModule = tdElement.find('.lineItemPopupDate').data('moduleName');
                var popupElement = tdElement.find('.lineItemPopupDate');
                var dataUrl = "index.php?module=Inventory&action=GetTaxes&record=" + selectedItemData.id + "&currency_id=" + jQuery('#currency_id option:selected').val() + "&sourceModule=" + app.getModuleName();
                app.request.get({'url': dataUrl}).then(
                        function (error, data) {
                            if (error == null) {
                                var itemRow = self.getClosestLineItemRow(element)
                                itemRow.find('.lineItemTypeDate').val(selectedModule);
                                self.mapResultsToFields(itemRow, data[0]);
                            }
                        },
                        function (error, err) {

                        }
                );
            },
            'change': function (event, ui) {
                var element = jQuery(this);
                //if you dont have disabled attribute means the user didnt select the item
                if (element.attr('disabled') == undefined) {
                    element.closest('td').find('.clearLineItemDate').trigger('click');
                }
            }
//		}).each(function() {
//			jQuery(this).data('autocomplete')._renderItem = function(ul, item) {
//				var term = this.element.val();
//				var regex = new RegExp('('+term+')', 'gi');
//				var htmlContent = item.label.replace(regex, '<b>$&</b>');
//				return jQuery('<li></li>').data('item.autocomplete', item).append(jQuery('<a></a>').html(htmlContent)).appendTo(ul);
//			};
        });
    },

    showLineItemPopupDate: function (callerParams) {
        var params = {
            'module': this.getModuleName(),
            'multi_select': true,
            'currency_id': this.currencyElement.val()
        };

        params = jQuery.extend(params, callerParams);
        var popupInstance = Vtiger_Popup_Js.getInstance();
        popupInstance.showPopup(params, 'post.LineItemPopupSelection.click');

    },

    saveDateCount: function () {
        jQuery('#totalDatesCount').val(this.lineItemsHolderDate.find('tr.' + this.lineItemDetectingClassDate).length);
    },

    registerProductAndServiceSelectorDate: function () {
        var self = this;

        this.lineItemsHolderDate.on('click', '.lineItemPopupDate', function (e) {
            var triggerer = jQuery(e.currentTarget);
            self.showLineItemPopupDate({'view': triggerer.data('popup')});
            var popupReferenceModuleDate = triggerer.data('moduleName');
            var postPopupHandler = function (e, data) {
                data = JSON.parse(data);
                if (!$.isArray(data)) {
                    data = [data];
                }
                self.postLineItemSelectionActionsDate(triggerer.closest('tr'), data, popupReferenceModuleDate);
            }
            app.event.off('post.LineItemPopupSelection.click');
            app.event.one('post.LineItemPopupSelection.click', postPopupHandler);
        });
    },

    postLineItemSelectionActionsDate: function (itemRow, selectedLineItemsDataDate, lineItemSelectedModuleNameDate) {
        for (var index in selectedLineItemsDataDate) {
            if (index != 0) {
                if (lineItemSelectedModuleNameDate == 'Vendors') {
                    jQuery('#addDate').trigger('click', selectedLineItemsDataDate[index]);
                }
            } else {
                itemRow.find('.lineItemTypeDate').val(lineItemSelectedModuleNameDate);
                this.mapResultsToFields(itemRow, selectedLineItemsDataDate[index]);
            }
        }
    },

    registerAddingNewDate: function () {
        console.log("registerAddingNewDate");
        var thisInstance = this;
        var lineItemTableDate = this.getLineItemContentsContainerDate();
        jQuery('.addDate').on('click', function () {
            var newRow = thisInstance.getBasicRowDate().addClass(thisInstance.rowClassDate);
            var sequenceNumber = thisInstance.getNextLineItemRowNumberDate();
            var id_bouton = this.id;
            var id_list = id_bouton.replace('addDate', '');
            newRow = newRow.appendTo(jQuery('#lineItemTabDate' + id_list));
            thisInstance.checkLineItemRowDate();
            newRow.find('input.rowNumberDates').val(sequenceNumber);
            thisInstance.updateLineItemsElementWithSequenceNumberDate(newRow, sequenceNumber);
            jQuery('.dateFieldTemp', newRow).addClass('dateField');
            jQuery('.dateField', newRow).removeClass('dateFieldTemp');
            vtUtils.registerEventForDatePickerFields(jQuery('.dateField', newRow), true);
            /* uni_cnfsecrm - v2 - modif 126 - DEBUT */
            thisInstance.setDateSession();
            /* uni_cnfsecrm - v2 - modif 126 - FIN */
            /* uni_cnfsecrm - v2 - modif 168 - DEBUT */
            thisInstance.updateRowNumberForRowDate(newRow, sequenceNumber);
            /* uni_cnfsecrm - v2 - modif 168 - FIN */
        });
    },

    /* uni-cnfsecrm - fin date */

    /* uni-cnfsecrm - Debut Apprenants */
    /* uni-cnfsecrm - Debut Apprenants */
    initializeVariablesApprenant: function () {
        this.dummyLineItemRowApprenant = jQuery('#rowApprenant0');
        //console.log(this.dummyLineItemRowApprenant);
        this.lineItemsHolderApprenant = jQuery('#lineItemTabApprenant');
        this.numOfLineItemsApprenant = this.lineItemsHolderApprenant.find('.' + this.lineItemDetectingClassApprenant).length;
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

//        this.numOfCurrencyDecimals = parseInt(jQuery('.numberOfCurrencyDecimal').val());
//        this.taxTypeElement = jQuery('#taxtype');
//        this.regionElement = jQuery('#region_id');
//        this.currencyElement = jQuery('#currency_id');
//
//        this.netTotalEle = jQuery('#netTotal');
//        this.finalDiscountTotalEle = jQuery('#discountTotal_final');
//        this.finalTaxEle = jQuery('#tax_final');
//        this.finalDiscountUIEle = jQuery('#finalDiscountUI');
//        this.finalDiscountEle = jQuery('#finalDiscount');
//        this.conversionRateEle = jQuery('#conversion_rate');
//        this.overAllDiscountEle = jQuery('#overallDiscount');
//        this.chargesTotalEle = jQuery('#chargesTotal');
//        this.preTaxTotalEle = jQuery('#preTaxTotal');
//        this.chargesContainer = jQuery('#chargesBlock')
//        this.chargesTotalDisplay = jQuery('#chargesTotalDisplay');
//        this.chargeTaxesContainer = jQuery('#chargeTaxesBlock');
//        this.chargeTaxesTotal = jQuery('#chargeTaxTotalHidden');
//        this.deductTaxesTotal = jQuery('#deductTaxesTotalAmount');
//        this.adjustmentEle = jQuery('#adjustment');
//        this.adjustmentTypeEles = jQuery('input[name="adjustmentType"]');
//        this.grandTotal = jQuery('#grandTotal');
//        this.groupTaxContainer = jQuery('#group_tax_div');
//        this.dedutTaxesContainer = jQuery('#deductTaxesBlock');

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

    registerAddingNewApprenant: function () {
        var self = this;
        var addLineItemEventHandler = function (e, data) {
            var currentTarget = jQuery(e.currentTarget);
            var params = {'currentTarget': currentTarget}
            console.log(params);
            var newLineItem = self.getNewLineItemApprenant(params);
            var sequenceNumber = self.getNextLineItemRowNumberApprenant();
            console.log("sequence " + sequenceNumber);
            newLineItem = newLineItem.appendTo(self.lineItemsHolderApprenant);
            newLineItem.find('input.contactName').addClass('autoCompleteApprenant');
            newLineItem.find('.ignore-ui-registration').removeClass('ignore-ui-registration');
            vtUtils.applyFieldElementsView(newLineItem);
            app.event.trigger('post.lineItem.New', newLineItem);
            self.checkLineItemRowApprenant();
            newLineItem.find('input.rowNumberApprenant').val(sequenceNumber);
            //      self.updateRowNumberForRowApprenant(newLineItem, sequenceNumber);
            self.registerLineItemAutoCompleteApprenant(newLineItem);
            if (typeof data != "undefined") {
                self.mapResultsToFieldsApprenant(newLineItem, data);
            }
        }
        jQuery('#addApprenant').on('click', addLineItemEventHandler);
    },

    registerAddProductService: function () {
        var self = this;
        var addLineItemEventHandler = function (e, data) {
            var currentTarget = jQuery(e.currentTarget);
            var params = {'currentTarget': currentTarget}
            var newLineItem = self.getNewLineItem(params);
            newLineItem = newLineItem.appendTo(self.lineItemsHolder);
            newLineItem.find('input.productName').addClass('autoComplete');
            newLineItem.find('.ignore-ui-registration').removeClass('ignore-ui-registration');
            vtUtils.applyFieldElementsView(newLineItem);
            app.event.trigger('post.lineItem.New', newLineItem);
            self.checkLineItemRow();
            self.registerLineItemAutoComplete(newLineItem);
            if (typeof data != "undefined") {
                console.log("pos03");
                self.mapResultsToFields(newLineItem, data);
            }
        }
        jQuery('#addProduct').on('click', addLineItemEventHandler);
        jQuery('#addService').on('click', addLineItemEventHandler);
    },

    getLineItemContentsContainerApprenant: function () {
        if (this.lineItemContentsContainerApprenant == false) {
            this.setLineItemContainerApprenant(jQuery('#lineItemTabglobalApprenant'));
        }
        return this.lineItemContentsContainerApprenant;
    },

    setLineItemContainerApprenant: function (element) {
        this.lineItemContentsContainerApprenant = element;
        return this;
    },

    getNextLineItemRowNumberApprenant: function () {
        if (this.rowSequenceHolderApprenant == false) {
            this.loadRowSequenceNumberApprenant();
        }
        return ++this.rowSequenceHolderApprenant;
    },

    loadRowSequenceNumberApprenant: function () {
        if (this.rowSequenceHolderApprenant == false) {
            this.rowSequenceHolderApprenant = jQuery('.' + this.rowClassApprenant, this.getLineItemContentsContainerApprenant()).length;
        }
        return this;
    },

    checkLineItemRowApprenant: function () {
        var lineItemTableApprenant = this.getLineItemContentsContainerApprenant();
        var noRow = lineItemTableApprenant.find('.lineItemRowApprenant').length;
        if (noRow > 1) {
            this.showLineItemsDeleteIconApprenant();
        } else {
            this.hideLineItemsDeleteIconApprenant();
        }
    },

    showLineItemsDeleteIconApprenant: function () {
        var lineItemTableApprenant = this.getLineItemContentsContainerApprenant();
        lineItemTableApprenant.find('.deleteRowApprenant').show();
    },

    updateRowNumberForRowApprenant: function (lineItemRowApprenant, expectedSequenceNumber, currentSequenceNumber) {
        if (typeof currentSequenceNumber == 'undefined') {
            //by default there will zero current sequence number
            currentSequenceNumber = 0;
        }

        /* uni_cnfsecrm - modif 104 - DEBUT */
        /* uni_cnfsecrm - correction 104 - DEBUT */
        /* uni_cnfsecrm - v2 - modif 115 - DEBUT */
        /* uni_cnfsecrm - v2 - modif 127 - DEBUT */
        var idFields = new Array('numclient', 'nomclient', 'telephone', 'email', 'contactName', 'etat', 'resultat', 'inscrit', 'apprenantid', 'be_essai', 'be_mesurage', 'be_verification', 'be_manoeuvre', 'he_essai', 'he_mesurage', 'he_verification', 'he_manoeuvre', 'initiale', 'recyclage', 'testprerequis', 'electricien', 'testprerequis_oui', 'electricien_oui', 'testprerequis_non', 'electricien_non'
                , 'b0_h0_h0v_b0', 'b0_h0_h0v_h0v', 'bs_be_he_b0', 'bs_be_he_h0v', 'bs_be_he_bs', 'bs_be_he_manoeuvre',
                'b1v_b2v_bc_br_b0', 'b1v_b2v_bc_br_h0v', 'b1v_b2v_bc_br_bs', 'b1v_b2v_bc_br_manoeuvre',
                'b1v_b2v_bc_br_b1v', 'b1v_b2v_bc_br_b2v', 'b1v_b2v_bc_br_bc', 'b1v_b2v_bc_br_br',
                'b1v_b2v_bc_br_essai', 'b1v_b2v_bc_br_verification', 'b1v_b2v_bc_br_mesurage',
                'b1v_b2v_bc_br_h1v_h2v_b0', 'b1v_b2v_bc_br_h1v_h2v_h0v', 'b1v_b2v_bc_br_h1v_h2v_bs',
                'b1v_b2v_bc_br_h1v_h2v_manoeuvre', 'b1v_b2v_bc_br_h1v_h2v_b1v', 'b1v_b2v_bc_br_h1v_h2v_b2v',
                'b1v_b2v_bc_br_h1v_h2v_bc', 'b1v_b2v_bc_br_h1v_h2v_br', 'b1v_b2v_bc_br_h1v_h2v_essai',
                'b1v_b2v_bc_br_h1v_h2v_verification', 'b1v_b2v_bc_br_h1v_h2v_mesurage',
                'b1v_b2v_bc_br_h1v_h2v_h1v', 'b1v_b2v_bc_br_h1v_h2v_h2v', 'b1v_b2v_bc_br_h1v_h2v_hc',
                'bs_be_he_he', 'b1v_b2v_bc_br_h1v_h2v_he', 'b1v_b2v_bc_br_he', 'date_start_appr',
                'date_fin_appr', 'duree_jour', 'duree_heure');
        /* uni_cnfsecrm - v2 - modif 127 - FIN */
        /* uni_cnfsecrm - correction 104 - DEBUT */
        /* uni_cnfsecrm - modif 104 - FIN */
        /* uni_cnfsecrm - v2 - modif 115 - FIN */
        var nameFields = new Array('');

        var expectedRowId = 'rowApprenant' + expectedSequenceNumber;
        for (var idIndex in idFields) {
            var elementId = idFields[idIndex];
            var actualElementId = elementId + currentSequenceNumber;
            var expectedElementId = elementId + expectedSequenceNumber;
            lineItemRowApprenant.find('#' + actualElementId).attr('id', expectedElementId)
                    .filter('[name="' + actualElementId + '"]').attr('name', expectedElementId);

            if (actualElementId.indexOf("_oui") >= 0 || actualElementId.indexOf("_non") >= 0)
            {
                var actualElementId_after = actualElementId;
                var expectedElementId_after = expectedElementId;
                actualElementId_after = actualElementId_after.replace("_oui", "");
                actualElementId_after = actualElementId_after.replace("_non", "");
                expectedElementId_after = expectedElementId_after.replace("_oui", "");
                expectedElementId_after = expectedElementId_after.replace("_non", "");
                console.log(actualElementId_after);
                console.log(expectedElementId_after);

                console.log('#' + expectedElementId);
                console.log('[name="' + actualElementId_after + '"]');
                console.log('name', expectedElementId_after);
                lineItemRowApprenant.find('#' + expectedElementId).filter('[name="' + actualElementId_after + '"]').attr('name', expectedElementId_after);
            }

        }

        for (var nameIndex in nameFields) {
            var elementName = nameFields[nameIndex];
            var actualElementName = elementName + currentSequenceNumber;
            var expectedElementName = elementName + expectedSequenceNumber;
            lineItemRowApprenant.find('[name="' + actualElementName + '"]').attr('name', expectedElementName);
        }
        console.log("expectedSequenceNumber " + expectedSequenceNumber);
        lineItemRowApprenant.attr('id', expectedRowId).attr('data-row-num', expectedSequenceNumber);

        return lineItemRowApprenant;
    },

    updateLineItemElementByOrderApprenant: function () {
        console.log("updateLineItemElementByOrderApprenant");
        var self = this;
        var checkedDiscountElements = {};
        var lineItems = this.lineItemsHolderApprenant.find('tr.' + this.lineItemDetectingClassApprenant);
        lineItems.each(function (index, domElement) {
            var lineItemRow = jQuery(domElement);
            var actualRowId = lineItemRow.attr('id');
        });

        lineItems.each(function (index, domElement) {
            var lineItemRow = jQuery(domElement);
            var expectedRowIndex = (index + 1);
            var expectedRowId = 'rowApprenant' + expectedRowIndex;
            console.log("expectedRowId" + expectedRowId);
            var actualRowId = lineItemRow.attr('id');
            console.log("actualRowId" + actualRowId);
            if (expectedRowId != actualRowId) {
                var actualIdComponents = actualRowId.split('rowApprenant');
                self.updateRowNumberForRowApprenant(lineItemRow, expectedRowIndex, actualIdComponents[1]);
            }
        });
    },

    registerDeleteLineItemEventApprenant: function () {
        var thisInstance = this;
        var lineItemTableApprenant = this.getLineItemContentsContainerApprenant();

        lineItemTableApprenant.on('click', '.deleteRowApprenant', function (e) {
            var element = jQuery(e.currentTarget);
            //removing the row

            element.closest('tr.' + thisInstance.rowClassApprenant).remove();
            thisInstance.checkLineItemRowApprenant();
        });

    },

    makeLineItemsSortableApprenant: function () {
        var thisInstance = this;
        var lineItemTableApprenant = this.getLineItemContentsContainerApprenant();
        lineItemTableApprenant.sortable({
            'containment': lineItemTableApprenant,
            'items': 'tr.' + this.rowClassApprenant,
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
            thisInstance.getClosestLineItemRowApprenant(jQuery(event.target)).find('input:focus').trigger('focusout');
        });
    },

    getClosestLineItemRowApprenant: function (element) {
        return element.closest('tr.' + this.rowClassApprenant);
    },

    getBasicRowApprenant: function () {
        if (this.basicRowApprenant == false) {
            var lineItemTableApprenant = this.getLineItemContentsContainerApprenant();
            this.basicRowApprenant = jQuery('.lineItemCloneCopyApprenant', lineItemTableApprenant)
        }
        var newRow = this.basicRowApprenant.clone(true, true);
        return newRow.removeClass('hide lineItemCloneCopyApprenant');
    },

    hideLineItemsDeleteIconApprenant: function () {
        var lineItemTableApprenant = this.getLineItemContentsContainerApprenant();
        lineItemTableApprenant.find('.deleteRowApprenant').hide();
    },

    registerLineItemAutoCompleteApprenant: function (container) {
        console.log('test 001')
        var self = this;
        if (typeof container == 'undefined') {
            container = this.lineItemsHolderApprenant;
        }
        container.find('input.autoCompleteApprenant').autocomplete({
            'minLength': '3',
            'source': function (request, response) {
                //element will be array of dom elements
                //here this refers to auto complete instance
                var inputElement = jQuery(this.element[0]);
                console.log(inputElement);
                var tdElement = inputElement.closest('td');
                var searchValue = request.term;
                var params = {};
                console.log('test 002')
                var searchModule = tdElement.find('.lineItemPopupApprenant').data('moduleName');
                params.search_module = searchModule
                params.search_value = searchValue;
                self.searchModuleNames(params).then(function (data) {
                    var reponseDataList = new Array();
                    var serverDataFormat = data;
                    if (serverDataFormat.length <= 0) {
                        serverDataFormat = new Array({
                            'label': app.vtranslate('JS_NO_RESULTS_FOUND'),
                            'type': 'no results'
                        });
                    }
                    for (var id in serverDataFormat) {
                        var responseData = serverDataFormat[id];
                        reponseDataList.push(responseData);
                    }
                    response(reponseDataList);
                });
            },
            'select': function (event, ui) {
                var selectedItemData = ui.item;
                //To stop selection if no results is selected
                if (typeof selectedItemData.type != 'undefined' && selectedItemData.type == "no results") {
                    return false;
                }
                var element = jQuery(this);
                element.attr('disabled', 'disabled');
                var tdElement = element.closest('td');
                console.log('test 003')
                var selectedModuleApprenant = tdElement.find('.lineItemPopupApprenant').data('moduleName');
                var popupElementApprenant = tdElement.find('.lineItemPopupApprenant');
                var dataUrl = "index.php?module=Calendar&action=GetTnfos&record=" + selectedItemData.id + "&sourceModule=" + app.getModuleName();
                app.request.get({'url': dataUrl}).then(
                        function (error, data) {
                            console.log(data);
                            if (error == null) {
                                var itemRow = self.getClosestLineItemRowApprenant(element)
                                itemRow.find('.lineItemTypeApprenant').val(selectedModuleApprenant);
                                self.mapResultsToFieldsApprenant(itemRow, data[0]);
                            }
                        },
                        function (error, err) {

                        }
                );
            },
            'change': function (event, ui) {
                var element = jQuery(this);
                //if you dont have disabled attribute means the user didnt select the item
                if (element.attr('disabled') == undefined) {
                    element.closest('td').find('.clearLineItemApprenant').trigger('click');
                }
            }
//		}).each(function() {
//			jQuery(this).data('autocomplete')._renderItem = function(ul, item) {
//				var term = this.element.val();
//				var regex = new RegExp('('+term+')', 'gi');
//				var htmlContent = item.label.replace(regex, '<b>$&</b>');
//				return jQuery('<li></li>').data('item.autocomplete', item).append(jQuery('<a></a>').html(htmlContent)).appendTo(ul);
//			};
        });
    },

    mapResultsToFieldsApprenant: function (parentRow, responseData) {
        console.log("mapResultsToFieldsApprenant");
        var lineItemNameElmentApprenant = jQuery('input.contactName', parentRow);
        var referenceModuleApprenant = this.getLineItemSetypeApprenant(parentRow);
        var lineItemRowNumber = parentRow.data('rowNum');
        console.log(responseData);
        for (var id in responseData) {
            var recordId = id;
            var recordData = responseData[id];
            console.log(recordData);
            var selectedName = recordData.name;
            console.log(recordData);
            var numclient = recordData.numclient;
            var nomclient = recordData.nomclient;
            var telephone = recordData.telephone;
            var email = recordData.email;
            var apprenantid = recordData.id;

            jQuery('input.lineItemTypeApprenant', parentRow).val(referenceModuleApprenant);
            console.log(selectedName);
            lineItemNameElmentApprenant.val(selectedName);
            lineItemNameElmentApprenant.attr('disabled', 'disabled');
            jQuery('input.numclient', parentRow).val(numclient);
            jQuery('input.nomclient', parentRow).val(nomclient);
            jQuery('input.telephone', parentRow).val(telephone);
            jQuery('input.email', parentRow).val(email);
            jQuery('input.apprenantid', parentRow).val(apprenantid);
        }
    },

    getLineItemSetypeApprenant: function (row) {
        return row.find('.lineItemTypeApprenant').val();
    },

    showLineItemPopupApprenant: function (callerParams) {
        var params = {
            'module': this.getModuleName(),
            'multi_select': true,
            //'currency_id': this.currencyElement.val()
        };

        params = jQuery.extend(params, callerParams);
        var popupInstance = Vtiger_Popup_Js.getInstance();
        popupInstance.showPopup(params, 'post.LineItemPopupSelectionApprenant.click');

    },

    registerApprenant: function () {
        var self = this;

        this.lineItemsHolderApprenant.on('click', '.lineItemPopupApprenant', function (e) {
            var triggerer = jQuery(e.currentTarget);
            self.showLineItemPopupApprenant({'view': triggerer.data('popup')});
            var popupReferenceModuleApprenant = triggerer.data('moduleName');
            var postPopupHandler = function (e, data) {
                data = JSON.parse(data);
                if (!$.isArray(data)) {
                    data = [data];
                }
                self.postLineItemSelectionActionsApprenant(triggerer.closest('tr'), data, popupReferenceModuleApprenant);
            }
            app.event.off('post.LineItemPopupSelectionApprenant.click');
            app.event.one('post.LineItemPopupSelectionApprenant.click', postPopupHandler);
        });
    },

    postLineItemSelectionActionsApprenant: function (itemRow, selectedLineItemsDataApprenant, lineItemSelectedModuleNameApprenant) {
        console.log("postLineItemSelectionActionsApprenant");
        console.log("index" + index);
        for (var index in selectedLineItemsDataApprenant) {
            if (index != 0) {
                console.log("lineItemSelectedModuleNameApprenant" + lineItemSelectedModuleNameApprenant);
                if (lineItemSelectedModuleNameApprenant == 'Contacts') {
                    jQuery('#addApprenant').trigger('click', selectedLineItemsDataApprenant[index]);
                }
            } else {
                console.log("lineItemSelectedModuleNameApprenant" + lineItemSelectedModuleNameApprenant);
                itemRow.find('.lineItemTypeApprenant').val(lineItemSelectedModuleNameApprenant);
                console.log("selectedLineItemsDataApprenant" + selectedLineItemsDataApprenant[index]);
                this.mapResultsToFieldsApprenant(itemRow, selectedLineItemsDataApprenant[index]);
            }
        }
    },

    registerClearLineItemSelectionApprenant: function () {
        var self = this;

        this.lineItemsHolderApprenant.on('click', '.clearLineItemApprenant', function (e) {
            var elem = jQuery(e.currentTarget);
            var parentElem = elem.closest('td');
            self.clearLineItemDetailsApprenant(parentElem);
            parentElem.find('input.contactName').removeAttr('disabled').val('');
            e.preventDefault();
        });
    },

    clearLineItemDetailsApprenant: function (parentElem) {
        var lineItemRowApprenant = this.getClosestLineItemRowApprenant(parentElem);
        jQuery('input.numclient', lineItemRowApprenant).val('');
        jQuery('input.nomclient', lineItemRowApprenant).val('');
        jQuery('input.telephone', lineItemRowApprenant).val('');
        jQuery('input.email', lineItemRowApprenant).val('');
        jQuery('input.contactName', lineItemRowApprenant).val('');


    },

    getClosestLineItemRowApprenant: function (element) {
        return element.closest('tr.' + this.lineItemDetectingClassApprenant);
    },

    getLineItemNextRowNumberApprenant: function () {
        return ++this.numOfLineItemsApprenant;
    },

    getNewLineItemApprenant: function (params) {
        var currentTarget = params.currentTarget;
        var itemType = currentTarget.data('moduleName');
        var newRow = this.dummyLineItemRowApprenant.clone(true).removeClass('hide').addClass(this.lineItemDetectingClassApprenant).removeClass('lineItemCloneCopyApprenant');
        newRow.find('.lineItemPopup').filter(':not([data-module-name="' + itemType + '"])').remove();
        newRow.find('.lineItemType').val(itemType);
        var newRowNum = this.getLineItemNextRowNumberApprenant();
        this.updateRowNumberForRowApprenant(newRow, newRowNum);
        this.initializeLineItemRowCustomFields(newRow, newRowNum);
        return newRow
    },

    registerSubmitEventApprenant: function () {
        var self = this;
        var editViewForm = this.getForm();
        //this._super();
        editViewForm.submit(function (e) {
            /* uni_cnfsecrm - v2 - modif 168 - DEBUT */
            self.updateLineItemElementByOrderDate();
            /* uni_cnfsecrm - v2 - modif 168 - FIN */
            self.updateLineItemElementByOrderApprenant();

            /* uni_cnfsecrm */
            self.saveDateCount();
            self.saveFinanceurCount();
            self.saveApprenantCount();
            return true;
        })
    },

    /* uni-cnfsecrm - Fin Apprenants */

    registerBasicEventsDate: function (container) {
        console.log("registerBasicEventsDate");
        this.initializeVariablesDate();
        this.registerAddingNewDate();
        this.registerDeleteLineItemEventDate();
        this.makeLineItemsSortableDate();
        //this.updateLineDate();
        this.registerLineItemAutoCompleteDate();
        this.registerProductAndServiceSelectorDate();
        /* uni_cnfsecrm - v2 - modif 168 - DEBUT */
        this.updateLineItemElementByOrderDate();
        /* uni_cnfsecrm - v2 - modif 168 - FIN */
    },

    registerBasicEventsFinanceur: function (container) {
        this.initializeVariablesFinanceur();
        this.registerAddingNewFinanceur();
        this.registerDeleteLineItemEventFinanceur();
        this.makeLineItemsSortableFinanceur();
        //this.updateLineFinanceur();
        this.registerLineItemAutoCompleteFinanceur();
        this.registerProductAndServiceSelectorFinanceur();
        this.registerClearLineItemSelectionFinanceur();
    },

    registerBasicEventsApprenant: function (container) {
        this.initializeVariablesApprenant();
        this.registerAddingNewApprenant();
        this.registerDeleteLineItemEventApprenant();
        this.makeLineItemsSortableApprenant();
//        this.updateLineApprenant();
        this.registerLineItemAutoCompleteApprenant();//ajout produit 
        this.registerApprenant();
        this.registerClearLineItemSelectionApprenant();
        this.registerSubmitEventApprenant();
        this.registerOptionsAvisAttestationShowEvent();
        this.registerOptionsAvisAttestationCloseEvent();
        this.registeroptionAvisAttestationChangeEvent();
        this.registerOptionsAvisAttestationCancelClickEvent();
    },

    saveApprenantCount: function () {
        console.log(this.lineItemsHolderApprenant.find('tr.' + this.lineItemDetectingClassApprenant).length);
        jQuery('#totalApprenantsCount').val(this.lineItemsHolderApprenant.find('tr.' + this.lineItemDetectingClassApprenant).length);
    },

    /* uni-cnfsecrm - fin */

    registerEvents: function () {
        this._super();
        this.registerBasicEventsDate();
        this.registerBasicEventsFinanceur();
        this.registerBasicEventsApprenant();
        /* unicnfsecrm_022020_09 - begin */
        this.registerAdresseAutoComplete();
        var url = window.location.href;
        var position = url.search("record");
        if (position < 0) {
            this.registerAdresseCharentonAutoComplete();
        }
        /* unicnfsecrm_022020_09 - end */
        /* uni_cnfsecrm - v2 - modif 177 - DEBUT */
        this.importExcel();
        /* uni_cnfsecrm - v2 - modif 177 - FIN */
    },

    /* uni_cnfsecrm - v2 - modif 177 - DEBUT */
    //sftp://root@94.23.214.76/var/www/html/cnfsecrm/modules/Contacts/actions/ImportExcel.php
    importExcel: function () {
        var self = this;
        jQuery("#importExcel").on("click", function () {
            var file_data = $('.file').prop('files')[0];
            if (file_data != undefined) {
                var form_data = new FormData();
                form_data.append('file', file_data);
                $.ajax({
                    type: 'POST',
                    url: 'index.php?module=Contacts&action=ImportExcel',
                    contentType: false,
                    processData: false,
                    data: form_data,
                    success: function (response) {
                        self.addApprenantExcel(response['result'].contactInfo, response['result'].contactError);

                    }
                });
            } else {
                return false;
            }

        })
    },
    addApprenantExcel: function (response, apprenantError) {
        var self = this;
        if (response != null) {
            jQuery.each(response, function (i, val) {
//            console.log(val)
//            var currentTarget = jQuery(e.currentTarget);
//            var params = {'currentTarget': currentTarget}
                var newLineItem = self.getNewLineItemApprenantExcel();
                var sequenceNumber = self.getNextLineItemRowNumberApprenant();
                newLineItem = newLineItem.appendTo(self.lineItemsHolderApprenant);
                newLineItem.find('input.contactName').addClass('autoCompleteApprenant');
                newLineItem.find('.ignore-ui-registration').removeClass('ignore-ui-registration');
                vtUtils.applyFieldElementsView(newLineItem);
                app.event.trigger('post.lineItem.New', newLineItem);
                self.checkLineItemRowApprenant();
                newLineItem.find('input.rowNumberApprenant').val(sequenceNumber);
                //      self.updateRowNumberForRowApprenant(newLineItem, sequenceNumber);
                self.registerLineItemAutoCompleteApprenant(newLineItem);
                self.mapResultsToFieldsApprenantExcel(newLineItem, val);
            });
        }

        /**/
        var message = "";
        if (response != null) {
            message += "<p>Apprenant(s) importé(s) avec succés :</p>"
            message += "<ul style='color: green;'>";
            jQuery.each(response, function (i, val) {
                message += "<li>" + val['apprenant_nom'] + " " + val['apprenant_prenom'] + "</li>";
            })
            message += "</ul>";
        }
        console.log("apprenantError")
        console.log(apprenantError)
        if (apprenantError != null) {
            message += "<p>Nom de direction manquant, veuillez le compléter dans le fichier pour l'importer.</p>"
            message += "<ul style='color: red;'>";
            jQuery.each(apprenantError, function (i, val) {
                message += "<li>" + val['Prénom'] + " " + val['Nom'] + "</li>";
            })
            message += "</ul>";
        }

        var modal = "<div class='modal-dialog'><div class='modal-content'><div class='modal-header'><div class='clearfix'><div class='pull-right ' ><button type='button' class='close' aria-label='Close' data-dismiss='modal'><span aria-hidden='true' class='fa fa-close'></span></button></div><h4 class='pull-left'>Resultat</h4></div></div><div class='modal-body'><div class='mailSentSuccessfully' data-relatedload=''>" + message + "</div><input type='hidden' id='flag' value='SENT'></div></div></div>";
        app.helper.hideProgress();
        app.helper.showModal(modal);
        /**/
    },

    getNewLineItemApprenantExcel: function () {
        var itemType = "Contacts";
        var newRow = this.dummyLineItemRowApprenant.clone(true).removeClass('hide').addClass(this.lineItemDetectingClassApprenant).removeClass('lineItemCloneCopyApprenant');
        newRow.find('.lineItemPopup').filter(':not([data-module-name="' + itemType + '"])').remove();
        newRow.find('.lineItemType').val(itemType);
        var newRowNum = this.getLineItemNextRowNumberApprenant();
        this.updateRowNumberForRowApprenant(newRow, newRowNum);
        this.initializeLineItemRowCustomFields(newRow, newRowNum);
        return newRow
    },

    mapResultsToFieldsApprenantExcel: function (parentRow, responseData) {
        var lineItemNameElmentApprenant = jQuery('input.contactName', parentRow);
        var referenceModuleApprenant = this.getLineItemSetypeApprenant(parentRow);
        var lineItemRowNumber = parentRow.data('rowNum');
        console.log(responseData)

        var selectedName = responseData['apprenant_nom'] + " " + responseData['apprenant_prenom'];
        var numclient = responseData.account_nom;
        var nomclient = responseData.account_num;
        var telephone = responseData.apprenant_phone
        var email = responseData.apprenant_email;
        var apprenantid = responseData.apprenant_id;

        jQuery('input.lineItemTypeApprenant', parentRow).val(referenceModuleApprenant);
        lineItemNameElmentApprenant.val(selectedName);
        lineItemNameElmentApprenant.attr('disabled', 'disabled');
        jQuery('input.numclient', parentRow).val(numclient);
        jQuery('input.nomclient', parentRow).val(nomclient);
        jQuery('input.telephone', parentRow).val(telephone);
        jQuery('input.email', parentRow).val(email);
        jQuery('input.apprenantid', parentRow).val(apprenantid);

    },
    /* uni_cnfsecrm - v2 - modif 177 - FIN */

    showLineItemsDeleteIcon: function () {
        var lineItemTable = this.getLineItemContentsContainer();
        lineItemTable.find('.deleteRow').show();
    },

    registerDeleteLineItemEvent: function () {
        var thisInstance = this;
        var lineItemTable = this.getLineItemContentsContainer();

        lineItemTable.on('click', '.deleteRow', function (e) {
            var element = jQuery(e.currentTarget);
            //removing the row

            element.closest('tr.' + thisInstance.rowClass).remove();
            thisInstance.checkLineItemRow();
        });
    },

    hideLineItemsDeleteIcon: function () {
        var lineItemTable = this.getLineItemContentsContainer();
        lineItemTable.find('.deleteRow').hide();
    },
    /**
     * Function which will give the closest line item row element
     * @return : jQuery object
     */
    getClosestLineItemRow: function (element) {
        return element.closest('tr.' + this.rowClass);
    },

    makeLineItemsSortable: function () {
        //console.log("makeLineItemsSortable");
        var thisInstance = this;
        var lineItemTable = this.getLineItemContentsContainer();
        lineItemTable.sortable({
            'containment': lineItemTable,
            'items': 'tr.' + this.rowClass,
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
            thisInstance.getClosestLineItemRow(jQuery(event.target)).find('input:focus').trigger('focusout');
        });
    },

    /* fin modification Unicom */

    openPopUp: function (e) {
        var thisInstance = this;
        var parentElem = thisInstance.getParentElement(jQuery(e.target));

        var params = this.getPopUpParams(parentElem);
        params.view = 'Popup';

        var isMultiple = false;
        if (params.multi_select) {
            isMultiple = true;
        }

        var sourceFieldElement = jQuery('input[class="sourceField"]', parentElem);

        var prePopupOpenEvent = jQuery.Event(Vtiger_Edit_Js.preReferencePopUpOpenEvent);
        sourceFieldElement.trigger(prePopupOpenEvent);

        if (prePopupOpenEvent.isDefaultPrevented()) {
            return;
        }
        var popupInstance = Vtiger_Popup_Js.getInstance();
        popupInstance.showPopup(params, function (data) {
            var responseData = JSON.parse(data);
            var dataList = new Array();
            for (var id in responseData) {
                var data = {
                    'name': responseData[id].name,
                    'id': id
                }
                dataList.push(data);
                if (!isMultiple) {
                    thisInstance.setReferenceFieldValue(parentElem, data);
                }
            }

            if (isMultiple) {
                sourceFieldElement.trigger(Vtiger_Edit_Js.refrenceMultiSelectionEvent, {'data': dataList});
            }
            sourceFieldElement.trigger(Vtiger_Edit_Js.postReferenceSelectionEvent, {'data': responseData});
        });
    },

    registerRelatedContactSpecificEvents: function (form) {
        var thisInstance = this;
        if (typeof form == "undefined") {
            form = this.getForm();
        }
        form.find('[name="contact_id"]').on(Vtiger_Edit_Js.preReferencePopUpOpenEvent, function (e) {
            var parentIdElement = form.find('[name="parent_id"]');
            if (parentIdElement.length <= 0) {
                parentIdElement = form.find('[name="contact_id"]');
            }
            var container = parentIdElement.closest('td');
            var popupReferenceModule = jQuery('input[name="popupReferenceModule"]', container).val();

            if (popupReferenceModule == 'Leads' && parentIdElement.val().length > 0) {
                e.preventDefault();
                app.helper.showErrorNotification({message: app.vtranslate('LBL_CANT_SELECT_CONTACT_FROM_LEADS')});
            }
        })
        //If module is not events then we dont have to register events
        if (!this.isEvents(form)) {
            return;
        }
        this.getRelatedContactElement(form).select2({
            minimumInputLength: 3,
            ajax: {
                'url': 'index.php?module=Contacts&action=BasicAjax&search_module=Contacts',
                'dataType': 'json',
                'data': function (term, page) {
                    var data = {};
                    data['search_value'] = term;
                    var parentIdElement = form.find('[name="parent_id"]');
                    if (parentIdElement.length > 0 && parentIdElement.val().length > 0) {
                        var closestContainer = parentIdElement.closest('td');
                        data['parent_id'] = parentIdElement.val();
                        data['parent_module'] = closestContainer.find('[name="popupReferenceModule"]').val();
                    }
                    return data;
                },
                'results': function (data) {
                    data.results = data.result;
                    for (var index in data.results) {

                        var resultData = data.result[index];
                        resultData.text = resultData.label;
                    }
                    return data
                },
                transport: function (params) {
                    return jQuery.ajax(params);
                }
            },
            multiple: true,
            //To Make the menu come up in the case of quick create
            dropdownCss: {'z-index': '10001'}
        });

        //To add multiple selected contact from popup
        form.find('[name="contact_id"]').on(Vtiger_Edit_Js.refrenceMultiSelectionEvent, function (e, result) {
            thisInstance.addNewContactToRelatedList(result, form);
        });

        this.fillRelatedContacts(form);
    },
    /**
     * Function to get reference search params
     */
    getReferenceSearchParams: function (element) {
        var tdElement = jQuery(element).closest('td');
        var params = {};
        var previousTd = tdElement.prev();
        var multiModuleElement = jQuery('select.referenceModulesList', previousTd);

        var referenceModuleElement;
        if (multiModuleElement.length) {
            referenceModuleElement = multiModuleElement;
        } else {
            referenceModuleElement = jQuery('input[name="popupReferenceModule"]', tdElement).length ?
                    jQuery('input[name="popupReferenceModule"]', tdElement) : jQuery('input.popupReferenceModule', tdElement);
        }
        var searchModule = referenceModuleElement.val();
        params.search_module = searchModule;
        return params;
    },

    isEvents: function (form) {
        if (typeof form === 'undefined') {
            form = this.getForm();
        }
        var moduleName = form.find('[name="module"]').val();
        if (form.find('.quickCreateContent').length > 0 && form.find('[name="calendarModule"]').val() === 'Events') {
            return true;
        }
        if (moduleName === 'Events') {
            return true;
        }
        return false;
    },

    getPopUpParams: function (container) {
        var params = this._super(container);
        var sourceFieldElement = jQuery('input[class="sourceField"]', container);

        if (sourceFieldElement.attr('name') == 'contact_id') {
            var form = container.closest('form');
            var parentIdElement = form.find('[name="parent_id"]');
            var closestContainer = parentIdElement.closest('td');
            var referenceModule = closestContainer.find('[name="popupReferenceModule"]');
            if (parentIdElement.length > 0 && parentIdElement.val().length > 0 && referenceModule.length > 0) {
                params['related_parent_id'] = parentIdElement.val();
                params['related_parent_module'] = referenceModule.val();
            }
        }
        return params;
    },

    addInviteesIds: function (form) {
        var thisInstance = this;
        if (thisInstance.isEvents(form)) {
            var inviteeIdsList = jQuery('#selectedUsers').val();
            if (inviteeIdsList) {
                inviteeIdsList = jQuery('#selectedUsers').val().join(';')
            }
            jQuery('<input type="hidden" name="inviteesid" />').
                    appendTo(form).
                    val(inviteeIdsList);
        }
    },

    resetRecurringDetailsIfDisabled: function (form) {
        var recurringCheck = form.find('input[name="recurringcheck"]').is(':checked');
        //If the recurring check is not enabled then recurring type should be --None--
        if (!recurringCheck) {
            jQuery('#recurringType').append(jQuery('<option value="--None--">None</option>')).val('--None--');
        }
    },

    initializeContactIdList: function (form) {
        var relatedContactElement = this.getRelatedContactElement(form);
        if (this.isEvents(form) && relatedContactElement.length) {
            jQuery('<input type="hidden" name="contactidlist" /> ').appendTo(form).val(relatedContactElement.val().split(',').join(';'));
            form.find('[name="contact_id"]').attr('name', '');
        }
    },

    registerRecurringEditOptions: function (e, form, InitialFormData) {
        var currentFormData = form.serialize();
        var editViewContainer = form.closest('.editViewPageDiv').length;
        var recurringEdit = form.find('.recurringEdit').length;
        var recurringEditMode = form.find('[name="recurringEditMode"]');
        var recurringCheck = form.find('input[name="recurringcheck"]').is(':checked');

        if (editViewContainer && InitialFormData === currentFormData && recurringEdit) {
            recurringEditMode.val('current');
        } else if (editViewContainer && recurringCheck && recurringEdit && InitialFormData !== currentFormData) {
            e.preventDefault();

            var recurringEventsUpdateModal = form.find('.recurringEventsUpdation');
            var clonedContainer = recurringEventsUpdateModal.clone(true, true);

            var callback = function (data) {
                var modalContainer = data.find('.recurringEventsUpdation');
                modalContainer.removeClass('hide');
                modalContainer.on('click', '.onlyThisEvent', function () {
                    recurringEditMode.val('current');
                    app.helper.hideModal();
                    form.vtValidate({
                        submitHandler: function () {
                            return true;
                        }
                    });
                    form.submit();
                });
                modalContainer.on('click', '.futureEvents', function () {
                    recurringEditMode.val('future');
                    app.helper.hideModal();
                    form.vtValidate({
                        submitHandler: function () {
                            return true;
                        }
                    });
                    form.submit();
                });
                modalContainer.on('click', '.allEvents', function () {
                    recurringEditMode.val('all');
                    app.helper.hideModal();
                    form.vtValidate({
                        submitHandler: function () {
                            return true;
                        }
                    });
                    form.submit();
                });
            };

            app.helper.showModal(clonedContainer, {
                'cb': callback
            });
        }
    },

    registerRecordPreSaveEvent: function (form) {
        var thisInstance = this;
        if (typeof form === "undefined") {
            form = this.getForm();
        }
        var InitialFormData = form.serialize();
        app.event.one(Vtiger_Edit_Js.recordPresaveEvent, function (e) {
            thisInstance.registerRecurringEditOptions(e, form, InitialFormData);
            thisInstance.addInviteesIds(form);
            thisInstance.resetRecurringDetailsIfDisabled(form);
            thisInstance.initializeContactIdList(form);
        });
    },

    registerTimeStartChangeEvent: function (container) {
        container.on('changeTime', 'input[name="time_start"]', function () {
            var startDateElement = container.find('input[name="date_start"]');
            var startTimeElement = container.find('input[name="time_start"]');
            var endDateElement = container.find('input[name="due_date"]');
            var endTimeElement = container.find('input[name="time_end"]');

            var activityType = container.find('[name="activitytype"]').val();

            var momentFormat = vtUtils.getMomentCompatibleDateTimeFormat();
            var m = moment(startDateElement.val() + ' ' + startTimeElement.val(), momentFormat);

            var minutesToAdd = container.find('input[name="defaultOtherEventDuration"]').val();
            if (activityType === 'Call') {
                minutesToAdd = container.find('input[name="defaultCallDuration"]').val();
            }
            if (Calendar_Edit_Js.userChangedTimeDiff) {
                minutesToAdd = Calendar_Edit_Js.userChangedTimeDiff;
            }
            m.add(parseInt(minutesToAdd), 'minutes');
            if ((container.find('[name="time_start"]').data('userChangedDateTime') !== 1) || (container.find('[name="module"]').val() === 'Calendar' || container.find('[name="module"]').val() === 'Events')) {
                if (m.format(vtUtils.getMomentDateFormat()) == 'Invalid date') {
                    m.format(vtUtils.getMomentDateFormat()) = '';
                }
                endDateElement.val(m.format(vtUtils.getMomentDateFormat()));
            }
            endTimeElement.val(m.format(vtUtils.getMomentTimeFormat()));

            vtUtils.registerEventForDateFields(endDateElement);
            vtUtils.registerEventForTimeFields(endTimeElement);
            endDateElement.valid();
        });
    },

    /**
     * Function which will fill the already saved contacts on load
     */
    fillRelatedContacts: function (form) {
        if (typeof form == "undefined") {
            form = this.getForm();
        }
        var relatedContactValue = form.find('[name="relatedContactInfo"]').data('value');
        for (var contactId in relatedContactValue) {
            var info = relatedContactValue[contactId];
            info.text = info.name;
            relatedContactValue[contactId] = info;
        }
        this.getRelatedContactElement(form).select2('data', relatedContactValue);
    },

    addNewContactToRelatedList: function (newContactInfo, form) {
        if (form.length <= 0) {
            form = this.getForm();
        }
        var resultentData = new Array();

        var element = jQuery('#contact_id_display', form);
        var selectContainer = jQuery(element.data('select2').container, form);
        var choices = selectContainer.find('.select2-search-choice');
        choices.each(function (index, element) {
            resultentData.push(jQuery(element).data('select2-data'));
        });
        var select2FormatedResult = newContactInfo.data;
        for (var i = 0; i < select2FormatedResult.length; i++) {
            var recordResult = select2FormatedResult[i];
            recordResult.text = recordResult.name;
            resultentData.push(recordResult);
        }
        element.select2('data', resultentData);
        if (form.find('.quickCreateContent').length > 0) {
            form.find('[name="relatedContactInfo"]').data('value', resultentData);
            var relatedContactElement = this.getRelatedContactElement(form);
            if (relatedContactElement.length > 0) {
                jQuery('<input type="hidden" name="contactidlist" /> ').appendTo(form).val(relatedContactElement.val().split(',').join(';'));
                form.find('[name="contact_id"]').attr('name', '');
            }
        }
    },

    referenceCreateHandler: function (container) {

        var thisInstance = this;
        var form = thisInstance.getForm();
        var mode = jQuery(form).find('[name="module"]').val();
        if (container.find('.sourceField').attr('name') != 'contact_id') {
            this._super(container);
            return;
        }
        var postQuickCreateSave = function (data) {
            var params = {};
            params.name = data._recordLabel;
            params.id = data._recordId;
            if (mode == "Calendar") {
                thisInstance.setReferenceFieldValue(container, params);
                return;
            }
            thisInstance.addNewContactToRelatedList({'data': [params]}, container);
        }

        var referenceModuleName = this.getReferencedModuleName(container);
        var quickCreateNode = jQuery('#quickCreateModules').find('[data-name="' + referenceModuleName + '"]');
        if (quickCreateNode.length <= 0) {
            return app.helper.showErrorNotification({message: app.vtranslate('JS_NO_CREATE_OR_NOT_QUICK_CREATE_ENABLED')});

        }
        quickCreateNode.trigger('click', {'callbackFunction': postQuickCreateSave});
    },

    /**
     * Function which will register the change event for repeatMonth radio buttons
     */
    registerRepeatMonthActions: function () {
        var thisInstance = this;
        thisInstance.getForm().find('input[name="repeatMonth"]').on('change', function (e) {
            //If repeatDay radio button is checked then only select2 elements will be enable
            thisInstance.repeatMonthOptionsChangeHandling();
        });
    },

    /**
     * This function will handle the change event for RepeatMonthOptions
     */
    repeatMonthOptionsChangeHandling: function () {
        //If repeatDay radio button is checked then only select2 elements will be enable
        if (jQuery('#repeatDay').is(':checked')) {
            jQuery('#repeatMonthDate').attr('disabled', true);
            jQuery('#repeatMonthDayType').select2("enable");
            jQuery('#repeatMonthDay').select2("enable");
        } else {
            jQuery('#repeatMonthDate').removeAttr('disabled');
            jQuery('#repeatMonthDayType').select2("disable");
            jQuery('#repeatMonthDay').select2("disable");
        }
    },

    /**
     * Function which will change the UI styles based on recurring type
     * @params - recurringType - which recurringtype is selected
     */
    changeRecurringTypesUIStyles: function (recurringType) {
        var thisInstance = this;
        if (recurringType == 'Daily' || recurringType == 'Yearly') {
            jQuery('#repeatWeekUI').removeClass('show').addClass('hide');
            jQuery('#repeatMonthUI').removeClass('show').addClass('hide');
        } else if (recurringType == 'Weekly') {
            jQuery('#repeatWeekUI').removeClass('hide').addClass('show');
            jQuery('#repeatMonthUI').removeClass('show').addClass('hide');
        } else if (recurringType == 'Monthly') {
            jQuery('#repeatWeekUI').removeClass('show').addClass('hide');
            jQuery('#repeatMonthUI').removeClass('hide').addClass('show');
        }
    },

    registerDateStartChangeEvent: function (container) {
        container.find('[name="date_start"]').on('change', function () {
            var timeStartElement = container.find('[name="time_start"]');
            timeStartElement.trigger('changeTime');
        });
    },

    registerTimeEndChangeEvent: function (container) {
        container.find('[name="time_end"]').on('changeTime', function () {
            var startDateElement = container.find('input[name="date_start"]');
            var startTimeElement = container.find('input[name="time_start"]');
            var endDateElement = container.find('input[name="due_date"]');
            var endTimeElement = container.find('input[name="time_end"]');
            var momentFormat = vtUtils.getMomentCompatibleDateTimeFormat();
            var m1 = moment(endDateElement.val() + ' ' + endTimeElement.val(), momentFormat);
            var m2 = moment(startDateElement.val() + ' ' + startTimeElement.val(), momentFormat);
            var newDiff = (m1.unix() - m2.unix()) / 60;
            Calendar_Edit_Js.userChangedTimeDiff = newDiff;
            container.find('[name="due_date"]').valid();
        });
        if (container.find('[name="record"]') !== '') {
            container.find('[name="time_end"]').trigger('changeTime');
        }
    },

    registerDateEndChangeEvent: function (container) {
        container.find('[name="due_date"]').on('change', function () {});
    },

    registerActivityTypeChangeEvent: function (container) {
        container.find('[name="activitytype"]').on('change', function () {
            var time_start_element = container.find('[name="time_start"]');
            time_start_element.trigger('changeTime');
        });
    },

    registerUserChangedDateTimeDetection: function (container) {
        var initialValue;
        container.on('focus',
                '[name="date_start"], [name="due_date"], [name="time_start"], [name="time_end"]',
                function () {
                    initialValue = jQuery(this).val();
                });
        container.on('blur',
                '[name="date_start"], [name="due_date"], [name="time_start"], [name="time_end"]',
                function () {
                    if (typeof initialValue !== 'undefined' && initialValue !== jQuery(this).val()) {
                        container.find('[name="time_start"]').data('userChangedDateTime', 1);
                    }
                });
    },

    registerDateTimeHandlersEditView: function (container) {
        var thisInstance = this;
        var registered = false;

        container.on('focus', '[name="date_start"],[name="time_start"]', function () {
            if (!registered) {
                thisInstance.registerDateStartChangeEvent(container);
                thisInstance.registerTimeStartChangeEvent(container);
                thisInstance.registerTimeEndChangeEvent(container);
                thisInstance.registerDateEndChangeEvent(container);
                thisInstance.registerUserChangedDateTimeDetection(container);
                thisInstance.registerActivityTypeChangeEvent(container);
                registered = true;
            }
        });
    },

    registerDateTimeHandlers: function (container) {
        var thisInstance = this;
        if (container.find('[name="record"]').val() === '') {
            this.registerDateStartChangeEvent(container);
            this.registerTimeStartChangeEvent(container);
            container.find('[name="time_end"]').on('focus', function () {
                thisInstance.registerTimeEndChangeEvent(container);
            });
            this.registerDateEndChangeEvent(container);
            this.registerUserChangedDateTimeDetection(container);
            this.registerActivityTypeChangeEvent(container);
        } else {
            this.registerDateTimeHandlersEditView(container);
        }
    },

    registerToggleReminderEvent: function (container) {
        container.find('input[name="set_reminder"]').on('change', function (e) {
            var element = jQuery(e.currentTarget);
            var reminderSelectors = element.closest('#js-reminder-controls')
                    .find('#js-reminder-selections');
            if (element.is(':checked')) {
                reminderSelectors.css('visibility', 'visible');
            } else {
                reminderSelectors.css('visibility', 'collapse');
            }
        })
    },

    /**
     * Function register to change recurring type.
     */

    registerRecurringTypeChangeEvent: function () {
        var thisInstance = this;
        jQuery('#recurringType').on('change', function (e) {
            var currentTarget = jQuery(e.currentTarget);
            var recurringType = currentTarget.val();
            thisInstance.changeRecurringTypesUIStyles(recurringType);
        });
    },

    /**
     * Function to register recurrenceField checkbox.
     */
    registerRecurrenceFieldCheckBox: function (container) {
        container.find('input[name="recurringcheck"]').on('change', function (e) {
            var element = jQuery(e.currentTarget);
            var repeatUI = jQuery('#repeatUI');
            if (element.is(':checked')) {
                repeatUI.css('visibility', 'visible');
            } else {
                repeatUI.css('visibility', 'collapse');
            }
        });
    },

    openPopUp2: function (e, apprenantid, row_num) {
        var thisInstance = this;
        var parentElem = thisInstance.getParentElement(jQuery(e.target));

        var params = this.getPopUpParams(parentElem);
        params.view = 'Popup2';
        params.module = 'Contacts';
        var record = jQuery("[name='record']").val();
        params.record = record;
        params.apprenantid = apprenantid;
        params.row_num = row_num;

        var popupInstance = Vtiger_Popup_Js.getInstance();
        console.log(params);
        popupInstance.showPopup(params, function (data) {
        });
    },

    registerOptionsAvisAttestationShowEvent: function () {
        var self = this;

        this.lineItemsHolderApprenant.on('click', '#optionAvisAttestation', function (e) {
            var triggerer = jQuery(e.currentTarget);
            var apprenantid = triggerer.data('apprenantid');
            var tr_apprenant = triggerer.closest('tr');
            var row_num = tr_apprenant.data('rowNum');
            console.log("row_num" + row_num);
            self.openPopUp2(e, apprenantid, row_num);
//            self.showLineItemPopupApprenant({'view': triggerer.data('popup')});
//            var popupReferenceModuleApprenant = triggerer.data('moduleName');
//            var postPopupHandler = function (e, data) {
//                data = JSON.parse(data);
//                if (!$.isArray(data)) {
//                    data = [data];
//                }
//                self.postLineItemSelectionActionsApprenant(triggerer.closest('tr'), data, popupReferenceModuleApprenant);
//            }
//            app.event.off('post.LineItemPopupSelectionApprenant.click');
//            app.event.one('post.LineItemPopupSelectionApprenant.click', postPopupHandler);    
//            var elem = jQuery(e.currentTarget);
//            var parentElem = elem.closest('td');
//            parentElem.find('#optionAvisAttestationUI').removeClass('hide');
//            parentElem.find('#optionAvisAttestation').addClass('hide');
//            e.preventDefault();
        });
    },

    registerOptionsAvisAttestationCloseEvent: function () {
        var self = this;
        this.lineItemsHolderApprenant.on('click', '.closeDiv', function (e) {
            var elem = jQuery(e.currentTarget);
            var parentElem = elem.closest('#optionAvisAttestationUI');
            parentElem.addClass('hide');
            var parentElemTD = parentElem.closest('td');
            parentElemTD.find('#optionAvisAttestation').removeClass('hide');
            e.preventDefault();
        });
    },

    registeroptionAvisAttestationChangeEvent: function () {
        var self = this;
        this.lineItemsHolderApprenant.on('click', '.optionAvisAttestationtSave', function (e) {
            var elem = jQuery(e.currentTarget);
            self.optionAvisAttestationChangeActions(e);
            e.preventDefault();
        });
    },

    registerOptionsAvisAttestationCancelClickEvent: function () {
        var self = this;
        this.lineItemsHolderApprenant.on('click', '.cancelLink', function (e) {
            var elem = jQuery(e.currentTarget);
            var parentElem = elem.closest('#optionAvisAttestationUI');
            parentElem.find('.closeDiv').trigger('click');
            e.preventDefault();
        });
    },

    optionAvisAttestationChangeActions: function (e) {
        var elem = jQuery(e.currentTarget);
        var parentElem = elem.closest('#optionAvisAttestationUI');
        parentElem.addClass('hide');
        var parentElemTD = parentElem.closest('td');
        parentElemTD.find('#optionAvisAttestation').removeClass('hide');
        e.preventDefault();
    },

    registerBasicEvents: function (container) {
        this._super(container);
        this.registerRecordPreSaveEvent(container);
        this.registerDateTimeHandlers(container);
        this.registerToggleReminderEvent(container);
        this.registerRecurrenceFieldCheckBox(container);
        this.registerRecurringTypeChangeEvent();
        this.repeatMonthOptionsChangeHandling();
        this.registerRepeatMonthActions();
        this.registerRelatedContactSpecificEvents(container);
        /* unicnfsecrm_mod_31 */
        this.registerCityAutoComplete();
        /* uni_cnfsecrm - v2 - modif 126 - DEBUT */
        this.getDateDebutFin();
        /* uni_cnfsecrm - v2 - modif 126 - FIN */
    },

    /* unicnfsecrm_022020_09 - begin */
    //remplir l'adresse de CHARENTON par default
    //unicnfsecrm_mod_39
    registerAdresseCharentonAutoComplete: function () {
        var container = $('recordEditView');
        $('input[name="lieu"]').val('11827');
        $('input[name="lieu_display"]').val('CHARENTON LE PONT');
        $('select[name="cf_921"] option[value="Au centre"]').attr('selected', 'selected').change();
        var data = {source_module: "lieu", record: 11827, selectedName: ""}
        var self = this;
        var idlieu = data.record;
        var sourceModule = data['source_module'];
        this.getRecordDetailsAucentre(data).then(
                function (data) {
                    var response = data;
                    self.mapAucentreDetails(self.addressFieldsMapping[sourceModule], response[0][idlieu].id, container);
                    if ((sourceModule == "Lieu") || (sourceModule == "lieu")) {
                        var adresse = response[0][idlieu].adresse;
                        var codePostale = response[0][idlieu].codePostale;
                        var ville = response[0][idlieu].ville;

                        //var salle = $("input[name='salle_display']").val();
                        $("textarea[name='cf_933']").val(adresse);
                        $("input[name='cf_929']").val(codePostale);
                        $("input[name='cf_931']").val(ville);
                    }
                },
                function (error, err) {

                });
    },

    registerAdresseAutoComplete: function () {
        var self = this;
        var container = $('recordEditView');

        $('select[name="cf_854"]').on('click', function (e, data) {
            var type = $('select[name="cf_854"] option:selected').val();
            var locaux = $('select[name="cf_921"] option:selected').val();
            var recordClient = $('input[name="parent_id"]').val();
            var recordLieu = $('input[name="lieu"]').val();
            if (type == 'Intra' && recordClient != 'undefined' && recordClient != '') {
                data = {source_module: "Accounts", record: recordClient, selectedName: ""}
                self.referenceSelectionClientHandler(data, container);
            } else if (type == 'Inter') {
                data = {source_module: "lieu", record: 11827, selectedName: ""}
                self.referenceSelectionAucentreHandler(data, container);
                $('input[name="lieu"]').val('11827');
                $('input[name="lieu_display"]').val('CHARENTON LE PONT');
                $('select[name="cf_921"] option[value="Au centre"]').attr('selected', 'selected').change();
            }
        });

        $('select[name="cf_921"]').on('click', function (e, data) {
            console.log(" Locaux changé ");
            var locaux = $('select[name="cf_921"] option:selected').val();
            console.log(" Locaux changé " + locaux);
            var recordClient = $('input[name="parent_id"]').val();
            var recordLieu = $('input[name="lieu"]').val();
            if (locaux == 'Client' && recordClient != 'undefined' && recordClient != '') {
                // var nomclient = $('tr[data-id="${record}"]').attr('data-name'); 
                data = {source_module: "Accounts", record: recordClient, selectedName: ""}
                self.referenceSelectionClientHandler(data, container);
            } else if (locaux == 'Au centre' && recordLieu != 'undefined' && recordLieu != '') {
                data = {source_module: "lieu", record: recordLieu, selectedName: ""}
                self.referenceSelectionAucentreHandler(data, container);
            }
        });

        $('input[name="parent_id"]').on(Vtiger_Edit_Js.referenceSelectionEvent, function (e, data) {
            var locaux = $('select[name="cf_921"] option:selected').val();
            var idClient = $('input[name="parent_id"]').val();
            if ((locaux == 'Client') && (idClient != 'undefined') && (idClient != '')) {
                self.referenceSelectionClientHandler(data, container);
            }
            // unicnfsecrm_mod_37
            var url = window.location.href;
            var position = url.search("record");
            if (position < 0) {
                self.copyClientApprenantRel(data, container);
            }

        });

        $('input[name="lieu"]').on(Vtiger_Edit_Js.referenceSelectionEvent, function (e, data) {
            var locaux = $('select[name="cf_921"] option:selected').val();
            var idLieu = $('input[name="lieu"]').val();
            if (locaux == 'Au centre') {
                self.referenceSelectionAucentreHandler(data, container);
            }
            /* uni_cnfsecrm - v2 - modif 186 - DEBUT */
            self.fixTimeStartSessionByPlace();
            /* uni_cnfsecrm - v2 - modif 186 - FIN */
        });
    },

    copyClientApprenantRel: function (data, container) {
        var self = this;
        var idclient = data.record;
        var sourceModule = data['source_module'];
        this.getRecordDetailsClient(data).then(
                function (data) {
                    var responseApprenant = data;
                    var nbreApprenant = responseApprenant[0]['listApprenant'].length;
                    var detailClient = responseApprenant[0][idclient];
                    var i = 0;
                    for (i; i < nbreApprenant; i++) {
                        //self.addLineApprenant(responseApprenant[0]['listApprenant'][i], container, detailClient);
                    }
                },
                function (error, err) {

                });
    },

    referenceSelectionClientHandler: function (data, container) {
        var self = this;
        var message = app.vtranslate('OVERWRITE_EXISTING_CLIENT');
        app.helper.showConfirmationBox({'message': message}).then(
                function (e) {
                    self.copyClientDetails(data, container);
                },
                function (error, err) {
                });
    },

    addressFieldsMapping: {
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
    },

    copyClientDetails: function (data, container, addressMap) {
        console.log("copyClientDetails");
        var self = this;
        var idclient = data.record;
        var sourceModule = data['source_module'];
        console.log("sourceModule " + sourceModule);
        this.getRecordDetailsClient(data).then(
                function (data) {
                    var response = data;
                    self.mapClientDetails(self.addressFieldsMapping[sourceModule], response[0][idclient].id, container);
                    if (sourceModule == "Accounts") {
                        var adresse = response[0][idclient].adresse;
                        var codePostale = response[0][idclient].codePostale;
                        var ville = response[0][idclient].ville;
                        console.log("adresse " + adresse);
                        console.log("codePostale " + codePostale);

                        $("textarea[name='cf_933']").val(adresse);
                        $("input[name='cf_929']").val(codePostale);
                        $("input[name='cf_931']").val(ville);
                        $('select[name="cf_921"] option[value="Client"]').attr('selected', 'selected').change();
                    }
                },
                function (error, err) {

                });
    },

    getRecordDetailsClient: function (params) {
        var aDeferred = jQuery.Deferred();
        var url = "index.php?module=" + app.getModuleName() + "&action=GetClient&record=" + params['record'] + "&source_module=" + params['source_module'];
        app.request.get({'url': url}).then(
                function (error, data) {
                    if (error == null) {
                        aDeferred.resolve(data);
                    } else {
                        //aDeferred.reject(data['message']);
                    }
                },
                function (error) {
                    aDeferred.reject();
                }
        )
        return aDeferred.promise();
    },

    mapClientDetails: function (sessionDetails, result, container) {
        console.log(sessionDetails);
        for (var key in sessionDetails) {
            container.find('[name="' + key + '"]').val(result[sessionDetails[key]]);
            container.find('[name="' + key + '"]').trigger('change');
        }
    },

    /* function au centre */
    referenceSelectionAucentreHandler: function (data, container) {
        var self = this;
        var message = app.vtranslate('OVERWRITE_EXISTING_LIEU');
        app.helper.showConfirmationBox({'message': message}).then(
                function (e) {
                    self.copyAucentreDetails(data, container);
                },
                function (error, err) {
                });
    },

    copyAucentreDetails: function (data, container, addressMap) {
        var self = this;
        var idlieu = data.record;
        var sourceModule = data['source_module'];
        this.getRecordDetailsAucentre(data).then(
                function (data) {
                    var response = data;
                    self.mapAucentreDetails(self.addressFieldsMapping[sourceModule], response[0][idlieu].id, container);
                    if ((sourceModule == "Lieu") || (sourceModule == "lieu")) {
                        var adresse = response[0][idlieu].adresse;
                        var codePostale = response[0][idlieu].codePostale;
                        var ville = response[0][idlieu].ville;

                        //var salle = $("input[name='salle_display']").val();
                        $("textarea[name='cf_933']").val(adresse);
                        $("input[name='cf_929']").val(codePostale);
                        $("input[name='cf_931']").val(ville);
                    }
                },
                function (error, err) {

                });
    },

    getRecordDetailsAucentre: function (params) {
        var aDeferred = jQuery.Deferred();
        var url = "index.php?module=" + app.getModuleName() + "&action=GetAucentre&record=" + params['record'] + "&source_module=" + params['source_module'];
        app.request.get({'url': url}).then(
                function (error, data) {
                    if (error == null) {
                        aDeferred.resolve(data);
                    } else {
                        //aDeferred.reject(data['message']);
                    }
                },
                function (error) {
                    aDeferred.reject();
                }
        )
        return aDeferred.promise();
    },

    mapAucentreDetails: function (sessionDetails, result, container) {
        for (var key in sessionDetails) {
            container.find('[name="' + key + '"]').val(result[sessionDetails[key]]);
            container.find('[name="' + key + '"]').trigger('change');
        }
    },

    addLineApprenant: function (data, container, detailClient) {
        var self = this;
        var newLineItem = self.getNewLineClientApprenantRel();
        var sequenceNumber = self.getNextLineItemRowNumberApprenant();
        newLineItem = newLineItem.appendTo(self.lineItemsHolderApprenant);
        newLineItem.find('input.contactName').addClass('autoCompleteApprenant');
        newLineItem.find('.ignore-ui-registration').removeClass('ignore-ui-registration');
        vtUtils.applyFieldElementsView(newLineItem);
        app.event.trigger('post.lineItem.New', newLineItem);
        self.checkLineItemRowApprenant();
        newLineItem.find('input.rowNumberApprenant').val(sequenceNumber);
        self.registerLineItemAutoCompleteApprenant(newLineItem);
        if (typeof data != "undefined") {
            self.mapResultsClientApprenantRel(newLineItem, data, detailClient);
        }
    },

    getNewLineClientApprenantRel: function () {
        var listApprenants = $('#lineItemTabApprenant');
        var dernierLigne = $('#lineItemTabApprenant tr:last .contactName').val();
        if ((dernierLigne == '') || (dernierLigne == null)) {
            $('#lineItemTabApprenant tr:last').remove();
            var newRowNum = 1;
        } else {
            var newRowNum = this.getLineItemNextRowNumberApprenant();
        }
        var itemType = '';
        var newRow = this.dummyLineItemRowApprenant.clone(true).removeClass('hide').addClass(this.lineItemDetectingClassApprenant).removeClass('lineItemCloneCopyApprenant');
        newRow.find('.lineItemPopup').filter(':not([data-module-name="' + itemType + '"])').remove();
        newRow.find('.lineItemType').val(itemType);
        //var newRowNum = this.getLineItemNextRowNumberApprenant();
        this.updateRowNumberForRowApprenant(newRow, newRowNum);
        this.initializeLineItemRowCustomFields(newRow, newRowNum);
        return newRow
    },

    mapResultsClientApprenantRel: function (parentRow, responseData, detailClient) {
        var lineItemNameElmentApprenant = jQuery('input.contactName', parentRow);
        var referenceModuleApprenant = this.getLineItemSetypeApprenant(parentRow);
        var lineItemRowNumber = parentRow.data('rowNum');

        var selectedName = responseData.firstname + ' ' + responseData.lastname;
        var numclient = detailClient.numclient;
        var nomclient = detailClient.nomclient;
        var telephone = responseData.phone;
        var email = responseData.email;
        if (email == '' || email == null) {
            var email = detailClient.email;
        }
        jQuery('input.lineItemTypeApprenant', parentRow).val(referenceModuleApprenant);
        lineItemNameElmentApprenant.val(selectedName);
        lineItemNameElmentApprenant.attr('disabled', 'disabled');
        jQuery('input.numclient', parentRow).val(numclient);
        jQuery('input.nomclient', parentRow).val(nomclient);
        jQuery('input.telephone', parentRow).val(telephone);
        jQuery('input.email', parentRow).val(email);
    },

    /* unicnfsecrm_022020_09 - end */

    /* unicnfsecrm_mod_31 */
    registerCityAutoComplete: function (container) {
        $("input[name='cf_929']").autocomplete({
            source: function (request, response) {
                console.log(request.term);
                $.ajax({
                    url: "https://api-adresse.data.gouv.fr/search/?postcode=" + $("input[name='cf_929']").val(),
                    data: {q: request.term},
                    dataType: "json",
                    success: function (data) {
                        var postcodes = [];
                        response($.map(data.features, function (item) {
                            // Ici on est obligé d'ajouter les CP dans un array pour ne pas avoir plusieurs fois le même
                            if ($.inArray(item.properties.postcode, postcodes) == -1) {
                                postcodes.push(item.properties.postcode);
                                return {label: item.properties.postcode + " - " + item.properties.city,
                                    city: item.properties.city,
                                    value: item.properties.postcode,
                                    etat: item.properties.context
                                };
                            }
                        }));
                    }
                });

            },

            // On remplit aussi la ville
            select: function (event, ui) {
                var etat = ui.item.etat;
                etat = etat.split(', ')
                var region = etat[2];
                $("input[name='cf_931']").val(ui.item.city);
                $("input[name='cf_1195']").val(region);
            }
        });
    },
    /* uni_cnfsecrm - v2 - modif 126 */

    /* uni_cnfsecrm - v2 - modif 167 - DEBUT */
    getDateDebutFin: function () {
        var self = this;
        $('.date_start').on('change', function () {
            self.setDateSession();
        }),
                $('.start_matin').on('change', function () {
            self.setDateSession();
        })
        $('.end_apresmidi').on('change', function () {
            self.setDateSession();
        })
    },

    setDateSession: function () {
        var tabDate = [];
        $('#lineItemTabDate tbody').children().each(function () {
            if ($(this).find('.date_start').val() != '' && $(this).find('.date_start').val() != undefined && $(this).find('.date_start').attr('id') != 'date_start0') {
                tabDate.push([$(this).find('.date_start').val(), $(this).find('.start_matin').val(), $(this).find('.end_apresmidi').val()])
            }
        })
        $('#Events_editView_fieldName_date_start').val(tabDate[0][0])
        $('#Events_editView_fieldName_due_date').val(tabDate[tabDate.length - 1][0])
        $('#Events_editView_fieldName_time_start').val(tabDate[0][1])
        $('#Events_editView_fieldName_time_end').val(tabDate[tabDate.length - 1][2])
    },
    /* uni_cnfsecrm - v2 - modif 167 - FIN */
    /* uni_cnfsecrm - v2 - modif 126 */


    /* uni_cnfsecrm - v2 - modif 168 - DEBUT */
    updateRowNumberForRowDate: function (lineItemRowDate, expectedSequenceNumber, currentSequenceNumber) {
        console.log('updateRowNumberForRowDate')
        if (typeof currentSequenceNumber == 'undefined') {
            //by default there will zero current sequence number
            currentSequenceNumber = 0;
        }
        var d = new Date();
        var datestart = d.getDate() + "-" + (d.getMonth() + 1) + "-" + d.getFullYear();

        var idFields = new Array('date_start', 'start_matin', 'end_matin', 'start_apresmidi', 'end_apresmidi', 'duree_formation', 'lineItemType');

        var nameFields = new Array('');

        var expectedRowId = 'rowdate' + expectedSequenceNumber;
        for (var idIndex in idFields) {
            var elementId = idFields[idIndex];
            var actualElementId = elementId + currentSequenceNumber;
            var expectedElementId = elementId + expectedSequenceNumber;
            lineItemRowDate.find('#' + actualElementId).attr('id', expectedElementId)
                    .filter('[name="' + actualElementId + '"]').attr('name', expectedElementId);
        }

        for (var nameIndex in nameFields) {
            var elementName = nameFields[nameIndex];
            var actualElementName = elementName + currentSequenceNumber;
            var expectedElementName = elementName + expectedSequenceNumber;
            lineItemRowDate.find('[name="' + actualElementName + '"]').attr('name', expectedElementName);
        }

        lineItemRowDate.attr('id', expectedRowId).attr('data-row-num', expectedSequenceNumber);

        return lineItemRowDate;
    },

    updateLineItemElementByOrderDate: function () {
        console.log('updateLineItemElementByOrderDate')
        var self = this;
        var checkedDiscountElements = {};
        var lineItems = this.lineItemsHolderDate.find('tr.' + this.lineItemDetectingClassDate);

        console.log('lineItems')
        console.log(lineItems)
        lineItems.each(function (index, domElement) {
            var lineItemRow = jQuery(domElement);
            var actualRowId = lineItemRow.attr('id');
        });

        lineItems.each(function (index, domElement) {

            var lineItemRow = jQuery(domElement);
            var expectedRowIndex = (index + 1);
            var expectedRowId = 'rowdate' + expectedRowIndex;
            var actualRowId = lineItemRow.attr('id');
            if (expectedRowId != actualRowId) {
                var actualIdComponents = actualRowId.split('rowdate');
                console.log('tet01')
                self.updateRowNumberForRowDate(lineItemRow, expectedRowIndex, actualIdComponents[1]);
            }
        });
    },
    /* uni_cnfsecrm - v2 - modif 168 - FIN */

});
