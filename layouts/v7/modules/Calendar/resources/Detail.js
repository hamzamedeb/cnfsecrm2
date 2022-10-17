Vtiger_Detail_Js("Calendar_Detail_Js", {
    //unicnfsecrm_mod_56
    setTokenApprenant: function (idApprenant, idSession) {
        console.log("tett")    
        var typeTokens = $('select[id="tokens' + idApprenant + '"] option:selected').val();
        var tokenValue = jQuery("#ticket_examen"+ idApprenant+" span.value").text();
        var dataUrl = "index.php?module=Calendar&action=ModifierTokens&recordId=" + idApprenant + "&typeTokens=" + typeTokens + "&idSession=" + idSession+"&tokenValue="+tokenValue;
        app.request.post({'url': dataUrl}).then(
                function (error, data) {
                    if (data == 'ok') {
                        app.helper.hideProgress();
                        var datasuccsess = "<div class='modal-dialog'><div class='modal-content'><div class='modal-header'><div class='clearfix'><div class='pull-right ' ><button type='button' class='close' aria-label='Close' data-dismiss='modal'><span aria-hidden='true' class='fa fa-close'></span></button></div><h4 class='pull-left'>Result</h4></div></div><div class='modal-body'><div class='mailSentSuccessfully' data-relatedload=''>Le token QCM a été attribué</div><input type='hidden' id='flag' value='SENT'></div></div></div>";
                    }
                    app.helper.showModal(datasuccsess);
                    if (error == null) {

                    }
                },
                function (error, err) {

                }
        );
    },
    setTokenApprenantTest: function (idApprenant, idSession) {
        var typeTokens = $('select[id="tokenstest' + idApprenant + '"] option:selected').val();
        var dataUrl = "index.php?module=Calendar&action=ModifierTokensTest&recordId=" + idApprenant + "&typeTokens=" + typeTokens + "&idSession=" + idSession;
        app.request.post({'url': dataUrl}).then(
                function (error, data) {
                    if (data == 'ok') {
                        app.helper.hideProgress();
                        var datasuccsess = "<div class='modal-dialog'><div class='modal-content'><div class='modal-header'><div class='clearfix'><div class='pull-right ' ><button type='button' class='close' aria-label='Close' data-dismiss='modal'><span aria-hidden='true' class='fa fa-close'></span></button></div><h4 class='pull-left'>Result</h4></div></div><div class='modal-body'><div class='mailSentSuccessfully' data-relatedload=''>Le token TEST a été attribué</div><input type='hidden' id='flag' value='SENT'></div></div></div>";
                    }
                    app.helper.showModal(datasuccsess);
                    if (error == null) {

                    }
                },
                function (error, err) {

                }
        );
    },
    
    setTokenApprenantReaffecter: function (idApprenant, idSession) {
        var typeTokens = $('select[id="tokensreaffecter' + idApprenant + '"] option:selected').val();
        var dataUrl = "index.php?module=Calendar&action=ModifierTokensReaffecter&recordId=" + idApprenant + "&typeTokens=" + typeTokens + "&idSession=" + idSession;
        app.request.post({'url': dataUrl}).then(
                function (error, data) {
                    if (data == 'ok') {
                        app.helper.hideProgress();
                        var datasuccsess = "<div class='modal-dialog'><div class='modal-content'><div class='modal-header'><div class='clearfix'><div class='pull-right ' ><button type='button' class='close' aria-label='Close' data-dismiss='modal'><span aria-hidden='true' class='fa fa-close'></span></button></div><h4 class='pull-left'>Result</h4></div></div><div class='modal-body'><div class='mailSentSuccessfully' data-relatedload=''>Le token QCM a été attribué</div><input type='hidden' id='flag' value='SENT'></div></div></div>";
                    }
                    app.helper.showModal(datasuccsess);
                    if (error == null) {

                    }
                },
                function (error, err) {

                }
        );
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

    registerAutoComplete: function (container) {
        var self = this;
        if (typeof container == 'undefined') {
            container = this.lineItemsHolderApprenant;
        }
        $('input.autoCompleteApprenant').autocomplete({
            'minLength': '0',
            'source': function (request, response) {
                //element will be array of dom elements
                //here this refers to auto complete instance
                var inputElement = jQuery(this.element[0]);
                console.log(inputElement);
                var tdElement = inputElement.closest('td');
                var searchValue = request.term;
                var params = {};
                params.search_module = "ContactsByEvents";
                params.search_value = searchValue;
                var idactivity = $('#recordId').val();
                params.activityid = idactivity;
                console.log(idactivity);
                self.searchModuleNames(params).then(function (data) {
                    console.log(data);
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
                         console.log(responseData);
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
                var idactivity = $('#recordId').val();
                //element.attr('disabled', 'disabled');
                var tdElement = element.closest('td');
                var selectedModuleApprenant = tdElement.find('.lineItemPopupApprenant').data('moduleName');
                var popupElementApprenant = tdElement.find('.lineItemPopupApprenant');
                var dataUrl = "index.php?module=Calendar&action=GetTnfosApprenants&record=" + selectedItemData.id + "&sourceModule=" + app.getModuleName() + "&idactivity=" + idactivity;
                app.request.get({'url': dataUrl}).then(
                        function (error, data) {
                            console.log(data);
                            if (error == null) {
                                $('.lineItemTableDivApprenants').html(data);
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
    
    /*uni_cnfsecrm - v2 - modif 176 - DEBUT*/ 
    setJetonValue: function () {
        console.log('test 0111')
        jQuery(".editAction").on("click", function (e) {
            var index_id = jQuery(e.target).parents('.fieldValue').attr('id');
            var type = "";
            var index = jQuery("#" + index_id).attr("data-field-type");
            if (index_id.indexOf("ticket_examen_test") >= 0) {
                type = "ticket_examen_test";
            } else if (index_id.indexOf("ticket_examen_reaffecter") >= 0) {
                type = "ticket_examen_reaffecter";
            } else if (index_id.indexOf("ticket_examen") >= 0) {
                type = "ticket_examen";
            }
            jQuery("#" + index_id + " .edit").removeClass('hide')
            jQuery("#" + index_id + " .value").addClass('hide')
        });
        jQuery(".input-group-addon-cancel").on("click", function (e) {
            var index_id = jQuery(e.target).parents('.fieldValue').attr('id');
            console.log(index_id)
            var type = "";
            var index = jQuery("#" + index_id).attr("data-field-type");
            if (index_id.indexOf("ticket_examen_test") >= 0) {
                type = "ticket_examen_test";
            } else if (index_id.indexOf("ticket_examen_reaffecter") >= 0) {
                type = "ticket_examen_reaffecter";
            } else if (index_id.indexOf("ticket_examen") >= 0) {
                type = "ticket_examen";
            }
            jQuery("#" + index_id + " .value").removeClass('hide')
            jQuery("#" + index_id + " .edit").addClass('hide')
        })
        jQuery(".input-group-addon-save").on("click", function (e) {
            var index_id = jQuery(e.target).parents('.fieldValue').attr('id');
            console.log(index_id)
            var type = "";
            var index = jQuery("#" + index_id).attr("data-field-type");
            if (index_id.indexOf("ticket_examen_test") >= 0) {
                type = "ticket_examen_test";
            } else if (index_id.indexOf("ticket_examen_reaffecter") >= 0) {
                type = "ticket_examen_reaffecter";
            } else if (index_id.indexOf("ticket_examen") >= 0) {
                type = "ticket_examen";
            }
            var valueInput = jQuery("input[name = '"+index_id+"']").val()
            jQuery("#" + index_id + " .value").text(valueInput)
            jQuery("#" + index_id + " .value").removeClass('hide')
            jQuery("#" + index_id + " .edit").addClass('hide')
        })
    },
    /* uni_cnfsecrm - v2 - modif 176 - FIN */

    registerEvents: function () {
        this._super();
        this.registerAutoComplete();
        /* uni_cnfsecrm - v2 - modif 176 - DEBUT */
        this.setJetonValue();
        /* uni_cnfsecrm - v2 - modif 176 - FIN */
    }

});