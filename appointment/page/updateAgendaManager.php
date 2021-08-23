<?php

use App\Plugin\Appointment\Agenda;

require_once($_SERVER['DOCUMENT_ROOT'] . '/app/lib/template/header_admin_template.php');
if (!empty($_GET['id']) && is_numeric($_GET['id'])):
    $Agenda = new Agenda();
    $Agenda->setId($_GET['id']);
    $Agenda->show();
    echo getTitle(trans('Agenda ') . $Agenda->getName(), getAppPageSlug()); ?>

    <div class="row">
        <div class="col-12 col-lg-4 my-3" id="manageList" data-id-agenda="<?= $Agenda->getId(); ?>">
            <div class="btn-group-vertical btnGroupAgendaManager">
                <button class="btn btn-block btnAgendaManager" data-manage="availabilities">Disponibilités</button>
                <button class="btn btn-block btnAgendaManager" data-manage="typeRdv">Type de RDV</button>
                <button class="btn btn-block btnAgendaManager" data-manage="rdv">Gérer ses RDV</button>
            </div>
        </div>
        <div class="col-12 col-lg-8 my-3" id="manageType"></div>
    </div>


<?php
else:
    echo getContainerErrorMsg(trans('Cette page n\'existe pas'));
endif;
require_once($_SERVER['DOCUMENT_ROOT'] . '/app/lib/template/footer_admin_template.php'); ?>
