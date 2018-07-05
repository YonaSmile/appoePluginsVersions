<?php
require('main.php');
$Rating = new App\Plugin\Rating\Rating();
$ratesCount = $Rating->showAll(true);

if($ratesCount) {
    echo json_encode(
        array(
            'name' => trans('Évaluations'),
            'count' => $ratesCount,
            'url' => WEB_PLUGIN_URL . 'rating/page/'
        ), JSON_UNESCAPED_UNICODE
    );
}