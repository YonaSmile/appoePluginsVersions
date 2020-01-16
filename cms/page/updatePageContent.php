<?php
require('header.php');

use App\Plugin\Cms\Cms;
use App\Plugin\Cms\CmsContent;
use App\Plugin\Cms\CmsMenu;
use App\Template;

require(CMS_PATH . 'process/postProcess.php');

if (!empty($_GET['id'])):

    $Cms = new Cms();
    $Cms->setId($_GET['id']);
    $Cms->setLang(APP_LANG);

    if ($Cms->show()):

        $CmsMenu = new CmsMenu();
        $CmsContent = new CmsContent($Cms->getId(), APP_LANG);

        //check if is a page operated by content CMS
        $menuPages = $CmsMenu->showAll(false, APP_LANG);
        $allMenuPages = extractFromObjArr($menuPages, 'slug');

        //get all pages for navigations
        $allCmsPages = $Cms->showAll();

        //get all html files
        $files = getFilesFromDir(WEB_PUBLIC_PATH . 'html/', ['onlyFiles' => true, 'onlyExtension' => 'php', 'noExtensionDisplaying' => true]);

        echo getAsset('mediaLibrary', true);
        echo getTitle(trans('Contenu de la page') . '<strong> ' . $Cms->getName() . '</strong>', getAppPageSlug());
        showPostResponse(); ?>
        <div class="row my-2">
            <div class="table-responsive">
                <table class="table noEffect">
                    <thead class="thead-light">
                    <tr>
                        <th>ID</th>
                        <th><?= trans('Type'); ?></th>
                        <th><?= trans('Fichier'); ?></th>
                        <th><?= trans('Slug'); ?></th>
                        <th><?= trans('Nom du menu'); ?></th>
                        <th><?= trans('Nom de la page'); ?></th>
                        <th class="text-left"><?= trans('Description'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><?= $Cms->getId(); ?></td>
                        <td><?= $Cms->getType(); ?></td>
                        <td><?= $Cms->getFilename(); ?></td>
                        <td><?= $Cms->getSlug(); ?></td>
                        <td><?= $Cms->getMenuName(); ?></td>
                        <td><?= $Cms->getName(); ?></td>
                        <td class="text-left"><?= $Cms->getDescription(); ?></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-12">
                <div class="row my-3">
                    <div class="col-12 col-lg-8 my-2">
                        <?php if ($Menu->checkUserPermission(getUserRoleId(), 'updatePage')): ?>
                            <button id="updatePageBtn" data-toggle="modal" data-target="#updatePageModal"
                                    class="btn btn-outline-warning btn-sm">
                                <i class="fas fa-wrench"></i> <?= trans('Modifier les en têtes'); ?>
                            </button>
                        <?php endif;
                        if (array_key_exists($Cms->getSlug(), $allMenuPages)): ?>
                            <a href="<?= webUrl($Cms->getSlug() . '/'); ?>"
                               class="btn btn-outline-info btn-sm" target="_blank">
                                <i class="fas fa-external-link-alt"></i> <?= trans('Visualiser la page'); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="col-12 col-lg-4 my-2 text-right">
                        <select class="custom-select custom-select-sm otherPagesSelect"
                                title="<?= trans('Parcourir les pages'); ?>...">
                            <option selected="selected" disabled><?= trans('Parcourir les pages'); ?>...</option>
                            <?php foreach ($allCmsPages as $pageSelect):
                                if ($Cms->getId() != $pageSelect->id): ?>
                                    <option data-href="<?= getPluginUrl('cms/page/pageContent/', $pageSelect->id); ?>"><?= $pageSelect->menuName; ?></option>
                                <?php endif;
                            endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <?php if (file_exists(WEB_PATH . $Cms->getFilename() . '.php')): ?>
        <form action="" method="post" id="pageContentManageForm">
            <div class="row my-2">
                <?php
                $Template = new Template(WEB_PATH . $Cms->getFilename() . '.php', $CmsContent->getData());
                $Template->show();
                ?>
            </div>
        </form>
        <nav id="headerLinks" class="btn-group-vertical"
             data-title="<?= ucfirst(mb_strtolower($Cms->getMenuName())); ?>"></nav>
    <?php else: ?>
        <p><?= trans('Model manquant'); ?></p>
    <?php endif; ?>
        <div class="modal fade" id="updatePageModal" tabindex="-1" role="dialog"
             aria-labelledby="updatePageModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="" method="post" id="updatePageForm">
                        <div class="modal-header">
                            <h5 class="modal-title" id="updatePageModalLabel">Modifier les en têtes</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="custom-control custom-checkbox my-3">
                                <input type="checkbox" class="custom-control-input" id="updateSlugAuto">
                                <label class="custom-control-label"
                                       for="updateSlugAuto"><?= trans('Mettre à jour le lien de la page automatiquement'); ?></label>
                            </div>

                            <?= getTokenField(); ?>
                            <input type="hidden" name="id" value="<?= $Cms->getId(); ?>">
                            <div class="row my-2">
                                <div class="col-12 my-2">
                                    <?= \App\Form::text('Nom', 'name', 'text', $Cms->getName(), true, 70); ?>
                                </div>
                                <div class="col-12 my-2">
                                    <?= \App\Form::textarea('Description', 'description', $Cms->getDescription(), 2, true, 'maxlength="160"'); ?>
                                </div>
                                <div class="col-12 my-2">
                                    <?= \App\Form::text('Nom du menu', 'menuName', 'text', $Cms->getMenuName(), true, 100); ?>
                                </div>
                                <div class="col-12 my-2">
                                    <?= \App\Form::text('Nom du lien URL' . ' (slug)', 'slug', 'text', $Cms->getSlug(), true, 100); ?>
                                </div>
                                <?php if (isTechnicien(getUserRoleId())): ?>
                                    <hr class="hrStyle">
                                    <div class="col-12 my-2">
                                        <?= \App\Form::select('Fichier', 'filename', array_combine($files, $files), $Cms->getFilename(), true); ?>
                                    </div>
                                    <div class="col-12 my-2">
                                        <?= \App\Form::select('Type de page', 'type', array_combine(CMS_TYPES, CMS_TYPES), $Cms->getType(), true); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <?= \App\Form::target('UPDATEPAGE'); ?>
                            <button type="submit" name="UPDATEPAGESUBMIT"
                                    class="btn btn-outline-info"><?= trans('Enregistrer'); ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <script type="text/javascript">

            function updateCmsContent($input, metaValue) {

                busyApp();

                var idCms = '<?= $Cms->getId(); ?>';
                var idCmsContent = $input.data('idcmscontent');
                var metaKey = $input.attr('id');

                delay(function () {
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
                    );
                }, 300);
            }


            $(document).ready(function () {

                $('#updateSlugAuto').on('change', function () {
                    $('form#updatePageForm input#slug').val(convertToSlug($('form#updatePageForm input#name').val()));
                });

                $('form#updatePageForm input#name').on('input', function () {
                    if ($('form#updatePageForm #updateSlugAuto').is(':checked')) {
                        $('form#updatePageForm input#slug').val(convertToSlug($(this).val()));
                    }
                });

                $('#headerLinks').append('<small class="d-block text-center w-100"><strong>' + $('#headerLinks').data('title') + '</strong></small>');
                $.each($('.templateZoneTitle'), function () {
                    var id = Math.random().toString(36).substr(2, 9);
                    $(this).attr('id', id);
                    $('#headerLinks').append('<a class="btn btn-sm btn-outline-info" href="#' + id + '">' + $(this).text() + '</a>');
                });

                $(window).scroll(function () {

                    $('#headerLinks').css('transform', 'translateX(0)');

                    clearTimeout($.data(this, 'scrollTimer'));
                    $.data(this, 'scrollTimer', setTimeout(function () {
                        if ($('#headerLinks a:hover').length == 0) {
                            $('#headerLinks a').blur();
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
                    $('input#' + $('#libraryModal').data('inputid')).val(src).trigger('input');
                    $('#libraryModal').modal('hide');
                });

                $('form#pageContentManageForm').submit(function (event) {
                    event.preventDefault();
                });

                $.each($('#pageContentManageForm input, #pageContentManageForm textarea, #pageContentManageForm select'), function () {
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

                $('form#pageContentManageForm').on('input', 'input, textarea, select', function (event) {
                    event.preventDefault();
                    var $input = $(this);
                    var metaValue = $input.val();

                    updateCmsContent($input, metaValue);
                });

                $(document.body).on('change', '.otherPagesSelect', function () {
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