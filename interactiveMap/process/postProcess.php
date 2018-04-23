<?php
if (checkPostAndTokenRequest()) {

    //Clean data
    $_POST = cleanRequest($_POST);

    $Response = new App\Response();

    if (isset($_POST['ADDINTERMAP'])) {
        if (
            !empty($_POST['title'])
            && !empty($_POST['width'])
            && !empty($_POST['height'])
        ) {

            $jsonArray = json_encode(array(
                'mapwidth' => $_POST['width'],
                'mapheight' => $_POST['height'],
                'categories' => [],
                'levels' => []
            ));

            $InteractiveMap = new App\Plugin\InteractiveMap\InteractiveMap();
            $InteractiveMap->feed($_POST);
            $InteractiveMap->setData($jsonArray);
            if ($InteractiveMap->save()) {

                $Traduction = new App\Plugin\Traduction\Traduction();
                $Traduction->setMetaKey($_POST['title']);
                $Traduction->setMetaValue($Traduction->getMetaKey());
                $Traduction->setLang(LANG);
                if ($Traduction->save()) {

                    //Delete post data
                    unset($_POST);

                    $Response->status = 'success';
                    $Response->error_code = 0;
                    $Response->error_msg = trans('La nouvelle carte a été enregistré');
                } else {
                    $Response->status = 'danger';
                    $Response->error_code = 1;
                    $Response->error_msg = trans('Un problème est survenu lors de l\'enregistrement de la carte');
                }
            }
        } else {
            $Response->status = 'danger';
            $Response->error_code = 1;
            $Response->error_msg = trans('Tous les champs sont obligatoires');
        }
    }
}