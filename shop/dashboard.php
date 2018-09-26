<?php
require('main.php');
$Commande = new \App\Plugin\Shop\Commande();
$allCommandes = $Commande->showAll(0, true, true);
$allCancelCommandes = $Commande->showAll(0, true, true, 1);
$allLiveCommandes = $Commande->showAll(0, true, true, 2);
$allConfirmCommandes = $Commande->showAll(0, true, true, 3);

$Menu = new \App\Menu();
$menuData = $Menu->displayMenuBySlug('commandes');

if (false !== $allCommandes) {
    echo json_encode(
        array(
            'name' => trans($menuData->name),
            'count' => $allCommandes,
            'url' => WEB_PLUGIN_URL . 'shop/page/commandes/',
            'html' => '<span>Confirmées: ' . $allConfirmCommandes . '</span><span>En cours: ' . $allLiveCommandes . '</span><span>Annulées: ' . $allCancelCommandes . '</span>'
        ), JSON_UNESCAPED_UNICODE
    );
}