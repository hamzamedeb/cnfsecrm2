 <!-- uni_cnfsecrm - v2 - modif 146 - FILE -->
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
        <div class="dashboardTitle" title="{vtranslate($WIDGET->getTitle(), $MODULE_NAME)}"><b>&nbsp;&nbsp;{vtranslate($WIDGET->getTitle(), $MODULE_NAME)}</b> (Nombre des factures à relancer : {$DATA['nbreImpayees45Jours']})</div>
    </div>
            <!-- uni_cnfsecrm - v2 - modif 147 - DEBUT -->
            <select name="filterList" id="filterList" class="form-control select2 widgetFilter col-lg-3 reloadOnChange select2-offscreen">
                <option value="1">Facture impayées depuis 45 jours et plus</option>
                <option value="2">Facture relancées depuis 45 jours et plus</option>
                <option value="3">A ne pas rappeler</option>
            </select>
            <!-- uni_cnfsecrm - v2 - modif 147 - FIN -->    
</div>
<div name="history" class="dashboardWidgetContent" style="padding-top:15px;">
    {include file="dashboards/Impayes45JoursContents.tpl"|@vtemplate_path:$MODULE_NAME WIDGET=$WIDGET}
</div>