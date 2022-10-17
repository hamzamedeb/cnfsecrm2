/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
/* uni_cnfsecrm - v2 - modif 103 - FILE */
Vtiger_Edit_Js("HistoryImpayes_Edit_Js", {

    addEditRow: function (typeAction, id) {
        var self = this;
        var updateModule = 0;
        if (typeAction == "edit") {
            var firstId = $('#tableData tbody tr').first().attr('id');
            if (firstId == "reponse" + id) {
                updateModule = 1;
            }
            var reponsePar = $('#reponseedit' + id + ' .reponsePar').val();
            var commentaire = $('#reponseedit' + id + ' .commentaire').val();
            var dateRappel = $('#reponseedit' + id + ' .dateRappel').val();
            var dateEcheance = $('#reponseedit' + id + ' .dateEcheance').val();
            var typeRelance = $('#reponseedit' + id + ' .typeRelance').val();
        } else if (typeAction == "save") {
            updateModule = 1;
            var reponsePar = $('#rowNew .reponsePar').val();
            var commentaire = $('#rowNew .commentaire').val();
            var dateRappel = $('#rowNew .dateRappel').val();
            var dateEcheance = $('#rowNew .dateEcheance').val();
            var typeRelance = $('#rowNew .typeRelance').val();
        }

        var clientid = $('.clientid').val();
        var invoiceid = $('.invoiceid').val();
        var recordId = $('.recordId').val();

        console.log(reponsePar, commentaire, dateRappel, dateEcheance, typeRelance, clientid, invoiceid, recordId)
        if (dateEcheance == '')
        {
            var messageResultErreur = "Date d’échéance est vide";
            var modal = "<div class='modal-dialog'><div class='modal-content'><div class='modal-header'><div class='clearfix'><div class='pull-right ' ><button type='button' class='close' aria-label='Close' data-dismiss='modal'><span aria-hidden='true' class='fa fa-close'></span></button></div><h4 class='pull-left'>Result</h4></div></div><div class='modal-body'><div class='mailSentSuccessfully' data-relatedload=''>" + messageResultErreur + "</div><input type='hidden' id='flag' value='SENT'></div></div></div>";
            app.helper.hideProgress();
            app.helper.showModal(modal);
            return;
        }
        /* uni_cnfsecrm - v2 - modif 103 - DEBUT */
        var dataUrl = "index.php?module=HistoryImpayes&action=ajoutHistorique&record=" + recordId + "&reponsePar=" + reponsePar + "&commentaire=" + commentaire + "&dateRappel=" + dateRappel + "&clientid=" + clientid + "&invoiceid=" + invoiceid + "&dateEcheance=" + dateEcheance + "&id=" + id + "&typeAction=" + typeAction + "&updateModule=" + updateModule + "&typeRelance=" + typeRelance;
        /* uni_cnfsecrm - v2 - modif 103 - FIN */
        app.request.get({'url': dataUrl}).then(
                function (error, data) {
                    console.log('data1')
                    console.log(data)
                    if (error == null) {

                        if (typeAction == "save")
                        {
                            var dateNow = new Date();
                            var d = dateNow.getDate();
                            var m = dateNow.getMonth();
                            var y = dateNow.getFullYear();
                            m++;
                            if (m < 10) {
                                m = '0' + m;
                            } else if (m == 12) {
                                m = '01';
                            }
                            $('#rowNew .reponsePar').val(1);
                            $('#rowNew .dateRappel').val(y + "-" + m + "-" + d);
                            $('#rowNew .commentaire').val("");
                            $('#rowNew .dateEcheance').val("")
                        }
                        var lastrow = 'reponseedit' + id;
                        self.createNewRow(data['dataNewRow'], lastrow, typeAction)
                        if (typeAction == "edit") {
                            $('#reponse' + data['dataNewRow']['id']).remove();
                            $('#reponseedit' + data['dataNewRow']['id']).remove();
                        }
                    }
                },
                function (error, err) {

                }
        );

    },

    createNewRow: function (data, lastrow, type) {
        var row = "";
        row += " <tr id='reponse" + data['id'] + "'>";
        row += " <td> " + data['reponsePar'] + "</td>";
        row += " <td> " + data['dateRappel'] + " </td>"
        row += " <td> " + data['dateEcheance'] + " </td>"
        /* uni_cnfsecrm - v2 - modif 118 - DEBUT */
        row += " <td> "
        if (data['typeRelance'] == "Depasse de 7 jours") {
            row += " Dépassé de 7 jours ";
        } else if (data['typeRelance'] == "Depasse de 14 jours") {
            row += " Dépassé de 14 jours ";
        } else if (data['typeRelance'] == "Depasse de 30 jours") {
            row += " Dépassé de 30 jours ";
        }
        row += " </td>"
        /* uni_cnfsecrm - v2 - modif 118 - FIN */
        row += " <td>" + data['commentaire'] + "</td>";
        row += " <td><span onclick='HistoryImpayes_Edit_Js.editRow(" + data['id'] + ")' class='fa fa-pencil'></span></td>";
        row += " </tr>";
        // reponseId reponseParId
        row += " <tr id='reponseedit" + data['id'] + "' class='hidden'>"
        row += "    <td>"
        row += "        <select class='form-control reponsePar'>"
        row += "            <option "
        if (data['reponseParId'] == 1) {
            row += "           selected='selected' "
        }
        row += "           value='1'>Par Telephone</option>"
        row += "            <option "
        if (data['reponseParId'] == 2) {
            row += "           selected='selected' "
        }
        row += "           value='2'>Par Email</option>" 
        row += "        </select>"
        row += "    </td>"
        row += "    <td>"
        row += "        <input type='input' value='" + data['dateRappel'] + "' class='form-control dateField dateRappel' data-date-format='dd-mm-yyyy' />"
        row += "    </td>"
        row += "    <td>"
        row += "        <input type='input' value='" + data['dateEcheance'] + "' class='form-control dateField dateEcheance' data-date-format='dd-mm-yyyy' />"
        row += "    </td>"
        /**/
        row += " <td>"
        row += " <select class='form-control typeRelance'>"
        /* correction 1 cnfse - DEBUT  */
        row += " <option";
        if (data['typeRelance'] == "Depasse de 7 jours") {
            row += " selected='selected'";
        }
        row += " value='Depasse de 7 jours'>Dépassé de 7 jours</option>";
        row += " <option ";
        if (data['typeRelance'] == "Depasse de 14 jours") {
            row += " selected='selected'";
        }
        row += " value='Depasse de 14 jours'>Dépassé de 14 jours</option>";
        row += " <option ";
        if (data['typeRelance'] == "Depasse de 30 jours") {
            row += "selected='selected'";
        }
        row += " value='Depasse de 30 jours'>Dépassé de 30 jours</option>"
        /* correction 1 cnfse - FIN  */
        row += "</select>";
        row += "    </td>"
        row += "    <td>"
        row += "        <textarea class='form-control commentaire'>" + data['commentaire'] + "</textarea>";
        row += "    </td>";
        row += " <td> <span onclick='HistoryImpayes_Edit_Js.addEditRow(\"edit\", " + data['id'] + ")' class='pointerCursorOnHover input-group-addon input-group-addon-save'><i class='fa fa-check'></i></span> </td>"
        row += " <td> <span onclick='HistoryImpayes_Edit_Js.annulerEditRow(" + data['id'] + ")' class='pointerCursorOnHover input-group-addon input-group-addon-cancel'><i class='fa fa-close'></i></span> </td>"
        row += " </tr> "

        if (type == "save") {
            $('#tableData tbody').prepend(row)
        } else {
            /* uni_cnfsecrm - v2 - modif 103 - DEBUT */
            $('#' + lastrow).eq(0).after(row)
            /* uni_cnfsecrm - v2 - modif 103 - FIN */
        }
    },

    editRow: function (id) {
        $('#reponse' + id).addClass('hidden');
        $('#reponseedit' + id).removeClass('hidden')
    },
    annulerEditRow: function (id) {
        $('#reponse' + id).removeClass('hidden');
        $('#reponseedit' + id).addClass('hidden')
    },

    selectReponse: function (event, id) {
        console.log('select1')
        if (id) {
            var idSelect = $('#reponseedit' + id + ' select.reponse :selected').val();
            if (idSelect == 4) {
                $('#reponseedit' + id + ' .etreRappeler').removeClass('hidden');
            } else {
                $('#reponseedit' + id + ' .etreRappeler').addClass('hidden');
            }
        } else {
            var idSelect = $('#rowNew select.reponse :selected').val();
            if (idSelect == 4) {
                $('#rowNew .etreRappeler').removeClass('hidden');
            } else {
                $('#rowNew .etreRappeler').addClass('hidden');
            }
        }
    },
}, {
    registerBasicEvents: function (container) {
        this._super(container);
    },
});