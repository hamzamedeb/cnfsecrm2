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
                {* uni_cnfsecrm - v2 - modif 94 - DEBUT *}
                <th colspan="11">Liste des Apprenants</th> 
                {* uni_cnfsecrm - v2 - modif 94 - FIN *}
            </tr> 
            <tr>
                <th class="lineItemBlockHeader">Nom apprenant</th>
                <th class="lineItemBlockHeader">Numéro client</th>
                <th class="lineItemBlockHeader">Nom client</th>
                <th class="lineItemBlockHeader">Téléphone</th>
                <th class="lineItemBlockHeader">Email</th>
                <th class="lineItemBlockHeader">Resultat</th>
                <th class="lineItemBlockHeader">Inscrit</th>
                {* uni_cnfsecrm - v2 - modif 94 - DEBUT *}
                <th class="lineItemBlockHeader"></th>
                {* uni_cnfsecrm - v2 - modif 94 - FIN *}
                {* uni_cnfsecrm - v2 - modif 132 - DEBUT *}
                <th>Envoi Convocation</th>
                {* uni_cnfsecrm - v2 - modif 132 - FIN *}
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
                        {* uni_cnfsecrm - v2 - modif 94 - DEBUT *}
                        {assign var=ETAT_APPRENANT value=SalesOrder_Module_Model::getEtatApprenant($LINE_APPRENANT_DETAIL["apprenantid$INDEX"],{$RECORD_ID})}
                        <td class="{if $ETAT_APPRENANT != 0} hidden {/if}" id="marquerAbsent{$INDEX}" style="width: 5%;"><a href="javascript:Inventory_Detail_Js.marquerAbsent({$SESSION_ID},{$LINE_APPRENANT_DETAIL["apprenantid$INDEX"]},{$INDEX})"><i class="fas fa-bed" aria-hidden="true" title="Marquer comme Absent"></i></a></td>
                        <td class="{if $ETAT_APPRENANT == 0} hidden {/if}" id="openPopup{$INDEX}"><span><a href="javascript:Inventory_Detail_Js.openPopup({$SESSION_ID},{$LINE_APPRENANT_DETAIL["apprenantid$INDEX"]})" class="quickView fa fa-eye icon action" data-app="INVENTORY" title="Vue Rapide"></a></span></td>
                        {* uni_cnfsecrm - v2 - modif 94 - FIN *}
                        {* uni_cnfsecrm - modif 132 - DEBUT *}
                        <td style="width: 5%;"><a href="javascript:Inventory_Detail_Js.sendEmailPDFClickHandler('module=Events&amp;view=SendEmail&amp;mode=composeMailData&amp;record={$SESSION_ID}&appr={$LINE_APPRENANT_DETAIL["apprenantid$INDEX"]}&email={$LINE_APPRENANT_DETAIL["email$INDEX"]}&doc=sendconvocation')"><i class="fa fa-envelope-square envoiattes cursorPointer" aria-hidden="true" title="Envoi convocation"></i></a></td>
                        {* uni_cnfsecrm - modif 132 - FIN *}
                    </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
    
</div>