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
<!-- unicnfsecrm_gestimpaye_01 : commentaires  --> 
{foreach item=DETAIL_VIEW_WIDGET from=$DETAILVIEW_LINKS['DETAILVIEWWIDGET']}
    {if ($DETAIL_VIEW_WIDGET->getLabel() eq 'Documents') }
            {assign var=DOCUMENT_WIDGET_MODEL value=$DETAIL_VIEW_WIDGET}
    {elseif ($DETAIL_VIEW_WIDGET->getLabel() eq 'ModComments')}
            {assign var=COMMENTS_WIDGET_MODEL value=$DETAIL_VIEW_WIDGET}
    {/if}
{/foreach}
<!-- FIN unicnfsecrm_gestimpaye_01 : commentaires -->

<div class = "quickPreview">
    <input type="hidden" name="sourceModuleName" id="sourceModuleName" value="{$MODULE_NAME}" />
    <input type="hidden" id = "nextRecordId" value ="{$NEXT_RECORD_ID}">
    <input type="hidden" id= "current_recordid" value ="{$RECORDID}"/>
    <input type="hidden" id= "etat_echeance" value ="{$etat_echeance}"/>    
    <input type="hidden" id = "previousRecordId" value ="{$PREVIOUS_RECORD_ID}">
    <!-- unicnfsecrm_mod_15 ( quickView ) -->
    <input type="hidden" name="nomfilter" class="nomfilter" value='' />
    <!-- fin unicnfsecrm_mod_15 ( quickView ) -->
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
                    <a href="{$RECORD->getFullDetailViewUrl()}&app={$SELECTED_MENU_CATEGORY}" target="_blank" class="btn btn-success btn-xs">
                       {vtranslate('LBL_VIEW_DETAILS', $MODULE_NAME)} 
                    </a>
                </div>
                    
            <!-- unicnfsecrm_mod_18 -->                               
            <div class="btn-group pull-left" style="padding-left: 20px">    
                 <a href="javascript:Vtiger_Index_Js.sendEmailPDFClickHandlerPayer('module=Invoice&amp;view=SendEmail&amp;mode=composeMailData&amp;record={$RECORDID}&relance=1')"> <input class="btn btn-success btn-xs" type="button" value="Relance"> </a>    
            </div>
            <!-- uni_cnfsecrm - v2 - modif 103 - DEBUT -->
            <div id="detailHistory" class="pull-left">
                {if $RAPPEL != 0}
                    <div id="btnDetailHistory" class="btn-group pull-left btnDetailHistory" style="padding-left: 20px">    
                        <a href="index.php?module=HistoryImpayes&view=Detail&record={$RAPPEL}&app=TOOLS" target="_blank" class="btn btn-success btn-xs">
                           Afficher Historique Impayée 
                        </a>
                    </div>
                {/if}
            </div>
           <!-- uni_cnfsecrm - v2 - modif 103 - FIN -->
           <!-- fin unicnfsecrm_mod_18 -->
           
           <!-- unicnfsecrm_mod_19 -->       
           <div class="btn-group pull-left" style="padding-left: 20px">    
               <input class="btn btn-success btn-xs NePasRelancer" type="button" value="Ne plus relancer">   
           </div>
           <!-- fin unicnfsecrm_mod_19 -->
                    
                    <!-- unicnfsecrm_gestimpaye_03 : export to pdf -->
                    <div class="btn-group pull-left" style="padding-left: 20px">
                        <a href="index.php?module={$MODULE_NAME}&action=ExportPDF&record={$RECORDID}" target="_blank" class="btn btn-success btn-xs">
                            {vtranslate('LBL_EXPORT_TO_PDF', $MODULE_NAME)}
                        </a>
                    </div>
                    <!-- fin unicnfsecrm_gestimpaye_03 : export to pdf -->
                    
                    <!-- unicnfsecrm_gestimpaye_04 : Etat échéance  -->
                    <div class="btn-group pull-left" style="padding-left: 20px">
                        <form method="POST" class="MultiFile-intercepted">
                            <button class="btn btn-success btn-xs updateetatecheance" type="button">Reporter</button>
                        </form>
                    </div>
                    <!-- fin unicnfsecrm_gestimpaye_04 : Etat échéance -->
                    
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
                        <!-- unicnfsecrm_mod_08 -->
                        <tr class="summaryViewEntries">
                            <td class="fieldLabel col-lg-5" ><label class="muted">Date de la facture </label></td>
                            <td class="fieldValue col-lg-7">{$DATE_FACTURE}</td>
                        </tr>    
                        <tr class="summaryViewEntries">
                           <td class="fieldLabel col-lg-5" ><label class="muted">Date d’échéance </label></td>
                            <td class="fieldValue col-lg-7"> {$DATE_ECHEANCE} </td> 
                        </tr>
                        <tr class="summaryViewEntries">
                            <td class="fieldLabel col-lg-5" ><label class="muted">Télephone du client</label></td>
                            <td class="fieldValue col-lg-7"> {$TELEPHONE_CLIENT} </td>
                        </tr>
                        <tr class="summaryViewEntries">
                            <td class="fieldLabel col-lg-5" ><label class="muted">Montant restant dû</label></td>
                            <td class="fieldValue col-lg-7"> {$BALENCE_FACTURE} € </td>   
                        </tr>
                        <!-- fin unicnfsecrm_mod_08 -->
                    </tbody>
                </table>
            </div>
                    
            <!-- unicnfsecrm_gestimpaye_01 : commentaires  -->  
            {if $COMMENTS_WIDGET_MODEL} 
                
                <div class="summaryWidgetContainer">
                    {*{$COMMENTS_WIDGET_MODEL|@var_dump}*}
                    <div class="widgetContainer_comments" data-url="{$COMMENTS_WIDGET_MODEL->getUrl()}" data-name="{$COMMENTS_WIDGET_MODEL->getLabel()}">
                        <div class="widget_header">
                            <input type="hidden" name="relatedModule" value="{$COMMENTS_WIDGET_MODEL->get('linkName')}" />
                            <h3 class="display-inline-block">{vtranslate($COMMENTS_WIDGET_MODEL->getLabel(),$MODULE_NAME)}</h3>
                        </div>
                        <form id="detailView" method="POST" class="MultiFile-intercepted">
                        <div class="widget_contents">  
                            
                        </div>
                        </form>
                    </div>
                </div>
               
            {/if}  
            
            
            {* {if $MODULE_MODEL->isCommentEnabled()}   
                <div class="quickPreviewComments">
                    {include file="ListViewQuickPreviewSectionHeader.tpl"|vtemplate_path:$MODULE_NAME TITLE="{vtranslate('LBL_RECENT_COMMENTS',$MODULE_NAME)}"}
                    {include file="QuickViewCommentsList.tpl"|vtemplate_path:$MODULE_NAME}
                </div>
            {/if} *}
            <!-- FIN unicnfsecrm_gestimpaye_01 : commentaires -->
            
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

