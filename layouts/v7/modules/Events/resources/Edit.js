/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/

Calendar_Edit_Js("Events_Edit_Js", {

}, {
    registerAddingNewDate: function () {
        console.log("registerAddingNewDate");
        var thisInstance = this;
        var lineItemTable = this.getLineItemContentsContainer();
        jQuery('.addDate').on('click', function () {
            var newRow = thisInstance.getBasicRow().addClass(thisInstance.rowClass);
            var sequenceNumber = thisInstance.getNextLineItemRowNumber();
            var id_bouton = this.id;
            var id_list = id_bouton.replace('addDate', '');
            newRow = newRow.appendTo(jQuery('#lineItemTab' + id_list));
            thisInstance.checkLineItemRow();
            newRow.find('input.rowNumber').val(sequenceNumber);
            thisInstance.updateLineItemsElementWithSequenceNumber(newRow, sequenceNumber);
        });
    },

    registerEvents: function () { 
        this._super();
        this.registerAddingNewDate();
    }

});