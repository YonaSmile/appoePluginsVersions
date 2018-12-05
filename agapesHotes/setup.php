<?php
require('main.php');

//Creating autorisations
$Menu = new \App\Menu();
$data = array(
    1 => array(
        'id' => 4000,
        'slug' => 'allSecteurs',
        'name' => 'Les secteurs',
        'min_role_id' => 3,
        'statut' => 1,
        'parent_id' => 10,
        'pluginName' => 'agapesHotes',
        'order_menu' => '4000'
    ),
    2 => array(
        'id' => 4001,
        'slug' => 'allSites',
        'name' => 'Les sites',
        'min_role_id' => 2,
        'statut' => 1,
        'parent_id' => 10,
        'pluginName' => 'agapesHotes',
        'order_menu' => '4001'
    ),
    3 => array(
        'id' => 4002,
        'slug' => 'allEtablissements',
        'name' => 'Les établissements',
        'min_role_id' => 1,
        'statut' => 1,
        'parent_id' => 10,
        'pluginName' => 'agapesHotes',
        'order_menu' => '4002'
    ),
    4 => array(
        'id' => 4003,
        'slug' => 'allPrestations',
        'name' => 'Les prestations',
        'min_role_id' => 1,
        'statut' => 0,
        'parent_id' => 4001,
        'pluginName' => 'agapesHotes',
        'order_menu' => '4003'
    ),
    5 => array(
        'id' => 4004,
        'slug' => 'allCourses',
        'name' => 'Liste de courses',
        'min_role_id' => 1,
        'statut' => 0,
        'parent_id' => 4002,
        'pluginName' => 'agapesHotes',
        'order_menu' => '4004'
    ),
    6 => array(
        'id' => 4005,
        'slug' => 'updateMainCourante',
        'name' => 'Main courante',
        'min_role_id' => 1,
        'statut' => 0,
        'parent_id' => 4001,
        'pluginName' => 'agapesHotes',
        'order_menu' => '4005'
    ),
    7 => array(
        'id' => 4006,
        'slug' => 'updateVivreCrue',
        'name' => 'Vivre crue',
        'min_role_id' => 1,
        'statut' => 0,
        'parent_id' => 4002,
        'pluginName' => 'agapesHotes',
        'order_menu' => '4006'
    ),
    8 => array(
        'id' => 4007,
        'slug' => 'updateMainSupplementaire',
        'name' => 'Main supplémentaire',
        'min_role_id' => 1,
        'statut' => 0,
        'parent_id' => 4001,
        'pluginName' => 'agapesHotes',
        'order_menu' => '4007'
    ),
    9 => array(
        'id' => 4008,
        'slug' => 'allEmployes',
        'name' => 'Les employés',
        'min_role_id' => 3,
        'statut' => 1,
        'parent_id' => 10,
        'pluginName' => 'agapesHotes',
        'order_menu' => '4008'
    ),
    10 => array(
        'id' => 4009,
        'slug' => 'allAccess',
        'name' => 'Les accès',
        'min_role_id' => 3,
        'statut' => 1,
        'parent_id' => 10,
        'pluginName' => 'agapesHotes',
        'order_menu' => '4009'
    ),
    11 => array(
        'id' => 4010,
        'slug' => 'allPti',
        'name' => 'Les PTI',
        'min_role_id' => 3,
        'statut' => 1,
        'parent_id' => 10,
        'pluginName' => 'agapesHotes',
        'order_menu' => '4010'
    ),
    12 => array(
        'id' => 4011,
        'slug' => 'updatePlanning',
        'name' => 'Planning',
        'min_role_id' => 1,
        'statut' => 0,
        'parent_id' => 10,
        'pluginName' => 'agapesHotes',
        'order_menu' => '4011'
    ),
    13 => array(
        'id' => 4012,
        'slug' => 'updateNoteDeFrais',
        'name' => 'Note de frais',
        'min_role_id' => 1,
        'statut' => 0,
        'parent_id' => 10,
        'pluginName' => 'agapesHotes',
        'order_menu' => '4012'
    ),
    14 => array(
        'id' => 4013,
        'slug' => 'updateAdditionalData',
        'name' => 'Données complémentaires',
        'min_role_id' => 1,
        'statut' => 0,
        'parent_id' => 4001,
        'pluginName' => 'agapesHotes',
        'order_menu' => '4013'
    ),
    15 => array(
        'id' => 4014,
        'slug' => 'ca',
        'name' => 'Chiffre d\'affaire',
        'min_role_id' => 2,
        'statut' => 1,
        'parent_id' => 10,
        'pluginName' => 'agapesHotes',
        'order_menu' => '4014'
    ),
    16 => array(
        'id' => 4015,
        'slug' => 'conso',
        'name' => 'Consommation',
        'min_role_id' => 2,
        'statut' => 1,
        'parent_id' => 10,
        'pluginName' => 'agapesHotes',
        'order_menu' => '4015'
    ),
    17 => array(
        'id' => 4016,
        'slug' => 'fraisDePersonnel',
        'name' => 'Frais de personnel',
        'min_role_id' => 2,
        'statut' => 1,
        'parent_id' => 10,
        'pluginName' => 'agapesHotes',
        'order_menu' => '4016'
    ),
    18 => array(
        'id' => 4017,
        'slug' => 'fraisGeneraux',
        'name' => 'Frais généraux',
        'min_role_id' => 2,
        'statut' => 1,
        'parent_id' => 10,
        'pluginName' => 'agapesHotes',
        'order_menu' => '4017'
    )
);

$dataCount = count($data);
$trueCount = 0;
foreach ($data as $key => $menuData) {
    if ($Menu->insertMenu($menuData['id'], $menuData['slug'], $menuData['name'], $menuData['min_role_id'], $menuData['statut'], $menuData['parent_id'], $menuData['pluginName'], $menuData['order_menu'])) {
        $trueCount++;
    }
}
if (unlink(WEB_PLUGIN_PATH . 'agapesHotes/setup.php')) {
    echo trans('Autorisations installés') . ' : ' . $trueCount . '/' . $dataCount . '.<br>';
}
