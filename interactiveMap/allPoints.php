<?php
require('main.php');
require_once('ini.php');
if (!empty($_GET['id']) && !empty($_GET['level']) && isset($_GET['location'])): ?>
    <?php
    $InteractiveMap = new App\Plugin\InteractiveMap\InteractiveMap();
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
                            <?= App\Form::text(trans('Titre'), 'title', 'text', $location['title'], false, 250, '', '', 'form-control-sm'); ?>
                            <?= App\Form::text(trans('A Propos'), 'about', 'text', $location['about'], false, 250, '', '', 'form-control-sm'); ?>
                            <?= App\Form::textarea(trans('description'), 'ckeditDescription', $location['description'], 5, false, '', 'ckeditorBasic'); ?>
                            <?= App\Form::text(trans('Photo'), 'thumbnail[]', 'file', '', false, 350, '', '', 'form-control-sm'); ?>
                            <?= App\Form::select(trans('Catégorie'), 'category', $allCategories, $location['category']); ?>
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
                                                // Success so call function to process the form

                                            }
                                            else {
                                                // Handle errors here
                                                console.log('ERRORS RESPONSE: ' + data.error);
                                            }
                                        },
                                        error: function (jqXHR, textStatus, errorThrown) {
                                            // Handle errors here
                                            console.log('ERRORS SENDING: ' + textStatus, 'DETAILS: ' + errorThrown);
                                            // STOP LOADING SPINNER
                                        }
                                    });
                                }

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
                                $.post(
                                    '<?= INTERACTIVE_MAP_URL; ?>process/ajaxProcess.php',
                                    $('form.locationForm').serialize(),
                                    function (data) {
                                        if (data) {
                                            $('form.locationForm input, form.locationForm textarea, form.locationForm select').removeClass('is-valid');
                                            input.addClass('is-valid');
                                            $('form.locationForm').effect('highlight');
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
