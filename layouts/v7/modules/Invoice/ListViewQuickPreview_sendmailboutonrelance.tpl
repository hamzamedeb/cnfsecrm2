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
                    
                <!-- unicnfsecrm_gestimpaye_02 : envoi mail relance -->
                <div class="btn-group pull-left envoiemail " style="padding-left: 20px">
                    <form class="form-horizontal" id="massEmailForm" method="post" action="index.php" enctype="multipart/form-data" name="massEmailForm">
                        <input type="hidden" name="selected_ids" value='' />
                        <input type="hidden" name="excluded_ids" value='' />
                        <input type="hidden" id="flag" name="flag" value="" />
                        <input type="hidden" name="viewname" value="" />
                        <input type="hidden" name="module" value="Emails"/>
                        <input type="hidden" name="mode" value="massSave" />
                        <input type="hidden" name="toemailinfo" value='{ZEND_JSON::encode($TOMAIL_INFO)}' />
                        <input type="hidden" name="view" value="MassSaveAjax" />
                        <input type="hidden"  name="to" value=["{$EMAIL}"] />
                        <input type="hidden"  name="toMailNamesList" value='{$TOMAILNAMESLIST}'/>
                        <input type="hidden" id="maxUploadSize" value="52428800" />
                        <input type="hidden" id="documentIds" name="documentids" value="" />
                        <input type="hidden" name="emailMode" value="" />
                        <input type="hidden" name="source_module" value="" />
                        <input type="hidden" name="search_key" value= "" />
                        <input type="hidden" name="operator" value="" />
                        <input type="hidden" name="search_value" value="" />
                        <input type="hidden" name="search_params" value="null" />
                        <input type="hidden" class="toEmail" id="toEmail" name="toEmail" value="{$EMAIL}" />
                        <input type="hidden" name="cc" value="" />
                        <input type="hidden" name="bcc" value="" />
                        <input type="hidden" name="subject" value="relance" />
                        <input type="hidden" name="signature" value="Yes" />
                        <input type="hidden" name="description" value=" <html><body>Email de relance</body></html>" />
                        <input type="hidden" name="attachments" value='{ZEND_JSON::encode($ATTACHMENTS)}' />

                        <button class="btn btn-success btn-xs" id="sendEmail" name="sendemail">
                            {vtranslate('LBL_RELANCE', $MODULE_NAME)}
                        </button>
                    </form>
                </div>  
                    <!-- fin unicnfsecrm_gestimpaye_02 : envoi mail relance -->
                    
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

