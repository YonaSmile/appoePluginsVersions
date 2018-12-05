<?php
$Site = new \App\Plugin\AgapesHotes\Site();
$allSites = $Site->showAll();
$allOrdSites = extractFromObjToSimpleArr($allSites, 'id', 'secteur_id');
$allSitesBySecteur = groupMultipleKeysObjectsArray($allSites, 'secteur_id');
$Secteur = new \App\Plugin\AgapesHotes\Secteur();
?>
<h4>Synthèse Secteur</h4>
<div class="row mb-3">
    <div class="col-12 positionRelative">
        <div class="table-responsive">
            <table class="table table-striped tableNonEffect text-center">
                <thead>
                <tr>
                    <th></th>
                    <th colspan="2"><?= trans('Chiffre d\'affaire'); ?></th>
                    <th><?= trans('Consommation'); ?></th>
                    <th><?= trans('Personnel'); ?></th>
                    <th><?= trans('Frais généraux'); ?></th>
                    <th><?= trans('Résultat d\'exploitation'); ?></th>
                    <th><?= trans('Retour achat 8%'); ?></th>
                    <th><?= trans('Frais de siège 4%'); ?></th>
                    <th><?= trans('Résultats'); ?></th>
                    <th><?= trans('Pourcentages de rentabilité'); ?></th>
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
                    <td class="table-secondary"></td>
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
            <table id="syntheseSecteurTable" class="table table-striped tableNonEffect text-center">
                <thead>
                <tr>
                    <th><?= trans('Site'); ?></th>
                    <th colspan="2"><?= trans('Chiffre d\'affaire'); ?></th>
                    <th><?= trans('Consommation'); ?></th>
                    <th><?= trans('Personnel'); ?></th>
                    <th><?= trans('Frais généraux'); ?></th>
                    <th><?= trans('Résultat d\'exploitation'); ?></th>
                    <th><?= trans('Retour achat 8%'); ?></th>
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
                            <td data-name="budget-ca"></td>
                            <td data-name="budget-conso"></td>
                            <td data-name="budget-perso"></td>
                            <td data-name="budget-fraisgeneraux"></td>
                            <td data-name="budget-resultatexploitation"></td>
                            <td data-name="budget-retourachat"></td>
                            <td data-name="budget-fraissiege"></td>
                            <td data-name="budget-resultats"></td>
                            <td class="table-secondary"></td>
                        </tr>
                        <tr data-siteid="<?= $site->id; ?>" data-name="siterowYearAgo">
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
                        <tr data-siteid="<?= $site->id; ?>" data-name="siterow">
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

        var budgetCa = 0, budgetConso = 0, budgetPerso = 0, budgetFraisGeneraux = 0, budgetResultatsExploitation = 0,
            budgetretoruAchat = 0, budgetFraisSiege = 0, budgetResultats = 0;

        var facturation = 0, consoreelDenree = 0, fraisperso = 0, fraisgeneraux = 0, resultatexploitation = 0,
            retourachat = 0, fraissiege = 0, resultats = 0, pourcentagesrentabilite = 0;

        var facturationAgo = 0, consoreelDenreeAgo = 0, fraispersoAgo = 0, fraisgenerauxAgo = 0, resultatexploitationAgo = 0,
            retourachatAgo = 0, fraissiegeAgo = 0, resultatsAgo = 0, pourcentagesrentabiliteAgo = 0;

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
                            $trBudget.find('td[data-name="budget-ca"]').text($siteData.budget.ca ? financial($siteData.budget.ca) + '€' : 0);
                            $trBudget.find('td[data-name="budget-conso"]').text($siteData.budget.conso ? financial($siteData.budget.conso) + '€' : 0);
                            $trBudget.find('td[data-name="budget-perso"]').text($siteData.budget.personnel ? financial($siteData.budget.personnel) + '€' : 0);
                            $trBudget.find('td[data-name="budget-fraisgeneraux"]').text($siteData.budget.fraisGeneraux ? financial($siteData.budget.fraisGeneraux) + '€' : 0);
                            $trBudget.find('td[data-name="budget-resultatexploitation"]').text($siteData.budget.resultatExploitation ? financial($siteData.budget.resultatExploitation) + '€' : 0);
                            $trBudget.find('td[data-name="budget-retourachat"]').text($siteData.budget.retourAchat ? financial($siteData.budget.retourAchat) + '€' : 0);
                            $trBudget.find('td[data-name="budget-fraissiege"]').text($siteData.budget.retourFraisSiege ? financial($siteData.budget.retourFraisSiege) + '€' : 0);
                            $trBudget.find('td[data-name="budget-resultats"]').text($siteData.budget.resultats ? financial($siteData.budget.resultats) + '€' : 0);

                            budgetCa += $.isNumeric($siteData.budget.ca) ? parseFloat(financial($siteData.budget.ca)) : 0;
                            budgetConso += $.isNumeric($siteData.budget.conso) ? parseFloat($siteData.budget.conso) : 0;
                            budgetPerso += $.isNumeric($siteData.budget.personnel) ? parseFloat($siteData.budget.personnel) : 0;
                            budgetFraisGeneraux += $.isNumeric($siteData.budget.fraisGeneraux) ? parseFloat($siteData.budget.fraisGeneraux) : 0;
                            budgetResultatsExploitation += $.isNumeric($siteData.budget.resultatExploitation) ? parseFloat($siteData.budget.resultatExploitation) : 0;
                            budgetretoruAchat += $.isNumeric($siteData.budget.retourAchat) ? parseFloat($siteData.budget.retourAchat) : 0;
                            budgetFraisSiege += $.isNumeric($siteData.budget.retourFraisSiege) ? parseFloat($siteData.budget.retourFraisSiege) : 0;
                            budgetResultats += $.isNumeric($siteData.budget.resultats) ? parseFloat($siteData.budget.resultats) : 0;

                            $trTotalBudget.find('td[data-name="budget-ca"]').html(financial(budgetCa) + '€');
                            $trTotalBudget.find('td[data-name="budget-conso"]').html(financial(budgetConso) + '€');
                            $trTotalBudget.find('td[data-name="budget-perso"]').html(financial(budgetPerso) + '€');
                            $trTotalBudget.find('td[data-name="budget-fraisgeneraux"]').html(financial(budgetFraisGeneraux) + '€');
                            $trTotalBudget.find('td[data-name="budget-resultatexploitation"]').html(financial(budgetResultatsExploitation) + '€');
                            $trTotalBudget.find('td[data-name="budget-retourachat"]').html(financial(budgetretoruAchat) + '€');
                            $trTotalBudget.find('td[data-name="budget-fraissiege"]').html(financial(budgetFraisSiege) + '€');
                            $trTotalBudget.find('td[data-name="budget-resultats"]').html(financial(budgetResultats) + '€');

                            /**
                             * Current year
                             * @type {number}
                             */
                            $trSite.find('td[data-name="site-facturation"]').text($siteData['facturation'] + '€');
                            $trSite.find('td[data-name="site-consoreel-denree"]').text($siteData['consoReel'].denree + '€');
                            $trSite.find('td[data-name="site-meta-fraisperso"]').text($siteData['siteMeta'].fraisDePersonnels + '€');
                            $trSite.find('td[data-name="site-fraisgeneraux"]').text($siteData['fraisGeneraux'] + '€');
                            $trSite.find('td[data-name="site-resultatexploitation"]').text($siteData['resultatExploitation'] + '€');
                            $trSite.find('td[data-name="site-retourachat"]').text($siteData['retourAchat'] + '€');
                            $trSite.find('td[data-name="site-fraissiege"]').text($siteData['fraisDeSiege'] + '€');
                            $trSite.find('td[data-name="site-resultats"]').text($siteData['resultats'] + '€');
                            $trSite.find('td[data-name="site-pourcentagesrentabilite"]').text(financial($siteData['pourcentagesDeRentabilite']) + '%');

                            facturation += $.isNumeric($siteData['facturation']) ? parseFloat(financial($siteData['facturation'])) : 0;
                            consoreelDenree += $.isNumeric($siteData['consoReel'].denree) ? parseFloat($siteData['consoReel'].denree) : 0;
                            fraisperso += $.isNumeric($siteData['siteMeta'].fraisDePersonnels) ? parseFloat($siteData['siteMeta'].fraisDePersonnels) : 0;
                            fraisgeneraux += $.isNumeric($siteData['fraisGeneraux']) ? parseFloat($siteData['fraisGeneraux']) : 0;
                            resultatexploitation += $.isNumeric($siteData['resultatExploitation']) ? parseFloat($siteData['resultatExploitation']) : 0;
                            retourachat += $.isNumeric($siteData['retourAchat']) ? parseFloat($siteData['retourAchat']) : 0;
                            fraissiege += $.isNumeric($siteData['fraisDeSiege']) ? parseFloat($siteData['fraisDeSiege']) : 0;
                            resultats += $.isNumeric($siteData['resultats']) ? parseFloat($siteData['resultats']) : 0;
                            pourcentagesrentabilite += $.isNumeric($siteData['pourcentagesDeRentabilite']) ? parseFloat($siteData['pourcentagesDeRentabilite']) : 0;

                            $trTotalSite.find('td[data-name="site-facturation"]').html(financial(facturation) + '€');
                            $trTotalSite.find('td[data-name="site-consoreel-denree"]').html(financial(consoreelDenree) + '€');
                            $trTotalSite.find('td[data-name="site-meta-fraisperso"]').html(financial(fraisperso) + '€');
                            $trTotalSite.find('td[data-name="site-fraisgeneraux"]').html(financial(fraisgeneraux) + '€');
                            $trTotalSite.find('td[data-name="site-resultatexploitation"]').html(financial(resultatexploitation) + '€');
                            $trTotalSite.find('td[data-name="site-retourachat"]').html(financial(retourachat) + '€');
                            $trTotalSite.find('td[data-name="site-fraissiege"]').html(financial(fraissiege) + '€');
                            $trTotalSite.find('td[data-name="site-resultats"]').html(financial(resultats) + '€');
                            $trTotalSite.find('td[data-name="site-pourcentagesrentabilite"]').html(financial(pourcentagesrentabilite) + '%');
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
                            $trSite.find('td[data-name="site-facturation"]').text($siteData['facturation'] + '€');
                            $trSite.find('td[data-name="site-consoreel-denree"]').text($siteData['consoReel'].denree + '€');
                            $trSite.find('td[data-name="site-meta-fraisperso"]').text($siteData['siteMeta'].fraisDePersonnels + '€');
                            $trSite.find('td[data-name="site-fraisgeneraux"]').text($siteData['fraisGeneraux'] + '€');
                            $trSite.find('td[data-name="site-resultatexploitation"]').text($siteData['resultatExploitation'] + '€');
                            $trSite.find('td[data-name="site-retourachat"]').text($siteData['retourAchat'] + '€');
                            $trSite.find('td[data-name="site-fraissiege"]').text($siteData['fraisDeSiege'] + '€');
                            $trSite.find('td[data-name="site-resultats"]').text($siteData['resultats'] + '€');
                            $trSite.find('td[data-name="site-pourcentagesrentabilite"]').text($siteData['pourcentagesDeRentabilite'] + '%');

                            facturationAgo += $.isNumeric($siteData['facturation']) ? parseFloat(financial($siteData['facturation'])) : 0;
                            consoreelDenreeAgo += $.isNumeric($siteData['consoReel'].denree) ? parseFloat($siteData['consoReel'].denree) : 0;
                            fraispersoAgo += $.isNumeric($siteData['siteMeta'].fraisDePersonnels) ? parseFloat($siteData['siteMeta'].fraisDePersonnels) : 0;
                            fraisgenerauxAgo += $.isNumeric($siteData['fraisGeneraux']) ? parseFloat($siteData['fraisGeneraux']) : 0;
                            resultatexploitationAgo += $.isNumeric($siteData['resultatExploitation']) ? parseFloat($siteData['resultatExploitation']) : 0;
                            retourachatAgo += $.isNumeric($siteData['retourAchat']) ? parseFloat($siteData['retourAchat']) : 0;
                            fraissiegeAgo += $.isNumeric($siteData['fraisDeSiege']) ? parseFloat($siteData['fraisDeSiege']) : 0;
                            resultatsAgo += $.isNumeric($siteData['resultats']) ? parseFloat($siteData['resultats']) : 0;
                            pourcentagesrentabiliteAgo += $.isNumeric($siteData['pourcentagesDeRentabilite']) ? parseFloat($siteData['pourcentagesDeRentabilite']) : 0;

                            $trTotalSiteYearAgo.find('td[data-name="site-facturation"]').html(financial(facturationAgo) + '€');
                            $trTotalSiteYearAgo.find('td[data-name="site-consoreel-denree"]').html(financial(consoreelDenreeAgo) + '€');
                            $trTotalSiteYearAgo.find('td[data-name="site-meta-fraisperso"]').html(financial(fraispersoAgo) + '€');
                            $trTotalSiteYearAgo.find('td[data-name="site-fraisgeneraux"]').html(financial(fraisgenerauxAgo) + '€');
                            $trTotalSiteYearAgo.find('td[data-name="site-resultatexploitation"]').html(financial(resultatexploitationAgo) + '€');
                            $trTotalSiteYearAgo.find('td[data-name="site-retourachat"]').html(financial(retourachatAgo) + '€');
                            $trTotalSiteYearAgo.find('td[data-name="site-fraissiege"]').html(financial(fraissiegeAgo) + '€');
                            $trTotalSiteYearAgo.find('td[data-name="site-resultats"]').html(financial(resultatsAgo) + '€');
                            $trTotalSiteYearAgo.find('td[data-name="site-pourcentagesrentabilite"]').html(financial(pourcentagesrentabiliteAgo) + '%');
                        }
                    }
                );
            }
        });
    });
</script>