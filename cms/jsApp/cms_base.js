const WEB_CMS_URL = WEB_APP_URL + 'plugin/cms/';
const WEB_CMS_PROCESS_URL = WEB_CMS_URL + 'process/';
const idCms = $('div#cmsPageId').attr('data-cms-id');
const $headerLinks = $('#headerLinks');

function updateCms($input, metaValue) {

    let idCmsContent = $input.attr('data-idcmscontent');
    let metaKey = $input.attr('name');
    $.post(
        WEB_CMS_PROCESS_URL + 'ajaxProcess.php',
        {
            'UPDATECMS': 'OK',
            id: idCmsContent,
            idCms: idCms,
            metaKey: metaKey,
            metaValue: metaValue,
            pageSlug: $('td[data-slug-page]').data('slug-page')
        },
        function (data) {
            if (data) {
                if ($.isNumeric(data)) {
                    $input.attr('data-idcmscontent', data);
                }

                if ($input.hasClass('urlFile')) {
                    popoverUrlFile();
                }

                $('small.categoryIdFloatContenaire').stop().fadeOut(function () {
                    $('small.' + metaKey).html('Enregistré').stop().fadeIn();
                });
            }
        }
    );
}

function updateCmsContent($input, metaValue) {

    busyApp();
    delay(function () {
        updateCms($input, metaValue);
        availableApp();

    }, 1000);
}


