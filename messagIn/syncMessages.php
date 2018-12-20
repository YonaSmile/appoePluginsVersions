<?php
require_once('main.php');
if (getUserIdSession()) {

    $MessagIn = new \App\Plugin\MessagIn\MessagIn(getUserIdSession());
    $messagesCounter = 0;

    if ($MessagIn->getData()) {
        foreach ($MessagIn->getData() as $message) {
            $messagesCounter++;
        }
    }
    echo $messagesCounter;
}