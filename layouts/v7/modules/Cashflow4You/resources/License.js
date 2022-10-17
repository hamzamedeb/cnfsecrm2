/* * *******************************************************************************
 * The content of this file is subject to the Cashflow4You license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is IT-Solutions4You s.r.o.
 * Portions created by IT-Solutions4You s.r.o. are Copyright(C) IT-Solutions4You s.r.o.
 * All Rights Reserved.
 * ****************************************************************************** */

Vtiger_List_Js("Cashflow4You_License_Js", {
    licenseInstance: false,
    getInstance: function () {
        if (Cashflow4You_License_Js.licenseInstance == false) {
            var instance = new window["Cashflow4You_License_Js"]();
            Cashflow4You_License_Js.licenseInstance = instance;
            return instance;
        }
        return Cashflow4You_License_Js.licenseInstance;
    }
}, {
    activateHeader : function(step) {
        var headersContainer = jQuery('.crumbs');
        headersContainer.find('.active').removeClass('active');
        jQuery('#'+step,headersContainer).addClass('active');
    },
    initiateStep : function(stepVal) {
        var step = 'step'+stepVal;
        this.activateHeader(step);
        jQuery('#stepContent'+(stepVal - 1)).hide();
        jQuery('#stepContent'+stepVal).show();
    },
    editLicense : function($type) {
        var aDeferred = jQuery.Deferred();
        var thisInstance = this;

        app.helper.showProgress();

        var license_key = jQuery('#license_key_val').val();
        var url = "index.php?module=Cashflow4You&view=IndexAjax&mode=editLicense&type="+$type+"&key="+license_key;

        app.request.post({'url':url}).then(
            function (err, response) {
                if (err === null) {
                    app.helper.hideProgress();
                    app.helper.showModal(response, {
                        'cb': function (modalContainer) {
                            modalContainer.find('#js-edit-license').on('click', function (e){
                                var form = modalContainer.find('#editLicense');
                                var params = {
                                    submitHandler: function (form) {
                                        if (!this.valid) {
                                            return false;
                                        }
                                        thisInstance.saveLicenseKey(form, false);
                                    }
                                };
                                form.vtValidate(params);
                            });
                        }
                    });
                }
            }
        );

        return aDeferred.promise();
    },
	/*
	* Function to Save the CustomLabel Details
	*/
	saveLicenseKey : function(form,is_install) {
		var thisInstance = this;
        if (is_install) {
            var licensekey_val = jQuery('#licensekey').val();

            var params = {
                module : app.getModuleName(),
                licensekey : licensekey_val,
                action : 'License',
                mode : 'editLicense',
                type : 'activate'
            };
        } else {
            var params = jQuery(form).serializeFormData();
        }
        thisInstance.validateLicenseKey(params).then(
			function(data) {
				
                if (!is_install) {
                    app.hideModalWindow();
                    app.helper.showSuccessNotification({"message":data.message});

                    jQuery('#license_key_val').val(data.licensekey);
                    jQuery('#license_key_label').html(data.licensekey);
                    jQuery('.license_due_date_val').html(data.due_date);

                    jQuery('#divgroup1').hide();
                    jQuery('#divgroup2').show();

                    jQuery('.license_due_date_tr').show();
                } else {
                    thisInstance.initiateStep(2);
                }
			}
		);
	},
	validateLicenseKey : function(data) {
        var thisInstance = this;
        var aDeferred = jQuery.Deferred();

        var form = jQuery('#editLicense');
        var CustomLabelElement = form.find('[name="licensekey"]');
            thisInstance.checkLicenseKey(data).then(
                function(data){
                    aDeferred.resolve(data);
                },
                function(data, err){
                    CustomLabelElement.validationEngine('showPrompt', data['message'] , 'error','bottomLeft',true);
                    aDeferred.reject(data);
                }
            );

        return aDeferred.promise();
	},
	/*
	 * Function to check Duplication of Tax Name
	 */
    checkLicenseKey : function(params) {
        var aDeferred = jQuery.Deferred();
        app.helper.showProgress();
        app.request.post({'data' : params}).then(function(err,response) {
            app.helper.hideProgress();
            if(err === null){
                var result = response.success;
                if(result == true) {
                    aDeferred.resolve(response);
                } else {
                    app.helper.showErrorNotification({"message":response.message});
                    aDeferred.reject(response);
                }
            } else{
                app.helper.showErrorNotification({"message":err});
                aDeferred.reject();
            }
        });
        return aDeferred.promise();
    },
	registerActions : function() {
		
        var thisInstance = this;
        var container = jQuery('#LicenseContainer');

        jQuery('#activate_license_btn').click(function(e) {
            thisInstance.editLicense('activate');
        });

        jQuery('#reactivate_license_btn').click(function(e) {
            thisInstance.editLicense('reactivate');
        });

        jQuery('#deactivate_license_btn').click(function(e) {
            thisInstance.deactivateLicense();
        });
	},
    deactivateLicense: function () {
        app.helper.showProgress();
        var license_key = jQuery('#license_key_val').val();
        var deactivateActionUrl = 'index.php?module=Cashflow4You&action=License&mode=deactivateLicense&key='+license_key;

        app.request.post({'url':deactivateActionUrl + '&type=control'}).then(
            function (err, response) {
                if (err === null) {
                    app.helper.hideProgress();
                    if (response.success) {
                        var message = app.vtranslate('LBL_DEACTIVATE_QUESTION','Cashflow4You');
                        app.helper.showConfirmationBox({'message': message}).then(function(data) {
                            app.helper.showProgress();
                            app.request.post({'url':deactivateActionUrl}).then(
                                function (err2, response2) {
                                    if (err2 === null) {
                                        if (response2.success) {
                                            app.helper.showSuccessNotification({message: response2.deactivate});

                                            jQuery('#license_key_val').val("");
                                            jQuery('#license_key_label').html("");

                                            jQuery('#divgroup1').show();
                                            jQuery('#divgroup2').hide();

                                            jQuery('.license_due_date_tr').hide();

                                        } else {
                                            app.helper.showErrorNotification({message: response2.deactivate});
                                        }
                                    }
                                    else {
                                        app.helper.showErrorNotification({"message":err2});
                                    }
                                    app.helper.hideProgress();
                                }
                            );
                        });
                    } else {
                        app.helper.showErrorNotification({message: response.deactivate});
                    }
                } else {
                    app.helper.hideProgress();
                    app.helper.showErrorNotification({"message":err});
                }
            }
        );
    },
	registerEvents: function() {
		this.registerActions();
	},
    registerInstallEvents: function() {
        var thisInstance = this;

        this.registerInstallActions();
        var form = jQuery('#editLicense');
        form.on('submit', function(e){
            e.preventDefault();
            thisInstance.saveLicenseKey(form,true);
        });
	},
    registerInstallActions : function() {
        var thisInstance = this;
        jQuery('#next_button').click(function(e) {
            window.location.href = "index.php?module=Cashflow4You&view=List";
        });
    },
});