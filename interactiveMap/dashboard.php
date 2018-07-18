<?php
require('main.php');
$InteractiveMap = new App\Plugin\InteractiveMap\InteractiveMap();
$pagesCount = $InteractiveMap->showAll(true);

if ($pagesCount) {
    echo json_encode(
        array(
            'name' => trans('Maps'),
            'count' => $pagesCount,
            'url' => WEB_PLUGIN_URL . 'interactiveMap/page/allInterMaps/'
        ), JSON_UNESCAPED_UNICODE
    );
}