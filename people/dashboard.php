<?php
require('main.php');
$People = new App\Plugin\People\People();
$peopleCount = $People->showAll(true);

if(false !== $peopleCount) {
    echo json_encode(
        array(
            'name' => trans('Personnes'),
            'count' => $peopleCount,
            'url' => WEB_PLUGIN_URL . 'people/page/allPeople/'
        ), JSON_UNESCAPED_UNICODE
    );
}