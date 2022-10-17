/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("Needs_Edit_Js", {
    zeroDiscountType: 'zero',
    percentageDiscountType: 'percentage',
    directAmountDiscountType: 'amount',
    individualTaxType: 'individual',
    groupTaxType: 'group'
}, {
    //Container which stores the line item elements
    lineItemContentsContainer: false,
    //Container which stores line item result details
    lineItemResultContainer: false,
    //contains edit view form element
    editViewForm: false,
    //a variable which will be used to hold the sequence of the row
    rowSequenceHolder: false,
    rowSequenceListHolder: false,
    //holds the element which has basic hidden row which we can clone to add rows
    basicRow: false,
    basicListRow: false,
    customRow: false,
    //will be having class which is used to identify the rows
    rowClass: 'lineItemRow',
    rowListClass: 'lineItemTable',
    prevSelectedCurrencyConversionRate: false,
    /**
     * Function that is used to get the line item container
     * @return : jQuery object
     */
    getLineItemContentsContainer: function () {
        if (this.lineItemContentsContainer == false) {
            this.setLineItemContainer(jQuery('#lineItemTabglobal'));
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
     * Function to get the line item result container
     * @result : jQuery object which represent line item result container
     */
    getLineItemResultContainer: function () {
        if (this.lineItemResultContainer == false) {
            this.setLinteItemResultContainer(jQuery('#lineItemResult'));
        }
        return this.lineItemResultContainer;
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
     * Function which will give the closest line item row element
     * @return : jQuery object
     */
    getClosestLineItemRow: function (element) {
        return element.closest('tr.' + this.rowClass);
    },
    /**
     * Function which gives edit view form
     * @return : jQuery object which represents the form element
     */
    getForm: function () {
        if (this.editViewForm == false) {
            this.editViewForm = jQuery('#EditView');
        }
        return this.editViewForm;
    },
    /**
     * Function which will set the line item total value excluding tax and discount
     * @params : lineItemRow - row which represents the line item
     *			 lineItemTotalValue - value which has line item total  (qty*listprice)
     * @return : current instance;
     */
    setLineItemVolume: function (lineItemRow, lineItemVolumeValue) {
        jQuery('.v_obj', lineItemRow).val(lineItemVolumeValue);
        jQuery('.v_obj_txt', lineItemRow).text(lineItemVolumeValue);
        return this;
    },
    setLineItemTotAdefinir: function (lineItemTable, lineItemTotAdefinirValue) {
        jQuery('.tot_a_definir', lineItemTable).text(lineItemTotAdefinirValue);
        return this;
    },
    setLineItemTotChargement: function (lineItemTable, lineItemTotChargementValue) {
        jQuery('.tot_chargement', lineItemTable).text(lineItemTotChargementValue);
        return this;
    },
    setLineItemTotDechargement: function (lineItemTable, lineItemTotDechargementValue) {
        jQuery('.tot_dechargement', lineItemTable).text(lineItemTotDechargementValue);
        return this;
    },
    setLineItemTotDestruction: function (lineItemTable, lineItemTotDestructionValue) {
        jQuery('.tot_destruction', lineItemTable).text(lineItemTotDestructionValue);
        return this;
    },
    /**
     * Function which will get the value of line item total (qty*listprice)
     * @params : lineItemRow - row which represents the line item
     * @return : string
     */
    getLineItemTotal: function (lineItemRow) {
        return parseFloat(this.getLineItemTotalElement(lineItemRow).text());
    },
    /**
     * Function which will get the line item total element
     * @params : lineItemRow - row which represents the line item
     * @return : jQuery element
     */
    getLineItemTotalElement: function (lineItemRow) {
        return jQuery('.productTotal', lineItemRow);
    },
    /** wajmovadom DE */
    getActionObjValue: function (lineItemRow) {
        return jQuery('.action_obj', lineItemRow).val();
    },
    setActionObjValue: function (lineItemRow, action_objValue) {
        lineItemRow.find('.action_obj').val(action_objValue);
        return this;
    },
    getNomObjValue: function (lineItemRow) {
        return jQuery('.nom_obj', lineItemRow).val();
    },
    setNomObjValue: function (lineItemRow, nom_objValue) {
        lineItemRow.find('.nom_obj').val(nom_objValue);
        return this;
    },
    getPieceObjValue: function (lineItemRow) {
        return jQuery('.piece_obj', lineItemRow).val();
    },
    setPieceObjValue: function (lineItemRow, piece_objValue) {
        lineItemRow.find('.piece_obj').val(piece_objValue);
        return this;
    },
    getNbObjValue: function (lineItemRow) {
        return jQuery('.nb_obj', lineItemRow).val();
    },
    setNbObjValue: function (lineItemRow, nb_objValue) {
        lineItemRow.find('.nb_obj').val(nb_objValue);
        return this;
    },
    getDescriptionObjValue: function (lineItemRow) {
        return jQuery('.description_obj', lineItemRow).val();
    },
    setDescriptionObjValue: function (lineItemRow, description_objValue) {
        lineItemRow.find('.description_obj').val(description_objValue);
        return this;
    },
    getLObjValue: function (lineItemRow) {
        return this.VirguleToPoint(jQuery('.l_obj', lineItemRow).val());
    },
    setLObjValue: function (lineItemRow, l_objValue) {
        lineItemRow.find('.l_obj').val(l_objValue);
        return this;
    },
    getPObjValue: function (lineItemRow) {
        return this.VirguleToPoint(jQuery('.p_obj', lineItemRow).val());
    },
    setPObjValue: function (lineItemRow, p_objValue) {
        lineItemRow.find('.p_obj').val(p_objValue);
        return this;
    },
    getHObjValue: function (lineItemRow) {
        return this.VirguleToPoint(jQuery('.h_obj', lineItemRow).val());
    },
    setHObjValue: function (lineItemRow, h_objValue) {
        lineItemRow.find('.h_obj').val(h_objValue);
        return this;
    },
    getVObjValue: function (lineItemRow) {
        return this.VirguleToPoint(jQuery('.v_obj', lineItemRow).val());
    },
    setVObjValue: function (lineItemRow, v_objValue) {
        lineItemRow.find('.v_obj').val(v_objValue);
        return this;
    },
    getNeedLineItemHeaderValue: function (lineItemTable) {
        return jQuery('.needlineitemHeader', lineItemTable).val();
    },
    setNeedLineItemHeaderValue: function (lineItemTable, needlineitemHeaderValue) {
        lineItemTable.find('.needlineitemHeader').val(needlineitemHeaderValue);
        return this;
    },
    setTotalAdefinirValue: function (lineItemRow, v_totadefinirValue) {
        lineItemRow.find('.tot_a_definir').val(v_totadefinirValue);
        return this;
    },
    setTotalChargementValue: function (lineItemRow, v_chargementValue) {
        lineItemRow.find('.tot_chargement').val(v_chargementValue);
        return this;
    },
    setTotalDechargementValue: function (lineItemRow, v_totdechargementValue) {
        lineItemRow.find('.tot_dechargement').val(v_totdechargementValue);
        return this;
    },
    setTotalDestructionValue: function (lineItemRow, v_totdestructionValue) {
        lineItemRow.find('.tot_destruction').val(v_totdestructionValue);
        return this;
    },
    /** wajmovadom FN */

    VirguleToPoint: function (val_virgule) {
        return val_virgule.replace(",", ".");
    },

    isAdjustMentAddType: function () {
        var adjustmentSelectElement = this.getAdjustmentTypeElement();
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
        var adjustmentSelectElement = this.getAdjustmentTypeElement();
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
    loadRowSequenceNumber: function () {
        if (this.rowSequenceHolder == false) {
            this.rowSequenceHolder = jQuery('.' + this.rowClass, this.getLineItemContentsContainer()).length;
        }
        return this;
    },
    loadRowSequenceListNumber: function () {
        if (this.rowSequenceListHolder == false) {
            this.rowSequenceListHolder = jQuery('.' + this.rowListClass, this.getLineItemContentsContainer()).length;
        }
        return this;
    },
    getNextLineItemRowNumber: function () {
        if (this.rowSequenceHolder == false) {
            this.loadRowSequenceNumber();
        }
        return ++this.rowSequenceHolder;
    },
    getNextLineItemListRowNumber: function () {
        if (this.rowSequenceListHolder == false) {
            this.loadRowSequenceListNumber();
        }
        return ++this.rowSequenceListHolder;
    },
    /**
     * Function which will return the basic row which can be used to add new rows
     * @return jQuery object which you can use to
     */
    getBasicRow: function () {
        if (this.basicRow == false) {
            var lineItemTable = this.getLineItemContentsContainer();
            this.basicRow = jQuery('.lineItemCloneCopy', lineItemTable)
        }
        var newRow = this.basicRow.clone(true, true);
        return newRow.removeClass('hide lineItemCloneCopy');
    },
    getBasicListRow: function () {
        if (this.basicListRow == false) {
            var lineItemTable = this.getLineItemContentsContainer();
            this.basicListRow = jQuery('.lineItemListCloneCopy', lineItemTable)
        }
        var newRow = this.basicListRow.clone(true, true);
        return newRow.removeClass('hide lineItemListCloneCopy');
    },
    getCustomRow: function (lineItemRow) {
        var lineItemTable = this.getLineItemContentsContainer();
        this.customRow = jQuery('#' + lineItemRow, lineItemTable)
        var newRow = this.customRow.clone(true, true);
        return newRow;
    },
    registerAddingNewStandardObjects: function () {
        var thisInstance = this;
        var lineItemTable = this.getLineItemContentsContainer();
        jQuery('.addStandardObject').on('click', function () {
            var newRow = thisInstance.getBasicRow().addClass(thisInstance.rowClass);
            var sequenceNumber = thisInstance.getNextLineItemRowNumber();
            var id_bouton = this.id;
            var id_list = id_bouton.replace('addStandardObject', '');
            newRow = newRow.appendTo(jQuery('#lineItemTab' + id_list));
            thisInstance.checkLineItemRow();
            newRow.find('input.rowNumber').val(sequenceNumber);
            thisInstance.updateLineItemsElementWithSequenceNumber(newRow, sequenceNumber);
            newRow.find('input.nom_obj').addClass('autoComplete');
            thisInstance.registerLineItemAutoComplete(newRow);
        });
    },
    registerAddingNewListsStandardObjects: function () {
        var thisInstance = this;
        var lineItemTable = this.getLineItemContentsContainer();
        jQuery('.addListStandardObjects').on('click', function () {

            var newTable = thisInstance.getBasicListRow().addClass(thisInstance.rowListClass);
            var sequenceNumber = thisInstance.getNextLineItemListRowNumber();
            newTable = newTable.appendTo(lineItemTable);
            lineItemTable.append("<br/>");
            thisInstance.checkLineItemRow();
            jQuery('.dateFieldTemp', newTable).addClass('dateField');
            jQuery('.dateField', newTable).removeClass('dateFieldTemp');
            jQuery('.dateField', newTable).after(" <span class='add-on'><i class='icon-calendar'></i></span>");
            app.registerEventForDatePickerFields(jQuery('.dateField', newTable), true);
            thisInstance.updateLineItemsListElementWithSequenceNumber(newTable, sequenceNumber);

            var newRow = thisInstance.getBasicRow().addClass(thisInstance.rowClass);
            var sequenceNumber = thisInstance.getNextLineItemRowNumber();
            newRow = newRow.appendTo(newTable);
            thisInstance.checkLineItemRow();
            newRow.find('input.rowNumber').val(sequenceNumber);
            thisInstance.updateLineItemsElementWithSequenceNumber(newRow, sequenceNumber);
            newRow.find('input.nom_obj').addClass('autoComplete');
            thisInstance.registerLineItemAutoComplete(newRow);
        });
    },
    mapResultsToFields: function (referenceModule, element, responseData) {
        var parentRow = jQuery(element).closest('tr.' + this.rowClass);
        var lineItemNameElment = jQuery('input.nom_obj', parentRow);
        var lineItemNameElment_hidden = jQuery('input.nom_obj_hidden', parentRow);
        for (var id in responseData) {
            var recordId = id;
            var recordData = responseData[id];
            var selectedName = recordData.name;
            var standardobject_name = recordData["standardobject_name"];
            var standardobject_rooms = recordData["standardobject_rooms"];
            var standardobject_no = recordData["standardobject_no"];
            var standardobject_width = recordData["standardobject_width"];
            var standardobject_depth = recordData["standardobject_depth"];
            var standardobject_height = recordData["standardobject_height"];
            var standardobject_volume = recordData["standardobject_volume"];
            if (referenceModule == 'StandardObjects') {
            }
            jQuery('input.selectedModuleId', parentRow).val(recordId);
            jQuery('input.lineItemType', parentRow).val(referenceModule);
            lineItemNameElment.val(standardobject_name);
            lineItemNameElment_hidden.val(standardobject_name);
            lineItemNameElment.attr('disabled', 'disabled');
            this.setPieceObjValue(parentRow, standardobject_rooms);
            this.setLObjValue(parentRow, standardobject_width);
            this.setPObjValue(parentRow, standardobject_depth);
            this.setHObjValue(parentRow, standardobject_height);
            this.setVObjValue(parentRow, standardobject_volume);
        }
    },
    showPopup: function (params) {
        var aDeferred = jQuery.Deferred();
        var popupInstance = Vtiger_Popup_Js.getInstance();
        popupInstance.show(params, function (data) {
            aDeferred.resolve(data);
        });
        return aDeferred.promise();
    },
    /*
     * Function which is reposible to handle the line item popups
     * @params : popupImageElement - popup image element
     */
    lineItemPopupEventHandler: function (popupImageElement) {
        var aDeferred = jQuery.Deferred();
        var thisInstance = this;
        var referenceModule = popupImageElement.data('moduleName');
        var moduleName = app.getModuleName();
        //thisInstance.getModulePopUp(e,referenceModule);
        var params = {};
        params.view = popupImageElement.data('popup');
        params.module = moduleName;
        params.multi_select = true;

        this.showPopup(params).then(function (data) {
            var responseData = JSON.parse(data);
            var len = Object.keys(responseData).length;
            if (len > 1) {
                for (var i = 0; i < len; i++) {
                    if (i == 0) {
                        thisInstance.mapResultsToFields(referenceModule, popupImageElement, responseData[i]);
                    } else if (i >= 1) {
                        var parentRow = jQuery(popupImageElement).closest('table');
                        var row = jQuery('.addStandardObject', parentRow).trigger('click');
                        //TODO : CLEAN :  we might synchronus invocation since following elements needs to executed once new row is created
                        var newRow = parentRow.find("tr").last();
                        var targetElem = jQuery('.lineItemPopup', newRow);
                        thisInstance.mapResultsToFields(referenceModule, targetElem, responseData[i]);
                        aDeferred.resolve();
                    }
                }
            } else {
                thisInstance.mapResultsToFields(referenceModule, popupImageElement, responseData);
                aDeferred.resolve();
            }
        })
        return aDeferred.promise();
    },
    registerCloneStandardObjectSaveEvent: function () {
        var thisInstance = this;
        var lineItemTableGlobal = this.getLineItemContentsContainer();
        lineItemTableGlobal.on('click', '.lineItemcloneStandardObjectSave', function (e) {
            var element = jQuery(e.currentTarget);
            var lineItemPopupDiv = element.closest('.validCheck');
            var lineItemPopupDiv_id = lineItemPopupDiv.attr("id");
            var id_list = lineItemPopupDiv_id.replace('cloneStandardObject_div', '');
            var name_liste_objradio = 'liste_objradio' + id_list;
            var list_clone = $('input[name=' + name_liste_objradio + ']:checked', lineItemPopupDiv).val()
            var lineItemTabclone = $("#lineItemTab" + list_clone);
            var lineItemTable = element.closest('.lineItemTable');
            lineItemTable.find('.lineItemRow').each(function (index, domElement) {
                var domElementvar = jQuery(domElement);
                var rowcheckbox = domElementvar.find('.rowcheckbox');
                if (rowcheckbox.is(':checked'))
                {
                    var rowcheckbox_val = rowcheckbox.val();
                    var newRow = domElementvar.clone(true, true);
                    var sequenceNumber = thisInstance.getNextLineItemRowNumber();
                    newRow = newRow.appendTo(jQuery('#lineItemTab' + list_clone));
                    thisInstance.checkLineItemRow();
                    newRow.find('input.rowNumber').val(sequenceNumber);
                    thisInstance.updateLineItemsElementWithSequenceNumber(newRow, sequenceNumber, rowcheckbox_val, 'notempty');
                }
            });
            jQuery('.closeDiv').trigger('click');
        });
    },
    registerDeleteStandardObjectListSaveEvent: function () {
        var thisInstance = this;
        var lineItemTableGlobal = this.getLineItemContentsContainer();
        lineItemTableGlobal.on('click', '.deleteStandardObjectList', function (e) {
            var element = jQuery(e.currentTarget);
            var lineItemTable = element.closest('.lineItemTable');
            lineItemTable.find('.lineItemRow').each(function (index, domElement) {
                var domElementvar = jQuery(domElement);
                domElementvar.remove();
                thisInstance.checkLineItemRow();
                thisInstance.lineItemDeleteActions();
            });
            lineItemTable.remove();
        });
    },
    registerLineItemsPopUpCancelClickEvent: function () {
        var editForm = this.getForm();
        editForm.on('click', '.cancelLink', function () {
            jQuery('.closeDiv').trigger('click')
        })
    },
    registerActionObjChangeEvent: function () {
        var lineItemResultTab = this.getLineItemResultContainer();
        var thisInstance = this;

        jQuery('.action_obj').on('change', function (e) {
            var element = jQuery(e.currentTarget);
            var action_obj_val = element.val();
            var actionobj_id = element.attr("id");
            jQuery("#" + actionobj_id + " option").removeProp("selected");
            jQuery("#" + actionobj_id + " option[value='" + action_obj_val + "']").attr('selected', 'selected');
        });
    },
    calculateVolumeobj: function (lineItemRow)
    {
        var l_obj = this.getLObjValue(lineItemRow);
        var p_obj = this.getPObjValue(lineItemRow);
        var h_obj = this.getHObjValue(lineItemRow);
        var nb_obj = this.getNbObjValue(lineItemRow);
        var lineItemVolume = parseFloat(l_obj) * parseFloat(p_obj) * parseFloat(h_obj) * parseFloat(nb_obj);
        lineItemVolume = parseFloat(lineItemVolume).toFixed(2);
        this.setLineItemVolume(lineItemRow, lineItemVolume);
    },
    calculateActionsTot: function (lineItemTable)
    {
        var thisInstance = this
        var Adefinir_total = 0;
        var Chargement_total = 0;
        var Dechargement_total = 0;
        var Destruction_total = 0;
        lineItemTable.find('tr.' + this.rowClass).each(function (index, domElement) {
            var lineItemRow = jQuery(domElement);
            action_obj = thisInstance.getActionObjValue(lineItemRow);
            switch (action_obj)
            {
                case 'A_definir' :
                    Adefinir_total += parseFloat(thisInstance.getVObjValue(lineItemRow));
                    break;

                case 'Chargement' :
                    Chargement_total += parseFloat(thisInstance.getVObjValue(lineItemRow));
                    break;

                case 'Dechargement' :
                    Dechargement_total += parseFloat(thisInstance.getVObjValue(lineItemRow));
                    break;

                case 'Destruction' :
                    Destruction_total += parseFloat(thisInstance.getVObjValue(lineItemRow));
                    break;
            }
        });
        Adefinir_total = parseFloat(Adefinir_total).toFixed(2);
        Chargement_total = parseFloat(Chargement_total).toFixed(2);
        Dechargement_total = parseFloat(Dechargement_total).toFixed(2);
        Destruction_total = parseFloat(Destruction_total).toFixed(2);
        this.setLineItemTotAdefinir(lineItemTable, Adefinir_total);
        this.setLineItemTotChargement(lineItemTable, Chargement_total);
        this.setLineItemTotDechargement(lineItemTable, Dechargement_total);
        this.setLineItemTotDestruction(lineItemTable, Destruction_total);
    },
    lineItemResultActions: function () {
        var thisInstance = this;
        var lineItemResultTab = this.getLineItemResultContainer();

        this.registerLineItemsPopUpCancelClickEvent();
        this.registerActionObjChangeEvent();
        lineItemResultTab.on('click', '.closeDiv', function (e) {
            jQuery(e.target).closest('div').addClass('hide');
        });
    },
    lineItemToTalResultCalculations: function () {
    },
    lineItemRowCalculations: function (lineItemRow) {
        this.calculateVolumeobj(lineItemRow);
//        this.calculateTotAdefinir(lineItemRow);
//        this.calculateTotChargement(lineItemRow);
//        this.calculateTotDechargement(lineItemRow);
//        this.calculateTotDestruction(lineItemRow);
    },
    lineItemActionsTotCalculations: function (lineItemTable) {
        this.calculateActionsTot(lineItemTable);
//        this.calculateTotAdefinir(lineItemRow);
//        this.calculateTotChargement(lineItemRow);
//        this.calculateTotDechargement(lineItemRow);
//        this.calculateTotDestruction(lineItemRow);
    },
    /**
     * Function which will handle the actions that need to be preformed once the qty is changed like below
     *  - calculate line item total -> discount and tax -> net price of line item -> grand total
     * @params : lineItemRow - element which will represent lineItemRow
     */
    nbobjChangeActions: function (lineItemRow) {
        this.lineItemRowCalculations(lineItemRow);
        var parent_table = lineItemRow.closest('table');
        this.lineItemActionsTotCalculations(parent_table);
    },
    lobjChangeActions: function (lineItemRow) {
        this.lineItemRowCalculations(lineItemRow);
        var parent_table = lineItemRow.closest('table');
        this.lineItemActionsTotCalculations(parent_table);
    },
    pobjChangeActions: function (lineItemRow) {
        this.lineItemRowCalculations(lineItemRow);
        var parent_table = lineItemRow.closest('table');
        this.lineItemActionsTotCalculations(parent_table);
    },
    hobjChangeActions: function (lineItemRow) {
        this.lineItemRowCalculations(lineItemRow);
        var parent_table = lineItemRow.closest('table');
        this.lineItemActionsTotCalculations(parent_table);
    },
    calculateLoading: function ()
    {
        var thisInstance = this
        var lineItemTableGlobal = this.getLineItemContentsContainer();
        lineItemTableGlobal.find('table.' + this.rowListClass).each(function (index, domElement) {
            var lineItemTable = jQuery(domElement);
            thisInstance.lineItemActionsTotCalculations(lineItemTable);
        });
    },
    lineItemDiscountChangeActions: function (lineItemRow) {
        this.lineItemToTalResultCalculations();
    },
    lineItemDeleteActions: function () {
        this.lineItemToTalResultCalculations();
    },
    hideLineItemPopup: function () {
        var editForm = this.getForm();
        var popUpElementContainer = jQuery('.popupTable', editForm).closest('div');
        if (popUpElementContainer.length > 0) {
            popUpElementContainer.addClass('hide');
        }
    },
    /**
     * Function which will regiser events for product and service popup
     */
    registerStandardObjectPopup: function () {
        var thisInstance = this;
        var lineItemTable = this.getLineItemContentsContainer();
        lineItemTable.on('click', 'img.lineItemPopup', function (e) {
            var element = jQuery(e.currentTarget);
            thisInstance.lineItemPopupEventHandler(element).then(function (data) {
                var parent = element.closest('tr');
                var deletedItemInfo = parent.find('.deletedItem');
                if (deletedItemInfo.length > 0) {
                    deletedItemInfo.remove();
                }
            })
        });
    },
    isStandardObjectSelected: function (element) {
        var parentRow = element.closest('tr');
        var productField = parentRow.find('.productName');
        var response = productField.validationEngine('validate');
        return response;
    },
    registerDeleteLineItemEvent: function () {
        var thisInstance = this;
        var lineItemTable = this.getLineItemContentsContainer();

        lineItemTable.on('click', '.deleteRow', function (e) {
            var element = jQuery(e.currentTarget);
            //removing the row
            element.closest('tr.' + thisInstance.rowClass).remove();
            thisInstance.checkLineItemRow();
            thisInstance.lineItemDeleteActions();
        });
    },
    registerNbobjChangeEventHandler: function () {
        var thisInstance = this;
        var lineItemTable = this.getLineItemContentsContainer();

        lineItemTable.on('focusout', '.nb_obj', function (e) {
            var element = jQuery(e.currentTarget);
            var lineItemRow = element.closest('tr.' + thisInstance.rowClass);

            thisInstance.nbobjChangeActions(lineItemRow);
        });
    },
    registerLobjChangeEventHandler: function () {
        var thisInstance = this;
        var lineItemTable = this.getLineItemContentsContainer();

        lineItemTable.on('focusout', '.l_obj', function (e) {
            var element = jQuery(e.currentTarget);
            var lineItemRow = element.closest('tr.' + thisInstance.rowClass);

            thisInstance.lobjChangeActions(lineItemRow);
        });
    },
    registerPobjChangeEventHandler: function () {
        var thisInstance = this;
        var lineItemTable = this.getLineItemContentsContainer();

        lineItemTable.on('focusout', '.p_obj', function (e) {
            var element = jQuery(e.currentTarget);
            var lineItemRow = element.closest('tr.' + thisInstance.rowClass);

            thisInstance.pobjChangeActions(lineItemRow);
        });
    },
    registerHobjChangeEventHandler: function () {
        var thisInstance = this;
        var lineItemTable = this.getLineItemContentsContainer();

        lineItemTable.on('focusout', '.h_obj', function (e) {
            var element = jQuery(e.currentTarget);
            var lineItemRow = element.closest('tr.' + thisInstance.rowClass);

            thisInstance.hobjChangeActions(lineItemRow);
        });
    },
    registerCloneStandardObjectShowEvent: function () {
        var thisInstance = this;
        var lineItemTableGlobal = this.getLineItemContentsContainer();
        jQuery('.cloneStandardObject').on('click', function (e) {
            var element = jQuery(e.currentTarget);
            var lineItemTable = element.closest('table.' + thisInstance.rowListClass);
            thisInstance.hideLineItemPopup();

            cloneStandardObjectTable = lineItemTable.find('.cloneStandardObjectTable');
            cloneStandardObjectTable_id = cloneStandardObjectTable.attr("id");
            var id_cloneStandardObjectTable = cloneStandardObjectTable_id.replace('cloneStandardObjectTable', '');
            cloneStandardObjectTable.empty();
            newRow = '<tr><th id="cloneStandardObject_div_title' + id_cloneStandardObjectTable + '" colspan="2" nowrap align="left" >Listes d\'objets</th><th align="right"><button type="button" class="close closeDiv">x</button></th></tr>';
            cloneStandardObjectTable.append(newRow);
            lineItemTableGlobal.find('table.' + thisInstance.rowListClass).each(function (index, domElement) {
                var lineItemTablecur = jQuery(domElement);
                id_list = lineItemTablecur.attr("id");
                var id_list = id_list.replace('lineItemTab', '');
                if (id_list != 0)
                {
                    needLineItemHeader = thisInstance.getNeedLineItemHeaderValue(lineItemTablecur);
                    $('#' + cloneStandardObjectTable.attr('id') + ' tr:last').after('<tr><td align="left" class="lineOnTop"><input type="radio" size="5" name="liste_objradio' + id_cloneStandardObjectTable + '" value="' + id_list + '"/></td><td align="center" class="lineOnTop"><div class="textOverflowEllipsis">' + needLineItemHeader + '</div></td></tr>');
                }
            });
            lineItemTable.find('.cloneStandardObjectUI').removeClass('hide');
        });
    },
    lineItemActions: function () {
        var lineItemTable = this.getLineItemContentsContainer();

        //this.registerLineItemAutoComplete();
        this.registerClearLineItemSelection();

        this.registerStandardObjectPopup();
        this.registerDeleteLineItemEvent();
        this.registerNbobjChangeEventHandler();
        this.registerLobjChangeEventHandler();
        this.registerPobjChangeEventHandler();
        this.registerHobjChangeEventHandler();
        this.registerCloneStandardObjectShowEvent();
        this.registerCloneStandardObjectSaveEvent();
        this.registerDeleteStandardObjectListSaveEvent();
        lineItemTable.on('click', '.closeDiv', function (e) {
            jQuery(e.currentTarget).closest('div').addClass('hide');
        });

    },
    /***
     * Function which will update the line item row elements with the sequence number
     * @params : lineItemRow - tr line item row for which the sequence need to be updated
     *			 currentSequenceNUmber - existing sequence number that the elments is having
     *			 expectedSequenceNumber - sequence number to which it has to update
     *
     * @return : row element after changes
     */
    updateLineItemsElementWithSequenceNumber: function (lineItemRow, expectedSequenceNumber, currentSequenceNumber, typeupdate) {
        if (typeof currentSequenceNumber == 'undefined') {
            //by default there will zero current sequence number
            currentSequenceNumber = 0;
        }
        if (typeupdate != 'notempty')
        {
            jQuery('input.action_obj', lineItemRow).val('A définir');
            jQuery('input.nom_obj', lineItemRow).val('');
            jQuery('input.nom_obj_hidden', lineItemRow).val('');
            jQuery('input.piece_obj', lineItemRow).val('');
            jQuery('input.nb_obj', lineItemRow).val('');
            jQuery('input.description_obj', lineItemRow).val('');
            jQuery('input.l_obj', lineItemRow).val('');
            jQuery('input.p_obj', lineItemRow).val('');
            jQuery('input.h_obj', lineItemRow).val('');
            jQuery('input.v_obj', lineItemRow).val('');
        }
        var idFields = new Array('standardobject_name', 'standardobject_name_hidden', 'standardobjectid', 'lineItemType', 'standardobject_action', 'standardobject_rooms',
                'standardobject_no', 'standardobject_description', 'standardobject_width', 'standardobject_depth', 'standardobject_height',
                'standardobject_volume', 'addStandardObject', 'listobj_num');

        var nameFields = new Array('');
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

        var expectedRowId = 'row' + expectedSequenceNumber;
        for (var idIndex in idFields) {
            var elementId = idFields[idIndex];
            var actualElementId = elementId + currentSequenceNumber;
            var expectedElementId = elementId + expectedSequenceNumber;
            lineItemRow.find('#' + actualElementId).attr('id', expectedElementId)
                    .filter('[name="' + actualElementId + '"]').attr('name', expectedElementId);
        }

        for (var nameIndex in nameFields) {
            var elementName = nameFields[nameIndex];
            var actualElementName = elementName + currentSequenceNumber;
            var expectedElementName = elementName + expectedSequenceNumber;
            lineItemRow.find('[name="' + actualElementName + '"]').attr('name', expectedElementName);
        }

        var parent_table = lineItemRow.closest('table');
        var id_list = parent_table.find('.listobj_id').val();
        lineItemRow.find('.listobj_num').val(id_list);

        return lineItemRow.attr('id', expectedRowId);
    },
    updateLineItemsListElementWithSequenceNumber: function (lineItemRow, expectedSequenceNumber, currentSequenceNumber) {
        if (typeof currentSequenceNumber == 'undefined') {
            //by default there will zero current sequence number
            currentSequenceNumber = 0;
        }
        jQuery('input.action_obj', lineItemRow).val('A définir');
        var idFields = new Array('addStandardObject', 'lineItemTab', 'listobj_id', 'Needs_editView_fieldName_date_start', 'Needs_editView_fieldName_time_start', 'Needs_editView_fieldName_due_date', 'Needs_editView_fieldName_time_end', 'place_no', 'cloneStandardObject');

        var nameFields = new Array('');
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

        var expectedTableId = 'lineItemTab' + expectedSequenceNumber;
        for (var idIndex in idFields) {
            var elementId = idFields[idIndex];
            var actualElementId = elementId + currentSequenceNumber;
            var expectedElementId = elementId + expectedSequenceNumber;
            lineItemRow.find('#' + actualElementId).attr('id', expectedElementId)
                    .filter('[name="' + actualElementId + '"]').attr('name', expectedElementId);
        }

        var place_no_display_actual = 'place_no' + currentSequenceNumber + '_display';
        var place_no_display_expected = "place_no" + expectedSequenceNumber + "_display";
        lineItemRow.find('#' + place_no_display_actual).attr('id', place_no_display_expected)
                .filter('[name="' + place_no_display_actual + '"]').attr('name', place_no_display_expected);

        var Needs_editView_fieldName_place_no_select_actual = 'Needs_editView_fieldName_place_no' + currentSequenceNumber + '_select';
        var Needs_editView_fieldName_place_no_select_expected = "Needs_editView_fieldName_place_no" + expectedSequenceNumber + "_select";
        lineItemRow.find('#' + Needs_editView_fieldName_place_no_select_actual).attr('id', Needs_editView_fieldName_place_no_select_expected)
                .filter('[name="' + Needs_editView_fieldName_place_no_select_actual + '"]').attr('name', Needs_editView_fieldName_place_no_select_expected);

        var Needs_editView_fieldName_place_no_create_actual = 'Needs_editView_fieldName_place_no' + currentSequenceNumber + '_create';
        var Needs_editView_fieldName_place_no_create_expected = "Needs_editView_fieldName_place_no" + expectedSequenceNumber + "_create";
        lineItemRow.find('#' + Needs_editView_fieldName_place_no_create_actual).attr('id', Needs_editView_fieldName_place_no_create_expected)
                .filter('[name="' + Needs_editView_fieldName_place_no_create_actual + '"]').attr('name', Needs_editView_fieldName_place_no_create_expected);

        for (var nameIndex in nameFields) {
            var elementName = nameFields[nameIndex];
            var actualElementName = elementName + currentSequenceNumber;
            var expectedElementName = elementName + expectedSequenceNumber;
            lineItemRow.find('[name="' + actualElementName + '"]').attr('name', expectedElementName);
        }

        lineItemRow.find('.listobj_id').val(expectedSequenceNumber);

        return lineItemRow.attr('id', expectedTableId);
    },
    updateLineItemElementByOrder: function () {
        var lineItemContentsContainer = this.getLineItemContentsContainer();
        var thisInstance = this;
        jQuery('tr.' + this.rowClass, lineItemContentsContainer).each(function (index, domElement) {
            var lineItemRow = jQuery(domElement);
            var expectedRowIndex = (index + 1);
            var expectedRowId = 'row' + expectedRowIndex;
            var actualRowId = lineItemRow.attr('id');
            if (expectedRowId != actualRowId) {
                var actualIdComponents = actualRowId.split('row');
                thisInstance.updateLineItemsElementWithSequenceNumber(lineItemRow, expectedRowIndex, actualIdComponents[1], 'notempty');
            }
        });
    },
    saveStandardObjectCount: function () {
        jQuery('#totalStandardObjectsCount').val(jQuery('tr.' + this.rowClass, this.getLineItemContentsContainer()).length);
    },
    makeLineItemsSortable: function () {
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
    registerSubmitEvent: function () {
        var thisInstance = this;
        var editViewForm = this.getForm();
        this._super();
        editViewForm.submit(function (e) {
            var deletedItemInfo = jQuery('.deletedItem', editViewForm);
            if (deletedItemInfo.length > 0) {
                e.preventDefault();
                var msg = app.vtranslate('JS_PLEASE_REMOVE_LINE_ITEM_THAT_IS_DELETED');
                var params = {
                    text: msg,
                    type: 'error'
                }
                Vtiger_Helper_Js.showPnotify(params);
                editViewForm.removeData('submit');
                return false;
            }
            thisInstance.updateLineItemElementByOrder();
            var lineItemTable = thisInstance.getLineItemContentsContainer();
            jQuery('.discountSave', lineItemTable).trigger('click');
            thisInstance.saveStandardObjectCount();
        })
    },
    /**
     * Function which will register event for Reference Fields Selection
     */
    registerReferenceSelectionEvent: function (container) {
        var thisInstance = this;

        jQuery('input[name="contact_id"]', container).on(Vtiger_Edit_Js.referenceSelectionEvent, function (e, data) {
            thisInstance.referenceSelectionEventHandler(data, container);
        });
    },
    /**
     * Reference Fields Selection Event Handler
     */
    referenceSelectionEventHandler: function (data, container) {
        var thisInstance = this;
        var message = app.vtranslate('OVERWRITE_EXISTING_MSG1') + app.vtranslate('SINGLE_' + data['source_module']) + ' (' + data['selectedName'] + ') ' + app.vtranslate('OVERWRITE_EXISTING_MSG2');
        Vtiger_Helper_Js.showConfirmationBox({'message': message}).then(
                function (e) {
                    thisInstance.copyAddressDetails(data, container);
                },
                function (error, err) {
                });
    },
    registerLineItemAutoComplete: function (container) {
        var thisInstance = this;
        if (typeof container == 'undefined') {
            container = thisInstance.getLineItemContentsContainer();
        }
        container.find('input.autoComplete').autocomplete({
            'minLength': '3',
            'source': function (request, response) {
                //element will be array of dom elements
                //here this refers to auto complete instance
                var inputElement = jQuery(this.element[0]);
                var trElement = inputElement.closest('tr');
                var searchValue = request.term;
                var params = {};
                var searchModule = trElement.find('.lineItemPopup').data('moduleName');
                params.search_module = searchModule;
                params.search_value = searchValue;
                thisInstance.searchModuleNames(params).then(function (data) {
                    var reponseDataList = new Array();
                    var serverDataFormat = data.result;
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
                var trElement = element.closest('tr');
                var selectedModule = trElement.find('.lineItemPopup').data('moduleName');
                var popupElement = trElement.find('.lineItemPopup');
                var dataUrl = "index.php?module=Inventory&action=GetTnfos&record=" + selectedItemData.id;
                AppConnector.request(dataUrl).then(
                        function (data) {
                            for (var id in data) {
                                if (typeof data[id] == "object") {
                                    var recordData = data[id];
                                    thisInstance.mapResultsToFields(selectedModule, popupElement, recordData);
                                }
                            }
                        },
                        function (error, err) {

                        }
                );
            },
            'change': function (event, ui) {
                var element = jQuery(this);
                //if you dont have disabled attribute means the user didnt select the item
//                if (element.attr('disabled') == undefined) {
//                    element.closest('tr').find('.clearLineItem').trigger('click');
//                }
            }
        });
    },
    registerClearLineItemSelection: function () {
        var thisInstance = this;
        var lineItemTable = this.getLineItemContentsContainer();
        lineItemTable.on('click', '.clearLineItem', function (e) {
            var elem = jQuery(e.currentTarget);
            var parentElem = elem.closest('tr');
            thisInstance.clearLineItemDetails(parentElem);
            parentElem.find('input.nom_obj').removeAttr('disabled').val('');
            e.preventDefault();
        });
    },
    clearLineItemDetails: function (parentElem) {
        var thisInstance = this;
        var lineItemRow = parentElem.closest('tr.' + thisInstance.rowClass);
        jQuery('input.action_obj', lineItemRow).val('A définir');
        jQuery('input.nom_obj', lineItemRow).val('');
        jQuery('input.nom_obj_hidden', lineItemRow).val('');
        jQuery('input.piece_obj', lineItemRow).val('');
        jQuery('input.nb_obj', lineItemRow).val('');
        jQuery('input.description_obj', lineItemRow).val('');
        jQuery('input.l_obj', lineItemRow).val('');
        jQuery('input.p_obj', lineItemRow).val('');
        jQuery('input.h_obj', lineItemRow).val('');
        jQuery('input.v_obj', lineItemRow).val('');
        jQuery('input.selectedModuleId', lineItemRow).val('');
    },
    checkLineItemRow: function () {
        var lineItemTable = this.getLineItemContentsContainer();
        var noRow = lineItemTable.find('.lineItemRow').length;
        if (noRow > 1) {
            this.showLineItemsDeleteIcon();
        } else {
            this.hideLineItemsDeleteIcon();
        }
    },
    showLineItemsDeleteIcon: function () {
        var lineItemTable = this.getLineItemContentsContainer();
        lineItemTable.find('.deleteRow').show();
    },
    hideLineItemsDeleteIcon: function () {
        var lineItemTable = this.getLineItemContentsContainer();
        lineItemTable.find('.deleteRow').hide();
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
    /**
     * Function which will register all the events
     */
    registerBasicEvents: function (container) {
        this._super(container);
        this.registerReferenceSelectionEvent(container);
    },
    registerEvents: function () {
        this._super();
        this.registerAddingNewStandardObjects();
        this.registerAddingNewListsStandardObjects();
        this.lineItemActions();
        this.calculateLoading();
        this.lineItemResultActions();
        this.makeLineItemsSortable();
        this.checkLineItemRow();
    }
});
