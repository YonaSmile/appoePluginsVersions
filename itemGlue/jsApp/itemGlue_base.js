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
    $('form#addArticleMetaForm textarea#metaValue').val('');
    var idEditor = $('textarea#metaValue').data('editor-id');
    $('div.inlineAppoeditor[data-editor-id="' + idEditor + '"]').html('');
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
                $(this).parent('.checkCategories').wrap('<div class="me-5 my-4 pb-2 border-bottom">');
            } else {
                $(this).parent('.checkCategories').prev('div').append($(this).parent('.checkCategories'));
            }
        }).eq(0).parent('.checkCategories').parent('div').parent('div')
            .addClass('d-flex flex-row justify-content-start flex-wrap').children('strong.inputLabel').addClass('w-100');

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

        $(document.body).on('input change', 'div.inlineAppoeditor', function (e) {
            e.stopPropagation();
            var id = $(this).data('editor-id');
            $('textarea.appoeditor[data-editor-id="' + id + '"]').val($(this).html());
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

            var idEditor = $('textarea#metaValue').data('editor-id');
            var textareaEditor = $('div.inlineAppoeditor[data-editor-id="' + idEditor + '"]');
            let idMeta = $('input[name="UPDATEMETAARTICLE"]').val();

            var data = {
                ADDARTICLEMETA: 'OK',
                UPDATEMETAARTICLE: idMeta,
                idArticle: $('input[name="idArticle"]').val(),
                metaKey: $('input#metaKey').val(),
                metaValue: $('#metaDataAvailable').is(':checked')
                    ? textareaEditor.html().replace(/(<([^>]+)>)/ig, "")
                    : textareaEditor.html()
            };

            addMetaArticle(data).done(function (results) {
                if (results == 'true' || results === true) {

                    notification('<strong>' + $('input#metaKey').val() + '</strong> à été enregistré !');

                    //clear form
                    resetMetas();

                    $articleMetaContainer.html(loaderHtml())
                        .load(WEB_ITEMGLUE_URL + 'page/getMetaArticle.php?idArticle=' + $articleMetaContainer.data('article-id'), function () {
                            $('button.metaProductTitle-' + idMeta).trigger('click');
                        });
                } else {
                    notification('Une erreur est survenu lors de l\'enregistrement de ' + $('input#metaKey').val(), 'danger');
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
            var idEditor = $('textarea#metaValue').data('editor-id');
            $('div.inlineAppoeditor[data-editor-id="' + idEditor + '"]').html(content);
            $('textarea#metaValue').val(content)
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
                        notification('Métadonnée supprimée');
                    }
                    availableApp();
                });
            }
        });

        $('.otherArticlesSelect').change(function () {
            var otherEventslink = $('option:selected', this).data('href');
            location.assign(otherEventslink);
        });

        $(document.body).on('click', '#clearArticleCache', function () {

            if (confirm('Vous êtes sur le point de vider le cache de l\'article')) {

                var $btn = $(this);
                $btn.html(loaderHtml());

                busyApp(false);
                $.post('/app/plugin/cms/process/ajaxProcess.php', {
                    clearPageCache: 'OK',
                    pageSlug: $btn.data('page-slug'),
                    pageLang: $btn.data('page-lang')
                }).done(function (data) {
                    if (data == 'true' || data === true) {
                        $btn.html('<i class="fas fa-check"></i> Cache vidé!').blur()
                            .removeClass('btn-outline-danger').addClass('btn-success');
                    } else {
                        alert('Un problème est survenu lors de la vidange du cache');
                    }
                    availableApp();
                });
            }
        });

        $('#updateSlugAuto').on('change', function () {
            $('form#updateArticleHeadersForm input#slug').val(convertToSlug($('form#updateArticleHeadersForm input#name').val()));
        });

        $('form#updateArticleHeadersForm input#name').on('input', function () {
            if ($('form#updateArticleHeadersForm #updateSlugAuto').is(':checked')) {
                $('form#updateArticleHeadersForm input#slug').val(convertToSlug($(this).val()));
                countChars($('form#updateArticleHeadersForm input#slug'), 'slug');
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

        countChars($('form#addArticleForm input#slug'), 'slug');
        countChars($('form#addArticleForm textarea#description'), 'description');
    });

    //Stop adding automaticly slug and description from the name of article
    $('form#addArticleForm input#slug, form#addArticleForm textarea#description').on('focus', function () {
        $('form#addArticleForm input#name').unbind('keyup');
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
                        $('div.admin-tab[data-idarticle="' + idArticle + '"]').slideUp().remove();
                        $('div#admin-tab-content').html('').hide();
                        closeOffCanvas();
                        availableApp();
                        notification('Article archivé');
                    }
                }
            );
        }
    });

    //Publish an article
    $(document).on('click', 'button.publishArticle', function (e) {
        e.preventDefault();

        let $btn = $(e.target);
        let idArticle = $btn.data('idarticle');
        let $btns = $('button.publishArticle[data-idarticle="' + idArticle + '"');

        if (confirm('Vous allez publier cet article')) {
            busyApp();
            $.post(
                WEB_ITEMGLUE_PROCESS_URL + 'ajaxProcess.php',
                {
                    featuredArticle: 'OK',
                    idArticleFeatured: idArticle,
                    newStatut: 2
                },
                function (data) {
                    if (data === true || data == 'true') {

                        $('div.admin-tab[data-idarticle="' + idArticle + '"]').find('[data-article="status"]').html('Publié');
                        $btns.removeClass('publishArticle').addClass('draftArticle').html('Dépublier l\'article');
                        $btns.closest('div').find('button.featuredArticle').removeAttr('disabled').attr('data-statutarticle', 2)
                            .children('span').removeClass('text-secondary').addClass('text-warning').html('<i class="far fa-star"></i>');
                        availableApp();
                        notification('L\'article a été publié');
                    }
                }
            );
        }
    });

    //Draft an article
    $(document).on('click', 'button.draftArticle', function (e) {
        e.preventDefault();

        let $btn = $(e.target);
        let idArticle = $btn.data('idarticle');
        let $btns = $('button.draftArticle[data-idarticle="' + idArticle + '"');

        if (confirm('Vous allez rendre brouillon cet article')) {
            busyApp();
            $.post(
                WEB_ITEMGLUE_PROCESS_URL + 'ajaxProcess.php',
                {
                    featuredArticle: 'OK',
                    idArticleFeatured: idArticle,
                    newStatut: 1
                },
                function (data) {
                    if (data === true || data == 'true') {

                        $('div.admin-tab[data-idarticle="' + idArticle + '"]').find('[data-article="status"]').html('Brouillon');
                        $btns.removeClass('draftArticle').addClass('publishArticle').html('Publier l\'article');
                        $btns.closest('div').find('button.featuredArticle').attr('disabled', true).attr('data-statutarticle', 1)
                            .children('span').removeClass('text-warning').addClass('text-secondary').html('<i class="far fa-star"></i>');
                        availableApp();
                        notification('L\'article a été brouilloné');
                    }
                }
            );
        }
    });

    //Highlight an article
    $(document).on('click', 'button.featuredArticle', function (e) {
        e.preventDefault();

        let $btn = $(this);
        let idArticle = $btn.data('idarticle');
        let $btns = $('button.featuredArticle[data-idarticle="' + idArticle + '"');
        let statut = parseInt($btn.attr('data-statutarticle'));

        if(statut > 1) {
            let titleStandard = $btn.attr('data-title-standard');
            let titleFeatured = $btn.attr('data-title-vedette');
            let confirmStandard = $btn.attr('data-confirm-standard');
            let confirmFeatured = $btn.attr('data-confirm-vedette');
            let nowStatut = statut === 3 ? 2 : 3;
            let $iconContainer = $btns.children('span');
            let iconFeatured = nowStatut === 3 ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>';
            let textFeatured = nowStatut === 3 ? 'En vedette' : 'Publié';
            let textConfirmFeatured = nowStatut === 3 ? confirmFeatured : confirmStandard;
            let textTitleFeatured = nowStatut === 3 ? titleFeatured : titleStandard;

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

                            $btns.attr('data-statutarticle', nowStatut);
                            $btns.attr('title', textTitleFeatured);
                            $iconContainer.html(iconFeatured);

                            $('div.admin-tab[data-idarticle="' + idArticle + '"]').find('[data-article="status"]').html(textFeatured);

                            availableApp();
                            notification(nowStatut === 3 ? 'L\'article a été mis en vedette' : 'L\'article n\'est plus en vedette');
                        }
                    }
                );
            }
        }
    });

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
                        notification('L\'article n\'est plus archivé');
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
                        notification('Article supprimé');
                    }
                }
            );
        }
    });
});