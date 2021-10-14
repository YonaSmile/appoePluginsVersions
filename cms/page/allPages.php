<?php require('header.php');

use App\Plugin\Cms\Cms;

$Cms = new Cms();
$Cms->setLang(APP_LANG);
$allPages = $Cms->showAll();

echo getTitle(getAppPageName(), getAppPageSlug()); ?>
    <div class="row">
        <div class="col-12 col-md-4 col-lg-3">
            <?php if ($allPages): ?>
                <input type="search" class="form-control" id="tab-search" placeholder="Rechercher...">
                <div id="tabs">
                    <?php foreach ($allPages as $page): ?>
                        <div class="tab" data-idcms="<?= $page->id ?>"
                             data-filter="<?= $page->type ?> <?= $page->filename ?> <?= $page->menuName ?> <?= $page->name ?> <?= $page->slug ?>">
                            <div class="tab-header">
                                <h5><?= $page->menuName ?></h5>
                                <small><?= $page->id ?></small>
                            </div>
                            <div class="tab-content">
                                <div class="d-none d-lg-block p-lg-4">
                                    <h2><?= $page->name ?></h2>
                                </div>
                                <div class="d-flex flex-row justify-content-between p-2 p-lg-4">
                                    <p><strong><?= trans('ID'); ?></strong><br><?= $page->id ?></p>
                                    <p><strong><?= trans('Type'); ?></strong><br><?= $page->type ?></p>
                                    <p>
                                        <strong><?= trans('Modifié le'); ?></strong><br><?= displayTimeStamp($page->updated_at) ?>
                                    </p>
                                    <p><strong><?= trans('Fichier'); ?></strong><br><?= $page->filename ?></p>
                                </div>
                                <div class="d-flex flex-row justify-content-between p-2 p-lg-4">
                                    <p><strong><?= trans('Nom du menu'); ?></strong><br><?= $page->menuName ?></p>
                                    <p><strong><?= trans('Nom de la page'); ?></strong><br><?= $page->name ?></p>
                                    <p><strong><?= trans('Slug'); ?></strong><br><?= $page->slug ?></p>
                                </div>
                                <p class="p-2 p-lg-4"><strong><?= trans('Description'); ?></strong>
                                    <br><?= $page->description ?>
                                </p>
                                <div class="d-flex flex-row justify-content-between btn-group">
                                    <a href="<?= getPluginUrl('cms/page/pageContent/', $page->id) ?>" class="btn">
                                        <i class="far fa-eye colorPrimary"></i> <span> <?= trans('Consulter'); ?></span>
                                    </a>
                                    <button type="button" class="btn deleteCms" data-idcms="<?= $page->id ?>">
                                        <i class="fas fa-archive text-danger"></i>
                                        <span><?= trans('Archiver'); ?></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="col-12 col-md-8 col-lg-9">
            <div id="tab-content"></div>
        </div>
    </div>
<?php require('footer.php'); ?>