<?php
$Site = new \App\Plugin\AgapesHotes\Site();
$allSites = $Site->showAll();
$allOrdSites = extractFromObjToSimpleArr($allSites, 'id', 'secteur_id');
array_unique($allOrdSites);
$allSitesBySecteur = groupMultipleKeysObjectsArray($allSites, 'secteur_id');
$Secteur = new \App\Plugin\AgapesHotes\Secteur();
?>
<h4>Synthèse (en € )</h4>
<div class="row mb-3">
    <div class="col-12 positionRelative">
        <div class="table-responsive">
            <table class="table table-striped tableNonEffect text-center" id="totalSyntheseTable">
                <thead>
                <tr>
                    <th></th>
                    <th colspan="2"><?= trans('Chiffre d\'affaires'); ?></th>
                    <th><?= trans('Consommation'); ?></th>
                    <th><?= trans('Personnel'); ?></th>
                    <th><?= trans('Frais généraux'); ?></th>
                    <th><?= trans('Résultat d\'exploitation'); ?></th>
                    <th><?= trans('Retour achat 6.5%'); ?></th>
                    <th><?= trans('Frais de siège 4%'); ?></th>
                    <th><?= trans('Résultats'); ?></th>
                    <th><?= trans('Rentabilité (%)'); ?></th>
                </tr>
                </thead>
                <tbody>
                <tr data-name="totalrow-budget">
                    <th rowspan="3"
                        style="vertical-align:middle;">Tous les sites
                    </th>
                    <td style="text-align:left !important;">
                        <small><em>Budget</em></small>
                    </td>
                    <td data-name="budget-ca"></td>
                    <td data-name="budget-conso"></td>
                    <td data-name="budget-perso"></td>
                    <td data-name="budget-fraisgeneraux"></td>
                    <td data-name="budget-resultatexploitation"></td>
                    <td data-name="budget-retourachat"></td>
                    <td data-name="budget-fraissiege"></td>
                    <td data-name="budget-resultats"></td>
                    <td style="text-align:center !important;" data-name="budget-pourcentagesrentabilite"></td>
                </tr>
                <tr data-name="totalrow-siteyearago">
                    <td style="text-align:left !important;">
                        <small><em><?= date('Y') - 1; ?></em></small>
                    </td>
                    <td data-name="site-facturation"></td>
                    <td data-name="site-consoreel-denree"></td>
                    <td data-name="site-meta-fraisperso"></td>
                    <td data-name="site-fraisgeneraux"></td>
                    <td data-name="site-resultatexploitation"></td>
                    <td data-name="site-retourachat"></td>
                    <td data-name="site-fraissiege"></td>
                    <td data-name="site-resultats"></td>
                    <td style="text-align:center !important;" data-name="site-pourcentagesrentabilite"></td>
                </tr>
                <tr data-name="totalrow-site">
                    <td style="text-align:left !important;">
                        <small><em><?= date('Y'); ?></em></small>
                    </td>
                    <td data-name="site-facturation"></td>
                    <td data-name="site-consoreel-denree"></td>
                    <td data-name="site-meta-fraisperso"></td>
                    <td data-name="site-fraisgeneraux"></td>
                    <td data-name="site-resultatexploitation"></td>
                    <td data-name="site-retourachat"></td>
                    <td data-name="site-fraissiege"></td>
                    <td data-name="site-resultats"></td>
                    <td style="text-align:center !important;" data-name="site-pourcentagesrentabilite"></td>
                </tr>
                </tbody>
            </table>
        </div>
        <hr class="my-4">
        <div class="table-responsive">
            <table id="syntheseSecteurTable" class="table table-striped tableNonEffect text-center fixed-header">
                <thead>
                <tr>
                    <th><?= trans('Site'); ?></th>
                    <th colspan="2"><?= trans('Chiffre d\'affaires'); ?></th>
                    <th><?= trans('Consommation'); ?></th>
                    <th><?= trans('Personnel'); ?></th>
                    <th><?= trans('Frais généraux'); ?></th>
                    <th><?= trans('Résultat d\'exploitation'); ?></th>
                    <th><?= trans('Retour achat 6.5%'); ?></th>
                    <th><?= trans('Frais de siège 4%'); ?></th>
                    <th><?= trans('Résultats'); ?></th>
                    <th><?= trans('Pourcentages de rentabilité'); ?></th>
                </tr>
                </thead>
                <tbody>

                <?php foreach ($allSitesBySecteur as $secteurId => $allSites):
                    $Secteur->setId($secteurId);
                    if ($Secteur->show() && $Secteur->getId() > 0):
                        ?>
                        <tr>
                            <th style="text-align:center !important; background:#4fb99f;color:#fff;"
                                colspan="11"><?= $Secteur->getNom(); ?></th>
                        </tr>
                        <?php foreach ($allSites as $site): ?>
                        <tr data-siteid="<?= $site->id; ?>" data-name="budgetrow">
                            <th rowspan="3"
                                style="vertical-align:middle;"><?= $site->nom ?></th>
                            <td style="text-align:left !important;">
                                <small><em>Budget</em></small>
                            </td>
                            <td nowrap data-name="budget-ca"></td>
                            <td data-name="budget-conso"></td>
                            <td data-name="budget-perso"></td>
                            <td data-name="budget-fraisgeneraux"></td>
                            <td data-name="budget-resultatexploitation"></td>
                            <td data-name="budget-retourachat"></td>
                            <td data-name="budget-fraissiege"></td>
                            <td data-name="budget-resultats"></td>
                            <td style="text-align:center !important;" data-name="budget-pourcentagesrentabilite"></td>
                        </tr>
                        <tr data-siteid="<?= $site->id; ?>" data-name="siterowYearAgo">
                            <td style="text-align:left !important;">
                                <small><em><?= date('Y') - 1; ?></em></small>
                            </td>
                            <td nowrap data-name="site-facturation"></td>
                            <td data-name="site-consoreel-denree"></td>
                            <td data-name="site-meta-fraisperso"></td>
                            <td data-name="site-fraisgeneraux"></td>
                            <td data-name="site-resultatexploitation"></td>
                            <td data-name="site-retourachat"></td>
                            <td data-name="site-fraissiege"></td>
                            <td data-name="site-resultats"></td>
                            <td style="text-align:center !important;" data-name="site-pourcentagesrentabilite"></td>
                        </tr>
                        <tr data-siteid="<?= $site->id; ?>" data-name="siterow">
                            <td style="text-align:left !important;">
                                <small><em><?= date('Y'); ?></em></small>
                            </td>
                            <td nowrap data-name="site-facturation"></td>
                            <td data-name="site-consoreel-denree"></td>
                            <td data-name="site-meta-fraisperso"></td>
                            <td data-name="site-fraisgeneraux"></td>
                            <td data-name="site-resultatexploitation"></td>
                            <td data-name="site-retourachat"></td>
                            <td data-name="site-fraissiege"></td>
                            <td data-name="site-resultats"></td>
                            <td style="text-align:center !important;" data-name="site-pourcentagesrentabilite"></td>
                        </tr>
                    <?php endforeach;
                    endif;
                endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {

        var allSites = <?= json_encode($allOrdSites); ?>;
        var countSites = (Object.keys(allSites).length * 2);
        var confirmedSites = 0;

        var budgetCa = 0, budgetConso = 0, budgetPerso = 0, budgetFraisGeneraux = 0, budgetResultatsExploitation = 0,
            budgetretoruAchat = 0, budgetFraisSiege = 0, budgetResultats = 0, budgetPourcentageRentabilite = 0;

        var facturation = 0, consoreelDenree = 0, fraisperso = 0, fraisgeneraux = 0, resultatexploitation = 0,
            retourachat = 0, fraissiege = 0, resultats = 0, pourcentagesrentabilite = 0;

        var facturationAgo = 0, consoreelDenreeAgo = 0, fraispersoAgo = 0, fraisgenerauxAgo = 0,
            resultatexploitationAgo = 0,
            retourachatAgo = 0, fraissiegeAgo = 0, resultatsAgo = 0, pourcentagesrentabiliteAgo = 0;

        $('html').css('overflow', 'hidden');

        $.each(allSites, function (site, secteur) {
            if (secteur > 0) {
                $.post(
                    '<?= AGAPESHOTES_URL . 'page/getAllSiteData.php'; ?>',
                    {
                        siteId: site,
                        year: '<?= date('Y'); ?>'
                    },
                    function (data) {
                        if (data) {
                            var parsedData = jQuery.parseJSON(data);
                            var $siteData = parsedData[secteur][site];

                            var $trSite = $('tr[data-siteid="' + site + '"][data-name="siterow"]');
                            var $trBudget = $('tr[data-siteid="' + site + '"][data-name="budgetrow"]');
                            var $trTotalBudget = $('tr[data-name="totalrow-budget"]');
                            var $trTotalSite = $('tr[data-name="totalrow-site"]');

                            /**
                             * Budget
                             * @type {number}
                             */
                            $trBudget.find('td[data-name="budget-ca"]').text($siteData.budget.ca ? financial($siteData.budget.ca) : 0);
                            $trBudget.find('td[data-name="budget-conso"]').text($siteData.budget.conso ? financial($siteData.budget.conso) : 0);
                            $trBudget.find('td[data-name="budget-perso"]').text($siteData.budget.personnel ? financial($siteData.budget.personnel) : 0);
                            $trBudget.find('td[data-name="budget-fraisgeneraux"]').text($siteData.budget.fraisGeneraux ? financial($siteData.budget.fraisGeneraux) : 0);
                            $trBudget.find('td[data-name="budget-resultatexploitation"]').text($siteData.budget.resultatExploitation ? financial($siteData.budget.resultatExploitation) : 0);
                            $trBudget.find('td[data-name="budget-retourachat"]').text($siteData.budget.retourAchat ? financial($siteData.budget.retourAchat) : 0);
                            $trBudget.find('td[data-name="budget-fraissiege"]').text($siteData.budget.retourFraisSiege ? financial($siteData.budget.retourFraisSiege) : 0);
                            $trBudget.find('td[data-name="budget-resultats"]').text($siteData.budget.resultats ? financial($siteData.budget.resultats) : 0);
                            $trBudget.find('td[data-name="budget-pourcentagesrentabilite"]').text($siteData.budget.conso > 0 ? financial($siteData.budget.resultats / $siteData.budget.ca) : 0);

                            budgetCa += $.isNumeric($siteData.budget.ca) ? parseReelFloat($siteData.budget.ca) : 0;
                            budgetConso += $.isNumeric($siteData.budget.conso) ? parseReelFloat($siteData.budget.conso) : 0;
                            budgetPerso += $.isNumeric($siteData.budget.personnel) ? parseReelFloat($siteData.budget.personnel) : 0;
                            budgetFraisGeneraux += $.isNumeric($siteData.budget.fraisGeneraux) ? parseReelFloat($siteData.budget.fraisGeneraux) : 0;
                            budgetResultatsExploitation += $.isNumeric($siteData.budget.resultatExploitation) ? parseReelFloat($siteData.budget.resultatExploitation) : 0;
                            budgetretoruAchat += $.isNumeric($siteData.budget.retourAchat) ? parseReelFloat($siteData.budget.retourAchat) : 0;
                            budgetFraisSiege += $.isNumeric($siteData.budget.retourFraisSiege) ? parseReelFloat($siteData.budget.retourFraisSiege) : 0;
                            budgetResultats += $.isNumeric($siteData.budget.resultats) ? parseReelFloat($siteData.budget.resultats) : 0;
                            budgetPourcentageRentabilite += $.isNumeric($siteData.budget.conso) && $siteData.budget.conso > 0 ? parseReelFloat($siteData.budget.resultats / $siteData.budget.ca) : 0;

                            $trTotalBudget.find('td[data-name="budget-ca"]').html(financial(budgetCa));
                            $trTotalBudget.find('td[data-name="budget-conso"]').html(financial(budgetConso));
                            $trTotalBudget.find('td[data-name="budget-perso"]').html(financial(budgetPerso));
                            $trTotalBudget.find('td[data-name="budget-fraisgeneraux"]').html(financial(budgetFraisGeneraux));
                            $trTotalBudget.find('td[data-name="budget-resultatexploitation"]').html(financial(budgetResultatsExploitation));
                            $trTotalBudget.find('td[data-name="budget-retourachat"]').html(financial(budgetretoruAchat));
                            $trTotalBudget.find('td[data-name="budget-fraissiege"]').html(financial(budgetFraisSiege));
                            $trTotalBudget.find('td[data-name="budget-resultats"]').html(financial(budgetResultats));
                            $trTotalBudget.find('td[data-name="budget-pourcentagesrentabilite"]').html(financial(budgetPourcentageRentabilite));

                            /**
                             * Current year
                             * @type {number}
                             */
                            $trSite.find('td[data-name="site-facturation"]').html(financial($siteData['facturation']));
                            $trSite.find('td[data-name="site-consoreel-denree"]').html(financial($siteData['consoReel'].denree));
                            $trSite.find('td[data-name="site-meta-fraisperso"]').html(financial($siteData['siteMeta'].fraisDePersonnel));
                            $trSite.find('td[data-name="site-fraisgeneraux"]').html(financial($siteData['fraisGeneraux']));
                            $trSite.find('td[data-name="site-resultatexploitation"]').html(financial($siteData['resultatExploitation']));
                            $trSite.find('td[data-name="site-retourachat"]').html(financial($siteData['retourAchat']));
                            $trSite.find('td[data-name="site-fraissiege"]').html(financial($siteData['fraisDeSiege']));
                            $trSite.find('td[data-name="site-resultats"]').html(financial($siteData['resultats']));
                            $trSite.find('td[data-name="site-pourcentagesrentabilite"]').html(financial($siteData['pourcentagesDeRentabilite']) + '%');

                            facturation += $.isNumeric($siteData['facturation']) ? parseReelFloat($siteData['facturation']) : 0;
                            consoreelDenree += $.isNumeric($siteData['consoReel'].denree) ? parseReelFloat($siteData['consoReel'].denree) : 0;
                            fraisperso += $.isNumeric($siteData['siteMeta'].fraisDePersonnel) ? parseReelFloat($siteData['siteMeta'].fraisDePersonnel) : 0;
                            fraisgeneraux += $.isNumeric($siteData['fraisGeneraux']) ? parseReelFloat($siteData['fraisGeneraux']) : 0;
                            resultatexploitation += $.isNumeric($siteData['resultatExploitation']) ? parseReelFloat($siteData['resultatExploitation']) : 0;
                            retourachat += $.isNumeric($siteData['retourAchat']) ? parseReelFloat($siteData['retourAchat']) : 0;
                            fraissiege += $.isNumeric($siteData['fraisDeSiege']) ? parseReelFloat($siteData['fraisDeSiege']) : 0;
                            resultats += $.isNumeric($siteData['resultats']) ? parseReelFloat($siteData['resultats']) : 0;
                            pourcentagesrentabilite += $.isNumeric($siteData['pourcentagesDeRentabilite']) ? parseReelFloat($siteData['pourcentagesDeRentabilite']) : 0;

                            $trTotalSite.find('td[data-name="site-facturation"]').html(financial(facturation));
                            $trTotalSite.find('td[data-name="site-consoreel-denree"]').html(financial(consoreelDenree));
                            $trTotalSite.find('td[data-name="site-meta-fraisperso"]').html(financial(fraisperso));
                            $trTotalSite.find('td[data-name="site-fraisgeneraux"]').html(financial(fraisgeneraux));
                            $trTotalSite.find('td[data-name="site-resultatexploitation"]').html(financial(resultatexploitation));
                            $trTotalSite.find('td[data-name="site-retourachat"]').html(financial(retourachat));
                            $trTotalSite.find('td[data-name="site-fraissiege"]').html(financial(fraissiege));
                            $trTotalSite.find('td[data-name="site-resultats"]').html(financial(resultats));
                            $trTotalSite.find('td[data-name="site-pourcentagesrentabilite"]').html(financial(pourcentagesrentabilite) + '%');

                            confirmedSites++;
                        }
                    }
                );
            }
        });

        $.each(allSites, function (site, secteur) {
            if (secteur > 0) {
                $.post(
                    '<?= AGAPESHOTES_URL . 'page/getAllSiteData.php'; ?>',
                    {
                        siteId: site,
                        year: '<?= date('Y') - 1; ?>'
                    },
                    function (data) {
                        if (data) {
                            var parsedData = jQuery.parseJSON(data);
                            var $trSite = $('tr[data-siteid="' + site + '"][data-name="siterowYearAgo"]');
                            var $trTotalSiteYearAgo = $('tr[data-name="totalrow-siteyearago"]');

                            var $siteData = parsedData[secteur][site];
                            $trSite.find('td[data-name="site-facturation"]').html($siteData['facturation']);
                            $trSite.find('td[data-name="site-consoreel-denree"]').html($siteData['consoReel'].denree);
                            $trSite.find('td[data-name="site-meta-fraisperso"]').html($siteData['siteMeta'].fraisDePersonnel);
                            $trSite.find('td[data-name="site-fraisgeneraux"]').html($siteData['fraisGeneraux']);
                            $trSite.find('td[data-name="site-resultatexploitation"]').html($siteData['resultatExploitation']);
                            $trSite.find('td[data-name="site-retourachat"]').html($siteData['retourAchat']);
                            $trSite.find('td[data-name="site-fraissiege"]').html($siteData['fraisDeSiege']);
                            $trSite.find('td[data-name="site-resultats"]').html($siteData['resultats']);
                            $trSite.find('td[data-name="site-pourcentagesrentabilite"]').html($siteData['pourcentagesDeRentabilite'] + '%');

                            facturationAgo += $.isNumeric($siteData['facturation']) ? parseReelFloat($siteData['facturation']) : 0;
                            consoreelDenreeAgo += $.isNumeric($siteData['consoReel'].denree) ? parseReelFloat($siteData['consoReel'].denree) : 0;
                            fraispersoAgo += $.isNumeric($siteData['siteMeta'].fraisDePersonnel) ? parseReelFloat($siteData['siteMeta'].fraisDePersonnel) : 0;
                            fraisgenerauxAgo += $.isNumeric($siteData['fraisGeneraux']) ? parseReelFloat($siteData['fraisGeneraux']) : 0;
                            resultatexploitationAgo += $.isNumeric($siteData['resultatExploitation']) ? parseReelFloat($siteData['resultatExploitation']) : 0;
                            retourachatAgo += $.isNumeric($siteData['retourAchat']) ? parseReelFloat($siteData['retourAchat']) : 0;
                            fraissiegeAgo += $.isNumeric($siteData['fraisDeSiege']) ? parseReelFloat($siteData['fraisDeSiege']) : 0;
                            resultatsAgo += $.isNumeric($siteData['resultats']) ? parseReelFloat($siteData['resultats']) : 0;
                            pourcentagesrentabiliteAgo += $.isNumeric($siteData['pourcentagesDeRentabilite']) ? parseReelFloat($siteData['pourcentagesDeRentabilite']) : 0;

                            $trTotalSiteYearAgo.find('td[data-name="site-facturation"]').html(financial(facturationAgo));
                            $trTotalSiteYearAgo.find('td[data-name="site-consoreel-denree"]').html(financial(consoreelDenreeAgo));
                            $trTotalSiteYearAgo.find('td[data-name="site-meta-fraisperso"]').html(financial(fraispersoAgo));
                            $trTotalSiteYearAgo.find('td[data-name="site-fraisgeneraux"]').html(financial(fraisgenerauxAgo));
                            $trTotalSiteYearAgo.find('td[data-name="site-resultatexploitation"]').html(financial(resultatexploitationAgo));
                            $trTotalSiteYearAgo.find('td[data-name="site-retourachat"]').html(financial(retourachatAgo));
                            $trTotalSiteYearAgo.find('td[data-name="site-fraissiege"]').html(financial(fraissiegeAgo));
                            $trTotalSiteYearAgo.find('td[data-name="site-resultats"]').html(financial(resultatsAgo));
                            $trTotalSiteYearAgo.find('td[data-name="site-pourcentagesrentabilite"]').html(financial(pourcentagesrentabiliteAgo) + '%');

                            confirmedSites++;
                        }
                    }
                );
            }
        });

        var interval = setInterval(function () {
            if (countSites == confirmedSites) {
                $('html').css('overflow', 'scroll');
                clearInterval(interval);
            }
        }, 500);

        function printSynthese() {
            var url = '<?= AGAPESHOTES_URL; ?>page/getFactures.php';
            var form = $('<form action="' + url + '" method="post" target="_blank">' +
                '<input type="text" name="tableData" value="' + escapeHtml($('#totalSyntheseTable').prop('outerHTML')) + '" />' +
                '</form>');
            $('body').append(form);
            form.submit();
        }
    });
</script>