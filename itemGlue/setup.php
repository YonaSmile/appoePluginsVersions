<?php
require('main.php');
$Article = new App\Plugin\ItemGlue\Article();
$ArticleContent = new App\Plugin\ItemGlue\ArticleContent();
$ArticleMeta = new App\Plugin\ItemGlue\ArticleMeta();

//Creating table
$pluginSetup = $Article->createTable();
echo $pluginSetup ? trans('Table') . ' Articles ' . trans('activé') . '.<br>' : $pluginSetup;

$pluginSetup = $ArticleContent->createTable();
echo $pluginSetup ? trans('Table') . ' Articles Content ' . trans('activé') . '.<br>' : $pluginSetup;

$pluginSetup = $ArticleMeta->createTable();
echo $pluginSetup ? trans('Table') . ' Articles Meta ' . trans('activé') . '.<br>' : $pluginSetup;

//Creating autorisations
$Menu = new App\Menu();
$data = array(
    1 => array(
        'id' => 600,
        'slug' => 'itemGlue',
        'name' => 'Articles',
        'min_role_id' => 1,
        'statut' => 1,
        'parent_id' => 10,
        'pluginName' => 'itemGlue',
        'order_menu' => '600'
    ),
    2 => array(
        'id' => 601,
        'slug' => 'allArticles',
        'name' => 'Tous les articles',
        'min_role_id' => 1,
        'statut' => 1,
        'parent_id' => 600,
        'pluginName' => 'itemGlue',
        'order_menu' => '601'
    ),
    3 => array(
        'id' => 602,
        'slug' => 'addArticle',
        'name' => 'Nouvel article',
        'min_role_id' => 4,
        'statut' => 1,
        'parent_id' => 600,
        'pluginName' => 'itemGlue',
        'order_menu' => '602'
    ),
    4 => array(
        'id' => 603,
        'slug' => 'updateArticle',
        'name' => 'Mise à jour de l\'article',
        'min_role_id' => 4,
        'statut' => 0,
        'parent_id' => 600,
        'pluginName' => 'itemGlue',
        'order_menu' => '603'
    ),
    5 => array(
        'id' => 604,
        'slug' => 'updateArticleContent',
        'name' => 'Contenu de l\'article',
        'min_role_id' => 1,
        'statut' => 0,
        'parent_id' => 600,
        'pluginName' => 'itemGlue',
        'order_menu' => '604'
    )
);

$dataCount = count($data);
$trueCount = 0;
foreach ($data as $key => $menuData) {
    if ($Menu->insertMenu($menuData['id'], $menuData['slug'], $menuData['name'], $menuData['min_role_id'], $menuData['statut'], $menuData['parent_id'], $menuData['pluginName'], $menuData['order_menu'])) {
        $trueCount++;
    }
}
if (unlink(WEB_PLUGIN_PATH . 'itemGlue/setup.php')) {
    echo trans('Autorisations installés') . ' : ' . $trueCount . '/' . $dataCount . '.<br>';
}
