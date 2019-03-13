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
                        <div class="col-12 col-lg-9 my-2">
                            <form action="" method="post" class="mb-3 p-2 border-bottom slideForm"
                                  id="addNoteDeFraisForm"
                                  style="display: none;">
                                <h6>Note de Frais</h6>
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
                                    <div class="col-12 col-lg-5 my-2">
                                        <?= \App\Form::text('Nom du frais *', 'nom', '', '', true, 150, 'list="nomFraisList" autocomplete="off"'); ?>
                                        <small class="text-secondary">Double clique pour la liste d'aide</small>
                                        <datalist id="nomFraisList"></datalist>
                                    </div>
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
                                    <div class="col-12 col-lg-6 my-3">
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
                                <div class="text-right">
                                    <button type="submit" id="saveNoteDeFraisBtn"
                                            class="btn btn-primary"><?= trans('Enregistrer'); ?></button>
                                    <button type="button" class="btn btn-secondary closeNoteDeFraisForm">
                                        <?= trans('Fermer'); ?></button>
                                </div>
                            </form>
                            <form action="" method="post" class="mb-3 p-2 border-bottom slideForm"
                                  id="addIndemniteKMForm"
                                  style="display: none;">
                                <h6>Indemnité kilométrique</h6>
                                <?= getTokenField(); ?>
                                <input type="hidden" name="siteId" value="<?= $Site->getId(); ?>">
                                <input type="hidden" name="employeId" value="">
                                <input type="hidden" name="year" value="<?= $start->format('Y'); ?>">
                                <input type="hidden" name="month" value="<?= $start->format('m'); ?>">
                                <?= \App\Form::target('ADDINDEMNITEKM'); ?>
                                <div class="row">
                                    <div class="col-12 col-lg-2 my-2">
                                        <?= \App\Form::select('Jour *', 'day', array_combine(range(1, $start->format('t')), range(1, $start->format('t'))), date('j'), true); ?>
                                    </div>
                                    <div class="col-12 col-lg-3 my-2">
                                        <?= \App\Form::select('Type de véhicule*', 'typeVehicule', TYPES_VEHICULE, '', true); ?>
                                    </div>
                                    <div class="col-12 col-lg-3 my-2">
                                        <?= \App\Form::text('Puissance *', 'puissance', '', '', true, 150, 'list="puissanceVehiculeList" autocomplete="off"'); ?>
                                        <datalist id="puissanceVehiculeList"></datalist>
                                    </div>
                                    <div class="col-12 col-lg-2 my-2">
                                        <?= \App\Form::text('Taux', 'taux', 'text', '', true, 10, 'readonly'); ?>
                                    </div>
                                    <div class="col-12 col-lg-2 my-2">
                                        <?= \App\Form::text('KM *', 'km', 'text', '', true, 8); ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-lg-3 my-3">
                                        <?= \App\Form::text('Objet du trajet *', 'objetTrajet', 'text', '', true); ?>
                                    </div>
                                    <div class="col-12 col-lg-3 my-3">
                                        <?= \App\Form::text('Trajet', 'trajet', 'text', '', true); ?>
                                    </div>
                                    <div class="col-12 col-lg-2 my-3">
                                        <?= \App\Form::text('Montant HT', 'montantHt', 'text', '', true, 8, 'readonly'); ?>
                                    </div>
                                    <div class="col-12 col-lg-4 my-3">
                                        <?= \App\Form::select('Affectation *', 'affectation', $allSites, $Site->getId(), true); ?>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 my-2">
                                        <?= \App\Form::textarea('Commentaire', 'commentaire'); ?>
                                    </div>
                                </div>
                                <div class="row my-3">
                                    <div class="col-12" id="infoAddIndemniteKmForm"></div>
                                </div>
                                <div class="text-right">
                                    <button type="submit" id="saveIndemniteKmBtn"
                                            class="btn btn-primary"><?= trans('Enregistrer'); ?></button>
                                    <button type="button" class="btn btn-secondary closeIndemniteKmForm">
                                        <?= trans('Fermer'); ?></button>
                                </div>
                            </form>
                            <div id="noteDeFraisEmployeData"></div>
                        </div>
                        <div class="col-12 col-lg-3 my-2 border-left">
                            <div>
                                <button type="button"
                                        class="btn btn-block bgColorPrimary addNoteFraisBtn my-2 text-left">
                                    <i class="fas fa-plus"></i> <strong>Note de Frais</strong>
                                </button>
                                <button type="button"
                                        class="btn btn-block bgColorPrimary addIndemniteKMBtn my-2 text-left">
                                    <i class="fas fa-plus"></i> <strong>Indemnité Kilométrique</strong>
                                </button>
                                <button type="button" style="display:none"
                                        class="btn btn-block btn-danger deleteNoteDeFrais my-2 text-left">
                                    <i class="fas fa-times"></i> <strong>Supprimer</strong>
                                    &nbsp;<span class="countPrintInfo"></span>
                                </button>
                                <button type="button" class="btn btn-block btn-info my-2 text-left printNoteDeFrais">
                                    <i class="fas fa-print"></i> <strong>Imprimer</strong>
                                    &nbsp;<span class="countPrintInfo">les notes de frais</span>
                                </button>
                                <button type="button" class="btn btn-block btn-warning my-2 text-left printIndemniteKm">
                                    <i class="fas fa-print"></i> <strong>Imprimer</strong> les indemnités km
                                </button>
                                <button type="button" class="btn btn-block btn-success my-2 text-left printAll">
                                    <i class="fas fa-print"></i> <strong>Imprimer tous</strong>
                                </button>
                                <hr class="mx-5">
                                <div>
                                    <p>
                                        <strong>Mois</strong> <?= ucfirst(strftime("%B", strtotime($start->format('Y-m-d')))); ?>
                                        <br>
                                        <strong>Année</strong> <?= $start->format('Y'); ?>
                                    </p>
                                    <p class="my-1">
                                        <strong>Notes de Frais TTC</strong>
                                        &nbsp;<span id="totalNoteDeFraisInfo"></span>
                                    </p>
                                    <p id="totalNoteDeFraisCheckedContainer" class="my-1" style="display: none;">
                                        <strong>Sélectionnés TTC</strong>
                                        &nbsp;<span id="totalNoteDeFraisCheckedInfo"></span>
                                    </p>
                                    <hr class="mx-5">
                                    <p id="totalIndemniteCheckedContainer" class="my-1" style="display: none;">
                                        <strong>Total Indemnité HT</strong>
                                        &nbsp;<span id="totalIndemniteCheckedInfo"></span>
                                    </p>
                                </div>
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

    <script>
        $(document).ready(function () {

            var employeId = '';
            var employeName = '';
            var dateNoteDeFrais = '<?= $start->format('Y') . ' ' . ucfirst(strftime("%B", strtotime($start->format('Y-m-d')))); ?>';

            function loadNoteDeFraisEmployeData() {
                $('#noteDeFraisEmployeData').html(loaderHtml() + ' Chargement...').load('<?= AGAPESHOTES_URL . 'page/getNoteDeFraisEmployeData.php'; ?>', {
                    site: '<?= $Site->getSlug(); ?>',
                    employeId: employeId,
                    annee: '<?= $start->format('Y'); ?>',
                    month: '<?= $start->format('m'); ?>'
                });
            }

            function countPrintData() {
                return $('body input[type="checkbox"][name="checkNoteDeFrais"]:checked').length;
            }

            function printNoteDeFrais() {

                var $choisenTr;
                var total;

                if (countPrintData() === 0) {
                    $choisenTr = $('body #noteDeFraisTablesContainer table tr.noteDeFraisTR');
                    total = financial(parseReelFloat($('body #totalNoteDeFraisInfo').text()));
                } else {
                    $choisenTr = $('body input[type="checkbox"][name="checkNoteDeFrais"]:checked').closest('tr.noteDeFraisTR');
                    total = financial(parseReelFloat($('body #totalNoteDeFraisCheckedInfo').text()));
                }

                var $dataTable = $choisenTr.clone();
                $('[data-name="checkbox"]', $dataTable).remove();

                var html = '';
                var commentaires = '';
                $.each($dataTable, function () {
                    html += $(this).prop('outerHTML');
                    commentaires += $(this).data('commentaires').length ? $(this).data('commentaires') + '<br>' : '';
                });

                var data = {
                    notesDeFraisTable: escapeHtml(html),
                    totalTTC: total,
                    commentaires: commentaires,
                    date: dateNoteDeFrais,
                    siteName: $('body #siteName').text(),
                    employeName: employeName,
                    pdfTemplateFilename: 'noteDeFrais',
                    pdfOutputName: 'NoteDeFrais-' + convertToSlug(employeName) + '-' + convertToSlug(dateNoteDeFrais)
                };

                pdfSend(data);
            }

            function printIndemniteKm() {

                var $choisenTr = $('body #noteDeFraisTablesContainer table tr.indemniteKmTR');
                var total = financial(parseReelFloat($('body #totalIndemniteCheckedInfo').text()));

                var $dataTable = $choisenTr.clone();
                $('.removeFromPrint', $dataTable).remove();

                var html = '';
                var commentaires = '';
                var puissance = '', typeVehicule = '', taux = '';

                $.each($dataTable, function () {
                    html += $(this).prop('outerHTML');
                    typeVehicule = $(this).data('typevehicule');
                    puissance = $(this).data('puissance');
                    taux = $(this).data('taux');
                    commentaires += $(this).data('commentaires').length ? $(this).data('commentaires') + '<br>' : '';
                });

                var data = {
                    typeVehicule: typeVehicule,
                    puissance: puissance,
                    taux: taux,
                    commentaires: commentaires,
                    indemniteKmTable:  escapeHtml(html),
                    total:  total,
                    date: dateNoteDeFrais,
                    siteName: $('body #siteName').text(),
                    employeName: employeName,
                    pdfTemplateFilename: 'indemniteKilometrique',
                    pdfOutputName: 'indemniteKilometrique-' + convertToSlug(employeName) + '-' + convertToSlug(dateNoteDeFrais)
                };

                pdfSend(data);
            }

            function printAll() {

                var $choisenTr;
                var total;

                if (countPrintData() === 0) {
                    $choisenTr = $('body #noteDeFraisTablesContainer table tr.noteDeFraisTR');
                    total = financial(parseReelFloat($('body #totalNoteDeFraisInfo').text()));
                } else {
                    $choisenTr = $('body input[type="checkbox"][name="checkNoteDeFrais"]:checked').closest('tr.noteDeFraisTR');
                    total = financial(parseReelFloat($('body #totalNoteDeFraisCheckedInfo').text()));
                }

                var $dataTable = $choisenTr.clone();
                $('[data-name="checkbox"]', $dataTable).remove();

                var html = '';
                var commentaires = '';
                $.each($dataTable, function () {
                    html += $(this).prop('outerHTML');
                    commentaires += $(this).data('commentaires').length ? $(this).data('commentaires') + '<br>' : '';
                });

                var data = {
                    totalTTC: total,
                    totalIndemniteKm: financial(parseReelFloat($('body #totalIndemniteCheckedInfo').text())),
                    notesDeFraisTable: escapeHtml(html),
                    commentaires: commentaires,
                    date: dateNoteDeFrais,
                    siteName: $('body #siteName').text(),
                    employeName: employeName,
                    pdfTemplateFilename: 'allNotes',
                    pdfOutputName: 'TousLesNotesDeFrais-' + convertToSlug(employeName) + '-' + convertToSlug(dateNoteDeFrais)
                };

                pdfSend(data);
            }

            $('.addNoteFraisBtn').on('click', function () {
                $('#addIndemniteKMForm').slideUp(300, function () {
                    $('#addNoteDeFraisForm').slideDown();
                });
            });
            $('.addIndemniteKMBtn').on('click', function () {
                $('#addNoteDeFraisForm').slideUp(300, function () {
                    $('#addIndemniteKMForm').slideDown();
                });
            });
            $('.closeNoteDeFraisForm').on('click', function () {
                $('#addNoteDeFraisForm').slideUp();
            });
            $('.closeIndemniteKmForm').on('click', function () {
                $('#addIndemniteKMForm').slideUp();
            });

            $('.printNoteDeFrais').on('click', function () {
                printNoteDeFrais();
            });

            $('.printIndemniteKm').on('click', function () {
                printIndemniteKm();
            });

            $('.printAll').on('click', function () {
                printAll();
            });

            /** Automatique Calcule **/
            $('#montantHt, #tva').on('input', function () {

                var montantHT = $('#montantHt').val();
                var tva = $('#tva').val();

                if (montantHT.length && tva.length) {
                    var montantTTC = montantHT * (1 + (tva / 100));
                    $('#addNoteDeFraisForm #montantTtc').val(financial(montantTTC, false));
                }
            });

            $('#taux, #km').on('input', function () {

                var taux = parseReelFloat($('#taux').val());
                var km = parseReelFloat($('#km').val());

                if (taux > 0 && km > 0) {
                    var montantHT = (km * taux);
                    $('#addIndemniteKMForm #montantHt').val(financial(montantHT, false));
                }
            });

            /** Save **/
            $('#saveNoteDeFraisBtn').on('click', function (event) {
                event.preventDefault();

                $('#infoAddNoteDeFraisForm').html('');
                $.post(
                    '<?= AGAPESHOTES_URL . 'process/ajaxProcess.php'; ?>',
                    $('form#addNoteDeFraisForm').serialize(),
                    function (data) {
                        if (data) {
                            if (data === true || data == 'true') {
                                $('#addNoteDeFraisForm').slideUp();
                                loadNoteDeFraisEmployeData();
                            } else {
                                $('#infoAddNoteDeFraisForm').html(data);
                            }
                        }
                    }
                );
            });

            $('#saveIndemniteKmBtn').on('click', function (event) {
                event.preventDefault();

                $('#infoAddIndemniteKmForm').html('');
                $.post(
                    '<?= AGAPESHOTES_URL . 'process/ajaxProcess.php'; ?>',
                    $('form#addIndemniteKMForm').serialize(),
                    function (data) {
                        if (data) {
                            if (data === true || data == 'true') {
                                $('#addIndemniteKMForm').slideUp();
                                loadNoteDeFraisEmployeData();
                            } else {
                                $('#infoAddIndemniteKmForm').html(data);
                            }
                        }
                    }
                );
            });

            $('button.gestionNotesDeFrais').on('click', function () {
                var $btn = $(this);

                employeName = $btn.data('employename');
                employeId = $btn.data('employeid');

                $('input[name="employeId"]').val(employeId);
                $('#modalGestionNotesDeFrais').data('employeid', employeId);
                $('#modalGestionNotesDeFrais #modalNoteDeFraisTitle').html('Gestion des Notes de frais de ' + employeName);
                $('#addNoteDeFraisForm').hide();
                $('#addIndemniteKMForm').hide();
                loadNoteDeFraisEmployeData();
            });

            $('button.addNoteFrais').on('click', function () {
                $('input[name="employeId"]').val(employeId);
            });

            $('select#type').on('change', function () {
                var type = $(this).val();
                var $nomFraisPageInput = $('#nomFraisList');
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

            $('select#typeVehicule').on('change', function () {
                var type = $(this).val();
                var $nomFraisPageInput = $('#puissanceVehiculeList');
                $nomFraisPageInput.html(loaderHtml() + ' Chargement...');
                $.post(
                    '<?= AGAPESHOTES_URL . 'process/ajaxProcess.php'; ?>',
                    {
                        getPuissanceVehiculePageByType: 'OK',
                        type: type
                    },
                    function (data) {
                        if (data) {
                            $nomFraisPageInput.html(data);
                            $('form input#puissance').val('');
                            $('form input#taux').val('')
                        }
                    }
                );
            });

            $('body').on('input', '#nom', function () {
                $('form input[name="code"]').val($('#nomFraisList option[value="' + $('#nom').val() + '"]').data('code'));
            });
            $('body').on('input', '#puissance', function () {
                $('form input[name="taux"]').val($('#puissanceVehiculeList option[value="' + $('#puissance').val() + '"]').data('taux'));
            });

            $('.deleteNoteDeFrais').on('click', function (event) {
                event.preventDefault();

                if (confirm('Vous allez supprimer ' + $('.countPrintInfo').eq(0).text())) {

                    var tab = new Array();
                    $.each($('body input[type="checkbox"][name="checkNoteDeFrais"]:checked'), function (i, val) {
                        tab[i] = $(this).val();
                    });

                    $.post(
                        '<?= AGAPESHOTES_URL . 'process/ajaxProcess.php'; ?>',
                        {
                            DELETENOTEDEFRAIS: 'OK',
                            idsNotesDeFrais: tab

                        },
                        function (data) {
                            if (data) {
                                $('.deleteNoteDeFrais').hide();
                                loadNoteDeFraisEmployeData();
                            }
                        }
                    );
                }
            });

            $('body').on('click', '.deleteIndemniteKilometrique', function (event) {
                event.preventDefault();

                $.post(
                    '<?= AGAPESHOTES_URL . 'process/ajaxProcess.php'; ?>',
                    {
                        DELETEINDEMNITEKM: 'OK',
                        idIndemniteKm: $(this).data('idindemnitekm')
                    },
                    function (data) {
                        if (data) {
                            loadNoteDeFraisEmployeData();
                        }
                    }
                );
            });
        });
    </script>
<?php else: ?>
    <?= getContainerErrorMsg(trans('Ce site n\'est pas accessible')); ?>
<?php endif; ?>
