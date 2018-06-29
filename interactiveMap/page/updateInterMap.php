<?php require('header.php');
if (!empty($_GET['id'])): ?>
    <?php
    $InteractiveMap = new App\Plugin\InteractiveMap\InteractiveMap();
    $InteractiveMap->setId($_GET['id']);
    if ($InteractiveMap->show()) : ?>
        <?php
        require(INTERACTIVE_MAP_PATH . 'process/postProcess.php');
        interMap_writeMapFile($InteractiveMap->getData(), $InteractiveMap->getTitle());
        ?>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <a href="<?= getPluginUrl('interactiveMap/page/updateInterMapContent/', $InteractiveMap->getId()) ?>"
                       class="btn btn-info btn-sm float-right">
                        <i class="fas fa-eye"></i> <?= trans('Consulter la carte'); ?>
                    </a>
                    <button class="btn btn-warning btn-sm float-right" id="rebootInterMap">
                        <i class="fas fa-redo"></i> <?= trans('Réinitialiser la carte'); ?>
                    </button>
                    <h1 class="bigTitle"><?= trans('Mise à jour de l\'article'); ?></h1>
                    <hr class="my-4">
                </div>
            </div>
            <?php if (isset($Response)): ?>
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-<?= $Response->display()->status ?>" role="alert">
                            <?= $Response->display()->error_msg; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <form action="" method="post" id="updatePageForm">
                <?= getTokenField(); ?>
                <input type="hidden" name="id" value="<?= $InteractiveMap->getId(); ?>">
                <div class="row">
                    <div class="col-12">
                        <?= App\Form::text(trans('Titre'), 'title', 'text', $InteractiveMap->getTitle(), true); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-6">
                        <?= App\Form::text(trans('Largeur'), 'width', 'text', $InteractiveMap->getWidth(), true); ?>
                    </div>
                    <div class="col-12 col-md-6">
                        <?= App\Form::text(trans('Hauteur'), 'height', 'text', $InteractiveMap->getHeight(), true); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 my-2">
                        <?= App\Form::radio(trans('Statut de la carte'), 'status', array_map('trans', INTERACTIVE_MAP_STATUS), $InteractiveMap->getStatus(), true); ?>
                    </div>
                </div>
                <div class="my-2"></div>
                <div class="row">
                    <div class="col-12">
                        <button type="submit" name="UPDATEINTERACTIVECARTE"
                                class="btn btn-outline-primary btn-block btn-lg">
                            <?= trans('Enregistrer'); ?>
                        </button>
                    </div>
                </div>
            </form>
            <hr class="my-5 hrStyle">
            <div class="row">
                <div class="col-12 accordion w-100 my-2" id="accordion">
                    <?php $mapContent = json_decode($InteractiveMap->getData(), true); ?>

                    <div class="card">
                        <div class="card-header" id="headingCat">
                            <h5 class="mb-0">
                                <button class="btn btn-link" type="button" data-toggle="collapse"
                                        data-target="#interMapCategories" aria-expanded="true"
                                        aria-controls="collapseOne">
                                    <?= trans('Catégories'); ?>
                                </button>
                            </h5>
                        </div>
                        <div id="interMapCategories" class="collapse" aria-labelledby="headingCat"
                             data-parent="#accordion">
                            <div class="card-body">

                                <?php if (!empty($mapContent['categories'])): ?>
                                    <?php foreach ($mapContent['categories'] as $key => $content): ?>
                                        <div class="accordion" id="accordionCategories<?= $key; ?>">
                                            <div class="card">
                                                <div class="card-header" id="headingCat<?= $key; ?>">
                                                    <h5 class="mb-0">
                                                        <button class="btn btn-link" type="button"
                                                                data-toggle="collapse"
                                                                data-target="#interMapCategories<?= $key; ?>"
                                                                aria-expanded="true"
                                                                aria-controls="collapseOne">
                                                            <?= $content['title']; ?>
                                                        </button>
                                                        <button class="button close float-right deleteInterMapArr"
                                                                data-parentjson="categories"
                                                                data-id="<?= $content['id']; ?>">
                                                            &times;
                                                        </button>
                                                    </h5>
                                                </div>
                                                <div id="interMapCategories<?= $key; ?>" class="collapse"
                                                     aria-labelledby="headingCat<?= $key; ?>"
                                                     data-parent="#interMapCategories">
                                                    <div class="card-body row" data-parentjson="categories"
                                                         data-id="<?= $content['id']; ?>">
                                                        <div class="col-12 col-lg-3">
                                                            <?= App\Form::text('ID', 'id', 'text', $content['id'], true, 300, 'disabled'); ?>
                                                        </div>
                                                        <div class="col-12 col-lg-3">
                                                            <?= App\Form::text(trans('Titre'), 'title', 'text', $content['title'], true, 300, '', '', 'updateInterMap'); ?>
                                                        </div>
                                                        <div class="col-12 col-lg-3">
                                                            <?= App\Form::text(trans('Couleur'), 'color', 'text', $content['color'], true, 300, '', '', 'updateInterMap'); ?>
                                                        </div>
                                                        <div class="col-12 col-lg-3">
                                                            <?= App\Form::text(trans('Développement la catégorie'), 'show', 'text', $content['show'], true, 300, '', '', 'updateInterMap'); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                <button class="btn btn-block btn-primary"
                                        data-toggle="modal" type="button"
                                        data-target="#modalAddInterMapCategorie">
                                    <?= trans('Nouvelle catégorie'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header" id="headingLev">
                            <h5 class="mb-0">
                                <button class="btn btn-link" type="button" data-toggle="collapse"
                                        data-target="#interMapLevels" aria-expanded="true"
                                        aria-controls="collapseOne">
                                    <?= trans('Niveaux'); ?>
                                </button>
                            </h5>
                        </div>
                        <div id="interMapLevels" class="collapse" aria-labelledby="headingLev"
                             data-parent="#accordion">
                            <div class="card-body">
                                <?php if (!empty($mapContent['levels'])): ?>
                                    <?php foreach ($mapContent['levels'] as $key => $content): ?>
                                        <div class="accordion" id="accordionLevels<?= $key ?>">
                                            <div class="card">
                                                <div class="card-header" id="headingLev<?= $key ?>">
                                                    <h5 class="mb-0">
                                                        <button class="btn btn-link" type="button"
                                                                data-toggle="collapse"
                                                                data-target="#interMapLevel<?= $key ?>"
                                                                aria-expanded="true"
                                                                aria-controls="collapseOne">
                                                            <?= $content['title']; ?>
                                                        </button>
                                                        <button class="button close float-right deleteInterMapArr"
                                                                data-parentjson="levels"
                                                                data-id="<?= $content['id']; ?>">
                                                            &times;
                                                        </button>
                                                    </h5>
                                                </div>
                                                <div id="interMapLevel<?= $key ?>" class="collapse"
                                                     aria-labelledby="headingLev<?= $key ?>"
                                                     data-parent="#interMapLevels">
                                                    <div class="card-body row" data-parentjson="levels"
                                                         data-id="<?= $content['id']; ?>">
                                                        <div class="col-12 col-lg-3">
                                                            <?= App\Form::text('ID', 'id', 'text', $content['id'], true, 300, 'disabled'); ?>
                                                        </div>
                                                        <div class="col-12 col-lg-3">
                                                            <?= App\Form::text(trans('Titre'), 'title', 'text', $content['title'], true, 300, '', '', 'updateInterMap'); ?>
                                                        </div>
                                                        <?php if (!empty($content['map'])): ?>
                                                            <div class="col-12 col-lg-3">
                                                                <img src="<?= $content['map']; ?>">
                                                            </div>
                                                        <?php endif; ?>
                                                        <?php if (!empty($content['minimap'])): ?>
                                                            <div class="col-12 col-lg-3">
                                                                <img src="<?= $content['minimap']; ?>">
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                <button class="btn btn-block btn-primary"
                                        data-toggle="modal" type="button"
                                        data-target="#modalAddInterMapLevel">
                                    <?= trans('Nouveau niveau'); ?></button>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header" id="headingOptions">
                            <h5 class="mb-0">
                                <button class="btn btn-link" type="button" data-toggle="collapse"
                                        data-target="#interMapOptions" aria-expanded="true"
                                        aria-controls="collapseOne">
                                    <?= trans('Options'); ?>
                                </button>
                            </h5>
                        </div>
                        <div id="interMapOptions" class="collapse" aria-labelledby="headingOptions"
                             data-parent="#accordion">
                            <div class="card-body">
                                <?php
                                $mapOptions = json_decode($InteractiveMap->getOptions(), true);
                                $optionsChoised = array();

                                if(isset($mapOptions['checkbox'])) {
                                    foreach ($mapOptions['checkbox'] as $key => $value) {
                                        $optionsChoised[$value] = $value;
                                    }
                                }

                                ?>
                                <form action="" method="post" id="addInterMapOptionsForm">
                                    <?= getTokenField(); ?>
                                    <input type="hidden" name="idMap" value="<?= $InteractiveMap->getId(); ?>">
                                    <div class="row">
                                        <div class="col-12 my-2">
                                            <?= App\Form::checkbox(trans('Options'), 'options', MAP_JS_OPTIONS, $optionsChoised, 'custom-control-inline'); ?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-lg-4 my-2">
                                            <?= App\Form::select(trans('Action lors du clic'), 'action', MAP_JS_ACTIONS, !empty($mapOptions['action']) ? $mapOptions['action'] : 'tooltip', true); ?>
                                        </div>
                                        <div class="col-12 col-lg-4 my-2">
                                            <?= App\Form::text(trans('Zoom autorisé'), 'maxscale', 'number', $mapOptions['maxscale'], true, 1); ?>
                                        </div>
                                        <div class="col-12 col-lg-4 my-2">
                                            <?= App\Form::text(trans('Couleur de remplissage (hex)'), 'mapfill', 'text', $mapOptions['mapfill'], false, 7); ?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 my-2">
                                            <button class="btn btn-block btn-primary" type="submit" name="ADDOPTIONS">
                                                <?= trans('Enregistrer'); ?></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="my-4"></div>
        </div>
        <div class="modal fade" id="modalAddInterMapCategorie" tabindex="-1" role="dialog"
             aria-labelledby="modalAddInterMapCategorieTitle"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="" method="post" id="addInterMapCategorieForm">
                        <div class="modal-header">
                            <h5 class="modal-title"
                                id="modalAddInterMapCategorieTitle"><?= trans('Ajouter une nouvelle catégorie'); ?></h5>
                        </div>
                        <div class="modal-body" id="modalAddInterMapCategorieBody">
                            <?= getTokenField(); ?>
                            <div class="row">
                                <div class="col-12 my-2">
                                    <?= App\Form::text(trans('Titre'), 'title', 'text', !empty($_POST['title']) ? $_POST['title'] : '', true, 300); ?>
                                </div>
                                <div class="col-12 my-2">
                                    <?= App\Form::text('ID', 'id', 'text', !empty($_POST['id']) ? $_POST['id'] : '', true, 150); ?>
                                </div>
                                <div class="col-12 my-2">
                                    <?= App\Form::text(trans('Couleur'), 'color', 'text', !empty($_POST['color']) ? $_POST['color'] : '', true, 300); ?>
                                </div>
                                <div class="col-12 my-2">
                                    <?= App\Form::radio(trans('Développement la catégorie'), 'show', array('false' => 'Non', 'true' => 'Oui'), 'false', true, 'custom-control-inline'); ?>
                                </div>
                            </div>
                            <div id="addInterMapCategorieError"></div>
                        </div>
                        <div class="modal-footer" id="modalAddInterMapCategorieFooter">
                            <button type="submit" id="addInterMapCatBtn" name="ADDINTERMAPCATEGORY"
                                    class="btn btn-primary"><?= trans('Enregistrer'); ?></button>
                            <button type="button" class="btn btn-secondary"
                                    data-dismiss="modal"><?= trans('Fermer'); ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modalAddInterMapLevel" tabindex="-1" role="dialog"
             aria-labelledby="modalAddInterMapLevelTitle"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="" method="post" id="addInterMapLevelForm" enctype="multipart/form-data">
                        <div class="modal-header">
                            <h5 class="modal-title"
                                id="modalAddInterMapLevelTitle"><?= trans('Ajouter un nouveaux niveau'); ?></h5>
                        </div>
                        <div class="modal-body" id="modalAddInterMapLevelBody">
                            <?= getTokenField(); ?>
                            <input type="hidden" name="idMap" value="<?= $InteractiveMap->getId(); ?>">
                            <div class="row">
                                <div class="col-12 my-2">
                                    <?= App\Form::text(trans('Titre'), 'title', 'text', !empty($_POST['title']) ? $_POST['title'] : '', true, 300); ?>
                                </div>
                                <div class="col-12 my-2">
                                    <?= App\Form::text('ID', 'id', 'text', !empty($_POST['id']) ? $_POST['id'] : '', true, 150); ?>
                                </div>
                                <div class="col-12 my-2">
                                    <?= App\Form::text(trans('Map (SVG,JPG)'), 'map[]', 'file'); ?>
                                </div>
                                <div class="col-12 my-2">
                                    <?= App\Form::text(trans('Mini Map (JPG)'), 'minimap[]', 'file'); ?>
                                </div>
                            </div>
                            <div id="addInterMapLevelError"></div>
                        </div>
                        <div class="modal-footer" id="modalAddInterMapLevelFooter">
                            <button type="submit" name="ADDINTERMAPLEVEL"
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

                var idMap = '<?= $InteractiveMap->getId(); ?>';

                $('button[name="ADDINTERMAPLEVEL"]').click(function () {
                    $('#loader').fadeIn('fast');
                });

                $('button#rebootInterMap').on('click', function (event) {
                    event.preventDefault();
                    if (confirm('<?= trans('Vous allez réinitialiser la carte'); ?>')) {
                        $('#loader').fadeIn('fast');
                        $.post(
                            '<?= INTERACTIVE_MAP_URL; ?>process/ajaxProcess.php',
                            {
                                rebootInterMap: 'OK',
                                idMap: idMap
                            }, function (data) {
                                if (data) {
                                    location.reload(true);
                                }
                            }
                        )
                    }
                });

                $('form#addInterMapCategorieForm').submit(function (event) {
                    event.preventDefault();
                    event.stopPropagation();

                    $('#addInterMapCategorieError').html('');
                    var id = $('form#addInterMapCategorieForm input#id').val();
                    var title = $('form#addInterMapCategorieForm input#title').val();
                    var color = $('form#addInterMapCategorieForm input#color').val();
                    var show = $('form#addInterMapCategorieForm input[name="show"]').val();
                    if (title.length && id.length) {
                        $.post(
                            '<?= INTERACTIVE_MAP_URL; ?>process/ajaxProcess.php',
                            {
                                addMapCategory: 'OK',
                                idMap: idMap,
                                id: id,
                                title: title,
                                color: color,
                                show: show
                            }, function (data) {
                                if (data) {
                                    $('#addInterMapCategorieError').html(data);
                                    $('#addInterMapCatBtn').removeClass('disabled').html('<?= trans('Enregistrer'); ?>');
                                }
                            }
                        )
                    }
                    return false;
                });

                $('.updateInterMap').bind("keyup change", function (event) {
                    event.preventDefault();
                    event.stopPropagation();

                    var $input = $(this);
                    var $inputParent = $input.parent('div').parent('div').parent('div');
                    var parentJson = $inputParent.data('parentjson');
                    var id = $inputParent.data('id');
                    var name = $input.attr('name');
                    var value = $input.val();

                    if (name.length && parentJson.length) {
                        $inputParent.stop(true, true);
                        $.post(
                            '<?= INTERACTIVE_MAP_URL; ?>process/ajaxProcess.php',
                            {
                                updateInterMap: 'OK',
                                idMap: idMap,
                                parent: parentJson,
                                id: id,
                                name: name,
                                value: value
                            }, function (data) {
                                if (data && (data == 'true' || data === true)) {
                                    $inputParent.effect('highlight');
                                } else {
                                    $inputParent.effect('shake');
                                }
                            }
                        )
                    }
                });

                $('.deleteInterMapArr').on('click', function (event) {
                    event.preventDefault();

                    if (confirm('<?= trans('Cette action est irreversible !'); ?>')) {
                        var $btn = $(this);
                        var $container = $btn.parent().parent().parent().parent('div.accordion');

                        var parentJson = $(this).data('parentjson');
                        var id = $(this).data('id');

                        if (id.length && parentJson.length) {
                            $.post(
                                '<?= INTERACTIVE_MAP_URL; ?>process/ajaxProcess.php',
                                {
                                    deleteInterMapArr: 'OK',
                                    idMap: idMap,
                                    parent: parentJson,
                                    id: id
                                }, function (data) {
                                    if (data && (data == 'true' || data === true)) {
                                        $container.slideUp();
                                    } else {
                                        $container.effect('shake');
                                    }
                                }
                            )
                        }
                    }
                });

                $('#modalAddInterMapCategorie input#title').keyup(function () {
                    $('#modalAddInterMapCategorie input#id').val(convertToSlug($(this).val()));
                });
                $('#modalAddInterMapCategorie input#id').keyup(function () {
                    $(this).val(convertToSlug($(this).val()));
                });

                $('#modalAddInterMapLevel input#title').keyup(function () {
                    $('#modalAddInterMapLevel input#id').val(convertToSlug($(this).val()));
                });
                $('#modalAddInterMapLevel input#id').keyup(function () {
                    $(this).val(convertToSlug($(this).val()));
                });
            });
        </script>
    <?php else: ?>
        <?= getContainerErrorMsg(trans('Cette page n\'existe pas')); ?>
    <?php endif; ?>
<?php else: ?>
    <?= trans('Cette page n\'existe pas'); ?>
<?php endif; ?>
<?php require('footer.php'); ?>
