<?php require('header.php');

use App\Plugin\Cms\Cms;

$Cms = new Cms();
$Cms->setStatut(0);
$Cms->setLang(APP_LANG);
$allPages = extractFromObjArr($Cms->showAll(), 'id');

echo getTitle(getAppPageName(), getAppPageSlug()); ?>
    <div class="row">
        <div class="col-12">
            <div class="table-responsive">
                <table id="pagesTable"
                       class="sortableTable table table-striped">
                    <thead>
                    <tr>
                        <th><?= trans('ID'); ?></th>
                        <th><?= trans('Type'); ?></th>
                        <th><?= trans('Fichier'); ?></th>
                        <th><?= trans('Nom du menu'); ?></th>
                        <th><?= trans('Nom de la page'); ?></th>
                        <th><?= trans('Slug'); ?></th>
                        <th><?= trans('Modifié le'); ?></th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if ($allPages): ?>
                        <?php foreach ($allPages as $page): ?>
                            <tr data-idcms="<?= $page->id ?>">
                                <td><?= $page->id ?></td>
                                <td><?= $page->type ?></td>
                                <td><?= $page->filename ?></td>
                                <td><?= $page->menuName ?></td>
                                <td><?= $page->name ?></td>
                                <td><?= $page->slug ?></td>
                                <td><?= displayTimeStamp($page->updated_at) ?></td>
                                <td>
                                    <a href="<?= getPluginUrl('cms/page/pageContent/', $page->id) ?>"
                                       class="btn btn-sm" title="<?= trans('Consulter'); ?>">
                                        <span class="btnUpdate"><i class="fas fa-cog"></i></span>
                                    </a>
                                    <?php if (isTechnicien(getUserRoleId())): ?>
                                        <button type="button" data-deletecmsid="<?= $page->id ?>"
                                                class="btn btn-sm deleteCms"
                                                title="<?= trans('Supprimer'); ?>">
                                            <span class="btnArchive"><i class="fas fa-times"></i></span>
                                        </button>
                                        <button type="button" class="btn btn-sm unpackPage"
                                                title="<?= trans('désarchiver'); ?>"
                                                data-idpage="<?= $page->id ?>">
                                            <span class="btnCheck"> <i class="fas fa-check"></i></span>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php require('footer.php'); ?>