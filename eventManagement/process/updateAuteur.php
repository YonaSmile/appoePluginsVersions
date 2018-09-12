<?php
if (checkPostAndTokenRequest()) {

    //Clean data
    $_POST = cleanRequest($_POST);

    $Response = new \App\Response();

    if (
        !empty($_POST['id'])
        && !empty($_POST['name'])) {

        $Auteur = new \App\Plugin\EventManagement\Auteur();

        //Update auteur
        $Auteur->feed($_POST);

        if ($Auteur->notExist()) {
            if ($Auteur->update()) {

                //Delete post data
                unset($_POST);

                $Response->status = 'success';
                $Response->error_code = 0;
                $Response->error_msg = trans('L\'auteur a été mis à jour');

            } else {

                $Response->status = 'danger';
                $Response->error_code = 1;
                $Response->error_msg = trans('Un problème est survenu lors de la mise à jour de l\'auteur');
            }
        } else {

            $Response->status = 'danger';
            $Response->error_code = 1;
            $Response->error_msg = trans('Le nom de l\'auteur est déjà utilisé');
        }

    } else {

        $Response->status = 'danger';
        $Response->error_code = 1;
        $Response->error_msg = trans('Le nom de l\'auteur est obligatoire');
    }
}