{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************}
{strip}
	<div class="main-container clearfix">
		<div id="modnavigator" class="module-nav editViewModNavigator">
			<div class="hidden-xs hidden-sm mod-switcher-container">
				{include file="partials/Menubar.tpl"|vtemplate_path:$MODULE}
			</div>
		</div>
		<div class="editViewPageDiv viewContent">
			<div class="col-sm-12 col-xs-12 content-area {if $LEFTPANELHIDE eq '1'} full-width {/if}">
    <form id="EditView" method="POST" enctype="multipart/form-data">
        <input type=hidden name="record" id="record" value="{$RECORD_ID}" />
        <input type="hidden" name="module" value="{$MODULE}" />
        <input type="hidden" name="action" value="SaveStepOne" />
        <input type="hidden" name="source_module" value="{$SOURCE_MODULE}"/>
        <input type="hidden" id="stdfilterlist" name="stdfilterlist" value=""/>
        <input type="hidden" id="advfilterlist" name="advfilterlist" value=""/>
        <input type="hidden" id="status" name="status" value="{$CV_PRIVATE_VALUE}"/>
        <input type="hidden" name="totalArticlesCount" id="totalArticlesCount" value="{$row_no}" />
        <div class="row-fluid">
            <div class="span2">&nbsp;</div>
            <div class="span8 well"> 
                <div class="textAlignCenter alert alert-info" style="height: 50px;"> <h3 style="margin-top: 5px;">Assistant Arrivage </h3></div>
                <hr/>
                <div class="row-fluid">
                    <h4>Nom d'arrivage</h4><input type="text" name="nom" maxlength="35" class="smallInputBox inputElement validate[required]" value="Arrivage" style="width: 200px">
                </div>                                    
                <div class="span2">&nbsp;</div>
            </div>
            <div name='editContent' id="lineItemTabglobalArticle">
    <div class="lineitemTableContainer" >
        <div class="fieldBlockContainer">  
	<table class="table table-bordered lineItemTableArticle" id="lineItemTabArticle">
            <tr>
                <th colspan="11">
                    <span class="eventLineItemHeader">Liste des articles</span>            
                </th>        
            </tr>
            <tr>
                    <td><strong>{vtranslate('LBL_TOOLS',$MODULE)}</strong></td>
                    <td><strong>{vtranslate('Code à barre',$MODULE)}</strong></td>                                       
                    <td><strong>{vtranslate('Quantité',$MODULE)}</strong></td>
                    <td><strong>{vtranslate('Nom produit',$MODULE)}</strong></td>
                    <td></td>                   
            </tr>
            <tr id="rowArticle0" class="hide lineItemCloneCopyArticle" data-row-num="0">
                    {include file="partials/LineItemsContentArticles.tpl"|@vtemplate_path:'Arrivages' row_no=0 data=[] IGNORE_UI_REGISTRATION=true}
            </tr>
            {foreach key=row_no item=data from=$RELATED_SESSION_APPRENANTS}
                    <tr id="rowArticle{$row_no}" data-row-num="{$row_no}" class="lineItemRowArticle">
                            {include file="partials/LineItemsContentArticles.tpl"|@vtemplate_path:'Arrivages' row_no=$row_no data=$data}
                    </tr>
            {/foreach}
            {if count($RELATED_SESSION_APPRENANTS) eq 0}
                    <tr id="rowArticle1" class="lineItemRowArticle" data-row-num="1">
                            {include file="partials/LineItemsContentArticles.tpl"|@vtemplate_path:'Arrivages' row_no=1 data=[] IGNORE_UI_REGISTRATION=false}
                    </tr>
            {/if}
        </table>	
        </div>	        	
    </div>
        <br>
    <div class="btn-toolbar">
        <span class="btn-group">
                <button type="button" class="btn btn-default" id="addArticle" data-module-name="Articles" >
                        <i class="fa fa-plus"></i>&nbsp;&nbsp;<strong>{vtranslate('LBL_ADD_ARTICLE',$MODULE)}</strong>
                </button>
        </span>
    </div>
</div>
        </div>
        
                <div class="row-fluid" align="right">
            <div class="span2">&nbsp;</div>
            <button type="submit" class="btn btn-primary" id="Btn_Valid" style="display: none">Valider</button>   
            <button type="submit" class="btn btn-primary" id="Btn_Process">{vtranslate('LBL_PROCED_TO_RECEPTION', $MODULE )}</button>   
        </div>
    </form>
</div>
</div>
</div>
{/strip}