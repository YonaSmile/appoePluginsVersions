<?php
if (checkPostAndTokenRequest()) {

    //Clean data
    $_POST = cleanRequest($_POST);

    $Response = new App\Response();

    if (isset($_POST['ADDPERSON'])) {

        if (!empty($_POST['name']) && !empty($_POST['type'])) {

            $People = new App\Plugin\People\People();

            //Add person
            $People->feed($_POST);
            if ($People->notExist()) {
                if ($People->save()) {

                    //Delete post data
                    unset($_POST);

                    $Response->status = 'success';
                    $Response->error_code = 0;
                    $Response->error_msg = trans('La personne a été enregistré') . ' <a href="' . getPluginUrl('people/page/update/', $People->getId()) . '">' . trans('Voir la personne') . '</a>';

                } else {
                    $Response->status = 'danger';
                    $Response->error_code = 1;
                    $Response->error_msg = trans('Un problème est survenu lors de l\'enregistrement de la personne');
                }
            } else {
                $Response->status = 'danger';
                $Response->error_code = 1;
                $Response->error_msg = trans('Cette personne exist déjà');
            }
        } else {
            $Response->status = 'danger';
            $Response->error_code = 1;
            $Response->error_msg = trans('Le type, le nom et l\'adresse email sont obligatoires');
        }
    }


    if (isset($_POST['UPDATEPERSON'])) {

        if (!empty($_POST['id']) && !empty($_POST['name']) && !empty($_POST['type'])) {

            $People = new App\Plugin\People\People($_POST['id']);

            //Update Person
            $People->feed($_POST);
            if ($People->notExist(true)) {
                if ($People->update()) {

                    //Delete post data
                    unset($_POST);

                    $Response->status = 'success';
                    $Response->error_code = 0;
                    $Response->error_msg = trans('La personne a été mise à jour');

                } else {

                    $Response->status = 'danger';
                    $Response->error_code = 1;
                    $Response->error_msg = trans('Un problème est survenu lors de la mise à jour de la personne');
                }
            } else {
                $Response->status = 'danger';
                $Response->error_code = 1;
                $Response->error_msg = trans('Cette personne exist déjà');
            }
        } else {

            $Response->status = 'danger';
            $Response->error_code = 1;
            $Response->error_msg = trans('Le type, le nom et l\'adresse email sont obligatoires');
        }
    }
}