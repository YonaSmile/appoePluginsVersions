<?php require('header.php');
echo getTitle($Page->getName() . ' (€)', $Page->getSlug());

$Secteur = new \App\Plugin\AgapesHotes\Secteur();
$Budget = new \App\Plugin\AgapesHotes\Budget();

$allSites = getSitesAccess();
$allSitesBySecteur = groupMultipleKeysObjectsArray($allSites, 'secteur_id');

$start = new \DateTime(date('Y-01-01'));
$end = new \DateTime(date('Y-12-t'));
$end->add(new \DateInterval('P1D'));
$interval = new \DateInterval('P1M');

$period = new \DatePeriod($start, $interval, $end);
$secteurTotal = array('budget' => array());
?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="table-responsive">
                    <table id="syntheseSecteurTable"
                           class="table table-striped tableNonEffect text-center fixed-header">
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
                            if ($Secteur->show() && $Secteur->getId() > 0): ?>
                                <tr>
                                    <th style="text-align:center !important; background:#4fb99f;color:#fff;"
                                        colspan="14"><?= $Secteur->getNom(); ?></th>
                                </tr>
                                <?php foreach ($allSites as $site):
                                    $budgetCumul = array();
                                    $budgetCumulCurrent = 0.00;
                                    ?>

                                    <!-- BUDGET -->
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
                                            $budgetCumulCurrent += !empty($Budget->getConso()) ? $Budget->getConso() : 0;

                                            if(!array_key_exists($date->format('n'), $secteurTotal['budget'])){
                                                $secteurTotal['budget'][$date->format('n')] = 0;
                                            }
                                            $secteurTotal['budget'][$date->format('n')] += !empty($Budget->getConso()) ? $Budget->getConso() : 0;
                                            $budgetCumul[$date->format('n')] = $budgetCumulCurrent;
                                            ?>
                                            <td><?= !empty($Budget->getConso()) ? financial($Budget->getConso()) : 0; ?>
                                            </td>
                                            <?php
                                            $Budget->clean();
                                        endforeach; ?>
                                    </tr>

                                    <!-- CUMUL -->
                                    <tr data-siteid="<?= $site->id; ?>" data-name="budgetrowcumul">
                                        <td style="text-align:left !important;">
                                            <small><em>Budget cumul</em></small>
                                        </td>
                                        <?php foreach ($period as $key => $date): ?>
                                            <td><?= financial($budgetCumul[$date->format('n')]); ?></td>
                                        <?php endforeach; ?>
                                    </tr>

                                    <!-- YEAR -1 -->
                                    <tr data-siteid="<?= $site->id; ?>" data-year="<?= date('Y') - 1; ?>"
                                        data-name="siterow" data-secteurid="<?= $secteurId; ?>">
                                        <td style="text-align:left !important;">
                                            <small><em><?= date('Y') - 1; ?></em></small>
                                        </td>
                                        <?php foreach ($period as $key => $date): ?>
                                            <td data-name="site-conso" data-month="<?= $date->format('n'); ?>"></td>
                                        <?php endforeach; ?>
                                    </tr>

                                    <!-- CUMUL -->
                                    <tr data-siteid="<?= $site->id; ?>" data-name="sitecumul"
                                        data-year="<?= date('Y') - 1; ?>" data-secteurid="<?= $secteurId; ?>">
                                        <td style="text-align:left !important;">
                                            <small><em><?= date('Y') - 1; ?> cumul</em></small>
                                        </td>
                                        <?php foreach ($period as $key => $date): ?>
                                            <td data-name="site-conso-cumul"
                                                data-month="<?= $date->format('n'); ?>"></td>
                                        <?php endforeach; ?>
                                    </tr>

                                    <!-- YEAR -->
                                    <tr data-siteid="<?= $site->id; ?>" data-year="<?= date('Y'); ?>"
                                        data-name="siterow" data-secteurid="<?= $secteurId; ?>">
                                        <td style="text-align:left !important;">
                                            <small><em><?= date('Y'); ?></em></small>
                                        </td>
                                        <?php foreach ($period as $key => $date): ?>
                                            <td data-name="site-conso" data-month="<?= $date->format('n'); ?>"></td>
                                        <?php endforeach; ?>
                                    </tr>

                                    <!-- CUMUL -->
                                    <tr data-siteid="<?= $site->id; ?>" data-year="<?= date('Y'); ?>"
                                        data-name="sitecumul" data-secteurid="<?= $secteurId; ?>">
                                        <td style="text-align:left !important;">
                                            <small><em><?= date('Y'); ?> cumul</em></small>
                                        </td>
                                        <?php foreach ($period as $key => $date): ?>
                                            <td data-name="site-conso-cumul"
                                                data-month="<?= $date->format('n'); ?>"></td>
                                        <?php endforeach; ?>
                                    </tr>

                                <?php endforeach;
                            endif; ?>
                            <tr>
                                <th rowspan="3"
                                    style="vertical-align:middle;">Total
                                </th>
                                <td style="text-align:left !important;">
                                    <small><em>Budget</em></small>
                                </td>
                                <?php foreach ($period as $key => $date): ?>
                                    <td><?= financial($secteurTotal['budget'][$date->format('n')]); ?></td>
                                <?php endforeach; ?>
                            </tr>
                            <tr>
                                <td style="text-align:left !important;">
                                    <small><em><?= date('Y') - 1; ?></em></small>
                                </td>
                                <?php foreach ($period as $key => $date): ?>
                                    <td data-name="total" data-year="<?= date('Y') - 1; ?>" data-month="<?= $date->format('n'); ?>"></td>
                                <?php endforeach; ?>
                            </tr>
                            <tr>
                                <td style="text-align:left !important;">
                                    <small><em><?= date('Y'); ?></em></small>
                                </td>
                                <?php foreach ($period as $key => $date): ?>
                                    <td data-name="total" data-year="<?= date('Y'); ?>" data-month="<?= $date->format('n'); ?>"></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript" src="<?= AGAPESHOTES_URL; ?>js/footer.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            var consoreelDenree = [];
            var total = [];
            setTimeout(function () {
                $('td[data-name="site-conso"]').each(function () {

                    var $td = $(this);
                    var $tr = $td.closest('tr');
                    var secteur = $tr.data('secteurid');
                    var site = $tr.data('siteid');
                    var year = $tr.data('year');
                    var month = $td.data('month');
                    var sendSata = {
                        siteId: site,
                        year: year,
                        month: month
                    };
                    $.ajax({
                        type: "POST",
                        async: false,
                        url: '<?= AGAPESHOTES_URL . 'page/getAllSiteData.php'; ?>',
                        data: sendSata,
                        success: function (data) {
                            if (data) {
                                var parsedData = jQuery.parseJSON(data);
                                var $siteData = parsedData[secteur][site];

                                if (typeof consoreelDenree[site] === 'undefined') {
                                    consoreelDenree[site] = [];
                                }
                                if (typeof consoreelDenree[site][year] === 'undefined') {
                                    consoreelDenree[site][year] = [];
                                }
                                if (typeof consoreelDenree[site][year][month] === 'undefined') {
                                    consoreelDenree[site][year][month] = '';
                                }

                                if (typeof total[year] === 'undefined') {
                                    total[year] = [];
                                }

                                if (typeof total[year][month] === 'undefined') {
                                    total[year][month] = 0;
                                }

                                $tr.find('td[data-name="site-conso"][data-month="' + month + '"]').text(financial($siteData['consoReel'].denree));
                                consoreelDenree[site][year][month] = $.isNumeric($siteData['consoReel'].denree) ? parseReelFloat($siteData['consoReel'].denree) : 0;
                                total[year][month] += $.isNumeric($siteData['consoReel'].denree) ? parseReelFloat($siteData['consoReel'].denree) : 0;
                                var sum = 0;
                                $.each(consoreelDenree[site][year], function (month, val) {
                                    val = parseReelFloat(val);
                                    if ($.isNumeric(val)) {
                                        sum += val;
                                    }
                                });
                                $('tr[data-name="sitecumul"][data-siteid="' + site + '"][data-year="' + year + '"] td[data-name="site-conso-cumul"][data-month="' + month + '"]').text(financial(sum));
                            }
                        }
                    });
                });
                console.log(total);
            }, 300);
        });
    </script>
<?php require('footer.php'); ?>