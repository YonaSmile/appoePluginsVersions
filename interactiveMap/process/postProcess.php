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

                interMap_writeMapFile($InteractiveMap->getData(), $InteractiveMap->getTitle());

                $Traduction = new App\Plugin\Traduction\Traduction();
                $Traduction->setMetaKey($_POST['title']);
                $Traduction->setMetaValue($Traduction->getMetaKey());
                $Traduction->setLang(LANG);
                $Traduction->save();

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
        } else {
            $Response->status = 'danger';
            $Response->error_code = 1;
            $Response->error_msg = trans('Tous les champs sont obligatoires');
        }
    }

    if (isset($_POST['UPDATEINTERACTIVECARTE'])) {
        if (
            !empty($_POST['id'])
            && !empty($_POST['title'])
            && !empty($_POST['width'])
            && !empty($_POST['height'])
            && isset($_POST['status'])
        ) {
            $InteractiveMap = new App\Plugin\InteractiveMap\InteractiveMap();
            $InteractiveMap->feed($_POST);
            if ($InteractiveMap->notExist(true)) {
                if ($InteractiveMap->update()) {

                    //Delete post data
                    unset($_POST);

                    interMap_writeMapFile($InteractiveMap->getData(), $InteractiveMap->getTitle());
                    $Response->status = 'success';
                    $Response->error_code = 0;
                    $Response->error_msg = trans('La nouvelle carte a été enregistré');

                } else {
                    $Response->status = 'danger';
                    $Response->error_code = 1;
                    $Response->error_msg = trans('Le titre de cette carte existe déjà');
                }
            }
        } else {
            $Response->status = 'danger';
            $Response->error_code = 1;
            $Response->error_msg = trans('Tous les champs sont obligatoires');
        }
    }

    if (isset($_POST['ADDINTERMAPLEVEL'])) {
        if (!empty($_POST['idMap'])
            && !empty($_POST['id'])
            && !empty($_POST['title'])
            && isset($_FILES)
        ) {
            $InteractiveMap = new App\Plugin\InteractiveMap\InteractiveMap($_POST['idMap']);
            $map = json_decode($InteractiveMap->getData(), true);

            $File = new App\Plugin\InteractiveMap\InterMapMedia($_POST['idMap']);
            $File->setUserId(getUserIdSession());

            if (!empty($_FILES['map']['name'])) {
                $File->setUploadFiles($_FILES['map']);
                $mapFile = $File->upload();
            }

            if (!empty($_FILES['minimap']['name'])) {
                $File->setUploadFiles($_FILES['minimap']);
                $minimapFile = $File->upload();
            }

            $newLevel = array(
                'id' => $_POST['id'],
                'title' => $_POST['title'],
                'map' => !empty($mapFile['filename'][0]) ? FILE_DIR_URL . $mapFile['filename'][0] : '',
                'minimap' => !empty($minimapFile['filename'][0]) ? FILE_DIR_URL . $minimapFile['filename'][0] : '',
                'locations' => array()
            );

            if (!in_array($_POST['id'], $map['levels'])) {

                array_push($map['levels'], $newLevel);

                $InteractiveMap->setData(json_encode($map));
                if ($InteractiveMap->updateData()) {

                    interMap_writeMapFile($InteractiveMap->getData(), $InteractiveMap->getTitle());
                    $Response->status = 'success';
                    $Response->error_code = 0;
                    $Response->error_msg = trans('Le niveau a été enregistré');
                }
            } else {

                $Response->status = 'danger';
                $Response->error_code = 1;
                $Response->error_msg = trans('Ce niveau existe déjà');
            }
        } else {

            $Response->status = 'danger';
            $Response->error_code = 1;
            $Response->error_msg = trans('Tous les champs sont obligatoires');
        }
    }
}