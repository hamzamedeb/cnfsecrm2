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
                <th colspan="9">Liste des Financeurs</th> 
            </tr> 
            <tr>
                <th class="lineItemBlockHeader">Nom Financeur</th>
                <th class="lineItemBlockHeader">%</th>
                <th class="lineItemBlockHeader">Mantant</th>
                <th class="lineItemBlockHeader">TVA</th>
                <th class="lineItemBlockHeader">TTC</th>                
                <th class="lineItemBlockHeader">Adresse</th>
                <th class="lineItemBlockHeader">Code postale</th>
                <th class="lineItemBlockHeader">Ville</th>
                <th class="lineItemBlockHeader">Telephone</th>
            </tr>
            
            </thead>
            <tbody>
                {foreach key=INDEX item=LINE_FINANCEURS_DETAIL from=$RELATED_SESSION_FINANCEURS}
                <tr>
                    <td>{$LINE_FINANCEURS_DETAIL["vendorname$INDEX"]}</td>
                    <td>{$LINE_FINANCEURS_DETAIL["montant$INDEX"]}</td>
                    <td>{$LINE_FINANCEURS_DETAIL["tva$INDEX"]}</td>
                    <td>{$LINE_FINANCEURS_DETAIL["pourcentage$INDEX"]}</td>
                    <td>{$LINE_FINANCEURS_DETAIL["ttc$INDEX"]}</td>                    
                    <td>{$LINE_FINANCEURS_DETAIL["street$INDEX"]}</td>
                    <td>{$LINE_FINANCEURS_DETAIL["postalcode$INDEX"]}</td>
                    <td>{$LINE_FINANCEURS_DETAIL["city$INDEX"]}</td>
                    <td>{$LINE_FINANCEURS_DETAIL["phone$INDEX"]}</td>
                </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
    
</div>