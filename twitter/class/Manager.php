<?php

namespace App\Plugin\Twitter;

class Manager
{
    private $connection = null;

    public function __construct()
    {
        if (is_null($this->connection) && twitter_is_active()) {
            $this->connection = new TwitterOAuth(TWITTER_CONSUMER_KEY, TWITTER_CONSUMER_SECRET, TWITTER_ACCESS_TOKEN, TWITTER_ACCESS_TOKEN_SECRET);
        }
    }

    public function twitter_search_user($query)
    {

        return $this->connection->get('users/search', ['q' => $query]);
    }

    public function twitter_get_lists($listName = '')
    {

        return $this->connection->get('lists/list', ['screen_name' => TWITTER_USERNAME, 'Name' => $listName]);
    }

    public function twitter_get_list_members($listName)
    {

        $listId = '';
        $list = $this->twitter_get_lists($listName);

        if (is_array($list)) {
            $listId = $list[0]->id;
        }

        return $this->connection->get('lists/members', ['screen_name' => TWITTER_USERNAME, 'list_id' => $listId]);
    }

    public function twitter_get_ids_list_members($listName)
    {
        $usersIds = array();
        $usersList = $this->twitter_get_list_members($listName);

        foreach ($usersList->users as $key => $user) {
            $usersIds[$key] = $user->id_str;
        }

        return $usersIds;
    }

    public function twitter_send_directMessage($userId, $message)
    {

        $data = [
            'event' => [
                'screen_name' => TWITTER_USERNAME,
                'type' => 'message_create',
                'message_create' => [
                    'target' => [
                        'recipient_id' => $userId
                    ],
                    'message_data' => [
                        'text' => $message
                    ]
                ]
            ]
        ];
        return $this->connection->post('direct_messages/events/new', $data, true);
    }
}