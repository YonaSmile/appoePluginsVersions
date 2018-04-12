<?php
define('MEHOUBARIM_JSON', WEB_PLUGIN_PATH . 'mehoubarim/mehoubarim.json');
define('VISITORS_JSON', WEB_PLUGIN_PATH . 'mehoubarim/visitors.json');
define('GLOBAL_JSON', WEB_PLUGIN_PATH . 'mehoubarim/global.json');

const STATUS_CONNECTED_USER = array(
    'Actif' => 'success',
    'En pause' => 'secondary',
    'Inactif' => 'warning',
    'Déconnecté' => 'danger'
);

/**
 * write on json file
 *
 * @param $data
 * @param $file
 */
function mehoubarim_jsonWrite($data, $file = MEHOUBARIM_JSON)
{
    $json_file = fopen($file, 'w');
    fwrite($json_file, json_encode($data));
    fclose($json_file);
}

/**
 * read a json file
 *
 * @param $file
 * @return array
 */
function mehoubarim_jsonRead($file = MEHOUBARIM_JSON)
{
    $json = file_get_contents($file);
    $parsed_json = json_decode($json, true);

    return $parsed_json;
}


/**
 * edit user statut to actif
 */
function mehoubarim_connecteUser()
{
    $userId = getUserIdSession();

    if (!empty($userId)) {

        //Get
        $parsed_json = mehoubarim_jsonRead();

        //Edit
        $parsed_json['users'][$userId]['lastConnect'] = time();
        $parsed_json['users'][$userId]['status'] = 'Actif';
        $parsed_json['users'][$userId]['pageConsulting'] = $_SERVER['REQUEST_URI'];

        //Write
        mehoubarim_jsonWrite($parsed_json);
    }
}

/**
 * @return mixed
 */
function mehoubarim_connectedUsers()
{

    //Get
    $parsed_json = mehoubarim_jsonRead();

    return $parsed_json['users'];
}

/**
 * @return bool
 */
function mehoubarim_getConnectedStatut()
{

    if (getUserIdSession() > 0) {

        //Get
        $parsed_json = mehoubarim_jsonRead();

        return $parsed_json['users'][getUserIdSession()]['status'];
    }

    return false;
}

/**
 * @param $statut
 */
function mehoubarim_updateConnectedStatus($statut)
{

    //Get
    $parsed_json = mehoubarim_jsonRead();

    if (getUserIdSession() > 0 && isset($parsed_json['users'][getUserIdSession()])) {

        //Edit
        $parsed_json['users'][getUserIdSession()]['status'] = $statut;

        //Write
        mehoubarim_jsonWrite($parsed_json);
    }

}

/**
 * @param $user
 */
function mehoubarim_logoutUser($user)
{
    //Get
    $parsed_json = mehoubarim_jsonRead();

    if (getUserIdSession() > 0 && isset($parsed_json['users'][$user])) {

        //Edit
        $parsed_json['users'][$user]['status'] = 'Déconnecté';
        $parsed_json['users'][$user]['pageConsulting'] = '';

        //Write
        mehoubarim_jsonWrite($parsed_json);
    }
}

/**
 * @return bool
 */
function mehoubarim_pageFreeToChanges()
{
    $userSessionId = getUserIdSession();

    if ($userSessionId > 0) {

        //Get
        $parsed_json = mehoubarim_jsonRead();

        //Check
        foreach ($parsed_json['users'] as $user => $param) {

            if ($user != $userSessionId) {
                if (isset($param['pageConsulting']) && $param['pageConsulting'] == $_SERVER['REQUEST_URI']) {
                    if (preg_match('/update/i', basename($_SERVER['PHP_SELF']))) {
                        if ($param['status'] == 'Actif') {

                            //Edit
                            $parsed_json['users'][$userSessionId]['pageConsulting'] = '';

                            //Write
                            mehoubarim_jsonWrite($parsed_json);

                            return $user;

                        } else {

                            //Edit
                            $parsed_json['users'][$user]['pageConsulting'] = '';

                            //Write
                            mehoubarim_jsonWrite($parsed_json);
                        }
                    }
                }
            }

        }
        return true;
    }
    return false;
}

/**
 * check & edit user status
 */
function mehoubarim_connectedUserStatus()
{
    $currentTime = time();
    $statutArray = array(
        $currentTime - (60 * 30) => 'Actif',
        $currentTime - (60 * 60 * 2) => 'En pause',
        $currentTime - (60 * 60 * 12) => 'Inactif'
    );

    //Get
    $parsed_json = mehoubarim_jsonRead();

    //Check
    foreach ($parsed_json['users'] as $user => $connectedUser) {

        $lastConnect = $connectedUser['lastConnect'];
        $statut = $connectedUser['status'];

        if ($statut != 'Déconnecté') {

            foreach ($statutArray as $timeArr => $statusArr) {
                if ($lastConnect >= $timeArr) {
                    $statut = $statusArr;
                    break;
                }
                $statut = 'Déconnecté';
            }

            //Edit
            $parsed_json['users'][$user]['status'] = $statut;
        }
    }

    //Write
    mehoubarim_jsonWrite($parsed_json);
}

/**
 * clean visitors & visits
 */
function mehoubarim_cleanVisitor()
{
    //Get
    $parsed_json = mehoubarim_jsonRead(VISITORS_JSON);

    //Edit
    $parsed_json['visitors'] = array();
    $parsed_json['totalPagesViews'] = array();

    //Write
    mehoubarim_jsonWrite($parsed_json, VISITORS_JSON);

    //Get
    $parsed_json = mehoubarim_jsonRead(GLOBAL_JSON);

    //Edit
    $parsed_json['dateBegin'] = date('Y-m-d H:i');

    //Write
    mehoubarim_jsonWrite($parsed_json, GLOBAL_JSON);

}

/**
 * get globals data
 */
function mehoubarim_getGlobal()
{
    //Get
    return mehoubarim_jsonRead(GLOBAL_JSON);
}

/**
 * get visitors & visits
 */
function mehoubarim_getVisitor()
{
    //Get
    return mehoubarim_jsonRead(VISITORS_JSON);
}

/**
 * @param $page
 * update visitors & visits
 */
function mehoubarim_updateVisitor($page)
{
    if (!empty($_SERVER['REMOTE_ADDR'])) {

        $visitorIP = $_SERVER['REMOTE_ADDR'];

        //Get
        $parsed_json = mehoubarim_jsonRead(VISITORS_JSON);

        //update visitor
        if (!isset($parsed_json['visitors'][$visitorIP])) {

            //Edit
            $parsed_json['visitors'][$visitorIP] = 1;

        } else {

            //Edit
            $parsed_json['visitors'][$visitorIP] += 1;
        }

        //update pages views
        if (!empty($parsed_json['totalPagesViews'][$page])) {

            //Edit
            $parsed_json['totalPagesViews'][$page] += 1;

        } else {

            //Edit
            $parsed_json['totalPagesViews'][$page] = 1;
        }

        //Write
        mehoubarim_jsonWrite($parsed_json, VISITORS_JSON);
    }
}

/**
 * @param $page
 * update only numbers of pages visited
 */
function mehoubarim_updatePagesViews($page)
{
    //Get
    $parsed_json = mehoubarim_jsonRead(VISITORS_JSON);

    if (!empty($parsed_json['totalPagesViews'][$page])) {

        //Edit
        $parsed_json['totalPagesViews'][$page] += 1;

    } else {

        //Edit
        $parsed_json['totalPagesViews'][$page] = 1;
    }
    //Write
    mehoubarim_jsonWrite($parsed_json, VISITORS_JSON);
}