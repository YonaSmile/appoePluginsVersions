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
                <div class="col-12">
                    <div class="row">
                        <?php if ($allMainsSupp):
                            $allClientsMainSupp = groupMultipleKeysObjectsArray($allMainsSupp, 'clientName');

                            $totalListPrice = 0;
                            foreach ($allClientsMainSupp as $clientName => $data):
                                $clientNameSlug = slugify($clientName);
                                $allTvaTotal = groupMultipleKeysObjectsArray($data, 'tauxTVA');
                                $totalCoursePrice = 0;
                                ?>
                                <div class="col-12 col-lg-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title"><?= $clientName; ?></h5>
                                            <p class="card-text">Facture rédigé le
                                                <strong><?= displayFrDate(current($data)->date); ?></strong>
                                                <br>
                                                <strong>Total TTC : </strong>
                                                <?php
                                                $allTtcTvaTotal = array();
                                                foreach ($allTvaTotal as $tva => $tvaData) {
                                                    $totalTva = 0;
                                                    foreach ($tvaData as $achatData) {
                                                        $totalTva += $achatData->total;
                                                    };
                                                    $taxe = ($totalTva * ($tva / 100));
                                                    $allTtcTvaTotal[] = $totalTva + $taxe;
                                                } ?><?= financial(array_sum($allTtcTvaTotal)); ?>€
                                            </p>
                                            <button data-clientname="<?= $clientNameSlug; ?>" data-toggle="modal"
                                                    data-target="#modalUpdateMainSupp-<?= $clientNameSlug; ?>"
                                                    class="btn btn-info text-white float-right updateMainSupp"
                                                    title="<?= trans('Éditer'); ?>">Éditer
                                            </button>
                                        </div>
                                    </div>
                                    <div class="modal fade" id="modalUpdateMainSupp-<?= $clientNameSlug; ?>"
                                         tabindex="-1" role="dialog"
                                         aria-labelledby="modalUpdateMainSuppTitle-<?= $clientNameSlug; ?>"
                                         aria-hidden="true">
                                        <div class="modal-dialog modal-lg" role="document">
                                            <div class="modal-content">
                                                <form action="" method="post"
                                                      id="updateMainSuppForm-<?= $clientNameSlug; ?>">
                                                    <?= getTokenField(); ?>
                                                    <input type="hidden" name="siteId"
                                                           value="<?= $Site->getId(); ?>">
                                                    <?= \App\Form::target('UPDATEMAINSUPPLEMENTAIRE'); ?>
                                                    <div class="modal-header">
                                                        <h5 class="modal-title"
                                                            id="modalUpdateMainSuppTitle-<?= $clientNameSlug; ?>">
                                                            <?= trans('Demande de facturation'); ?></h5>
                                                        <small>HORS CONTRAT</small>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-12 my-2">
                                                                <div class="row my-2">
                                                                    <div class="col-4 my-2">
                                                                        <?= \App\Form::text('Émetteur', 'emetteur', 'texte', 'Les Agapes Hôtes, ' . $Site->getNom(), true, 255, 'disabled="disabled" readonly', '', 'basePrint'); ?>
                                                                    </div>
                                                                    <div class="col-4 my-2">
                                                                        <?= \App\Form::text('Date de la Facture', 'date', 'date', current($data)->date, true, 255, 'min="' . $dateDebut->format('Y-m-d') . '" max="' . $dateFin->format('Y-m-d') . '"', '', 'basePrint'); ?>
                                                                    </div>
                                                                    <div class="col-4 my-2">
                                                                        <?= App\Form::text('Destinataire', 'client_name', 'text', $clientName, true, 150, 'list="etablissementList" autocomplete="off"', '', 'basePrint'); ?>
                                                                        <?php if ($allEtablissements): ?>
                                                                            <datalist id="etablissementList">
                                                                                <?php foreach ($allEtablissements as $etablissement): ?>
                                                                                    <option value="<?= $etablissement->nom; ?>"><?= $etablissement->nom; ?></option>
                                                                                <?php endforeach; ?>
                                                                            </datalist>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                </div>
                                                                <hr class="mx-5">
                                                                <div class="d-xs-none d-sm-block">
                                                                    <div class="row my-1 infoTable">
                                                                        <div class="col-3"><strong>Produit</strong>
                                                                        </div>
                                                                        <div class="col-1"><strong>Qté</strong></div>
                                                                        <div class="col-2"><strong>Prix/unité
                                                                                HT</strong></div>
                                                                        <div class="col-2"><strong>Total HT</strong>
                                                                        </div>
                                                                        <div class="col-2"><strong>TVA</strong></div>
                                                                        <div class="col-2"><strong>Total TTC</strong>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="allFactureProducts">
                                                                    <?php $c = 1;
                                                                    foreach ($data as $achat):
                                                                        $totalCoursePrice += $achat->total;
                                                                        $taxe = ($achat->total * ($achat->tauxTVA / 100)); ?>
                                                                        <input type="hidden" name="id_<?= $c; ?>"
                                                                               value="<?= $achat->id; ?>"
                                                                               class="mainSuppIdInput">
                                                                        <div class="row my-1 productFields positionRelative">
                                                                            <span style="position: absolute; top: 25%;left: 5px;z-index: 999;cursor: pointer;"
                                                                                  class="text-danger deleteAchat"
                                                                                  data-idachat="<?= $achat->id; ?>"
                                                                                  data-clientname="<?= $clientNameSlug; ?>">
                                                                                <i class="fas fa-ban"></i></span>
                                                                            <div class="col-3 my-1">
                                                                                <?= \App\Form::text('Nom de l\'article', 'nom_' . $c, 'text', $achat->nom, true, 255, 'list="coursesList" autocomplete="off"', '', 'form-control-sm', 'Nom de l\'article'); ?>
                                                                                <?php if ($allCourses): ?>
                                                                                    <datalist id="coursesList">
                                                                                        <?php foreach ($allCourses as $cours): ?>
                                                                                            <option value="<?= $cours->nom; ?>"><?= $cours->nom; ?></option>
                                                                                        <?php endforeach; ?>
                                                                                    </datalist>
                                                                                <?php endif; ?>
                                                                            </div>
                                                                            <div class="col-1 my-1">
                                                                                <?= \App\Form::text('Qté', 'quantite_' . $c, 'number', $achat->quantite, true, 255, '', '', 'form-control-sm quantiteField', 'Quantité'); ?>
                                                                            </div>
                                                                            <div class="col-2 my-1">
                                                                                <?= \App\Form::text('Prix/unité HT', 'prixHTunite_' . $c, 'text', $achat->prixHTunite, true, 255, '', '', 'form-control-sm prixUnitaireField', 'Prix unitaire HT'); ?>
                                                                            </div>
                                                                            <div class="col-2 my-1">
                                                                                <?= \App\Form::text('Total HT', 'total_' . $c, 'text', $achat->total, true, 255, 'readonly', '', 'form-control-sm totalField', 'Total HT'); ?>
                                                                            </div>
                                                                            <div class="col-2 my-1">
                                                                                <?= \App\Form::text('TVA', 'tauxTVA_' . $c, 'text', $achat->tauxTVA, true, 255, '', '', 'form-control-sm tvaField', 'TVA (%)'); ?>
                                                                            </div>
                                                                            <div class="col-2 my-1">
                                                                                <?= \App\Form::text('Total TTC', 'totalTtc_' . $c, 'text', financial($achat->total + $taxe), true, 255, 'readonly', '', 'form-control-sm totalTtcField', 'Total TTC'); ?>
                                                                            </div>
                                                                            <hr class="mx-auto my-1 w-25 d-md-block d-lg-none">
                                                                        </div>
                                                                        <?php $c++;
                                                                    endforeach; ?>
                                                                </div>
                                                                <button type="button"
                                                                        data-clientname="<?= $clientNameSlug; ?>"
                                                                        class="btn btn-sm btn-info addProductFields">
                                                                    <i class="fas fa-plus"></i> <?= trans('Ajouter un produit'); ?>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-12 my-2 formUpdateMainSuppInfos"
                                                                 data-clientname="<?= $clientNameSlug; ?>"></div>
                                                        </div>
                                                        <div class="text-right totalContainer-<?= $clientNameSlug; ?>">
                                                            <?php
                                                            $allTtcTvaTotal = array();
                                                            foreach ($allTvaTotal as $tva => $tvaData):
                                                                $totalTva = 0;
                                                                foreach ($tvaData as $achatData) {
                                                                    $totalTva += $achatData->total;
                                                                };
                                                                $taxe = ($totalTva * ($tva / 100));
                                                                $allTtcTvaTotal[] = $totalTva + $taxe; ?>
                                                                <strong>TOTAL HT TVA <?= $tva; ?>
                                                                    %</strong> <?= financial($totalTva); ?>€
                                                                <br>
                                                            <?php endforeach; ?>
                                                            <strong>
                                                                TOTAL HT</strong> <?= financial($totalCoursePrice); ?>€
                                                            <br>
                                                            <strong>TOTAL
                                                                TTC</strong> <?= financial(array_sum($allTtcTvaTotal)); ?>
                                                            €
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="submit"
                                                                class="updateMainSupplementaireBtn btn btn-primary"
                                                                data-clientname="<?= $clientNameSlug; ?>"><?= trans('Enregistrer'); ?></button>
                                                        <button type="button"
                                                                class="printFacture btn btn-warning"
                                                                data-clientname="<?= $clientNameSlug; ?>"><?= trans('Imprimer'); ?></button>
                                                        <button type="button" class="btn btn-secondary"
                                                                data-dismiss="modal"><?= trans('Fermer'); ?></button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach;
                        endif; ?>
                    </div>
                </div>
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
                                        <div class="col-12 col-lg-2 my-2">
                                            <?= \App\Form::text('Date de la Facture', 'date', 'date', '', true, 255, 'min="' . $dateDebut->format('Y-m-d') . '" max="' . $dateFin->format('Y-m-d') . '"'); ?>
                                        </div>
                                        <div class="col-12 col-lg-3 my-2">
                                            <?= App\Form::text('Destinataire', 'client_name', 'text', '', true, 150, 'list="etablissementList" autocomplete="off"'); ?>
                                            <?php if ($allEtablissements): ?>
                                                <datalist id="etablissementList">
                                                    <?php foreach ($allEtablissements as $etablissement): ?>
                                                        <option value="<?= $etablissement->nom; ?>"><?= $etablissement->nom; ?></option>
                                                    <?php endforeach; ?>
                                                </datalist>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <hr class="mx-5">
                                    <div class="d-md-none d-lg-block">
                                        <div class="row my-1 infoTable">
                                            <div class="col-lg-3"><strong>Nom de l'article</strong></div>
                                            <div class="col-lg-1"><strong>Quantité</strong></div>
                                            <div class="col-lg-2"><strong>Prix unitaire HT</strong></div>
                                            <div class="col-lg-2"><strong>Total HT</strong></div>
                                            <div class="col-lg-2"><strong>TVA(%)</strong></div>
                                            <div class="col-lg-2"><strong>Total TTC</strong></div>
                                        </div>
                                    </div>
                                    <div class="allFactureProducts">
                                        <?php for ($c = 1; $c <= 5; $c++): ?>
                                            <div class="row my-1 productFields">
                                                <div class="col-12 col-lg-3 my-1">
                                                    <?= \App\Form::text('Nom de l\'article', 'nom_' . $c, 'text', '', true, 255, 'list="coursesList" autocomplete="off"', '', 'form-control-sm', 'Nom de l\'article'); ?>
                                                    <?php if ($allCourses): ?>
                                                        <datalist id="coursesList">
                                                            <?php foreach ($allCourses as $cours): ?>
                                                                <option value="<?= $cours->nom; ?>"><?= $cours->nom; ?></option>
                                                            <?php endforeach; ?>
                                                        </datalist>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="col-12 col-lg-1 my-1">
                                                    <?= \App\Form::text('Quantité', 'quantite_' . $c, 'number', '', true, 255, '', '', 'form-control-sm quantiteField', 'Quantité'); ?>
                                                </div>
                                                <div class="col-12 col-lg-2 my-1">
                                                    <?= \App\Form::text('Prix unitaire HT', 'prixHTunite_' . $c, 'text', '', true, 255, '', '', 'form-control-sm prixUnitaireField', 'Prix unitaire HT'); ?>
                                                </div>
                                                <div class="col-12 col-lg-2 my-1">
                                                    <?= \App\Form::text('Total HT', 'total_' . $c, 'text', '', true, 255, 'readonly', '', 'form-control-sm totalField', 'Total HT'); ?>
                                                </div>
                                                <div class="col-12 col-lg-2 my-1">
                                                    <?= \App\Form::text('TVA (%)', 'tauxTVA_' . $c, 'text', '', true, 255, '', '', 'form-control-sm tvaField', 'TVA (%)'); ?>
                                                </div>
                                                <div class="col-12 col-lg-2 my-1">
                                                    <?= \App\Form::text('Total TTC', 'totalTtc_' . $c, 'text', '', true, 255, 'readonly', '', 'form-control-sm totalTtcField', 'Total TTC'); ?>
                                                </div>
                                                <hr class="mx-auto my-1 w-25 d-md-block d-lg-none">
                                            </div>
                                        <?php endfor; ?>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-info addProductFields">
                                        <i class="fas fa-plus"></i> <?= trans('Ajouter un produit'); ?>
                                    </button>
                                </div>
                            </div>
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
        <script type="text/javascript" src="/app/js/printThis.js"></script>
        <script>
            $(document).ready(function () {

                function updateFormAddProductTotal() {

                    $('.allFactureProducts').find('.productFields').each(function (i) {
                        var $parent = $(this);
                        var quantite = parseFloat($parent.find('input.quantiteField').val());
                        var prixUnitaire = parseFloat($parent.find('input.prixUnitaireField').val());

                        if (quantite > 0 && prixUnitaire > 0) {
                            var totalHT = parseFloat(quantite * prixUnitaire);
                            $parent.find('input.totalField').val(financial(totalHT));

                            var tauxTva = parseFloat($parent.find('input.tvaField').val());

                            if (tauxTva > 0) {
                                if (tauxTva == 5.5 || tauxTva == 10 || tauxTva == 20) {
                                    var taxe = (totalHT * (tauxTva / 100));
                                    var totalTtc = parseFloat(totalHT + taxe);
                                    $parent.find('input.totalTtcField').val(financial(totalTtc));
                                } else {
                                    $parent.find('input.tvaField').val('');
                                    alert('Le taux de tva ne peut être que : 5.50 / 10.00 / 20.00');
                                }
                            }
                        }


                    });
                }

                function removeTotalContainer($btn) {
                    if (typeof $btn.data('clientname') !== undefined) {
                        var clientName = $btn.data('clientname');
                        var $form = $btn.closest('form');
                        $form.find('.totalContainer-' + clientName).remove();
                    }
                }

                function removeAddProductFieldsBtn($btn) {
                    if (typeof $btn.data('clientname') !== undefined) {
                        var clientName = $btn.data('clientname');
                        var $form = $btn.closest('form');
                        $form.find('button.addProductFields[data-clientname="' + clientName + '"]').remove();
                    }
                }

                function prepareFacture($element) {

                    var $newParent = $element.clone();
                    var $form = $newParent.find('form');

                    $('input[type="hidden"]', $form).remove();

                    $('input', $form).each(function () {

                        var $input = $(this);
                        var inputType = $input.attr('name');
                        var inputVal = $input.val();
                        var $parent = $input.parent();

                        $input.remove();

                        if ($input.hasClass('basePrint')) {
                            $parent.append('<br>' + inputVal);
                        } else {

                            if (
                                inputType.match("^prixHTunite_")
                                || inputType.match("^total_")
                                || inputType.match("^totalTtc_")) {
                                $parent.html(inputVal + '€');
                            } else if (inputType.match("^tauxTVA_")) {
                                $parent.html(inputVal + '%');
                            } else {
                                $parent.html(inputVal);
                            }
                        }
                    });

                    $('.productFields hr, datalist, .deleteAchat, button, .modal-footer', $form).remove();

                    return $newParent;

                }

                $('button.printFacture').on('click', function () {

                    var $btn = $(this);
                    var clientName = $btn.data('clientname');
                    var $parent = $btn.closest('div#modalUpdateMainSupp-' + clientName);

                    var $newParent = prepareFacture($parent);
                    $newParent.printThis({
                        loadCSS: "<?= AGAPESHOTES_URL; ?>css/print.css",
                    });
                });

                var lastModifiedModalId = '';

                $('button.addProductFields').on('click', function (event) {
                    event.preventDefault();

                    var $btn = $(this);
                    removeTotalContainer($btn);

                    var $formContainer = $btn.prev('.allFactureProducts');


                    var incrementFieldNumber = $formContainer.find('.productFields').length + 1;
                    var $clone = $('.productFields').first().clone();
                    $clone.find('span.deleteAchat').remove();
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
                    $formContainer.append($clone);
                    incrementFieldNumber += 1;
                });

                $('body').on('focus', 'input.totalField, input.totalTtcField', function () {
                    updateFormAddProductTotal();
                });

                $('#modalAddMainSupp').on('shown.bs.modal', function () {
                    $('#addMainSuppForm input[name="date"]').focus();
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
                    );
                });

                $('.updateMainSupplementaireBtn').on('click', function (event) {
                    event.preventDefault();
                    var $btn = $(this);
                    var clientName = $btn.data('clientname');
                    var $form = $btn.closest('form#updateMainSuppForm-' + clientName);
                    updateFormAddProductTotal();

                    $('.formUpdateMainSuppInfos[data-clientname="' + clientName + '"]').hide().html('');
                    busyApp();

                    $.post(
                        '<?= AGAPESHOTES_URL . 'process/ajaxMainSupplementaireProcess.php'; ?>',
                        $form.serialize(),
                        function (data) {
                            if (data === true || data == 'true') {
                                $('#loader').fadeIn(400);
                                location.reload();
                            } else {
                                $('.formUpdateMainSuppInfos[data-clientname="' + clientName + '"]')
                                    .html('<p class="">' + data + '</p>').show();
                            }
                            availableApp();
                        }
                    );
                });

                $('.deleteAchat').on('click', function (event) {
                    event.preventDefault();
                    var $btn = $(this);
                    var idAchat = $btn.data('idachat');
                    var clientName = $btn.data('clientname');

                    busyApp();
                    removeTotalContainer($btn);
                    removeAddProductFieldsBtn($btn);

                    $.post(
                        '<?= AGAPESHOTES_URL . 'process/ajaxMainSupplementaireProcess.php'; ?>',
                        {
                            DELETEACHAT: 'OK',
                            idAchat: idAchat
                        },
                        function (data) {
                            if (data === true || data == 'true') {
                                $btn.parent('div.productFields').prev('input.mainSuppIdInput').remove();
                                $btn.parent('div.productFields').slideUp(200).remove();
                                lastModifiedModalId = 'modalUpdateMainSupp-' + clientName;
                            } else {
                                $('.formUpdateMainSuppInfos[data-clientname="' + clientName + '"]')
                                    .html('<p class="">' + data + '</p>').show();
                            }
                            availableApp();
                        }
                    );
                });

                $('.modal').on('hide.bs.modal', function (e) {

                    if ($(this).attr('id') == lastModifiedModalId) {
                        $('#loader').fadeIn(400);
                        location.reload();
                    }
                });
            });
        </script>

    <?php else: ?>
        <?= getContainerErrorMsg(trans('Cet établissement n\'est pas accessible')); ?>
    <?php endif; ?>
<?php endif; ?>
<?php require('footer.php'); ?>