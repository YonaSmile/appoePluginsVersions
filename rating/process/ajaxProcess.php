<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/app/plugin/rating/main.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/app/plugin/rating/include/rating_function.php');
if (checkAjaxRequest()) {

    $_POST = cleanRequest($_POST);

    if (isset($_POST['fetch']) && !empty($_POST['widget_type']) && !empty($_POST['widget_id'])) {
        $Rating = new App\Plugin\Rating\Rating($_POST['widget_type'], $_POST['widget_id']);

        $data = getRate($Rating->getData());
        $data['widget_id'] = 'item-' . $_POST['widget_id'];

        echo json_encode($data);
    }

    if (isset($_POST['clicked_on']) && !empty($_POST['widget_type']) && !empty($_POST['widget_id'])) {

        preg_match('/star_([1-5]{1})/', $_POST['clicked_on'], $match);

        $Rating = new App\Plugin\Rating\Rating($_POST['widget_type'], $_POST['widget_id']);
        $Rating->setUser(time());
        $Rating->setScore($match[1]);

        if ($Rating->save()) {

            /*$data = getRate($Rating->getData());
            $data['widget_id'] = 'item-' . $_POST['widget_id'];

            echo json_encode($data);*/

            echo json_encode('Merci');
        }
    }

    if (isset($_POST['initRating']) && !empty($_POST['type']) && !empty($_POST['typeId'])) {

        $Rating = new App\Plugin\Rating\Rating();
        $Rating->setType($_POST['type']);
        $Rating->setTypeId($_POST['typeId']);

        if ($Rating->deleteAll()) {
            echo 'true';
        }
    }
}