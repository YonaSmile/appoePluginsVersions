<?php
if (checkPostAndTokenRequest()) {

    //Clean data
    $_POST = cleanRequest($_POST);

    $Response = new \App\Response();

    if (!empty($_POST['name'])) {

        $Auteur = new \App\Plugin\EventManagement\Auteur();

        //Add Auteur
        $Auteur->feed($_POST);

        if ($Auteur->notExist()) {
            if ($Auteur->save()) {

                //Delete post data
                unset($_POST);

                $Response->status = 'success';
                $Response->error_code = 0;
                $Response->error_msg = trans('Le nouvel auteur a été enregistré') . ' <a href="' . getPluginUrl('eventManagement/page/auteur/', $Auteur->getId()) . '">' . trans('Voir l\'auteur') . '</a>';


            } else {

                $Response->status = 'danger';
                $Response->error_code = 1;
                $Response->error_msg = trans('Un problème est survenu lors de l\'enregistrement de l\'auteur');
            }
        } else {

            $Response->status = 'warning';
            $Response->error_code = 2;
            $Response->error_msg = trans('Cet auteur exist déjà');
        }
    } else {

        $Response->status = 'danger';
        $Response->error_code = 1;
        $Response->error_msg = trans('Le nom de l\'auteur est obligatoire');
    }
}