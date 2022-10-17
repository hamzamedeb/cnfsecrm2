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
{* uni_cnfsecrm - v2 - modif 103 - FILE *}
<div class = "quickPreview">
    <input type="hidden" name="sourceModuleName" id="sourceModuleName" value="{$MODULE_NAME}" />
    <input type="hidden" id = "nextRecordId" value ="{$NEXT_RECORD_ID}">
    <input type="hidden" id = "previousRecordId" value ="{$PREVIOUS_RECORD_ID}">

    <div class='quick-preview-modal modal-content'>
        <div class='modal-body'>
            <div class = "quickPreviewModuleHeader row">
                <div class = "col-lg-10">
                    <div class="row qp-heading">
                        {include file="ListViewQuickPreviewHeaderTitle.tpl"|vtemplate_path:$MODULE_NAME MODULE_MODEL=$MODULE_MODEL RECORD=$RECORD}
                    </div>
                </div>
                <div class = "col-lg-2 pull-right">
                    <button class="close" aria-hidden="true" data-dismiss="modal" type="button" title="{vtranslate('LBL_CLOSE')}">x</button>
                </div>
            </div> 

            <div class="quickPreviewActions clearfix">
                <div class="btn-group pull-left">
                    <button class="btn btn-success btn-xs" onclick="window.location.href = '{$RECORD->getFullDetailViewUrl()}&app={$SELECTED_MENU_CATEGORY}'">
                       {vtranslate('LBL_VIEW_DETAILS', $MODULE_NAME)} 
                    </button>
                </div>
                {if $NAVIGATION}
                    <div class="btn-group pull-right">
                        <button class="btn btn-default btn-xs" id="quickPreviewPreviousRecordButton" data-record="{$PREVIOUS_RECORD_ID}" data-app="{$SELECTED_MENU_CATEGORY}" {if empty($PREVIOUS_RECORD_ID)} disabled="disabled" {*{else} onclick="Vtiger_List_Js.triggerPreviewForRecord({$PREVIOUS_RECORD_ID})"*}{/if} >
                            <i class="fa fa-chevron-left"></i>
                        </button>
                        <button class="btn btn-default btn-xs" id="quickPreviewNextRecordButton" data-record="{$NEXT_RECORD_ID}" data-app="{$SELECTED_MENU_CATEGORY}" {if empty($NEXT_RECORD_ID)} disabled="disabled" {*{else} onclick="Vtiger_List_Js.triggerPreviewForRecord({$NEXT_RECORD_ID})"*}{/if}>
                            <i class="fa fa-chevron-right"></i>
                        </button>
                    </div>
                {/if}

            </div>
            <div class = "quickPreviewSummary">
                <input type="hidden" class="clientid" name="clientid" value="{$DETAIL['clientId']}">
                <input type="hidden" class="invoiceid" name="invoiceid" value="{$DETAIL['invoiceId']}" >
                <input type="hidden" class="recordId" name="recordId" value="{$DETAIL['recordId']}" >
                <table class="summary-table no-border" style="width:100%;">
                    <tbody>
                        {foreach item=FIELD_MODEL key=FIELD_NAME from=$SUMMARY_RECORD_STRUCTURE['SUMMARY_FIELDS']}
                            {if $FIELD_MODEL->get('name') neq 'modifiedtime' && $FIELD_MODEL->get('name') neq 'createdtime'}
                                <tr class="summaryViewEntries">
                                    <td class="fieldLabel col-lg-5" ><label class="muted">{vtranslate($FIELD_MODEL->get('label'),$MODULE_NAME)}</label></td>
                                    <td class="fieldValue col-lg-7">
                                        <div class="row">
                                            <span class="value textOverflowEllipsis" {if $FIELD_MODEL->get('uitype') eq '19' or $FIELD_MODEL->get('uitype') eq '20' or $FIELD_MODEL->get('uitype') eq '21'}style="word-wrap: break-word;"{/if}>
                                                {include file=$FIELD_MODEL->getUITypeModel()->getDetailViewTemplateName()|@vtemplate_path:$MODULE_NAME FIELD_MODEL=$FIELD_MODEL USER_MODEL=$USER_MODEL MODULE=$MODULE_NAME RECORD=$RECORD}
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            {/if}
                        {/foreach}
                         
                         {foreach item=FIELD_MODEL key=FIELD_NAME from=$DETAIL['list']}
                                <tr class="summaryViewEntries">
                                    <td class="fieldLabel col-lg-5" ><label class="muted">{$FIELD_NAME}</label></td>
                                    <td class="fieldValue col-lg-7">
                                        <div class="row">
                                            {if {$FIELD_NAME} == 'Nom & prenom'} 
                                                <span class="value textOverflowEllipsis"> <a href="index.php?module=Accounts&view=Detail&record={$DETAIL['clientId']}&app=MARKETING"> {$FIELD_MODEL} </a> </span>
                                            {elseif {$FIELD_NAME} == 'Facture'}
                                                <span class="value textOverflowEllipsis"> <a href="index.php?module=Invoice&view=Detail&record={$DETAIL['invoiceId']}&app=INVENTORY" > {$FIELD_MODEL} </a> </span>   
                                            {else}
                                                <span class="value textOverflowEllipsis">
                                                {$FIELD_MODEL}
                                                </span>
                                            {/if}    
                                        </div>
                                    </td>
                                </tr>
                        {/foreach}
                    </tbody>
                </table>
                    
                <table class="table" id="tableAction">
                    {* uni_cnfsecrm - v2 - modif 119 - DEBUT *}
                    <tbody>
                        <th>Réponse Par</th>
                        <th>Date</th>
                        <th>Date Echéance</th>
                        <th>Type Relance</th>
                        <th>Commentaire</th>
                    </tbody>
                    {* uni_cnfsecrm - v2 - modif 119 - FIN *}
                    <tr id="rowNew">
                        <td>
                            <select class="form-control reponsePar">
                                <option value="1">Par Telephone</option>
                                <option value="2">Par Email</option>
                            </select>
                        </td>
                        <td>
                            <input type="date" value="" class="form-control dateRappel" />
                        </td>
                        <td>
                            <input type="date" value="" class="form-control dateEcheance" />
                        </td>
                        <td>
                            <select class="form-control typeRelance">
                                <!-- correction 1 cnfse - DEBUT -->
                                <option value="Depasse de 7 jours">Dépassé de 7 jours</option>
                                <option value="Depasse de 14 jours">Dépassé de 14 jours</option>
                                <option value="Depasse de 30 jours">Dépassé de 30 jours</option>
                                <!-- correction 1 cnfse - FIN -->
                            </select>
                        </td>
                        <td>
                            <textarea class="form-control commentaire"></textarea>
                        </td>
                        <td>
                            <button style="font-size: 20px;" class="btn btn-success pull-right" id="addRow" onclick="HistoryImpayes_Edit_Js.addEditRow('save','')">+</button> 
                        </td>
                    </tr>
                </table>
                
                <br>
                <table id="tableData" class="table">
                    {foreach item=FIELD_MODEL key=FIELD_NAME from=$DETAIL_HISTORIQUE}
                        <tr id="reponse{$FIELD_MODEL['id']}">
                            <td>{$FIELD_MODEL['reponse_par']}</td>
                            <td>{$FIELD_MODEL['date_rappel']}</td>
                            <td>{$FIELD_MODEL['date_echeance']}</td>
                            <td>
                                {* uni_cnfsecrm - v2 - modif 118 - DEBUT *}
                                {if $FIELD_MODEL['type_relance'] == "Depasse de 7 jours"}
                                    Dépassé de 7 jours
                                {else if $FIELD_MODEL['type_relance'] == "Depasse de 14 jours"}
                                    Dépassé de 14 jours
                                {else if $FIELD_MODEL['type_relance'] == "Depasse de 30 jours"}
                                    Dépassé de 30 jours
                                {/if}   
                                {* uni_cnfsecrm - v2 - modif 118 - FIN *}
                            </td>
                            <td>{$FIELD_MODEL['commentaire']}</td>
                            <td><span onclick="HistoryImpayes_Edit_Js.editRow({$FIELD_MODEL['id']})" class="fa fa-pencil"></span></td>
                        </tr>
                        <tr id="reponseedit{$FIELD_MODEL['id']}" class="hidden">
                            <td>
                                <select class="form-control reponsePar">
                                    <option {if {$FIELD_MODEL['reponse_parId']} == 1} selected="selected" {/if} value="1">Par Telephone</option>
                                    <option {if {$FIELD_MODEL['reponse_parId']} == 2} selected="selected" {/if} value="2">Par Email</option>
                                </select>
                            </td>
                            <td>
                                <input type="input" value="{$FIELD_MODEL['date_rappel']}" data-date-format="dd-mm-yyyy" class="form-control dateField dateRappel" />
                            </td>
                            <td>
                                <input type="input" value="{$FIELD_MODEL['date_echeance']}" data-date-format="dd-mm-yyyy" class="form-control dateField dateEcheance" />
                            </td>
                            <td>
                                <select class="form-control typeRelance">
                                    <!-- correction 1 cnfse - DEBUT -->
                                    <option {if {$FIELD_MODEL['type_relance']} == "Depasse de 7 jours"} selected="selected" {/if} value="Depasse de 7 jours">Dépassé de 7 jours</option>
                                    <option {if {$FIELD_MODEL['type_relance']} == "Depasse de 14 jours"} selected="selected" {/if} value="Depasse de 14 jours">Dépassé de 14 jours</option>
                                    <option {if {$FIELD_MODEL['type_relance']} == "Depasse de 30 jours"} selected="selected" {/if} value="Depasse de 30 jours">Dépassé de 30 jours</option>
                                    <!-- correction 1 cnfse - FIN -->
                                </select>
                            </td>
                            <td>
                                <textarea class="form-control commentaire">{$FIELD_MODEL['commentaire']}</textarea>
                            </td>
                            <td> <span onclick="HistoryImpayes_Edit_Js.addEditRow('edit', {$FIELD_MODEL['id']})" class="pointerCursorOnHover input-group-addon input-group-addon-save"><i class="fa fa-check"></i></span> </td>
                            <td> <span onclick="HistoryImpayes_Edit_Js.annulerEditRow({$FIELD_MODEL['id']})" class="pointerCursorOnHover input-group-addon input-group-addon-cancel"><i class="fa fa-close"></i></span> </td>
                        </tr>
                    {/foreach}
                </table>
               
            </div>
            
            <div class="engagementsContainer">
                {include file="ListViewQuickPreviewSectionHeader.tpl"|vtemplate_path:$MODULE_NAME TITLE="{vtranslate('LBL_UPDATES',$MODULE_NAME)}"}
                {include file="RecentActivities.tpl"|vtemplate_path:$MODULE_NAME}
            </div>

            <br>
            {if $MODULE_MODEL->isCommentEnabled()}
                <div class="quickPreviewComments">
                    {include file="ListViewQuickPreviewSectionHeader.tpl"|vtemplate_path:$MODULE_NAME TITLE="{vtranslate('LBL_RECENT_COMMENTS',$MODULE_NAME)}"}
                    {include file="QuickViewCommentsList.tpl"|vtemplate_path:$MODULE_NAME}
                </div>
            {/if}
        </div>
    </div>
</div>

