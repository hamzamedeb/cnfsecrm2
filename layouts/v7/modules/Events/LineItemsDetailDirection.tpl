{*+**********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************}
{* uni_cnfsecrm - v2 - modif 176 - FILE *}

<div class="details block">
    <div class="lineItemTableDiv lineItemTableDivDirection">       
        <table class="table table-bordered lineItemsTable" style = "margin-top:15px">
            <thead>
            <tr>
                <th colspan="">Liste des Directions</th>                 
            </tr> 
            <tr>
                <th class="lineItemBlockHeader">Diection</th>
                <th class="lineItemBlockHeader">Envoi Avis & Attestation</th>
                <th class="lineItemBlockHeader">Envoi Convocation</th>
            </tr>
            </thead>
            <tbody>
                {foreach key=INDEX item=DIRECTION_DETAIL from=$RELATED_SESSION_DIRECTION}
                    <tr>
                        <td> {$DIRECTION_DETAIL} </td>
                        <td> <a href="javascript:Events_Detail_Js.sendEmailPDFClickHandler('module=Events&view=SendEmailAttestationSpecifique&mode=composeMailData&record={$RECORD->getId()}&doc=avisetattestation&direction={$DIRECTION_DETAIL}')"><i class="fa fa-envelope-square envoiattes cursorPointer" aria-hidden="true" title="Envoi Avis"></i></a> </td>
                        <td> <a href="javascript:Events_Detail_Js.sendEmailPDFClickHandler('module=Events&view=SendEmailSpecifique&mode=composeMailData&record={$RECORD->getId()}&doc=sendconvocation&direction={$DIRECTION_DETAIL}')"><i class="fa fa-envelope-square envoiattes cursorPointer" aria-hidden="true" title="Envoi convocation"></i></a> </td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
</div>
            
         