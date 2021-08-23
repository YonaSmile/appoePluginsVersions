<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/app/lib/template/header_admin_template.php');
echo getTitle(getAppPageName(), getAppPageSlug()); ?>

    <div class="row">
        <div class="col-12 mb-3"><h5 class="agendaTitle">Ajouter un agenda</h5></div>
        <div class="col-12">
            <form id="addAgendaForm">
                <div class="form-row">
                    <div class="col-12 col-md-4 mb-2">
                        <?= \App\Form::input('agendaName', ['title' => 'Nom de l\'agenda', 'required' => true]); ?>
                    </div>
                    <div class="col-12 col-md-2 mb-2 d-flex align-items-end">
                        <?= App\Form::btn('OK', 'ADDAGENDASUBMIT'); ?>
                    </div>
                </div>
            </form>
        </div>

        <div class="col-12 py-2 mt-5 mb-3"><h5>Mes agendas</h5></div>
        <div class="col-12" id="agendas"></div>
    </div>


<?php require_once($_SERVER['DOCUMENT_ROOT'] . '/app/lib/template/footer_admin_template.php'); ?>