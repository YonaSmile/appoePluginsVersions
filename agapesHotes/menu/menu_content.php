<?php
if (getUserRoleId() == 1):
    $SiteAccess = new \App\Plugin\AgapesHotes\SiteAccess();
    $SiteAccess->setSiteUserId(getUserIdSession());
    $Site = $SiteAccess->showSiteByUser();
    ?>
    <li class="<?= activePage('updateMainCourante'); ?>">
        <a href="<?= WEB_PLUGIN_URL; ?>agapesHotes/page/mainCourante/<?= $Site->secteurSlug; ?>/<?= $Site->slug; ?>/">
            <?= trans('Main Courante'); ?></a>
    </li>
    <!--<li class="<?= activePage('updatePlanning'); ?>">
        <a href="<?= WEB_PLUGIN_URL; ?>agapesHotes/page/planning/<?= $Site->secteurSlug; ?>/<?= $Site->slug; ?>/">
            <?= trans('Planning'); ?></a>
    </li>-->
    <li class="<?= activePage('updateNoteDeFrais'); ?>">
        <a href="<?= WEB_PLUGIN_URL; ?>agapesHotes/page/noteDeFrais/<?= $Site->secteurSlug; ?>/<?= $Site->slug; ?>/">
            <?= trans('Note de frais'); ?></a>
    </li>
    <li class="<?= activePage('updateVivreCrue'); ?>">
        <a href="<?= WEB_PLUGIN_URL; ?>agapesHotes/page/vivreCrue/<?= $Site->secteurSlug; ?>/<?= $Site->slug; ?>/">
            <?= trans('Vivre cru'); ?></a>
    </li>
    <li class="<?= activePage('updateMainSupplementaire'); ?>">
        <a href="<?= WEB_PLUGIN_URL; ?>agapesHotes/page/mainSupplementaire/<?= $Site->secteurSlug; ?>/<?= $Site->slug; ?>/">
            <?= trans('Facturation HC'); ?></a>
    </li>
<?php endif; ?>