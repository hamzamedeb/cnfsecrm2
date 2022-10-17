{*<!--
/* * *******************************************************************************
 * The content of this file is subject to the Cashflow4You license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is IT-Solutions4You s.r.o.
 * Portions created by IT-Solutions4You s.r.o. are Copyright(C) IT-Solutions4You s.r.o.
 * All Rights Reserved.
 * ****************************************************************************** */
-->*}

<div class="editContainer" style="padding-left: 2%;padding-right: 2%">
    <div class="row">
        <div class="col-sm-12 col-md-12 col-lg-12">
            <h3>{vtranslate('LBL_MODULE_NAME','Cashflow4You')} {vtranslate('LBL_INSTALL','Cashflow4You')}</h3>
        </div>
    </div>
    <hr>
    <div class="row">
    {assign var=LABELS value = ["step1" => "LBL_VALIDATION", "step2" => "LBL_FINISH"]}
    {include file="BreadCrumbs.tpl"|vtemplate_path:$MODULE ACTIVESTEP=$STEP BREADCRUMB_LABELS=$LABELS MODULE=$MODULE}
    </div>
    <div class="clearfix"></div>
    <div class="installationContents">
        <div style="border:1px solid #ccc;padding:1%;{if $STEP neq "1"}display:none;{/if}" id="stepContent1">
            <form name="install" id="editLicense"  method="POST" action="index.php" class="form-horizontal">
                <input type="hidden" name="module" value="Cashflow4You"/>
                <input type="hidden" name="view" value="install"/>
                <input type="hidden" name="installtype" value="validate"/>
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <h4><strong>{vtranslate('LBL_WELCOME','Cashflow4You')}</strong></h4>
                        <br>
                        <p>
                           {vtranslate('LBL_WELCOME_DESC','Cashflow4You')}</br>
                            {vtranslate('LBL_WELCOME_FINISH','Cashflow4You')}
                        </p>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <label><strong>{vtranslate('LBL_INSERT_KEY','Cashflow4You')}</strong></label>
                        <br>
                        <p>
                            {vtranslate('LBL_ONLINE_ASSURE','Cashflow4You')}
                        </p>
                    </div>
                </div>
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            {include file='LicenseDetails.tpl'|@vtemplate_path:$MODULE}
                        </div>
                </div>
            </form>
        </div>
        <div style="border:1px solid #ccc;padding:1%;display:none;" id="stepContent2">
            <input type="hidden" name="installtype" value="redirect_recalculate" />
            <div class="controls">
                <div>{vtranslate('LBL_INSTALL_SUCCESS','Cashflow4You')}</div>
                <div class="clearfix">
                </div>
            </div>
            <br>
            <div class="controls">
                <button type="button" id="next_button" class="btn btn-success"/><strong>{vtranslate('LBL_FINISH','Cashflow4You')}</strong></button>&nbsp;&nbsp;
            </div>
        </div>
    </div>
</div>
<script language="javascript" type="text/javascript">
jQuery(document).ready(function() {
    var thisInstance = Cashflow4You_License_Js.getInstance();
    thisInstance.registerInstallEvents();
});
</script>                                   

 				