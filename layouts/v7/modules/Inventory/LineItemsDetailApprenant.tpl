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
                <th colspan="10">Liste des Apprenants</th> 
            </tr> 
            <tr>
                <th class="lineItemBlockHeader">Nom apprenant</th>
                <th class="lineItemBlockHeader">Numéro client</th>
                <th class="lineItemBlockHeader">Nom client</th>
                <th class="lineItemBlockHeader">Téléphone</th>
                <th class="lineItemBlockHeader">Email</th>
                <th class="lineItemBlockHeader">Resultat</th>
                <th class="lineItemBlockHeader">Inscrit</th>
            </tr>
            
            </thead>
            <tbody>
                {foreach key=INDEX item=LINE_APPRENANT_DETAIL from=$RELATED_APPRENANTS}
                    <tr>
                        <td>{$LINE_APPRENANT_DETAIL["contactName$INDEX"]}</td>
                        <td>{$LINE_APPRENANT_DETAIL["numclient$INDEX"]}</td>
                        <td>{$LINE_APPRENANT_DETAIL["nomclient$INDEX"]}</td>
                        <td>{$LINE_APPRENANT_DETAIL["telephone$INDEX"]}</td>
                        <td>{$LINE_APPRENANT_DETAIL["email$INDEX"]}</td>
                        <td>{$LINE_APPRENANT_DETAIL["resultat$INDEX"]}</td>
                        <td>{if $LINE_APPRENANT_DETAIL["inscrit$INDEX"] eq '0'} Non inscrit {else} Inscrit {/if} </td>                        
                    </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
    
</div>