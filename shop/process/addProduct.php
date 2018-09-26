<?php
if (checkPostAndTokenRequest()) {

    //Clean data
    $_POST = cleanRequest($_POST);

    $Response = new \App\Response();

    if (isset($_POST['ADDPRODUCT'])
        && !empty($_POST['type'])
        && !empty($_POST['name'])
        && !empty($_POST['slug'])
        && !empty($_POST['price'])
        && isset($_POST['poids'])
        && isset($_POST['dimension'])
        && isset($_POST['status'])) {

        $Product = new \App\Plugin\Shop\Product();

        //Add Produit
        $Product->feed($_POST);

        if (!$Product->exist()) {
            if ($Product->save()) {

                //Categories
                $CategoryRelation = new \App\CategoryRelations('SHOP', $Product->getId());

                if (!empty($_POST['categories'])) {
                    foreach ($_POST['categories'] as $chosenCategory) {
                        $CategoryRelation->setCategoryId($chosenCategory);
                        $CategoryRelation->save();
                    }
                }

                //Add Translation
                $Traduction = new \App\Plugin\Traduction\Traduction();
                $Traduction->setLang(LANG);
                $Traduction->setMetaKey($Product->getName());
                $Traduction->setMetaValue($Product->getName());
                $Traduction->save();

                //Delete post data
                unset($_POST);

                $Response->status = 'success';
                $Response->error_msg = trans('Le produit a été enregistré') . ' <a href="' . getPluginUrl('shop/page/updateProductData/', $Product->getId()) . '">' . trans('Voir les détails du produit') . '</a>';

            } else {

                $Response->status = 'danger';
                $Response->error_code = 1;
                $Response->error_msg = trans('Un problème est survenu lors de l\'enregistrement du produit');
            }
        } else {

            $Response->status = 'warning';
            $Response->error_code = 2;
            $Response->error_msg = trans('Ce produit exist déjà');
        }
    } else {

        $Response->status = 'danger';
        $Response->error_code = 1;
        $Response->error_msg = trans('Tous les champs sont obligatoires');
    }
}