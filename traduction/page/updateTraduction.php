<?php
require('header.php');
require(TRADUCTION_PATH . 'process/postProcess.php');
$Traduction = new \App\Plugin\Traduction\Traduction(APP_LANG);
$allContent = $Traduction->getDbData();
?>
<?= getTitle($Page->getName(), $Page->getSlug()); ?>
    <div class="container-fluid">
        <?php if (isset($Response)): ?>
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-<?= $Response->display()->status ?>" role="alert">
                        <?= $Response->display()->error_msg; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <div class="row">
            <div class="col-12 col-lg-6 form-inline">
                <div class="input-group mb-2 mr-sm-2">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                    </div>
                    <input type="text" class="form-control " name="filterTrad" id="filterTrad"
                           placeholder="<?= trans('Filtre'); ?>">
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <form action="" method="post" class="form-inline float-right">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" name="metaKeySingle" id="metaKeySingle"
                               placeholder="<?= trans('Mot'); ?>" required="true">
                        <?= getTokenField(); ?>
                        <div class="input-group-append">
                            <?= \App\Form::target('ADDTRADUCTION'); ?>
                            <button type="submit" name="ADDTRADUCTIONSUBMIT" class="btn btn-outline-info">
                                <?= trans('Ajouter une traduction'); ?>
                            </button>
                            <button type="button" id="addMultipleTrads" class="btn btn-outline-success"
                                    data-toggle="modal" data-target="#modalAddMultipleTrads">
                                <i class="fas fa-ellipsis-h"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="my-4"></div>
        <?php if ($allContent): ?>
            <form action="" method="post" id="pageContentManageForm">
                <div class="row" id="tradContainer">
                    <?php foreach ($allContent as $metaKey => $content): ?>
                        <div class="col-12 fileContent bg_grey_hover tradContent my-2">
                            <?= \App\Form::text($metaKey, $metaKey, 'text', $content['metaValue'], false, 250, 'data-idtrad="' . $content['id'] . '" autocomplet="off"'); ?>
                            <button type="button" class="deleteTraduction btn btn-sm"
                                    style="position: absolute; bottom: 0; right: 0;"
                                    data-keytrad="<?= $metaKey; ?>">
                                <span class="btnArchive"><i class="fas fa-times"></i></span>
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="my-3"></div>
                <div class="row">
                    <div class="col-12">
                        <?= \App\Form::submit('Enregistrer', 'TRADSUBMIT'); ?>
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>
    <div class="my-4"></div>
    <div class="modal fade" id="modalAddMultipleTrads" tabindex="-1" role="dialog"
         aria-labelledby="modalAddMultipleTradsTitle"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="" method="post" id="addMenuPageForm">
                    <div class="modal-header">
                        <h5 class="modal-title"
                            id="modalAddMultipleTradsTitle"><?= trans('Ajouter plusieurs traductions'); ?></h5>
                    </div>
                    <div class="modal-body" id="modalAddMultipleTradsBody">
                        <?= getTokenField(); ?>
                        <div class="row">
                            <?php foreach (LANGUAGES as $id => $lang): ?>
                                <div class="col-12 my-2">
                                    <?= \App\Form::text($lang, 'metaValue-' . $id, 'text', !empty($_POST['metaValue-' . $id]) ? $_POST['metaValue-' . $id] : ''); ?>
                                </div>
                            <?php endforeach; ?>
                            <div class="col-12 my-2">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" id="tradSlug">
                                    <label class="custom-control-label"
                                           for="tradSlug"><?= trans('Traduction de lien'); ?></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer" id="modalAddMultipleTradsFooter">
                        <?= \App\Form::target('ADDMULTIPLETRADS'); ?>
                        <button type="submit" name="ADDMULTIPLETRADSSUBMIT"
                                class="btn btn-primary"><?= trans('Enregistrer'); ?></button>
                        <button type="button" class="btn btn-secondary"
                                data-dismiss="modal"><?= trans('Fermer'); ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function () {

            $('form#pageContentManageForm').submit(function (event) {
                event.preventDefault();
            });

            $.each($('form#pageContentManageForm input, form#pageContentManageForm textarea'), function () {
                var id = $(this).data('idtrad');
                $('<small class="trad' + id + ' categoryIdFloatContenaire">').insertAfter($(this));
            });

            $('#filterTrad').keyup(function () {
                var val = $.trim($(this).val().normalize('NFD').replace(/[\u0300-\u036f]/g, "")).replace(/ +/g, ' ').toLowerCase();

                $('.tradContent').show().filter(function () {
                    var text = $(this).text().normalize('NFD').replace(/[\u0300-\u036f]/g, "").replace(/\s+/g, ' ').toLowerCase();
                    return !~text.indexOf(val);
                }).hide();
            });

            $('#metaKeySingle').keyup(function () {
                $('input#metaValue-fr').val($('#metaKeySingle').val());
            });

            $('form#pageContentManageForm').on('input', 'input', function (event) {
                event.preventDefault();
                var $input = $(this);
                var idContent = $input.data('idtrad');
                var metaKey = $input.attr('name');
                var metaValue = $input.val();

                busyApp();
                delay(function () {
                    $.post(
                        '<?= TRADUCTION_URL; ?>process/ajaxProcess.php',
                        {
                            id: idContent,
                            web_traduction: 'OK',
                            metaKey: metaKey,
                            metaValue: metaValue
                        },
                        function (data) {
                            if (data === true || data == 'true') {
                                $('small.trad' + idContent).html('<?= trans('Enregistré'); ?>');
                                availableApp();
                            }
                        }
                    )
                }, 300);
            });

            $('form#pageContentManageForm').on('click', '.deleteTraduction', function (event) {
                event.preventDefault();
                var $btn = $(this);
                var keytrad = $btn.data('keytrad');
                var $parent = $btn.parent('div');

                if (confirm('<?= trans('Vous allez supprimer cette traduction'); ?>')) {
                    busyApp();
                    $.post(
                        '<?= TRADUCTION_URL; ?>process/ajaxProcess.php',
                        {
                            deleteTrad: 'OK',
                            keytrad: keytrad
                        },
                        function (data) {
                            if (data === true || data == 'true') {
                                $parent.slideUp('fast');
                                availableApp();
                            }
                        }
                    )
                }
            });

            $('#tradSlug').change(function () {
                if (this.checked) {
                    $('#modalAddMultipleTradsBody input[type="text"]').each(function () {
                        $(this).val(convertToSlug($(this).val()));
                    });
                }
            });
        });
    </script>
<?php require('footer.php'); ?>