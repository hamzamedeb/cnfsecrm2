<!-- uni_cnfsecrm - v2 - modif 146 - FILE -->
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
            <tr>
            {foreach from=$DATA key=ship item=facture} 
                {if $facture['invoiceid'] neq ''}    
                    <tr> 
                        <td style="text-align: center;">
                            <a class="quickView fa fa-eye icon action" id="{$facture['invoiceid']}" data-app="INVENTORY" title="30jours"></a>
                        </td>
                        <td><a href="index.php?module=Invoice&view=Detail&record={$facture['invoiceid']}&app=INVENTORY">{$facture['numero_facture']}</a></td>
                        <td><a href="index.php?module=Invoice&view=Detail&record={$facture['invoiceid']}&app=INVENTORY">{$facture['subject']}</a></td>
                        <td>{$facture['accountname']}</td>
                        <td>{$facture['total']}</td>
                        <td>{$facture['balance']}</td>     
                    </tr>
                {/if}    
            {/foreach}
            </tr>
        </tbody>
    </table>



