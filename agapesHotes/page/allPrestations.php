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
        echo getTitle($Page->getName(), $Page->getSlug(), ' de ' . $Site->getNom());
        $Prestation = new \App\Plugin\AgapesHotes\Prestation();
        $Prestation->setSiteId($Site->getId());
        $allPrestations = $Prestation->showAll();
        ?>
        <div class="container-fluid">
            <button id="addPrestation" type="button" class="btn btn-info btn-sm mb-4" data-toggle="modal"
                    data-target="#modalAddPrestation">
                <?= trans('Ajouter un prestation'); ?>
            </button>
            <div class="row">
                <div class="col-12">
                    <div class="table-responsive">
                        <table id="pagesTable"
                               class="sortableTable table table-striped">
                            <thead>
                            <tr>
                                <th><?= trans('Nom'); ?></th>
                                <th><?= trans('Modifié par'); ?></th>
                                <th><?= trans('Modifié le'); ?></th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php if ($allPrestations):
                                $User = new \App\Users();
                                $PrestationPrix = new \App\Plugin\AgapesHotes\PrixPrestation();

                                foreach ($allPrestations as $prestation):
                                    $User->setId($prestation->userId);
                                    $User->show();
                                    $PrestationPrix->setDateDebut(date('Y-m-d'));
                                    $PrestationPrix->setPrestationId($prestation->id);
                                    ?>
                                    <tr data-idprestation="<?= $prestation->id ?>">
                                        <td>
                                            <span data-name="nom"><?= $prestation->nom ?></span>
                                            <span class="float-right text-info">
                                                <small>
                                                    <?php if ($PrestationPrix->showReelPrice()): ?>
                                                        <span class="prestationPrice"
                                                              data-prestationprixid="<?= $PrestationPrix->getId(); ?>"
                                                              data-price="<?= $PrestationPrix->getPrixHT(); ?>"
                                                              data-datedebut="<?= displayDBDate($PrestationPrix->getDateDebut()); ?>">
                                                            <?= $PrestationPrix->getPrixHT() . '€ HT'; ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </small>
                                            </span>
                                        </td>
                                        <td><?= $User->getNom(); ?> <?= $User->getPrenom(); ?></td>
                                        <td><?= displayTimeStamp($prestation->updated_at) ?></td>
                                        <td>
                                            <button data-idprestation="<?= $prestation->id ?>"
                                                    class="btn btn-sm updatePrice" data-toggle="modal"
                                                    data-target="#modalAddPrice"
                                                    title="<?= trans('Mettre à jour le prix de la prestation'); ?>">
                                                <span class="btnPrice"><i class="fas fa-dollar-sign"></i></span>
                                            </button>
                                            <button data-idprestation="<?= $prestation->id ?>"
                                                    class="btn btn-sm updatePrestation"
                                                    title="<?= trans('Modifier'); ?>">
                                                <span class="btnEdit"><i class="fas fa-wrench"></i></span>
                                            </button>
                                            <button type="button" class="btn btn-sm archivePrestation"
                                                    title="<?= trans('Archiver'); ?>"
                                                    data-idprestation="<?= $prestation->id ?>">
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
        <div class="modal fade" id="modalAddPrestation" tabindex="-1" role="dialog"
             aria-labelledby="modalAddPrestationTitle"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="" method="post" id="addPrestationForm">
                        <div class="modal-header">
                            <h5 class="modal-title"
                                id="modalAddPrestationTitle"><?= trans('Ajouter un prestation'); ?></h5>
                        </div>
                        <div class="modal-body" id="modalPrestationBody">
                            <div class="row">
                                <div class="col-12 my-2">
                                    <input type="hidden" name="siteId" value="<?= $Site->getId(); ?>">
                                    <?= \App\Form::text('Nom', 'nom', 'text', !empty($_POST['nom']) ? $_POST['nom'] : '', true, 255); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 my-2" id="FormAddPrestationInfos"></div>
                            </div>
                        </div>
                        <div class="modal-footer" id="modalPrestationFooter">
                            <?= \App\Form::target('ADDPRESTATION'); ?>
                            <button type="submit" id="savePrestationBtn"
                                    class="btn btn-primary"><?= trans('Enregistrer'); ?></button>
                            <button type="button" class="btn btn-secondary"
                                    data-dismiss="modal"><?= trans('Fermer'); ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="modal fade" id="modalAddPrice" tabindex="-1" role="dialog"
             aria-labelledby="modalAddPriceTitle"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="" method="post" id="addPrestationPriceForm">
                        <div class="modal-header">
                            <h5 class="modal-title" id="modalAddPriceTitle"></h5>
                        </div>
                        <div class="modal-body" id="modalPriceBody">
                            <div class="row">
                                <div class="col-12 my-2">
                                    <input type="hidden" id="siteIdInput" name="siteId" value="<?= $Site->getId(); ?>">
                                    <input type="hidden" id="prestationIdInput" name="prestationId" value="">
                                    <input type="hidden" id="idPrestationPrixInput" name="id" value="">
                                    <?= \App\Form::text('Nouveau prix HT', 'prixHT', 'text', '', true, 255); ?>
                                    <div class="my-2"></div>
                                    <?= \App\Form::text('Date d\'effet du nouveau prix', 'dateDebut', 'text', '', true, 10, '', '', 'datepicker'); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 my-2" id="FormAddPriceInfos"></div>
                            </div>
                        </div>
                        <div class="modal-footer" id="modalPriceFooter">
                            <?= \App\Form::target('UPDATEPRESTATIONPRICE'); ?>
                            <button type="submit" id="savePriceBtn"
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

                $('#savePrestationBtn').on('click', function (event) {
                    event.preventDefault();

                    $('#FormAddPrestationInfos').hide().html('');
                    busyApp();

                    $.post(
                        '<?= AGAPESHOTES_URL . 'process/ajaxPrestationProcess.php'; ?>',
                        $('#addPrestationForm').serialize(),
                        function (data) {
                            if (data === true || data == 'true') {
                                $('#loader').fadeIn(400);
                                location.reload();
                            } else {
                                $('#FormAddPrestationInfos')
                                    .html('<p class="bg-danger text-white">' + data + '</p>').show();
                            }
                            availableApp();
                        }
                    )
                });

                $('#savePriceBtn').on('click', function (event) {
                    event.preventDefault();

                    $('#FormAddPriceInfos').hide().html('');
                    busyApp();

                    $.post(
                        '<?= AGAPESHOTES_URL . 'process/ajaxPrestationProcess.php'; ?>',
                        $('#addPrestationPriceForm').serialize(),
                        function (data) {
                            if (data === true || data == 'true') {
                                $('#loader').fadeIn(400);
                                location.reload();
                            } else {
                                $('#FormAddPriceInfos')
                                    .html('<p class="bg-danger text-white">' + data + '</p>').show();
                            }
                            availableApp();
                        }
                    )
                });

                $('.updatePrice').on('click', function () {
                    var idPrestation = $(this).data('idprestation');
                    var $tr = $('tr[data-idprestation="' + idPrestation + '"]');
                    var prestationName = $tr.find('span[data-name="nom"]').text();
                    var $parent = $tr.find('span.prestationPrice');

                    $('#modalAddPriceTitle').html('Prix de la prestation : ' + prestationName);
                    $('input#prestationIdInput').val(idPrestation);

                    if ($parent.length > 0) {
                        $('#addPrestationPriceForm input#idPrestationPrixInput').val($parent.data('prestationprixid'));
                        $('#addPrestationPriceForm input#prixHT').val($parent.data('price'));
                        $('#addPrestationPriceForm input#dateDebut').val($parent.data('datedebut'));

                    }
                });

                $('.updatePrestation').on('click', function (event) {
                    event.preventDefault();
                    var idPrestation = $(this).data('idprestation');

                    var $tdNom = $('tr[data-idprestation="' + idPrestation + '"] span[data-name="nom"]');
                    var oldName = $tdNom.text();

                    $tdNom.html('<div class="positionRelative"><input name="nom" value="' + oldName + '" class="form-control">' +
                        '<button type="button" data-idprestation="' + idPrestation + '" class="btn btn-sm btn-success floatOnrightElement btnUpdatePrestation"><i class="fas fa-check"></i></button></div>');
                });

                $('body').on('click', '.btnUpdatePrestation', function (event) {
                    event.preventDefault();
                    var idPrestation = $(this).data('idprestation');
                    var newName = $(this).prev('input').val();

                    $.post(
                        '<?= AGAPESHOTES_URL . 'process/ajaxPrestationProcess.php'; ?>',
                        {
                            UPDATEPRESTATION: 'OK',
                            idPrestationUpdate: idPrestation,
                            newName: newName
                        },
                        function (data) {
                            if (data === true || data == 'true') {
                                var $tdNom = $('tr[data-idprestation="' + idPrestation + '"] span[data-name="nom"]');
                                $tdNom.html(newName);
                            } else {
                                alert(data);
                            }

                            availableApp();
                        }
                    );

                });

                $('.archivePrestation').on('click', function (event) {
                    event.preventDefault();
                    var idPrestation = $(this).data('idprestation');

                    if (confirm('<?= trans('Vous allez archiver cette prestation'); ?>')) {
                        busyApp();
                        $.post(
                            '<?= AGAPESHOTES_URL . 'process/ajaxPrestationProcess.php'; ?>',
                            {
                                ARCHIVEPRESTATION: 'OK',
                                idPrestationArchive: idPrestation
                            },
                            function (data) {
                                if (data === true || data == 'true') {
                                    $('tr[data-idprestation="' + idPrestation + '"]').slideUp();
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