jQuery(document).ready(function ($) {

    var zoning = true;

    if (!$('.templateZoneTitle').length) {
        zoning = false;
        $('#pageContentManageForm').show().addClass('row');
    }

    $.each($('.templateZoneTitle'), function () {

        //Add anchor
        let id = $(this).attr('id');

        if (zoning) {

            $(this).removeAttr('id');
            $('#headerLinks').append('<a class="list-group-item list-group-item-action" data-id="' + id + '" type="button" data-bs-toggle="collapse" data-bs-target="#collapse' + id + '">' + $(this).text() + '</a>');

            //Add zone
            $(this).nextUntil('.templateZoneTitle').addBack().wrapAll('<div id="' + id + '" class="templateZone row my-2"></div>');
        } else {
            $('#headerLinks').append('<a class="list-group-item list-group-item-action" href="#' + id + '">' + $(this).text() + '</a>');
        }
    });

    if (zoning) {
        var html = '<div class="accordion" id="pageContentManageFormAccordion">';

        $('.templateZone').each(function (num, el) {

            let id = $(this).attr('id');
            let title = $(this).find('h5.templateZoneTitle').text();
            $(el).find('h5.templateZoneTitle').remove();

            //Card
            html += '<div class="card"><div class="card-header bgColorPrimary" id="heading' + id + '"><h2 class="mb-0"><button class="btn btn-link collapsed zoneTitleBtn" type="button" data-id="' + id + '" data-bs-toggle="collapse" data-bs-target="#collapse' + id + '" aria-expanded="false" aria-controls="collapse' + id + '">' + title + ' </button> </h2></div>';
            html += '<div id="collapse' + id + '" class="collapse collapseZone" aria-labelledby="heading' + id + '" data-bs-parent="#pageContentManageFormAccordion"><div class="card-body">';
            html += $(el).get(0).outerHTML;
            html += '</div></div></div>';
        });

        html += '</div>';
        $('form#pageContentManageForm').html(html).fadeIn(500);

        let userNavbarHeight = $('#site header nav.navbar').height();
        $(document.body).on('shown.bs.collapse', '.collapseZone', function () {
            var $panel = $(this).closest('.card');
            $('html,body').animate({
                scrollTop: $panel.offset().top - userNavbarHeight
            }, 500);
        })

    }

    popoverUrlFile();

    $.each($('#pageContentManageForm input, #pageContentManageForm textarea, #pageContentManageForm select'), function () {
        $('<small class="' + $(this).attr('name') + ' categoryIdFloatContenaire">').insertAfter($(this));
    });

    $(document.body).on('submit', 'form#pageContentManageForm', function (event) {
        event.preventDefault();
    });

    $(document.body).on('click', '#fillLorem', function (event) {
        event.preventDefault();
        let $btn = $(this);

        if (confirm('Vous allez préremplir tous les champs vide !')) {

            $btn.html(loaderHtml() + ' Remplissage en cours');
            let $allInput = $('form#pageContentManageForm input, form#pageContentManageForm textarea');
            let count = 0;

            $allInput.each(function (index, el) {
                if (!$(el).val() || $(el).val() === '<br>') {
                    let val;
                    if ($(el).prop('tagName') === 'INPUT') {
                        if ($(el).hasClass('urlFile')) {
                            val = 'https://via.placeholder.com/1024x600';
                        } else if ($(el).attr('type') === 'url') {
                            val = 'https://aoe-communication.com';
                        } else if ($(el).attr('type') === 'tel') {
                            val = '01 23 45 67 89';
                        } else if ($(el).attr('type') === 'email') {
                            val = 'exemple@domain.fr';
                        } else {
                            val = 'Lorem ipsum dolor sit amet';
                        }
                    } else if ($(el).prop('tagName') === 'TEXTAREA') {
                        val = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi vehicula sed quam ut iaculis. Sed eu ligula rhoncus, viverra turpis nec, malesuada dui. Aliquam sed urna ut nisi facilisis luctus ut ut neque. Mauris ultricies auctor arcu id suscipit. Etiam non aliquet purus. Donec nunc tortor, eleifend a facilisis in, finibus at eros. Curabitur id tristique erat. In hac habitasse platea dictumst. Curabitur sed faucibus sapien. Maecenas faucibus ultricies nulla, id gravida elit faucibus nec. Nam pretium libero at ex tincidunt lacinia. Praesent sem odio, tempus quis iaculis sit amet, venenatis id dolor. Cras at nisi eget lacus rhoncus mattis eget sit amet dui. Praesent fringilla urna libero. Curabitur ut ultricies justo. ';
                        if ($(el).hasClass('appoeditor')) {
                            $('div.inlineAppoeditor[data-editor-id="' + $(el).data('editor-id') + '"]').html(val);
                        }
                    }
                    $(el).val(val);
                    updateCms($(el), val);
                }
                count++;
            });

            if ($allInput.length === count) {
                $btn.html('Préremplie').removeClass('btn-outline-dark').addClass('btn-outline-success disabled').attr('disabled', 'disabled');
            }
        }
    });

    $(document.body).on('input', 'form#pageContentManageForm input, form#pageContentManageForm textarea, form#pageContentManageForm select', function (event) {
        event.preventDefault();
        updateCmsContent($(this), $(this).val());
    });

    $(document.body).on('input change', 'form#pageContentManageForm div.inlineAppoeditor', function () {
        let textarea = $('textarea[data-editor-id="' + $(this).data('editor-id') + '"]');

        if (getViewMode($(this)) === 'viewMode') {
            textarea.val($(this).html());
            updateCmsContent(textarea, textarea.val());
        }
    });

    $('#updateSlugAuto').on('change', function () {
        $('form#updatePageForm input#slug').val(convertToSlug($('form#updatePageForm input#name').val()));
    });

    $('form#updatePageForm input#name').on('keyup', function () {
        if ($('form#updatePageForm #updateSlugAuto').is(':checked')) {
            let $inputSlug = $('form#updatePageForm input#slug');
            $inputSlug.val(convertToSlug($(this).val()));
            countChars($inputSlug, 'slug');
        }
    });

    //Stop adding automaticly slug and description from the name of article
    $('form#updatePageForm input#slug').on('focus', function () {
        $('form#updatePageForm input#name').unbind('keyup');
    });

    $(document.body).on('change', '.otherPagesSelect', function () {
        location.assign($('option:selected', this).data('href'));
    });

    $(document.body).on('click', '#clearPageCache', function () {

        if (confirm('Vous êtes sur le point de vider le cache de la page')) {

            var $btn = $(this);
            $btn.html(loaderHtml());

            busyApp(false);
            $.post(WEB_CMS_PROCESS_URL + 'ajaxProcess.php', {
                clearPageCache: 'OK',
                pageSlug: $btn.data('page-slug'),
                pageLang: $btn.data('page-lang')
            }).done(function (data) {
                if (data === 'true' || data === true) {
                    $btn.html('<i class="fas fa-check"></i> Cache vidé!').blur()
                        .removeClass('btn-outline-danger').addClass('btn-success');
                } else {
                    alert('Un problème est survenu lors de la vidange du cache');
                }
                availableApp();
            });
        }
    });

    $('#updatePageModal').on('show.bs.modal', function (event) {
        let $btn = $(event.relatedTarget);
        let idCms = $btn.attr('data-idcms');
        let $content = $('div[data-idcms="' + idCms + '"]').first();

        let type = $content.find('[data-page="type"]').text();
        let filename = $content.find('[data-page="filename"]').text();
        let parent = $content.find('[data-page="parent"]').attr('data-page-param');
        let menuName = $content.find('[data-page="menuName"]').text();
        let name = $content.find('[data-page="name"]').text();
        let slug = $content.find('[data-page="slug"]').text();
        let description = $content.find('[data-page="description"]').text();

        let $form = $('#updatePageModal').find('form');
        $form.find('input[name="id"]').val(idCms);
        $form.find('input[name="name"]').val(name);
        $form.find('textarea[name="description"]').val(description);
        $form.find('input[name="menuName"]').val(menuName);
        $form.find('input[name="slug"]').val(slug);
        $form.find('select[name="parent"]').val(parent);
        $form.find('select[name="filename"]').val(filename);
        $form.find('select[name="type"]').val(type);
    });

    $(document.body).on('click', 'button.archiveCms', function () {
        let idCms = $(this).data('idcms');
        if (confirm('Vous allez archiver cette page')) {
            busyApp();
            $.post(
                WEB_PLUGIN_URL + 'cms/process/ajaxProcess.php',
                {
                    idCmsArchive: idCms
                },
                function (data) {
                    if (data === true || data == 'true') {
                        $('div.admin-tab[data-idcms="' + idCms + '"]').slideUp().remove();
                        availableApp();
                    }
                }
            );
        }
    });

    $(document.body).on('click', '.unpackPage', function () {
        var idPage = $(this).data('idpage');
        if (confirm('Vous allez désarchiver cette page')) {
            busyApp();
            $.post(
                WEB_PLUGIN_URL + 'cms/process/ajaxProcess.php',
                {
                    unpackPage: 'OK',
                    idUnpackPage: idPage
                },
                function (data) {
                    if (data === true || data == 'true') {
                        $('tr[data-idcms="' + idPage + '"]').slideUp();
                        availableApp();
                    }
                }
            );
        }
    });

    $(document.body).on('click', '.deleteCms', function () {
        var idCms = $(this).data('deletecmsid');
        if (confirm('Vous allez supprimer définitivement cette page')) {
            busyApp();
            $.post(
                WEB_PLUGIN_URL + 'cms/process/ajaxProcess.php',
                {
                    idCmsDelete: idCms
                },
                function (data) {
                    if (data === true || data == 'true') {
                        $('tr[data-idcms="' + idCms + '"]').slideUp();
                        availableApp();
                    }
                }
            );
        }
    });
});