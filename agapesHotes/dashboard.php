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

if (false !== $allSecteur && false !== $allSites && false !== $allEmployes) {
    echo json_encode(
        array(
            1 => array(
                'name' => trans($menuSecteurData->name),
                'count' => $allSecteur,
                'url' => WEB_PLUGIN_URL . 'agapesHotes/page/allSecteurs/'
            ),
            2 => array(
                'name' => trans($menuSiteData->name),
                'count' => $allSites,
                'url' => WEB_PLUGIN_URL . 'agapesHotes/page/allSites/'
            ),
            3 => array(
                'name' => trans($menuEmployesData->name),
                'count' => $allEmployes,
                'url' => WEB_PLUGIN_URL . 'agapesHotes/page/allEmployes/'
            )
        ), JSON_UNESCAPED_UNICODE
    );
}