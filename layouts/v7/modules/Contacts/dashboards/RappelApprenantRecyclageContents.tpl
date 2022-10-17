<!-- uni_cnfsecrm - modif 101 - FILE -->
<!-- uni_cnfsecrm - v2 - modif 111 - FILE -->
{if count($DATA) gt 0 }
	<input class="widgetData" type=hidden value='{Vtiger_Util_Helper::toSafeHTML(ZEND_JSON::encode($DATA))}' />
        <table id="test1" style="text-align: center;" border="1" width="100%">
            <thead>
                <tr>
                    <th style="width: 5%; text-align: center;"></th>
                    <th style="width: 10%; text-align: center;">№ apprenant </th>
                    <th style="width: 20%; text-align: center;">Nom & prenom</th>
                    <th style="width: 20%; text-align: center;">Email</th>
                    <th style="width: 15%; text-align: center;">Telphone</th>
                    <th style="width: 30%; text-align: center;">Nom session</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                {foreach from=$DATA key=ship item=listApprenant}
                    {* uni_cnfsecrm - v2 - modif 114 - DEBUT *}
                    {if $listApprenant['contactid'] != ''}
                        <tr id="row{$listApprenant['contactid']}"> 
                            <td class="" id="openPopup"><span><a href="javascript:Vtiger_Detail_Js.openPopupRecyclage({$listApprenant['contactid']},{$listApprenant['activityid']})" class="fa fa-eye icon action" id="{$listApprenant['contactid']}"></a></span></td>
                            <td>{$listApprenant['contact_no']}</td>
                            <td>{$listApprenant['name']}</td>
                            <td>{$listApprenant['email']}</td>    
                            <td>{$listApprenant['phone']}</td>
                            <td>{$listApprenant['subject']}</td>
                        </tr>
                    {/if}
                    {* uni_cnfsecrm - v2 - modif 114 - FIN *}
                {/foreach}
                </tr>
            </tbody>
        </table>
                <br/><br/>
{else}
	<span class="noDataMsg">
		
	</span>
{/if}


