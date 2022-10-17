<!-- unicnfsecrm_mod_15 -->
{if count($DATA) gt 0 }
	<input class="widgetData" type=hidden value='{Vtiger_Util_Helper::toSafeHTML(ZEND_JSON::encode($DATA))}' />
        <table border="1" width="100%">
            <thead>
                <tr>
                    <th>Détail du facture</th>
                    <th>Numéro Facture </th>
                    <th>Intitulé</th>
                    <th>Nom du Client / Prospect</th>                  
                    <th>Montant total</th>
                    <th>Balance</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$DATA key=ship item=facture} 
                    <tr> 
                        <td style="text-align: center;">
                            <a class="quickView fa fa-eye icon action" id="{$facture['invoiceid']}" data-app="INVENTORY" title="7jours"></a>
                        </td>
                        <td><a href="index.php?module=Invoice&view=Detail&record={$facture['invoiceid']}&app=INVENTORY">{$facture['numero_facture']}</a></td>
                        <td><a href="index.php?module=Invoice&view=Detail&record={$facture['invoiceid']}&app=INVENTORY">{$facture['subject']}</a></td>
                        <td>{$facture['accountname']}</td>
                        <td>{$facture['total']}</td>
                        <td>{$facture['balance']}</td>    
                    </tr> 
                {/foreach}
            </tbody>
        </table>
{else}
	<span class="noDataMsg">
		
	</span>
{/if}


