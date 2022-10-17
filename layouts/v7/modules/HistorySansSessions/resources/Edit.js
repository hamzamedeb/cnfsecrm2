/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
/* uni_cnfsecrm - v2 - modif 107 - FILE */
Vtiger_Edit_Js("HistorySansSessions_Edit_Js", {

    addEditRow: function (typeAction, id) {

        var self = this;
        var commentaire, dateRappel, reponse, etreRappeler

        var updateModule = 0;
        if (typeAction == "edit") {
            var message = "Sûr de modifier la reponse ";
            var firstId = $('#tableData tbody tr').first().attr('id');
            if (firstId == "reponse" + id) {
                updateModule = 1;
            }
            commentaire = $('#reponseedit' + id + ' .commentaire').val();
            dateRappel = $('#reponseedit' + id + ' .dateRappel').val();
            reponse = $('#reponseedit' + id + ' .reponse').val();
            if (reponse == 4) {
                etreRappeler = $('#reponseedit' + id + ' .etreRappeler').val();
            }

        } else if (typeAction == "save") {
            var message = "Sûr d'ajouter la reponse ";
            updateModule = 1;
            var commentaire = $('#tableAction .commentaire').val();
            var dateRappel = $('#tableAction .dateRappel').val();
            var reponse = $('#tableAction .reponse').val();
            if (reponse == 4) {
                var etreRappeler = $('#tableAction .etreRappeler').val();
            }
        }

        var apprenantId = $('.contactid').val();
        var recordId = $('.recordId').val();
        //console.log(commentaire, dateRappel, reponse, typeAction, recordId)

        if (reponse == 0) {
            var messageResultErreur = "selectionner la reponse";
            var modal = "<div class='modal-dialog'><div class='modal-content'><div class='modal-header'><div class='clearfix'><div class='pull-right ' ><button type='button' class='close' aria-label='Close' data-dismiss='modal'><span aria-hidden='true' class='fa fa-close'></span></button></div><h4 class='pull-left'>Result</h4></div></div><div class='modal-body'><div class='mailSentSuccessfully' data-relatedload=''>" + messageResultErreur + "</div><input type='hidden' id='flag' value='SENT'></div></div></div>";
            app.helper.hideProgress();
            app.helper.showModal(modal);
            return;
        }
        var messageResult;
        var dataUrl = "index.php?module=HistorySansSessions&action=ajoutHistorique&record=" + recordId + "&commentaire=" + commentaire + "&dateRappel=" + dateRappel + "&reponse=" + reponse + "&apprenantId=" + apprenantId + "&etreRappeler=" + etreRappeler + "&id=" + id + "&typeAction=" + typeAction + "&updateModule=" + updateModule;
        app.request.get({'url': dataUrl}).then(
                function (error, data) {
                    if (error == null) {
                        console.log('data')
                        console.log(data)
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
                            $('#rowNew .dateRappel').val(y + "-" + m + "-" + d);
                            $('#rowNew .reponse').val(0);
                            $('#rowNew .etreRappeler').val(y + "-" + m + "-" + d);
                            $('#rowNew .etreRappeler').addClass("hidden");
                            $('#rowNew .commentaire').val("");
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

//        var numRow = $('#tableAction tbody tr').length;
//        var row = $('#row0');
//        $('#tableAction tbody').append("<tr>" + row.html() + "</tr>");
//        var newRow = $('#tableAction tbody tr').last().remove('id');
//        newRow.attr('id', 'row' + numRow)
    },

    createNewRow: function (data, lastrow, type) {
//        console.log('data')
//        console.log(data)
        var row = "";
        row += " <tr id='reponse" + data['id'] + "'>";
        row += " <td> " + data['dateRappel'] + " </td>"
        row += " <td> " + data['reponse'] + " <br>";
        if (data['reponseId'] == 4) {
            row += data['etreRappeler'];
        }
        row += " </td>";
        row += " <td>" + data['commentaire'] + "</td>";
        row += " <td><span onclick='HistorySansSessions_Edit_Js.editRow(" + data['id'] + ")' class='fa fa-pencil'></span></td>";
        row += " </tr>";

        row += " <tr id='reponseedit" + data['id'] + "' class='hidden'>"
        row += "    <td>"
        row += "        <input type='input' value='" + data['dateRappel'] + "' class='form-control dateField dateRappel' data-date-format='dd-mm-yyyy' />"
        row += "    </td>"
        row += "    <td class='reponseTd'>"
        row += "        <select onchange='HistorySansSessions_Edit_Js.selectReponse(event, " + data['id'] + ")' class='form-control reponse'>"
        row += "            <option "
        if (data['reponseId'] == 0) {
            row += "           selected='selected' "
        }
        row += "           value='0'></option>";
        row += "            <option "
        if (data['reponseId'] == 1) {
            row += "           selected='selected' "
        }
        row += "           value='1'>Est inscrit chez nous</option>";
        row += "            <option "
        if (data['reponseId'] == 2) {
            row += "           selected='selected' ";
        }
        row += "           value='2'>Est parti à la concurrence</option>";
        row += "            <option "
        if (data['reponseId'] == 3) {
            row += "           selected='selected' ";
        }
        row += "           value='3'>Ne veut pas faire</option>";
        row += "            <option "
        if (data['reponseId'] == 4) {
            row += "           selected='selected' ";
        }
        row += "           value='4'>Désire être rappeler</option>";
        row += "        </select>";
        row += "<input class='form-control dateField etreRappeler ";
        if (data['reponseId'] != 4) {
            row += "     hidden '";
        }
        row += "  type='input' value='" + data['etreRappeler'] + "' data-date-format='dd-mm-yyyy' />";
        row += "    </td>"
        row += "    <td>"
        row += "        <textarea class='form-control commentaire'>" + data['commentaire'] + "</textarea>";
        row += "    </td>";
        row += " <td> <span onclick='HistorySansSessions_Edit_Js.addEditRow(\"edit\", " + data['id'] + ")' class='pointerCursorOnHover input-group-addon input-group-addon-save'><i class='fa fa-check'></i></span> </td>"
        row += " <td> <span onclick='HistorySansSessions_Edit_Js.annulerEditRow(" + data['id'] + ")' class='pointerCursorOnHover input-group-addon input-group-addon-cancel'><i class='fa fa-close'></i></span> </td>"
        row += " </tr> ";
        if (type == "save") {
            $('#tableData tbody').prepend(row)
        } else {
            $('#' + lastrow).eq(0).after(row)
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
        this.verifierReponse();
    },

    verifierReponse: function () {
        var reponse = $('select[name="cf_1281"]').val();
        if (reponse == "Désire être rappeler") {
            $('input[name="cf_1283"]').attr("disabled", false)
        } else {
            $('input[name="cf_1283"]').attr("disabled", true)
        }
        $('select[name="cf_1281"]').on('change', function (e) {
            if ($('select[name="cf_1281"]').val() == "Désire être rappeler") {
                $('input[name="cf_1283"]').attr("disabled", false)
            } else {
                $('input[name="cf_1283"]').attr("disabled", true)
            }
        });
    },
});