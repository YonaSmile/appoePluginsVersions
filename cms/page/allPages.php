<?php require('header.php');

use App\Plugin\Cms\Cms;

$Cms = new Cms();
$Cms->setLang(APP_LANG);
$allPages = $Cms->showAll();

echo getTitle(getAppPageName(), getAppPageSlug()); ?>
    <div class="row">
        <div class="col-12 col-md-4 col-lg-3">
            <?php if ($allPages): ?>
                <div class="input-group mb-1" id="tab-search">
                    <div class="input-group-prepend">
                        <div class="input-group-text"><i class="fas fa-search"></i></div>
                    </div>
                    <input type="search" class="form-control" id="tab-search-input" placeholder="Rechercher...">
                </div>
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
                                <div class="p-0 px-lg-4">
                                    <p><strong><?= trans('ID'); ?></strong><?= $page->id ?></p>
                                    <p><strong><?= trans('Type'); ?></strong><?= $page->type ?></p>
                                    <p>
                                        <strong><?= trans('Modifié le'); ?></strong><?= displayTimeStamp($page->updated_at) ?>
                                    </p>
                                    <p><strong><?= trans('Fichier'); ?></strong><?= $page->filename ?></p>
                                    <p><strong><?= trans('Nom du menu'); ?></strong><?= $page->menuName ?></p>
                                    <p><strong><?= trans('Nom de la page'); ?></strong><?= $page->name ?></p>
                                    <p><strong><?= trans('Slug'); ?></strong><?= $page->slug ?></p>
                                    <p><strong><?= trans('Description'); ?></strong><?= $page->description ?></p>
                                </div>
                                <div class="d-flex flex-row justify-content-between btn-group p-0 p-lg-4">
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