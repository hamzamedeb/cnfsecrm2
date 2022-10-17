/* * *******************************************************************************
 * The content of this file is subject to the Cashflow4You license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is IT-Solutions4You s.r.o.
 * Portions created by IT-Solutions4You s.r.o. are Copyright(C) IT-Solutions4You s.r.o.
 * All Rights Reserved.
 * ****************************************************************************** */
jQuery.Class("Cashflow4You_Actions_Js",{

    getListViewPopup: function(e,source_module) {
        this.CreatePayment("index.php?=&=&=");
    },

    showPayments: function(e) {
        var recordId = app.getRecordId();
        var sourceModule = app.getModuleName();
        var postData = {
            'module' : 'Cashflow4You',
            'view' : 'ListPayments',
            'source_module': sourceModule,
            'record' : recordId
        };
        app.helper.showProgress();
        app.request.post({'data':postData}).then(function(e,response) {
            app.helper.hideProgress();
            if (e === null) {
                var data = {};
                var callback = function(modalContainer) {
                    modalContainer.find('#js-addpayment-button').on('click', function(){
                        window.location.href = 'index.php?module=Cashflow4You&view=Edit&relationid='+recordId+'&sourceModule='+sourceModule+'&relationOperation=1&sourceRecord='+recordId;
                    });
                }

                data['cb'] = callback;
                app.helper.showModal(response,data);
            }
        });
    },

    CreatePayment: function(e,source_module) {

            var thisInstance = this;
            var listInstance = Vtiger_List_Js.getInstance();
            var validationResult = listInstance.checkListRecordSelected();
            //if (validationResult != true) {
                // Compute selected ids, excluded ids values, along with cvid value and pass as url parameters
                var selectedIds = listInstance.readSelectedIds(true);
                var excludedIds = listInstance.readExcludedIds(true);
                var cvId = listInstance.getCurrentCvId();
                var postData = {
                    'module' : 'Cashflow4You',
                    'view' : 'CreatePaymentActionAjax',
                    'mode' : 'showCreatePaymentForm',
                    'viewname' : cvId,
                    'selected_ids':selectedIds,
                    'excluded_ids' : excludedIds
                };

                app.request.post({'data':postData}).then(function(err,response){
                    if (err == null) {
                        resp = response.split('|||###|||');

                        if( resp.length == 2) {
                            app.helper.showErrorNotification({"message":resp[1]});
                        } else {

                            var callback = function(container) {
                                var massEditForm = container.find('#createPayment');

                                jQuery('[name="paymentamount"]', container).on('change',function() {
                                    thisInstance.checkPaymentAmount(thisInstance);
                                });

                                jQuery('#js-save-cashflow', container).on('click', function() {
                                    var form = container.find('form');
                                    if(form.valid()) {
                                        if(thisInstance.CheckCreatePayment(form)) {
                                            var formData = form.serializeFormData();
                                            app.request.post({'data':formData}).then(function(e,res) {
                                                app.helper.hideProgress();
                                                if (e === null) {
                                                    app.helper.hideModal();
                                                    app.helper.showSuccessNotification({
                                                        'message' : res.message
                                                    });
                                                }

                                                listInstance.loadListViewRecords();

                                            });
                                        }
                                    }
                                });
                            };
                            var data = {};
                            data['cb'] = callback;
                            app.helper.hideProgress();
                            app.helper.showModal(response,data);
                        }
                    }
                });
            //} else {
            //        listInstance.noRecordSelectedAlert();
            //}
        },
        postCEdit : function(massEditContainer) {
		var thisInstance = this;
		massEditContainer.find('form').on('submit', function(e){
			e.preventDefault();
			var form = jQuery(e.currentTarget);
			var invalidFields = form.data('jqv').InvalidFields;
			if(invalidFields.length == 0){
				form.find('[name="saveButton"]').attr('disabled',"disabled");
			}
			var invalidFields = form.data('jqv').InvalidFields;
			if(invalidFields.length > 0){
				return;
			}
			thisInstance.cActionSave(form, true).then(
				function(data) {
                                        var listViewInstance = Vtiger_List_Js.getInstance();
					listViewInstance.getListViewRecords();
					Vtiger_List_Js.clearList();
				},
				function(error,err){
				}
			)
		});
	},
        
    cActionSave : function(form, isMassEdit){
            if(typeof isMassEdit == 'undefined') {
                isMassEdit = false;
            }
            var aDeferred = jQuery.Deferred();
            var massActionUrl = form.serializeFormData();
            if(isMassEdit) {
                //on submit form trigger the massEditPreSave event
                var massEditPreSaveEvent = jQuery.Event(Vtiger_List_Js.massEditPreSave);
                form.trigger(massEditPreSaveEvent);
                if(massEditPreSaveEvent.isDefaultPrevented()) {
                    form.find('[name="saveButton"]').removeAttr('disabled');
                    aDeferred.reject();
                    return aDeferred.promise();
                }
            }
            
            AppConnector.request(massActionUrl).then(
                function(data) {
                    var response = data['result'];
                    var result = response['success'];

                    if(result == true) {
                      //alert(response['message']); 
                      var params = {
                             text: app.vtranslate(response['message']),
                             type: 'info' 
                            };
                      Vtiger_Helper_Js.showPnotify(params);
                    } else {
                      //alert(response['message']); 
                      var params = {
                             text: app.vtranslate(response['message'])
                            };
                      Vtiger_Helper_Js.showPnotify(params);
                    }
                    app.hideModalWindow();
                    aDeferred.resolve(data);
                },
                function(error,err){
                    app.hideModalWindow();
                        aDeferred.reject(error,err);
                }
            );
            return aDeferred.promise();
	},
    getFormatedSum: function(element) {

        var value = element.val();
        var groupSeparator = app.getGroupingSeparator();
        var decimalSeparator = app.getDecimalSeparator();

        if(groupSeparator === "$"){
            groupSeparator = "\\$";
        }
        var strippedValue = value.replace(groupSeparator, '');

        var regex = new RegExp(groupSeparator,'g');
        strippedValue = strippedValue.replace(regex, '');


        var spacePattern = /\s/;
        if(spacePattern.test(groupSeparator)) {
            strippedValue = strippedValue.replace(/ /g, '');
        }

        var strippedValue = strippedValue.replace(decimalSeparator, '.');

        if(spacePattern.test(decimalSeparator)) {
            strippedValue = strippedValue.replace(/ /g, '.');
        }

        if(isNaN(strippedValue)) {
            alert(jQuery('#paid_is_nan').html());
        }
        strippedValue = strippedValue * 1;
        return strippedValue;
    },
    getPaymentAmount: function() {
        var element = jQuery('[name="paymentamount"]');
        return this.getFormatedSum(element);
    },

    setPaymentAmount: function(paymentAmount) {
        var currency_symbol = jQuery('#currency_symbol').val();
        paymentAmount_formated = app.convertCurrencyToUserFormat(paymentAmount, currency_symbol);
        jQuery('[name="paymentamount"]').val(paymentAmount_formated);
    },
    checkPaymentAmount: function(thisInstance){

            var idstring=jQuery('#idstring').val();
            var idlist = idstring.split(';');
            var sum_open_amount = eval(jQuery('#summ_openamount_hidden').val());

            var dec_place = eval(jQuery('#dec_place').val());
            var module = jQuery('#module').val();
            var currency_symbol = jQuery('#currency_symbol').val();

            var paid_amount = thisInstance.getPaymentAmount();

            if(isNaN(sum_open_amount)) {
              alert(jQuery('#sumary_is_nan').html());
            }
            for(var i=0;i<idlist.length;i++) {
                jQuery('#payment_chck_'+idlist[i]).attr('checked', false); 
            }
            var open_amount = paid_amount;
            var tmp_open_amount = paid_amount;
            var tmp_sum_open_amount = sum_open_amount;
            var sum_payment = 0;
            var sum_outstandingbalance = 0;
            
            for(var i=0;i<idlist.length;i++) {
              partial_open_amount = jQuery('#openamount_'+idlist[i]).val()*1;
              j=i+1;
              if(isNaN(partial_open_amount)) {
                alert(jQuery('#open_amount').html()+" "+j+" "+jQuery('#is_nan').html());
              } else {
                if( tmp_open_amount <= 0){
                  payment = 0;
                } else if( tmp_open_amount < partial_open_amount ) {
                  payment = tmp_open_amount;
                  tmp_open_amount = 0;
                } else {
                  payment = partial_open_amount;
                  tmp_open_amount -= partial_open_amount;
                }
                payment = Math.round(payment*100)/100;
                sum_payment += payment;
                jQuery('#payment_'+idlist[i]).val( app.convertCurrencyToUserFormat(payment, ''));
                jQuery('#previous_payment_'+idlist[i]).val( payment.toFixed(2) );

                var outstandingbalance = payment - partial_open_amount;
                outstandingbalance = Math.round((outstandingbalance)*100)/100;

                color="#009900";
                if( outstandingbalance.toFixed(2) != 0.00 ) {
                    color="#FF0000";
                }
                outstandingbalance_formated = app.convertCurrencyToUserFormat(outstandingbalance, currency_symbol);

                jQuery('#outstandingbalance_'+idlist[i]).html(outstandingbalance_formated);
                jQuery('#outstandingbalance_'+idlist[i]).css("color",color);

                tmp_sum_open_amount -= payment
                sum_outstandingbalance += outstandingbalance;
              }
            }
            sum_payment = Math.round(sum_payment*100)/100;

            tmp_sum_open_amount = Math.round(tmp_sum_open_amount*100)/100;
            sum_balance_payment = Math.round((paid_amount-sum_payment)*100)/100;
            sum_outstandingbalance = Math.round((sum_outstandingbalance)*100)/100;


            jQuery('#summ_payment').html(app.convertCurrencyToUserFormat(sum_payment, currency_symbol));
            jQuery('#summ_payment_hidden').html( sum_payment.toFixed(2) );

            color="#009900";
            if( sum_outstandingbalance.toFixed(2) != 0.00 ) {
                color="#FF0000";
            }
            summ_outstandingbalance_formated = app.convertCurrencyToUserFormat(sum_outstandingbalance, currency_symbol);
            jQuery('#summ_outstandingbalance').html(summ_outstandingbalance_formated);
            jQuery('#summ_outstandingbalance').css("color",color);

            jQuery('#summ_outstandingbalance_hidden').html(summ_outstandingbalance_formated);
            jQuery('#summ_outstandingbalance_hidden').css("color",color);

            color="#009900";
            if( tmp_sum_open_amount.toFixed(2) != 0.00 ) {
                color="#FF0000";
            }

            jQuery('#balance_openamount').html(app.convertCurrencyToUserFormat(tmp_sum_open_amount, currency_symbol));
            jQuery('#balance_openamount').css("color",color);
            jQuery('#balance_openamount').css("font-weight",'bold');

            color="#009900";
            if( sum_balance_payment.toFixed(2) != 0.00 ) {
                color="#FF0000";
            }

            jQuery('#balance_payment').html(app.convertCurrencyToUserFormat(sum_balance_payment, currency_symbol));
            jQuery('#balance_payment').css("color",color);
            jQuery('#balance_payment').css("font-weight",'bold');
            jQuery('#balance_payment_hidden').val( sum_balance_payment.toFixed(2) );

            jQuery('#paymentamount_hidden').val( paid_amount.toFixed(2) );
            if( tmp_sum_open_amount.toFixed(2) != 0) {
                jQuery('#vat_amount').val( 0 );
            } else {
               jQuery('#vat_amount').val( eval(jQuery('#vat_amount_hidden').val()) ); 
            }
        },
		
        checkPayment: function( invid ) {
            var idstring=jQuery('#idstring').val();
            var idlist = idstring.split(';');
            var paid_amount = eval(jQuery('#paymentamount_hidden').val());
            var sum_open_amount = eval(jQuery('#summ_openamount_hidden').val());
            var currency_symbol = jQuery('#currency_symbol').val();

            if(isNaN(paid_amount)){
                alert(jQuery('#paid_is_nan').html());
            }
            if(isNaN(sum_open_amount)){
                alert(jQuery('#sumary_is_nan').html());
            }

            var open_amount = paid_amount;
            var tmp_open_amount = paid_amount;
            var tmp_sum_open_amount = sum_open_amount;
            var sum_payment = 0;
            var sum_outstandingbalance = 0;
            var outstandingbalance_tmp = 0;

            for(var i=0;i<idlist.length;i++) {
                j=i+1;

                partial_amount = this.getFormatedSum(jQuery('#payment_'+idlist[i]));
                previous_partial_amount = jQuery('#previous_payment_'+idlist[i]).val() * 1;
                partial_open_amount = jQuery('#openamount_'+idlist[i]).val() * 1;

                if(isNaN(partial_open_amount)) {
                    alert(jQuery('#open_amount').html()+" "+j+" "+jQuery('#is_nan').html());
                } else if(isNaN(partial_amount)) {
                    alert(jQuery('#payment').html()+" "+j+" "+jQuery('#is_nan').html());
                } else if( partial_amount > partial_open_amount ) {
                    payment = partial_amount;
                    tmp_open_amount -= payment;

                    payment = Math.round(payment*100)/100;
                    sum_payment += payment;
                    outstandingbalance = payment - partial_open_amount;
                    outstandingbalance = Math.round((outstandingbalance)*100)/100;
                    sum_outstandingbalance += outstandingbalance;
                    if( invid == idlist[i] ) {
                        if( confirm(jQuery('#high_payment').html()) )  {
                            jQuery('#previous_payment_'+idlist[i]).val( payment.toFixed(2) );
                            color="#009900";
                            if( outstandingbalance.toFixed(2) != 0.00 ) {
                                color="#FF0000";
                            }
                            jQuery('#outstandingbalance_'+idlist[i]).html(app.convertCurrencyToUserFormat(outstandingbalance, currency_symbol));
                            jQuery('#outstandingbalance_'+idlist[i]).css("color",color);
                        } else {
                            payment = previous_partial_amount;
                            tmp_open_amount = Math.round(tmp_partial_open_amount*100)/100;
                            sum_payment -= partial_amount;
                            sum_payment += payment;
                            sum_outstandingbalance -= outstandingbalance;
                            sum_outstandingbalance += payment;
                        }
                        jQuery('#payment_'+idlist[i]).val( app.convertCurrencyToUserFormat(payment,''));
                    }
                    outstandingbalance_tmp += Math.abs(outstandingbalance.toFixed(2));
                    tmp_sum_open_amount -= payment
                } else {
                    payment = partial_amount;
                    tmp_open_amount -= payment;

                    payment = Math.round(payment*100)/100;
                    sum_payment += payment;
                    outstandingbalance = payment - partial_open_amount;
                    outstandingbalance = Math.round((outstandingbalance)*100)/100;
                    sum_outstandingbalance += outstandingbalance;
                    if( invid == idlist[i] ) {
                        jQuery('#payment_'+idlist[i]).val(app.convertCurrencyToUserFormat(payment,''));

                        color="#009900";
                        if( outstandingbalance.toFixed(2) != 0.00 ) {
                            color="#FF0000";
                        }
                        jQuery('#outstandingbalance_'+idlist[i]).html(app.convertCurrencyToUserFormat(outstandingbalance, currency_symbol));
                        jQuery('#outstandingbalance_'+idlist[i]).css("color",color);
                    }
                    tmp_sum_open_amount -= payment;
                    outstandingbalance_tmp += Math.abs(outstandingbalance.toFixed(2));
                }
            }

            tmp_sum_open_amount = Math.round(tmp_sum_open_amount*100)/100;
            sum_balance_payment = Math.round((paid_amount-sum_payment)*100)/100;
            sum_outstandingbalance = Math.round((sum_outstandingbalance)*100)/100;

            jQuery('#summ_payment').html(app.convertCurrencyToUserFormat(sum_payment, currency_symbol));
            jQuery('#summ_payment_hidden').html( sum_payment.toFixed(2) );

            color="#009900";
            if( sum_outstandingbalance.toFixed(2) != 0.00 ) {
                color="#FF0000";
            }
            jQuery('#summ_outstandingbalance').html(app.convertCurrencyToUserFormat(sum_outstandingbalance, currency_symbol));
            jQuery('#summ_outstandingbalance').css("color",color);

            jQuery('#summ_outstandingbalance_hidden').html(app.convertCurrencyToUserFormat(sum_outstandingbalance, currency_symbol));
            jQuery('#summ_outstandingbalance_hidden').css("color",color);

            var color="#009900";
            if( tmp_sum_open_amount.toFixed(2) != 0.00 ) {
                color="#FF0000";
            }

            jQuery('#balance_openamount').html(app.convertCurrencyToUserFormat(tmp_sum_open_amount, currency_symbol));
            jQuery('#balance_openamount').css("color",color);
            jQuery('#balance_openamount').css("font-weight",'bold');

            color="#009900";
            if( sum_balance_payment.toFixed(2) != 0.00 ) {
                color="#FF0000";
            }

            jQuery('#balance_payment').html(app.convertCurrencyToUserFormat(sum_balance_payment, currency_symbol));
            jQuery('#balance_payment').css("color",color);
            jQuery('#balance_payment').css("font-weight",'bold');

            jQuery('#balance_payment_hidden').val( sum_balance_payment.toFixed(2) );
            if( outstandingbalance_tmp.toFixed(2) != 0) {
                vat_amount_val =  0;
            } else {
                vat_amount_val = eval(jQuery('#vat_amount_hidden').val());
            }
            jQuery('#vat_amount').val( vat_amount_val );
        },
        
        SetPayment: function( invid ) {
            var idstring=jQuery('#idstring').val();
            var idlist = idstring.split(';');
            var paid_amount = eval(jQuery('#paymentamount_hidden').val()*1);
            var sum_open_amount = eval(jQuery('#summ_openamount_hidden').val()*1);
            var currency_symbol = jQuery('#currency_symbol').val();
            if(isNaN(paid_amount)){
              alert(jQuery('#paid_is_nan').html());
            }
            if(isNaN(sum_open_amount)){
              alert(jQuery('#sumary_is_nan').html());
            }
            var paid_value = paid_amount;
            if( jQuery('#payment_chck_'+invid).is(":checked")) {
                jQuery('#payment_'+invid).attr('readonly', true);
                for(var i=0;i<idlist.length;i++) {
                    if( jQuery('#payment_chck_'+idlist[i]).is(":checked")){
                        if(idlist[i] != invid ){
                            paid_value -= this.getFormatedSum(jQuery('#payment_'+idlist[i]));
                        }
                    } else {
                        var value = 0.00;
                        jQuery('#payment_'+idlist[i]).val( app.convertCurrencyToUserFormat(value,''));
                        var outstandingbalance = value;
                        outstandingbalance -= jQuery('#openamount_'+idlist[i]).val()*1;

                        color="#009900";
                        if( outstandingbalance.toFixed(2) != 0.00 ) {
                            color="#FF0000";
                        }
                        jQuery('#outstandingbalance_'+idlist[i]).html(app.convertCurrencyToUserFormat(outstandingbalance, currency_symbol));
                        jQuery('#outstandingbalance_'+idlist[i]).css("color",color);

                    }
                }
                var open_amount = jQuery('#openamount_'+invid).val()*1;
                var payment = 0.00;
                var outstandingbalance = 0.00;
                if( paid_value >= open_amount) {
                    payment = open_amount;
                } else if(paid_value > 0) {
                    payment = paid_value;
                    outstandingbalance = payment - open_amount;
                } else {
                    payment = 0.00;
                    outstandingbalance = payment - open_amount;
                }

                jQuery('#payment_'+invid).val(app.convertCurrencyToUserFormat(payment,'') );
                color="#009900";
                if( outstandingbalance.toFixed(2) != 0.00 ) {
                    color="#FF0000";
                }
                jQuery('#outstandingbalance_' + invid).html(app.convertCurrencyToUserFormat(outstandingbalance, currency_symbol));
                jQuery('#outstandingbalance_' + invid).css("color",color);

            } else {
                jQuery('#payment_chck_'+invid).attr('checked', false);
                jQuery('#payment_'+invid).attr('readonly', false);
            }
        },
        
        RecalculatePayment: function( invid ) {
            var idstring=jQuery('#idstring').val();
            var idlist = idstring.split(';');
            var paid_amount = eval(jQuery('#paymentamount_hidden').val())*1;
            
            var balance_payment_hidden = eval(jQuery('#balance_payment_hidden').val());
            var openamount = jQuery('#openamount_'+invid).val()*1;
            var payment = this.getFormatedSum(jQuery('#payment_'+invid));
            
            for(var i=0;i<idlist.length;i++) {
                if(idlist[i] != invid ) {
                    paid_amount -= this.getFormatedSum(jQuery('#payment_'+idlist[i]));
                }
            }
            if( paid_amount > 0) {
                if(paid_amount >= openamount ) {
                    payment = openamount;
                } else {
                    payment = paid_amount;
                }
            } else {
               payment = 0.00;
            }

            jQuery('#payment_'+invid).val( app.convertCurrencyToUserFormat(payment, '') );

            Cashflow4You_Actions_Js.checkPayment(invid);
        },
        
        CheckCreatePayment: function() {
            var balance_payment = eval(jQuery('#balance_payment_hidden').val());
            if( balance_payment != 0.00 ) {
              alert(jQuery('#zero_balance').html());
              return false;
            }  
            return true;
        }
},{
    registerEvents: function (){
    }
});