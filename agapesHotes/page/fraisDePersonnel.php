<?php require('header.php');
echo getTitle($Page->getName(), $Page->getSlug());

$Secteur = new \App\Plugin\AgapesHotes\Secteur();
$Budget = new \App\Plugin\AgapesHotes\Budget();

$allSites = getSitesAccess();
$allSitesBySecteur = groupMultipleKeysObjectsArray($allSites, 'secteur_id');

$start = new \DateTime(date('Y-01-01'));
$end = new \DateTime(date('Y-12-t'));
$end->add(new \DateInterval('P1D'));
$interval = new \DateInterval('P1M');

$period = new \DatePeriod($start, $interval, $end);

$anneeAgoCumul = array();
$anneeAgoCurrent = 0;
$anneeCumul = array();
$anneeCurrent = 0;
?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="table-responsive">
                    <table id="syntheseSecteurTable" class="table table-striped tableNonEffect text-center fixed-header">
                        <thead>
                        <tr>
                            <th><?= trans('Site'); ?></th>
                            <th>#</th>
                            <?php foreach ($period as $key => $date): ?>
                                <th><?= $date->format('m'); ?></th>
                            <?php endforeach; ?>
                        </tr>
                        </thead>
                        <tbody>

                        <?php foreach ($allSitesBySecteur as $secteurId => $allSites):
                            $Secteur->setId($secteurId);
                            if ($Secteur->show() && $Secteur->getId() > 0):
                                $budgetCumul = array();
                                $budgetCumulCurrent = 0;
                                ?>
                                <tr>
                                    <th style="text-align:center !important; background:#4fb99f;color:#fff;"
                                        colspan="14"><?= $Secteur->getNom(); ?></th>
                                </tr>
                                <?php foreach ($allSites as $site): ?>
                                <tr data-siteid="<?= $site->id; ?>" data-name="budgetrow">
                                    <th rowspan="6"
                                        style="vertical-align:middle;"><?= $site->nom ?></th>
                                    <td style="text-align:left !important;">
                                        <small><em>Budget</em></small>
                                    </td>
                                    <?php foreach ($period as $key => $date):
                                        $Budget->setStatus(1);
                                        $Budget->setYear(date('Y'));
                                        $Budget->setSiteId($site->id);
                                        $Budget->setMonth($date->format('n'));
                                        $Budget->showBySite();
                                        $budgetCumulCurrent += !empty($Budget->getPersonnel()) ? $Budget->getPersonnel() : 0;

                                        $budgetCumul[$date->format('n')] = $budgetCumulCurrent;
                                        ?>
                                        <td><?= !empty($Budget->getPersonnel()) ? financial($Budget->getPersonnel()) : 0; ?>€</td>
                                        <?php
                                        $Budget->clean();
                                    endforeach; ?>
                                </tr>
                                <tr data-siteid="<?= $site->id; ?>" data-name="budgetrow">
                                    <td style="text-align:left !important;">
                                        <small><em>Budget cumul</em></small>
                                    </td>
                                    <?php foreach ($period as $key => $date): ?>
                                        <td><?= financial($budgetCumul[$date->format('n')]); ?>€</td>
                                    <?php endforeach; ?>
                                </tr>
                                <tr data-siteid="<?= $site->id; ?>" data-name="siterowYearAgo">
                                    <td style="text-align:left !important;">
                                        <small><em><?= date('Y') - 1; ?></em></small>
                                    </td>
                                    <?php foreach ($period as $key => $date):
                                        $fraisPerso = getSiteMeta($site->id, date('Y') - 1, $date->format('m'));

                                        $anneeAgoCurrent += !empty($fraisPerso['fraisDePersonnels']) ? $fraisPerso['fraisDePersonnels'] : 0;
                                        $anneeAgoCumul[$date->format('m')] = $anneeAgoCurrent;
                                        ?>
                                        <td><?= !empty($fraisPerso['fraisDePersonnels']) ? financial($fraisPerso['fraisDePersonnels']) : 0; ?>
                                            €
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                                <tr data-siteid="<?= $site->id; ?>" data-name="siterowYearAgo">
                                    <td style="text-align:left !important;">
                                        <small><em><?= date('Y') - 1; ?> cumul</em></small>
                                    </td>
                                    <?php foreach ($period as $key => $date): ?>
                                        <td><?= financial($anneeAgoCumul[$date->format('m')]); ?>€</td>
                                    <?php endforeach; ?>
                                </tr>
                                <tr data-siteid="<?= $site->id; ?>" data-name="siterow">
                                    <td style="text-align:left !important;">
                                        <small><em><?= date('Y'); ?></em></small>
                                    </td>
                                    <?php foreach ($period as $key => $date):
                                        $fraisPerso = getSiteMeta($site->id, date('Y'), $date->format('m'));
                                        $anneeCurrent += !empty($fraisPerso['fraisDePersonnels']) ? $fraisPerso['fraisDePersonnels'] : 0;
                                        $anneeCumul[$date->format('m')] = $anneeCurrent;
                                        ?>
                                        <td><?= !empty($fraisPerso['fraisDePersonnels']) ? financial($fraisPerso['fraisDePersonnels']) : 0; ?>
                                            €
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                                <tr data-siteid="<?= $site->id; ?>" data-name="siterow">
                                    <td style="text-align:left !important;">
                                        <small><em><?= date('Y'); ?> cumul</em></small>
                                    </td>
                                    <?php foreach ($period as $key => $date): ?>
                                        <td><?= financial($anneeCumul[$date->format('m')]); ?>€</td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach;
                            endif;
                        endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="<?= AGAPESHOTES_URL; ?>js/footer.js"></script>
<?php require('footer.php'); ?>