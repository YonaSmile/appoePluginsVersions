<?php
if (checkPostAndTokenRequest()) {

    //Clean data
    $_POST = cleanRequest($_POST);

    $Response = new App\Response();

    if (!empty($_POST['id'])
        && !empty($_POST['titre'])
        && !empty($_POST['auteurId'])
        && !empty($_POST['description'])
    ) {

        $Event = new App\Plugin\EventManagement\Event();

        //Update Event
        $Event->feed($_POST);

        if (!empty($_FILES['image']['name'])) {
            $Event->uploadFile($_FILES['image']);
        }

        if ($Event->update()) {

            //Delete post data
            unset($_POST);

            $Response->status = 'success';
            $Response->error_code = 0;
            $Response->error_msg = trans('L\'évènement a été mise à jour');

        } else {

            $Response->status = 'danger';
            $Response->error_code = 1;
            $Response->error_msg = trans('Un problème est survenu lors de la mise à jour de l\'évènement');
        }

    } else {

        $Response->status = 'danger';
        $Response->error_code = 1;
        $Response->error_msg = trans('Le titre, la description, la durée et l\'auteur de l\'évènement sont obligatoires');
    }
}