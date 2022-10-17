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
{* uni_cnfsecrm - v2 - modif 120 - FILE *}
<div class = "quickPreview">
    <input type="hidden" name="sourceModuleName" id="sourceModuleName" value="{$MODULE_NAME}" />
    <input type="hidden" id = "nextRecordId" value ="{$NEXT_RECORD_ID}">
    <input type="hidden" id = "previousRecordId" value ="{$PREVIOUS_RECORD_ID}">
    <input type="hidden" id="idclient" value="{$DETAIL['idclient']}" >
    <div class='quick-preview-modal modal-content'>
        <div class='modal-body'>
            <div class = "quickPreviewModuleHeader row">
                <div class="col-lg-2 pull-right">
                    <button class="close" aria-hidden="true" data-dismiss="modal" type="button" title="Fermer">x</button>
                </div>
            </div>
            {if $NAVIGATION}
                <div class="btn-group pull-right">
                    <a {if $PREVIOUS_RECORD_ID } href="javascript:Vtiger_Detail_Js.openPopupProspects({$PREVIOUS_RECORD_ID},{$PREVIOUS_DEVIS_ID})" {/if} >
                        <button class="btn btn-default btn-xs" id="quickPreviewPreviousRecordButton" data-record="{$PREVIOUS_RECORD_ID}" data-app="{$SELECTED_MENU_CATEGORY}" {if empty($PREVIOUS_RECORD_ID)} disabled="disabled" {*{else} onclick="Vtiger_List_Js.triggerPreviewForRecord({$PREVIOUS_RECORD_ID})"*}{/if} >
                            <i class="fa fa-chevron-left"></i>
                        </button>
                    </a>

                    <a {if $NEXT_RECORD_ID } href="javascript:Vtiger_Detail_Js.openPopupProspects({$NEXT_RECORD_ID},{$NEXT_DEVIS_ID})" {/if}>
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
                                        <button class="btn btn-success btn-xs" id="rappeler" onclick="javascript:Vtiger_Detail_Js.sendEmailPDFClickHandlerProspects('module=SuiviProspects&view=SendEmail&mode=composeMailData&record={$DETAIL['idclient']}');">Rappeler</button>
                                    </div>
                                </div>
                            {elseif $DETAIL['list']['Rappeler'] == 1}
                                {* uni_cnfsecrm - v2 - modif 136 - DEBUT *}
                                <div class="col-lg-4">
                                    <a id="afficherDetail" href="index.php?module=SuiviProspects&view=Detail&record={$DETAIL['suiviprospectsid']}&app=TOOLS">  <button class="btn btn-success  btn-xs"> Afficher Historique Prospects </button> </a>
                                </div>
                                {* uni_cnfsecrm - v2 - modif 136 - FIN *}
                            {/if}
                            <div class="col-lg-3">
                                <button class="btn btn-danger btn-xs" id="nePlusRappeler" onclick="javascript:Vtiger_Detail_Js.nePlusRappelerProspects({$DETAIL['idclient']})"> Ne Plus Rappeler </button>
                            </div>
                        {/if}
                    </div>
                        <br/><br/>
                    <div class="row">
                        <input type="hidden" value="{$DETAIL['iddevis']}" class="iddevis" />
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
                                                <span class="value textOverflowEllipsis"> <a href="index.php?module=Accounts&view=Detail&record={$DETAIL['idclient']}&app=MARKETING"> {$DETAIL_INFO} </a> </span>
                                            {elseif {$key} == 'Devis'}
                                                <span class="value textOverflowEllipsis"> <a href="index.php?module=Quotes&view=Detail&record={$DETAIL['iddevis']}&mode=showDetailViewByMode&requestMode=full&tab_label=Devis%20Details&app=SALES" > {$DETAIL_INFO} </a> </span>   
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

