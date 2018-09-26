<?php
if (checkPostAndTokenRequest()) {

    //Clean data
    $_POST = cleanRequest($_POST);

    $Response = new \App\Response();

    if (isset($_POST['UPDATEPRODUCT'])
        && !empty($_POST['type'])
        && !empty($_POST['name'])
        && !empty($_POST['slug'])
        && !empty($_POST['price'])
        && isset($_POST['poids'])
        && isset($_POST['dimension'])
        && isset($_POST['status'])) {

        //update Produit
        $Product->feed($_POST);

        if (!$Product->exist(true)) {
            if ($Product->update()) {

                //Categories
                $CategoryRelation = new \App\CategoryRelations('SHOP', $Product->getId());
                $allCategories = $CategoryRelation->getData();
                $allSimpleCategories = extractFromObjToSimpleArr($allCategories, 'id', 'categoryId');

                if (!empty($_POST['categories'])) {

                    if (!is_null($allCategories)) {
                        foreach ($allCategories as $category) {
                            if (!in_array($category->categoryId, $_POST['categories'])) {
                                $CategoryRelation->setId($category->id);
                                $CategoryRelation->delete();
                            }
                        }
                    }

                    foreach ($_POST['categories'] as $chosenCategory) {
                        if (!in_array($chosenCategory, $allSimpleCategories)) {
                            $CategoryRelation->setCategoryId($chosenCategory);
                            $CategoryRelation->save();
                        }
                    }

                } else {

                    if (!is_null($allCategories)) {
                        foreach ($allCategories as $category) {
                            $CategoryRelation->setId($category->id);
                            $CategoryRelation->delete();
                        }
                    }
                }

                unset($_POST);

                $Response->status = 'success';
                $Response->error_msg = 'Le produit a été mis à jour';

            } else {

                $Response->status = 'danger';
                $Response->error_code = 1;
                $Response->error_msg = 'Un problème est survenu lors de la mise à jour du produit';
            }
        } else {

            $Response->status = 'warning';
            $Response->error_code = 2;
            $Response->error_msg = trans('Le nom du produit exist déjà pour le même vendeur ou bien le lien du produit est déjà occupé');
        }

    } else {

        $Response->status = 'danger';
        $Response->error_code = 1;
        $Response->error_msg = trans('Tous les champs sont obligatoires');
    }
}