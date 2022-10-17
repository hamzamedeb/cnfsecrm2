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
    <div class="list-content">
        <form name="allowed_modules_form" id="updateAllowedModulesForm" method="post" action="index.php">
            <input type="hidden" name="module" value="Cashflow4You">
            <input type="hidden" name="action" value="Integration">
            <div class="container-fluid" style="position: relative; clear:both;">
                <div class="widget_header row settingsHeader">
                    <h3><a href="index.php?module={$CURRENT_MODULE}&view=List">{vtranslate('Cashflow4You', 'Cashflow4You')} {vtranslate('LBL_INTEGRATION','Cashflow4You')}</a></h3>
                    <hr>
                </div>
                <div  id="CompanyDetailsContainer" class="{if !empty($ERROR_MESSAGE)}hide{/if}">
                    <div class="row">
                        <table class="table table-bordered">
                            <thead>
                            <tr class="listViewContentHeader">
                                <th colspan="2"><strong>{vtranslate('LBL_AVAILABLE_MODULES','Cashflow4You')}</strong></th>
                            </tr>
                            </thead>
                            <tbody>
                            {foreach item=MODULES_VALUE key=MODULES_NAME  from=$MODUES_INTERGATION}
                                <tr>
                                    <td >
                                        <input type="hidden" name="module_{$MODULES_NAME}" id="module_{$MODULES_NAME}" value="{$MODULES_VALUE}"/>
                                        <input type="checkbox" name="chx_{$MODULES_NAME}" id="chx_{$MODULES_NAME}" {if $MODULES_VALUE eq 1 }checked{/if}/>
                                    </td>
                                    <td >
                                        {vtranslate($MODULES_NAME, 'Vtiger')}
                                    </td>
                                </tr>
                            {/foreach}
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
            <div class='modal-overlay-footer clearfix'>
                <div class="row clearfix">
                    <div class='textAlignCenter col-lg-12 col-md-12 col-sm-12 '>
                        <button type='submit' class='btn btn-success saveButton' >{vtranslate('LBL_SAVE', $MODULE)}</button>&nbsp;&nbsp;
                        <a class='cancelLink' href="javascript:history.back()" type="reset">{vtranslate('LBL_CANCEL', $MODULE)}</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
{/strip}