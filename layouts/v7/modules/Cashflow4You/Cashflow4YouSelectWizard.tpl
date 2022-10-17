{*<!--
/* * *******************************************************************************
 * The content of this file is subject to the Cashflow4You license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is IT-Solutions4You s.r.o.
 * Portions created by IT-Solutions4You s.r.o. are Copyright(C) IT-Solutions4You s.r.o.
 * All Rights Reserved.
 * ****************************************************************************** */
-->*}
{strip}
<div class="modal-dialog modal-lg">
    <div class="modal-content ">
        {include file="ModalHeader.tpl"|vtemplate_path:$MODULE TITLE=vtranslate('Create_Payment', $MODULE)}
        <div class="modal-body">
        <div class="container-fluid CustomLabelModalContainer">

    <form class="form-horizontal recordEditView" name="massEdit" id="createPayment" method="post" action="index.php">

        {if !empty($PICKIST_DEPENDENCY_DATASOURCE)}
            <input type="hidden" name="picklistDependency" value='{Vtiger_Util_Helper::toSafeHTML($PICKIST_DEPENDENCY_DATASOURCE)}' />
        {/if}
        <input type="hidden" name="module" id="module" value="{$MODULE}">
        <input type="hidden" name="action" value="SaveAjax">
            <input type="hidden" id="idstring" name="idstring" value={$IDSTRING}>
            <input type="hidden" name="balance_payment_hidden" id="balance_payment_hidden" value="{$BALANCE_OPEN_AMOUNT_SUM_HIDDEN}"  >
            <input type="hidden" name="summ_openamount_hidden" id="summ_openamount_hidden" value="{$OPEN_SUM_HIDDEN}"  >
            <input type="hidden" name="summ_outstandingbalance_hidden" id="summ_outstandingbalance_hidden" value="{$OUTSTANDING_SUM_HIDDEN}"  >
            <input type="hidden" name="summ_payment_hidden" id="summ_payment_hidden" value="{$OPEN_SUM_HIDDEN}"  >
            <input type="hidden" name="sourcemodule" id="sourcemodule" value="{$SOURCEMODULE}"  >
            <input type="hidden" name="paymentamount_hidden" id="paymentamount_hidden" value="{$OPEN_SUM_HIDDEN}"  >
            <input type="hidden" id="massEditFieldsNameList" data-value='{Vtiger_Util_Helper::toSafeHTML(ZEND_JSON::encode($MASS_EDIT_FIELD_DETAILS))}' />
            <input type="hidden" name="relatedto" id="relatedto" value="{$RELATEDTO}"  >
            <input type="hidden" name="contactid" id="relatedto" value="{$CONTACT}"  >
            <input type="hidden" name="vat_amount_hidden" id="vat_amount_hidden" value="{$VAT_AMOUNT}"  >
            <input type="hidden" name="vat_amount" id="vat_amount" value="{$VAT_AMOUNT}"  >
            <input type="hidden" name="relationid" id="relationid" value="{$RELATIONID}"  >
            <input type="hidden" name="currency_symbol" id="currency_symbol" value="{$CURRENCY_SYMBOL}"  >

            <input type="hidden" name="dec_sep" id="dec_sep" value="{$DEC_SEP}"  >
            <input type="hidden" name="group_sep" id="group_sep" value="{$GROUP_SEP}"  >
            <input type="hidden" name="dec_place" id="dec_place" value="{$DEC_PLACE}"  >

                <table class="massEditTable table table-bordered equalSplit">
                    <tr>
                    {assign var=COUNTER value=0}
                    {foreach key=FIELD_NAME item=FIELD_MODEL from=$RECORD_STRUCTURE name=blockfields}
                        {assign var="isReferenceField" value=$FIELD_MODEL->getFieldDataType()}
                        {assign var="refrenceList" value=$FIELD_MODEL->getReferenceList()}
                        {assign var="refrenceListCount" value=count($refrenceList)}
                        {if $FIELD_MODEL->get('uitype') eq "19"}
                            {if $COUNTER eq '1'}
                                <td></td><td></td></tr><tr >
                                {assign var=COUNTER value=0}
                            {/if}
                        {/if}
                        {if $COUNTER eq 2}
                            </tr><tr>
                            {assign var=COUNTER value=1}
                        {else}
                            {assign var=COUNTER value=$COUNTER+1}
                        {/if}
                        <td class='fieldLabel'>
                            {if $isReferenceField neq "reference"}<label class="muted pull-right">{/if}
                            {if $FIELD_MODEL->isMandatory() eq true && $isReferenceField neq "reference"} <span class="redColor">*</span> {/if}
                            {if $isReferenceField eq "reference"}
                                {if $refrenceListCount > 1}
                                    {assign var="DISPLAYID" value=$FIELD_MODEL->get('fieldvalue')}
                                    {assign var="REFERENCED_MODULE_STRUCT" value=$FIELD_MODEL->getUITypeModel()->getReferenceModule($DISPLAYID)}
                                    {if !empty($REFERENCED_MODULE_STRUCT)}
                                        {assign var="REFERENCED_MODULE_NAME" value=$REFERENCED_MODULE_STRUCT->get('name')}
                                    {/if}
                                    <span class="pull-right">
                                        {if $FIELD_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}
                                        <select style="width: 150px;" class="chzn-select referenceModulesList" id="referenceModulesList">
                                            <optgroup>
                                                {foreach key=index item=value from=$refrenceList}
                                                    <option value="{$value}" {if $value eq $REFERENCED_MODULE_NAME} selected {/if} >{vtranslate($value, $value)}</option>
                                                {/foreach}
                                            </optgroup>
                                        </select>
                                    </span>
                                {else}
                                    <label class="muted pull-right">{if $FIELD_MODEL->isMandatory() eq true} <span class="redColor">*</span> {/if}{vtranslate($FIELD_MODEL->get('label'), $MODULE)}</label>
                                {/if}
                            {else}
                                {vtranslate($FIELD_MODEL->get('label'), $MODULE)}
                            {/if}
                        {if $isReferenceField neq "reference"}</label>{/if}
                        </td>
                        <td class="fieldValue" {if $FIELD_MODEL->get('uitype') eq '19'} colspan="3" {assign var=COUNTER value=$COUNTER+1} {/if}>
                            {assign var="PICKLIST_EDIT_MODE" value="edit"}
                            {include file=vtemplate_path($FIELD_MODEL->getUITypeModel()->getTemplateName(),$MODULE)}
                        </td>
                    {/foreach}
                    </tr>
                </table>
                            <div class="modal-header contentsBackground">
                                <h3>{vtranslate('PaymentDetails', $MODULE)}</h3>
                            </div>
                            <table class="massEditTable table table-bordered">
                            <tr class='listViewHeaders'>
                                <th width="30%" class="listViewHeaderValues" style="text-align: left;" ><b>{vtranslate(LBL_SUBJECT)}</b></span></th>
                                <th width="10%" class="listViewHeaderValues" style="text-align: right;" ><b>{vtranslate(LBL_TOTAL)}</b></th>
                                <th width="10%" class="listViewHeaderValues" style="text-align: right;"><b>{vtranslate(LBL_CASHFLOW_ALREADY_PAYD , $MODULE)}</b></th>
                                <th width="10%" class="listViewHeaderValues" style="text-align: right;"><b>{vtranslate(LBL_CASHFLOW_OPEN_AMOUNT, $MODULE)}</b></th>
                                <th width="10%" class="listViewHeaderValues" style="text-align: right;"><b>{vtranslate(LBL_PAY_OFF, $MODULE)}</b></th>
                                <th width="10%" class="listViewHeaderValues" style="text-align: right;"><b>{vtranslate(LBL_CASHFLOW_OUTSTANDING_BALANCE, $MODULE)}</b></th>
                                <th class="listViewHeaderValues" style="text-align: right;"><b>{vtranslate(LBL_CASHFLOW_PAYMENT, $MODULE)}</b></th>
                            </tr>
                            {foreach from="$INVOICES" item="invdetail" key="invid" }
                            <tr>
                              <td align="left" class="fieldLabel">{$invdetail.subject}</td>
                              <td class="fieldValue" style="text-align: right;">{$invdetail.hdnGrandTotal}</td>
                              <td class="fieldValue" style="text-align: right;">{$invdetail.paidamount}</td>
                              <td class="fieldValue" style="text-align: right;" name="show_openamount_{$invid}" id="show_openamount_{$invid}" >
                                {$invdetail.show_openamount}
                                <input type="hidden" name="openamount_{$invid}" id="openamount_{$invid}" value="{$invdetail.openamount_hidden}"  >

                              </td>
                              <td class="fieldValue" style="text-align: right;">
                                  <input id="payment_chck_{$invid}" type="checkbox" name="payment_chck_{$invid}" onclick="return Cashflow4You_Actions_Js.SetPayment({$invid});"/>
                              </td>
                              <td class="fieldValue" style="color:#009900;text-align: right;" name="outstandingbalance_{$invid}" id="outstandingbalance_{$invid}">
                                  {$invdetail.outstandingbalance}
                                  <input type="hidden" align="right" name="outstandingbalance_hidden_{$invid}" id="outstandingbalance_hidden_{$invid}" size="12" value="{$invdetail.outstandingbalance_hidden}" >
                              </td>
                              <td class="fieldValue" style="text-align: right;">
                                  <input type="text" align="right" name="payment_{$invid}" id="payment_{$invid}" size="12" value="{$invdetail.openamount}" class="input-small" onchange="Cashflow4You_Actions_Js.checkPayment({$invid})" >
                                  <input type="hidden" align="right" name="previous_payment_{$invid}" id="previous_payment_{$invid}" size="12" value="{$invdetail.openamount_hidden}" >
                                  <span class="icon-repeat" style="margin-left:5px ;" onclick="return Cashflow4You_Actions_Js.RecalculatePayment({$invid});"></span>
                              </td>
                            </tr>
                            {/foreach}
                          {if $INVOICES_NUM > 1}
                            <tr>
                              <td align="left" class="fieldLabel" style="border-top-width:5px;"><strong>{vtranslate(LBL_CASHFLOW_SUMMARY, $MODULE)}</strong></td>
                              <td class="fieldValue" name="summ_total" id="summ_total" style="text-align: right; border-top-width:5px;">{$TOTAL_SUM}</td>
                              <td class="fieldValue" name="summ_paid" id="summ_paid" style="text-align: right; border-top-width:5px;">{$PAID_SUM}</td>
                              <td class="fieldValue" name="summ_openamount" id="summ_openamount" style="text-align: right; border-top-width:5px;">{$REMAINING_SUM}</td>
                              <td class="fieldValue" name="summ_openamount" id="summ_openamount" style="text-align: right; border-top-width:5px;"></td>
                              <td class="fieldValue" name="summ_outstandingbalance" id="summ_outstandingbalance" style="text-align: right; border-top-width:5px;color:#009900;">{$OUTSTANDING_SUM}</td>
                              <td class="fieldValue" name="summ_payment" id="summ_payment" style="text-align: right; border-top-width:5px;">{$OPEN_SUM}</td>
                                                    </tr>
                          {/if}
                            <tr>
                              <td align="left" class="fieldLabel"><strong>{vtranslate(LBL_CASHFLOW_BALANCE, $MODULE)}</strong></td>
                              <td class="fieldValue" style="text-align: right;" name="balance_total" id="balance_total" align="right"></td>
                              <td class="fieldValue" style="text-align: right;" name="balance_paid" id="balance_paid" align="right"></td>
                              <td class="fieldValue" style="text-align: right;" name="balance_openamount" id="balance_openamount" align="right">
                                <b><span style='color:#009900;'>{$BALANCE_OPEN_AMOUNT_SUM}</span></b>
                              </td>
                              <td class="fieldValue" style="text-align: right;" name="balance_outstandingbalance" id="balance_outstandingbalance" align="right"></td>
                              <td class="fieldValue" style="text-align: right;" name="balance_outstandingbalance" id="balance_outstandingbalance" align="right"></td>
                              <td class="fieldValue" style="text-align: right;" name="balance_payment" id="balance_payment" align="right">
                                <b><span style='color:#009900;'>{$BALANCE_PAYMENT_SUM}</span></b>
                              </td>
                            </tr>
                            </table>
                            <div id="paid_is_nan" name="" class="hide" >{vtranslate(paid, $MODULE)} {vtranslate(LBL_CASHFLOW_IS_NAN, $MODULE, $MODULE)}</div>
                            <div id="sumary_is_nan" name="" class="hide" >{vtranslate(LBL_CASHFLOW_SUMMARY, $MODULE)}{vtranslate(LBL_CASHFLOW_PAYMENT, $MODULE)} {vtranslate(LBL_CASHFLOW_IS_NAN, $MODULE)}</div>
                            <div id="open_amount" name="" class="hide" >{vtranslate(LBL_CASHFLOW_OPEN_AMOUNT, $MODULE)} </div>
                            <div id="is_nan" name="" class="hide" >{vtranslate(LBL_CASHFLOW_IS_NAN, $MODULE)}</div>
                            <div id="payment" name="" class="hide" >{vtranslate(LBL_CASHFLOW_PAYMENT, $MODULE)}</div>
                            <div id="high_payment" name="" class="hide" >{vtranslate(LBL_CASHFLOW_HIGH, $MODULE)} {vtranslate(LBL_CASHFLOW_PAYMENT, $MODULE)}. {vtranslate(LBL_CASHFLOW_CHANGE_PAYMENT_QUEST, $MODULE)}</div>
                            <div id="zero_balance" name="" class="hide" >{vtranslate(LBL_CASHFLOW_BALLANCE_OUT_RANGE, $MODULE)}</div>               </form>
            </div>
        </div>
        {assign var=BUTTON_NAME value={vtranslate('LBL_SAVE', $MODULE)}}
        {assign var=BUTTON_ID value="js-save-cashflow"}
        {include file="ModalFooter.tpl"|vtemplate_path:$MODULE}
    </div>
</div>
{/strip}