<?php
require('header.php');

use App\Plugin\Cms\Cms;
use App\Plugin\Cms\CmsContent;
use App\Plugin\Cms\CmsTemplate;

if (!empty($_GET['id'])):

    $Cms = new Cms();
    $Cms->setId($_GET['id']);
    $Cms->setLang(APP_LANG);

    if ($Cms->show()):

        // get page content
        $CmsContent = new CmsContent($Cms->getId(), APP_LANG);

        //get all pages for navigations
        $allCmsPages = $Cms->showAll();

        echo getTitle(trans('Contenu de la page') . '<strong> ' . $Cms->getName() . '</strong>', getAppPageSlug());
        showPostResponse(); ?>
        <div class="row mb-2" id="cmsPageId" data-cms-id="<?= $Cms->getId(); ?>">
            <div class="col-12">
                <div class="row">
                    <div class="col-12 col-lg-8 my-2">
                        <?php if ($Cms->getType() === 'PAGE'): ?>
                            <a href="<?= webUrl($Cms->getSlug() . '/'); ?>"
                               class="btn btn-outline-info btn-sm" id="takeLookToPage" target="_blank">
                                <i class="fas fa-external-link-alt"></i> <?= trans('Visualiser la page'); ?>
                            </a>
                            <button class="btn btn-sm btn-outline-danger"
                                    data-page-lang="<?= APP_LANG; ?>" data-page-slug="<?= $Cms->getSlug(); ?>"
                                    id="clearPageCache"><i class="fas fa-eraser"></i> Vider le cache
                            </button>
                            <?php if (isTechnicien(getUserRoleId())): ?>
                                <button class="btn btn-sm btn-outline-dark"
                                        data-page-lang="<?= APP_LANG; ?>" data-page-id="<?= $Cms->getId(); ?>"
                                        id="fillLorem"><i class="fas fa-paint-roller"></i> Préremplir la page
                                </button>
                            <?php endif;
                        endif; ?>
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
        <div class="row mb-2">
            <div class="col-12 col-lg-10">
                <form action="" method="post" id="pageContentManageForm" style="display:none;">
                    <?php
                    $Template = new CmsTemplate(WEB_PATH . $Cms->getFilename() . '.php', $CmsContent->getData());
                    $Template->show(); ?>
                </form>
            </div>
            <div class="d-none d-lg-block col-2 positionRelative">
                <nav id="headerLinks" class="list-group list-group-flush"></nav>
            </div>
        </div>
    <?php else: ?>
        <p><?= trans('Model manquant'); ?></p>
    <?php endif; ?>


    <?php else:
        echo getContainerErrorMsg(trans('Cette page n\'existe pas'));
    endif;
else:
    echo trans('Cette page n\'existe pas');
endif;
require('footer.php'); ?>