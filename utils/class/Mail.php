<?php

namespace App\Plugin\Utils;

class Mail
{

    public static function checkAjaxPostRecaptcha(array $post, $serverSecretKey, $connectedStatus = false)
    {

        //Check Ajax Request
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

            //User Stay Connected
            if ($connectedStatus && function_exists('mehoubarim_connecteUser')) {
                mehoubarim_connecteUser();
            }

            //Clean Post
            $recaptchaField = cleanRequest($post['g-recaptcha-response']);

            //Check and Confirm Recaptcha V3
            if (!empty($recaptchaField)) {
                return checkRecaptcha($serverSecretKey, $recaptchaField);
            }
        }

        return false;
    }
}