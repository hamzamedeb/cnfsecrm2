{*
/* * *******************************************************************************
 * The content of this file is subject to the Cashflow4You license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is IT-Solutions4You s.r.o.
 * Portions created by IT-Solutions4You s.r.o. are Copyright(C) IT-Solutions4You s.r.o.
 * All Rights Reserved.
 * ****************************************************************************** */
*}
{strip}
<div class="modal-dialog modelContainer">
    <div class="modal-content">
        {assign var=HEADER_TITLE value={vtranslate('Payments', {$MODULE_NAME})}}
        {include file="ModalHeader.tpl"|vtemplate_path:$MODULE TITLE=$HEADER_TITLE}
        <div class="padding10">
            <div id="table-content" class="table-container">
                    <table class="table listview-table">
                        {if $PAYTYPE != 'loadPayments' }
                        <thead>
                        <tr class="listViewContentHeader">
                                <td colspan="2" nowrap="nowrap">
                                    <b>{vtranslate('Grand Total', 'Cashflow4You')}</b>
                                </td>
                                <td nowrap="nowrap" align='right'>
                                    <b>{$GRAND_TOTAL} {$GTOTAL_CURRENCY}</b>
                                </td>
                            </tr>
                        </thead>
                        <tbody class="overflow-y">
                            {foreach  item="PAYMENT" key="PAYMENT_ID" from=$PAYMENTS}
                                <tr>
                                    <td nowrap="nowrap">
                                        <small>{vtranslate($PAYMENT.paymentstatus, 'Cashflow4You')}</small>
                                    </td>
                                    <td nowrap="nowrap">
                                        <small>{$PAYMENT.paymentdate}</small>
                                    </td>
                                    <td align='right' nowrap="nowrap">
                                        <a href='index.php?module=Cashflow4You&view=Detail&record={$PAYMENT_ID}&return_module=Cashflow4You'>
                                           <small> {$PAYMENT.amount}</small>
                                        </a>
                                    </td>
                                </tr>
                            {/foreach}
                        <tr>
                            <td colspan="2">
                                <b>{vtranslate('Total amount', 'Cashflow4You')}</b>
                            </td>
                            <td align='right' nowrap>
                                <b>{$TOTAL}</b>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <b>{vtranslate('Balance', 'Cashflow4You')}</b>
                            </td>
                            <td align='right' nowrap>
                                <b>{$TOTAL_BALLANCE}</b>
                            </td>
                        </tr>
                        </tbody>
                    {/if}
               </table>
            </div>
        </div>
        <br>
            {assign var=BUTTON_NAME value={vtranslate('Create_Payment', 'Cashflow4You')}}
            {assign var=BUTTON_ID value="js-addpayment-button"}
            {include file="ModalFooter.tpl"|vtemplate_path:$MODULE}

            <div id="alert_doc_title" style="display:none;">{$CASHFLOW4YOU_MOD.ALERT_DOC_TITLE}</div>
    </div>
</div>
