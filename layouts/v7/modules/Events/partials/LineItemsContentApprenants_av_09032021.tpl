{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
*************************************************************************************}

{strip}
    
    
{assign var="contactName" value="contactName"|cat:$row_no}    
{assign var="numclient" value="numclient"|cat:$row_no}
{assign var="nomclient" value="nomclient"|cat:$row_no}
{assign var="telephone" value="telephone"|cat:$row_no}
{assign var="email" value="email"|cat:$row_no}
{assign var="etat" value="etat"|cat:$row_no}
{assign var="resultat" value="resultat"|cat:$row_no}
{assign var="ticket_examen" value="ticket_examen"|cat:$row_no}
{assign var="ticket_examen_test" value="ticket_examen_test"|cat:$row_no}
{assign var="type_tokens" value="type_tokens"|cat:$row_no}
{assign var="type_tokens_test" value="type_tokens_test"|cat:$row_no}
{assign var="emailenligne" value="emailenligne"|cat:$row_no}

<!-- unicnfsecrm_022020_13 -->
{assign var="statutfacture" value="statutfacture"|cat:$row_no}
{assign var="inscrit" value="inscrit"|cat:$row_no}
{assign var="apprenantid" value="apprenantid"|cat:$row_no}
{assign var="be_essai" value="be_essai"|cat:$row_no}
{assign var="be_mesurage" value="be_mesurage"|cat:$row_no}
{assign var="be_verification" value="be_verification"|cat:$row_no}
{assign var="be_manoeuvre" value="be_manoeuvre"|cat:$row_no}
{assign var="he_essai" value="he_essai"|cat:$row_no}
{assign var="he_mesurage" value="he_mesurage"|cat:$row_no}
{assign var="he_verification" value="he_verification"|cat:$row_no}
{assign var="he_manoeuvre" value="he_manoeuvre"|cat:$row_no}
{assign var="initiale" value="initiale"|cat:$row_no}
{assign var="recyclage" value="recyclage"|cat:$row_no}
{assign var="testprerequis_oui" value="testprerequis_oui"|cat:$row_no}
{assign var="electricien_oui" value="electricien_oui"|cat:$row_no}
{assign var="testprerequis_non" value="testprerequis_non"|cat:$row_no}
{assign var="electricien_non" value="electricien_non"|cat:$row_no}
{assign var="testprerequis" value="testprerequis"|cat:$row_no}
{assign var="electricien" value="electricien"|cat:$row_no}

{assign var="contactDeleted" value="contactDeleted"|cat:$row_no}

<td style="text-align:center;">
        <i class="fa fa-trash deleteRowApprenant cursorPointer" title="{vtranslate('LBL_DELETE',$MODULE)}"></i>
        &nbsp;<a><img src="{vimage_path('drag.png')}" border="0" title="{vtranslate('LBL_DRAG',$MODULE)}"/></a>
        <input type="hidden" class="rowNumber" value="{$row_no}" />
        <input id="{$apprenantid}" name="{$apprenantid}" value="{$data.$apprenantid}" class="apprenantid" type="hidden">  
        <input id="{$ticket_examen}" name="{$ticket_examen}" value="{$data.$ticket_examen}" class="ticket_examen" type="hidden">  
        <input id="{$ticket_examen_test}" name="{$ticket_examen_test}" value="{$data.$ticket_examen_test}" class="ticket_examen_test" type="hidden">  
        <input id="{$type_tokens}" name="{$type_tokens}" value="{$data.$type_tokens}" class="type_tokens" type="hidden">  
        <input id="{$type_tokens_test}" name="{$type_tokens_test}" value="{$data.$type_tokens_test}" class="type_tokens_test" type="hidden">  
        <input id="{$emailenligne}" name="{$emailenligne}" value="{$data.$emailenligne}" class="emailenligne" type="hidden">  
        
