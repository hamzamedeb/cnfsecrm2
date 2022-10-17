<!-- uni_cnfsecrm - modif 68 - FILE -->
{if count($DATA) gt 0 }
	<input class="widgetData" type=hidden value='{Vtiger_Util_Helper::toSafeHTML(ZEND_JSON::encode($DATA))}' />
        <table style="text-align: center;" border="1" width="100%">
            <thead>
                <tr>
                    <th style="width: 50%; text-align: center;">Intitulé</th>
                    <th style="width: 30%; text-align: center;">date de début</th>
                    <th style="width: 20%; text-align: center;">nombre apprenant / session</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                {foreach from=$DATA key=ship item=facture} 
                    <tr> 
                        <td><a href="index.php?module=Calendar&view=Detail&record={$facture['activityid']}&app=SALES">{$facture['subject']}</td>
                        <td>{$facture['date_start']}</td>
                        <td>{$facture['nbreapprenant']}</td>     
                    </tr> 
                {/foreach}
                </tr>
            </tbody>
        </table>
                <br/><br/>
{else}
	<span class="noDataMsg">
		
	</span>
{/if}


