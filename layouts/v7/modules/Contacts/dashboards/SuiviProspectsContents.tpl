{* uni_cnfsecrm - v2 - modif 120 - FILE *}
{if count($DATA) gt 0 }
	<input class="widgetData" type=hidden value='{Vtiger_Util_Helper::toSafeHTML(ZEND_JSON::encode($DATA))}' />
        <table id="test1" style="text-align: center;" border="1" width="100%">
            <thead>
                <tr>
                    <th style="width: 5%; text-align: center;"></th>
                    <th style="width: 10%; text-align: center;">№ prospect </th>
                    <th style="width: 20%; text-align: center;">Nom & prenom</th>
                    <th style="width: 20%; text-align: center;">Email</th>
                    <th style="width: 15%; text-align: center;">Telphone</th>
                    <th style="width: 30%; text-align: center;">Devis</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                {foreach from=$DATA key=ship item=listProspects}
                    {if $listProspects['accountid'] != ''}
                        <tr id="row{$listProspects['accountid']}"> 
                            <td class="" id="openPopupProspects"><span><a href="javascript:Vtiger_Detail_Js.openPopupProspects({$listProspects['accountid']},{$listProspects['quoteid']})" class="fa fa-eye icon action" id="{$listProspects['accountid']}"></a></span></td>
                            <td>{$listProspects['account_no']}</td>
                            <td>{$listProspects['name']}</td>
                            <td>{$listProspects['email']}</td>    
                            <td>{$listProspects['phone']}</td>
                            <td>{$listProspects['subject']}</td>
                        </tr>
                    {/if}
                {/foreach}
                </tr>
            </tbody>
        </table> 
                <br/><br/>
{else}
	<span class="noDataMsg">
		
	</span>
{/if}


