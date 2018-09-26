<?php
require('main.php');
$Commande = new \App\Plugin\Shop\Commande();
$CommandeDetails = new \App\Plugin\Shop\CommandeDetails();
$Product = new \App\Plugin\Shop\Product();
$ProductContent = new \App\Plugin\Shop\ProductContent();
$ProductMeta = new \App\Plugin\Shop\ProductMeta();
$Stock = new \App\Plugin\Shop\Stock();

//Creating tables
$pluginSetup = $Commande->createTable();
echo $pluginSetup ? trans('Table') . ' COMMANDE ' . trans('activé') . '.<br>' : $pluginSetup;
$pluginSetup = $CommandeDetails->createTable();
echo $pluginSetup ? trans('Table') . ' COMMANDE DETAILS ' . trans('activé') . '.<br>' : $pluginSetup;
$pluginSetup = $Product->createTable();
echo $pluginSetup ? trans('Table') . ' PRODUIT ' . trans('activé') . '.<br>' : $pluginSetup;
$pluginSetup = $ProductContent->createTable();
echo $pluginSetup ? trans('Table') . ' PRODUIT CONTENT' . trans('activé') . '.<br>' : $pluginSetup;
$pluginSetup = $ProductMeta->createTable();
echo $pluginSetup ? trans('Table') . ' PRODUIT META ' . trans('activé') . '.<br>' : $pluginSetup;
$pluginSetup = $Stock->createTable();
echo $pluginSetup ? trans('Table') . ' STOCK ' . trans('activé') . '.<br>' : $pluginSetup;

//Creating autorisations
$Menu = new \App\Menu();
$data = array(
    1 => array(
        'id' => 700,
        'slug' => 'shop',
        'name' => 'Boutique',
        'min_role_id' => 1,
        'statut' => 1,
        'parent_id' => 10,
        'pluginName' => 'shop',
        'order_menu' => '700'
    ),
    2 => array(
        'id' => 701,
        'slug' => 'commandes',
        'name' => 'Commandes',
        'min_role_id' => 1,
        'statut' => 1,
        'parent_id' => 700,
        'pluginName' => 'shop',
        'order_menu' => '701'
    ),
    3 => array(
        'id' => 702,
        'slug' => 'products',
        'name' => 'Produits',
        'min_role_id' => 1,
        'statut' => 1,
        'parent_id' => 700,
        'pluginName' => 'shop',
        'order_menu' => '702'
    ),
    4 => array(
        'id' => 703,
        'slug' => 'addProduct',
        'name' => 'Nouveau produit',
        'min_role_id' => 1,
        'statut' => 1,
        'parent_id' => 700,
        'pluginName' => 'shop',
        'order_menu' => '703'
    ),
    5 => array(
        'id' => 704,
        'slug' => 'stock',
        'name' => 'Stock',
        'min_role_id' => 1,
        'statut' => 1,
        'parent_id' => 700,
        'pluginName' => 'shop',
        'order_menu' => '704'
    ),
    6 => array(
        'id' => 705,
        'slug' => 'addStock',
        'name' => 'Nouveau Stock',
        'min_role_id' => 1,
        'statut' => 1,
        'parent_id' => 700,
        'pluginName' => 'shop',
        'order_menu' => '705'
    ),
    7 => array(
        'id' => 706,
        'slug' => 'updateProduct',
        'name' => 'Mise à jour du produit',
        'min_role_id' => 1,
        'statut' => 0,
        'parent_id' => 700,
        'pluginName' => 'shop',
        'order_menu' => '706'
    ),
    8 => array(
        'id' => 707,
        'slug' => 'updateStock',
        'name' => 'Mise à jour du Stock',
        'min_role_id' => 1,
        'statut' => 0,
        'parent_id' => 700,
        'pluginName' => 'shop',
        'order_menu' => '707'
    ),
    9 => array(
        'id' => 708,
        'slug' => 'updateProductData',
        'name' => 'Détails du produit',
        'min_role_id' => 1,
        'statut' => 0,
        'parent_id' => 700,
        'pluginName' => 'shop',
        'order_menu' => '708'
    ),
    10 => array(
        'id' => 709,
        'slug' => 'shopArchives',
        'name' => 'Archives',
        'min_role_id' => 1,
        'statut' => 1,
        'parent_id' => 700,
        'pluginName' => 'shop',
        'order_menu' => '709'
    )
);

$dataCount = count($data);
$trueCount = 0;
foreach ($data as $key => $menuData) {
    if ($Menu->insertMenu($menuData['id'], $menuData['slug'], $menuData['name'], $menuData['min_role_id'], $menuData['statut'], $menuData['parent_id'], $menuData['pluginName'], $menuData['order_menu'])) {
        $trueCount++;
    }
}
if (unlink(WEB_PLUGIN_PATH . 'shop/setup.php')) {
    echo trans('Autorisations installés') . ' : ' . $trueCount . '/' . $dataCount . '.<br>';
}
