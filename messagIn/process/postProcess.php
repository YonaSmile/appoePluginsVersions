<?php
if (checkPostAndTokenRequest()) {

    //Clean data
    $_POST = cleanRequest($_POST);

    $Response = new App\Response();

    if (isset($_POST['ADDMESSAGE'])
        && !empty($_POST['toUser'])
        && !empty($_POST['text'])
    ) {


        $MessagIn = new App\Plugin\MessagIn\MessagIn();

        //Add Projet
        $MessagIn->feed($_POST);
        $MessagIn->setFromUser($User->getId());


        if ($MessagIn->save()) {

            //Delete post data
            unset($_POST);

            $Response->status = 'success';
            $Response->error_code = 0;
            $Response->error_msg = trans('Le message a été envoyé');

        } else {

            $Response->status = 'danger';
            $Response->error_code = 1;
            $Response->error_msg = trans('Un problème est survenu lors de l\'envoi du message');
        }

    } else {

        $Response->status = 'danger';
        $Response->error_code = 1;
        $Response->error_msg = trans('Tous les champs sont obligatoires');
    }
}