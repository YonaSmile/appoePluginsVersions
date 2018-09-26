<?php
if (checkPostAndTokenRequest()) {

    //Clean data
    $_POST = cleanRequest($_POST);

    $Response = new \App\Response();

    if (!empty($_POST['ADDSTOCK'])
        && !empty($_POST['product_id'])
        && (!empty($_POST['limit_quantity']) || !empty($_POST['date_limit']))) {

        $Stock = new \App\Plugin\Shop\Stock();

        $limitQuantity = !empty($_POST['limit_quantity']) ? $_POST['limit_quantity'] : NULL;
        $limitDate = !empty($_POST['date_limit']) ? $_POST['date_limit'] : NULL;

        //Add Stock
        $Stock->setProductId($_POST['product_id']);
        $Stock->setLimitQuantity($limitQuantity);
        $Stock->setDateLimit($limitDate);

        if (!$Stock->exist()) {
            if ($Stock->save()) {

                unset($_POST);

                $Response->status = 'success';
                $Response->error_msg = 'Le stock a été enregistré.';

            } else {

                $Response->status = 'danger';
                $Response->error_code = 1;
                $Response->error_msg = 'Un problème est survenu lors de l\'enregistrement du stock';
            }
        } else {

            $Response->status = 'warning';
            $Response->error_code = 2;
            $Response->error_msg = 'Ce stock exist déjà';
        }
    } else {

        $Response->status = 'danger';
        $Response->error_code = 1;
        $Response->error_msg = 'Tous les champs sont obligatoires';
    }
}