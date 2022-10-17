{*<!--
/*********************************************************************************
** The contents of this file are subject to the vtiger CRM Public License Version 1.0
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
*
********************************************************************************/
-->*}
<div class = "quickPreview">
    <input type="hidden" name="sourceModuleName" id="sourceModuleName" value="{$MODULE_NAME}" />
    <input type="hidden" id = "nextRecordId" value ="{$NEXT_RECORD_ID}">
    <input type="hidden" id = "previousRecordId" value ="{$PREVIOUS_RECORD_ID}">
    {* uni_cnfsecrm - v2 - modif 111 - DEBUT *}
    <input type="hidden" id="apprenantId" value="{$DETAIL['apprenantid']}" >
    {* uni_cnfsecrm - v2 - modif 111 - FIN *}
    <div class='quick-preview-modal modal-content'>
        <div class='modal-body'>
            <div class = "quickPreviewModuleHeader row">
                <div class="col-lg-2 pull-right">
                    <button class="close" aria-hidden="true" data-dismiss="modal" type="button" title="Fermer">x</button>
                </div>
            </div>
            {if $NAVIGATION}
                <div class="btn-group pull-right">
                    <a {if $PREVIOUS_RECORD_ID } href="javascript:Vtiger_Detail_Js.openPopupRecyclage({$PREVIOUS_RECORD_ID},{$PREVIOUS_SESSION_ID})" {/if} >
                        <button class="btn btn-default btn-xs" id="quickPreviewPreviousRecordButton" data-record="{$PREVIOUS_RECORD_ID}" data-app="{$SELECTED_MENU_CATEGORY}" {if empty($PREVIOUS_RECORD_ID)} disabled="disabled" {*{else} onclick="Vtiger_List_Js.triggerPreviewForRecord({$PREVIOUS_RECORD_ID})"*}{/if} >
                            <i class="fa fa-chevron-left"></i>
                        </button>
                    </a>

                    <a {if $NEXT_RECORD_ID } href="javascript:Vtiger_Detail_Js.openPopupRecyclage({$NEXT_RECORD_ID},{$NEXT_SESSION_ID})" {/if}>
                        <button class="btn btn-default btn-xs" id="quickPreviewNextRecordButton" data-record="{$NEXT_RECORD_ID}" data-app="{$SELECTED_MENU_CATEGORY}" {if empty($NEXT_RECORD_ID)} disabled="disabled" {*{else} onclick="Vtiger_List_Js.triggerPreviewForRecord({$NEXT_RECORD_ID})"*}{/if}>
                            <i class="fa fa-chevron-right"></i>
                        </button>
                    </a>
                </div>
            {/if}
            <div class="quickPreviewActions clearfix">
                <div class = "quickPreviewSummary">
                    <div class="row" id="rowBtn">
                        {if $DETAIL['neplusrappeler'] != 1}
                            {if $DETAIL['list']['Rappeler'] == 0 }
                                <div class="col-lg-3">
                                    <div class="btn-group pull-left" style="padding-left: 20px">    
                                        {* uni_cnfsecrm - v2 - modif 111 - DEBUT *}
                                        {*<a href="javascript:Vtiger_Index_Js.sendEmailPDFClickHandlerPayer('module=HistoryRecyclage&amp;view=SendEmail&amp;mode=composeMailData&amp;record={$RECORDID}&relance=1')"> <input class="btn btn-success btn-xs" type="button" value="Relance"> </a>*}    
                                        <button class="btn btn-success btn-xs" id="rappeler" onclick="javascript:Vtiger_Detail_Js.sendEmailPDFClickHandlerRecyclage('module=HistoryRecyclage&view=SendEmail&mode=composeMailData&record={$DETAIL['apprenantid']}');">Rappeler</button>
                                        {* uni_cnfsecrm - v2 - modif 111 - FIN *}
                                    </div>
                                </div>
                            {elseif $DETAIL['list']['Rappeler'] == 1}
                                {* uni_cnfsecrm - v2 - modif 131 - DEBUT *}
                                <div class="col-lg-4">
                                    <a id="afficherDetail" href="index.php?module=HistoryRecyclage&view=Detail&record={$DETAIL['historyrecyclageid']}&app=TOOLS">  <button class="btn btn-success  btn-xs"> Afficher Historique Recyclages </button> </a>
                                </div>
                                {* uni_cnfsecrm - v2 - modif 131 - FIN *}
                            {/if}
                            <div class="col-lg-4">
                                <button class="btn btn-danger btn-xs" id="nePlusRappeler" onclick="javascript:Vtiger_Detail_Js.nePlusRappeler({$DETAIL['apprenantid']})"> Ne Plus Rappeler </button>
                            </div>
                        {/if}
                    </div>
                        <br/><br/>
                    <div class="row">
                        <input type="hidden" value="{$DETAIL['activityid']}" class="sessionId" />
                        <table class="summary-table no-border" style="width:100%;">
                            <tbody>
                                {foreach key=key item=DETAIL_INFO from=$DETAIL['list']}
                                <tr class="summaryViewEntries">
                                    <td class="fieldLabel col-lg-5">
                                        <label class="muted">
                                            {$key}
                                        </label>
                                    </td>
                                    <td class="fieldValue col-lg-7">
                                        <div class="row">
                                            {if {$key} == 'Nom & prenom'} 
                                                <span class="value textOverflowEllipsis"> <a href="index.php?module=Contacts&view=Detail&record={$DETAIL['contactid']}&app=MARKETING"> {$DETAIL_INFO} </a> </span>
                                            {elseif {$key} == 'Session'}
                                                <span class="value textOverflowEllipsis"> <a href="index.php?module=Calendar&view=Detail&record={$DETAIL['activityid']}&app=SALES" > {$DETAIL_INFO} </a> </span>   
                                            {elseif {$key} == 'Rappeler'}
                                                <span class="value textOverflowEllipsis RappelerValue"> {$DETAIL_INFO} </span>
                                            {else}
                                                <span class="value textOverflowEllipsis"> {$DETAIL_INFO} </span>
                                            {/if}    
                                        </div>
                                    </td>
                                </tr>
                                {/foreach}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

