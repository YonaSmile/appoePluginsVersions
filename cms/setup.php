<?php
require('main.php');
$Cms = new App\Plugin\Cms\Cms();
$CmsContent = new App\Plugin\Cms\CmsContent();
$CmsMenu = new App\Plugin\Cms\CmsMenu();

//Creating table
$pluginSetup = $Cms->createTable();
echo $pluginSetup ? trans('Table') . ' CMS ' . trans('activé') . '.<br>' : $pluginSetup;

$pluginSetup = $CmsMenu->createTable();
echo $pluginSetup ? trans('Table') . ' CMS Menu ' . trans('activé') . '.<br>' : $pluginSetup;

$pluginSetup = $CmsContent->createTable();
echo $pluginSetup ? trans('Table') . ' CMS Content ' . trans('activé') . '.<br>' : $pluginSetup;

//Creating autorisations
$Menu = new App\Menu();
$data = array(
    1 => array(
        'id' => 200,
        'slug' => 'cms',
        'name' => 'CMS',
        'min_role_id' => 1,
        'statut' => 1,
        'parent_id' => 10,
        'pluginName' => 'cms',
        'order_menu' => '200'
    ),
    2 => array(
        'id' => 201,
        'slug' => 'allPages',
        'name' => 'Toutes les pages',
        'min_role_id' => 1,
        'statut' => 1,
        'parent_id' => 200,
        'pluginName' => 'cms',
        'order_menu' => '201'
    ),
    3 => array(
        'id' => 202,
        'slug' => 'addPage',
        'name' => 'Nouvelle page',
        'min_role_id' => 4,
        'statut' => 1,
        'parent_id' => 200,
        'pluginName' => 'cms',
        'order_menu' => '202'
    ),
    4 => array(
        'id' => 203,
        'slug' => 'updatePage',
        'name' => 'Mise à jour de la page',
        'min_role_id' => 4,
        'statut' => 0,
        'parent_id' => 200,
        'pluginName' => 'cms',
        'order_menu' => '203'
    ),
    5 => array(
        'id' => 204,
        'slug' => 'updatePageContent',
        'name' => 'Contenu de la page',
        'min_role_id' => 1,
        'statut' => 0,
        'parent_id' => 200,
        'pluginName' => 'cms',
        'order_menu' => '204'
    ),
    6 => array(
        'id' => 205,
        'slug' => 'updateMenu',
        'name' => 'Tous les menus',
        'min_role_id' => 4,
        'statut' => 1,
        'parent_id' => 200,
        'pluginName' => 'cms',
        'order_menu' => '205'
    )
);

$dataCount = count($data);
$trueCount = 0;
foreach ($data as $key => $menuData) {
    if ($Menu->insertMenu($menuData['id'], $menuData['slug'], $menuData['name'], $menuData['min_role_id'], $menuData['statut'], $menuData['parent_id'], $menuData['pluginName'], $menuData['order_menu'])) {
        $trueCount++;
    }
}
if (unlink(WEB_PLUGIN_PATH . 'cms/setup.php')) {
    echo trans('Autorisations installés') . ' : ' . $trueCount . '/' . $dataCount . '.<br>';
}