</td>	
<td>    
    <div class="itemNameDiv form-inline">
        <div class="row">
            <div class="col-lg-10">
                <div class="input-group" style="width:100%">
                    <input type="text" id="{$contactName}" name="{$contactName}" value="{$data.$contactName}" class="contactName form-control {if $row_no neq 0} autoCompleteApprenant {/if} " placeholder="{vtranslate('LBL_TYPE_SEARCH',$MODULE)}"
                        {if !empty($data.$contactName)} disabled="disabled" {/if}>
                    {if !$data.$contactDeleted}
                        <span class="input-group-addon cursorPointer clearLineItemApprenant" title="{vtranslate('LBL_CLEAR',$MODULE)}">
                            <i class="fa fa-times-circle"></i>
                        </span>
                    {/if}
                    <input type="hidden" id="lineItemTypeApprenant{$row_no}" name="lineItemTypeApprenant{$row_no}" value="{$entityType}" class="lineItemTypeApprenant"/>
                    <div class="col-lg-2">
                        <span class="lineItemPopupApprenant cursorPointer" data-popup="ContactsPopup" title="{vtranslate('Contacts',$MODULE)}" data-module-name="Contacts" data-field-name="contactid">{Vtiger_Module_Model::getModuleIconPath('Contacts')}</span>
                    </div> 
                </div>
            </div>
        </div> 
    </div>
</td>
<td>
<div>
        <span class="span10">
            <div>                
                <div class="input-append row-fluid"><div class="span12 row-fluid"> 
                        <input id="{$numclient}" type="text" value="{$data.$numclient}" class="numclient smallInputBox inputElement" name="{$numclient}" style="width: 150px">  
                    </div>
                </div>
            </div>
        </span>
    </div>
</td>
<td>
<div>
        <span class="span10">
            <div>                
                <div class="input-append row-fluid"><div class="span12 row-fluid"> 
                        <input id="{$nomclient}" type="text" value="{$data.$nomclient}" class="nomclient smallInputBox inputElement" name="{$nomclient}" style="width: 150px">  
                    </div>
                </div>
            </div>
        </span>
    </div>
</td>
<td>
<div>
        <span class="span10">
            <div>                
                <div class="input-append row-fluid"><div class="span12 row-fluid"> 
                        <input id="{$telephone}" type="text" value="{$data.$telephone}" class="telephone smallInputBox inputElement" name="{$telephone}" style="width: 150px">  
                    </div>
                </div>
            </div>
        </span>
    </div>
</td>
<td>
<div>
        <span class="span10">
            <div>                
                <div class="input-append row-fluid"><div class="span12 row-fluid"> 
                        <input id="{$email}" type="text" value="{$data.$email}" class="email smallInputBox inputElement" name="{$email}" style="width: 150px">  
                    </div>
                </div>
            </div>
        </span>
    </div>
</td>

