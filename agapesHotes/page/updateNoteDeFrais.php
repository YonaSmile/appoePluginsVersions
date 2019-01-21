<?php require('header.php');
if (!empty($_GET['secteur']) && !empty($_GET['site'])):

    //Get Secteur
    $Secteur = new \App\Plugin\AgapesHotes\Secteur();
    $Secteur->setSlug($_GET['secteur']);

    //Get Site
    $Site = new \App\Plugin\AgapesHotes\Site();
    $Site->setSlug($_GET['site']);

    //Check Secteur and Site
    if (
        $Secteur->showBySlug() && $Site->showBySlug() && $Site->getSecteurId() == $Secteur->getId()
    ):
        echo getTitle($Page->getName(), $Page->getSlug(), ' de <strong>' . $Site->getNom() . '</strong> du mois de <strong>' . strftime("%B", strtotime(date('Y-m-d'))) . '</strong>');

        //Get Main Supplementaire
        $NoteDeFrais = new \App\Plugin\AgapesHotes\NoteDeFrais();
        $NoteDeFrais->setSiteId($Site->getId());

        //Get Etablissement by Site
        $Etablissement = new \App\Plugin\AgapesHotes\Etablissement();
        $Etablissement->setSiteId($Site->getId());
        $allEtablissements = $Etablissement->showAllBySite();

        $allCourses = array();

        //Get Courses
        $Course = new \App\Plugin\AgapesHotes\Courses();
        foreach ($allEtablissements as $etablissement) {
            $Course->setEtablissementId($etablissement->id);
            $allCourses = array_merge_recursive($allCourses, $Course->showAll());
        }

        //Select period
        $dateDebut = new \DateTime(date('Y-m-01'));
        $dateFin = new \DateTime(date('Y-m-t'));
        $allNoteDeFrais = $NoteDeFrais->showByDate($dateDebut->format('Y-m-d'), $dateFin->format('Y-m-d'));
        ?>
        <div class="container-fluid">
            <button id="addMainSupp" type="button" class="btn btn-info btn-sm mb-4" data-toggle="modal"
                    data-target="#modalNoteDeFrais">
                <?= trans('Ajouter un frais'); ?>
            </button>
            <div class="row">
                <div class="col-12">
                    <div class="row">
                        <div class="table-responsive">
                            <table id="pagesTable"
                                   class="sortableTable table table-striped">
                                <thead>
                                <tr>
                                    <th><?= trans('Date'); ?></th>
                                    <th><?= trans('Type'); ?></th>
                                    <th><?= trans('Produit'); ?></th>
                                    <th><?= trans('Montant HT'); ?></th>
                                    <th><?= trans('Modifié par'); ?></th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if ($allNoteDeFrais):
                                    foreach ($allNoteDeFrais as $noteDeFrais): ?>
                                        <tr data-idnotedefrais="<?= $noteDeFrais->id; ?>">
                                            <td data-name="date"
                                                data-dateformat="<?= $noteDeFrais->date; ?>"><?= displayFrDate($noteDeFrais->date); ?></td>
                                            <td data-name="type"><?= $noteDeFrais->type; ?></td>
                                            <td data-name="produit"><?= $noteDeFrais->nom; ?></td>
                                            <td data-name="montantht"><?= $noteDeFrais->montantHt; ?>€</td>
                                            <td><?= displayCompleteDate($noteDeFrais->updated_at); ?></td>
                                            <td>
                                                <button data-idnotedefrais="<?= $noteDeFrais->id ?>" data-toggle="modal"
                                                        data-target="#modalNoteDeFrais"
                                                        class="btn btn-sm updateNoteDeFrais"
                                                        title="<?= trans('Modifier'); ?>">
                                                    <span class="btnEdit"><i class="fas fa-wrench"></i></span>
                                                </button>
                                                <button type="button" class="btn btn-sm deleteNoteDeFrais"
                                                        title="<?= trans('Supprimer'); ?>"
                                                        data-idnotedefrais="<?= $noteDeFrais->id ?>">
                                                    <span class="btnArchive"><i class="fas fa-archive"></i></span>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach;
                                endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modalNoteDeFrais" tabindex="-1" role="dialog"
             aria-labelledby="modalNoteDeFraisTitle"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="" method="post" id="noteDeFraisForm">
                        <input type="hidden" name="id" value="">
                        <input type="hidden" name="siteId" value="<?= $Site->getId(); ?>">
                        <?= getTokenField(); ?>
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalNoteDeFraisTitle"><?= trans('Note de frais'); ?></h5>
                        </div>
                        <div class="modal-body" id="modalNoteDeFraisBody">
                            <div class="row">
                                <div class="col-12 col-lg-6 my-2">
                                    <?= \App\Form::text('Date', 'date', 'date', '', true, 50); ?>
                                </div>
                                <div class="col-12 col-lg-6 my-2">
                                    <?= \App\Form::select('Type', 'type', array('Denrée Alimentaire' => 'Denrée Alimentaire', 'Unique Entretien' => 'Unique Entretien'), '', true); ?>
                                </div>
                                <div class="col-12 col-lg-8 my-2">
                                    <?= \App\Form::text('Nom du produit', 'nom', 'text', '', true, 255); ?>
                                </div>
                                <div class="col-12 col-lg-4 my-2 align-self-end">
                                    <?= \App\Form::text('Montant HT (€)', 'montantHt', 'text', '', true, 7); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 my-2" id="FormNoteDeFraisInfos"></div>
                            </div>
                        </div>
                        <div class="modal-footer" id="modalNoteDeFraisFooter">
                            <?= \App\Form::target('UPDATENOTEDEFRAIS'); ?>
                            <button type="submit" id="saveNoteDeFraisBtn"
                                    class="btn btn-primary"><?= trans('Enregistrer'); ?></button>
                            <button type="button" class="btn btn-secondary"
                                    data-dismiss="modal"><?= trans('Fermer'); ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <script type="text/javascript" src="/app/js/printThis.js"></script>
        <script>
            $(document).ready(function () {

                $('#saveNoteDeFraisBtn').on('click', function (event) {
                    event.preventDefault();

                    $('#FormNoteDeFraisInfos').hide().html('');
                    busyApp();

                    $.post(
                        '<?= AGAPESHOTES_URL . 'process/ajaxNoteDeFraisProcess.php'; ?>',
                        $('#noteDeFraisForm').serialize(),
                        function (data) {
                            if (data === true || data == 'true') {
                                $('#loader').fadeIn(400);
                                location.reload();
                            } else {
                                $('#FormNoteDeFraisInfos')
                                    .html('<p class="">' + data + '</p>').show();
                            }
                            availableApp();
                        }
                    );
                });

                $('.updateNoteDeFrais').on('click', function () {
                    var idNoteDeFrais = $(this).data('idnotedefrais');
                    var $tr = $('tr[data-idnotedefrais="' + idNoteDeFrais + '"]');

                    var produit = $tr.find('[data-name="produit"]').text();
                    var date = $tr.find('[data-name="date"]').data('dateformat');
                    var type = $tr.find('[data-name="type"]').text();
                    var montantHt = parseReelFloat($tr.find('[data-name="montantht"]').text());

                    var $form = $('#noteDeFraisForm');

                    $form.find('input[name="id"]').val(idNoteDeFrais);
                    $form.find('input#date').val(date);
                    $form.find('select#type').val(type);
                    $form.find('input#nom').val(produit);
                    $form.find('input#montantHt').val(montantHt);
                });

                $('.deleteAchat').on('click', function (event) {
                    event.preventDefault();
                    var $btn = $(this);
                    var idAchat = $btn.data('idachat');
                    var clientName = $btn.data('clientname');

                    busyApp();

                    $.post(
                        '<?= AGAPESHOTES_URL . 'process/ajaxMainSupplementaireProcess.php'; ?>',
                        {
                            DELETEACHAT: 'OK',
                            idAchat: idAchat
                        },
                        function (data) {
                            if (data === true || data == 'true') {
                                $btn.parent('div.productFields').prev('input.mainSuppIdInput').remove();
                            } else {
                                $('.formUpdateMainSuppInfos[data-clientname="' + clientName + '"]')
                                    .html('<p class="">' + data + '</p>').show();
                            }
                            availableApp();
                        }
                    );
                });
            });
        </script>

    <?php else: ?>
        <?= getContainerErrorMsg(trans('Cet site n\'est pas accessible')); ?>
    <?php endif; ?>
<?php endif; ?>
<?php require('footer.php'); ?>