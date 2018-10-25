<?php
if (checkPostAndTokenRequest()) {

    //Clean data
    $_POST = cleanRequest($_POST);

    $Response = new \App\Response();

}