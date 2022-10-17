/*+***********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is: vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 *************************************************************************************/
/* uni_cnfsecrm - modif 82 - FILE */
Vtiger_Edit_Js("Apprenantselearning_Edit_Js", {

    sendEmailPDFClickHandler: function (url) {
        var self = this;
        var params = app.convertUrlToDataParams(url);
        app.helper.showProgress();
        app.request.post({data: params}).then(function (err, response) {
            var callback = function () {
                var emailEditInstance = new Emails_MassEdit_Js();
                emailEditInstance.registerEvents();
            };
            var data = {};
            data['cb'] = callback;
            app.helper.hideProgress();
            app.helper.showModal(response, data);

            /*jQuery('#sendEmail').on('click', function () {
             console.log('click modal')
             var data = "<div class='modal-dialog'><div class='modal-content'><div class='modal-header'><div class='clearfix'><div class='pull-right ' ><button type='button' class='close' aria-label='Close' data-dismiss='modal'><span aria-hidden='true' class='fa fa-close'></span></button></div><h4 class='pull-left'>Result</h4></div></div><div class='modal-body'><div class='mailSentSuccessfully' data-relatedload=''>Statut mis à jour avec succès </div><input type='hidden' id='flag' value='SENT'></div></div></div>";
             //app.helper.hideProgress();
             app.helper.showModal(data);
             });*/
        });

    },

    updateStatut: function (recordId, statut) {
        var self = this;
        var dateRendezVous = $('#dateRendezVous').val();
        console.log(dateRendezVous);
                /* uni_cnfsecrm - modif 85 - DEBUT */
        var heureRendezVous = $('#heureRendezVous').val();
        console.log(heureRendezVous);
        var dataUrl = "index.php?module=Apprenantselearning&action=UpdateStatut&record=" + recordId + "&sourceModule=" + app.getModuleName() + "&statut=" + statut + "&dateRendezVous=" + dateRendezVous + "&heureRendezVous=" + heureRendezVous;
        /* uni_cnfsecrm - modif 85 - FIN */
        var messageemail = '';
        app.request.get({'url': dataUrl}).then(
                function (error, data) {
                    if (error == null) {
                        if (data == true) {
                            messageemail = 'Statut mis à jour avec succès';
                            var typeFormation = $('#typeFormation').val();
                            if (statut == 'marquerInscrit')
                            {
                                /* uni_cnfsecrm - modif 84 - DEBUT */
                                //$('#ignoreInscription').removeClass('hidden');
                                /* uni_cnfsecrm - modif 84 - FIN */
                                messageemail = 'Paramètres inscription envoyés à l\'apprenant';
                            } else if (statut == 'validerFormation') {
                                $('#validerFormationTheorique').addClass('hidden');
                                $('#validerFormation').addClass('hidden');
                                $('#formRendezVous').addClass('hidden')
                                $('#sendEmailRappel').addClass('hidden')
                                $('#statutFormation').text('Fini la formation');
                                /* uni_cnfsecrm - modif 84 - DEBUT */
                                $('#ignoreInscription').addClass('hidden');
                                /* uni_cnfsecrm - modif 84 - FIN */
                                /* uni_cnfsecrm - modif 86 - Debut */
                                if (typeFormation == 'HABILITATIONS' || typeFormation == 'AIPR') {
                                    $('#envoiAvisAttestation').removeClass('hidden');
                                } else if (typeFormation == 'HYGIENE') {
                                    $('#envoiAttestation').removeClass('hidden');
                                }
                                /* uni_cnfsecrm - modif 86 - FIN */
                                messageemail = 'Statut changé à Fini la formation';
                            } else if (statut == 'validerTheorique') {
                                $('#formRendezVous').removeClass('hidden');
                                $('#validerFormationTheorique').addClass('hidden');
                                /* uni_cnfsecrm - modif 84 - DEBUT */
                                $('#ignoreInscription').addClass('hidden');
                                /* uni_cnfsecrm - modif 84 - FIN */
                                $('#statutFormation').text('Rendez-vous à prendre');
                                messageemail = 'Formation théorique validé';
                            } else if (statut == 'ajouterRendezVous') {
                                $('#formRendezVous').addClass('hidden');
                                $('#validerFormation').removeClass('hidden');
                                $('#DateRendezVousChamp').removeClass('hidden');                                
                                $('#HeureRendezVousChamp').removeClass('hidden');  
                                $('#statutFormation').text('Rendez-vous pratique');
                                messageemail = 'Rendez-vous ajouté';
                                /* uni_cnfsecrm - modif 84 - DEBUT */
                            } else if (statut == 'ignoreInscription') {
                                $('#statutFormation').text('Inscription ignoré');
                                $('#validerFormationTheorique').addClass('hidden');
                                $('#validerFormation').addClass('hidden');
                                $('#ignoreInscription').addClass('hidden');
                                $('#sendEmailRappel').addClass('hidden');
                                messageemail = 'Inscription ignoré';                            
                            /* uni_cnfsecrm - modif 84 - FIN */
                        } else
                        {

                        }
                        data = "<div class='modal-dialog'><div class='modal-content'><div class='modal-header'><div class='clearfix'><div class='pull-right ' ><button type='button' class='close' aria-label='Close' data-dismiss='modal'><span aria-hidden='true' class='fa fa-close'></span></button></div><h4 class='pull-left'>Result</h4></div></div><div class='modal-body'><div class='mailSentSuccessfully' data-relatedload=''>" + messageemail + "</div><input type='hidden' id='flag' value='SENT'></div></div></div>";
                        app.helper.hideProgress();
                        app.helper.showModal(data);
                    } else {
                        messageemail = 'Problème modification statut';
                        data = "<div class='modal-dialog'><div class='modal-content'><div class='modal-header'><div class='clearfix'><div class='pull-right ' ><button type='button' class='close' aria-label='Close' data-dismiss='modal'><span aria-hidden='true' class='fa fa-close'></span></button></div><h4 class='pull-left'>Result</h4></div></div><div class='modal-body'><div class='mailSentSuccessfully' data-relatedload=''>" + messageemail + "</div><input type='hidden' id='flag' value='SENT'></div></div></div>";
                        app.helper.hideProgress();
                        app.helper.showModal(data);
                    }
                }
        },
                function (error, err) {

                }
        );
    },

    showNotify: function (message, type) {
        var params = {
            title: message,
            type: type,
            width: '30%',
            delay: '5000'
        };
        Vtiger_Helper_Js.showPnotify(params);
    },

    sendEmailRappel: function (typeRappel, recordId, type) {
        if (type == 'rappel') {
            var message = "Voulez vous rappeler l\'apprenant ?";
        } else if (type == 'inscrit') {
            var message = 'Voulez vous envoyer les paramètres d\'inscriptions à l\'apprenant ?';
        }
        var x = confirm(message);
        if (x) {
            if (type == 'rappel') {
                this.updateRappel(typeRappel, recordId);
                $('#sendEmailRappel').addClass('hidden');
            } else if (type == 'inscrit') {
                this.updateStatut(recordId, 'marquerInscrit', '');
                var typeFormation = $('#typeFormation').val();
                if ((typeFormation == 'AIPR') || (typeFormation == 'HABILITATIONS')) {
                    $('#validerFormationTheorique').removeClass('hidden');
                    $('#marquerInscrit').addClass('hidden');
                } else if (typeFormation == 'HYGIENE') {
                    $('#validerFormation').removeClass('hidden');
                    $('#marquerInscrit').addClass('hidden');
                }
                $('#statutFormation').text('En cours de formation');
            }
            var form = $('#massEmailFormRappel');
            jQuery('#flagRappel').val('SENT');
            form = jQuery(form);
            app.helper.hideModal();
            app.helper.showProgress();
            var data = new FormData(form[0]);
            var postParams = {
                data: data,
                contentType: false,
                processData: false
            };
            app.request.post(postParams).then(function (err, data) {
                if (type == 'rappel') {
                    messageemail = "Email de Rappel Envoyé avec succès";
                    data = "<div class='modal-dialog'><div class='modal-content'><div class='modal-header'><div class='clearfix'><div class='pull-right ' ><button type='button' class='close' aria-label='Close' data-dismiss='modal'><span aria-hidden='true' class='fa fa-close'></span></button></div><h4 class='pull-left'>Result</h4></div></div><div class='modal-body'><div class='mailSentSuccessfully' data-relatedload=''>" + messageemail + "</div><input type='hidden' id='flag' value='SENT'></div></div></div>";
                    app.helper.hideProgress();
                    app.helper.showModal(data);
                    $('#emailApprenant').text(1);
                }
            });
        } else {
            return false;
        }
    },

    updateRappel: function (typeRappel, recordId) {
        var self = this;
        var dataUrl = "index.php?module=Apprenantselearning&action=UpdateRappel&record=" + recordId + "&sourceModule=" + app.getModuleName() + "&typeRappel=" + typeRappel;
        app.request.get({'url': dataUrl}).then(
                function (error, data) {
                    if (error == null) {
                        if (data == true) {

                        } else {

                        }
                    }
                },
                function (error, err) {

                }
        );
    },

    updateRappelTel: function (recordId, type) {
        var self = this;
        var etat = 0;
        if ($('#rappelTel').is(":checked")) {
            etat = 1;
        } else {
            etat = 0;
        }
        var dataUrl = "index.php?module=Apprenantselearning&action=UpdateRappelTel&record=" + recordId + "&sourceModule=" + app.getModuleName() + "&etat=" + etat + "&type=" + type;
        app.request.get({'url': dataUrl}).then(
                function (error, data) {
                    if (error == null) {
                        if (data == true) {
                            messageemail = 'Statut Rappel téléphonique mis à jour avec succès';
                        } else {
                            messageemail = 'Problème de mise à jour du statut Rappel téléphonique';
                        }
                        data = "<div class='modal-dialog'><div class='modal-content'><div class='modal-header'><div class='clearfix'><div class='pull-right ' ><button type='button' class='close' aria-label='Close' data-dismiss='modal'><span aria-hidden='true' class='fa fa-close'></span></button></div><h4 class='pull-left'>Result</h4></div></div><div class='modal-body'><div class='mailSentSuccessfully' data-relatedload=''>" + messageemail + "</div><input type='hidden' id='flag' value='SENT'></div></div></div>";
                        app.helper.hideProgress();
                        app.helper.showModal(data);
                    }
                },
                function (error, err) {

                }
        );

    },
}, {

});