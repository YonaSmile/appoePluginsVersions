<?php require('header.php');
require_once(SHOP_PATH . 'process/addStock.php');
echo getTitle(getAppPageName(), getAppPageSlug());
showPostResponse(); ?>
    <div class="container">
        <form action="" method="post" id="addStockForm">
            <?= getTokenField();
            $Product = new \App\Plugin\Shop\Product();
            $allProduct = $Product->showAll();
            $listProduct = extractFromObjToSimpleArr($allProduct, 'id', 'name');
            ?>
            <div class="row">
                <div class="col-12 my-2">
                    <?= App\Form::select('Produit', 'product_id', $listProduct, !empty($_POST['product_id']) ? $_POST['product_id'] : '', true); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-12 col-lg-6 my-2">
                    <?= App\Form::text('Quantité limité', 'limit_quantity', 'number', !empty($_POST['limit_quantity']) ? $_POST['limit_quantity'] : ''); ?>
                </div>

                <div class="col-12 col-lg-6 my-2">
                    <?= App\Form::text('Date limité', 'date_limit', 'text', !empty($_POST['date_limit']) ? $_POST['date_limit'] : '', false, 10, '', '', 'datepicker'); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-12 my-2">
                    <?= App\Form::target('ADDSTOCK'); ?>
                    <?= App\Form::submit('Enregistrer', 'ADDSTOCKSUBMIT'); ?>
                </div>
            </div>
        </form>
    </div>
<?php require('footer.php'); ?>