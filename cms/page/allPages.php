<?php require('header.php');

use App\Form;
use App\Plugin\Cms\Cms;

require(CMS_PATH . 'process/postProcess.php');

$Cms = new Cms();
$Cms->setLang(APP_LANG);
$allPages = $Cms->showAll();

//get all html files
$files = getFilesFromDir(WEB_PUBLIC_PATH . 'html/', ['onlyFiles' => true, 'onlyExtension' => 'php', 'noExtensionDisplaying' => true]);

echo getTitle(getAppPageName(), getAppPageSlug()); ?>
    <div class="row">
        <div class="col-12 col-md-4 col-lg-3">
            <?php if ($allPages): ?>
                <div class="input-group mb-1" id="admin-tab-search">
                    <div class="input-group-prepend">
                        <div class="input-group-text"><i class="fas fa-search"></i></div>
                    </div>
                    <input type="search" class="form-control" id="admin-tab-search-input" placeholder="Rechercher...">
                </div>
                <div id="admin-tabs">
                    <?php foreach ($allPages as $page): ?>
                        <div class="admin-tab" data-idcms="<?= $page->id ?>"
                             data-filter="<?= $page->type ?> <?= $page->filename ?> <?= $page->menuName ?> <?= $page->name ?> <?= $page->slug ?>">
                            <div class="admin-tab-header">
                                <h5><?= $page->name ?></h5>
                                <small><?= $page->type ?></small>
                            </div>
                            <div class="admin-tab-content">
                                <div class="d-flex align-items-center justify-content-center justify-content-lg-start p-3 text-center text-lg-left">
                                    <div class="d-none d-lg-block display-3 mr-3"><i class="fas fa-file-alt"></i></div>
                                    <div>
                                        <h2><?= $page->name ?></h2>
                                        <a href="<?= getPluginUrl('cms/page/pageContent/', $page->id) ?>"
                                           class="btnLink"><?= trans('Consulter'); ?></a>
                                        |
                                        <button type="button" class="btnLink updateCms" data-toggle="modal" data-idcms="<?= $page->id ?>"
                                                data-target="#updatePageModal"><?= trans('Modifier'); ?></button>
                                        |
                                        <button type="button" class="btnLink deleteCms" data-idcms="<?= $page->id ?>">
                                            <?= trans('Archiver'); ?></button>
                                    </div>
                                </div>
                                <div class="p-0 px-lg-3 pb-lg-3" data-idcms="<?= $page->id ?>">
                                    <p><i class="fas fa-fingerprint"></i><strong><?= trans('ID'); ?></strong><?= $page->id ?></p>
                                    <p><i class="fas fa-layer-group"></i><strong><?= trans('Type'); ?></strong><span data-page="type"><?= $page->type ?></span></p>
                                    <p><i class="far fa-clock"></i>
                                        <strong><?= trans('Modifié le'); ?></strong><?= displayTimeStamp($page->updated_at) ?>
                                    </p>
                                    <p><i class="far fa-file-code"></i><strong><?= trans('Fichier'); ?></strong><span data-page="filename"><?= $page->filename ?></span></p>
                                    <p><i class="fas fa-project-diagram"></i><strong><?= trans('Nom du menu'); ?></strong><span data-page="menuName"><?= $page->menuName ?></span></p>
                                    <p><i class="far fa-file-alt"></i><strong><?= trans('Nom de la page'); ?></strong><span data-page="name"><?= $page->name ?></span></p>
                                    <p><i class="fas fa-link"></i><strong><?= trans('Slug'); ?></strong><span data-page="slug"><?= $page->slug ?></span></p>
                                    <p><i class="fas fa-quote-right"></i><strong><?= trans('Description'); ?></strong><span data-page="description"><?= $page->description ?></span></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="col-12 col-md-8 col-lg-9">
            <div id="admin-tab-content"></div>
        </div>
    </div>
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
                        <input type="hidden" name="id" value="">
                        <div class="row my-2">
                            <div class="col-12 my-2">
                                <?= Form::text('Nom', 'name', 'text', '', true, 70, 'data-seo="title"'); ?>
                            </div>
                            <div class="col-12 my-2">
                                <?= Form::textarea('Description', 'description', '', 2, true, 'maxlength="158" data-seo="description"'); ?>
                            </div>
                            <div class="col-12 my-2">
                                <?= Form::text('Nom du menu', 'menuName', 'text', '', true, 70); ?>
                            </div>
                            <div class="col-12 my-2">
                                <?= Form::text('Nom du lien URL' . ' (slug)', 'slug', 'text', '', true, 70, 'data-seo="slug"'); ?>
                            </div>
                            <?php if (isTechnicien(getUserRoleId())): ?>
                                <hr class="hrStyle">
                                <div class="col-12 my-2">
                                    <?= Form::select('Fichier', 'filename', array_combine($files, $files), '', true); ?>
                                </div>
                                <div class="col-12 my-2">
                                    <?= Form::select('Type de page', 'type', array_combine(CMS_TYPES, CMS_TYPES), '', true); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <?= Form::target('UPDATEPAGE'); ?>
                        <button type="submit" name="UPDATEPAGESUBMIT"
                                class="btn btn-outline-info"><?= trans('Enregistrer'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php require('footer.php'); ?>