<?php
if (checkPostAndTokenRequest()) {

    //Clean data
    $_POST = cleanRequest($_POST);

    $Response = new App\Response();

    if (!empty($_POST['titre'])
        && !empty($_POST['auteurId'])
        && !empty($_POST['description'])
    ) {

        $Event = new App\Plugin\EventManagement\Event();

        //Add Event
        $Event->feed($_POST);

        if ($Event->notExist()) {

            if (!empty($_FILES['image']['name'])) {
                $Event->uploadFile($_FILES['image']);
            }

            if ($Event->save()) {

                //Delete post data
                unset($_POST);

                $Response->status = 'success';
                $Response->error_code = 0;
                $Response->error_msg = trans('Le nouvel évènement a été enregistré') . ' <a href="' . getPluginUrl('eventManagement/page/event/', $Event->getId()) . '">' . trans('Voir l\'évènement') . '</a>';

            } else {

                $Response->status = 'danger';
                $Response->error_code = 1;
                $Response->error_msg = trans('Un problème est survenu lors de l\'enregistrement de l\'évènement');
            }
        } else {

            $Response->status = 'warning';
            $Response->error_code = 2;
            $Response->error_msg = trans('Cet évènement exist déjà');
        }
    } else {

        $Response->status = 'danger';
        $Response->error_code = 1;
        $Response->error_msg = trans('Le titre, la description, la durée et l\'auteur de l\'évènement sont obligatoires');
    }
}