<td>
    <span id="optionAvisAttestation" class="cursorPointer" data-apprenantid="{$data.$apprenantid}"><i class="fa fa-cogs"></i></span>
    <!-- Popup Coefficient Div -->
    <div id="optionAvisAttestationUI" class="optionAvisAttestationUI hide">        
                <input type="checkbox" id="{$be_essai}" name="{$be_essai}" {if $data.$be_essai eq '1'}checked{/if}> <strong>BE essai</strong> &nbsp;&nbsp;
                <input type="checkbox" id="{$be_mesurage}" name="{$be_mesurage}" {if $data.$be_mesurage eq '1'}checked{/if}> <strong>BE mesurage</strong> &nbsp;&nbsp;
                <input type="checkbox" id="{$be_verification}" name="{$be_verification}" {if $data.$be_verification eq '1'}checked{/if}> <strong>BE vérification</strong> &nbsp;&nbsp;  
                <input type="checkbox" id="{$be_manoeuvre}" name="{$be_manoeuvre}" {if $data.$be_manoeuvre eq '1'}checked{/if}> <strong>BE manoeuvre</strong><br><br>
                <input type="checkbox" id="{$he_essai}" name="{$he_essai}" {if $data.$he_essai eq '1'}checked{/if}> <strong>HE essai</strong> &nbsp;&nbsp; 
                <input type="checkbox" id="{$he_mesurage}" name="{$he_mesurage}" {if $data.$he_mesurage eq '1'}checked{/if}> <strong>HE mesurage</strong> &nbsp;&nbsp;
                <input type="checkbox" id="{$he_verification}" name="{$he_verification}" {if $data.$he_verification eq '1'}checked{/if}> <strong>HE vérification</strong> &nbsp;&nbsp; 
                <input type="checkbox" id="{$he_manoeuvre}" name="{$he_manoeuvre}" {if $data.$he_manoeuvre eq '1'}checked{/if}> <strong>HE manoeuvre</strong> &nbsp;&nbsp;
                <select id="{$resultat}" name="{$resultat}" class="resultat">
                    <option value="avis_favorable" {if $data.$resultat eq 'avis_favorable'}selected{/if}>Avis favorable</option>
                    <option value="avis_defavorable" {if $data.$resultat eq 'avis_defavorable'}selected{/if}>Avis défavorable</option>
                    <option value="autre" {if $data.$resultat eq 'autre'}selected{/if}>Autre</option>
                </select>
                
                <br><br>
                <h2>Avis</h2>
                <HR>
                <input type="checkbox" id="{$initiale}" name="{$initiale}" {if $data.$initiale eq '1'}checked{/if}> <strong>Initiale</strong> &nbsp;&nbsp;
                <input type="checkbox" id="{$recyclage}" name="{$recyclage}" {if $data.$recyclage eq '1'}checked{/if}> <strong>Recyclage</strong> <br><br>                
                <strong>Test prérequis réussi</strong> <input type="radio" id="{$testprerequis_oui}" name="{$testprerequis}" value="oui" {if $data.$testprerequis eq '1'}checked{/if}> <strong>Oui</strong> <input type="radio" id="{$testprerequis_non}" name="{$testprerequis}" value="non" {if $data.$testprerequis eq '0'}checked{/if}> <strong>Non</strong><br><br>
                <strong>Électricien</strong> <input type="radio" id="{$electricien_oui}" name="{$electricien}" value="oui" {if $data.$electricien eq '1'}checked{/if}> Oui</strong> <input type="radio" id="{$electricien_non}" name="{$electricien}" value="non" {if $data.$electricien eq '0'}checked{/if}> <strong>Non</strong><br><br>
    </div>
    <!-- End Popup Div --> 
</td>

<td>
<div>
        <span class="span10">
            <div>                
                <div class="input-append row-fluid"><div class="span12 row-fluid"> 
                    <select name="{$etat}" id="{$etat}" class="etat smallInputBox inputElement" style="width: 150px">
                        <option value="confirmer" {if $data.$etat eq 'confirmer'} selected {/if}>Confirmer</option>
                        <option value="en_attente" {if $data.$etat eq 'en_attente'} selected {/if}>En attente</option>
                        <option value="annuler" {if $data.$etat eq 'annuler'} selected {/if}>Annuler</option>
                        <option value="annulation_tardive" {if $data.$etat eq 'annulation_tardive'} selected {/if}>Annulation tardive</option>
                    </select>
                    </div>
                </div>
            </div>
        </span>
    </div>
</td>

<!-- unicnfsecrm_022020_13  -->
<td>
    <div>
        <span class="span10">
            <div>                
                <div class="input-append row-fluid">
                    <div class="span12 row-fluid"> 
                        <input id="{$statutfacture}" type="text" value="{$data.$statutfacture}" class="smallInputBox inputElement" name="{$statutfacture}" disabled="disabled" style="width: 100px">                 
                    </div>
                </div>
            </div>
        </span>
    </div>
</td>

<td>
    <div>
        <span class="span10">
            <div>                
                <div class="input-append row-fluid"><div class="span12 row-fluid"> 
                    <input type="checkbox" name="{$inscrit}" id="{$inscrit}" {if $data.$inscrit eq 1} checked {/if} class="inscrit smallInputBox inputElement" style="width: 15px" value="inscrit">                    
                    </div>
                </div>
            </div>
        </span>
    </div>
</td>

<td>
    <div>
        <span class="span10">
            <div>                
                <div class="input-append row-fluid"><div class="span12 row-fluid"> 
                        <a target="blank" href="index.php?module=Contacts&view=Edit&record={$data.$apprenantid}">{Vtiger_Module_Model::getModuleIconPath('Contacts')}</a>
                    </div>
                </div>
            </div>
        </span>
    </div>
</td>
	
{/strip}