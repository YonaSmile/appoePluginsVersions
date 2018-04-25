<?php
require('header.php');
if (!empty($_GET['id'])): ?>
    <?php
    require(INTERACTIVE_MAP_PATH . 'process/postProcess.php');
    $InteractiveMap = new App\Plugin\InteractiveMap\InteractiveMap();
    $InteractiveMap->setId($_GET['id']);
    if ($InteractiveMap->show()) : ?>
        <?php interMap_writeMapFile($InteractiveMap->getData(), $InteractiveMap->getTitle()); ?>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1 class="bigTitle"><?= $InteractiveMap->getTitle(); ?></h1>
                    <hr class="mb-2">
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <a href="<?= getPluginUrl('interactiveMap/page/updateInterMap/', $InteractiveMap->getId()) ?>"
                       class="btn btn-warning btn-sm">
                        <span class="fas fa-cog"></span> <?= trans('Modifier la carte'); ?>
                    </a>
                </div>
            </div>
            <div class="my-2"></div>
            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered bg-white">
                            <tr class="table-info-light">
                                <th><?= trans('Largeur'); ?></th>
                                <th><?= trans('Hauteur'); ?></th>
                                <th><?= trans('Statut de la carte'); ?></th>
                            </tr>
                            <tr>
                                <td><?= $InteractiveMap->getWidth(); ?></td>
                                <td><?= $InteractiveMap->getHeight(); ?></td>
                                <td><?= INTERACTIVE_MAP_STATUS[$InteractiveMap->getStatus()] ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="my-2"></div>
            <?php if (isset($Response)): ?>
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-<?= $Response->display()->status ?>" role="alert">
                            <?= $Response->display()->error_msg; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <div class="my-1"></div>
            <div class="row">
                <div class="col-12 col-lg-8">
                    <div class="col-12 mb-3">
                        <h5 class="strong py-2 border-bottom text-uppercase text-vert">
                            <?= trans('La carte'); ?>
                        </h5>
                    </div>
                    <div id="mapplic"></div>
                    <div class="custom-control custom-checkbox mt-4 mb-2">
                        <input type="checkbox" class="custom-control-input" id="addPointsChecker">
                        <label class="custom-control-label" for="addPointsChecker">
                            <?= trans('Insérer des emplacements à chaque click sur la carte'); ?>
                        </label>
                    </div>
                    <div class="custom-control custom-checkbox my-2">
                        <input type="checkbox" class="custom-control-input" id="addPointsCheckerSameTitle">
                        <label class="custom-control-label" for="addPointsCheckerSameTitle">
                            <?= trans('Définir le titre par le nom de l\'emplacement'); ?>
                        </label>
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <h5 class="strong py-2 border-bottom text-uppercase text-vert">
                                <?= trans('Contenu de la carte'); ?>
                            </h5>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12" id="pointContenair"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="my-4"></div>
        <script type="text/javascript">

            $(document).ready(function () {

                var currentLevel = $('.mapplic-levels option:selected').val();

                var idMap = '<?= $InteractiveMap->getId(); ?>';

                var freeToAdd = true;

                function reloadPointContainer(location) {

                    if (location && !$('#mapplic').hasClass('mapplic-fullscreen')) {

                        var currentLevel = $('.mapplic-levels option:selected').val();
                        $('#pointContenair').html('<i class="fas fa-circle-notch fa-spin"></i>');

                        var src = '<?= INTERACTIVE_MAP_URL . 'allPoints.php?'; ?>';
                        var data = 'id=' + idMap + '&level=' + currentLevel + '&location=' + location;
                        $('#pointContenair').load(src + data);
                    }
                }

                function uniqId() {
                    return Math.round(new Date().getTime() + (Math.random() * 100));
                }

                var mapplic = $('#mapplic').mapplic({
                    source: '<?= INTERACTIVE_MAP_URL . slugify($InteractiveMap->getTitle()); ?>.json',
                    sidebar: true, 			// Enable sidebar
                    minimap: true, 			// Enable minimap
                    markers: true, 		// Disable markers
                    fillcolor: true, 		// Disable default fill color
                    fullscreen: true, 		// Enable fullscreen
                    maxscale: 3, 			// Setting maxscale to 3 times bigger than the original file
                    developer: true,
                    landmark: true
                });

                var self = mapplic.data('mapplic');

                $('#pointContenair').on('click', 'button.refreshInterMapPoint', function (event) {
                    $('#loader').fadeIn('fast');
                    location.reload(true);
                });

                $('#pointContenair').on('click', 'button.deleteInterMapPoint', function (event) {
                    event.preventDefault();
                    var $btn = $(this);
                    var idMap = $btn.data('idmap');
                    var level = $btn.data('level');
                    var locationId = $btn.data('id');

                    if (confirm('<?= trans('Vous allez supprimer ce point de la carte'); ?>')) {
                        $('#pointContenair').html('<i class="fas fa-circle-notch fa-spin"></i>');
                        $.post(
                            '<?= INTERACTIVE_MAP_URL; ?>process/ajaxProcess.php',
                            {
                                deleteMapLocation: 'OK',
                                idMap: idMap,
                                locationId: locationId,
                                level: level
                            }, function (data) {
                                if (data) {
                                    $('a.mapplic-pin[data-location="' + locationId + '"]').remove();
                                    self.hideLocation();
                                    $('#pointContenair').html('');
                                    $('li.mapplic-list-location[data-location="' + locationId + '"]').remove();
                                }
                            }
                        );
                    }
                });

                mapplic.on('locationopened', function (e, location) {
                    //if (!$('#addPointsChecker').is(':checked')) {
                    reloadPointContainer(location.id);

                    //}
                });

                $('#pointContenair').bind('blur change', $('form.locationForm input, form.locationForm textarea, form.locationForm select'), function (e) {
                    var $input = $(e.target);
                    $.post(
                        '<?= INTERACTIVE_MAP_URL; ?>process/ajaxProcess.php',
                        $('form.locationForm').serialize(),
                        function (data) {
                            if (data) {
                                $('form.locationForm input, form.locationForm textarea, form.locationForm select').removeClass('is-valid');
                                $input.addClass('is-valid');
                            }
                        }
                    );
                });

                $(document).on('click', 'a.mapplic-pin, li.mapplic-list-location', function (e) {
                    var locationPointer = $(this);
                    $(".mapplic-levels option").each(function () {
                        if ($(this).val() == locationPointer.data('location')) {
                            e.preventDefault();
                            self.switchLevel(locationPointer.data('location'));
                            self.moveTo(0, 0, 0, 0);
                            return false;
                        }
                    });

                });

                $(document).on('click', '.mapplic-layer', function (e) {

                    if ($('#addPointsChecker').is(':checked')) {

                        if (freeToAdd) {
                            freeToAdd = false;

                            $('#pointContenair').html('<i class="fas fa-circle-notch fa-spin"></i>');

                            var element = $(this).children('.mapplic-map-image').prop("tagName");

                            if (element != 'DIV') {
                                var id = uniqId();
                            } else {
                                var id = e.target.id;
                            }
                            var map = $('.mapplic-map'),
                                x = (e.pageX - map.offset().left) / map.width(),
                                y = (e.pageY - map.offset().top) / map.height();

                            var xPoint = parseFloat(x).toFixed(4);
                            var yPoint = parseFloat(y).toFixed(4);
                            var currentLevel = $('.mapplic-levels option:selected').val();
                            var title = '';
                            if ($('#addPointsCheckerSameTitle').is(':checked')) {
                                title = id;
                            }
                            $.post(
                                '<?= INTERACTIVE_MAP_URL; ?>process/ajaxProcess.php',
                                {
                                    addMapLocation: 'OK',
                                    idMap: idMap,
                                    id: id,
                                    currentLevel: currentLevel,
                                    xPoint: xPoint,
                                    yPoint: yPoint,
                                    title: title
                                }, function (data) {
                                    if (data && (data == 'true' || data === true)) {
                                        reloadPointContainer(id);
                                        var top = yPoint * 100,
                                            left = xPoint * 100;
                                        $('.mapplic-layer a').removeClass('mapplic-active');
                                        $('.mapplic-layer')
                                            .append('<a href="#" class="mapplic-pin default mapplic-active" style="top: ' + top + '%; left: ' + left + '%;" data-location="' + id + '"></a>');
                                        freeToAdd = true;
                                    }
                                }
                            );
                        }
                    }
                });

                mapplic.on('levelswitched', function (e, level) {
                    self.moveTo(0, 0, 0, 0);
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
