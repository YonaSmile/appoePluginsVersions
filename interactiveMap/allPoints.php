<?php
require('main.php');
require_once('ini.php');
if (!empty($_GET['id']) && !empty($_GET['level']) && isset($_GET['location'])): ?>
    <?php
    $InteractiveMap = new \App\Plugin\InteractiveMap\InteractiveMap();
    $InteractiveMap->setId($_GET['id']);
    if ($InteractiveMap->show()) : ?>

        <?php
        $map = json_decode($InteractiveMap->getData(), true);
        $allCategories = array();
        if (!empty($map['categories'])) {
            foreach ($map['categories'] as $category) {
                $allCategories[$category['id']] = $category['id'];
            }
        }
        for ($i = 0; $i < count($map['levels']); $i++) : ?>
            <?php if ($map['levels'][$i]['id'] == $_GET['level']) : ?>
                <?php foreach ($map['levels'][$i]['locations'] as $location): ?>
                    <?php if ($location['id'] == $_GET['location']): ?>
                        <div class="row">
                            <div class="col-12">
                                <h6>ID : <?= $location['id']; ?></h6>
                            </div>
                        </div>
                        <form method="post" class="locationForm" enctype="multipart/form-data" action="">
                            <input type="hidden" id="idMap" name="idMap" value="<?= $InteractiveMap->getId(); ?>">
                            <input type="hidden" id="id" name="id" value="<?= $location['id']; ?>">
                            <input type="hidden" name="updateMapLocation" value="OK">
                            <input type="hidden" name="description" id="ckeditData"
                                   value="<?= $location['description']; ?>">
                            <input type="hidden" id="level" name="level" value="<?= $_GET['level']; ?>">
                            <?= \App\Form::text('Titre', 'title', 'text', $location['title'], false, 250, '', '', 'form-control-sm mb-2'); ?>
                            <?= \App\Form::text('A Propos', 'about', 'text', $location['about'], false, 250, '', '', 'form-control-sm mb-2'); ?>
                            <div class="mb-2">
                                <?= \App\Form::textarea('description', 'ckeditDescription', $location['description'], 5, false, '', 'ckeditorBasic'); ?>
                            </div>
                            <?= \App\Form::text('Photo', 'thumbnail[]', 'file', '', false, 350, '', '', 'form-control-sm mb-2'); ?>
                            <?= \App\Form::select('Catégorie', 'category', $allCategories, $location['category'], false); ?>
                            <div style="position: relative" class="my-2">
                                <?= \App\Form::text('Couleur de remplissage', 'fill', 'color', !empty($location['fill']) ? $location['fill'] : '', false, 50, '', '', 'form-control-sm mb-2'); ?>
                                <button type="button" style="position: absolute; right: 0;top:50%;"
                                        class="btn btn-danger btn-sm cleanColor">
                                    <i class="fas fa-times"></i></button>
                            </div>
                            <input type="hidden" id="pin" name="pin"
                                   value="<?= !empty($location['pin']) ? $location['pin'] : 'hidden'; ?>">
                            <?php if (INTERACTIVE_MAP_PINS): ?>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input"
                                        <?= !empty($location['pin']) && $location['pin'] != 'hidden' ? 'checked' : ''; ?>
                                           id="checkMarker">
                                    <label class="custom-control-label"
                                           for="checkMarker"><?= trans('Choix du marqueur'); ?></label>
                                </div>
                                <div class="btn-group" role="group"
                                     id="markersChoisesImg" <?= !empty($location['pin']) && $location['pin'] != 'hidden' ? '' : 'style="display: none"'; ?>
                                     aria-label="markerChoise">
                                    <?php foreach (INTERACTIVE_MAP_PINS as $key => $pin): ?>
                                        <?php if (file_exists(INTERACTIVE_MAP_PATH . 'images/' . $pin)): ?>
                                            <button type="button"
                                                    class="btn <?= !empty($location['pin']) && $location['pin'] == $key ? 'bg-info' : ''; ?> btn-light markerChoiseBtn p-2"
                                                    data-pinchoise="<?= $key; ?>">
                                                <img src="<?= INTERACTIVE_MAP_URL . 'images/' . $pin; ?>">
                                            </button>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </form>
                        <hr>
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <button type="button"
                                        class="btn btn-outline-primary btn-block my-2 refreshInterMapPoint">
                                    <?= trans('Rafraîchir'); ?>
                                </button>
                            </div>
                            <div class="col-12 col-md-6">
                                <button type="button"
                                        class="btn btn-outline-danger btn-block my-2 deleteInterMapPoint"
                                        data-idmap="<?= $_GET['id']; ?>" data-level="<?= $_GET['level']; ?>"
                                        data-id="<?= $location['id']; ?>"><?= trans('Supprimer'); ?></button>
                            </div>
                        </div>
                        <script>
                            $(document).ready(function () {

                                // Variable to store your files
                                var files;

                                // Add events
                                $('input[type=file]').on('change', prepareUpload);

                                // Grab the files and set them to our variable
                                function prepareUpload(event) {
                                    event.stopPropagation();
                                    event.preventDefault();

                                    files = event.target.files;
                                    uploadFiles();
                                }

                                function uploadFiles() {

                                    busyApp();

                                    // Create a formdata object and add the files
                                    var data = new FormData();
                                    $.each(files, function (key, value) {
                                        data.append(key, value);
                                    });

                                    var idMap = $('form.locationForm input#idMap').val();
                                    var level = $('form.locationForm input#level').val();
                                    var idLocation = $('form.locationForm input#id').val();

                                    $.ajax({
                                        url: '<?= INTERACTIVE_MAP_URL; ?>process/ajaxProcess.php?uploadThumbnail&idMap=' + idMap + '&level=' + level + '&idLocation=' + idLocation,
                                        type: 'POST',
                                        data: data,
                                        cache: false,
                                        processData: false, // Don't process the files
                                        contentType: false, // Set content type to false as jQuery will tell the server its a query string request
                                        success: function (data, textStatus, jqXHR) {
                                            if (typeof data.error === 'undefined') {
                                                $('input[type=file]').addClass('is-valid');
                                                availableApp();
                                            }
                                            else {
                                                // Handle errors here
                                                //console.log('ERRORS RESPONSE: ' + data.error);
                                            }
                                        },
                                        error: function (jqXHR, textStatus, errorThrown) {
                                            // Handle errors here
                                            //console.log('ERRORS SENDING: ' + textStatus, 'DETAILS: ' + errorThrown);
                                        }
                                    });
                                }

                                $('button.cleanColor').on('click', function (event) {
                                    event.stopPropagation();
                                    event.preventDefault();

                                    $('input#fill').val('#343F4B').trigger('change');
                                });

                                $('#checkMarker').change(function (event) {
                                    if ($(this).is(':checked')) {
                                        event.stopPropagation();
                                        event.preventDefault();
                                        $('#markersChoisesImg').slideDown('fast');
                                    } else {
                                        $('#markersChoisesImg').slideUp('fast');
                                        $('button.markerChoiseBtn').removeClass('bg-info');
                                        $('input#pin').val('hidden');
                                    }
                                });

                                $('button.markerChoiseBtn').on('click', function () {
                                    var $btn = $(this);
                                    $('button.markerChoiseBtn').removeClass('bg-info');
                                    $btn.addClass('bg-info');
                                    $('input#pin').val($btn.data('pinchoise'));
                                    updateInterMapData($('input#pin'));
                                });

                                $('form.locationForm').bind('blur change', $(' input, textarea, select'), function (e) {
                                    var $input = $(e.target);
                                    updateInterMapData($input);
                                });

                            });


                            CKEDITOR.replaceAll('ckeditorBasic');

                            for (var i in CKEDITOR.instances) {

                                CKEDITOR.instances[i].on('blur', function () {
                                    var id = this.element.$.id;
                                    var $input = $('#' + id);
                                    var value = this.getData();
                                    $('form.locationForm input#ckeditData').val(value);
                                    updateInterMapData($input);
                                });
                            }


                            function updateInterMapData(input) {
                                busyApp();
                                $.post(
                                    '<?= INTERACTIVE_MAP_URL; ?>process/ajaxProcess.php',
                                    $('form.locationForm').serialize(),
                                    function (data) {
                                        if (data) {
                                            $('form.locationForm input, form.locationForm textarea, form.locationForm select').removeClass('is-valid');
                                            input.addClass('is-valid');
                                            $('form.locationForm').effect('highlight');
                                            availableApp();
                                        }
                                    }
                                );
                            }
                        </script>
                        <?php break; ?>

                    <?php endif; ?>
                <?php endforeach; ?>

                <?php break; ?>

            <?php endif; ?>
        <?php endfor; ?>
    <?php endif; ?>
<?php endif; ?>
