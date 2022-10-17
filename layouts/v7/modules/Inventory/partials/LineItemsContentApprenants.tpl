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
{assign var="inscrit" value="inscrit"|cat:$row_no}
{assign var="apprenantid" value="apprenantid"|cat:$row_no}

{assign var="contactDeleted" value="contactDeleted"|cat:$row_no}

<td style="text-align:center;">
        <i class="fa fa-trash deleteRowApprenant cursorPointer" title="{vtranslate('LBL_DELETE',$MODULE)}"></i>
        &nbsp;<a><img src="{vimage_path('drag.png')}" border="0" title="{vtranslate('LBL_DRAG',$MODULE)}"/></a>
        <input type="hidden" class="rowNumber" value="{$row_no}" />
        <input id="{$apprenantid}" name="{$apprenantid}" value="{$data.$apprenantid}" class="apprenantid" type="hidden">  
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

<td>
<div>
        <span class="span10">
            <div>                
                <div class="input-append row-fluid"><div class="span12 row-fluid"> 
                        <input id="{$resultat}" type="text" value="{$data.$resultat}" class="resultat smallInputBox inputElement" name="{$resultat}" style="width: 150px">  
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
	
{/strip}