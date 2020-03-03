const WEB_ITEMGLUE_URL = WEB_APP_URL + 'plugin/itemGlue/';
const WEB_ITEMGLUE_PROCESS_URL = WEB_ITEMGLUE_URL + 'process/';

function addMetaArticle(data) {
    return $.post(WEB_ITEMGLUE_PROCESS_URL + 'ajaxProcess.php', data);
}

function deleteMetaArticle(idMetaArticle) {

    return $.post(
        WEB_ITEMGLUE_PROCESS_URL + 'ajaxProcess.php',
        {
            DELETEMETAARTICLE: 'OK',
            idMetaArticle: idMetaArticle
        }
    );
}

function resetMetas() {
    $('form#addArticleMetaForm input[name="UPDATEMETAARTICLE"]').val('');
    $('form#addArticleMetaForm input#metaKey').val('');
    CKEDITOR.instances.metaValue.setData('');
    $('form#addArticleMetaForm').trigger("reset").blur();
    return true;
}

$(document).ready(function () {

    /**
     * Update article content
     */

    if ($("#allMediaModalContainer").length) {

        let $articleMetaContainer = $('#metaArticleContenair');

        $('#allMediaModalContainer').load('/app/ajax/media.php?getAllMedia');

        $articleMetaContainer.load(WEB_ITEMGLUE_URL + 'page/getMetaArticle.php?idArticle=' + $articleMetaContainer.data('article-id'));

        $('form#galleryArticleForm').submit(function () {
            $('#loader').fadeIn('fast');
        });

        $('input[name="categories[]"]').each(function () {
            if ($(this).next('label').text().charAt(0) !== '-') {
                $(this).parent('.checkCategories').wrap('<div class="mr-5 my-4 pb-2 border-bottom">');
            } else {
                $(this).parent('.checkCategories').prev('div').append($(this).parent('.checkCategories'));
            }
        }).eq(0).parent('.checkCategories').parent('div').parent('div')
            .addClass('d-flex flex-row justify-content-start flex-wrap my-3')
            .children('strong.inputLabel').addClass('w-100');

        CKEDITOR.config.height = 300;

        $('#metaDataAvailable').change(function () {
            if ($('#metaDataAvailable').is(':checked')) {
                $('form#addArticleMetaForm input#metaKey').val(convertToSlug($('form#addArticleMetaForm input#metaKey').val()));
            }
        });

        $('form#addArticleMetaForm input#metaKey').keyup(function () {
            if ($('#metaDataAvailable').is(':checked')) {
                $('form#addArticleMetaForm input#metaKey').val(convertToSlug($('form#addArticleMetaForm input#metaKey').val()));
            }
        });

        $(document.body).on('click', '#resetmeta', function (e) {
            e.preventDefault();
            resetMetas();
        });

        $('form#addArticleMetaForm').on('submit', function (event) {
            event.preventDefault();

            if ($('#metaDataAvailable').is(':checked')) {
                if (!confirm('Vous êtes sur le point de supprimer la mise en forme')) {
                    return false;
                }
            }

            var $form = $(this);
            busyApp();

            var data = {
                ADDARTICLEMETA: 'OK',
                UPDATEMETAARTICLE: $('input[name="UPDATEMETAARTICLE"]').val(),
                idArticle: $('input[name="idArticle"]').val(),
                metaKey: $('input#metaKey').val(),
                metaValue: $('#metaDataAvailable').is(':checked')
                    ? CKEDITOR.instances.metaValue.document.getBody().getText()
                    : CKEDITOR.instances.metaValue.getData()
            };

            addMetaArticle(data).done(function (results) {
                if (results == 'true') {

                    //clear form
                    resetMetas();

                    $articleMetaContainer.html(loaderHtml())
                        .load(WEB_ITEMGLUE_URL + 'page/getMetaArticle.php?idArticle=' + $articleMetaContainer.data('article-id'));
                }

                $('[type="submit"]', $form).attr('disabled', false).html('Enregistrer').removeClass('disabled');
                availableApp();
            });
        });

        $articleMetaContainer.on('click', '.metaProductUpdateBtn', function () {
            var $btn = $(this);
            var idMetaArticle = $btn.data('idmetaproduct');

            var $contenair = $('div.card[data-idmetaproduct="' + idMetaArticle + '"]');
            var title = $contenair.find('h5 button.metaProductTitle-' + idMetaArticle).text();
            var content = $contenair.find('div.metaProductContent-' + idMetaArticle).html();

            $('input[name="UPDATEMETAARTICLE"]').val(idMetaArticle);
            $('input#metaKey').val($.trim(title));
            CKEDITOR.instances.metaValue.setData(content);
        });

        $articleMetaContainer.on('click', '.metaProductDeleteBtn', function () {
            var $btn = $(this);
            var idMetaArticle = $btn.data('idmetaproduct');

            if (confirm('Êtes-vous sûr de vouloir supprimer cette métadonnée ?')) {
                busyApp();

                deleteMetaArticle(idMetaArticle).done(function (data) {
                    if (data == 'true') {

                        $articleMetaContainer.html(loaderHtml())
                            .load(WEB_ITEMGLUE_URL + 'page/getMetaArticle.php?idArticle=' + $articleMetaContainer.data('article-id'));
                    }
                    availableApp();
                });
            }
        });

        $('.otherArticlesSelect').change(function () {
            var otherEventslink = $('option:selected', this).data('href');
            location.assign(otherEventslink);
        });

        $('#updateSlugAuto').on('change', function () {
            $('form#updateArticleHeadersForm input#slug').val(convertToSlug($('form#updateArticleHeadersForm input#name').val()));
        });

        $('form#updateArticleHeadersForm input#name').on('input', function () {
            if ($('form#updateArticleHeadersForm #updateSlugAuto').is(':checked')) {
                $('form#updateArticleHeadersForm input#slug').val(convertToSlug($(this).val()));
            }
        });
    }

    /**
     * Add article
     */
    //Focus on input for add an article
    setTimeout(function () {
        $('form#addArticleForm input#name').focus();
    }, 100);

    //Add automatically a slug and a description from name of the article, when add new article
    $('form#addArticleForm input#name').keyup(function () {
        $('form#addArticleForm input#slug').val(convertToSlug($(this).val()));
        $('form#addArticleForm textarea#description').val($(this).val());
    });

    /**
     * All articles
     */

    //Archive an article
    $(document).on('click', '.archiveArticle', function (e) {
        e.preventDefault();

        let $btn = $(this);
        let idArticle = $btn.data('idarticle');
        if (confirm($btn.data('confirm-msg'))) {
            busyApp();
            $.post(
                WEB_ITEMGLUE_PROCESS_URL + 'ajaxProcess.php',
                {
                    archiveArticle: 'OK',
                    idArticleArchive: idArticle
                },
                function (data) {
                    if (data === true || data == 'true') {
                        $('tr[data-idarticle="' + idArticle + '"]').slideUp();
                        availableApp();
                    }
                }
            );
        }
    });

    //Highlight an article
    $(document).on('click', '.featuredArticle', function () {

        let $btn = $(this);

        let titleStandard = $btn.data('title-standard');
        let titleFeatured = $btn.data('title-vedette');
        let confirmStandard = $btn.data('confirm-standard');
        let confirmFeatured = $btn.data('confirm-vedette');

        let currentStatut = $btn.data('statutarticle');
        let nowStatut = currentStatut == 2 ? 1 : 2;

        let idArticle = $btn.data('idarticle');
        let $iconContainer = $btn.children('span');

        let iconFeatured = nowStatut == 2 ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>';

        let textConfirmFeatured = nowStatut == 2 ? confirmStandard : confirmFeatured;
        let textTitleFeatured = nowStatut == 2 ? titleStandard : titleFeatured;

        if (confirm(textConfirmFeatured)) {
            busyApp();
            $.post(
                WEB_ITEMGLUE_PROCESS_URL + 'ajaxProcess.php',
                {
                    featuredArticle: 'OK',
                    idArticleFeatured: idArticle,
                    newStatut: nowStatut
                },
                function (data) {
                    if (data === true || data == 'true') {

                        $btn.data('statutarticle', nowStatut);
                        $btn.attr('title', textTitleFeatured);
                        $iconContainer.html(iconFeatured);
                        availableApp();
                    }
                }
            );
        }
    });

    let artcileGridPreference = getCookie('articleGridPreferences');
    if (artcileGridPreference === 'grid') {
        createArticleGridView();
        showArticlesGrid();
    }

    $('#displayArticleAsGrid').on('click', function () {
        createArticleGridView();
        showArticlesGrid();
    });

    $('#displayArticleAsTable').on('click', function () {
        showArticlesTable();
    });

    function showArticlesTable() {

        $('#displayArticleAsTable').prop('disabled', true);
        setCookie('articleGridPreferences', 'table', 365);

        $('#articlesGridContainer').fadeOut('fast', function () {
            if (!$('.table-responsive:has(table#articlesTable)').is(":visible")) {
                $('.table-responsive:has(table#articlesTable)').fadeIn('fast');
                $('#displayArticleAsGrid').prop('disabled', false);
            }
        });
    }

    function showArticlesGrid() {

        $('#displayArticleAsGrid').prop('disabled', true);
        setCookie('articleGridPreferences', 'grid', 365);

        $('.table-responsive:has(table#articlesTable)').fadeOut('fast', function () {
            if (!$('#articlesGridContainer').is(":visible")) {
                $('#articlesGridContainer').fadeIn('slow');
                $('#displayArticleAsTable').prop('disabled', false);
            }
        });
    }

    function createArticleGridView() {

        if (!$('#articlesGridContainer').length) {

            $('<div id="articlesGridContainer" class="card-columns"></div>')
                .hide()
                .insertAfter('.table-responsive:has(table#articlesTable)');

            $('table#articlesTable tr:has(td)').each(function (index, tr) {
                let $tr = $(tr);
                let html = '<div class="card my-3" data-idarticle="' + $tr.data('idarticle') + '">';

                html += '<img class="card-img" src="' + $tr.data('img') + '" class="card-img-top">';
                html += '<div class="card-body">';
                html += '<p class="card-title"><b>Titre</b><br>' + $tr.find('td[data-col="name"]').text() + '</p>';
                html += '<p class="card-text"><b>Description</b><br>' + $tr.data('description') + '</p>';
                html += '<p class="card-text"><b>Slug</b><br>' + $tr.find('td[data-col="slug"]').text() + '</p>';
                html += '<p class="card-text"><b>Catégories</b><br>' + $tr.find('td[data-col="categories"]').text() + '</p>';
                html += '<p class="card-text"><small class="text-muted">' + $tr.find('td[data-col="date"]').text() + '</small></p>';
                html += '<p class="card-text">' + $tr.find('td[data-col="buttons"]').html() + '</p>';
                html += '</div></div></div>';

                $('#articlesGridContainer').append(html);
            });
        }
    }

    /**
     * Articles archives
     */

    //Unpack an article
    $('.unpackArticle').on('click', function () {

        let $btn = $(this);
        let idArticle = $btn.data('idarticle');
        if (confirm($btn.data('confirm-msg'))) {
            busyApp();
            $.post(
                WEB_ITEMGLUE_PROCESS_URL + 'ajaxProcess.php',
                {
                    unpackArticle: 'OK',
                    idUnpackArticle: idArticle
                },
                function (data) {
                    if (data === true || data == 'true') {
                        $('tr[data-idarticle="' + idArticle + '"]').slideUp();
                        availableApp();
                    }
                }
            );
        }
    });

    //Delete definitively an article
    $('.deleteArticle').on('click', function () {

        let $btn = $(this);
        let idArticle = $btn.data('idarticle');
        if (confirm($btn.data('confirm-msg'))) {
            busyApp();
            $.post(
                WEB_PLUGIN_URL + 'itemGlue/process/ajaxProcess.php',
                {
                    deleteArticle: 'OK',
                    idArticleDelete: idArticle
                },
                function (data) {
                    if (data === true || data == 'true') {
                        $('tr[data-idarticle="' + idArticle + '"]').slideUp();
                        availableApp();
                    }
                }
            );
        }
    });
});