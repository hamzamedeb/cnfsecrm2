/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Vtiger_Detail_Js("Inventory_Detail_Js", {
    triggerRecordPreview: function (recordId) {
        var thisInstance = app.controller();
        thisInstance.showRecordPreview(recordId);
    },

    sendEmailPDFClickHandler: function (url) {
        var params = app.convertUrlToDataParams(url);

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
    },

    //creation facture financeur 
    //unicnfsecrm_mod_55
    test: function (url) {
        console.log('test 1');
        var self = this;
        var url;
        var message = app.vtranslate('Voulez-vous créer les factures financeurs?');
        app.helper.showConfirmationBox({'message': message}).then(
                function (e) {
                    self.test1(url);
                },
                function (error, err) {
                });
    },
//unicnfsecrm_mod_55
    test1: function (url) {
        var params = app.convertUrlToDataParams(url);
        console.log('params');
        console.log(params);
        app.helper.showProgress();
        app.request.post({data: params}).then(function (err, response) {
            console.log(err);
            console.log(response);
            var callback = function () {
                var emailEditInstance = new Emails_MassEdit_Js();
                emailEditInstance.registerEvents();
            };
            var data = {};
            data['cb'] = callback;
            app.helper.hideProgress();

            if (response[0] == 'ok') {
                var datasuccsess = "<div class='modal-dialog'><div class='modal-content'><div class='modal-header'><div class='clearfix'><div class='pull-right ' ><button type='button' class='close' aria-label='Close' data-dismiss='modal'><span aria-hidden='true' class='fa fa-close'></span></button></div><h4 class='pull-left'>Result</h4></div></div><div class='modal-body'><div class='detailShowAllModal' data-relatedload=''>Les factures financeurs ont été crée avec succès </div></div></div></div>";
                app.helper.showModal(datasuccsess);
            } else if (response[0] == 'prob_no_fact_client') {
                var datasuccsess = "<div class='modal-dialog'><div class='modal-content'><div class='modal-header'><div class='clearfix'><div class='pull-right ' ><button type='button' class='close' aria-label='Close' data-dismiss='modal'><span aria-hidden='true' class='fa fa-close'></span></button></div><h4 class='pull-left'>Result</h4></div></div><div class='modal-body'><div class='mailSentSuccessfully' data-relatedload=''>La facture client n'est pas crée</div></div></div></div>";
                app.helper.showModal(datasuccsess);
            } else if (response[0] == 'prob_no_financeurs') {
                var datasuccsess = "<div class='modal-dialog'><div class='modal-content'><div class='modal-header'><div class='clearfix'><div class='pull-right ' ><button type='button' class='close' aria-label='Close' data-dismiss='modal'><span aria-hidden='true' class='fa fa-close'></span></button></div><h4 class='pull-left'>Result</h4></div></div><div class='modal-body'><div class='mailSentSuccessfully' data-relatedload=''>La facture n'a pas de financeurs</div></div></div></div>";
                app.helper.showModal(datasuccsess);
            } else {
                var datasuccsess = "<div class='modal-dialog'><div class='modal-content'><div class='modal-header'><div class='clearfix'><div class='pull-right ' ><button type='button' class='close' aria-label='Close' data-dismiss='modal'><span aria-hidden='true' class='fa fa-close'></span></button></div><h4 class='pull-left'>Result</h4></div></div><div class='modal-body'><div class='mailSentSuccessfully' data-relatedload=''>Problème de création de facture</div></div></div></div>";
                app.helper.showModal(datasuccsess);
            }

        });
    },

    /* uni_cnfsecrm - v2 - modif 94 - DEBUT */
    marquerAbsent: function (sessionId, apprenantId, index) {
        var message = "Voulez vous marquer l'apprenant comme absent ?";
        var messageResult;
        app.helper.showConfirmationBox({'message': message}).then(
                function (e) {
                    var dataUrl = "index.php?module=SalesOrder&action=MarquerAbsent&record=" + app.getRecordId() + "&sessionId=" + sessionId + "&apprenantId=" + apprenantId;
                    app.request.get({'url': dataUrl}).then(
                            function (error, data) {
                                if (error == null) {
                                    if (data == true) {
                                        messageResult = "L'apprenant est marqué absent.";
                                        $('#marquerAbsent' + index).addClass('hidden')
                                        $('#openPopup' + index).removeClass('hidden')
                                    } else {
                                        //var errorMsg = app.vtranslate('JS_PRB_MARQUER_APPR_ABSENT');
                                        messageResult = "Impossible de mettre l'apprenant comme absent";
                                    }
                                    data = "<div class='modal-dialog'><div class='modal-content'><div class='modal-header'><div class='clearfix'><div class='pull-right ' ><button type='button' class='close' aria-label='Close' data-dismiss='modal'><span aria-hidden='true' class='fa fa-close'></span></button></div><h4 class='pull-left'>Result</h4></div></div><div class='modal-body'><div class='mailSentSuccessfully' data-relatedload=''>" + messageResult + "</div><input type='hidden' id='flag' value='SENT'></div></div></div>";
                                    app.helper.hideProgress();
                                    app.helper.showModal(data);
                                }
                            },
                            function (error, err) {

                            }
                    );
                },
                function (error, err) {

                }
        );
    },

    openPopup: function (sessionId, apprenantId) {
        var self = this;
        self.showQuickPreviewForId(sessionId, apprenantId);
    },
    showQuickPreviewForId: function (sessionId, apprenantId) {
        var self = this;
        var params = {};
        var moduleName = app.getModuleName();
        params['module'] = moduleName;
        params['record'] = app.getRecordId();
        params['sesion'] = sessionId;
        params['apprenant'] = apprenantId;
        params['view'] = 'PopUpSalesOrderDetail';
        params['navigation'] = 'true';

        app.helper.showProgress();
        app.request.get({data: params}).then(function (err, response) {
            app.helper.hideProgress();
            jQuery('#helpPageOverlay').css({"width": "550px", "box-shadow": "-8px 0 5px -5px lightgrey", 'height': '100vh', 'background': 'white'});
            app.helper.loadHelpPageOverlay(response);
            var params = {
                setHeight: "100%",
                alwaysShowScrollbar: 2,
                autoExpandScrollbar: true,
                setTop: 0,
                scrollInertia: 70,
                mouseWheel: {preventDefault: true}
            };
            app.helper.showVerticalScroll(jQuery('.quickPreview .modal-body'), params);
            $('#apprenantId').val(apprenantId);
            self.registerCalendar();
        });
    },

    registerCalendar: function () {
        var self = this;
        $('.lineItemPopupCalendar').on('click', function (e) {
            var triggerer = jQuery(e.currentTarget);
            self.showLineItemPopupCalendar({'view': triggerer.data('popup')});
            var popupReferenceModuleApprenant = triggerer.data('moduleName');
            var postPopupHandler = function (e, data) {
                data = JSON.parse(data);
                console.log('data');
                console.log(data)
                if (!$.isArray(data)) {
                    data = [data];
                }
                $.each(data[0], function (key, value) {
                    $('#calendarName').val(value.name)
                    $('#calendarId').val(key)
                });
            }
            app.event.off('post.LineItemPopupSelection.click');
            app.event.one('post.LineItemPopupSelection.click', postPopupHandler);
        })
    },
    showLineItemPopupCalendar: function (callerParams) {
        var params = {
            'module': app.getModuleName(),
            'multi_select': true,
            //'currency_id': this.currencyElement.val()
        };
        params = jQuery.extend(params, callerParams);
        var popupInstance = Vtiger_Popup_Js.getInstance();
        popupInstance.showPopup(params, 'post.LineItemPopupSelection.click');

    },

    setNewSession: function () {
        var message = "Voulez vous rattacher l'apprenant à une nouvelle Session ?";
        var messageResult;
        var sessionId = $('#calendarId').val();
        var sessionName = $('#calendarName').val();
        var apprenantId = $('#apprenantId').val();
        if (sessionId == '' || apprenantId == '') {
            return;
        }
        app.helper.showConfirmationBox({'message': message}).then(
                function (e) {
                    var dataUrl = "index.php?module=SalesOrder&action=SetNewSession&record=" + app.getRecordId() + "&sessionId=" + sessionId + "&apprenantId=" + apprenantId;
                    app.request.get({'url': dataUrl}).then(
                            function (error, data) {
                                if (error == null) {
                                    console.log('data')
                                    console.log(data)
                                    console.log(sessionId)
                                    if (data == true) {
                                        messageResult = "L'apprenant est bien rattaché à une nouvelle session.";
                                        $('#dataVide').remove();
                                        $('#historiqueApp').prepend("<li>Apprenant attaché à la Session  <a href='index.php?module=Calendar&amp;view=Detail&amp;record=" + sessionId + "&amp;app=SALES'>" + sessionName + "</a> </li>");
                                        $('#reporterFormation').attr('disabled', 'disabled')
                                        $('#saitPas').attr('disabled', 'disabled');
                                    } else {
                                        messageResult = "Problème de rattacher l'apprenant à une nouvelle session";
                                    }
                                    data = "<div class='modal-dialog'><div class='modal-content'><div class='modal-header'><div class='clearfix'><div class='pull-right ' ><button type='button' class='close' aria-label='Close' data-dismiss='modal'><span aria-hidden='true' class='fa fa-close'></span></button></div><h4 class='pull-left'>Result</h4></div></div><div class='modal-body'><div class='mailSentSuccessfully' data-relatedload=''>" + messageResult + "</div><input type='hidden' id='flag' value='SENT'></div></div></div>";
                                    app.helper.hideProgress();
                                    app.helper.showModal(data);
                                }
                            },
                            function (error, err) {

                            }
                    );
                },
                function (error, err) {

                }
        );
    },

    neSaiPas: function () {
        var message = "Voulez vous ajouter l'apprenant à la liste des stagiaires sans session ?";
        var messageResult;
        var apprenantId = $('#apprenantId').val();
        if (apprenantId == '') {
            return;
        }
        app.helper.showConfirmationBox({'message': message}).then(
                function (e) {
                    var dataUrl = "index.php?module=SalesOrder&action=neSaiPas&record=" + app.getRecordId() + "&apprenantId=" + apprenantId;
                    app.request.get({'url': dataUrl}).then(
                            function (error, data) {
                                if (error == null) {
                                    console.log(data)
                                    messageResult = data['message'];
                                    if (data['result'] == true) {
                                        $('#dataVide').remove();
                                        $('#historiqueApp').prepend("<li> Apprenant ajouté à la liste des 'Stagiaires sans session'</li>");
                                        $('#saitPas').attr('disabled', 'disabled');
                                    }
                                    data = "<div class='modal-dialog'><div class='modal-content'><div class='modal-header'><div class='clearfix'><div class='pull-right ' ><button type='button' class='close' aria-label='Close' data-dismiss='modal'><span aria-hidden='true' class='fa fa-close'></span></button></div><h4 class='pull-left'>Result</h4></div></div><div class='modal-body'><div class='mailSentSuccessfully' data-relatedload=''>" + messageResult + "</div><input type='hidden' id='flag' value='SENT'></div></div></div>";
                                    app.helper.hideProgress();
                                    app.helper.showModal(data);
                                }
                            },
                            function (error, err) {

                            }
                    );
                },
                function (error, err) {

                }
        );
    },

    annulerFacture: function () {
        var message = "Voulez vous annuler la facture ?";
        var messageResult;
        var apprenantId = $('#apprenantId').val();
        if (apprenantId == '') {
            return;
        }
        app.helper.showConfirmationBox({'message': message}).then(
                function (e) {
                    var dataUrl = "index.php?module=SalesOrder&action=AnnulerFacture&record=" + app.getRecordId() + "&apprenantId=" + apprenantId;
                    app.request.get({'url': dataUrl}).then(
                            function (error, data) {
                                if (error == null) {
                                    console.log(data)
                                    messageResult = data['message'];
                                    if (data['resultAvoir'] == true) {
                                        $('#dataVide').remove();
                                        $('#historiqueApp').prepend("<li> Un avoir <a href='index.php?module=Invoice&view=Detail&record=" + data['idInvoiceAvoir'] + "&app=INVENTORY'>" + data['subject'] + "</a> a été crée  </li>");
                                        $('#annulerFacture').attr('disabled', 'disabled');
                                    }
                                    data = "<div class='modal-dialog'><div class='modal-content'><div class='modal-header'><div class='clearfix'><div class='pull-right ' ><button type='button' class='close' aria-label='Close' data-dismiss='modal'><span aria-hidden='true' class='fa fa-close'></span></button></div><h4 class='pull-left'>Result</h4></div></div><div class='modal-body'><div class='mailSentSuccessfully' data-relatedload=''>" + messageResult + "</div><input type='hidden' id='flag' value='SENT'></div></div></div>";
                                    app.helper.hideProgress();
                                    app.helper.showModal(data);
                                }
                            },
                            function (error, err) {

                            }
                    );
                },
                function (error, err) {

                }
        );
    },
    /* uni_cnfsecrm - v2 - modif 94 - FIN */
},
        {
            showRecordPreview: function (recordId, templateId) {
                var thisInstance = this;
                var params = {};
                var moduleName = app.getModuleName();
                params['module'] = moduleName;
                params['record'] = recordId;
                params['view'] = 'InventoryQuickPreview';
                params['navigation'] = 'false';
                params['mode'] = 'Detail';

                if (templateId) {
                    params['templateid'] = templateId;
                }
                app.helper.showProgress();
                app.request.get({data: params}).then(function (err, response) {
                    app.helper.hideProgress();

                    if (templateId) {
                        jQuery('#pdfViewer').html(response);
                        return;
                    }
                    app.helper.showModal(response, {'cb': function (modal) {
                            jQuery('.modal-dialog').css({"width": "870px"});
                            thisInstance.registerChangeTemplateEvent(modal, recordId);
                        }
                    });
                });
            },
            registerChangeTemplateEvent: function (container, recordId) {
                var thisInstance = this;
                var select = container.find('#fieldList');
                select.on("change", function () {
                    var templateId = select.val();
                    thisInstance.showRecordPreview(recordId, templateId);
                });

            },

            registerEvents: function () {
                var self = this;
                this._super();
                this.getDetailViewContainer().find('.inventoryLineItemDetails').popover({html: true});
                app.event.on("post.relatedListLoad.click", function () {
                    self.getDetailViewContainer().find('.inventoryLineItemDetails').popover({html: true});
                });

                /* uni_cnfsecrm - modif 90 - Mettre le bouton editer "disable" si la date de creation de facture ou convention différent annee actuelle - DEBUT */
                if (app.getModuleName() == 'Invoice' || app.getModuleName() == 'SalesOrder') {
                    this.setDisabledBtnEdit();
                    /* uni_cnfsecrm - v2 - modif 175 - DEBUT */
                    this.setDisabledBtnAvoir();
                    /* uni_cnfsecrm - v2 - modif 175 - FIN */
                }
                /* uni_cnfsecrm - modif 90 - FIN */
                this.addBtnExportPDF();
                /* uni_cnfsecrm - v2 - modif 170 - DEBUT */
                this.hidePDFMakerContentDivInvoice();
                /* uni_cnfsecrm - v2 - modif 170 - FIN */
            },

            /* uni_cnfsecrm - modif 90 - Mettre le bouton editer "disable" si la date de creation de facture ou convention différent annee actuelle - DEBUT */
            setDisabledBtnEdit: function () {
                let currentUserId = app.getUserId();
                var moduleName = app.getModuleName();
                if (moduleName == 'Invoice') {
                    var dateCreate = $('#Invoice_detailView_fieldValue_createdtime span').text();
                    var btnEdit = $('#Invoice_detailView_basicAction_LBL_EDIT');
                } else if (moduleName == 'SalesOrder') {
                    var dateCreate = $('#SalesOrder_detailView_fieldValue_createdtime span').text();
                    var btnEdit = $('#SalesOrder_detailView_basicAction_LBL_EDIT');
                }
                if (dateCreate) {
                    var dateCreate = moment(dateCreate, "DD-MM-YYYY");
                    var dateCreate = dateCreate.toDate();
                    var dateNow = new Date();
                    if (currentUserId != 20) {
                        if (dateNow.getFullYear() != dateCreate.getFullYear()) {
                            btnEdit.attr('disabled', 'disabled'); 
                        }
                    }
                }
            },
            /* uni_cnfsecrm - modif 90 - FIN */

            /* uni_cnfsecrm - v2 - modif 175 - DEBUT */
            setDisabledBtnAvoir: function () {
                const dataUrl = "index.php?module=Inventory&action=getFactureAvoir&record=" + app.getRecordId();
                app.request.get({'url': dataUrl}).then(
                        function (error, data) {
                            if (error == null) {
                                let result = data['result'];
                                console.log('result')
                                console.log(result)
                                if (result == 0) {
                                    const btnCreateAvoir = document.querySelector('#createAvoirFacture');
                                    btnCreateAvoir.disabled = true;
                                }
                            }
                        },
                        );
            },
            /* uni_cnfsecrm - v2 - modif 175 - FIN */

            addBtnExportPDF: function () {
                console.log('test 01');

            },
            /* uni_cnfsecrm - v2 - modif 170 - DEBUT */
            hidePDFMakerContentDivInvoice: function () {
                var moduleName = app.getModuleName();
                if (moduleName == "Invoice") {
                    setTimeout(function () {
                        jQuery("#PDFMakerContentDiv").addClass('hide');
                    }, 500);
                }
            },
            /* uni_cnfsecrm - v2 - modif 170 - FIN */

        });