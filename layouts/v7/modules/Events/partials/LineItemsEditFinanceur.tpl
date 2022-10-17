{strip}
<input type="hidden" name="totalFinanceurCount" id="totalFinanceurCount" value="{$row_no}" />
<div name='editContent' id="lineItemTabglobalFinanceur" class="hide">   
    <div class="lineitemTableContainer" >
        <div class="fieldBlockContainer">  
	{assign var=LINE_ITEM_BLOCK_LABEL value="LBL_ITEM_DETAILS"}
	{assign var=BLOCK_FIELDS value=$RECORD_STRUCTURE.$LINE_ITEM_BLOCK_LABEL}
	{assign var=BLOCK_LABEL value=$LINE_ITEM_BLOCK_LABEL}
	
	<table class="table table-bordered lineItemTableFinanceur" id="lineItemTabFinanceur">
            <tr>
                <th colspan="10">
                    <span class="eventLineItemHeader">Liste des financeurs</span>            
                </th>        
            </tr>
            <tr>
                    <td><strong>{vtranslate('LBL_TOOLS',$MODULE)}</strong></td>
                    <td><strong>{vtranslate('Financeur',$MODULE)}</strong></td>
                    <td><strong>{vtranslate('%',$MODULE)}</strong></td>                   
                    <td><strong>{vtranslate('Montant',$MODULE)}</strong></td>
                    <td><strong>{vtranslate('TVA',$MODULE)}</strong></td>                   
                    <td><strong>{vtranslate('TTC',$MODULE)}</strong></td>   
                    <td><strong>{vtranslate('Adresse',$MODULE)}</strong></td> 
                    <td><strong>{vtranslate('Code postal',$MODULE)}</strong></td> 
                    <td><strong>{vtranslate('Ville',$MODULE)}</strong></td> 
                    <td><strong>{vtranslate('Téléphone',$MODULE)}</strong></td>  
            </tr>
            <tr id="rowfinanceur0" class="hide lineItemCloneCopyFinanceur" data-row-num="0">
                    {include file="partials/LineItemsContentFinanceur.tpl"|@vtemplate_path:'Inventory' row_no=0 data=[] IGNORE_UI_REGISTRATION=true}
            </tr>            
            {foreach key=row_no item=data from=$RELATED_SESSION_FINANCEURS}                
                    <tr id="rowfinanceur{$row_no}" data-row-num="{$row_no}" class="lineItemRowFinanceur">
                            {include file="partials/LineItemsContentFinanceur.tpl"|@vtemplate_path:'Inventory' row_no=$row_no data=$data}
                    </tr>
            {/foreach}
            {if count($RELATED_SESSION_FINANCEURS) eq 0}
                    <tr id="rowfinanceur1" class="lineItemRowFinanceur" data-row-num="1">
                            {include file="partials/LineItemsContentFinanceur.tpl"|@vtemplate_path:'Inventory' row_no=1 data=[] IGNORE_UI_REGISTRATION=false}
                    </tr>
            {/if}
        </table>

	<br>
        </div>	        	
    </div>
</div>
<div class="row-fluid verticalBottomSpacing hide" id="div_date">
        <div class="btn-toolbar">
                <span class="btn-group">
                        <button type="button" class="btn btn-default addFinanceur" id="addFinanceur">
                                <i class="fa fa-plus"></i>&nbsp;&nbsp;<strong>{vtranslate('LBL_ADD_FINANCEUR',$MODULE)}</strong>
                        </button>
                </span>
        </div>
</div>
<br>                                
{/strip}			

