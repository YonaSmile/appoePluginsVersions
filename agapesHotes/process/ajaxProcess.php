<?php
require_once('../main.php');
if (checkAjaxRequest()) {

    if (getUserIdSession()) {

        $_POST = cleanRequest($_POST);


    }
}