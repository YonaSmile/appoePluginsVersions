<?php
require_once(WEB_PLUGIN_PATH . 'mehoubarim/header.php');
require_once(WEB_PLUGIN_PATH . 'mehoubarim/mehoubarim_functions.php');
$mehoubarim_url_parts = explode('/', $_SERVER['PHP_SELF']);

if (in_array('app', $mehoubarim_url_parts)) {
    if (in_array('page', $mehoubarim_url_parts)) {
        mehoubarim_connecteUser();
        $mehoubarim = mehoubarim_pageFreeToChanges();
        if (true !== $mehoubarim && false !== $mehoubarim) {
            $UserConnected = new App\Users($mehoubarim);
            $mehoubarim_html = '
            <!doctype html>
            <html lang="fr">
            <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
            <meta http-equiv="X-UA-Compatible" content="ie=edge">
            <title>APPOE</title>
            </head>
            <body style="margin:0;">
            <div style="display:flex;min-height:100vh;">
            <div style="margin:auto;text-align:center;">
            <h1 style="margin: 0;font-size: 3em;font-family: Arial, sans-serif;font-weight: 900;text-shadow: 2px 2px 1px #888;">APPOE</h1>
            <span style="display:block;font-family: Arial, sans-serif;font-weight: 100;">%message%<hr style="margin:10px;"><a href="javascript:history.back()">' . trans('Retourner à la page précédente') . '</a></span>
            </div>
            </div>
            </body>
            </html>';
            $mehoubarim_html = str_replace('%message%', trans('Cette page est en ce moment manipulé par ' . $UserConnected->getNom() . ' ' . $UserConnected->getPrenom()), $mehoubarim_html);
            echo $mehoubarim_html;
            exit();
        }
    }
}