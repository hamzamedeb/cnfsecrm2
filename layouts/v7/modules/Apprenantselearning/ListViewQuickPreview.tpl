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
{* uni_cnfsecrm - modif 82 - FILE *}
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
                <div class="btn-group">
                    <form method="post" action="index.php" enctype="multipart/form-data" id="massEmailFormRappel" name="massEmailFormRappel">
                        <input type="hidden" name="selected_ids" value='' />
                        <input type="hidden" name="excluded_ids" value='' />
                        <input type="hidden" id="flagRappel" name="flag" value="" />
                        <input type="hidden" name="viewname" value="" />
                        <input type="hidden" name="module" value="Emails"/>
                        <input type="hidden" name="mode" value="massSave" />
                        <input type="hidden" name="toemailinfo" value='' />
                        <input type="hidden" name="view" value="MassSaveAjax" />
                        <input type="hidden"  name="to" value=["{$DETAIL_APPRENANT['detail']['email']}"] />
                        {*<input type="hidden"  name="to" value="contact@cnfse.fr" />*}
                        <input type="hidden"  name="toMailNamesList" value=''/>
                        <input type="hidden" id="maxUploadSize" value="52428800" />
                        <input type="hidden" id="documentIds" name="documentids" value="" />
                        <input type="hidden" name="emailMode" value="" />
                        <input type="hidden" name="source_module" value="" />
                        <input type="hidden" name="search_key" value= "" />
                        <input type="hidden" name="operator" value="" />
                        <input type="hidden" name="search_value" value="" />
                        <input type="hidden" name="search_params" value="null" />
                        <input type="hidden" class="toEmail" id="toEmail" name="toEmail" value="{$DETAIL_APPRENANT['detail']['email']}" />
                        {*<input type="hidden" class="toEmail" id="toEmail" name="toEmail" value="contact@cnfse.fr" />*}
                        <input type="hidden" name="cc" value="" />
                        <input type="hidden" name="bcc" value="" />
                        <input type="hidden" name="subject" value="{$DETAIL_APPRENANT['subject']}" />
                        <input type="hidden" name="signature" value="Yes" />
                        <input type="hidden" name="description" value="{$DETAIL_APPRENANT['description']}" />
                        {* uni_cnfsecrm - modif 84 - DEBUT *}
                        {if $DETAIL_APPRENANT['detail']['Statut'] neq "Stagiaire à inscrire" and $DETAIL_APPRENANT['detail']['Statut'] neq "Fini la formation" and $DETAIL_APPRENANT['detail']['Statut'] neq "Inscription ignoré" }
                        {* uni_cnfsecrm - modif 84 - FIN *}
                            {if $DETAIL_APPRENANT['detail']['Rappel Email'] eq 0}
                                {if $DETAIL_APPRENANT['rappel']['type'] > 6}
                                {$emailRappel = 1}  
                                {else}
                                {$emailRappel = 0}
                                {/if}
                            {/if}
                        {/if}
                        <input value="Rappel pour {$DETAIL_APPRENANT['rappel']['type']} jours" onclick="Apprenantselearning_Edit_Js.sendEmailRappel({$DETAIL_APPRENANT['rappel']['type']},{$RECORD->getId()},'rappel')" style="margin-left: 10px;" id="sendEmailRappel" name="sendEmailRappel" class="btn btn-success btn-xs {if $emailRappel eq 0} hidden {/if} " type="button"> 
                                
                        {if $DETAIL_APPRENANT['detail']['Statut'] eq "Stagiaire à inscrire"}
                        {$marquerInscrit = 1}
                        {else}
                        {$marquerInscrit = 0}    
                        {/if} 
                        <input value="Marquer Inscrit" onclick="Apprenantselearning_Edit_Js.sendEmailRappel({$DETAIL_APPRENANT['rappel']['type']},{$RECORD->getId()},'inscrit')" style="margin-left: 10px;" id="marquerInscrit" name="marquerInscrit" class='btn btn-success btn-xs {if $marquerInscrit eq 0} hidden {/if} ' type="button">
                        
                    </form>  
                </div>
                {if $DETAIL_APPRENANT['detail']['Statut'] eq "Fini la formation" }
                    {if ($DETAIL_APPRENANT['servicecategory'] == 'AIPR' or $DETAIL_APPRENANT['servicecategory'] == 'HABILITATIONS')}
                    {$envoiAttestation = 0}
                    {$envoiAvisAttestation = 1}
                    {else}    
                    {$envoiAttestation = 1}
                    {$envoiAvisAttestation = 0}
                    {/if}    
                {else}
                {$envoiAttestation = 0}    
                {$envoiAvisAttestation = 0}    
                {/if} 
                <div class="btn-group">
                    <a class="btn btn-success btn-xs {if $envoiAvisAttestation eq 0} hidden {/if}" id="envoiAvisAttestation" href="javascript:Apprenantselearning_Edit_Js.sendEmailPDFClickHandler('module=Events&amp;view=SendEmail&amp;mode=composeMailData&amp;record={$DETAIL_APPRENANT['activityid']}&amp;appr={$DETAIL_APPRENANT['apprenant']}&amp;email={$DETAIL_APPRENANT['detail']['email']}&amp;doc=avisetattestation')">Envoi Avis & Attestation</a>
                    <a class="btn btn-success btn-xs {if $envoiAttestation eq 0} hidden {/if}" id="envoiAttestation" href="javascript:Apprenantselearning_Edit_Js.sendEmailPDFClickHandler('module=Events&amp;view=SendEmail&amp;mode=composeMailData&amp;record={$DETAIL_APPRENANT['activityid']}&amp;appr={$DETAIL_APPRENANT['apprenant']}&amp;email={$DETAIL_APPRENANT['detail']['email']}&amp;doc=attestation')">Envoi Attestation</a>
                </div>
                {* uni_cnfsecrm - modif 84 - DEBUT *}
                <div class="btn-group">
                    {if $DETAIL_APPRENANT['detail']['Statut'] eq "Stagiaire à inscrire"} 
                    {$ignoreInscription = 1}
                    {else}
                    {$ignoreInscription = 0}    
                    {/if}
                    <input onclick="Apprenantselearning_Edit_Js.updateStatut({$RECORD->getId()},'ignoreInscription')" type="button" name="" id="ignoreInscription" value="Ignorer l'inscription" class="btn btn-success btn-xs {if $ignoreInscription eq 0} hidden {/if} ">
                </div>
                {* uni_cnfsecrm - modif 84 - FIN *}
                 <br/><br/>
                
                <div class="btn-group">
                    {if (($DETAIL_APPRENANT['servicecategory'] == 'AIPR' or $DETAIL_APPRENANT['servicecategory'] == 'HABILITATIONS') and $DETAIL_APPRENANT['theoriqueCase'] == 1 and $DETAIL_APPRENANT['detail']['Statut'] eq "Rendez-vous pratique" ) or ($DETAIL_APPRENANT['servicecategory'] == 'HYGIENE' and  $DETAIL_APPRENANT['detail']['Statut'] eq "En cours de formation" ) } 
                    {$validerFormation = 1}
                    {else}
                    {$validerFormation = 0}    
                    {/if}
                    <input onclick="Apprenantselearning_Edit_Js.updateStatut({$RECORD->getId()},'validerFormation')" type="button" name="" id="validerFormation" value="Valider la formation" class="btn btn-success btn-xs {if $validerFormation eq 0} hidden {/if} ">
                    
                    {if ($DETAIL_APPRENANT['servicecategory'] == 'AIPR' or $DETAIL_APPRENANT['servicecategory'] == 'HABILITATIONS') and $DETAIL_APPRENANT['detail']['Statut'] eq "En cours de formation"} 
                    {$validerTheorique = 1}
                    {else}
                    {$validerTheorique = 0}    
                    {/if}
                    <input onclick="Apprenantselearning_Edit_Js.updateStatut({$RECORD->getId()},'validerTheorique')" type="button" name="" id="validerFormationTheorique" value="Valider la formation théorique" class="btn btn-success btn-xs {if $validerTheorique eq 0} hidden {/if} ">
                    
                </div> 
                <br/><br/>
                {if ($DETAIL_APPRENANT['servicecategory'] == 'AIPR' or $DETAIL_APPRENANT['servicecategory'] == 'HABILITATIONS') and $DETAIL_APPRENANT['theoriqueCase'] == 1 and $DETAIL_APPRENANT['detail']['Statut'] eq "Rendez-vous à prendre" } 
                    {$formTheorique = 1}
                    {else}
                    {$formTheorique = 0}    
                {/if}
                <form name="form1" method="POST" id="formRendezVous" {if $formTheorique eq 0} class="hidden"{/if} >
                    <div class="row">
                       <div class="col-md-4">
                            <label class="muted">Date Rendez-vous</label>                    
                       </div> 
                    </div>
                    <div class="row">
                        <div class="col-md-5">                    
                            <div class="input-group inputElement"  style="margin-bottom: 5px">                        
                                <input required id="dateRendezVous" type="text" class="dateField form-control" data-fieldtype="date" name="dateRendezVous" data-date-format="dd-mm-yyyy" value="{$DETAIL_APPRENANT['detail']['Date Rendez-Vous']}" data-rule-date="true" aria-invalid="false">
                                <span class="input-group-addon"><i class="fa fa-calendar "></i></span>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="input-group inputElement" style="width: 20px" >                        
                                <input id="heureRendezVous" type="text" value="" data-format="24" name="heureRendezVous" class="timepicker-default ui-timepicker-input form-control smallInputBox" value="{$DETAIL_APPRENANT['detail']['Heure Rendez-Vous']}" autocomplete="off" aria-invalid="false">
                                <span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                            </div>
                        </div> 
                    </div>
                    <div class="row">
                        <input style="margin-left: 10px;" onclick="Apprenantselearning_Edit_Js.updateStatut({$RECORD->getId()},'ajouterRendezVous')" type="button" name="" value="Ajouter rendez-vous" class="btn btn-success btn-xs">
                    </div>
                </form>
                
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
                <input type="hidden" name="typeFormation" id="typeFormation" value="{$DETAIL_APPRENANT['servicecategory']}">
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
                        {* uni_cnfsecrm_elearning - 003 *}
                        {foreach item=FIELD_MODEL key=FIELD_NAME from=$DETAIL_APPRENANT['detail']}
                           {if $FIELD_NAME neq 'Date Rendez-Vous' and $FIELD_NAME neq 'Heure Rendez-Vous'}
                            <tr class="summaryViewEntries">
                                <td class="fieldLabel col-lg-5" ><label class="muted">{$FIELD_NAME}</label></td>
                                <td class="fieldValue col-lg-7">
                                    <div class="row">
                                        {if {$FIELD_NAME} eq 'Statut'}
                                        <span class="value textOverflowEllipsis" id="statutFormation" style="word-wrap: break-word;">
                                        {else if {$FIELD_NAME} eq 'Rappel Email'}
                                        <span class="value textOverflowEllipsis" id="emailApprenant" style="word-wrap: break-word;">
                                        {else}   
                                        <span class="value textOverflowEllipsis" style="word-wrap: break-word;">
                                        {/if}    
                                            {$FIELD_MODEL}  
                                        </span>
                                    </div>
                                </td>
                            </tr>
                            {else if $FIELD_NAME eq 'Date Rendez-Vous'}
                                <tr id ="DateRendezVousChamp" class="summaryViewEntries {if $DETAIL_APPRENANT['detail']['Statut'] neq "Rendez-vous pratique"}hidden{/if}" >
                                <td class="fieldLabel col-lg-5" ><label class="muted">{$FIELD_NAME}</label></td>
                                <td class="fieldValue col-lg-7">
                                    <div class="row">
                                        <span class="value textOverflowEllipsis" style="word-wrap: break-word;">    
                                            {$FIELD_MODEL}  
                                        </span>
                                    </div>
                                </td>
                            </tr>
                            {else if $FIELD_NAME eq 'Heure Rendez-Vous'}
                                <tr id ="HeureRendezVousChamp" class="summaryViewEntries {if $DETAIL_APPRENANT['detail']['Statut'] neq "Rendez-vous pratique"}hidden{/if}" >
                                <td class="fieldLabel col-lg-5" ><label class="muted">{$FIELD_NAME}</label></td>
                                <td class="fieldValue col-lg-7">
                                    <div class="row">
                                        <span class="value textOverflowEllipsis" style="word-wrap: break-word;">    
                                            {$FIELD_MODEL}  
                                        </span>
                                    </div>
                                </td>
                            </tr>
                            {/if}
                        {/foreach}
                        {if $DETAIL_APPRENANT['rappel']['type'] > 6}
                        <tr class="summaryViewEntries tdrappeltel">
                            <td class="fieldLabel col-lg-5" ><label class="muted">Rappel Telephonique</label></td>
                            <td class="fieldValue col-lg-7">
                                <div class="row">
                                    <span class="value textOverflowEllipsis" style="word-wrap: break-word;">
                                        <input onchange="Apprenantselearning_Edit_Js.updateRappelTel({$RECORD->getId()},{$DETAIL_APPRENANT['rappel']['type']})" type="checkbox" name="rappelTel" id="rappelTel" {if $DETAIL_APPRENANT['rappelTel'] eq 1 } checked="" {/if} value="">  
                                    </span>
                                </div>
                            </td>
                        </tr>
                        {/if}
                        {* -- *}
                    </tbody>
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
