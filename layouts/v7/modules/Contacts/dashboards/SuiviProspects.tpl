{* uni_cnfsecrm - v2 - modif 120 - FILE *}
<div class="widgeticons">
    <div class="footerIcons pull-right">
        {include file="dashboards/DashboardFooterIcons.tpl"|@vtemplate_path:$MODULE_NAME}
    </div>
</div>
<div class="dashboardWidgetHeader clearfix">
    {if $SHARED_USERS|@count gt 0 || $SHARED_GROUPS|@count gt 0}
        {assign var="usersList" value="1"}
    {/if}
    <div class="title">
        <div class="dashboardTitle" title="{vtranslate($WIDGET->getTitle(), $MODULE_NAME)}"><b>Suivi Prospects</b> (Nombre des Prospects à rappeler {$DATA['nbreAppr']})</div>
    </div>    
        <div class="userList">
            <select name="filterProspect" id="filterProspect" class="form-control select2 widgetFilter col-lg-3 reloadOnChange select2-offscreen">
                <option value="1">A rappeler</option>
                <option value="2">Rappelé</option>
                <option value="3">A ne pas rappeler</option>
            </select>
        </div>    
</div>
<div name="history" class="dashboardWidgetContent" style="padding-top:15px;">
    {include file="dashboards/SuiviProspectsContents.tpl"|@vtemplate_path:$MODULE_NAME WIDGET=$WIDGET}
</div>