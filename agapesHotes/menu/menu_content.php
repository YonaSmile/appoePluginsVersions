<?php
if (getUserRoleId() == 1):
    $SiteAccess = new \App\Plugin\AgapesHotes\SiteAccess();
    $SiteAccess->setSiteUserId(getUserIdSession());
    $Site = $SiteAccess->showSiteByUser();
    ?>
    <li class="<?= activePage('mainCourante'); ?>">
        <a href="<?= AGAPESHOTES_URL; ?>page/mainCourante/<?= $Site->secteurSlug; ?>/<?= $Site->slug; ?>/">
            <?= trans('Main Courante'); ?></a>
        <a href="<?= AGAPESHOTES_URL; ?>page/planning/<?= $Site->secteurSlug; ?>/<?= $Site->slug; ?>/">
            <?= trans('Planning'); ?></a>
        <a href="<?= AGAPESHOTES_URL; ?>page/noteDeFrais/<?= $Site->secteurSlug; ?>/<?= $Site->slug; ?>/">
            <?= trans('Note de frais'); ?></a>
        <a href="<?= AGAPESHOTES_URL; ?>page/vivreCrue/<?= $Site->secteurSlug; ?>/<?= $Site->slug; ?>/">
            <?= trans('Vivre crue'); ?></a>
        <a href="<?= AGAPESHOTES_URL; ?>page/mainSupplementaire/<?= $Site->secteurSlug; ?>/<?= $Site->slug; ?>/">
            <?= trans('Facturation HC'); ?></a>
    </li>
<?php endif; ?>