/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Edit_Js("Accounts_Edit_Js", {

}, {

    //This will store the editview form
    editViewForm: false,

    //Address field mapping within module
    addressFieldsMappingInModule: {
        'bill_street': 'ship_street',
        'bill_pobox': 'ship_pobox',
        'bill_city': 'ship_city',
        'bill_state': 'ship_state',
        'bill_code': 'ship_code',
        'bill_country': 'ship_country'
    },

    // mapping address fields of MemberOf field in the module              
    memberOfAddressFieldsMapping: {
        'bill_street': 'bill_street',
        'bill_pobox': 'bill_pobox',
        'bill_city': 'bill_city',
        'bill_state': 'bill_state',
        'bill_code': 'bill_code',
        'bill_country': 'bill_country',
        'ship_street': 'ship_street',
        'ship_pobox': 'ship_pobox',
        'ship_city': 'ship_city',
        'ship_state': 'ship_state',
        'ship_code': 'ship_code',
        'ship_country': 'ship_country'
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
     * Function to copy address between fields
     * @param strings which accepts value as either odd or even
     */
    copyAddress: function (swapMode, container) {
        var thisInstance = this;
        var addressMapping = this.addressFieldsMappingInModule;
        if (swapMode == "false") {
            for (var key in addressMapping) {
                var fromElement = container.find('[name="' + key + '"]');
                var toElement = container.find('[name="' + addressMapping[key] + '"]');
                toElement.val(fromElement.val());
            }
        } else if (swapMode) {
            var swappedArray = thisInstance.swapObject(addressMapping);
            for (var key in swappedArray) {
                var fromElement = container.find('[name="' + key + '"]');
                var toElement = container.find('[name="' + swappedArray[key] + '"]');
                toElement.val(fromElement.val());
            }
        }
    },

    /**
     * Function to register event for copying address between two fileds
     */
    registerEventForCopyingAddress: function (container) {
        var thisInstance = this;
        var swapMode;
        jQuery('[name="copyAddress"]').on('click', function (e) {
            var element = jQuery(e.currentTarget);
            var target = element.data('target');
            if (target == "billing") {
                swapMode = "false";
            } else if (target == "shipping") {
                swapMode = "true";
            }
            thisInstance.copyAddress(swapMode, container);
        })
    },

    /**
     * Function which will copy the address details - without Confirmation
     */
    copyAddressDetails: function (data, container) {
        var thisInstance = this;
        thisInstance.getRecordDetails(data).then(
                function (data) {
                    var response = data['result'];
                    thisInstance.mapAddressDetails(thisInstance.memberOfAddressFieldsMapping, response['data'], container);
                },
                function (error, err) {

                });
    },

    /**
     * Function which will map the address details of the selected record
     */
    mapAddressDetails: function (addressDetails, result, container) {
        for (var key in addressDetails) {
            // While Quick Creat we don't have address fields, we should  add
            if (container.find('[name="' + key + '"]').length == 0) {
                container.append("<input type='hidden' name='" + key + "'>");
            }
            container.find('[name="' + key + '"]').val(result[addressDetails[key]]);
            container.find('[name="' + key + '"]').trigger('change');
            container.find('[name="' + addressDetails[key] + '"]').val(result[addressDetails[key]]);
            container.find('[name="' + addressDetails[key] + '"]').trigger('change');
        }
    },

    /**
     * Function which will register basic events which will be used in quick create as well
     *
     */
    registerBasicEvents: function (container) {
        this._super(container);
        this.registerEventForCopyingAddress(container);
        /*unicnfsecrm_mod_31*/
        this.registerCityAutoComplete();
        /* unicnfsecrm_022020_00 */
        this.testExisteNomClient(container);
        this.testExisteTelClient(container);
        this.testExisteEmailClient(container);
    },

    /* unicnfsecrm_mod_31 */
    registerCityAutoComplete: function (container) {
        $("input[name='bill_code']").autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: "https://api-adresse.data.gouv.fr/search/?postcode=" + $("input[name='bill_code']").val(),
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
                $("input[name='bill_city']").val(ui.item.city);
                $("input[name='cf_1200']").val(region);
            }
        });
    },

    /* unicnfsecrm_022020_00 */
    testExisteNomClient: function (container) {
        console.log("testExisteNomClient");
        var self = this;
        $(container).ready(function () {
            container.find("input[name=accountname]").on("input", function () {
                var str = container.find("input[name=accountname]").val();
                var str1 = container.find("input[name=phone]").val();
                var dataUrl = "index.php?module=Accounts&action=GetClient&sourceModule=" + app.getModuleName() + "&testnom=" + str + '&type=accountname';
                app.request.get({'url': dataUrl}).then(
                        function (error, data) {
                            if (error == null) {
                                console.log(data[0]['reponse']);
                                if (data[0]['reponse'] == true) {
                                    container.find("div p.messagealertClient").remove();
                                    var newEl = document.createElement('div');
                                    newEl.innerHTML = '<p class="messagealertClient">le nom du client est deja utiliser !</p>';
                                    var ref = document.querySelector('input[name=accountname]');
                                    insertAfter(newEl, ref);
                                    container.find("input[name=accountname]").addClass("input-error");
                                } else {
                                    container.find("input[name=accountname]").removeClass("input-error");
                                    container.find("div p.messagealertClient").remove();
                                }
                                function insertAfter(el, referenceNode) {
                                    referenceNode.parentNode.insertBefore(el, referenceNode.nextSibling);
                                }
                                ;
                            }
                        },
                        function (error, err) {

                        }
                );
            });
        });
    },
    /* unicnfsecrm_022020_00 */
    testExisteTelClient: function (container) {
        var self = this;
        $(container).ready(function () {
            container.find("input[name=phone]").on("input", function () {
                var str = container.find("input[name=phone]").val();
                for (var i = 0; i < str.length; i++) {
                    str = str.replace('.', "");
                    str = str.replace(' ', "");
                }
                var dataUrl = "index.php?module=Accounts&action=GetClient&sourceModule=" + app.getModuleName() + "&testnom=" + str + '&type=phone';
                app.request.get({'url': dataUrl}).then(
                        function (error, data) {
                            if (error == null) {
                                console.log(data[0]['reponse']);
                                if (data[0]['reponse'] == true) {
                                    container.find("div p.messagealertPhone").remove();
                                    var newEl = document.createElement('div');
                                    newEl.innerHTML = '<p class="messagealertPhone">Le téléphone du client est deja utilisé !</p>';
                                    var ref = document.querySelector('input[name=phone]');
                                    insertAfter(newEl, ref);
                                    container.find("input[name=phone]").addClass("input-error");
                                } else {
                                    container.find("input[name=phone]").removeClass("input-error");
                                    container.find("div p.messagealertPhone").remove();
                                }
                                function insertAfter(el, referenceNode) {
                                    referenceNode.parentNode.insertBefore(el, referenceNode.nextSibling);
                                }
                                ;
                            }
                        },
                        function (error, err) {

                        }
                );
            });
        });
    },

    testExisteEmailClient: function (container) {
        var self = this;
        $(container).ready(function () {
            container.find("input[name=email1]").on("input", function () {
                var str = container.find("input[name=email1]").val();
                var dataUrl = "index.php?module=Accounts&action=GetClient&sourceModule=" + app.getModuleName() + "&testnom=" + str + '&type=email';
                app.request.get({'url': dataUrl}).then(
                        function (error, data) {
                            if (error == null) {
                                console.log(data[0]['reponse']);
                                if (data[0]['reponse'] == true) {
                                    container.find("div p.messagealertEmail").remove();
                                    var newEl = document.createElement('div');
                                    newEl.innerHTML = '<p class="messagealertEmail">L\'email du client est deja utilisé !</p>';
                                    var ref = document.querySelector('input[name=email1]');
                                    insertAfter(newEl, ref);
                                    container.find("input[name=email1]").addClass("input-error");
                                } else {
                                    container.find("input[name=email1]").removeClass("input-error");
                                    container.find("div p.messagealertEmail").remove();
                                }
                                function insertAfter(el, referenceNode) {
                                    referenceNode.parentNode.insertBefore(el, referenceNode.nextSibling);
                                }
                                ;
                            }
                        },
                        function (error, err) {

                        }
                );
            });
        });
    },
});