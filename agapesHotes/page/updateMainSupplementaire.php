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
        $MainSupp = new \App\Plugin\AgapesHotes\MainSupplementaire();
        $MainSupp->setSiteId($Site->getId());

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

        //Get Prestations
        $Prestation = new \App\Plugin\AgapesHotes\Prestation();
        $Prestation->setSiteId($Site->getId());
        $allCourses = array_merge_recursive($allCourses, $Prestation->showAll());

        //Get Client
        $Client = new App\Plugin\AgapesHotes\Client();
        $allClients = $Client->showByType();

        //Select period
        $dateDebut = new \DateTime(date('Y-m-01'));
        $dateFin = new \DateTime(date('Y-m-t'));
        $allMainsSupp = $MainSupp->showByDate($dateDebut->format('Y-m-d'), $dateFin->format('Y-m-d'));
        ?>
        <div class="container-fluid">
            <button id="addMainSupp" type="button" class="btn btn-info btn-sm mb-4" data-toggle="modal"
                    data-target="#modalAddMainSupp">
                <?= trans('Ajouter une facture'); ?>
            </button>
            <div class="row">
                <div class="col-12 col-lg-8">
                    <?php if ($allMainsSupp):
                        $allClientsMainSupp = groupMultipleKeysObjectsArray($allMainsSupp, 'client_id');

                        $totalListPrice = 0;
                        foreach ($allClientsMainSupp as $clientId => $data):
                            $allTvaTotal = groupMultipleKeysObjectsArray($data, 'tauxTVA');
                            $totalCoursePrice = 0;
                            $Client->setId($clientId);
                            $Client->show();
                            ?>
                            <h6>
                                <span class="text-info addMainSuppSpan" data-toggle="modal"
                                      data-target="#modalAddMainSupp"
                                      data-clientnature="<?= $Client->getNature(); ?>"
                                      data-clientname="<?= $Client->getName(); ?>"
                                      data-clientfirstname="<?= $Client->getFirstName(); ?>"
                                      style="cursor: pointer;"><i class="fas fa-plus"></i></span>
                                <?= $Client->getEntitled(); ?>
                            </h6>
                            <ol class="courseListOl">
                                <?php foreach ($data as $achat):
                                    $totalCoursePrice += $achat->total;
                                    ?>
                                    <li>
                                        <strong>Produit</strong> <span
                                                data-name="product"><?= $achat->nom; ?></span>&nbsp;
                                        <strong>Date</strong> <span
                                                data-name="date"><?= displayFrDate($achat->date); ?></span>&nbsp;
                                        <strong>Quantité</strong> <span
                                                data-name="quantite"><?= $achat->quantite; ?></span>&nbsp;
                                        <strong>Prix HT/unité</strong> <span
                                                data-name="prixHTunite"><?= $achat->prixHTunite; ?></span>€&nbsp;
                                        <strong>Taux TVA</strong> <span
                                                data-name="tauxTVA"><?= $achat->tauxTVA; ?></span>%&nbsp;
                                        <strong>Total</strong> <span data-name="total"><?= $achat->total; ?></span>€&nbsp;
                                    </li>
                                <?php endforeach; ?>
                                <div class="my-3">
                                    <small class="float-right mt-1">
                                        <?php
                                        foreach ($allTvaTotal as $tva => $tvaData):
                                            $totalTva = 0;
                                            foreach ($tvaData as $achat) {
                                                $totalTva += $achat->total;
                                            }; ?>
                                            <strong>TOTAL HT TVA <?= $tva; ?>
                                                %</strong> <?= financial($totalTva); ?>€&nbsp;
                                        <?php endforeach; ?>
                                        <strong>TOTAL HT</strong> <?= financial($totalCoursePrice); ?>€
                                    </small>
                                </div>
                                <hr class="ml-5 mt-5">
                            </ol>
                        <?php endforeach;
                    endif; ?>
                </div>
                <div class="col-12 col-lg-4"></div>
            </div>
        </div>
        <div class="modal fade" id="modalAddMainSupp" tabindex="-1" role="dialog"
             aria-labelledby="modalAddMainSuppTitle"
             aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form action="" method="post" id="addMainSuppForm">
                        <div class="modal-header">
                            <h5 class="modal-title"
                                id="modalAddMainSuppTitle"><?= trans('Ajouter une facture'); ?></h5>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-12 my-2">
                                    <?= getTokenField(); ?>
                                    <input type="hidden" name="siteId" value="<?= $Site->getId(); ?>">
                                    <?= \App\Form::target('ADDMAINSUPPLEMENTAIRE'); ?>
                                    <div class="row my-2">
                                        <div class="col-12 col-lg-3 my-2">
                                            <?= \App\Form::text('Date de la Facture', 'date', 'date', '', true, 255, 'min="' . $dateDebut->format('Y-m-d') . '" max="' . $dateFin->format('Y-m-d') . '"'); ?>
                                        </div>
                                        <div class="col-12 col-lg-3 my-2">
                                            <?= App\Form::select('Nature', 'client_nature', PEOPLE_NATURE, '', true); ?>
                                        </div>
                                        <div class="col-12 col-lg-3 my-2">
                                            <?= App\Form::text('Nom du destinateur', 'client_name', 'text', '', true, 150, 'list="clientsList" autocomplete="off"'); ?>
                                            <?php if ($allClients): ?>
                                                <datalist id="clientsList">
                                                    <?php foreach ($allClients as $client): ?>
                                                        <option value="<?= $client->name; ?>"><?= $client->name; ?></option>
                                                    <?php endforeach; ?>
                                                </datalist>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-12 col-lg-3 my-2">
                                            <?= App\Form::text('Prénom du destinateur', 'client_firstName', 'text', '', false, 150); ?>
                                        </div>
                                    </div>
                                    <hr class="mx-5">
                                    <div id="allFactureProducts">
                                        <div class="row my-2 productFields">
                                            <div class="col-12 col-lg-4 my-2">
                                                <?= \App\Form::text('Nom de l\'article', 'nom_1', 'text', '', true, 255, 'list="coursesList" autocomplete="off"', '', 'form-control-sm'); ?>
                                                <?php if ($allCourses): ?>
                                                    <datalist id="coursesList">
                                                        <?php foreach ($allCourses

                                                        as $cours): ?>
                                                        <option value="<?= $cours->nom; ?>">
                                                            <?php endforeach; ?>
                                                    </datalist>
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-12 col-lg-2 my-2">
                                                <?= \App\Form::text('Quantité', 'quantite_1', 'number', '', true, 255, '', '', 'form-control-sm quantiteField'); ?>
                                            </div>
                                            <div class="col-12 col-lg-2 my-2">
                                                <?= \App\Form::text('Prix unitaire HT', 'prixHTunite_1', 'text', '', true, 255, '', '', 'form-control-sm prixUnitaireField'); ?>
                                            </div>
                                            <div class="col-12 col-lg-2 my-2">
                                                <?= \App\Form::text('Taux de TVA', 'tauxTVA_1', 'text', '', true, 255, '', '', 'form-control-sm'); ?>
                                            </div>
                                            <div class="col-12 col-lg-2 my-2">
                                                <?= \App\Form::text('Total', 'total_1', 'text', '', true, 255, '', '', 'form-control-sm totalField'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-info addProductFields">
                                <i class="fas fa-plus"></i> <?= trans('Ajouter un produit'); ?>
                            </button>
                            <div class="row">
                                <div class="col-12 my-2" id="FormAddMainSuppInfos"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" id="saveMainSupplementaireBtn"
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

                function updateFormAddProductTotal() {

                    $('#allFactureProducts').find('.productFields').each(function (i) {
                        var quantite = parseFloat($(this).find('input.quantiteField').val());
                        var prixUnitaire = parseFloat($(this).find('input.prixUnitaireField').val());

                        if (quantite > 0 && prixUnitaire > 0) {
                            $(this).find('input.totalField').val(financial(quantite * prixUnitaire));
                        }
                    });
                }

                var incrementFieldNumber = 2;
                $('button.addProductFields').on('click', function (event) {
                    event.preventDefault();

                    var $clone = $('.productFields').first().clone();

                    $clone.find('input').each(function (i) {

                        //initialize value
                        $(this).val('');

                        //name change
                        var name = $(this).attr('name').split('_')[0];
                        var newName = name + '_' + incrementFieldNumber;
                        $(this).attr('name', newName);

                        //id change
                        $(this).attr('id', newName);

                        //label change
                        $(this).prev('label').attr('for', newName);
                    });
                    $('#allFactureProducts').append($clone);
                    incrementFieldNumber += 1;
                });

                $('body').on('focus', '#addMainSuppForm input.totalField', function () {

                    var $parent = $(this).closest('.productFields');
                    var quantite = parseFloat($parent.find('input.quantiteField').val());
                    var prixUnitaire = parseFloat($parent.find('input.prixUnitaireField').val());

                    if (quantite > 0 && prixUnitaire > 0) {
                        $parent.find('input.totalField').val(financial(quantite * prixUnitaire));
                    }

                });

                $('.addMainSuppSpan').on('click', function () {

                    $('#addMainSuppForm').find('[name="client_nature"]').val($(this).data('clientnature'));
                    $('#addMainSuppForm').find('[name="client_name"]').val($(this).data('clientname'));
                    $('#addMainSuppForm').find('[name="client_firstName"]').val($(this).data('clientfirstname'));
                });

                $('#modalAddMainSupp').on('shown.bs.modal', function () {
                    $('#addMainSuppForm input[name="date"]').focus();
                });

                $('#addMainSupp').on('click', function () {
                    $('#addMainSuppForm').find('[name="client_nature"]').val('');
                    $('#addMainSuppForm').find('[name="client_name"]').val('');
                    $('#addMainSuppForm').find('[name="client_firstName"]').val('');
                });

                $('#saveMainSupplementaireBtn').on('click', function (event) {
                    event.preventDefault();

                    updateFormAddProductTotal();

                    $('#FormAddMainSuppInfos').hide().html('');
                    busyApp();

                    $.post(
                        '<?= AGAPESHOTES_URL . 'process/ajaxMainSupplementaireProcess.php'; ?>',
                        $('#addMainSuppForm').serialize(),
                        function (data) {
                            if (data === true || data == 'true') {
                                $('#loader').fadeIn(400);
                                location.reload();
                            } else {
                                $('#FormAddMainSuppInfos')
                                    .html('<p class="">' + data + '</p>').show();
                            }
                            availableApp();
                        }
                    )
                });

            });
        </script>

    <?php else: ?>
        <?= getContainerErrorMsg(trans('Cet établissement n\'est pas accessible')); ?>
    <?php endif; ?>
<?php endif; ?>
<?php require('footer.php'); ?>