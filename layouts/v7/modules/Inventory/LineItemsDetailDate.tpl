{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************}
{* modules/Inventory/views/Detail.php *}

<div class="details block">
    <div class="lineItemTableDiv">
        <table class="table table-bordered lineItemsTable" style = "margin-top:15px">
            <thead>
            <tr>
                <th colspan="6"> Journées </th> 
            </tr>
            <tr>
            <th class="lineItemBlockHeader">Date</th>
            <th class="lineItemBlockHeader">Debut heure Matin</th>
            <th class="lineItemBlockHeader">Fin heure Matin</th>
            <th class="lineItemBlockHeader">Debut heure aprés midi</th>
            <th class="lineItemBlockHeader">Fin heure aprés midi</th>
            <th class="lineItemBlockHeader">Durée formation</th>
            </tr>
            </thead>
            <tbody>
            {foreach key=INDEX item=LINE_DATES_DETAIL from=$RELATED_DATES}
                <tr>
                    <td>{$LINE_DATES_DETAIL["date_start$INDEX"]}</td>
                    <td>{$LINE_DATES_DETAIL["start_matin$INDEX"]}</td>
                    <td>{$LINE_DATES_DETAIL["end_matin$INDEX"]}</td>
                    <td>{$LINE_DATES_DETAIL["start_apresmidi$INDEX"]}</td>
                    <td>{$LINE_DATES_DETAIL["end_apresmidi$INDEX"]}</td>
                    <td>{$LINE_DATES_DETAIL["duree_formation$INDEX"]}</td>                    
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
    
</div>