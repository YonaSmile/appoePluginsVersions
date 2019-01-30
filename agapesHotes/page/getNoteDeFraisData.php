<?php
require_once('../main.php');
includePluginsFiles();

//Get Secteur
$Secteur = new \App\Plugin\AgapesHotes\Secteur();
$Secteur->setSlug($_POST['secteur']);

//Get Site
$Site = new \App\Plugin\AgapesHotes\Site();
$Site->setSlug($_POST['site']);
$allSites = extractFromObjToSimpleArr($Site->showAll(), 'id', 'nom');

//Check Secteur and Site
if ($Secteur->showBySlug() && $Site->showBySlug() && $Site->getSecteurId() == $Secteur->getId()):

    //Select period
    $startDate = !empty($_POST['startDate']) ? $_POST['startDate'] : date('Y-m-01');
    $start = new \DateTime($startDate);

    $allContratsOfEmployes = getAllEmployeHasContratInSite($Site->getId(), $start->format('Y'), $start->format('m'));
    $allEmployes = extractFromObjToSimpleArr($allContratsOfEmployes, 'employe_id', 'employe_name', 'employe_firstName');
    ?>
    <div class="col-12 py-4">
        <h5 class="mb-0"><?= ucfirst(strftime("%B", strtotime($start->format('Y-m-d')))); ?> <?= $start->format('Y'); ?></h5>
        <small class="d-block">Commence le <?= $start->format('d/m/Y'); ?> et se termine
            le <?= $start->format('t/m/Y'); ?></small>

    </div>
    <?php if ($allContratsOfEmployes):
    foreach ($allContratsOfEmployes as $contrat): ?>
        <div class="col-12 col-lg-3 my-2">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">
                        <small><?= getPeopleNatureNameById($contrat->employe_nature); ?></small> <?= $contrat->employe_name . ' ' . $contrat->employe_firstName; ?>
                    </h5>
                    <p class="card-text">
                    </p>
                    <button data-employename="<?= $contrat->employe_name . ' ' . $contrat->employe_firstName; ?>"
                            data-toggle="modal"
                            data-employeid="<?= $contrat->employe_id; ?>"
                            data-target="#modalGestionNotesDeFrais"
                            class="btn btn-info text-white float-right gestionNotesDeFrais"
                            title="<?= trans('Éditer'); ?>">Éditer
                    </button>
                </div>
            </div>
        </div>
    <?php endforeach;
