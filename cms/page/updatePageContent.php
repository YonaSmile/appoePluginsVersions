<?php require('header.php');
if (!empty($_GET['id'])):

    $Cms = new \App\Plugin\Cms\Cms();
    $Cms->setId($_GET['id']);

    if ($Cms->show()):

        $CmsMenu = new \App\Plugin\Cms\CmsMenu();
        $CmsContent = new \App\Plugin\Cms\CmsContent($Cms->getId(), APP_LANG);

        //check if is a page operated by content CMS
        $menuPages = $CmsMenu->showAll();
        $allMenuPages = extractFromObjArr($menuPages, 'slug');

        //get all pages for navigations
        $allCmsPages = $Cms->showAllPages();

        echo getTitle(trans('Contenu de la page') . '<strong> ' . $Cms->getName() . '</strong>', $Page->getSlug()); ?>
        <div class="row my-2">
            <div class="col-12">
                <?php if ($Menu->checkUserPermission(getUserRoleId(), 'updatePage')): ?>
                    <a id="updatePageBtn"
                       href="<?= getPluginUrl('cms/page/update/', $Cms->getId()); ?>"
                       class="btn btn-warning btn-sm">
                        <span class="fas fa-cog"></span> <?= trans('Modifier la page'); ?>
                    </a>
                <?php endif;
                if (array_key_exists($Cms->getSlug(), $allMenuPages)): ?>
                    <a href="<?= webUrl($Cms->getSlug() . '/'); ?>"
                       class="btn btn-info btn-sm" target="_blank">
                        <span class="fas fa-external-link-alt"></span> <?= trans('Visualiser la page'); ?>
                    </a>
                <?php endif; ?>
                <select class="custom-select otherPagesSelect otherProjetSelect notPrint float-right"
                        title="<?= trans('Parcourir les pages'); ?>...">
                    <option selected="selected" disabled><?= trans('Parcourir les pages'); ?>...</option>
                    <?php foreach ($allCmsPages as $pageSelect):
                        if ($Cms->getId() != $pageSelect->id): ?>
                            <option data-href="<?= getPluginUrl('cms/page/pageContent/', $pageSelect->id); ?>"><?= $pageSelect->name; ?></option>
                        <?php endif;
                    endforeach; ?>
                </select>
            </div>
        </div>
        <?php if (file_exists(WEB_PATH . $Cms->getSlug() . '.php')): ?>
        <form action="" method="post" id="pageContentManageForm">
            <div class="row my-2" data-spy="scroll" data-target="#headerLinks" style="position: relative;">
                <?php
                $Template = new \App\Template(WEB_PATH . $Cms->getSlug() . '.php', $CmsContent->getData());
                $Template->show();
                ?>
            </div>
            <div class="row my-2">
                <div class="col-12">
                    <button type="button" class="btn btn-outline-primary btn-block btn-lg">
                        <?= trans('Enregistrer'); ?>
                    </button>
                </div>
            </div>
        </form>
        <nav id="headerLinks" class="btn-group-vertical"></nav>
    <?php else: ?>
        <p><?= trans('Model manquant'); ?></p>
    <?php endif;
        echo getAsset('mediaLibrary'); ?>
        <script type="text/javascript">

            function updateCmsContent($input, metaValue) {

                busyApp();

                var idCms = '<?= $Cms->getId(); ?>';
                var idCmsContent = $input.data('idcmscontent');
                var metaKey = $input.attr('id');

                $.post(
                    '<?= CMS_URL; ?>process/ajaxProcess.php',
                    {
                        id: idCmsContent,
                        idCms: idCms,
                        metaKey: metaKey,
                        metaValue: metaValue
                    },
                    function (data) {
                        if (data) {
                            if ($.isNumeric(data)) {
                                $input.data('idcmscontent', data);
                            }

                            $('small.categoryIdFloatContenaire').stop().fadeOut(function () {
                                $('small.' + metaKey).html('<?= trans('Enregistré'); ?>').stop().fadeIn();
                            });
                            availableApp();
                        }
                    }
                )
            }


            $(document).ready(function () {

                $.each($('.templateZoneTitle'), function () {
                    var id = Math.random().toString(36).substr(2, 9);
                    $('<div id="' + id + '" style="position: relative;top: -40px;"></div>').insertBefore($(this));
                    $('#headerLinks').append('<a class="btn btn-sm btn-outline-info" href="#' + id + '">' + $(this).text() + '</a>');
                });

                $(window).scroll(function () {

                    $('#headerLinks').css('transform', 'translateX(0)');

                    clearTimeout($.data(this, 'scrollTimer'));
                    $.data(this, 'scrollTimer', setTimeout(function () {
                        if ($('#headerLinks a:hover').length == 0) {
                            $('#headerLinks').css('transform', 'translateX(100%)');
                        }
                    }, 3000));
                });

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

                $(document).on('dblclick', 'input.urlFile', function (event) {
                    event.stopPropagation();
                    event.preventDefault();

                    $('input[rel=cms-img-popover]').popover('hide');
                    var idInput = $(this).attr('id');
                    $('#libraryModal').data('inputid', idInput);
                    $('#libraryModal').modal('show');
                });

                $('#libraryModal').on('click', '.copyLinkOnClick', function (e) {
                    e.preventDefault();

                    var src = $(this).parent().data('src');
                    $('input#' + $('#libraryModal').data('inputid')).val(src).trigger('focus');
                    $('#libraryModal').modal('hide');
                });

                $('form#pageContentManageForm').submit(function (event) {
                    event.preventDefault();
                });

                $.each($('input, textarea, select'), function () {
                    var id = $(this).attr('name');
                    $('<small class="' + id + ' categoryIdFloatContenaire">').insertAfter($(this));
                });

                for (var i in CKEDITOR.instances) {

                    CKEDITOR.instances[i].on('blur', function () {
                        var id = this.element.$.id;
                        var $input = $('#' + id);
                        var metaValue = this.getData();

                        updateCmsContent($input, metaValue);
                    });

                }

                $('form#pageContentManageForm').on('blur', 'input, textarea, select', function (event) {
                    event.preventDefault();
                    var $input = $(this);
                    var metaValue = $input.val();

                    updateCmsContent($input, metaValue);
                });

                $('.otherPagesSelect').change(function () {
                    var otherEventslink = $('option:selected', this).data('href');
                    location.assign(otherEventslink);
                });

            });
        </script>
    <?php else:
        echo getContainerErrorMsg(trans('Cette page n\'existe pas'));
    endif;
else:
    echo trans('Cette page n\'existe pas');
endif;
require('footer.php'); ?>