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
<form id="detailView" method="POST">
    {include file='DetailViewBlockView.tpl'|@vtemplate_path:$MODULE_NAME RECORD_STRUCTURE=$RECORD_STRUCTURE MODULE_NAME=$MODULE_NAME}
    {include file='LineItemsDetailDate.tpl'|@vtemplate_path:'Inventory'}
    {include file='LineItemsDetailApprenant.tpl'|@vtemplate_path:'Inventory'}
    {if $MODULE_NAME eq 'SalesOrder' or $MODULE_NAME eq 'Invoice'}
        {include file='LineItemsDetailFinanceur.tpl'|@vtemplate_path:'Inventory'}  
    {/if}
    {include file='LineItemsDetail.tpl'|@vtemplate_path:'Inventory'}
</form>
