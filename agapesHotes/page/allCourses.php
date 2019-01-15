<?php require('header.php');
if (!empty($_GET['secteur']) && !empty($_GET['site']) && !empty($_GET['etablissement'])):

    //Get Secteur
    $Secteur = new \App\Plugin\AgapesHotes\Secteur();
    $Secteur->setSlug($_GET['secteur']);

    //Get Site
    $Site = new \App\Plugin\AgapesHotes\Site();
    $Site->setSlug($_GET['site']);

    //Get Etablissement
    $Etablissement = new \App\Plugin\AgapesHotes\Etablissement();
    $Etablissement->setSlug($_GET['etablissement']);

    //Check Secteur, Site and Etablissement
    if (
        $Secteur->showBySlug() && $Site->showBySlug() && $Etablissement->showBySlug()
        && $Site->getSecteurId() == $Secteur->getId() && $Site->getId() == $Etablissement->getSiteId()
    ):
        echo getTitle($Page->getName(), $Page->getSlug(), ' de ' . $Etablissement->getNom());
        $Courses = new \App\Plugin\AgapesHotes\Courses();
        $Courses->setEtablissementId($Etablissement->getId());
        $allCourses = $Courses->showAll();
        ?>
        <div class="container-fluid">
            <button id="addCourses" type="button" class="btn btn-info btn-sm mb-4" data-toggle="modal"
                    data-target="#modalAddCourses">
                <?= trans('Ajouter à la Liste vivre cru'); ?>
            </button>
            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        <table class="sortableTable table table-striped">
                            <thead>
                            <tr>
                                <th><?= trans('Nom de l\'article'); ?></th>
                                <th><?= trans('Modifié par'); ?></th>
                                <th><?= trans('Modifié le'); ?></th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if ($allCourses):
                                foreach ($allCourses as $courses): ?>
                                    <tr data-idcourses="<?= $courses->id ?>">
                                        <td>
                                            <span data-name="nom"><?= $courses->nom ?></span>
                                        </td>
                                        <td><?= getUserEntitled($courses->userId); ?></td>
                                        <td><?= displayTimeStamp($courses->updated_at) ?></td>
                                        <td>
                                            <button data-idcourses="<?= $courses->id ?>"
                                                    class="btn btn-sm updateCourses"
                                                    title="<?= trans('Modifier'); ?>">
                                                <span class="btnEdit"><i class="fas fa-wrench"></i></span>
                                            </button>
                                            <button type="button" class="btn btn-sm archiveCourses"
                                                    title="<?= trans('Archiver'); ?>"
                                                    data-idcourses="<?= $courses->id ?>">
                                                <span class="btnArchive"><i class="fas fa-archive"></i></span>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modalAddCourses" tabindex="-1" role="dialog"
             aria-labelledby="modalAddCoursesTitle"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="" method="post" id="addCoursesForm">
                        <div class="modal-header">
                            <h5 class="modal-title"
                                id="modalAddCoursesTitle"><?= trans('Ajouter à la Liste vivre cru'); ?></h5>
                        </div>
                        <div class="modal-body" id="modalCoursesBody">
                            <div class="row">
                                <div class="col-12 my-2">
                                    <input type="hidden" name="etablissementId" value="<?= $Etablissement->getId(); ?>">
                                    <?= \App\Form::text('Nom de l\'article', 'nom', 'text', !empty($_POST['nom']) ? $_POST['nom'] : '', true, 255); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 my-2" id="FormAddCoursesInfos"></div>
                            </div>
                        </div>
                        <div class="modal-footer" id="modalCoursesFooter">
                            <?= \App\Form::target('ADDCOURSES'); ?>
                            <button type="submit" id="saveCoursesBtn"
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

                $('#saveCoursesBtn').on('click', function (event) {
                    event.preventDefault();

                    $('#FormAddCoursesInfos').hide().html('');
                    busyApp();

                    $.post(
                        '<?= AGAPESHOTES_URL . 'process/ajaxCoursesProcess.php'; ?>',
                        $('#addCoursesForm').serialize(),
                        function (data) {
                            if (data === true || data == 'true') {
                                $('#loader').fadeIn(400);
                                location.reload();
                            } else {
                                $('#FormAddCoursesInfos')
                                    .html('<p class="bg-danger text-white">' + data + '</p>').show();
                            }
                            availableApp();
                        }
                    )
                });

                $('.updateCourses').on('click', function (event) {
                    event.preventDefault();
                    var idCourses = $(this).data('idcourses');

                    var $tdNom = $('tr[data-idcourses="' + idCourses + '"] span[data-name="nom"]');
                    var oldName = $tdNom.text();

                    $tdNom.html('<div class="positionRelative"><input name="nom" value="' + oldName + '" class="form-control">' +
                        '<button type="button" data-idcourses="' + idCourses + '" class="btn btn-sm btn-success floatOnrightElement btnUpdateCourses"><i class="fas fa-check"></i></button></div>');
                });

                $('body').on('click', '.btnUpdateCourses', function (event) {
                    event.preventDefault();
                    var idCourses = $(this).data('idcourses');
                    var newName = $(this).prev('input').val();

                    $.post(
                        '<?= AGAPESHOTES_URL . 'process/ajaxCoursesProcess.php'; ?>',
                        {
                            UPDATECOURSES: 'OK',
                            idCoursesUpdate: idCourses,
                            newName: newName
                        },
                        function (data) {
                            if (data === true || data == 'true') {
                                var $tdNom = $('tr[data-idcourses="' + idCourses + '"] span[data-name="nom"]');
                                $tdNom.html(newName);
                            } else {
                                alert(data);
                            }

                            availableApp();
                        }
                    );

                });

                $('.archiveCourses').on('click', function (event) {
                    event.preventDefault();
                    var idCourses = $(this).data('idcourses');

                    if (confirm('<?= trans('Vous allez archiver cet article'); ?>')) {
                        busyApp();
                        $.post(
                            '<?= AGAPESHOTES_URL . 'process/ajaxCoursesProcess.php'; ?>',
                            {
                                ARCHIVECOURSES: 'OK',
                                idCoursesArchive: idCourses
                            },
                            function (data) {
                                if (data === true || data == 'true') {
                                    $('tr[data-idcourses="' + idCourses + '"]').slideUp();
                                } else {
                                    alert(data);
                                }

                                availableApp();
                            }
                        );
                    }
                });
            });
        </script>
    <?php else: ?>
        <?= getContainerErrorMsg(trans('Ce site n\'est pas accessible')); ?>
    <?php endif; ?>
<?php endif; ?>
<?php require('footer.php'); ?>