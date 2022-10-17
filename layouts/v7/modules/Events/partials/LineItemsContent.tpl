{assign var="date_start" value="date_start"|cat:$row_no}
{assign var="start_matin" value="start_matin"|cat:$row_no}
{assign var="end_matin" value="end_matin"|cat:$row_no}
{assign var="start_apresmidi" value="start_apresmidi"|cat:$row_no}
{assign var="end_apresmidi" value="end_apresmidi"|cat:$row_no}
<td style="text-align:center;">
		<i class="fa fa-trash deleteRow cursorPointer" title="{vtranslate('LBL_DELETE',$MODULE)}"></i>
		&nbsp;<a><img src="{vimage_path('drag.png')}" border="0" title="{vtranslate('LBL_DRAG',$MODULE)}"/></a>
		<input type="hidden" class="rowNumber" value="{$row_no}" />
	</td>
<td class="fieldLabelNeeds medium">
                    <label class="muted pull-right marginRight10px">
                        <span class="redColor">*</span>Date</label>
                </td>
                <td class="fieldValue medium">
                    <div>
                        <span class="span10">
                            <div>
                                <div class="input-append row-fluid"><div class="span12 row-fluid date"> 
                                        <input id="{$date_start}" type="text" class="date_start dateField" name="{$date_start}" data-validation-engine="validate[required]" data-date-format="dd-mm-yyyy" style="width: 150px">  
                                        <span class="add-on"><i class="icon-calendar"></i></span>
                                    </div>
                                </div>
                            </div>                            
                        </span>
                    </div>
                </td>
                
                <td class="fieldLabelNeeds medium">
                    <label class="muted pull-right marginRight10px">
                        <span class="redColor">*</span>Heure matin</label>
                </td>
                <td class="fieldValue medium">
                    <div>
                        <span class="span10">
                            <div>
                                <div class="input-append row-fluid">
                                    <div class="span12 row-fluid date">
                                        <input id="{$start_matin}" type="text" data-format="24" name="{$start_matin}" class="start_matin timepicker-default input-small ui-timepicker-input" autocomplete="off">
                                        <span class="add-on cursorPointer"><i class="icon-time"></i></span>
                                    </div>
                                </div>
                            </div>
                            <br/>
                            <div>
                                <div class="input-append time">
                                    <input id="{$end_matin}" type="text" data-format="24" name="{$end_matin}" class="end_matin timepicker-default input-small ui-timepicker-input" autocomplete="off">
                                    <span class="add-on cursorPointer"><i class="icon-time"></i></span>
                                </div>
                            </div>
                        </span>
                    </div>
                </td>
                <td class="fieldLabelNeeds medium">
                    <label class="muted pull-right marginRight10px">
                        <span class="redColor">*</span>Heure après midi</label>
                </td>
                <td class="fieldValue medium">
                    <div>
                        <span class="span10">
                            <div>
                                <div class="input-append row-fluid">
                                    <div class="span12 row-fluid date">
                                        <input id="{$start_apresmidi}" type="text" data-format="24" name="{$start_apresmidi}" class="start_apresmidi timepicker-default input-small ui-timepicker-input" autocomplete="off">
                                        <span class="add-on cursorPointer"><i class="icon-time"></i></span>
                                    </div>
                                </div>
                            </div>
                            <br/>
                            <div>
                                <div class="input-append time">
                                    <input id="{$end_apresmidi}" type="text" data-format="24" name="{$end_apresmidi}" class="end_apresmidi timepicker-default input-small ui-timepicker-input" autocomplete="off">
                                    <span class="add-on cursorPointer"><i class="icon-time"></i></span>
                                </div>
                            </div>
                        </span>
                    </div>
                </td>
                <td></td>