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
{* uni_cnfsecrm - modif 101 - FILE *}
<div class = "quickPreview">
    <div class='quick-preview-modal modal-content'>
        <div class='modal-body'>
            <div class = "quickPreviewModuleHeader row">
                <div class="col-lg-2 pull-right">
                    <button class="close" aria-hidden="true" data-dismiss="modal" type="button" title="Fermer">x</button>
                </div>
            </div>
            {*{$DETAIL|print_r}*}
            <div class="quickPreviewActions clearfix">
                <div class = "quickPreviewSummary">
                    <div class="row" id="rowBtn">
                        {if $DETAIL['neplusrappeler'] != 1}
                            {if $DETAIL['list']['Rappeler'] == 0 }
                                <div class="col-lg-3">
                                    <button class="btn btn-success btn-xs" id="rappeler" onclick="javascript:Vtiger_Detail_Js.triggerSendEmailPopUp('index.php?module=Contacts&view=MassActionAjax&mode=showComposeEmailForm&step=step1','Emails',{$DETAIL['apprenantid']});">Rappeler</button>
                                </div>
                            {elseif $DETAIL['list']['Rappeler'] == 1}
                                <div class="col-lg-3">
                                    <a id="afficherDetail" href="index.php?module=HistoryRecyclage&view=Detail&record={$DETAIL['historyrecyclageid']}&app=TOOLS">  <button class="btn btn-info  btn-xs"> Afficher Les Détails </button> </a>
                                </div>
                            {/if}
                            <div class="col-lg-3">
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

