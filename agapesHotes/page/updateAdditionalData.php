<?php require('header.php');
if (!empty($_GET['secteur']) && !empty($_GET['site'])):

    //Get Secteur
    $Secteur = new \App\Plugin\AgapesHotes\Secteur();
    $Secteur->setSlug($_GET['secteur']);

    //Get Site
    $Site = new \App\Plugin\AgapesHotes\Site();
    $Site->setSlug($_GET['site']);

    $enablesMetasSite = array(
        1 => 'Participation tournant',
        'Frais de personnel',
        'Frais fixes'
    );

    //Check Secteur and Site
    if (
        $Secteur->showBySlug() && $Site->showBySlug() && $Site->getSecteurId() == $Secteur->getId()
    ):
        echo getTitle($Page->getName(), $Page->getSlug(), ' de <strong>' . $Site->getNom() . '</strong> de l\'année <strong>' . date('Y') . '</strong>');

        $SiteMeta = new \App\Plugin\AgapesHotes\SiteMeta();
        $SiteMeta->setSiteId($Site->getId());
        $SiteMeta->setYear(date('Y'));
        $allSiteMeta = groupMultipleKeysObjectsArray($SiteMeta->showByYear(), 'month');
        ?>
        <div class="row">
            <div class="table-responsive col-12">
                <table class="table table-striped tableNonEffect">
                    <thead>
                    <tr>
                        <th>#</th>
                        <?php for ($c = 1; $c <= 12; $c++): ?>
                            <th style="text-align: center !important; <?= $c == date('n') ? 'background:#4fb99f;color:#fff;' : ''; ?>">
                                <?= strlen($c) < 2 ? '0' . $c : $c; ?></th>
                        <?php endfor; ?>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($enablesMetasSite as $key => $meta): ?>
                        <tr data-year="<?= date('Y'); ?>"
                            data-siteid="<?= $Site->getId(); ?>">
                            <th><?= $meta; ?></th>
                            <?php for ($c = 1; $c <= 12; $c++):
                                $siteMeta = array();
                                if (array_key_exists($c, $allSiteMeta)) {
                                    $siteMeta = extractFromObjArr($allSiteMeta[$c], 'dataName');
                                }
                                ?>
                                <td>
                                    <input class="text-center form-control inputSiteMeta sensibleField"
                                           name="<?= $meta; ?>" type="text" autocomplete="off"
                                           style="padding: 5px 0 !important; <?= $c == date('n') ? 'background:#4fb99f;color:#fff;' : ''; ?> min-width: 72px;"
                                           data-month="<?= $c; ?>"
                                           data-idsitemeta="<?= !empty($siteMeta[$meta]->id) ? $siteMeta[$meta]->id : ''; ?>"
                                           value="<?= !empty($siteMeta[$meta]->data) ? $siteMeta[$meta]->data : ''; ?>">
                                </td>
                            <?php endfor; ?>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>


        <script>
            $(document).ready(function () {

                var delay = (function () {
                    var timer = 0;
                    return function (callback, ms) {
                        clearTimeout(timer);
                        timer = setTimeout(callback, ms);
                    };
                })();


                $('.inputSiteMeta').on('input', function (event) {
                    event.preventDefault();

                    var $Input = $(this);
                    var $Parent = $Input.closest('tr');

                    $('input.inputSiteMeta').removeClass('successInput');

                    var dataName = $Input.attr('name');
                    var month = $Input.data('month');
                    var dataInput = $Input.val();
                    var id = $Input.data('idsitemeta');
                    var siteId = $Parent.data('siteid');
                    var year = $Parent.data('year');

                    disabledAllFields($Input);

                    delay(function () {
                        busyApp();

                        $.post(
                            '<?= AGAPESHOTES_URL . 'process/ajaxSiteMetaProcess.php'; ?>',
                            {
                                UPDATESITEMETA: 'OK',
                                siteId: siteId,
                                dataName: dataName,
                                year: year,
                                month: month.length === 1 ? '0' + month : month,
                                data: dataInput,
                                id: id
                            },
                            function (data) {
                                if (data && $.isNumeric(data)) {
                                    $Input.data('idsitemeta', data);
                                    $Input.addClass('successInput');

                                    if (month < 12 && dataInput.length > 0) {
                                        $Input.blur();

                                        for (let c = month + 1; c <= 12; c++) {
                                            setTimeout(function () {
                                                var $inputBoucle = $('input.inputSiteMeta[name="' + dataName + '"][data-month="' + c + '"]');
                                                var id = $inputBoucle.data('idsitemeta');

                                                $inputBoucle.val(dataInput);

                                                $.post(
                                                    '<?= AGAPESHOTES_URL . 'process/ajaxSiteMetaProcess.php'; ?>',
                                                    {
                                                        UPDATESITEMETA: 'OK',
                                                        siteId: siteId,
                                                        dataName: dataName,
                                                        year: year,
                                                        month: c.length === 1 ? '0' + c : c,
                                                        data: dataInput,
                                                        id: id
                                                    },
                                                    function (data) {
                                                        if (data && $.isNumeric(data)) {
                                                            $inputBoucle.data('idsitemeta', data);
                                                            $inputBoucle.addClass('successInput');
                                                        }
                                                    }
                                                );
                                            }, 300);
                                        }
                                    }

                                } else {
                                    alert(data);
                                }
                                availableApp();
                                activateAllFields();
                            }
                        );
                    }, 300);
                });
            });
        </script>
    <?php else: ?>
        <?= getContainerErrorMsg(trans('Ce site n\'est pas accessible')); ?>
    <?php endif; ?>
<?php endif; ?>
<?php require('footer.php'); ?>