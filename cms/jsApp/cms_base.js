const WEB_CMS_URL = WEB_APP_URL + 'plugin/cms/';
const WEB_CMS_PROCESS_URL = WEB_CMS_URL + 'process/';

const $pageStatus = $('#pageStatus');
const idCms = $('table td[data-cms="id"]').text();
const $headerLinks = $('#headerLinks');
const $libraryContainer = $('#loadMediaLibrary');

function updateCmsContent($input, metaValue) {

    busyApp();
    let idCmsContent = $input.attr('data-idcmscontent');
    let metaKey = $input.attr('name');

    delay(function () {
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

                    $('small.categoryIdFloatContenaire').stop().fadeOut(function () {
                        $('small.' + metaKey).html('Enregistré').stop().fadeIn();
                    });
                    availableApp();
                }
            }
        );
    }, 1000);
}


jQuery(document).ready(function ($) {

    var zoning = true;

    if (!$('.templateZoneTitle').length) {
        zoning = false;
        $('#pageContentLoader').css('opacity', 0).slideUp(500);
        $('#pageContentManageForm').show().addClass('row');
    }

    $headerLinks.append('<small class="d-block text-center w-100"><strong>' + $headerLinks.data('title') + '</strong></small>');
    $.each($('.templateZoneTitle'), function () {

        //Add anchor
        let id = $(this).attr('id');

        if (zoning) {

            $(this).removeAttr('id');
            $('#headerLinks').append('<a class="btn btn-sm btn-outline-info" data-id="' + id + '" type="button" data-toggle="collapse" data-target="#collapse' + id + '">' + $(this).text() + '</a>');

            //Add zone
            $(this).nextUntil('.templateZoneTitle').addBack().wrapAll('<div id="' + id + '" class="templateZone row my-2"></div>');
        } else {
            $('#headerLinks').append('<a class="btn btn-sm btn-outline-info" href="#' + id + '">' + $(this).text() + '</a>');
        }
    });

    if (zoning) {
        var html = '<div class="accordion" id="pageContentManageFormAccordion">';

        $('.templateZone').each(function (num, el) {

            let id = $(this).attr('id');
            let title = $(this).find('h5.templateZoneTitle').text();
            $(el).find('h5.templateZoneTitle').remove();

            //Card
            html += '<div class="card"><div class="card-header bgColorPrimary" id="heading' + id + '"><h2 class="mb-0"><button class="btn btn-link collapsed zoneTitleBtn" type="button" data-id="' + id + '" data-toggle="collapse" data-target="#collapse' + id + '" aria-expanded="false" aria-controls="collapse' + id + '">' + title + ' </button> </h2></div>';
            html += '<div id="collapse' + id + '" class="collapse collapseZone" aria-labelledby="heading' + id + '" data-parent="#pageContentManageFormAccordion"><div class="card-body">';
            html += $(el).get(0).outerHTML;
            html += '</div></div></div>';
        });

        html += '</div>';
        $('#pageContentLoader').css('opacity', 0).slideUp(500);
        $('form#pageContentManageForm').html(html).fadeIn(500);

        let userNavbarHeight = $('#site header nav.navbar').height();
        $(document.body).on('shown.bs.collapse', '.collapseZone', function () {
            var $panel = $(this).closest('.card');
            $('html,body').animate({
                scrollTop: $panel.offset().top - userNavbarHeight
            }, 500);
        })

    }
    $('input[rel=cms-img-popover]').popover({
        container: 'body',
        html: true,
        trigger: 'hover',
        delay: 200,
        placement: 'top',
        content: function () {
            return '<img src="' + $(this).val() + '" />';
        }
    });

    $.each($('#pageContentManageForm input, #pageContentManageForm textarea, #pageContentManageForm select'), function () {
        $('<small class="' + $(this).attr('name') + ' categoryIdFloatContenaire">').insertAfter($(this));
    });

    $(document.body).on('click', '#libraryModal .copyLinkOnClick', function (e) {
        e.preventDefault();
        var inputId = $libraryContainer.attr('data-inputid');
        var src = $(this).parent().data('src');
        $('#pageContentManageForm input#' + inputId).val(src).trigger('input');
        $('#libraryModal').modal('hide');
    });

    $(document.body).on('submit', 'form#pageContentManageForm', function (event) {
        event.preventDefault();
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


    if ($headerLinks.find('a').length) {
        $(window).scroll(function () {

            $headerLinks.css('transform', 'translate(0, -50%)');

            clearTimeout($.data(this, 'scrollTimer'));
            $.data(this, 'scrollTimer', setTimeout(function () {
                if ($('a:hover', $headerLinks).length === 0) {
                    $('a', $headerLinks).blur();
                    $headerLinks.css('transform', 'translate(100%, -50%)');
                }
            }, 3000));
        });
    }

    $(document).on('dblclick', 'input.urlFile', function (event) {
        event.stopPropagation();
        event.preventDefault();

        $('input[rel=cms-img-popover]').popover('hide');
        $libraryContainer.attr('data-inputid', $(this).attr('id'));
        $('#libraryModal').modal('show');
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

    if ($('input.urlFile').length) {
        $pageStatus.html(loaderHtml() + ' Chargement des média.');
        $libraryContainer.load(WEB_APP_URL + 'lib/assets/mediaLibrary.php', function () {
            $pageStatus.html('');
        });
    }
});