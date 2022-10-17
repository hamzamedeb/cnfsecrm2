/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
/* uni_cnfsecrm - v2 - modif 120 - FILE */
Vtiger_Edit_Js("SuiviProspects_Edit_Js", {

    addEditRow: function (typeAction, id) {
        var self = this;
        var reponsePar, commentaire, dateRappel, reponse, etreRappeler

        var updateModule = 0;
        if (typeAction == "edit") {
            var firstId = $('#tableData tbody tr').first().attr('id');
            if (firstId == "reponse" + id) {
                updateModule = 1;
            }
            reponsePar = $('#reponseedit' + id + ' .reponsePar').val();
            commentaire = $('#reponseedit' + id + ' .commentaire').val();
            dateRappel = $('#reponseedit' + id + ' .dateRappel').val();
            reponse = $('#reponseedit' + id + ' .reponse').val();
            if (reponse == 4) {
                etreRappeler = $('#reponseedit' + id + ' .etreRappeler').val();
            }
        } else if (typeAction == "save") {
            updateModule = 1;
            var reponsePar = $('#tableAction .reponsePar').val();
            var commentaire = $('#tableAction .commentaire').val();
            var dateRappel = $('#tableAction .dateRappel').val();
            var reponse = $('#tableAction .reponse').val();
            if (reponse == 4) {
                var etreRappeler = $('#tableAction .etreRappeler').val();
            }
        }
        console.log(reponsePar, commentaire, dateRappel, reponse, typeAction,id)
        var accountid = $('.accountid').val();
        var devisid = $('.devisid').val();
        var recordId = $('.recordId').val();

        if (reponse == 0) {
            var messageResultErreur = "Vous devez choisir la réponse de prospect !";
            var modal = "<div class='modal-dialog'><div class='modal-content'><div class='modal-header'><div class='clearfix'><div class='pull-right ' ><button type='button' class='close' aria-label='Close' data-dismiss='modal'><span aria-hidden='true' class='fa fa-close'></span></button></div><h4 class='pull-left'>Result</h4></div></div><div class='modal-body'><div class='mailSentSuccessfully' data-relatedload=''>" + messageResultErreur + "</div><input type='hidden' id='flag' value='SENT'></div></div></div>";
            app.helper.hideProgress();
            app.helper.showModal(modal);
            return;
        }
        var messageResult;
        var dataUrl = "index.php?module=SuiviProspects&action=ajoutHistorique&record=" + recordId + "&reponsePar=" + reponsePar + "&commentaire=" + commentaire + "&dateRappel=" + dateRappel + "&reponse=" + reponse + "&accountid=" + accountid + "&devisid=" + devisid + "&etreRappeler=" + etreRappeler + "&id=" + id + "&typeAction=" + typeAction + "&updateModule=" + updateModule;
        app.request.get({'url': dataUrl}).then(
                function (error, data) {
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
    },

    createNewRow: function (data, lastrow, type) {
        var row = "";
        row += " <tr id='reponse" + data['id'] + "'>";
        row += " <td> " + data['dateRappel'] + " </td>"
        row += " <td> " + data['reponse'] + " <br>";
        if (data['reponseId'] == 4) {
            row += data['etreRappeler'];
        }
        row += " </td>";
        row += " <td>" + data['commentaire'] + "</td>";
        row += " <td><span onclick='SuiviProspects_Edit_Js.editRow(" + data['id'] + ")' class='fa fa-pencil'></span></td>";
        row += " </tr>";
        // reponseId reponseParId
        row += " <tr id='reponseedit" + data['id'] + "' class='hidden'>"
        row += "    <td>"
        row += "        <input type='input' value='" + data['dateRappel'] + "' class='form-control dateField dateRappel' data-date-format='dd-mm-yyyy' />"
        row += "    </td>"
        row += "    <td class='reponseTd'>"
        row += "        <select onchange='SuiviProspects_Edit_Js.selectReponse(event, " + data['id'] + ")' class='form-control reponse'>"
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
        row += " <td> <span onclick='SuiviProspects_Edit_Js.addEditRow(\"edit\", " + data['id'] + ")' class='pointerCursorOnHover input-group-addon input-group-addon-save'><i class='fa fa-check'></i></span> </td>"
        row += " <td> <span onclick='SuiviProspects_Edit_Js.annulerEditRow(" + data['id'] + ")' class='pointerCursorOnHover input-group-addon input-group-addon-cancel'><i class='fa fa-close'></i></span> </td>"
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
        var reponse = $('select[name="cf_1254"]').val();
        if (reponse == "Désire être rappeler") {
            $('input[name="cf_1256"]').attr("disabled", false)
        } else {
            $('input[name="cf_1256"]').attr("disabled", true)
        }
        $('select[name="cf_1254"]').on('change', function (e) {
            if ($('select[name="cf_1254"]').val() == "Désire être rappeler") {
                $('input[name="cf_1256"]').attr("disabled", false)
            } else {
                $('input[name="cf_1256"]').attr("disabled", true)
            }
        });
    },
});