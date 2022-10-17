{*<!--
/*+***********************************************************************************
* The contents of this file are subject to the vtiger CRM Public License Version 1.1
* ("License"); You may not use this file except in compliance with the License
* The Original Code is: vtiger CRM Open Source
* The Initial Developer of the Original Code is vtiger.
* Portions created by vtiger are Copyright (C) vtiger.
* All Rights Reserved.
************************************************************************************/
-->*}

{strip}
	<div class="col-sm-12 col-xs-12 module-action-bar clearfix coloredBorderTop">
		<div class="module-action-content clearfix">
			<span class="col-lg-7 col-md-7">
				<span>
					{assign var=MODULE_MODEL value=Vtiger_Module_Model::getInstance($MODULE)}
					{assign var=DEFAULT_FILTER_ID value=$MODULE_MODEL->getDefaultCustomFilter()}
					{if $DEFAULT_FILTER_ID}
						{assign var=CVURL value="&viewname="|cat:$DEFAULT_FILTER_ID}
						{assign var=DEFAULT_FILTER_URL value=$MODULE_MODEL->getListViewUrl()|cat:$CVURL}
					{else}
						{assign var=DEFAULT_FILTER_URL value=$MODULE_MODEL->getListViewUrlWithAllFilter()}
					{/if}
					<a title="{vtranslate($MODULE, $MODULE)}" href='{$DEFAULT_FILTER_URL}'><h4 class="module-title pull-left">&nbsp;{vtranslate($MODULE, $MODULE)}&nbsp;</h4></a>
				</span>
				<span>
					<p class="current-filter-name pull-left">
						&nbsp;<span class="fa fa-angle-right" aria-hidden="true"></span>
						&nbsp;
						{if $VIEW eq 'Detail' or $VIEW eq 'ChartDetail'}
							{$REPORT_NAME}
						{else}
							{$VIEW}
						{/if}
						&nbsp;
					</p>
				</span>
				{if $VIEWNAME}
					{if $VIEWNAME neq 'All'}
						{foreach item=FOLDER from=$FOLDERS}
							{if $FOLDER->getId() eq $VIEWNAME}
								{assign var=FOLDERNAME value=$FOLDER->getName()}
								{break}
							{/if}
						{/foreach}
					{else}
						{assign var=FOLDERNAME value=vtranslate('LBL_ALL_REPORTS', $MODULE)}
					{/if}
					<span>
						<p class="current-filter-name filter-name pull-left"><span class="fa fa-angle-right" aria-hidden="true"></span>&nbsp;{$FOLDERNAME}&nbsp;</p>
					</span>
				{/if}
			</span>

			<span class="col-lg-5 col-md-5 pull-right">
				<div id="appnav" class="navbar-right">
					<ul class="nav navbar-nav">
						{if $IS_CREATE_PERMITTED}
							<li>
								<button id="calendarview_basicaction_addevent" type="button" 
										class="btn addButton btn-default module-buttons cursorPointer" 
										onclick='window.location.href ="index.php?module=Arrivages&view=Wizard"'>
									<div class="fa fa-plus" aria-hidden="true"></div>&nbsp;&nbsp;
									{vtranslate('LBL_WIZARD', $MODULE)}
								</button>								
							</li>
						{/if}
						<li>
							<div class="settingsIcon">
								<button type="button" class="btn btn-default module-buttons dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
									<span class="fa fa-wrench" aria-hidden="true" title="{vtranslate('LBL_SETTINGS', $MODULE)}"></span>&nbsp;&nbsp;{vtranslate('LBL_CUSTOMIZE', 'Reports')}&nbsp; <span class="caret"></span>
								</button>
								<ul class="detailViewSetting dropdown-menu">
									{foreach item=SETTING from=$MODULE_SETTING_ACTIONS}
										{if $SETTING->getLabel() eq 'LBL_EDIT_FIELDS'}
											<li id="{$MODULE_NAME}_listview_advancedAction_{$SETTING->getLabel()}_Events"><a href="{$SETTING->getUrl()}&sourceModule=Events">{vtranslate($SETTING->getLabel(), $MODULE_NAME,vtranslate('LBL_EVENTS',$MODULE_NAME))}</a></li>
											<li id="{$MODULE_NAME}_listview_advancedAction_{$SETTING->getLabel()}_Calendar"><a href="{$SETTING->getUrl()}&sourceModule=Calendar">{vtranslate($SETTING->getLabel(), $MODULE_NAME,vtranslate('LBL_TASKS','Calendar'))}</a></li>
										{else if $SETTING->getLabel() eq 'LBL_EDIT_WORKFLOWS'} 
											<li id="{$MODULE_NAME}_listview_advancedAction_{$SETTING->getLabel()}_WORKFLOWS"><a href="{$SETTING->getUrl()}&sourceModule=Events">{vtranslate('LBL_EVENTS', $MODULE_NAME)} {vtranslate('LBL_WORKFLOWS',$MODULE_NAME)}</a></li>	
											<li id="{$MODULE_NAME}_listview_advancedAction_{$SETTING->getLabel()}_WORKFLOWS"><a href="{$SETTING->getUrl()}&sourceModule=Calendar">{vtranslate('LBL_TASKS', 'Calendar')} {vtranslate('LBL_WORKFLOWS',$MODULE_NAME)}</a></li>
										{else}
											<li id="{$MODULE_NAME}_listview_advancedAction_{$SETTING->getLabel()}"><a href={$SETTING->getUrl()}>{vtranslate($SETTING->getLabel(), $MODULE_NAME, vtranslate($MODULE_NAME, $MODULE_NAME))}</a></li>
										{/if}
									{/foreach}
									<li>
										<a>
											<span id="calendarview_basicaction_calendarsetting" onclick='Calendar_Calendar_Js.showCalendarSettings();' class="cursorPointer">
												{vtranslate('LBL_CALENDAR_SETTINGS', 'Calendar')}
											</span>
										</a>
									</li>
								</ul>
							</div>
						</li>
					</ul>
				</div>
			</span>
		</div>
		{assign var=FIELDS_INFO value=Reports_Field_Model::getListViewFieldsInfo()}
		{if $FIELDS_INFO neq null}
			<script type="text/javascript">
				var uimeta = (function () {
					var fieldInfo = {$FIELDS_INFO};
					return {
						field: {
							get: function (name, property) {
								if (name && property === undefined) {
									return fieldInfo[name];
								}
								if (name && property) {
									return fieldInfo[name][property]
								}
							},
							isMandatory: function (name) {
								if (fieldInfo[name]) {
									return fieldInfo[name].mandatory;
								}
								return false;
							},
							getType: function (name) {
								if (fieldInfo[name]) {
									return fieldInfo[name].type
								}
								return false;
							}
						}
					};
				})();
			</script>
		{/if}
		<div class="rssAddFormContainer hide">
		</div>
	</div> 
{/strip}