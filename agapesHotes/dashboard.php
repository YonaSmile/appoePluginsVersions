<?php
require('main.php');
$Secteur = new \App\Plugin\AgapesHotes\Secteur();
$Site = new \App\Plugin\AgapesHotes\Site();
$Empolye = new \App\Plugin\AgapesHotes\Employe();

$allSecteur = $Secteur->showAll(true);
$allSites = $Site->showAll(true);
$allEmployes = $Empolye->showByType(true);

$Menu = new \App\Menu();
$menuSecteurData = $Menu->displayMenuBySlug('allSecteurs');
$menuSiteData = $Menu->displayMenuBySlug('allSites');
$menuEmployesData = $Menu->displayMenuBySlug('allEmployes');

$allSecteursArr = $allSitesArr = $allEmployesArr = array();

if ($Menu->checkUserPermission(getUserRoleId(), 'allSecteurs')) {
    $allSecteursArr = array(
        'name' => trans($menuSecteurData->name),
        'count' => $allSecteur,
        'url' => WEB_PLUGIN_URL . 'agapesHotes/page/allSecteurs/'
    );
}

if ($Menu->checkUserPermission(getUserRoleId(), 'allSites')) {
    $allSitesArr = array(
        'name' => trans($menuSiteData->name),
        'count' => $allSites,
        'url' => WEB_PLUGIN_URL . 'agapesHotes/page/allSites/'
    );
}

if ($Menu->checkUserPermission(getUserRoleId(), 'allEmployes')) {
    $allEmployesArr = array(
        'name' => trans($menuEmployesData->name),
        'count' => $allEmployes,
        'url' => WEB_PLUGIN_URL . 'agapesHotes/page/allEmployes/'
    );
}

if (false !== $allSecteur && false !== $allSites && false !== $allEmployes) {
    echo json_encode(
        array(
            1 => $allSecteursArr,
            2 => $allSitesArr,
            3 => $allEmployesArr
        ), JSON_UNESCAPED_UNICODE
    );
}