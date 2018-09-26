<?php if (empty($_GET['id'])): ?>
    <?php header('location:products.php'); ?>
<?php else: ?>
    <?php require('header.php');
    $Stock = new \App\Plugin\Shop\Stock($_GET['id']);
    require_once('../process/updateStock.php');
    $Product = new \App\Plugin\Shop\Product($Stock->getProductId());
    ?>
    <?= getTitle('Limite de stock pour ' . $Product->getName(), $Page->getSlug()); ?>
    <div class="container">
        <?php if (isset($Response)): ?>
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-<?= $Response->display()->status ?>" role="alert">
                        <?= $Response->display()->error_msg; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <form action="" method="post">
            <input type="hidden" name="stock_id" value="<?= $Stock->getId(); ?>">
            <input type="hidden" name="product_id" value="<?= $Stock->getProductId(); ?>">

            <div class="row">

                <div class="col-12 col-lg-6 my-2">
                    <?= \App\Form::text('Quantité limité', 'limit_quantity', 'number', $Stock->getLimitQuantity(), false, 10, 'aria-describedby="limit_quantity_help"'); ?>
                    <small id="limit_quantity_help" class="form-text text-muted">
                        <?= trans('Quantité restante'); ?> <?= $Product->getRemainingQuantity(); ?>
                    </small>
                </div>

                <div class="col-12 col-lg-6 my-2">
                    <?= \App\Form::text('Date limité', 'date_limit', 'text', $Stock->getDateLimit(), false, 10, '', '', 'datepicker'); ?>
                </div>

            </div>
            <div class="row">
                <div class="col-12 my-2">
                    <?= \App\Form::target('UPDATESTOCK'); ?>
                    <?= \App\Form::submit('Enregistrer', 'UPDATESTOCKSUBMIT'); ?>
                </div>
            </div>
        </form>
    </div>
    <?php require('footer.php'); ?>
<?php endif; ?>