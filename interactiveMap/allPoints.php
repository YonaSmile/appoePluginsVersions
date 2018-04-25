<?php
require('main.php');
require_once('ini.php');
if (!empty($_GET['id']) && !empty($_GET['level']) && isset($_GET['location'])): ?>
    <?php
    $InteractiveMap = new App\Plugin\InteractiveMap\InteractiveMap();
    $InteractiveMap->setId($_GET['id']);
    if ($InteractiveMap->show()) : ?>

        <?php
        $map = json_decode($InteractiveMap->getData(), true);
        $allCategories = array();
        foreach ($map['categories'] as $category) {
            $allCategories[$category['id']] = $category['id'];
        }
        for ($i = 0;
             $i < count($map['levels']);
             $i++) : ?>
            <?php if ($map['levels'][$i]['id'] == $_GET['level']) : ?>
                <?php foreach ($map['levels'][$i]['locations'] as $location): ?>
                    <?php if ($location['id'] == $_GET['location']): ?>
                        <div class="row">
                            <div class="col-12">
                                <h6>ID : <?= $location['id']; ?></h6>
                            </div>
                        </div>
                        <form method="post" class="locationForm" enctype="multipart/form-data" action="">
                            <input type="hidden" name="idMap" value="<?= $InteractiveMap->getId(); ?>">
                            <input type="hidden" name="id" value="<?= $location['id']; ?>">
                            <input type="hidden" name="updateMapLocation" value="OK">
                            <input type="hidden" name="level" value="<?= $_GET['level']; ?>">
                            <?= App\Form::text(trans('Titre'), 'title', 'text', $location['title']); ?>
                            <?= App\Form::text(trans('A Propos'), 'about', 'text', $location['about']); ?>
                            <?= App\Form::textarea(trans('description'), 'description', $location['description'], 4); ?>
                            <?= App\Form::select(trans('Catégorie'), 'category', $allCategories, $location['category']); ?>
                        </form>
                        <hr>
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <button type="button"
                                        class="btn btn-outline-primary btn-block my-2 refreshInterMapPoint">
                                    <?= trans('Rafraîchir'); ?>
                                </button>
                            </div>
                            <div class="col-12 col-md-6">
                                <button type="button"
                                        class="btn btn-outline-danger btn-block my-2 deleteInterMapPoint"
                                        data-idmap="<?= $_GET['id']; ?>" data-level="<?= $_GET['level']; ?>"
                                        data-id="<?= $location['id']; ?>"><?= trans('Supprimer'); ?></button>
                            </div>
                        </div>
                        <?php break; ?>

                    <?php endif; ?>
                <?php endforeach; ?>

                <?php break; ?>

            <?php endif; ?>
        <?php endfor; ?>
    <?php endif; ?>
<?php endif; ?>
