<?php
if (checkPostAndTokenRequest()) {

    //Clean data
    $_POST = cleanRequest($_POST);

    $Response = new App\Response();

    if (isset($_POST['ADDTRADUCTION'])) {
        if (!empty($_POST['metaKeySingle'])) {

            $Traduction = new App\Plugin\Traduction\Traduction();
            $Traduction->setMetaKey($_POST['metaKeySingle']);
            $Traduction->setMetaValue($Traduction->getMetaKey());
            $Traduction->setLang(LANG);
            if ($Traduction->save()) {

                //Delete post data
                unset($_POST);

                $Response->status = 'success';
                $Response->error_code = 0;
                $Response->error_msg = trans('La nouvelle traduction a été enregistré');
            } else {
                $Response->status = 'danger';
                $Response->error_code = 1;
                $Response->error_msg = trans('Un problème est survenu lors de l\'enregistrement de la traduction');
            }
        } else {
            $Response->status = 'danger';
            $Response->error_code = 1;
            $Response->error_msg = trans('Tous les champs sont obligatoires');
        }
    }

    if (isset($_POST['ADDMULTIPLETRADS'])) {
        if (!empty($_POST['metaValue-fr'])) {

            $Traduction = new App\Plugin\Traduction\Traduction();

            foreach (LANGUAGES as $id => $lang) {
                if (!empty($_POST['metaValue-' . $id])) {
                    $Traduction->setMetaKey($_POST['metaValue-fr']);
                    $Traduction->setMetaValue($_POST['metaValue-' . $id]);
                    $Traduction->setLang($id);
                    if (!$Traduction->saveMultiple()) {

                        $success = false;
                        $Response->status = 'danger';
                        $Response->error_code = 1;
                        $Response->error_msg = trans('Un problème est survenu lors de l\'enregistrement de la traduction');

                        break;
                    }
                }
            }

            //Delete post data
            unset($_POST);

            if (empty($success)) {
                $Response->status = 'success';
                $Response->error_code = 0;
                $Response->error_msg = trans('Les nouvelles traductions ont été enregistrées');
            }

        } else {
            $Response->status = 'danger';
            $Response->error_code = 1;
            $Response->error_msg = trans('Tous les champs sont obligatoires');
        }
    }
}