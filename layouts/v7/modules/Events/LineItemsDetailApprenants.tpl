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
                <th colspan="6"><input type="text" id="contactName" name="contactName" class="contactName form-control  autoCompleteApprenant   ui-autocomplete-input" placeholder="Rechercher par apprenant"></th>                 
            </tr> 
            </thead>
        </table>
    </div>
    <div class="lineItemTableDiv lineItemTableDivApprenants">       
        <table class="table table-bordered lineItemsTable" style = "margin-top:15px">
            <thead>
            <tr>
                {if $categorieformation eq 'HABILITATIONS' or $categorieformation eq 'AIPR'}
                    {* uni_cnfsecrm - v2 - modif 124 - DEBUT *}
                    {* uni_cnfsecrm - v2 - modif 142 - DEBUT *}
                    {if $categorieformation eq 'AIPR'}
                        {$colspan = '18'}
                    {else}
                        {$colspan = '17'}
                        {* uni_cnfsecrm - v2 - modif 124 - FIN *}
                    {/if}
                    {* uni_cnfsecrm - v2 - modif 142 - FIN *}
                {else} 
                {$colspan = '12'}
                {/if}
                <th colspan="{$colspan}">Liste des Apprenants</th>                 
            </tr> 
            <tr>
                <th class="lineItemBlockHeader">Nom apprenant</th>
                <th class="lineItemBlockHeader">Numéro client</th>
                <th class="lineItemBlockHeader">Nom client</th>
                <th class="lineItemBlockHeader">Téléphone</th>
                <th class="lineItemBlockHeader">Email</th>
                <th class="lineItemBlockHeader">Resultat</th>
                <th class="lineItemBlockHeader">Ticket Examen</th>
                {*<th class="lineItemBlockHeader">Ticket Examen Test</th>*}
                {* uni_cnfsecrm - v2 - modif 142 - DEBUT *}
                {*<th class="lineItemBlockHeader">Ticket Examen Réaffecter</th>*}
                {* uni_cnfsecrm - v2 - modif 142 - FIN *}
                <!-- unicnfsecrm_022020_13 -->
                <th class="lineItemBlockHeader">Statut facture</th>
                <th class="lineItemBlockHeader">Inscrit</th>  
                <!-- uni_cnfsecrm - modif 81 -->
                <th class="lineItemBlockHeader">Envoi Attestation</th>
                {if $categorieformation eq 'HABILITATIONS' or $categorieformation eq 'AIPR'}
                <!-- uni_cnfsecrm - modif 81 -->
                <th class="lineItemBlockHeader">Envoi Avis & Attestation</th>
                <!-- unicnfsecrm_mod_56 -->
                {if $categorieformation eq 'AIPR'}
                <th class="lineItemBlockHeader">Affecter Token QCM</th>
                {*<th class="lineItemBlockHeader">Affecter Token Test</th>*}
                {* uni_cnfsecrm - v2 - modif 142 - DEBUT *}
                {*<th class="lineItemBlockHeader">Réaffecter Token QCM</th>*}
                {* uni_cnfsecrm - v2 - modif 142 - FIN *} 
                {/if}
                {/if}
                {* uni_cnfsecrm - modif 81 - DEBUT *}
                <th>Envoi Convocation</th>
                {* uni_cnfsecrm - modif 81 - FIN *}
                <th class="lineItemBlockHeader">Envoi informations Apprenants</th>
                {* uni_cnfsecrm - v2 - modif 124 - DEBUT *}
                {if $categorieformation eq 'HABILITATIONS'}
                <th class="lineItemBlockHeader">Exporter tout</th>
                {/if}
                {* uni_cnfsecrm - v2 - modif 124 - FIN *}
                {if $CLIENT_ID eq 189380}
                    <th>Direction</th>
                {/if}
            </tr>
            
            </thead>
            <tbody>
                {foreach key=INDEX item=LINE_APPRENANT_DETAIL from=$RELATED_SESSION_APPRENANTS}
                    {assign var="directionApp" value={$LINE_APPRENANT_DETAIL["direction$INDEX"]}}  
                    <tr>
                        <td><a target="blank" href="index.php?module=Contacts&view=Detail&record={$LINE_APPRENANT_DETAIL["apprenantid$INDEX"]}">{$LINE_APPRENANT_DETAIL["contactName$INDEX"]}</a></td>
                        <td><a target="blank" href="index.php?module=Accounts&view=Detail&record={$LINE_APPRENANT_DETAIL["accountid$INDEX"]}">{$LINE_APPRENANT_DETAIL["numclient$INDEX"]}</a></td>
                        <td><a target="blank" href="index.php?module=Accounts&view=Detail&record={$LINE_APPRENANT_DETAIL["accountid$INDEX"]}">{$LINE_APPRENANT_DETAIL["nomclient$INDEX"]}</a></td>
                        <td>{$LINE_APPRENANT_DETAIL["telephone$INDEX"]}</td>
                        <td>{$LINE_APPRENANT_DETAIL["email$INDEX"]}</td>
                        <td>{if $LINE_APPRENANT_DETAIL["resultat$INDEX"] eq 'avis_favorable'}Avis favorable
                            {elseif $LINE_APPRENANT_DETAIL["resultat$INDEX"] eq 'avis_defavorable'}Avis defavorable{else}Autre{/if}</td>
                        {* uni_cnfsecrm - v2 - modif 176 - DEBUT *}
                        <td class="fieldValue" data-field-type="{$LINE_APPRENANT_DETAIL["apprenantid$INDEX"]}" id="ticket_examen{$LINE_APPRENANT_DETAIL["apprenantid$INDEX"]}">
                            <span class="value" data-field-type="" style="display: inline-block;">{$LINE_APPRENANT_DETAIL["ticket_examen$INDEX"]}</span>
                            <span class="edit pull-left hide ">
                                <input type="hidden" class="fieldBasicData" data-name="" data-type="" data-displayvalue="" data-value="">
                                <div class="input-group editElement">
                                    <input class="inputElement form-control" type="text" name="ticket_examen{$LINE_APPRENANT_DETAIL["apprenantid$INDEX"]}" data-label="" data-rule-phone="true" data-fieldinfo="">
                                    <div class="input-save-wrap">
                                        <span class="pointerCursorOnHover input-group-addon input-group-addon-save"> 
                                            <i class="fa fa-check"></i>
                                        </span>
                                        <span class="pointerCursorOnHover input-group-addon input-group-addon-cancel">
                                            <i class="fa fa-close"></i>
                                        </span>
                                    </div>
                                </div>
                            </span>
                            <span class="action pull-right"><a href="#" onclick="return false;" class="editAction fa fa-pencil"></a></span>
                        </td>
                        {* uni_cnfsecrm - v2 - modif 176 - FIN *}
                        <td>{$LINE_APPRENANT_DETAIL["statutfacture$INDEX"]}</td>
                        <td>{if $LINE_APPRENANT_DETAIL["inscrit$INDEX"] eq '0'} Non inscrit {else} Inscrit {/if} </td>                        
                        <td style="width: 5%;"><a href="javascript:Events_Detail_Js.sendEmailPDFClickHandler('module=Events&amp;view=SendEmail&amp;mode=composeMailData&amp;record={$RECORD->getId()}&appr={$LINE_APPRENANT_DETAIL["apprenantid$INDEX"]}&email={$LINE_APPRENANT_DETAIL["email$INDEX"]}&doc=attestation')"><i class="fa fa-envelope-square envoiattes cursorPointer" aria-hidden="true" title="Envoi Attestation"></i></a></td>                    	
                        {if $categorieformation eq 'HABILITATIONS' or $categorieformation eq 'AIPR'}
                        <td style="width: 5%;"><a href="javascript:Events_Detail_Js.sendEmailPDFClickHandler('module=Events&amp;view=SendEmail&amp;mode=composeMailData&amp;record={$RECORD->getId()}&appr={$LINE_APPRENANT_DETAIL["apprenantid$INDEX"]}&email={$LINE_APPRENANT_DETAIL["email$INDEX"]}&doc=avisetattestation')"><i class="fa fa-envelope-square envoiattes cursorPointer" aria-hidden="true" title="Envoi Avis"></i></a></td>
                        {/if}
                        {if $categorieformation eq 'AIPR'}
                        {* uni_cnfsecrm - v2 - modif 142 - DEBUT *}
                        <td>
                            <select onchange="Calendar_Detail_Js.setTokenApprenant({$LINE_APPRENANT_DETAIL["apprenantid$INDEX"]},{$RECORD->getId()});" name="tokens" id="tokens{$LINE_APPRENANT_DETAIL["apprenantid$INDEX"]}">
                                <option {if $LINE_APPRENANT_DETAIL["type_tokens$INDEX"] eq 'aucun'} selected="selected" {/if} value="aucun">Aucun</option> 
                                <option {if $LINE_APPRENANT_DETAIL["type_tokens$INDEX"] eq 'concepteur'} selected="selected" {/if} value="concepteur">Concepteur</option>
                                <option {if $LINE_APPRENANT_DETAIL["type_tokens$INDEX"] eq 'encadrant'} selected="selected" {/if} value="encadrant">Encadrant</option>
                                <option {if $LINE_APPRENANT_DETAIL["type_tokens$INDEX"] eq 'operateur'} selected="selected" {/if} value="operateur">Operateur</option>
                            </select>
                        </td>
                        {* uni_cnfsecrm - v2 - modif 142 - FIN *}
                        {/if}
                        {* uni_cnfsecrm - modif 81 - DEBUT *}
                        {if $CLIENT_ID eq 189380}
                            <td style="width: 5%;"><a href="javascript:Events_Detail_Js.sendEmailPDFClickHandler('module=Events&amp;view=SendEmailPersSpecifique&amp;mode=composeMailData&amp;record={$RECORD->getId()}&appr={$LINE_APPRENANT_DETAIL["apprenantid$INDEX"]}&email={$LINE_APPRENANT_DETAIL["email$INDEX"]}&doc=sendconvocation&direct={$directionApp}')"><i class="fa fa-envelope-square envoiattes cursorPointer" aria-hidden="true" title="Envoi convocation"></i></a></td>
                        {else}
                            <td style="width: 5%;"><a href="javascript:Events_Detail_Js.sendEmailPDFClickHandler('module=Events&amp;view=SendEmail&amp;mode=composeMailData&amp;record={$RECORD->getId()}&appr={$LINE_APPRENANT_DETAIL["apprenantid$INDEX"]}&email={$LINE_APPRENANT_DETAIL["email$INDEX"]}&doc=sendconvocation')"><i class="fa fa-envelope-square envoiattes cursorPointer" aria-hidden="true" title="Envoi convocation"></i></a></td>
                        {/if}    
                        
                        {* uni_cnfsecrm - modif 81 - FIN *}
                        <td style="width: 5%;"><a href="javascript:Events_Detail_Js.sendEmailPDFClickHandler('module=Events&amp;view=SendEmail&amp;mode=composeMailData&amp;record={$RECORD->getId()}&appr={$LINE_APPRENANT_DETAIL["apprenantid$INDEX"]}&email={$LINE_APPRENANT_DETAIL["email$INDEX"]}&doc=elearningdoc')"><i class="fa fa-envelope-square envoielearning cursorPointer" aria-hidden="true" title="Envoi Information E-learning"></i></a></td>
                    {* uni_cnfsecrm - v2 - modif 124 - DEBUT *}
                        {if $categorieformation eq 'HABILITATIONS'}
                            <td style="width: 5%;"><a href="index.php?module=Events&action=ExportPDF&record={$RECORD->getId()}&appr={$LINE_APPRENANT_DETAIL["apprenantid$INDEX"]}&app=SALES&doc=exportertout"><i class="fa fa-file-pdf-o exportattes cursorPointer" aria-hidden="true" title="Exporter tout"></i></a></td>
                        {/if}    
                    {* uni_cnfsecrm - v2 - modif 124 - FIN *}    
                        <td>{$LINE_APPRENANT_DETAIL["direction$INDEX"]}</td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
</div>{*189380*}