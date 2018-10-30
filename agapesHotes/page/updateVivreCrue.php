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
        echo getTitle($Page->getName(), $Page->getSlug(), ' de <strong>' . $Etablissement->getNom() . '</strong> du mois de <strong>' . strftime("%B", strtotime(date('Y-m-d'))) . '</strong>');

        //Get Courses
        $Course = new \App\Plugin\AgapesHotes\Courses();
        $Course->setEtablissementId($Etablissement->getId());
        $allCourses = $Course->showAll();

        //Get Vivre crue
        $VivreCrue = new \App\Plugin\AgapesHotes\VivreCrue();
        $VivreCrue->setEtablissementId($Etablissement->getId());

        //Select period
        $dateDebut = new \DateTime(date('Y-m-01'));
        $dateFin = new \DateTime(date('Y-m-t'));
        $allVivresCrue = $VivreCrue->showByDate($dateDebut->format('Y-m-d'), $dateFin->format('Y-m-d'));
        ?>
        <div class="container-fluid">
            <button id="addVivreCrue" type="button" class="btn btn-info btn-sm mb-4" data-toggle="modal"
                    data-target="#modalAddVivreCrue">
                <?= trans('Ajouter une course'); ?>
            </button>
            <div class="row">
                <div class="col-12 col-lg-6">
                    <?php if ($allVivresCrue):
                        $allTvaTotal = groupMultipleKeysObjectsArray($allVivresCrue, 'tauxTVA');
                        $allVivresCrue = groupMultipleKeysObjectsArray($allVivresCrue, 'nom');
                        $totalListPrice = 0;
                        foreach ($allVivresCrue as $nomArticle => $data):
                            $totalCoursePrice = 0;
                            ?>
                            <h6><span class="text-info addCourseSpan" data-toggle="modal"
                                      data-target="#modalAddVivreCrue" data-nomarticle="<?= $nomArticle; ?>"
                                      style="cursor: pointer;"><i class="fas fa-plus"></i></span> <?= $nomArticle; ?>
                            </h6>
                            <ol class="courseListOl">
                                <?php foreach ($data as $achat):
                                    $totalCoursePrice += $achat->total;
                                    ?>
                                    <li>
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

                                <small class="float-right">
                                    <strong>Total</strong> <?= financial($totalCoursePrice); ?>€
                                </small>
                                <hr class="ml-5 mb-3 mr-3">
                            </ol>
                            <?php
                            $totalListPrice += $totalCoursePrice;
                        endforeach; ?>
                        <h6>TOTAL</h6>
                        <ul>
                            <?php
                            foreach ($allTvaTotal as $tva => $data):
                                $totalTva = 0;
                                foreach ($data as $achat) {
                                    $totalTva += $achat->total;
                                }; ?>
                                <li><strong>TVA <?= $tva; ?>%</strong> <?= financial($totalTva); ?>€</li>
                            <?php endforeach; ?>
                            <li><strong>Total HT</strong> <?= financial($totalListPrice); ?>€</li>
                        </ul>
                    <?php endif; ?>
                </div>
                <div class="col-12 col-lg-6"></div>
            </div>
        </div>
        <div class="modal fade" id="modalAddVivreCrue" tabindex="-1" role="dialog"
             aria-labelledby="modalAddVivreCrueTitle"
             aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="" method="post" id="addVivreCrueForm">
                        <div class="modal-header">
                            <h5 class="modal-title"
                                id="modalAddVivreCrueTitle"><?= trans('Ajouter un prestation'); ?></h5>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-12 my-2">
                                    <input type="hidden" name="etablissementId" value="<?= $Etablissement->getId(); ?>">
                                    <?= \App\Form::text('Date de la course', 'date', 'date', !empty($_POST['date']) ? $_POST['date'] : '', true, 255, 'min="' . $dateDebut->format('Y-m-d') . '" max="' . $dateFin->format('Y-m-d') . '"'); ?>
                                    <div class="my-2"></div>
                                    <?= \App\Form::text('Nom de l\'article', 'nom', 'text', !empty($_POST['nom']) ? $_POST['nom'] : '', true, 255, 'list="coursesList" autocomplete="off"'); ?>
                                    <datalist id="coursesList">
                                        <?php if ($allCourses):
                                            foreach ($allCourses as $cours): ?>
                                                <option value="<?= $cours->nom; ?>"><?= $cours->nom; ?></option>
                                            <?php endforeach;
                                        endif; ?>
                                    </datalist>
                                    <div class="row my-2">
                                        <div class="col-12 col-lg-6">
                                            <?= \App\Form::text('Prix unitaire HT', 'prixHTunite', 'text', !empty($_POST['prixHTunite']) ? $_POST['prixHTunite'] : '', true, 255); ?>
                                        </div>
                                        <div class="col-12 col-lg-6">
                                            <?= \App\Form::text('Quantité', 'quantite', 'tel', !empty($_POST['quantite']) ? $_POST['quantite'] : '', true, 255); ?>
                                        </div>
                                    </div>
                                    <div class="row my-2">
                                        <div class="col-12 col-lg-6">
                                            <?= \App\Form::text('Taux de TVA', 'tauxTVA', 'text', !empty($_POST['tauxTVA']) ? $_POST['tauxTVA'] : '', true, 255); ?>
                                        </div>
                                        <div class="col-12 col-lg-6">
                                            <?= \App\Form::text('Total', 'total', 'text', !empty($_POST['total']) ? $_POST['total'] : '', true, 255); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 my-2" id="FormAddVivreCrueInfos"></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <?= \App\Form::target('ADDVIVRECRUE'); ?>
                            <button type="submit" id="saveVivreCrueBtn"
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

                function updateFormAddCourseTotal() {

                    var prixHTunite = $('#addVivreCrueForm input[name="prixHTunite"]');
                    var quantite = $('#addVivreCrueForm input[name="quantite"]');

                    if (prixHTunite.val().length > 0 && $.isNumeric(prixHTunite.val())
                        && quantite.val().length > 0 && $.isNumeric(quantite.val())) {
                        var total = parseFloat(prixHTunite.val()) * parseFloat(quantite.val());
                        $('#addVivreCrueForm input[name="total"]').val(financial(total)).prop('readonly', true);
                    }
                }

                $('#modalAddVivreCrue').on('shown.bs.modal', function () {
                    $('#addVivreCrueForm input[name="date"]').focus();
                    $('#FormAddVivreCrueInfos').hide().html('');
                });

                $('.addCourseSpan').on('click', function () {
                    var nomArticle = $(this).data('nomarticle');
                    var listExemple = $(this).parent('h6').next('ol').children('li').last();

                    $('#addVivreCrueForm input[name="nom"]').val(nomArticle).prop('readonly', true);
                    $('#addVivreCrueForm input[name="quantite"]').val(listExemple.find('span[data-name="quantite"]').text());
                    $('#addVivreCrueForm input[name="prixHTunite"]').val(listExemple.find('span[data-name="prixHTunite"]').text()).prop('readonly', true);
                    $('#addVivreCrueForm input[name="tauxTVA"]').val(listExemple.find('span[data-name="tauxTVA"]').text()).prop('readonly', true);

                    updateFormAddCourseTotal();
                });

                $('#addVivreCrueForm input[name="total"]').on('focus', function () {
                    updateFormAddCourseTotal();
                });

                $('#addVivreCrue').on('click', function () {
                    $('#addVivreCrueForm input[name="nom"]').val('').prop('readonly', false);
                    $('#addVivreCrueForm input[name="quantite"]').val('');
                    $('#addVivreCrueForm input[name="prixHTunite"]').val('').prop('readonly', false);
                    $('#addVivreCrueForm input[name="tauxTVA"]').val('').prop('readonly', false);
                    $('#addVivreCrueForm input[name="total"]').val('').prop('readonly', false);
                });

                $('#saveVivreCrueBtn').on('click', function (event) {
                    event.preventDefault();

                    updateFormAddCourseTotal();
                    $('#FormAddVivreCrueInfos').hide().html('');
                    busyApp();

                    $.post(
                        '<?= AGAPESHOTES_URL . 'process/ajaxVivreCrueProcess.php'; ?>',
                        $('#addVivreCrueForm').serialize(),
                        function (data) {
                            if (data === true || data == 'true') {
                                $('#loader').fadeIn(400);
                                location.reload();
                            } else {
                                $('#FormAddVivreCrueInfos')
                                    .html('<p class="bg-danger text-white">' + data + '</p>').show();
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