 <!-- uni_cnfsecrm - modif 68 - FILE -->
<div class="dashboardWidgetHeader clearfix"> 
	<table width="100%" cellspacing="0" cellpadding="0">
            <tbody>
                <tr>
                    <td class="span12">
                            <div class="dashboardTitle" title="{vtranslate($WIDGET->getTitle(), $MODULE_NAME)}"><b>&nbsp;&nbsp;{vtranslate($WIDGET->getTitle(), $MODULE_NAME)}</b></div>
                    </td>

                    <td class="refresh span1" align="right">
                            <span style="position:relative;"></span>
                    </td>


                    <td class="widgeticons span4" align="right">
                            {include file="dashboards/DashboardFooterIcons.tpl"|@vtemplate_path:$MODULE_NAME}
                    </td>

                </tr>

            </tbody>
	</table>
</div>

<div class="dashboardWidgetContent" style="padding-top:15px;">
	{include file="dashboards/ProchainesSessionsContents.tpl"|@vtemplate_path:$MODULE_NAME}
</div>