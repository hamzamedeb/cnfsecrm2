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
{* uni_cnfsecrm - v2 - modif 94 - FILE *}
<div class = "quickPreview">
    <div class='quick-preview-modal modal-content'>
        <div class='modal-body'>
            <div class = "quickPreviewModuleHeader row">
                <div class="col-lg-2 pull-right">
                    <button class="close" aria-hidden="true" data-dismiss="modal" type="button" title="Fermer">x</button>
                </div>
            </div>
            <div class="quickPreviewActions clearfix">
                <div class = "quickPreviewSummary">
                    <br/><br/>
                    <div class="row">
                        <div class="col-lg-4">
                            {foreach key=row_no item=data from=$HISTORIQUE_APP['action']}
                                {if {$data} == 1} 
                                    {$test1 = 1}
                                {/if}    
                            {/foreach}    
                            <button {if {$test1} == 1} disabled="disabled" {/if} id="reporterFormation" type="button" class="btn btn-info" data-toggle="collapse" data-target="#demo">Reporter la formation</button>
                            <br/>
                            <div id="demo" class="collapse" style="margin-top: 20px;">
                                <div class="input-group" style="width:100%">
                                    <input type="text" id="calendarName" value="" class="form-control ">
                                    <input type="hidden" id="calendarId" value="" class="form-control">
                                    <input type="hidden" id="apprenantId" value="" class="form-control">
                                    <span class="input-group-addon cursorPointer clearLineItemApprenant" title="Effacer"><i class="fa fa-times-circle"></i></span>
                                    <div class="col-lg-5">
                                        <span class="lineItemPopupCalendar cursorPointer" data-popup="CalendarPopup" title="{vtranslate('Calendar','SalesOrder')}" data-module-name="Calendar" data-field-name="activityid">{Vtiger_Module_Model::getModuleIconPath('Calendar')}</span>
                                    </div>
                                </div>
                                <br/><br/>
                                <div>
                                    <button onclick="window.location.href = 'javascript:Inventory_Detail_Js.setNewSession()' " class="btn btn-success" style="margin-top: 10px;">Valider la session</button>
                                </div>
                            </div>
                        </div>
                        {* on ne sait pas *}
                        <div class="col-lg-4">
                            {foreach key=row_no item=data from=$HISTORIQUE_APP['action']}
                                {if {$data} == 1 || {$data} == 3 } 
                                    {$test2 = 1}
                                {/if}    
                            {/foreach} 
                            <button {if {$test2} != 0} disabled="disabled" {/if} id="saitPas"  onclick="window.location.href = 'javascript:Inventory_Detail_Js.neSaiPas()' " type="button" class="btn btn-warning" style="margin-left: 20px;">On ne sait pas</button>
                        </div>
                        {*annuler la facture*}

                        <div class="col-lg-4">
                            {foreach key=row_no item=data from=$HISTORIQUE_APP['action']}
                                {if {$data} == 2} 
                                    {$test3 = 1}
                                {/if}    
                            {/foreach} 
                            <button {if {$test3} == 1} disabled="disabled" {/if} id="annulerFacture" onclick="window.location.href = 'javascript:Inventory_Detail_Js.annulerFacture()'" type="button" class="btn btn-danger">Annuler la facture</button>
                        </div>

                    </div>
                    <br/><br/>
                </div>
            </div>
            
            <div class="engagementsContainer">
                <div id="quickPreviewHeader">
                    <div class="title">Mises à jour</div>
                </div>
                
                <div class="recentActivitiesContainer" id="updates">
                    <div class="history">
                            
                        <ul id="historiqueApp" style="list-style:none;">
                            {if {$LAST_ACTION} == 0}
                                <li id="dataVide">Aucune mise à jour</li>
                            {else}
                            {foreach key=row_no item=data from=$HISTORIQUE_APP['historique']}
                                <li>{$data}</li>
                            {/foreach}    
                            {/if}
                        </ul>
                        
                    </div>
                </div>    
            </div>                    
        </div>
    </div>
</div>

