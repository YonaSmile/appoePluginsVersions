<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/app/main.php');
$Traduction = new App\Plugin\Traduction\Traduction(LANG);

$Media = new App\Media();
$Category = new App\Category();

$Category->setType('MEDIA');
$allCategories = $Category->showByType();

$listCatgories = extractFromObjToArrForList($Category->showByType(), 'id');
$allLibrary = extractFromObjToSimpleArr($allCategories, 'id', 'name');

if ($allLibrary): ?>
    <div class="container-fluid">
        <?php foreach ($allLibrary as $id => $name): ?>
            <?php
            $Media->setTypeId($id);
            $allFiles = $Media->showFiles();
            if ($allFiles): ?>
                <h5 class="libraryName p-3" id="media-<?= $id; ?>"><?= $name; ?></h5>
                <hr class="my-3 mx-5">
                <div class="card-columns">
                    <?php foreach ($allFiles as $file): ?>
                        <div class="card fileContent bg-none border-0">
                            <?php if (isImage(FILE_DIR_PATH . $file->name)): ?>
                                <img src="<?= getThumb($file->name, 370); ?>" alt="<?= $file->description; ?>"
                                     data-originsrc="<?= FILE_DIR_URL . $file->name; ?>"
                                     class="img-fluid seeOnOverlay">
                            <?php else: ?>
                                <a href="<?= FILE_DIR_URL . $file->name; ?>" target="_blank">
                                    <img src="<?= getImgAccordingExtension(getFileExtension($file->name)); ?>">
                                </a>
                            <?php endif; ?>
                            <div class="form-group mt-1 mb-0">
                                <small style="font-size: 9px;">
                                    <strong class="fileLink" data-src="<?= FILE_DIR_URL . $file->name; ?>">
                                        <button class="btn btn-sm btn-outline-info btn-block copyLinkOnClick">
                                            <?= trans('Choisir'); ?>
                                        </button>
                                    </strong>
                                </small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="my-3"></div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
<?php endif; ?>