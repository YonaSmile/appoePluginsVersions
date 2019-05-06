<?php
require_once(WEB_PLUGIN_PATH . 'twitter/ini.php');

use App\Plugin\Twitter\Manager;

function twitter_send_message_to_lists(array $lists, $message)
{

    if (is_array($lists)) {

        $Manager = new Manager();
        $userIds = array();

        foreach ($lists as $key => $listName) {
            $userIds[] = $Manager->twitter_get_ids_list_members($listName);
        }

        foreach (flatten($userIds) as $userId) {
            $Manager->twitter_send_directMessage($userId, $message);
        }

        return true;
    }

    return false;
}