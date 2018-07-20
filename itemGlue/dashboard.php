<?php
require('main.php');
$Article = new App\Plugin\ItemGlue\Article();
$articlesCount = $Article->showAll(true);

if (false !== $articlesCount) {
    echo json_encode(
        array(
            'name' => trans('Articles'),
            'count' => $articlesCount,
            'url' => WEB_PLUGIN_URL . 'itemGlue/page/allArticles/'
        ), JSON_UNESCAPED_UNICODE
    );
}