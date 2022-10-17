Vtiger_Detail_Js("Events_Detail_Js", {
    sendEmailPDFClickHandler: function (url) {
        var params = app.convertUrlToDataParams(url);
        /* uni_cnfsecrm - v2 - modif 182 - DEBUT */
        if (params.email == "") {
            alert("Email de l'apprenant inexistant veuillez l'indiquer");
        } else {
            app.helper.showProgress();
            app.request.post({data: params}).then(function (err, response) {
                var callback = function () {
                    var emailEditInstance = new Emails_MassEdit_Js();
                    emailEditInstance.registerEvents();
                };
                var data = {};
                data['cb'] = callback;
                app.helper.hideProgress();
                app.helper.showModal(response, data);
            });
        }
        /* uni_cnfsecrm - v2 - modif 182 - FIN */
    },

}, {

    _delete: function (deleteRecordActionUrl) {
        var params = app.convertUrlToDataParams(deleteRecordActionUrl + "&ajaxDelete=true");
        app.helper.showProgress();
        app.request.post({data: params}).then(
                function (err, data) {
                    app.helper.hideProgress();
                    if (err === null) {
                        if (typeof data !== 'object') {
                            window.location.href = data;
                        } else {
                            app.helper.showAlertBox({'message': data.prototype.message});
                        }
                    } else {
                        app.helper.showAlertBox({'message': err});
                    }
                });
    },

    /**
     * To Delete Record from detail View
     * @param URL deleteRecordActionUrl
     * @returns {undefined}
     */
    remove: function (deleteRecordActionUrl) {
        var thisInstance = this;
        var isRecurringEvent = jQuery('#addEventRepeatUI').data('recurringEnabled');
        if (isRecurringEvent) {
            app.helper.showConfirmationForRepeatEvents().then(function (postData) {
                deleteRecordActionUrl += '&' + jQuery.param(postData);
                thisInstance._delete(deleteRecordActionUrl);
            });
        } else {
            this._super(deleteRecordActionUrl);
        }
    },

    registerEvents: function () {
        this._super();
    }

});