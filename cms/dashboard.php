<?php
require('main.php');
$Cms = new App\Plugin\Cms\Cms();
$pagesCount = $Cms->showAllPages(true);

if ($pagesCount) {
    echo json_encode(
        array(
            'name' => trans('Pages'),
            'count' => $pagesCount,
            'url' => WEB_PLUGIN_URL . 'cms/page/allPages/'
        ), JSON_UNESCAPED_UNICODE
    );
}