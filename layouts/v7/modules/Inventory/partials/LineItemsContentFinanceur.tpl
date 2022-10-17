{strip}   
{assign var="pourcentage" value="pourcentage"|cat:$row_no}
{assign var="montant" value="montant"|cat:$row_no}
{assign var="tva" value="tva"|cat:$row_no}
{assign var="ttc" value="ttc"|cat:$row_no}
{assign var="identite" value="identite"|cat:$row_no}
{assign var="adresse" value="adresse"|cat:$row_no}
{assign var="code_postal" value="code_postal"|cat:$row_no}
{assign var="ville" value="ville"|cat:$row_no}
{assign var="telephone" value="telephone"|cat:$row_no}    
{assign var="deletedfinanceur" value="deletedfinanceur"|cat:$row_no}
{assign var="vendorname" value="vendorname"|cat:$row_no}
{assign var="vendorid" value="vendorid"|cat:$row_no}   

<td style="text-align:center;">
    <i class="fa fa-trash deleteRowFinanceur cursorPointer" title="{vtranslate('LBL_DELETE',$MODULE)}"></i>
    &nbsp;<a><img src="{vimage_path('drag.png')}" border="0" title="{vtranslate('LBL_DRAG',$MODULE)}"/></a>
    <input type="hidden" class="rowNumberFinanceurs" value="{$row_no}" />
    <input id="{$vendorid}" name="{$vendorid}" value="{$data.$vendorid}" class="vendorid" type="hidden"> 
</td>	
<td>   
<div class="itemNameDiv form-inline">
    <div class="row">
        <div class="col-lg-10">
            <div class="input-group" style="width:100%">
                <input type="text" id="{$vendorname}" name="{$vendorname}" value="{$data.$vendorname}" class="vendorname form-control {if $row_no neq 0} autoCompleteFinanceur {/if} " placeholder="{vtranslate('LBL_TYPE_SEARCH',$MODULE)}"
                {if !empty($data.$vendorname)} disabled="disabled" {/if}>
                {if !$data.$financeurDeleted}
                    <span class="input-group-addon cursorPointer clearLineItemFinanceur" title="{vtranslate('LBL_CLEAR',$MODULE)}">
                        <i class="fa fa-times-circle"></i>
                    </span>
                {/if}                    
                <input type="hidden" id="lineItemTypeFinanceur{$row_no}" name="lineItemTypeFinanceur{$row_no}" value="{$entityType}" class="lineItemTypeFinanceur"/>
                <div class="col-lg-2">
                    <span class="lineItemPopupFinanceur cursorPointer" data-popup="VendorsPopup" title="{vtranslate('Vendors',$MODULE)}" data-module-name="Vendors" data-field-name="vendorid">{Vtiger_Module_Model::getModuleIconPath('Vendors')}</span>
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
                    <input id="{$pourcentage}" type="text" value="{if $data.$pourcentage neq ''}{$data.$pourcentage}{else}0{/if}" class="pourcentage smallInputBox inputElement" name="{$pourcentage}" style="width: 100px">  
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
                    <input id="{$montant}" type="text" value="{if $data.$montant neq ''}{$data.$montant}{else}0{/if}" class="montant smallInputBox inputElement" name="{$montant}" style="width: 100px">  
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
                    <input id="{$tva}" type="text" value="{if $data.$tva neq ''}{$data.$tva}{else}0{/if}" class="tva smallInputBox inputElement" name="{$tva}" style="width: 100px">  
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
                    <input id="{$ttc}" type="text" value="{if $data.$ttc neq ''}{$data.$ttc}{else}0{/if}" class="ttc smallInputBox inputElement" name="{$ttc}" style="width: 100px">  
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
                    <input id="{$adresse}" type="text" value="{$data.$adresse}" class="adresse smallInputBox inputElement" name="{$adresse}" style="width: 150px">  
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
                    <input id="{$code_postal}" type="text" value="{$data.$code_postal}" class="code_postal smallInputBox inputElement" name="{$code_postal}" style="width: 100px">  
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
                    <input id="{$ville}" type="text" value="{$data.$ville}" class="ville smallInputBox inputElement" name="{$ville}" style="width: 150px">  
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
{/strip}