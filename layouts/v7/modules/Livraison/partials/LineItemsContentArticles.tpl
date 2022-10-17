{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
*************************************************************************************}

{strip}
    
    
{assign var="productName" value="productName"|cat:$row_no}    
{assign var="codebarre" value="codebarre"|cat:$row_no}
{assign var="nomproduit" value="nomproduit"|cat:$row_no}
{assign var="qty" value="qty"|cat:$row_no}
{assign var="articleid" value="articleid"|cat:$row_no}

{assign var="productDeleted" value="productDeleted"|cat:$row_no}

<td style="text-align:center;">
        <i class="fa fa-trash deleteRowArticle cursorPointer" title="{vtranslate('LBL_DELETE',$MODULE)}"></i>
        &nbsp;<a><img src="{vimage_path('drag.png')}" border="0" title="{vtranslate('LBL_DRAG',$MODULE)}"/></a>
        <input type="hidden" class="rowNumber" value="{$row_no}" />
        <input id="{$articleid}" name="{$articleid}" value="{$data.$articleid}" class="articleid" type="hidden">  
</td>	
<td>
    <div>
        <span class="span10">
            <div>                
                <div class="input-append row-fluid"><div class="span12 row-fluid">                         
                        <input id="{$codebarre}" name="{$codebarre}" type="text" value="{if !empty($data.$codebarre)}{$data.$codebarre}{else}{/if}" class="codebarre {if $row_no neq 0} autoComplete {/if}" style="height: 30px;" placeholder="{vtranslate('LBL_TYPE_SEARCH',$MODULE)}" {if !empty($data.$codebarre)} disabled="disabled" {/if}/>
                        <input id="{$codebarrevalue}" name="{$codebarrevalue}" class="codebarrevalue" type="hidden" value="{$data.$codebarre}">
                        <input id="{$productid}" name="{$productid}" class="productid" type="hidden" value="{$data.$productid}">
                        <span class="lineItemPopup cursorPointer alignMiddle hide" data-popup="ProductsPopup" data-module-name="Products" title="{vtranslate('Products',$MODULE)}" data-field-name="articlesid" src="{vimage_path('Products.png')}"/>
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
                        <input id="{$qty}" type="text" value="{if !empty($data.$qty)}{$data.$qty}{else}1{/if}" class="qty smallInputBox inputElement" name="{$qty}" style="width: 150px">  
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
                        <input id="{$nomproduit}" type="text" value="" class="nomproduit smallInputBox inputElement" name="{$nomproduit}" style="width: 150px">  
                    </div> 
                </div>
            </div>
        </span>
    </div>
</td>
<td>    
    <div class="itemNameDiv form-inline">
        <div class="row">
            <div class="col-lg-10">
                <span class="cursorPointer clearLineItemArticle" title="{vtranslate('LBL_CLEAR',$MODULE)}">
                            <i class="fa fa-times-circle"></i>
                        </span>
            </div>
        </div> 
    </div>
</td>
	
{/strip}