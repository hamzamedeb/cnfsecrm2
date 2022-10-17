/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Inventory_Edit_Js("Quotes_Edit_Js", {}, {

    accountsReferenceField: false,
    contactsReferenceField: false,

    initializeVariables: function () {
        this._super();
        var form = this.getForm();
        this.accountsReferenceField = form.find('[name="account_id"]');
        this.contactsReferenceField = form.find('[name="contact_id"]');
    },

    /**
     * Function to get popup params
     */
    getPopUpParams: function (container) {
        var params = this._super(container);
        var sourceFieldElement = jQuery('input[class="sourceField"]', container);
        var referenceModule = jQuery('input[name=popupReferenceModule]', container).val();
        if (!sourceFieldElement.length) {
            sourceFieldElement = jQuery('input.sourceField', container);
        }

        if ((sourceFieldElement.attr('name') == 'contact_id' || sourceFieldElement.attr('name') == 'potential_id') && referenceModule != 'Leads') {
            var form = this.getForm();
            var parentIdElement = form.find('[name="account_id"]');
            if (parentIdElement.length > 0 && parentIdElement.val().length > 0 && parentIdElement.val() != 0) {
                var closestContainer = parentIdElement.closest('td');
                params['related_parent_id'] = parentIdElement.val();
                params['related_parent_module'] = closestContainer.find('[name="popupReferenceModule"]').val();
            } else if (sourceFieldElement.attr('name') == 'potential_id') {
                parentIdElement = form.find('[name="contact_id"]');
                var relatedParentModule = parentIdElement.closest('td').find('input[name="popupReferenceModule"]').val()
                if (parentIdElement.length > 0 && parentIdElement.val().length > 0 && relatedParentModule != 'Leads') {
                    closestContainer = parentIdElement.closest('td');
                    params['related_parent_id'] = parentIdElement.val();
                    params['related_parent_module'] = closestContainer.find('[name="popupReferenceModule"]').val();
                }
            }
        }
        return params;
    },

/* unicnfsecrm_022020_23 - begin */
    /** 
     * Function which will register event for Reference Fields Selection
     */
//    registerReferenceSelectionEvent: function (container) {
//        this._super(container);
//        var self = this;
//
//        this.accountsReferenceField.on(Vtiger_Edit_Js.referenceSelectionEvent, function (e, data) {
//            self.referenceSelectionEventHandler(data, container);
//        });
//    },

/* unicnfsecrm_022020_23 - end */

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

        if (params.search_module == 'Contacts' || params.search_module == 'Potentials') {
            var form = this.getForm();
            if (this.accountsReferenceField.length > 0 && this.accountsReferenceField.val().length > 0) {
                var closestContainer = this.accountsReferenceField.closest('td');
                params.parent_id = this.accountsReferenceField.val();
                params.parent_module = closestContainer.find('[name="popupReferenceModule"]').val();
            } else if (params.search_module == 'Potentials') {

                if (this.contactsReferenceField.length > 0 && this.contactsReferenceField.val().length > 0) {
                    closestContainer = this.contactsReferenceField.closest('td');
                    params.parent_id = this.contactsReferenceField.val();
                    params.parent_module = closestContainer.find('[name="popupReferenceModule"]').val();
                }
            }
        }

        // Added for overlay edit as the module is different
        if (params.search_module == 'Products' || params.search_module == 'Services') {
            params.module = 'Quotes';
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
    registerBasicEvents: function (container) {
        this._super(container);
        this.registerForTogglingBillingandShippingAddress();
        this.registerEventForCopyAddress();
        /* unicnfsecrm_022020_23 - begin */
        this.registerAdresseAutoComplete();
        var url = window.location.href;
        var position = url.search("record");
        if (position < 0) {
            this.registerAdresseCharentonAutoComplete();
        }
        /* unicnfsecrm_022020_23 - end */
    },

    /* unicnfsecrm_022020_23 - begin */
    //remplir l'adresse de CHARENTON par default
    //unicnfsecrm_mod_39
    registerAdresseCharentonAutoComplete: function () {
        var container = $('recordEditView');
        $('input[name="lieu"]').val('11827');
        $('input[name="lieu_display"]').val('CHARENTON LE PONT');
        $('select[name="cf_915"] option[value="Au centre"]').attr('selected', 'selected').change();
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
                        $("textarea[name='bill_street']").val(adresse);
                        $("input[name='bill_code']").val(codePostale);
                        $("input[name='bill_city']").val(ville);
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
            var locaux = $('select[name="cf_915"] option:selected').val();
            var recordClient = $('input[name="account_id"]').val();
            var recordLieu = $('input[name="lieu"]').val();
            if (type == 'Intra' && recordClient != 'undefined' && recordClient != '') {
                data = {source_module: "Accounts", record: recordClient, selectedName: ""}
                self.referenceSelectionClientHandler(data, container);
            } else if (type == 'Inter') {
                data = {source_module: "lieu", record: 11827, selectedName: ""}
                self.referenceSelectionAucentreHandler(data, container);
                $('input[name="lieu"]').val('11827');
                $('input[name="lieu_display"]').val('CHARENTON LE PONT');
                $('select[name="cf_915"] option[value="Au centre"]').attr('selected', 'selected').change();
            }
        });

        $('select[name="cf_915"]').on('click', function (e, data) {
            console.log(" Locaux changé ");
            var locaux = $('select[name="cf_915"] option:selected').val();
            console.log(" Locaux changé " + locaux);
            var recordClient = $('input[name="account_id"]').val();
            var recordLieu = $('input[name="lieu"]').val();
            if (locaux == 'Chez le client' && recordClient != 'undefined' && recordClient != '') {
                // var nomclient = $('tr[data-id="${record}"]').attr('data-name'); 
                data = {source_module: "Accounts", record: recordClient, selectedName: ""}
                self.referenceSelectionClientHandler(data, container);
            } else if (locaux == 'Au centre' && recordLieu != 'undefined' && recordLieu != '') {
                data = {source_module: "lieu", record: recordLieu, selectedName: ""}
                self.referenceSelectionAucentreHandler(data, container);
            }
        });

        $('input[name="account_id"]').on(Vtiger_Edit_Js.referenceSelectionEvent, function (e, data) {
            var locaux = $('select[name="cf_915"] option:selected').val();
            var idClient = $('input[name="account_id"]').val();
            if ((locaux == 'Chez le client') && (idClient != 'undefined') && (idClient != '')) {
                self.referenceSelectionClientHandler(data, container);
            }
            // unicnfsecrm_mod_37
            var url = window.location.href;
            var position = url.search("record");
            if (position < 0) {
                /* unicnfsecrm - disable fct rempissage automatique des apprenants de client */
               // self.copyClientApprenantRel(data, container);
            }

        });

        $('input[name="lieu"]').on(Vtiger_Edit_Js.referenceSelectionEvent, function (e, data) {
            var locaux = $('select[name="cf_915"] option:selected').val();
            var idLieu = $('input[name="lieu"]').val();
            if (locaux == 'Au centre') {
                self.referenceSelectionAucentreHandler(data, container);
            }
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
                        self.addLineApprenant(responseApprenant[0]['listApprenant'][i], container, detailClient);
                    }
                },
                function (error, err) {

                });
    },

    referenceSelectionClientHandler: function (data, container) {
        var self = this;
        var message = app.vtranslate('OVERWRITE_EXISTING_CLIENT','Quotes');
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

                        $("textarea[name='bill_street']").val(adresse);
                        $("input[name='bill_code']").val(codePostale);
                        $("input[name='bill_city']").val(ville);
                        $('select[name="cf_915"] option[value="Chez le client"]').attr('selected', 'selected').change();
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
        var message = app.vtranslate('OVERWRITE_EXISTING_LIEU','Quotes');
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
                        $("textarea[name='bill_street']").val(adresse);
                        $("input[name='bill_code']").val(codePostale);
                        $("input[name='bill_city']").val(ville);
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

    /* unicnfsecrm_022020_23 - end */
});