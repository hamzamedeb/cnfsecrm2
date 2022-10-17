/* * *******************************************************************************
 * The content of this file is subject to the Cashflow4You license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is IT-Solutions4You s.r.o.
 * Portions created by IT-Solutions4You s.r.o. are Copyright(C) IT-Solutions4You s.r.o.
 * All Rights Reserved.
 * ****************************************************************************** */

Vtiger.Class('Cashflow4You_Uninstall_Js',{
        uninstallInstance: false,
        getInstance: function () {
            if (Cashflow4You_Uninstall_Js.uninstallInstance == false) {
                var instance = new window["Cashflow4You_Uninstall_Js"]();
                Cashflow4You_Uninstall_Js.uninstallInstance = instance;
                return instance;
            }
            return Cashflow4You_Uninstall_Js.uninstallInstance;
        }
    },{

        uninstallCashflow4You: function() {

            var message = app.vtranslate('LBL_UNINSTALL_CONFIRM','Cashflow4You');
            app.helper.showConfirmationBox({'message': message}).then(function() {
                app.helper.showProgress();
                app.request.post({'url':'index.php?module=Cashflow4You&action=UninstallCashflow4You'}).then(
                    function(err,response) {

                        app.helper.hideProgress();
                        if(err === null){
                            if (response.success == true) {
                                app.helper.showSuccessNotification({message: app.vtranslate('JS_ITEMS_DELETED_SUCCESSFULLY')});
                                window.location.href = "index.php";
                            } else {
                                app.helper.showErrorNotification({message: ''});
                            }
                        } else {
                            app.helper.showErrorNotification({message: err});
                        }
                    });
            });
        },

        registerEvents: function() {
            this.registerActions();
        },

        registerActions : function() {
            var thisInstance = this;
            jQuery('#uninstall_cashflow4you_btn').click(function(e) {
                thisInstance.uninstallCashflow4You();
            });
        }
    }
)