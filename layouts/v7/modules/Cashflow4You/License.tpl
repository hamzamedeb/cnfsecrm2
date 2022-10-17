{*<!--
/* * *******************************************************************************
 * The content of this file is subject to the Cashflow4You license.
 * ("License"); You may not use this file except in compliance with the License
 * The Initial Developer of the Original Code is IT-Solutions4You s.r.o.
 * Portions created by IT-Solutions4You s.r.o. are Copyright(C) IT-Solutions4You s.r.o.
 * All Rights Reserved.
 * ****************************************************************************** */
-->*}
{strip}
    <div class="container" id="licenseContainer">
        <form name="profiles_privilegies" action="index.php" method="post" class="form-horizontal">
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12">
                    <h3>{vtranslate('LBL_LICENSE',$MODULE)}</h3>
                    <hr>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12">
                    <input type="hidden" name="module" value="{$MODULE}" />
                    <input type="hidden" name="view" value="" />
                    <input type="hidden" name="license_key_val" id="license_key_val" value="{$LICENSE}" />
                    {include file='LicenseDetails.tpl'|@vtemplate_path:$MODULE}
                </div>
            </div>
        </form>
    </div>
{/strip}