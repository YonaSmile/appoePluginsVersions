const WEB_APPOINTMENT_URL = WEB_APP_URL + 'plugin/appointment/';
const WEB_APPOINTMENT_AJAX_URL = WEB_APPOINTMENT_URL + 'ajax/';

function getAdminAgendas() {
    appointment_getLoader();
    appointment_ajax({getAdminAgendas: 'OK'}).done(function (data) {
        if (data) {
            $('div#agendas').html(data);
        }
        appointment_removeLoader();
    });
}

function getAdminListManage(idAgenda, list) {
    return appointment_ajax({getManageList: list, idAgenda: idAgenda});
}

jQuery(window).on('load', function () {

    if ($('div#agendas').length) {
        getAdminAgendas();

        $(document.body).on('submit', 'form#addAgendaForm', function (e) {
            e.preventDefault();

            let $form = $(this);
            let agendaName = $form.find('input#agendaName').val();
            if (agendaName && agendaName !== '') {

                busyApp();
                appointment_ajax({setAdminAgendas: 'OK', agendaName: agendaName}).done(function (data) {
                    if (data === 'true') {
                        $form.trigger('reset');
                        getAdminAgendas();
                    }
                    availableApp();
                });
            }

            getAdminAgendas();
        });

        $(document.body).on('click', 'button.deleteAgenda', function () {
            let $btn = $(this);
            let $parent = $btn.closest('div.agendaInfos');
            let idAgenda = $parent.attr('data-id-agenda');

            busyApp();
            appointment_ajax({deleteAdminAgendas: 'OK', idAgenda: idAgenda}).done(function (data) {
                if (data === 'true') {
                    $parent.fadeOut(500, function () {
                        $parent.remove()
                    });
                    //getAdminAgendas();
                }
                availableApp();
            });
        });

        $(document.body).on('input', 'input[id^=agendaName-]', function () {

            let $input = $(this);
            let $parent = $input.closest('div.agendaInfos');
            let idAgenda = $parent.attr('data-id-agenda');
            let agendaName = $input.val();
            busyApp();
            delay(function () {
                appointment_ajax({changeNameAdminAgendas: 'OK', idAgenda: idAgenda, agendaName: agendaName})
                    .done(function (data) {
                        if (data === 'true') {
                            notification('Le nom de l\'agenda mis à jour');
                        } else {
                            notification('Erreur', 'danger');
                        }
                        availableApp();
                    });
            }, 2000);
        });

        $(document.body).on('change', 'input[id^=agendaStatus-]', function () {
            let $input = $(this);
            let $parent = $input.closest('div.agendaInfos');
            let idAgenda = $parent.attr('data-id-agenda');

            appointment_ajax({changeStatusAdminAgendas: 'OK', idAgenda: idAgenda}).done(function (data) {
                if (data === 'true') {
                    notification('Status mis à jour');
                } else {
                    notification('Erreur', 'danger');
                }
            });
        });
    }

    if ($('div#manageList').length) {

        $(document.body).on('click', 'button.btnAgendaManager', function () {
            let $btn = $(this);
            let manageList = $btn.attr('data-manage');
            let $parent = $btn.closest('div#manageList');
            let idAgenda = $parent.attr('data-id-agenda');

            if ($('#manageType').attr('data-current-type') !== manageList) {

                $('button.btnAgendaManager').removeClass('active');
                $btn.addClass('active');

                appointment_getLoader()
                getAdminListManage(idAgenda, manageList).done(function (data) {
                    if (data) {
                        $('#manageType').attr('data-current-type', manageList).html(data);
                    }
                    appointment_removeLoader();
                });
            }
        });

        $(document.body).on('submit', 'form#addAvailability', function (e) {
            e.preventDefault();

            let $form = $(this);
            let idAgenda = $form.attr('data-id-agenda');
            let day = $form.find('select#day').val();
            let start = $form.find('select#start').val();
            let end = $form.find('select#end').val();

            if (idAgenda && day !== '' && start !== '' && end !== '') {

                busyApp();
                appointment_ajax({
                    setAdminAvailability: 'OK',
                    idAgenda: idAgenda,
                    day: day,
                    start: start,
                    end: end
                }).done(function (data) {
                    if (data === 'true') {
                        getAdminListManage(idAgenda, 'availabilities').done(function (data) {
                            if (data) {
                                $('#manageType').html(data);
                            }
                        });
                    } else {
                        notification(data, 'danger');
                    }
                    availableApp();
                });
            }
        });

        $(document.body).on('click', 'button.deleteAvailability', function () {
            let $btn = $(this);
            let $parent = $btn.closest('div.agendaInfos');
            let idAvailability = $btn.attr('data-id-availability');

            busyApp();
            appointment_ajax({deleteAdminAvailability: 'OK', idAvailability: idAvailability})
                .done(function (data) {
                    if (data === 'true') {
                        $btn.parent('li').fadeOut(500, function () {
                            $btn.parent('li').remove()
                        });
                    }
                    availableApp();
                });
        });

        $(document.body).on('click', 'button.deleteRdvTypeForm', function () {
            let $btn = $(this);
            let $parent = $btn.closest('form.rdvTypForm');
            let idRdvTypeForm = $parent.attr('data-id-rdv-type-form');

            appointment_ajax({deleteAdminARdvTypeForm: 'OK', idRdvTypeForm: idRdvTypeForm})
                .done(function (data) {
                    if (data === 'true') {
                        $parent.fadeOut(500, function () {
                            $parent.remove()
                        });
                    }
                });
        });


        $(document.body).on('submit', 'form#addTypeRdv', function (e) {
            e.preventDefault();

            let $form = $(this);
            let idAgenda = $form.attr('data-id-agenda');
            let rdvTypeName = $form.find('input#name').val();
            let rdvTypeDuration = $form.find('select#duration').val();
            let rdvTypeInfo = $form.find('textarea#information').val();
            if (idAgenda && rdvTypeName !== '' && rdvTypeDuration !== '') {

                busyApp(false);
                appointment_ajax({
                    adminAddRdvType: 'OK',
                    idAgenda: idAgenda,
                    name: rdvTypeName,
                    duration: rdvTypeDuration,
                    information: rdvTypeInfo
                }).done(function (data) {
                    if (data === 'true') {
                        getAdminListManage(idAgenda, 'typeRdv').done(function (data) {
                            if (data) {
                                $('#manageType').html(data);
                            }
                        });
                    }
                    availableApp();
                });
            }
        });

        $(document.body).on('submit', 'form#addTypeRdvForm', function (e) {
            e.preventDefault();

            let $form = $(this);
            let idAgenda = $form.data('id-agenda');
            let idRdvType = $form.data('id-rdv-type');
            let name = $form.find('input#name').val();
            let type = $form.find('select#type').val();
            let placeholder = $form.find('input#placeholder').val();
            let required = $form.find('input#required').is(':checked');
            let position = $form.find('select#position').val();

            if (idAgenda && idRdvType && name !== '' && type !== '' && required !== '' && position !== '') {

                busyApp(false);
                appointment_ajax({
                    adminAddRdvTypeForm: 'OK',
                    idAgenda: idAgenda,
                    idRdvType: idRdvType,
                    name: name,
                    type: type,
                    placeholder: placeholder,
                    required: required,
                    position: position,
                }).done(function (data) {
                    if (data === 'true') {
                        appointment_ajax({getManageList: 'typeRdvForm', idAgenda: idAgenda, idRdvType: idRdvType})
                            .done(function (data) {
                                if (data) {
                                    $('#rdvTypeFormContent').html(data);
                                }
                            });
                    }
                    availableApp();
                });
            }
        });

        $(document.body).on('input', 'input[id^=rdvTypeForm], select[id^=rdvTypeForm]', function (e) {
            e.preventDefault();

            let $form = $(this).closest('form.rdvTypForm');
            let idRdvTypeForm = $form.attr('data-id-rdv-type-form');
            let name = $form.find('input#rdvTypeFormName-' + idRdvTypeForm).val();
            let type = $form.find('select#rdvTypeFormType-' + idRdvTypeForm).val();
            let placeholder = $form.find('input#rdvTypeFormPlaceholder-' + idRdvTypeForm).val();
            let required = $form.find('input#rdvTypeFormRequired-' + idRdvTypeForm).is(':checked');
            let position = $form.find('select#rdvTypeFormPosition-' + idRdvTypeForm).val();

            if (idRdvTypeForm && name !== '' && type !== '' && required !== '' && position !== '') {
                busyApp();
                delay(function () {
                    appointment_ajax({
                        adminUpdateRdvTypeForm: 'OK',
                        idRdvTypeForm: idRdvTypeForm,
                        name: name,
                        type: type,
                        placeholder: placeholder,
                        required: required,
                        position: position
                    }).done(function (data) {
                        if (data === 'true') {
                            notification('Champs personalisé enregistré');
                        } else {
                            notification('Erreur', 'danger');
                        }
                        availableApp();
                    });
                }, 2000);
            }
        });

        $(document.body).on('click', 'button.deleteRdvType', function () {
            let $btn = $(this);
            let $parent = $btn.closest('div.agendaInfos');
            let idRdvType = $parent.attr('data-id-rdv-type');

            appointment_ajax({deleteAdminRdvType: 'OK', idRdvType: idRdvType}).done(function (data) {
                if (data === 'true') {
                    $parent.fadeOut(500, function () {
                        $parent.remove()
                    });
                }
            });
        });

        $(document.body).on('input', 'input[id^=rdvTypeName-]', function () {

            let $input = $(this);
            let $parent = $input.closest('div.agendaInfos');
            let idRdvType = $parent.attr('data-id-rdv-type');
            let rdvTypeName = $input.val();
            busyApp();
            delay(function () {
                appointment_ajax({changeNameAdminRdvType: 'OK', idRdvType: idRdvType, rdvTypeName: rdvTypeName})
                    .done(function (data) {
                        if (data === 'true') {
                            notification('Nom mis à jour');
                        } else {
                            notification('Erreur', 'danger');
                        }
                        availableApp();
                    });
            }, 1000);
        });

        $(document.body).on('input', 'select[id^=rdvTypeDuration-]', function () {

            let $input = $(this);
            let $parent = $input.closest('div.agendaInfos');
            let idRdvType = $parent.attr('data-id-rdv-type');
            let rdvTypeDuration = $input.val();

            appointment_ajax({changeDurationAdminRdvType: 'OK', idRdvType: idRdvType, duration: rdvTypeDuration})
                .done(function (data) {
                    if (data === 'true') {
                        notification('Durée mise à jour');
                    } else {
                        notification('Erreur', 'danger');
                    }
                });
        });

        $(document.body).on('input', 'textarea[id^=information-]', function () {

            let $input = $(this);
            let $parent = $input.closest('div.agendaInfos');
            let idRdvType = $parent.attr('data-id-rdv-type');
            let information = $input.val();
            busyApp();
            delay(function () {
                appointment_ajax({changeInformationAdminRdvType: 'OK', idRdvType: idRdvType, information: information})
                    .done(function (data) {
                        if (data === 'true') {
                            notification('Information mise à jour');
                        } else {
                            notification('Erreur', 'danger');
                        }
                        availableApp();
                    });
            }, 4000);
        });

        $(document.body).on('change', 'input[id^=rdvTypeStatus-]', function () {
            let $input = $(this);
            let $parent = $input.closest('div.agendaInfos');
            let idRdvType = $parent.attr('data-id-rdv-type');

            appointment_ajax({changeStatusAdminRdvType: 'OK', idRdvType: idRdvType}).done(function (data) {
                if (data === 'true') {
                    notification('Status mis à jour');
                } else {
                    notification('Erreur', 'danger');
                }
            });
        });

        $(document.body).on('change', 'select#rdvTypes, select#rdvYear, select#rdvMonth', function () {
            let $select = $(this);
            let $parent = $select.closest('div#getRdvGrid');
            let idRdvType = $parent.find('select#rdvTypes').val();
            let year = $parent.find('select#rdvYear').val();
            let month = $parent.find('select#rdvMonth').val();

            if(idRdvType && year && month) {
                appointment_getLoader();
                appointment_ajax({
                    getRdvGrid: 'OK',
                    idRdvType: idRdvType,
                    year: year,
                    month: month
                }).done(function (data) {
                    if (data) {
                        $('#rdvCalendar').html(data);
                    }
                    appointment_removeLoader();
                });
            }
        });

        $(document.body).on('click', 'table#calendar td.day:not(".other-month")', function () {
            let $el = $(this);

            $('table#calendar td.day').removeClass('selectedDay');
            $el.addClass('selectedDay');

            let date = $el.data('date');
            let idRdvType = $el.closest('table#calendar').data('id-rdv-type');

            if(date && idRdvType) {
                appointment_getLoader();
                appointment_ajax({
                    getRdvAvailabilities: 'OK',
                    idRdvType: idRdvType,
                    date: date,
                }).done(function (data) {
                    if (data) {
                        $('#rdvAvailabilities').html(data);
                    }
                    appointment_removeLoader();
                });
            }
        });

        $(document.body).on('show.bs.modal', '#dedicatedForm', function (event) {
            let button = $(event.relatedTarget);
            var idRdvType = button.closest('div.agendaInfos').data('id-rdv-type');
            let idAgenda = button.closest('div.agendaInfos').data('id-agenda')
            var modal = $(this);

            appointment_ajax({
                getManageList: 'typeRdvForm',
                idAgenda: idAgenda,
                idRdvType: idRdvType
            }).done(function (data) {
                if (data) {
                    modal.find('.modal-body #rdvTypeFormContent').html(data);
                }
            });
        });

    }
});