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
                {if $NOM_FORMATION eq 'Recyclage habilitation B0 H0 H0V' or $NOM_FORMATION eq 'Habilitation électrique BS BE HE-B0 H0 H0V' or $NOM_FORMATION eq 'Habilitation électrique B0 H0-H0V' or $NOM_FORMATION eq 'RECYCLAGE Habilitation BS BE HE' or $NOM_FORMATION eq 'AIPR CONCEPTEUR' or $NOM_FORMATION eq 'AIPR ENCADRANT' or $NOM_FORMATION eq 'AIPR OPERATEUR'}
                {$colspan = '12'}
                {else} 
                {$colspan = '10'}
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
                <th class="lineItemBlockHeader">Inscrit</th>
                                
                <th class="lineItemBlockHeader">Export Attestation</th>                
                <th class="lineItemBlockHeader">Envoi Attestation</th>
                {if $NOM_FORMATION eq 'Recyclage habilitation B0 H0 H0V' or $NOM_FORMATION eq 'Habilitation électrique BS BE HE-B0 H0 H0V' or $NOM_FORMATION eq 'Habilitation électrique B0 H0-H0V' or $NOM_FORMATION eq 'RECYCLAGE Habilitation BS BE HE' or $NOM_FORMATION eq 'AIPR CONCEPTEUR' or $NOM_FORMATION eq 'AIPR ENCADRANT' or $NOM_FORMATION eq 'AIPR OPERATEUR'}
                <th class="lineItemBlockHeader">Export Avis</th>
                <th class="lineItemBlockHeader">Envoi Avis</th>
                {/if}
            </tr>
            
            </thead>
            <tbody>
                {foreach key=INDEX item=LINE_APPRENANT_DETAIL from=$RELATED_SESSION_APPRENANTS}
                    <tr>
                        <td>{$LINE_APPRENANT_DETAIL["contactName$INDEX"]}</td>
                        <td>{$LINE_APPRENANT_DETAIL["numclient_no$INDEX"]}</td>
                        <td>{$LINE_APPRENANT_DETAIL["nomclient$INDEX"]}</td>
                        <td>{$LINE_APPRENANT_DETAIL["telephone$INDEX"]}</td>
                        <td>{$LINE_APPRENANT_DETAIL["email$INDEX"]}</td>
                        <td>{$LINE_APPRENANT_DETAIL["resultat$INDEX"]}</td>
                        <td>{$LINE_APPRENANT_DETAIL["ticket_examen$INDEX"]}</td>
                        <td>{if $LINE_APPRENANT_DETAIL["inscrit$INDEX"] eq '0'} Non inscrit {else} Inscrit {/if} </td>                        
                        <td style="width: 5%;"><a href="index.php?module=Events&action=ExportPDF&record={$RECORD->getId()}&appr={$LINE_APPRENANT_DETAIL["apprenantid$INDEX"]}&app=SALES&doc=attestation"><i class="fa fa-file-pdf-o exportattes cursorPointer" aria-hidden="true" title="Export Attestation"></i></a></td>                        
                        <td style="width: 5%;"><a href="javascript:Events_Detail_Js.sendEmailPDFClickHandler('module=Events&amp;view=SendEmail&amp;mode=composeMailData&amp;record={$RECORD->getId()}&appr={$LINE_APPRENANT_DETAIL["apprenantid$INDEX"]}&email={$LINE_APPRENANT_DETAIL["email$INDEX"]}&doc=attestation')"><i class="fa fa-envelope-square envoiattes cursorPointer" aria-hidden="true" title="Envoi Attestation"></i></a></td>                    	
                        {if $NOM_FORMATION eq 'Recyclage habilitation B0 H0 H0V' or $NOM_FORMATION eq 'Habilitation électrique BS BE HE-B0 H0 H0V' or $NOM_FORMATION eq 'Habilitation électrique B0 H0-H0V' or $NOM_FORMATION eq 'RECYCLAGE Habilitation BS BE HE' or $NOM_FORMATION eq 'AIPR CONCEPTEUR' or $NOM_FORMATION eq 'AIPR ENCADRANT' or $NOM_FORMATION eq 'AIPR OPERATEUR'}
                        <td style="width: 5%;"><a href="index.php?module=Events&action=ExportPDF&record={$RECORD->getId()}&appr={$LINE_APPRENANT_DETAIL["apprenantid$INDEX"]}&app=SALES&doc=avis"><i class="fa fa-file-pdf-o exportattes cursorPointer" aria-hidden="true" title="Export Avis"></i></a></td>
                        <td style="width: 5%;"><a href="javascript:Events_Detail_Js.sendEmailPDFClickHandler('module=Events&amp;view=SendEmail&amp;mode=composeMailData&amp;record={$RECORD->getId()}&appr={$LINE_APPRENANT_DETAIL["apprenantid$INDEX"]}&email={$LINE_APPRENANT_DETAIL["email$INDEX"]}&doc=avis')"><i class="fa fa-envelope-square envoiattes cursorPointer" aria-hidden="true" title="Envoi Avis"></i></a></td>                    	
                        {/if}
                        </tr>
                {/foreach}
            </tbody>
        </table>
    </div>
</div>