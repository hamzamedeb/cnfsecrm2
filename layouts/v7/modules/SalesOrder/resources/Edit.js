/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Inventory_Edit_Js("SalesOrder_Edit_Js", {}, {

    /**
     * Function to get popup params
     */
    getPopUpParams: function (container) {
        var params = this._super(container);
        var sourceFieldElement = jQuery('input[class="sourceField"]', container);
        if (!sourceFieldElement.length) {
            sourceFieldElement = jQuery('input.sourceField', container);
        }

        if (sourceFieldElement.attr('name') == 'contact_id' || sourceFieldElement.attr('name') == 'potential_id') {
            var form = this.getForm();
            var parentIdElement = form.find('[name="account_id"]');
            if (parentIdElement.length > 0 && parentIdElement.val().length > 0 && parentIdElement.val() != 0) {
                var closestContainer = parentIdElement.closest('td');
                params['related_parent_id'] = parentIdElement.val();
                params['related_parent_module'] = closestContainer.find('[name="popupReferenceModule"]').val();
            } else if (sourceFieldElement.attr('name') == 'potential_id') {
                parentIdElement = form.find('[name="contact_id"]');
                if (parentIdElement.length > 0 && parentIdElement.val().length > 0) {
                    closestContainer = parentIdElement.closest('td');
                    params['related_parent_id'] = parentIdElement.val();
                    params['related_parent_module'] = closestContainer.find('[name="popupReferenceModule"]').val();
                }
            }
        }
        return params;
    },

    /**
     * Function to register event for enabling recurrence
     * When recurrence is enabled some of the fields need
     * to be check for mandatory validation
     */
    registerEventForEnablingRecurrence: function () {
        var thisInstance = this;
        var form = this.getForm();
        var enableRecurrenceField = form.find('[name="enable_recurring"]');
        var fieldNamesForValidation = new Array('recurring_frequency', 'start_period', 'end_period', 'payment_duration', 'invoicestatus');
        var selectors = new Array();
        for (var index in fieldNamesForValidation) {
            selectors.push('[name="' + fieldNamesForValidation[index] + '"]');
        }
        var selectorString = selectors.join(',');
        var validationToggleFields = form.find(selectorString);
        enableRecurrenceField.on('change', function (e) {
            var element = jQuery(e.currentTarget);
            var addValidation;
            if (element.is(':checked')) {
                addValidation = true;
            } else {
                addValidation = false;
            }

            //If validation need to be added for new elements,then we need to detach and attach validation
            //to form
            if (addValidation) {
                thisInstance.AddOrRemoveRequiredValidation(validationToggleFields, true);
            } else {
                thisInstance.AddOrRemoveRequiredValidation(validationToggleFields, false);
            }
        })
        if (!enableRecurrenceField.is(":checked")) {
            thisInstance.AddOrRemoveRequiredValidation(validationToggleFields, false);
        } else if (enableRecurrenceField.is(":checked")) {
            thisInstance.AddOrRemoveRequiredValidation(validationToggleFields, true);
        }
    },

    AddOrRemoveRequiredValidation: function (dependentFieldsForValidation, addValidation) {
        jQuery(dependentFieldsForValidation).each(function (key, value) {
            var relatedField = jQuery(value);
            if (addValidation) {
                relatedField.removeClass('ignore-validation').data('rule-required', true);
                if (relatedField.is("select")) {
                    relatedField.attr('disabled', false);
                } else {
                    relatedField.removeAttr('disabled');
                }
            } else if (!addValidation) {
                relatedField.addClass('ignore-validation').removeAttr('data-rule-required');
                if (relatedField.is("select")) {
                    relatedField.attr('disabled', true).trigger("change");
                    var select2Element = app.helper.getSelect2FromSelect(relatedField);
                    select2Element.trigger('Vtiger.Validation.Hide.Messsage');
                    select2Element.find('a').removeClass('input-error');
                } else {
                    relatedField.attr('disabled', 'disabled').trigger('Vtiger.Validation.Hide.Messsage').removeClass('input-error');
                }
            }
        });
    },

    /**
     * Function to search module names
     */
    searchModuleNames: function (params) {
        var aDeferred = jQuery.Deferred();
        if (typeof params.module == 'undefined') {
            params.module = app.getModuleName();
        }
        if (typeof params.action == 'undefined') {
            params.action = 'BasicAjax';
        }

        if (typeof params.base_record == 'undefined') {
            var record = jQuery('[name="record"]');
            var recordId = app.getRecordId();
            if (record.length) {
                params.base_record = record.val();
            } else if (recordId) {
                params.base_record = recordId;
            } else if (app.view() == 'List') {
                var editRecordId = jQuery('#listview-table').find('tr.listViewEntries.edited').data('id');
                if (editRecordId) {
                    params.base_record = editRecordId;
                }
            }
        }

        // Added for overlay edit as the module is different
        if (params.search_module == 'Products' || params.search_module == 'Services') {
            params.module = 'SalesOrder';
        }

        app.request.get({'data': params}).then(
                function (error, data) {
                    if (error == null) {
                        aDeferred.resolve(data);
                    }
                },
                function (error) {
                    aDeferred.reject();
                }
        )
        return aDeferred.promise();
    },

    /**
     * Function which will register event for Reference Fields Selection
     */
    registerReferenceSelectionEvent: function (container) {
        this._super(container);
        var self = this;

        jQuery('input[name="account_id"]', container).on(Vtiger_Edit_Js.referenceSelectionEvent, function (e, data) {
            var locaux = $('select[name="cf_860"] option:selected').val();
            var idClient = $('input[name="account_id"]').val();
            if ((locaux == 'Chez le client') && (idClient != 'undefined') && (idClient != '')) {
                self.referenceSelectionEventHandler(data, container);
            }
        });
    },
    registerBasicEvents: function (container) {
        this._super(container);
        this.registerEventForEnablingRecurrence();
        this.registerForTogglingBillingandShippingAddress();
        //this.registerEventForCopyAddress();
        this.registerReferenceSelectionSessionDetail();
        container.find('[name="bill_street"]').parent('td').css('pointer-events', 'none');
        container.find('[name="bill_street"]').css('background-color', '#DEDEDE');
        container.find('[name="cf_973"]').parent('td').css('pointer-events', 'none');
        container.find('[name="cf_973"]').css('background-color', '#DEDEDE');
        container.find('[name="bill_code"]').parent('td').css('pointer-events', 'none');
        container.find('[name="bill_code"]').css('background-color', '#DEDEDE');
        container.find('[name="bill_city"]').parent('td').css('pointer-events', 'none');
        container.find('[name="bill_city"]').css('background-color', '#DEDEDE');
        container.find('[name="bill_state"]').parent('td').css('pointer-events', 'none');
        container.find('[name="bill_state"]').css('background-color', '#DEDEDE');
        container.find('[name="bill_country"]').parent('td').css('pointer-events', 'none');
        container.find('[name="bill_country"]').css('background-color', '#DEDEDE');
        /* uni_cnfsecrm - v2 - modif 125 - DEBUT */
        this.updateTypeSession();
        /* uni_cnfsecrm - v2 - modif 125 - FIN */
    },

    /* unicnfsecrm_022020_26 - debut */
    registerReferenceSelectionSessionDetail: function () {
        console.log('test 1')
        var self = this;
        var container = $('.recordEditView');
        jQuery('input[name="session"]', container).on(Vtiger_Edit_Js.referenceSelectionEvent, function (e, data) {
            console.log('data')
            console.log(data)
            self.referenceSelectionSessionHandlerDetail(data, container);
        });
    },

    referenceSelectionSessionHandlerDetail: function (data, container) {
        var self = this;
        if (data['selectedName']) {
            var message = app.vtranslate('OVERWRITE_EXISTING_SESSION') + ' (' + data['selectedName'] + ') ';
            app.helper.showConfirmationBox({'message': message}).then(
                    function (e) {
                        self.copySessionDetails(data, container);
                    },
                    function (error, err) {
                    });
        }
    },
    copySessionDetails: function (data, container, addressMap) {
        var self = this;
        var idSession = data.record;
        console.log('idSession');
        console.log(idSession);
        //test detail session
        var dataUrlSession = "index.php?module=SalesOrder&action=GetDetailSession&record=" + idSession;
        app.request.get({'url': dataUrlSession}).then(
                function (error, dataSession) {
                    if (error == null) {
                        console.log('dataSession')
                        console.log(dataSession)
                        //appel au fichier de detail formation
                        var formation = dataSession['formation'];
                        var type = dataSession['type'];
                        var nbrJours = dataSession['nbrJours'];
                        var nbrHeures = dataSession['nbrHeures'];
                        self.detailformation(formation, type, nbrJours, nbrHeures);
                        //fin appel au fichier de detail formation
                        var dateStart = dataSession['dateStart'];
                        var dateFin = dataSession['dueDate'];

                        var adresse = dataSession['adresse'];
                        var ville = dataSession['ville'];
                        var cp = dataSession['cp'];
                        var idsalle = dataSession['idsalle'];
                        var idlieu = dataSession['idlieu'];
                        var nomSalle = dataSession['nomSalle'];
                        var nomLieu = dataSession['nomLieu'];
                        var formateur = dataSession['formateur'];
                        var locaux = dataSession['locaux'];
                        var region = dataSession['region'];
                        var subject = dataSession['subject'];
                        var elearning = dataSession['elearning'];
                        console.log("elearning" + elearning);
                        container.find("input[name='subject']").val(subject);
                        container.find("input[name='cf_988']").val(dateStart);
                        container.find("input[name='cf_990']").val(dateFin);
                        container.find("textarea[name='bill_street']").val(adresse);
                        container.find("input[name='bill_city']").val(ville);
                        container.find("input[name='bill_code']").val(cp);
                        container.find("input[name='salle']").val(idsalle);
                        container.find("input[name='salle_display']").val(nomSalle);
                        container.find("input[name='lieu']").val(idlieu);
                        container.find("input[name='lieu_display']").val(nomLieu);
                        container.find("input[name='bill_state']").val(region);

                        if (elearning == "1")
                        {
                            jQuery('#SalesOrder_editView_fieldName_cf_1204').prop('checked', true);
                        } else
                        {
                            jQuery('#SalesOrder_editView_fieldName_cf_1204').prop('checked', false);
                        }

                        container.find("select[name='assigned_user_id'] option[value=" + formateur + "]").attr('selected', 'selected').change();
                        if (locaux == 'Au centre')
                        {
                            locaux = 'Au centre';
                        } else if (locaux == 'Chez le client')
                        {
                            locaux = 'Client';
                        } else if (locaux == 'Autres') {
                            locaux = 'Autre cas';
                        }
                        container.find("select[name='cf_860'] option[value='" + locaux + "']").attr('selected', 'selected').change();
                        container.find("select[name='cf_977'] option[value='" + type + "']").attr('selected', 'selected').change();
                        /* uni_cnfsecrm - modif 80 - DEBUT */
//                        if (type == 'Inter') {
//                            var nbreApprenant = dataSession['nbreApprenant'];
//                            $('.lineItemRow input.qty').val(nbreApprenant);
//                        }
                        /* uni_cnfsecrm - modif 80 - FIN */
                        
                        /* uni_cnfsecrm - v2 - modif 125 - DEBUT */
                        (type == 'Inter')?self.updateNbreAppr():'';
                        /* uni_cnfsecrm - v2 - modif 125 - FIN */
                    }
                },
                function (error, err) {

                }
        );
        //appel au fichier des journee
        var dataUrl = "index.php?module=Inventory&action=GetJournee&record=" + idSession;
        $("tr").remove(".lineItemRowDate");
        app.request.get({'url': dataUrl}).then(
                function (error, data) {
                    if (error == null) {
                        var nbr_journee = data['count'];
                        for (i = 0; i < nbr_journee; i++) {
                            self.AddingNewDateSessionAction(data);
                        }
                    }
                },
                function (error, err) {

                }
        );
        //fin appel
    },
    
    /* uni_cnfsecrm - v2 - modif 125 - DEBUT */
    updateTypeSession: function(){
        var self = this;
        $("select[name='cf_977']").on('change',function(){
            self.updateNbreAppr();
        })
    },
    /* uni_cnfsecrm - v2 - modif 125 - FIN */

    detailformation: function (formation, type, nbrJours, nbrHeures) {
        var self = this;
        //appel au fichier des formation
        var type = type;
        var nbrJours = nbrJours;
        var nbrHeures = nbrHeures;
        console.log('formation')
        console.log(formation)
        var dataUrl = "index.php?module=Inventory&action=GetTaxes&record=" + formation;
        app.request.get({'url': dataUrl}).then(
                function (error, dataFormation) {
                    if (error == null) {
                        var parentRow = $('#lineItemTab #row1');
                        var dataFormation = dataFormation[0];
                        self.mapResultsToFields(parentRow, dataFormation, type, nbrJours, nbrHeures);
                    }
                },
                function (error, err) {

                }
        );
        //fin appel
    },

    mapResultsToFields: function (parentRow, responseData, type, nbrJours, nbrHeures) {
        var lineItemNameElment = jQuery('input.productName', parentRow);
        var referenceModule = this.getLineItemSetype(parentRow);
        var lineItemRowNumber = parentRow.data('rowNum');
        console.log('responseData 1')
        console.log(responseData);
        for (var id in responseData) {
            var recordId = id;
            var recordData = responseData[id];
            var selectedName = recordData.name;
            var unitPrice = recordData.listprice;
            var listPriceValues = recordData.listpricevalues;
            var taxes = recordData.taxes;
            var purchaseCost = recordData.purchaseCost;
            this.setPurchaseCostValue(parentRow, purchaseCost);
            var imgSrc = recordData.imageSource;
            var nbr_jours = nbrJours;
            var nbr_heures = nbrHeures;
            this.setImageTag(parentRow, imgSrc);
            var listpriceinter = recordData.listpriceinter;
            var listpriceintra = recordData.listpriceintra;
            if (referenceModule == 'Products') {
                parentRow.data('quantity-in-stock', recordData.quantityInStock);
            }
            var description = recordData.description;

            if (type == "Inter")
            {
                type = 'inter';
                jQuery('input.par_personne', parentRow).prop('checked', true);
            } else if (type == "Intra")
            {
                type = 'intra';
                jQuery('input.par_personne', parentRow).prop('checked', false);
            }

            jQuery('select.tarif', parentRow).val(type);
            jQuery('input.nbr_jours', parentRow).val(nbr_jours);
            jQuery('input.nbr_heures', parentRow).val(nbr_heures);
            jQuery('input.listpriceinter', parentRow).val(listpriceinter);
            jQuery('input.listpriceintra', parentRow).val(listpriceintra);

            jQuery('input.selectedModuleId', parentRow).val(recordId);
            jQuery('input.lineItemType', parentRow).val(referenceModule);
            lineItemNameElment.val(selectedName);
            lineItemNameElment.attr('disabled', 'disabled');
            jQuery('input.listPrice', parentRow).val(listpriceinter);
            var currencyId = this.currencyElement.val();
            var listPriceValuesJson = JSON.stringify(listPriceValues);
            if (typeof listPriceValues[currencyId] != 'undefined') {
                this.formatListPrice(parentRow, listPriceValues[currencyId]);
                this.lineItemRowCalculations(parentRow);
            }
            jQuery('input.listPrice', parentRow).attr('list-info', listPriceValuesJson);
            jQuery('input.listPrice', parentRow).data('baseCurrencyId', recordData.baseCurrencyId);
            jQuery('textarea.lineItemCommentBox', parentRow).val(description);
            var taxUI = this.getTaxDiv(taxes, parentRow);
            jQuery('.taxDivContainer', parentRow).html(taxUI);

            //Take tax percentage according to tax-region, if region is selected.
            var selectedRegionId = this.regionElement.val();
            if (selectedRegionId != 0) {
                var taxPercentages = jQuery('.taxPercentage', parentRow);
                jQuery.each(taxPercentages, function (index1, taxDomElement) {
                    var taxPercentage = jQuery(taxDomElement);
                    var regionsList = taxPercentage.data('regionsList');
                    var value = regionsList['default'];
                    if (selectedRegionId && regionsList[selectedRegionId]) {
                        value = regionsList[selectedRegionId];
                    }
                    taxPercentage.val(parseFloat(value));
                });
            }

            if (this.isIndividualTaxMode()) {
                parentRow.find('.productTaxTotal').removeClass('hide')
            } else {
                parentRow.find('.productTaxTotal').addClass('hide')
            }
        }
        if (referenceModule == 'Products') {
            this.loadSubProducts(parentRow);
        }

        jQuery('.qty', parentRow).trigger('focusout');
    },

    AddingNewDateSessionAction: function (data) {
        var thisInstance = this;
        var lineItemTableDate = this.getLineItemContentsContainerDate();
        var newRow = thisInstance.getBasicRowDate().addClass(thisInstance.rowClassDate);
        var sequenceNumber = thisInstance.getNextLineItemRowNumberDate();
        newRow = newRow.appendTo(jQuery('#lineItemTabDate'));
        thisInstance.checkLineItemRowDate();
        newRow.find('input.rowNumberDates').val(sequenceNumber);
        thisInstance.updateLineDateSession(newRow, data);
        thisInstance.updateRowNumberForRowDate(newRow, sequenceNumber);
        jQuery('.dateFieldTemp', newRow).addClass('dateField');
        jQuery('.dateField', newRow).removeClass('dateFieldTemp');
        vtUtils.registerEventForDatePickerFields(jQuery('.dateField', newRow), true);
    },
    updateLineDateSession: function (lineItemRowDate, data) {
        var d = new Date();
        var datestart = d.getDate() + "-" + (d.getMonth() + 1) + "-" + d.getFullYear();
        jQuery('input.date_start', lineItemRowDate).val(data[i].date_start);
        jQuery('input.start_matin', lineItemRowDate).val(data[i].start_matin);
        jQuery('input.end_matin', lineItemRowDate).val(data[i].end_matin);
        jQuery('input.start_apresmidi', lineItemRowDate).val(data[i].start_apresmidi);
        jQuery('input.end_apresmidi', lineItemRowDate).val(data[i].end_apresmidi);
        jQuery('input.duree_formation', lineItemRowDate).val(data[i].duree_formation);
    },
    /* unicnfsecrm_022020_26 -fin */
});