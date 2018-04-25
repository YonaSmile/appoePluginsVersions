<?php
require('main.php');
$InteractiveMap = new App\Plugin\InteractiveMap\InteractiveMap();

//Creating table
$pluginSetup = $InteractiveMap->createTable();
echo $pluginSetup ? trans('Table') . ' INTERACTIVE MAP ' . trans('activé') . '.<br>' : trans('Une erreur est survenue');

//Creating autorisations
$Menu = new App\Menu();
$data = array(
    1 => array(
        'id' => 80,
        'slug' => 'interactiveMap',
        'name' => 'Carte Interactive',
        'min_role_id' => 1,
        'statut' => 1,
        'parent_id' => 10,
        'pluginName' => 'interactiveMap',
        'order_menu' => '80'
    ),
    2 => array(
        'id' => 81,
        'slug' => 'allInterMaps',
        'name' => 'Toutes les cartes',
        'min_role_id' => 1,
        'statut' => 1,
        'parent_id' => 80,
        'pluginName' => 'interactiveMap',
        'order_menu' => '81'
    ),
    3 => array(
        'id' => 82,
        'slug' => 'addInterMap',
        'name' => 'Ajouter une carte',
        'min_role_id' => 1,
        'statut' => 1,
        'parent_id' => 80,
        'pluginName' => 'interactiveMap',
        'order_menu' => '82'
    ),
    4 => array(
        'id' => 83,
        'slug' => 'updateInterMap',
        'name' => 'Modifier la carte',
        'min_role_id' => 1,
        'statut' => 0,
        'parent_id' => 80,
        'pluginName' => 'interactiveMap',
        'order_menu' => '83'
    ),
    5 => array(
        'id' => 84,
        'slug' => 'updateInterMapContent',
        'name' => 'Éditer la carte',
        'min_role_id' => 1,
        'statut' => 0,
        'parent_id' => 80,
        'pluginName' => 'interactiveMap',
        'order_menu' => '84'
    )
);

$dataCount = count($data);
$trueCount = 0;
foreach ($data as $key => $menuData) {
    if ($Menu->insertMenu($menuData['id'], $menuData['slug'], $menuData['name'], $menuData['min_role_id'], $menuData['statut'], $menuData['parent_id'], $menuData['pluginName'], $menuData['order_menu'])) {
        $trueCount++;
    }
}
if (unlink(WEB_PLUGIN_PATH . 'interactiveMap/setup.php')) {
    echo trans('Autorisations installés') . ' : ' . $trueCount . '/' . $dataCount . '.<br>';
}
