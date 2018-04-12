<?php
require('header.php');

require(CMS_PATH . 'process/ajaxProcess.php');
require(CMS_PATH . 'process/postProcess.php');

$Cms = new App\Plugin\Cms\Cms();
$CmsMenu = new App\Plugin\Cms\CmsMenu();

$allCmsMenu = $CmsMenu->showAll();

$MENUS = constructMenu($allCmsMenu);

$allPages = extractFromObjToSimpleArr($Cms->showAllPages(), 'id', 'name');
$allPages[10] = trans('Aucun parent');
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="display-4 bigTitle"><?= trans('Menu'); ?></h1>
        </div>
    </div>
    <hr class="my-2">
    <button id="addMenuPage" type="button" class="btn btn-primary mb-4" data-toggle="modal"
            data-target="#modalAddMenuPage">
        <?= trans('Nouvelle page au menu'); ?>
    </button>
    <?php if (isset($Response)): ?>
        <div class="row my-2">
            <div class="col-12">
                <div class="alert alert-<?= $Response->display()->status ?>" role="alert">
                    <?= $Response->display()->error_msg; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <div class="row my-3">
        <?php foreach (CMS_LOCATIONS as $key => $value): ?>
            <div class="col-12 col-lg-6">
                <div class="row">
                    <div class="col-12">
                        <h5><?= $value; ?></h5>
                    </div>
                </div>
                <div class="row mb-4" id="menuAdminUpdate">
                    <div class="col-12">
                        <?php if (isset($MENUS[$key][10])): ?>

                            <?php foreach ($MENUS[$key][10] as $menu): ?>
                                <?php if ($menu->location == $key): ?>
                                    <div data-menuid="<?= $menu->id; ?>"
                                         class="m-0 mt-3 py-0 px-3 jumbotron bg-warning text-white fileContent">
                                        <input type="tel" class="updateMenuData positionMenuSpan"
                                               data-menuid="<?= $menu->id; ?>" data-column="position"
                                               value="<?= $menu->position; ?>">
                                        <input type="text" data-menuid="<?= $menu->id; ?>" class="updateMenuData"
                                               data-column="name" value="<?= $menu->name; ?>">
                                        <small class="inputInfo"></small>
                                        <?php if (empty($MENUS[$key][$menu->id])): ?>
                                            <button type="button" class="close deleteMenu">
                                                <span class="fas fa-times"></span>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                    <?php if (!empty($MENUS[$key][$menu->id])): ?>
                                        <?php foreach ($MENUS[$key][$menu->id] as $subMenu): ?>
                                            <div class="px-3 py-0 m-0 ml-4 mt-1 jumbotron fileContent"
                                                 data-menuid="<?= $subMenu->id; ?>">
                                                <input type="tel" class="updateMenuData positionMenuSpan"
                                                       data-menuid="<?= $subMenu->id; ?>" data-column="position"
                                                       value="<?= $subMenu->position; ?>">
                                                <input type="text" data-menuid="<?= $subMenu->id; ?>"
                                                       class="updateMenuData" data-column="name"
                                                       value="<?= $subMenu->name; ?>">
                                                <small class="inputInfo"></small>
                                                <?php if (empty($MENUS[$key][$subMenu->id])): ?>
                                                    <button type="button" class="close deleteMenu">
                                                        <span class="fas fa-times"></span>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                            <?php if (!empty($MENUS[$key][$subMenu->id])): ?>
                                                <?php foreach ($MENUS[$key][$subMenu->id] as $subSubMenu): ?>
                                                    <div class="px-3 py-0 m-0 ml-5 mt-1 jumbotron fileContent"
                                                         data-menuid="<?= $subSubMenu->id; ?>">
                                                        <input type="tel" class="updateMenuData positionMenuSpan"
                                                               data-menuid="<?= $subSubMenu->id; ?>"
                                                               data-column="position"
                                                               value="<?= $subSubMenu->position; ?>">
                                                        <input type="text" data-menuid="<?= $subSubMenu->id; ?>"
                                                               class="updateMenuData" data-column="name"
                                                               value="<?= $subSubMenu->name; ?>">
                                                        <small class="inputInfo"></small>
                                                        <?php if (empty($MENUS[$key][$subSubMenu->id])): ?>
                                                            <button type="button" class="close deleteMenu">
                                                                <span class="fas fa-times"></span>
                                                            </button>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<div class="modal fade" id="modalAddMenuPage" tabindex="-1" role="dialog" aria-labelledby="modalAddMenuPageTitle"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="" method="post" id="addMenuPageForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalAddMenuPageTitle"><?= trans('Ajouter une page au menu'); ?></h5>
                </div>
                <div class="modal-body" id="modalAddMenuPageBody">
                    <?= getTokenField(); ?>
                    <div class="row">
                        <div class="col-12 my-2">
                            <?= App\Form::select(trans('Page'), 'idCms', $allPages, !empty($_POST['idCms']) ? $_POST['idCms'] : '', true); ?>
                        </div>
                        <div class="col-12 my-2">
                            <?= App\Form::text(trans('Nom'), 'name', 'text', !empty($_POST['name']) ? $_POST['name'] : '', true, 70); ?>
                        </div>
                        <div class="col-12 my-2">
                            <?= App\Form::text(trans('Position / Ordre'), 'position', 'tel', !empty($_POST['position']) ? $_POST['position'] : '', false); ?>
                        </div>
                        <div class="col-12 my-2">
                            <?= App\Form::select(trans('Emplacement'), 'location', CMS_LOCATIONS, !empty($_POST['location']) ? $_POST['location'] : '', true); ?>
                        </div>
                        <div class="col-12 my-2" id="parentPageForm"></div>
                    </div>
                </div>
                <div class="modal-footer" id="modalAddMenuPageFooter">
                    <button type="submit" name="ADDMENUPAGE"
                            class="btn btn-primary"><?= trans('Enregistrer'); ?></button>
                    <button type="button" class="btn btn-secondary"
                            data-dismiss="modal"><?= trans('Fermer'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {

        $('select#location').on('change', function () {
            var location = $(this).val();
            var $parentPageInput = $('#parentPageForm');
            $parentPageInput.html('<i class="fas fa-circle-notch fa-spin"></i> <?= trans('Chargement'); ?>');
            $.post(
                '<?= CMS_URL . 'process/ajaxProcess.php'; ?>',
                {
                    getParentPageByLocation: location
                },
                function (data) {
                    if (data) {
                        $parentPageInput.html(data);
                    }
                }
            )
        });

        $('select#idCms').change(function () {
            $('input#name').val(($('option:selected', this).text()));
        });

        $('.updateMenuData').on('keyup blur', function (event) {
            event.preventDefault();

            var $input = $(this);
            var column = $input.data('column');
            var idMenu = $input.data('menuid');
            var value = $input.val();
            var $inputInfo = $input.parent('div').children('small.inputInfo');
            $inputInfo.html('');

            $.post(
                '<?= CMS_URL . 'process/ajaxProcess.php'; ?>',
                {
                    updateMenu: 'OK',
                    column: column,
                    idMenu: idMenu,
                    value: value
                },
                function (data) {
                    if (data === true || data == 'true') {
                        $inputInfo.html('<?= trans('Enregistré'); ?>')
                    }
                }
            );

        });

        $('.deleteMenu').on('click', function () {
            var div = $(this).parent('div');
            var menuId = div.data('menuid');
            if (confirm('<?= trans('Vous allez supprimer ce menu'); ?>')) {
                $.post(
                    '<?= CMS_URL . 'process/ajaxProcess.php'; ?>',
                    {
                        idCmsMenuDelete: menuId
                    },
                    function (data) {
                        if (data === true || data == 'true') {
                            div.slideUp();
                        }
                    }
                );
            }
        });
    });
</script>
<?php require('footer.php'); ?>
