<!-- uni_cnfsecrm - v2 - modif 107 - FILE  -->
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
        {* uni_cnfsecrm - v2 - modif 114 - DEBUT *}
        <div class="dashboardTitle" title="{vtranslate($WIDGET->getTitle(), $MODULE_NAME)}"><b>Apprenants sans Session</b> (Nombre des apprenants à rappeler {$DATA['nbreAppr']})</div>
        {* uni_cnfsecrm - v2 - modif 114 - FIN *}
    </div>    
        <div class="userList">
            <select name="filterList" id="filterSansSession" class="form-control select2 widgetFilter col-lg-3 reloadOnChange select2-offscreen">
                <option value="1">A rappeler</option>
                <option value="2">Rappelé</option>
                <option value="3">A ne pas rappeler</option>
            </select>
        </div>    
</div>
<div name="history" class="dashboardWidgetContent" style="padding-top:15px;">
    {include file="dashboards/ApprenantSansSessionContents.tpl"|@vtemplate_path:$MODULE_NAME WIDGET=$WIDGET}
</div>