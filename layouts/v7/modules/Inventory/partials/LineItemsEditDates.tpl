{strip}  
<input type="hidden" name="totalDatesCount" id="totalDatesCount" value="{$row_no}" />    
<div name='editContent' id="lineItemTabglobalDate">   
    <div class="lineitemTableContainer" >
        <div class="fieldBlockContainer">            
            <table class="table table-bordered lineItemTabDate" id="lineItemTabDate">
                <tr>
                    <th colspan="8">
                        <span class="eventLineItemHeader">Journées</span>            
                    </th>        
                </tr>
                <tr id="rowdate0" class="hide lineItemCloneCopyDates" data-row-num="0">
                        {include file="partials/LineItemsContentDates.tpl"|@vtemplate_path:'Inventory' row_no=0 data=[]}
                </tr>     
            {if count($RELATED_DATES) eq 0}
                <tr id="rowdate1" class="lineItemRowDate" data-row-num="1">
                        {include file="partials/LineItemsContentDates.tpl"|@vtemplate_path:'Inventory' row_no=1 data=[]}
                </tr>
            {else}
                {assign var="date_id_value" value=""}        
                {assign var="rowlist_no" value=0} 
            {foreach key=row_no item=data from=$RELATED_DATES name=stobjfor}       
                {assign var="date_start" value="date_start"|cat:$row_no}
                {assign var="start_matin" value="start_matin"|cat:$row_no}
                {assign var="end_matin" value="end_matin"|cat:$row_no}
                {assign var="start_apresmidi" value="start_apresmidi"|cat:$row_no}         
                {assign var="end_apresmidi" value="end_apresmidi"|cat:$row_no}         
                <tr id="rowdate{$row_no}" class="lineItemRowDate" data-row-num="{$row_no}">
                    {include file="partials/LineItemsContentDates.tpl"|@vtemplate_path:'Inventory' row_no=$row_no data=$data}
                </tr>       
            {/foreach}      
            {/if}
             </table>       
        </div>
    </div>
</div>
<div class="row-fluid verticalBottomSpacing" id="div_date">
        <div class="btn-toolbar">
                <span class="btn-group">
                        <button type="button" class="btn btn-default addDate" id="addDate">
                            <i class="fa fa-plus"></i>&nbsp;&nbsp;<strong>{vtranslate('LBL_ADD_DATE',$MODULE)}</strong>
                        </button>
                </span>
        </div>
</div> 
<br>
{/strip}