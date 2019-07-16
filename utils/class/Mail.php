<?php

namespace App\Plugin\Utils;

class Mail
{

    public static function checkAjaxPostRecaptcha(array $post, $serverSecretKey)
    {

        //Check Ajax Request
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

            //User Stay Connected
            if (function_exists('mehoubarim_connecteUser')) {
                mehoubarim_connecteUser();
            }

            //Clean Post
            $post = cleanRequest($post);

            //Check and Confirm Recaptcha V3
            if (!empty($post['g-recaptcha-response'])) {
                if (false === checkRecaptcha($serverSecretKey, $post['g-recaptcha-response'])) {
                    return false;
                }
            } else {
                return false;
            }
        }

        return true;
    }
}