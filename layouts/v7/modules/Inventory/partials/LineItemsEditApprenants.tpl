{strip}
{assign var="FINAL" value=$RELATED_APPRENANTS.1.final_details}

<input type="hidden" name="totalApprenantsCount" id="totalApprenantsCount" value="{$row_no}" />

<div name='editContent' id="lineItemTabglobalApprenant">
    <div class="lineitemTableContainer" >
        <div class="fieldBlockContainer">  
	<table class="table table-bordered lineItemTableApprenant" id="lineItemTabApprenant">
            <tr>
                <th colspan="8">
                    <span class="eventLineItemHeader">Liste des apprenants</span>            
                </th>        
            </tr>
            <tr>
                    <td><strong>{vtranslate('LBL_TOOLS',$MODULE)}</strong></td>
                    <td><strong>{vtranslate('Apprenant',$MODULE)}</strong></td>                   
                    <td><strong>{vtranslate('Numéro client',$MODULE)}</strong></td>
                    <td><strong>{vtranslate('Nom client',$MODULE)}</strong></td>                   
                    <td><strong>{vtranslate('Téléphone',$MODULE)}</strong></td>   
                    <td><strong>{vtranslate('Email',$MODULE)}</strong></td> 
                    <td><strong>{vtranslate('Etat',$MODULE)}</strong></td> 
                    <td><strong>{vtranslate('Resultat',$MODULE)}</strong></td> 
                    <td><strong>{vtranslate('',$MODULE)}</strong></td>  
            </tr>
            <tr id="rowApprenant0" class="hide lineItemCloneCopyApprenant" data-row-num="0">
                    {include file="partials/LineItemsContentApprenants.tpl"|@vtemplate_path:'Inventory' row_no=0 data=[] IGNORE_UI_REGISTRATION=true}
            </tr>
            {foreach key=row_no item=data from=$RELATED_APPRENANTS}
                    <tr id="rowApprenant{$row_no}" data-row-num="{$row_no}" class="lineItemRowApprenant">
                            {include file="partials/LineItemsContentApprenants.tpl"|@vtemplate_path:'Inventory' row_no=$row_no data=$data}
                    </tr>
            {/foreach}
            {if count($RELATED_APPRENANTS) eq 0}
                    <tr id="rowApprenant1" class="lineItemRowApprenant" data-row-num="1">
                            {include file="partials/LineItemsContentApprenants.tpl"|@vtemplate_path:'Inventory' row_no=1 data=[] IGNORE_UI_REGISTRATION=false}
                    </tr>
            {/if}
        </table>

	<br>
        </div>	        	
    </div>
</div>
<div class="row-fluid verticalBottomSpacing" id="div_date">
        <div class="btn-toolbar">
                <span class="btn-group">
                    <button type="button" class="btn btn-default addButton addApprenant" id="addApprenant{$rowlist_no}">
                        <i class="icon-plus"></i><strong> {vtranslate('LBL_ADD_APPRENANT',$MODULE)}</strong>
                        </button>
                </span>
        </div>
</div>
<br>                                
{/strip}	