<?php
if (checkPostAndTokenRequest()) {

    //Clean data
    $_POST = cleanRequest($_POST);

    $Response = new \App\Response();
    $Response->MediaTabactive = false;

    if (isset($_POST['SAVEPRODUCTCONTENT'])) {

        if (!empty($_POST['productContent']) && !empty($_POST['productId'])) {

            $ProductContent = new \App\Plugin\Shop\ProductContent($_POST['productId'], LANG);
            $ProductContent->setContent($_POST['productContent']);
            $ProductContent->setResume(!empty($_POST['resume']) ? $_POST['resume'] : null);

            if (!empty($ProductContent->getId())) {
                if ($ProductContent->update()) {

                    $Response->status = 'success';
                    $Response->error_code = 0;
                    $Response->error_msg = trans('Le contenu du produit a été mise à jour');
                }
            } else {
                if ($ProductContent->save()) {

                    $Response->status = 'success';
                    $Response->error_code = 0;
                    $Response->error_msg = trans('Le contenu du produit a été enregistré');
                }
            }

            //Delete post data
            unset($_POST);

        } else {

            $Response->status = 'danger';
            $Response->error_code = 1;
            $Response->error_msg = trans('Le contenu du produit est obligatoire');
        }
    }

    if (isset($_POST['ADDIMAGESTOPRODUCT']) && !empty($_POST['productId'])) {

        $html = '';
        $selectedFilesCount = 0;

        $ShopMedia = new \App\Plugin\Shop\ShopMedia($_POST['productId']);
        $ShopMedia->setUserId(getUserIdSession());

        //Get uploaded files
        if (!empty($_FILES)) {
            $ShopMedia->setUploadFiles($_FILES['inputFile']);
            $files = $ShopMedia->upload();
            $html .= trans('Fichiers importés') . ' : <strong>' . $files['countUpload'] . '</strong>. ' . (!empty($files['errors']) ? '<br><span class="text-danger">' . $files['errors'] . '</span>' : '');
        }

        //Get selected files
        if (!empty($_POST['textareaSelectedFile'])) {

            $selectedFiles = $_POST['textareaSelectedFile'];

            if (strpos($selectedFiles, '|||')) {
                $files = explode('|||', $selectedFiles);
            } else {
                $files = array($selectedFiles);
            }

            foreach ($files as $key => $file) {
                $ShopMedia->setName($file);
                if ($ShopMedia->save()) $selectedFilesCount++;
            }

            $html .= trans('Fichiers sélectionnés enregistrés') . ' <strong>' . $selectedFilesCount . '</strong>.';
        }

        $Response->status = 'info';
        $Response->error_code = 1;
        $Response->error_msg = $html;
        $Response->MediaTabactive = true;
    }
}