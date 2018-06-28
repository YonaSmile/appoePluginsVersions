<?php
require_once('../main.php');
require_once('../include/interMap_function.php');
if (checkAjaxRequest()) {

    if (getUserIdSession()) {

        $_POST = cleanRequest($_POST);

        //Add new map
        if (!empty($_POST['addMapCategory'])
            && !empty($_POST['idMap'])
            && !empty($_POST['id'])
            && !empty($_POST['title'])
            && !empty($_POST['color'])
            && !empty($_POST['show'])
        ) {
            $InteractiveMap = new App\Plugin\InteractiveMap\InteractiveMap($_POST['idMap']);
            $map = json_decode($InteractiveMap->getData(), true);

            $_POST['id'] = slugify($_POST['id']);

            $newCategory = array(
                'id' => $_POST['id'],
                'title' => $_POST['title'],
                'color' => $_POST['color'],
                'show' => $_POST['show']
            );

            if (!in_array($_POST['id'], $map['categories'])) {

                array_push($map['categories'], $newCategory);
                $InteractiveMap->setData(json_encode($map));
                if ($InteractiveMap->updateData()) {
                    interMap_writeMapFile($InteractiveMap->getData(), $InteractiveMap->getTitle());
                    echo trans('La nouvelle catégorie a été enregistré');
                }
            } else {
                echo trans('Cette catégorie existe déjà');
            }
        }

        //Archive Map
        if (!empty($_POST['idMapDelete'])) {
            $InteractiveMap = new App\Plugin\InteractiveMap\InteractiveMap($_POST['idMapDelete']);

            $InteractiveMap->setStatus(0);
            if ($InteractiveMap->update()) {
                echo 'true';
            }
        }

        //Update Map details
        if (
            !empty($_POST['updateInterMap'])
            && !empty($_POST['idMap'])
            && !empty($_POST['parent'])
            && !empty($_POST['id'])
            && !empty($_POST['name'])
            && isset($_POST['value'])
        ) {
            $InteractiveMap = new App\Plugin\InteractiveMap\InteractiveMap($_POST['idMap']);
            $map = json_decode($InteractiveMap->getData(), true);

            for ($i = 0; $i < count($map[$_POST['parent']]); $i++) {
                if ($map[$_POST['parent']][$i]['id'] == $_POST['id']) {
                    $map[$_POST['parent']][$i][$_POST['name']] = $_POST['value'];
                    break;
                }
            }

            $InteractiveMap->setData(json_encode($map));
            if ($InteractiveMap->updateData()) {
                interMap_writeMapFile($InteractiveMap->getData(), $InteractiveMap->getTitle());
                echo 'true';
            }

        }

        //Delete primary details form Map
        if (
            !empty($_POST['deleteInterMapArr'])
            && !empty($_POST['idMap'])
            && !empty($_POST['parent'])
            && !empty($_POST['id'])
        ) {
            $InteractiveMap = new App\Plugin\InteractiveMap\InteractiveMap($_POST['idMap']);
            $map = json_decode($InteractiveMap->getData(), true);

            for ($i = 0; $i < count($map[$_POST['parent']]); $i++) {
                if ($map[$_POST['parent']][$i]['id'] == $_POST['id']) {
                    unset($map[$_POST['parent']][$i]);
                    break;
                }
            }

            $InteractiveMap->setData(json_encode($map));
            if ($InteractiveMap->updateData()) {
                interMap_writeMapFile($InteractiveMap->getData(), $InteractiveMap->getTitle());
                echo 'true';
            }

        }

        // Add map location
        if (!empty($_POST['addMapLocation'])
            && !empty($_POST['idMap'])
            && !empty($_POST['id'])
            && !empty($_POST['currentLevel'])
            && !empty($_POST['xPoint'])
            && !empty($_POST['yPoint'])
            && isset($_POST['title'])
        ) {
            $InteractiveMap = new App\Plugin\InteractiveMap\InteractiveMap($_POST['idMap']);
            $map = json_decode($InteractiveMap->getData(), true);

            $newPoint = array(
                'id' => $_POST['id'],
                'title' => $_POST['title'],
                'about' => '',
                'description' => '',
                'category' => '',
                'thumbnail' => '',
                'x' => $_POST['xPoint'],
                'y' => $_POST['yPoint'],
                'fill' => '',
                'pin' => 'hidden'
            );

            $fountCount = 0;
            foreach ($map['levels'] as $key => $level) {
                if ($level['id'] == $_POST['currentLevel']) {
                    foreach ($map['levels'][$key]['locations'] as $locKey => $location) {
                        if ($_POST['id'] == $location['id']) {
                            $fountCount++;
                            break;
                        }
                    }
                    break;
                }
            }

            if ($fountCount == 0) {
                array_push($map['levels'][$key]['locations'], $newPoint);

                $InteractiveMap->setData(json_encode($map));
                if ($InteractiveMap->updateData()) {
                    interMap_writeMapFile($InteractiveMap->getData(), $InteractiveMap->getTitle());
                    echo 'true';
                }
            } else {
                echo 'false';
            }
        }

        //Update Map location details
        if (!empty($_POST['updateMapLocation'])
            && !empty($_POST['idMap'])
            && !empty($_POST['id'])
            && !empty($_POST['level'])
        ) {
            $InteractiveMap = new App\Plugin\InteractiveMap\InteractiveMap($_POST['idMap']);
            $map = json_decode($InteractiveMap->getData(), true);

            for ($i = 0; $i <= count($map['levels']); $i++) {
                if ($map['levels'][$i]['id'] == $_POST['level']) {
                    foreach ($map['levels'][$i]['locations'] as $key => $location) {
                        if ($location['id'] == $_POST['id']) {
                            $map['levels'][$i]['locations'][$key]['title'] = !empty($_POST['title']) ? $_POST['title'] : '';
                            $map['levels'][$i]['locations'][$key]['about'] = !empty($_POST['about']) ? $_POST['about'] : '';
                            $map['levels'][$i]['locations'][$key]['description'] = !empty($_POST['description']) ? $_POST['description'] : '';
                            $map['levels'][$i]['locations'][$key]['category'] = !empty($_POST['category']) ? $_POST['category'] : '';
                            $map['levels'][$i]['locations'][$key]['pin'] = !empty($_POST['pin']) ? $_POST['pin'] : 'hidden';
                            $map['levels'][$i]['locations'][$key]['fill'] = !empty($_POST['fill']) ? $_POST['fill'] : '';
                            break;
                        }
                    }
                    break;
                }
            }

            $InteractiveMap->setData(json_encode($map));
            if ($InteractiveMap->updateData()) {
                interMap_writeMapFile($InteractiveMap->getData(), $InteractiveMap->getTitle());
                echo trans('Le point a été mis à jour');
            }
        }

        // Delete Map location
        if (!empty($_POST['deleteMapLocation'])
            && !empty($_POST['idMap'])
            && !empty($_POST['locationId'])
            && !empty($_POST['level'])
        ) {
            $InteractiveMap = new App\Plugin\InteractiveMap\InteractiveMap($_POST['idMap']);
            $map = json_decode($InteractiveMap->getData(), true);

            for ($i = 0; $i <= count($map['levels']); $i++) {
                if ($map['levels'][$i]['id'] == $_POST['level']) {
                    foreach ($map['levels'][$i]['locations'] as $key => $location) {
                        if ($location['id'] == $_POST['locationId']) {
                            unset($map['levels'][$i]['locations'][$key]);
                            break;
                        }
                    }
                    break;
                }
            }

            $InteractiveMap->setData(json_encode($map));
            if ($InteractiveMap->updateData()) {
                interMap_writeMapFile($InteractiveMap->getData(), $InteractiveMap->getTitle());
                echo trans('Le point a été supprimé');
            }

        }

        //Restart Map details
        if (!empty($_POST['rebootInterMap']) && !empty($_POST['idMap'])) {

            $InteractiveMap = new App\Plugin\InteractiveMap\InteractiveMap($_POST['idMap']);

            $jsonArray = json_encode(array(
                'mapwidth' => $InteractiveMap->getWidth(),
                'mapheight' => $InteractiveMap->getHeight(),
                'categories' => [],
                'levels' => []
            ));

            $InteractiveMap->setData($jsonArray);
            if ($InteractiveMap->updateData()) {
                interMap_writeMapFile($InteractiveMap->getData(), $InteractiveMap->getTitle());
                echo 'true';
            }
        }

        // Add / Update location's thumbnail
        if (isset($_GET['uploadThumbnail'])
            && !empty($_GET['idMap'])
            && !empty($_GET['level'])
            && !empty($_GET['idLocation'])) {

            $File = new App\Plugin\InteractiveMap\InterMapMedia($_GET['idMap']);
            $File->setUserId(getUserIdSession());

            $arrayFiles = array();
            foreach ($_FILES as $file) {
                $arrayFiles = array(
                    'name' => array($file['name']),
                    'type' => array($file['type']),
                    'tmp_name' => array($file['tmp_name']),
                    'error' => array($file['error']),
                    'size' => array($file['size']),
                );

                $File->setUploadFiles($arrayFiles);
                $thumbnail = $File->upload();
            }

            $thumbnailSrc = !empty($thumbnail['filename'][0]) ? FILE_DIR_URL . $thumbnail['filename'][0] : '';

            $InteractiveMap = new App\Plugin\InteractiveMap\InteractiveMap($_GET['idMap']);
            $map = json_decode($InteractiveMap->getData(), true);

            for ($i = 0; $i <= count($map['levels']); $i++) {
                if ($map['levels'][$i]['id'] == $_GET['level']) {
                    foreach ($map['levels'][$i]['locations'] as $key => $location) {
                        if ($location['id'] == $_GET['idLocation']) {
                            $map['levels'][$i]['locations'][$key]['thumbnail'] = $thumbnailSrc;
                            break;
                        }
                    }
                    break;
                }
            }

            $InteractiveMap->setData(json_encode($map));
            if ($InteractiveMap->updateData()) {
                interMap_writeMapFile($InteractiveMap->getData(), $InteractiveMap->getTitle());
                echo trans('L\'image a été enregistré');
            }
        }
    }
}