endif; ?>
    <div class="modal fade" id="modalGestionNotesDeFrais" tabindex="-1" role="dialog"
         aria-labelledby="modalNoteDeFraisTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalNoteDeFraisTitle"><?= trans('Gestion des Notes de frais'); ?></h5>
                </div>
                <div class="modal-body" id="modalNoteDeFraisBody">
                    <div class="row">
                        <div class="col-12 col-lg-9 my-2" id="noteDeFraisEmployeData">
                            <i class="fas fa-circle-notch fa-spin"></i> Chargement
                        </div>
                        <div class="col-12 col-lg-3 my-2 border-left">
                            <button type="button" class="btn btn-block bgColorPrimary addNoteFrais my-2 text-left"
                                    data-toggle="modal" data-target="#modalAddNoteDeFrais">
                                <i class="fas fa-plus"></i> <strong>Note de Frais</strong>
                            </button>
                            <button type="button" class="btn btn-block bgColorPrimary my-2 text-left"
                                    data-toggle="modal">
                                <i class="fas fa-plus"></i> <strong>Index Kilométrique</strong>
                            </button>
                            <button type="button" class="btn btn-block btn-info my-2 text-left printNoteDeFrais">
                                <i class="fas fa-print"></i> <strong>Imprimer</strong> <span
                                        id="countPrintInfo">tous</span>
                            </button>
                            <hr class="mx-5">
                            <div>
                                <p>
                                    <strong>Mois</strong> <?= ucfirst(strftime("%B", strtotime($start->format('Y-m-d')))); ?>
                                    <br>
                                    <strong>Année</strong> <?= $start->format('Y'); ?>
                                </p>
                                <p class="my-1">
                                    <strong>Total TTC</strong> <span id="totalNoteDeFraisInfo"></span>
                                </p>
                                <p id="totalNoteDeFraisCheckedContainer" class="my-1" style="display: none;">
                                    <strong>Total sélectionnés TTC</strong>&nbsp;
                                    <span id="totalNoteDeFraisCheckedInfo"></span>
                                </p>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                            data-dismiss="modal"><?= trans('Fermer'); ?></button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="modalAddNoteDeFrais" tabindex="-1" role="dialog"
         aria-labelledby="modalAddNoteDeFraisTitle"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"
                        id="modalAddNoteDeFraisTitle"><?= trans('Ajouter une note de frais'); ?></h5>
                </div>
                <form action="" method="post" id="addNoteDeFraisForm">
                    <div class="modal-body" id="modalNoteDeFraisBody">
                        <?= getTokenField(); ?>
                        <input type="hidden" name="siteId" value="<?= $Site->getId(); ?>">
                        <input type="hidden" name="employeId" value="">
                        <input type="hidden" name="year" value="<?= $start->format('Y'); ?>">
                        <input type="hidden" name="month" value="<?= $start->format('m'); ?>">
                        <?= \App\Form::target('ADDNOTEDEFRAIS'); ?>
                        <div class="row">
                            <div class="col-12 col-lg-2 my-2">
                                <?= \App\Form::select('Jour *', 'day', array_combine(range(1, $start->format('t')), range(1, $start->format('t'))), date('j'), true); ?>
                            </div>
                            <div class="col-12 col-lg-3 my-2">
                                <?= \App\Form::select('Type *', 'type', TYPES_NOTE_FRAIS, '', true); ?>
                            </div>
                            <div class="col-12 col-lg-5 my-2" id="nomTypeFrais"></div>
                            <div class="col-12 col-lg-2 my-2">
                                <?= \App\Form::text('Code', 'code', 'text', '', false, 10); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-lg-2 my-3">
                                <?= \App\Form::text('Montant HT *', 'montantHt', 'text', '', true, 8); ?>
                            </div>
                            <div class="col-12 col-lg-2 my-3">
                                <?= \App\Form::text('TVA(%) *', 'tva', 'text', '', true, 6); ?>
                            </div>
                            <div class="col-12 col-lg-2 my-3">
                                <?= \App\Form::text('Montant TTC', 'montantTtc', 'text', '', true, 10, 'readonly'); ?>
                            </div>
                            <div class="col-12 col-lg-4 my-3">
                                <?= \App\Form::select('Affectation *', 'affectation', $allSites, $Site->getId(), true); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 my-2">
                                <?= \App\Form::text('Motif', 'motif', 'text'); ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 my-2">
                                <?= \App\Form::textarea('Commentaire', 'commentaire'); ?>
                            </div>
                        </div>
                        <div class="row my-3">
                            <div class="col-12" id="infoAddNoteDeFraisForm"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" id="saveNoteDeFraisBtn"
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

            var employeId = '';
            var employeName = '';

            function loadNoteDeFraisEmployeData() {
                $('#noteDeFraisEmployeData').load('<?= AGAPESHOTES_URL . 'page/getNoteDeFraisEmployeData.php'; ?>', {
                    site: '<?= $Site->getSlug(); ?>',
                    employeId: employeId,
                    annee: '<?= $start->format('Y'); ?>',
                    month: '<?= $start->format('m'); ?>'
                });
            }

            function printNoteDeFrais() {

                var url = '<?= AGAPESHOTES_URL; ?>page/getFactureNoteDeFrais.php';

                var $dataTable = $('#noteDeFraisTablesContainer').clone();
                $('table tr [data-name="checkbox"]', $dataTable).remove();

                var form = $('<form action="' + url + '" method="post" target="_blank">' +
                    '<input type="text" name="employeName" value="' + employeName + '" />' +
                    '<input type="text" name="siteName" value="' + $('body #siteName').text() + '" />' +
                    '<input type="text" name="date" value="' + '<?= $start->format('Y') . ' ' . ucfirst(strftime("%B", strtotime($start->format('Y-m-d')))); ?>' + '" />' +
                    '<input type="text" name="commentaires" value="Commentaire" />' +
                    '<input type="text" name="notesDeFraisTable" value="' + escapeHtml($dataTable.html()) + '" />' +
                    '<input type="text" name="totalTTC" value="' + $('body #totalNoteDeFraisInfo').text() + '" />' +
                    '</form>');
                $('body').append(form);
                form.submit();
            }

            $('.printNoteDeFrais').on('click', function () {
                printNoteDeFrais();
            });

            $('#montantHt, #tva').on('input', function () {

                var montantHT = $('#montantHt').val();
                var tva = $('#tva').val();

                if (montantHT.length && tva.length) {
                    var montantTTC = montantHT * (1 + (tva / 100));
                    $('#montantTtc').val(financial(montantTTC, false));
                }
            });

            $('#saveNoteDeFraisBtn').on('click', function (event) {
                event.preventDefault();

                $('#infoAddNoteDeFraisForm').html('');
                $.post(
                    '<?= AGAPESHOTES_URL . 'process/ajaxProcess.php'; ?>',
                    $('form#addNoteDeFraisForm').serialize(),
                    function (data) {
                        if (data) {
                            if (data === true || data == 'true') {
                                loadNoteDeFraisEmployeData();
                                $('#modalAddNoteDeFrais').modal('hide');
                            } else {
                                $('#infoAddNoteDeFraisForm').html(data);
                            }
                        }
                    }
                );
            });

            $('button.gestionNotesDeFrais').on('click', function () {
                var $btn = $(this);

                employeName = $btn.data('employename');
                employeId = $btn.data('employeid');

                $('#modalGestionNotesDeFrais').data('employeid', employeId);
                $('#modalGestionNotesDeFrais #modalNoteDeFraisTitle').html('Gestion des Notes de frais de ' + employeName);
                loadNoteDeFraisEmployeData();
            });

            $('button.addNoteFrais').on('click', function () {
                $('input[name="employeId"]').val(employeId);
            });

            $('select#type').on('change', function () {
                var type = $(this).val();
                var $nomFraisPageInput = $('#nomTypeFrais');
                $nomFraisPageInput.html(loaderHtml() + ' Chargement...');
                $.post(
                    '<?= AGAPESHOTES_URL . 'process/ajaxProcess.php'; ?>',
                    {
                        getNomFraisPageByType: 'OK',
                        type: type
                    },
                    function (data) {
                        if (data) {
                            $nomFraisPageInput.html(data);
                            $('form input[name="code"]').val('')
                        }
                    }
                );
            });

            $('body').on('input', '#nom', function () {
                $('form input[name="code"]').val($('#nomFraisList option[value="' + $('#nom').val() + '"]').data('code'));
            });
        });
    </script>
<?php else: ?>
    <?= getContainerErrorMsg(trans('Ce site n\'est pas accessible')); ?>
<?php endif; ?>
