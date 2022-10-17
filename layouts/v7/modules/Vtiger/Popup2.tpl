{*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.1
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************}
{* modules/Vtiger/views/Popup.php *}

{strip}
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        {include file="ModalHeader.tpl"|vtemplate_path:$MODULE TITLE={vtranslate($MODULE,$MODULE)}}
        <div class="modal-body">
            <div id="popupPageContainer" class="contentsDiv col-sm-12">
                <input type="hidden" id="parentModule" value="{$SOURCE_MODULE}"/>
                <input type="hidden" id="module" value="{$MODULE}"/>
                <input type="hidden" id="parent" value="{$PARENT_MODULE}"/>
                <input type="hidden" id="sourceRecord" value="{$SOURCE_RECORD}"/>
                <input type="hidden" id="sourceField" value="{$SOURCE_FIELD}"/>
                <input type="hidden" id="url" value="{$GETURL}" />
                <input type="hidden" id="multi_select" value="{$MULTI_SELECT}" />
                <input type="hidden" id="currencyId" value="{$CURRENCY_ID}" />
                <input type="hidden" id="relatedParentModule" value="{$RELATED_PARENT_MODULE}"/>
                <input type="hidden" id="relatedParentId" value="{$RELATED_PARENT_ID}"/>
                <input type="hidden" id="view" name="view" value="{$VIEW}"/>
                <input type="hidden" id="relationId" value="{$RELATION_ID}" />
                <input type="hidden" id="selectedIds" name="selectedIds">
                <input type="hidden" id="row_num" name="row_num" value="{$row_num}">
                {if !empty($POPUP_CLASS_NAME)}
                    <input type="hidden" id="popUpClassName" value="{$POPUP_CLASS_NAME}"/>
                {/if}
                {* uni_cnfsecrm - v2 - modif 109 - DEBUT *}
                <input type="hidden" id="typeFormation" name="typeFormation" value="{$TYPE_FORMATION}">
                {* uni_cnfsecrm - v2 - modif 109 - FIN *}
                {* uni_cnfsecrm - v2 - modif 127 - DEBUT *}
                <h2>Durée Session</h2>
                <div class="row">
                    <div class="col-sm-4 form-group">
                        <label class="">Date de Début de session</label><br>
                        <div class="input-group inputElement" style="margin-bottom: 3px"><input id="date_start_appr" type="text" class="dateField form-control" data-fieldname="date_start_appr" data-fieldtype="date" name="date_start_appr" data-date-format="dd-mm-yyyy" value="{$date_start_appr}" data-rule-required="true" data-rule-date="true" aria-required="true" aria-invalid="false"><span class="input-group-addon"><i class="fa fa-calendar "></i></span></div>
                    </div>
                    <div class="col-sm-4 form-group">
                        <label>Date de fin de session</label><br>
                        <div class="input-group inputElement" style="margin-bottom: 3px"><input id="date_fin_appr" type="text" class="dateField form-control" data-fieldname="date_fin_appr" data-fieldtype="date" name="date_fin_appr" data-date-format="dd-mm-yyyy" value="{$date_fin_appr}" data-rule-required="true" data-rule-date="true" aria-required="true" aria-invalid="false"><span class="input-group-addon"><i class="fa fa-calendar "></i></span></div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-4 form-group">
                        <label>Durée par jour</label><br>
                        <input class="form-control" name="duree_jour" value="{$duree_jour}" type="text" id="duree_jour">
                    </div>
                    <div class="col-sm-4 form-group">
                        <label>Durée par heure</label><br>
                        <input class="form-control" name="duree_heure" value="{$duree_heure}" type="text" id="duree_heure">
                        
                    </div>
                </div>
                {* uni_cnfsecrm - v2 - modif 127 - FIN *}
                <h2>Attestation</h2>
                <HR> 
                {* uni_cnfsecrm - modif 104 - DEBUT *}
               {* <input type="checkbox" id="be_essai" name="be_essai" {if $be_essai eq '1'}checked{/if}> <strong>BE essai</strong> &nbsp;&nbsp;
                <input type="checkbox" id="be_mesurage" name="be_mesurage" {if $be_mesurage eq '1'}checked{/if}> <strong>BE mesurage</strong> &nbsp;&nbsp;
                <input type="checkbox" id="be_verification" name="be_verification" {if $be_verification eq '1'}checked{/if}> <strong>BE vérification</strong> &nbsp;&nbsp;  
                <input type="checkbox" id="be_manoeuvre" name="be_manoeuvre" {if $be_manoeuvre eq '1'}checked{/if}> <strong>BE manoeuvre</strong><br><br>
                <input type="checkbox" id="he_essai" name="he_essai" {if $he_essai eq '1'}checked{/if}> <strong>HE essai</strong> &nbsp;&nbsp; 
                <input type="checkbox" id="he_mesurage" name="he_mesurage" {if $he_mesurage eq '1'}checked{/if}> <strong>HE mesurage</strong> &nbsp;&nbsp;
                <input type="checkbox" id="he_verification" name="he_verification" {if $he_verification eq '1'}checked{/if}> <strong>HE vérification</strong> &nbsp;&nbsp; 
                <input type="checkbox" id="he_manoeuvre" name="he_manoeuvre" {if $he_manoeuvre eq '1'}checked{/if}> <strong>HE manoeuvre</strong> &nbsp;&nbsp; 
                <br><br> *}
                {* uni_cnfsecrm - modif 104 - FIN *}
                <select id="resultat" name="resultat" class="resultat">
                    <option value="avis_favorable" {if $resultat eq 'avis_favorable'}selected{/if}><strong>Avis favorable</strong></option>
                    <option value="avis_defavorable" {if $resultat eq 'avis_defavorable'}selected{/if}><strong>Avis défavorable</strong></option>
                    <option value="autre" {if $resultat eq 'autre'}selected{/if}><strong>Autre</strong></option>
                </select>
                <br><br>
                <h2>Avis</h2>
                <HR>
                <!-- uni_cnfsecrm - modif 104 - DEBUT -->
                <table style="width: 100%; height: 100%; vertical-align: middle; text-align: center " class="table-bordered">
                    <thead>
                        <tr>
                            <th>Habilitation</th>
                            <th><div style="writing-mode:tb-rl; filter: flipv fliph; white-space: nowrap;">B0</div></th>
                            <th><div style="writing-mode:tb-rl; filter: flipv fliph; white-space: nowrap;">H0v</div></th>
                            <th><div style="writing-mode:tb-rl; filter: flipv fliph; white-space: nowrap;">BS</div></th>
                            <th><div style="writing-mode:tb-rl; filter: flipv fliph; white-space: nowrap;">BE manœuvre</div></th>
                            <th><div style="writing-mode:tb-rl; filter: flipv fliph; white-space: nowrap;">B1v</div></th>
                            <th><div style="writing-mode:tb-rl; filter: flipv fliph; white-space: nowrap;">B2v</div></th>
                            <th><div style="writing-mode:tb-rl; filter: flipv fliph; white-space: nowrap;">BC</div></th>
                            <th><div style="writing-mode:tb-rl; filter: flipv fliph; white-space: nowrap;">BR</div></th>
                            <th><div style="writing-mode:tb-rl; filter: flipv fliph; white-space: nowrap;">BE Essai</div></th>
                            <th><div style="writing-mode:tb-rl; filter: flipv fliph; white-space: nowrap;">BE Vérification</div></th>
                            <th><div style="writing-mode:tb-rl; filter: flipv fliph; white-space: nowrap;">BE Mesurage</div></th>
                            <th><div style="writing-mode:tb-rl; filter: flipv fliph; white-space: nowrap;">H1v</div></th>
                            <th><div style="writing-mode:tb-rl; filter: flipv fliph; white-space: nowrap;">H2v</div></th>
                            <th><div style="writing-mode:tb-rl; filter: flipv fliph; white-space: nowrap;">HC</div></th>
                            {* uni_cnfsecrm - v2 - modif 115 - DEBUT *}
                            <th><div style="writing-mode:tb-rl; filter: flipv fliph; white-space: nowrap;">HE</div></th>
                            {* uni_cnfsecrm - v2 - modif 115 - FIN *}
                        </tr>
                    </thead>
                    <tbody>
                        {if $TYPE_FORMATION == "B0 H0 H0v"}
                        <tr style="height: 30px;">
                            <td style="vertical-align: middle; ">B0 H0 H0v</td>
                            <td><input type="checkbox" {if $b0_h0_h0v_b0 eq '1'} checked {/if} id="b0_h0_h0v_b0"></td>
                            <td><input type="checkbox" {if $b0_h0_h0v_h0v eq '1'} checked {/if} id="b0_h0_h0v_h0v"></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            {* uni_cnfsecrm - v2 - modif 115 - DEBUT *}
                            <td></td>
                            {* uni_cnfsecrm - v2 - modif 115 - FIN *}
                        </tr>
                        {else if $TYPE_FORMATION == "BS BE HE"}
                        <tr style="height: 30px;">
                            <td style="vertical-align: middle; ">BS BE HE</td>
                            <td><input type="checkbox" {if $bs_be_he_b0 eq '1'} checked {/if} id="bs_be_he_b0"></td>
                            <td><input type="checkbox" {if $bs_be_he_h0v eq '1'} checked {/if} id="bs_be_he_h0v"></td>
                            <td><input type="checkbox" {if $bs_be_he_bs eq '1'} checked {/if} id="bs_be_he_bs"></td>
                            <td><input type="checkbox" {if $bs_be_he_manoeuvre eq '1'} checked {/if} id="bs_be_he_manoeuvre"></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            {* uni_cnfsecrm - v2 - modif 115 - DEBUT *}
                            <td><input type="checkbox" {if $bs_be_he_he eq '1'} checked {/if} id="bs_be_he_he"></td>
                            {* uni_cnfsecrm - v2 - modif 115 - FIN *}
                        </tr>
                        {else if $TYPE_FORMATION == "B1v B2v BC BR"}
                        <tr style="height: 30px;">
                            <td style="vertical-align: middle; ">B1v B2v BC BR</td>
                            <td><input type="checkbox" {if $b1v_b2v_bc_br_b0 eq '1'} checked {/if} id="b1v_b2v_bc_br_b0"></td>
                            <td><input type="checkbox" {if $b1v_b2v_bc_br_h0v eq '1'} checked {/if} id="b1v_b2v_bc_br_h0v"></td>
                            <td><input type="checkbox" {if $b1v_b2v_bc_br_bs eq '1'} checked {/if} id="b1v_b2v_bc_br_bs"></td>
                            <td><input type="checkbox" {if $b1v_b2v_bc_br_manoeuvre eq '1'} checked {/if} id="b1v_b2v_bc_br_manoeuvre"></td>
                            <td><input type="checkbox" {if $b1v_b2v_bc_br_b1v eq '1'} checked {/if} id="b1v_b2v_bc_br_b1v"></td>
                            <td><input type="checkbox" {if $b1v_b2v_bc_br_b2v eq '1'} checked {/if} id="b1v_b2v_bc_br_b2v"></td>
                            <td><input type="checkbox" {if $b1v_b2v_bc_br_bc eq '1'} checked {/if} id="b1v_b2v_bc_br_bc"></td>
                            <td><input type="checkbox" {if $b1v_b2v_bc_br_br eq '1'} checked {/if} id="b1v_b2v_bc_br_br"></td>
                            <td><input type="checkbox" {if $b1v_b2v_bc_br_essai eq '1'} checked {/if} id="b1v_b2v_bc_br_essai"></td>
                            <td><input type="checkbox" {if $b1v_b2v_bc_br_verification eq '1'} checked {/if} id="b1v_b2v_bc_br_verification"></td>
                            <td><input type="checkbox" {if $b1v_b2v_bc_br_mesurage eq '1'} checked {/if} id="b1v_b2v_bc_br_mesurage"></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            {* uni_cnfsecrm - v2 - modif 115 - DEBUT *}
                            <td><input type="checkbox" {if $b1v_b2v_bc_br_he eq '1'} checked {/if} id="b1v_b2v_bc_br_he"></td>
                            {* uni_cnfsecrm - v2 - modif 115 - FIN *}
                        </tr>
                        {else if $TYPE_FORMATION == "B1v B2v BC BR H1v H2v"}
                        <tr style="height: 30px;">
                            <td style="vertical-align: middle; ">B1v B2v BC BR H1v H2v</td>
                            <td><input type="checkbox" {if $b1v_b2v_bc_br_h1v_h2v_b0 eq '1'} checked {/if} id="b1v_b2v_bc_br_h1v_h2v_b0"></td>
                            <td><input type="checkbox" {if $b1v_b2v_bc_br_h1v_h2v_h0v eq '1'} checked {/if} id="b1v_b2v_bc_br_h1v_h2v_h0v"></td>
                            <td><input type="checkbox" {if $b1v_b2v_bc_br_h1v_h2v_bs eq '1'} checked {/if} id="b1v_b2v_bc_br_h1v_h2v_bs"></td>
                            <td><input type="checkbox" {if $b1v_b2v_bc_br_h1v_h2v_manoeuvre eq '1'} checked {/if} id="b1v_b2v_bc_br_h1v_h2v_manoeuvre"></td>
                            <td><input type="checkbox" {if $b1v_b2v_bc_br_h1v_h2v_b1v eq '1'} checked {/if} id="b1v_b2v_bc_br_h1v_h2v_b1v"></td>
                            <td><input type="checkbox" {if $b1v_b2v_bc_br_h1v_h2v_b2v eq '1'} checked {/if} id="b1v_b2v_bc_br_h1v_h2v_b2v"></td>
                            <td><input type="checkbox" {if $b1v_b2v_bc_br_h1v_h2v_bc eq '1'} checked {/if} id="b1v_b2v_bc_br_h1v_h2v_bc"></td>
                            <td><input type="checkbox" {if $b1v_b2v_bc_br_h1v_h2v_br eq '1'} checked {/if} id="b1v_b2v_bc_br_h1v_h2v_br"></td>
                            <td><input type="checkbox" {if $b1v_b2v_bc_br_h1v_h2v_essai eq '1'} checked {/if} id="b1v_b2v_bc_br_h1v_h2v_essai"></td>
                            <td><input type="checkbox" {if $b1v_b2v_bc_br_h1v_h2v_verification eq '1'} checked {/if} id="b1v_b2v_bc_br_h1v_h2v_verification"></td>
                            <td><input type="checkbox" {if $b1v_b2v_bc_br_h1v_h2v_mesurage eq '1'} checked {/if} id="b1v_b2v_bc_br_h1v_h2v_mesurage"></td>
                            <td><input type="checkbox" {if $b1v_b2v_bc_br_h1v_h2v_h1v eq '1'} checked {/if} id="b1v_b2v_bc_br_h1v_h2v_h1v"></td>
                            <td><input type="checkbox" {if $b1v_b2v_bc_br_h1v_h2v_h2v eq '1'} checked {/if} id="b1v_b2v_bc_br_h1v_h2v_h2v"></td>
                            <td><input type="checkbox" {if $b1v_b2v_bc_br_h1v_h2v_hc eq '1'} checked {/if} id="b1v_b2v_bc_br_h1v_h2v_hc"></td>
                            {* uni_cnfsecrm - v2 - modif 115 - DEBUT *}
                            <td><input type="checkbox" {if $b1v_b2v_bc_br_h1v_h2v_he eq '1'} checked {/if} id="b1v_b2v_bc_br_h1v_h2v_he"></td>
                            {* uni_cnfsecrm - v2 - modif 115 - FIN *}
                        </tr>
                        {/if}
                    </tbody>
                </table>
                <!-- uni_cnfsecrm - modif 104 - FIN -->
                <input type="checkbox" id="initiale" name="initiale" {if $initiale eq '1'}checked{/if}> <strong>Initiale</strong> &nbsp;&nbsp;
                <input type="checkbox" id="recyclage" name="recyclage" {if $recyclage eq '1'}checked{/if}> <strong>Recyclage</strong> <br><br>                                
                <strong>Test prérequis réussi</strong> <input type="radio" id="testprerequis_oui" name="testprerequis" value="oui" {if $testprerequis eq '1'}checked{/if}> <strong>Oui</strong> <input type="radio" id="testprerequis_non" name="testprerequis" value="non" {if $testprerequis eq '0'}checked{/if}> <strong>Non</strong><br><br>
                <strong>Électricien</strong> <input type="radio" id="electricien_oui" name="electricien" value="oui" {if $electricien eq '1'}checked{/if}> <strong>Oui</strong> <input type="radio" id="electricien_non" name="electricien" value="non" {if $electricien eq '0'}checked{/if}> <strong>Non</strong><br><br>
 
<button class="selectAvisAttestation btn btn-success"><strong>Valider</strong></button>
    <input type="hidden" class="triggerEventName" value="{$smarty.request.triggerEventName}"/>
            </div>
        </div>
    </div>
</div>
{/strip}