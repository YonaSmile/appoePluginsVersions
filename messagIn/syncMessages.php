<?php
require_once('main.php');
if ((session_id() != '') && getUserIdSession()) {

    $MessagIn = new \App\Plugin\MessagIn\MessagIn(getUserIdSession());
    $messagesCounter = 0;

    if ($MessagIn->getData()) {
        foreach ($MessagIn->getData() as $message) {
            $messagesCounter++;
        }
    }

    echo $messagesCounter;
} else {
    echo trans('Vous avez été déconnecté');
}