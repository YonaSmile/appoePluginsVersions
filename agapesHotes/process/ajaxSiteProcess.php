<?php
require_once('../main.php');
if (checkAjaxRequest()) {
    if (getUserIdSession()) {

        $_POST = cleanRequest($_POST);

        $SiteProcess = new \App\Plugin\AgapesHotes\Site();

        // ADD SITE
        if (!empty($_POST['ADDSITE'])) {

            if (!empty($_POST['nom']) && !empty($_POST['secteur_id'])
                && !empty($_POST['ref'])) {

                $SiteProcess->setRef($_POST['ref']);
                $SiteProcess->setNom($_POST['nom']);
                $SiteProcess->setSlug(slugify($_POST['nom']));
                $SiteProcess->setSecteurId($_POST['secteur_id']);
                $SiteProcess->setAlsaceMoselle(isset($_POST['alsaceMoselle']) ? 1 : 0);

                if ($SiteProcess->notExist()) {

                    if ($SiteProcess->save()) {
                        echo json_encode(true);

                        $Etablissement = new \App\Plugin\AgapesHotes\Etablissement();
                        $Etablissement->setSiteId($SiteProcess->getId());
                        $Etablissement->setNom($SiteProcess->getNom());
                        $Etablissement->setSlug(slugify($SiteProcess->getNom()));
                        $Etablissement->save();
                    }
                } else {
                    echo 'Ce nom de site exist déjà !';
                }
            } else {
                echo 'Un nom et un secteur sont attendus !';
            }
        }

        // UPDATE SITE
        if (!empty($_POST['UPDATESITE'])) {

            if (!empty($_POST['idSiteUpdate']) && !empty($_POST['nom'])
                && !empty($_POST['secteur_id']) && !empty($_POST['ref'])) {

                $SiteProcess->setId($_POST['idSiteUpdate']);
                if ($SiteProcess->show()) {

                    $SiteProcess->setRef($_POST['ref']);
                    $SiteProcess->setNom($_POST['nom']);
                    $SiteProcess->setSlug(slugify($_POST['nom']));
                    $SiteProcess->setSecteurId($_POST['secteur_id']);
                    $SiteProcess->setAlsaceMoselle(isset($_POST['alsaceMoselleUpdate']) ? 1 : 0);

                    if ($SiteProcess->notExist(true)
                        && $SiteProcess->notExistSlug(true)
                        && $SiteProcess->notExistRef(true)) {

                        if ($SiteProcess->update()) {
                            echo json_encode(true);
                        }
                    } else {
                        echo 'Le nom ou la référence du site exist déjà !';
                    }
                } else {
                    echo 'Ce site n\'existe pas !';
                }
            } else {
                echo 'Tous les champs sont obligatoires !';
            }
        }

        // ARCHIVE SITE
        if (!empty($_POST['ARCHIVESITE'])) {

            if (!empty($_POST['idSiteArchive'])) {

                $SiteProcess->setId($_POST['idSiteArchive']);
                if ($SiteProcess->show()) {

                    if ($SiteProcess->delete()) {
                        echo json_encode(true);
                    } else {
                        echo 'Impossible d\'archiver ce site !';
                    }
                } else {
                    echo 'Ce site n\'existe pas !';
                }
            }
        }
    }
}