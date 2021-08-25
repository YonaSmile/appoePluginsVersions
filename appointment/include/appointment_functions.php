<?php

use App\Form;
use App\Hook;
use App\Plugin\Appointment\Agenda;
use App\Plugin\Appointment\AgendaMeta;
use App\Plugin\Appointment\Availabilities;
use App\Plugin\Appointment\Client;
use App\Plugin\Appointment\Exception;
use App\Plugin\Appointment\Rdv;
use App\Plugin\Appointment\RdvType;
use App\Plugin\Appointment\RdvTypeForm;

const APPOINTMENT_PATH = WEB_PLUGIN_PATH . 'appointment/';
const APPOINTMENT_URL = WEB_PLUGIN_URL . 'appointment/';
const APPOINTMENT_TABLES = array(
    TABLEPREFIX . 'appoe_plugin_appointment_agendas',
    TABLEPREFIX . 'appoe_plugin_appointment_agendasmetas',
    TABLEPREFIX . 'appoe_plugin_appointment_availabilities',
    TABLEPREFIX . 'appoe_plugin_appointment_rdvtypes',
    TABLEPREFIX . 'appoe_plugin_appointment_rdvtypesform',
    TABLEPREFIX . 'appoe_plugin_appointment_rdv',
    TABLEPREFIX . 'appoe_plugin_appointment_clients',
    TABLEPREFIX . 'appoe_plugin_appointment_exception',
);
const APPOINTMENT_TIMEOUT_VALIDATION = 24; // in hours
const APPOINTMENT_AGENDA_CHOICE_TITLE = 'Choisir l\'agenda';
const APPOINTMENT_RDVTYPE_CHOICE_TITLE = 'Choisir le type de rendez-vous';
const APPOINTMENT_DATES_CHOICE_TITLE = 'Choisir votre rendez-vous';
const APPOINTMENT_FORM_TITLE = 'Vos coordonnées de contact';

Hook::add_action('cron', 'appointment_cron');

/********************************** BACK **************************************/
function appointment_agenda_admin_getAll()
{
    $html = '';
    $Agenda = new Agenda();
    if ($agendas = $Agenda->showAll()):
        ob_start();
        foreach ($agendas as $agenda): ?>
            <div class="agendaInfos py-3 border-top d-flex align-items-center" data-id-agenda="<?= $agenda->id; ?>">
                <div class="nameInputContainer"><?= Form::input('agendaName-' . $agenda->id,
                        ['val' => $agenda->name, 'class' => 'font-weight-normal']); ?></div>
                <div><?= Form::switch('agendaStatus-' . $agenda->id,
                        ['val' => $agenda->status ? 'true' : '', 'parentClass' => 'd-inline ml-3']); ?></div>
                <div class="ml-auto"><a
                            href="<?= WEB_PLUGIN_URL . 'appointment/page/agendaManager/' . $agenda->id . '/'; ?>"
                            class="btn btn-sm btn-outline-info">Gérer cet agenda</a></div>
                <button type="button" class="btn deleteAgenda"><i class="far fa-trash-alt"></i></button>
            </div>
        <?php endforeach;
        $html .= ob_get_clean();
    endif;

    return $html;
}

/**
 * @param $idAgenda
 * @return string
 */
function appointment_settings_admin_getAll($idAgenda)
{
    $html = '';
    $Agenda = new Agenda();
    $Agenda->setId($idAgenda);
    if ($Agenda->show()):

        $html .= '<h5 class="agendaTitle">Paramètres</h5><p class="text-muted">Gérer vos paramètres.</p>';

    endif;

    return $html;
}

/**
 * @param $idAgenda
 * @return string
 */
function appointment_availabilities_admin_getAll($idAgenda)
{
    $html = '';
    $Agenda = new Agenda();
    $Agenda->setId($idAgenda);
    if ($Agenda->show()):

        $week = appointment_getWeekDays();
        $html .= '<h5 class="agendaTitle">Disponibilités</h5><p class="text-muted">Gérer ses disponibilités en renseignant les jours et les horaires de prise de rendez-vous disponible.</p>';
        $html .= '<form id="addAvailability" class="mt-4 mb-5" data-id-agenda="' . $idAgenda . '"><div class="form-row">';
        $html .= '<div class="col-12 col-lg-4">' . Form::select('Jour', 'day', $week) . '</div>';
        $html .= '<div class="col-12 col-lg-3">' . Form::selectTime('Heure début', 'start', true, 0, 24, 50, 10) . '</div>';
        $html .= '<div class="col-12 col-lg-3">' . Form::selectTime('Heure fin', 'end', true, 0, 24, 50, 10) . '</div>';
        $html .= '<div class="col-12 col-lg-2 d-flex align-items-end">' . Form::btn('OK', 'ADDAVAILIBILITYSUBMIT') . '</div>';
        $html .= '</div></form>';

        $Availability = new App\Plugin\Appointment\Availabilities();
        $Availability->setIdAgenda($Agenda->getId());
        if ($availabilities = $Availability->showAll()):

            $availabilities = groupMultipleKeysObjectsArray($availabilities, 'day');
            ksort($availabilities);
            ob_start();
            foreach ($availabilities as $day => $listAvailability): ?>
                <div class="agendaInfos py-3 border-top" data-id-agenda="<?= $Agenda->getId(); ?>">
                    <strong class="colorPrimary"><?= $week[$day]; ?></strong>
                    <ul class="mt-2 mb-0">
                        <?php foreach ($listAvailability as $availability): ?>
                            <li class="my-0"><?= minutesToHours($availability->start); ?>
                                - <?= minutesToHours($availability->end); ?>
                                <button type="button" data-id-availability="<?= $availability->id; ?>"
                                        class="btn px-1 py-0 deleteAvailability"><i class="far fa-trash-alt"></i>
                                </button>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endforeach;
            $html .= ob_get_clean();
        endif;
    endif;

    return $html;
}

/**
 * @param $idAgenda
 * @return string
 */
function appointment_typeRdv_admin_getAll($idAgenda)
{
    $html = '';
    $Agenda = new Agenda();
    $Agenda->setId($idAgenda);
    if ($Agenda->show()):

        $html .= '<h5 class="agendaTitle">Type de rendez-vous</h5><p class="text-muted">Nommez les différents types 
        de rendez-vous que vous proposez et fixer une durée à chaque type de rendez-vous.</p>';
        $html .= '<form id="addTypeRdv" class="mt-4 mb-5" data-id-agenda="' . $idAgenda . '"><div class="form-row">';
        $html .= '<div class="col-12 col-lg-8 mb-2">' . Form::input('name', ['title' => 'Nom du rendez-vous', 'placeholder' => 'Nommez le type de rendez-vous']) . '</div>';
        $html .= '<div class="col-12 col-lg-4 mb-2">' . Form::duration('duration', ['title' => 'Durée de rendez-vous', 'minTxt' => 'minutes', 'required' => true]) . '</div>';
        $html .= '<div class="col-12 mb-2">' . Form::textarea('Informations supplémentaires', 'information', '', 3, false, '', '', 'Décrivez ou donnez des informations sur ce type de rendez-vous') . '</div>';
        $html .= '<div class="col-12 col-lg-2 d-flex align-items-end">' . Form::btn('OK', 'ADDRDVTYPESUBMIT') . '</div>';
        $html .= '</div></form>';

        $RdvType = new App\Plugin\Appointment\RdvType();
        $RdvType->setIdAgenda($Agenda->getId());
        if ($rdvTypes = $RdvType->showAll()):

            ob_start();
            foreach ($rdvTypes as $rdvType): ?>
                <div class="agendaInfos py-3 border-top d-flex flex-wrap align-items-center"
                     data-id-rdv-type="<?= $rdvType->id; ?>" data-id-agenda="<?= $Agenda->getId(); ?>">
                    <div class="nameInputContainer mb-2 mb-lg-0"><?= Form::input('rdvTypeName-' . $rdvType->id,
                            ['val' => $rdvType->name, 'class' => 'border-0']); ?></div>
                    <div class="mx-2 durationSelectContainer mb-2 mb-lg-0"><?= Form::duration('rdvTypeDuration-' . $rdvType->id,
                            ['val' => $rdvType->duration, 'parentClass' => 'd-inline ml-3']); ?>minutes
                    </div>
                    <div class="mx-2"><?= Form::switch('rdvTypeStatus-' . $rdvType->id,
                            ['val' => $rdvType->status ? 'true' : '', 'parentClass' => 'd-inline ml-3']); ?></div>
                    <div class="ml-auto mb-2 mb-lg-0">
                        <button data-toggle="modal" data-target="#dedicatedForm"
                                class="btn btn-sm btn-outline-info">Gérer le formulaire dédié
                        </button>
                    </div>
                    <button type="button" class="btn deleteRdvType mb-2 mb-lg-0"><i class="far fa-trash-alt"></i>
                    </button>
                    <div class="d-block w-100 mb-3 mt-2 informationTextareaContainer"><?= Form::textarea('', 'information-' . $rdvType->id, $rdvType->information, 3, false, '', '', 'Décrivez ou donnez des informations sur ce type de rendez-vous'); ?></div>
                </div>
            <?php endforeach; ?>
            <div class="modal fade" id="dedicatedForm" tabindex="-1" aria-labelledby="dedicatedFormLabel"
                 aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                    <div class="modal-content rounded-0">
                        <div class="modal-header py-0 border-0">
                            <h5 class="modal-title agendaTitle" id="dedicatedFormLabel">Formulaire de RDV</h5>
                            <button type="button" class="close m-0" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p class="text-muted">Chaque formulaire comporte des champs obligatoires comme le nom, le
                                prénom, l'email ou encore le numéro de téléphone. Ces champs sont tous obligatoire.
                                <br>Ici, vous pouvez ajouter des champs qui vous seront nécessaires à ce type de
                                rendez-vous</p>
                            <div id="rdvTypeFormContent"></div>
                        </div>
                    </div>
                </div>
            </div>
            <?php $html .= ob_get_clean();
        endif;
    endif;

    return $html;
}

/**
 * @param $idAgenda
 * @return string
 */
function appointment_rdv_admin_getRdvTypes($idAgenda)
{
    $html = '';
    $Agenda = new Agenda();
    $Agenda->setId($idAgenda);
    if ($Agenda->show()):

        $html .= '<h5 class="agendaTitle">Gérez vos rendez-vous</h5><p class="text-muted">Veuillez choisir le type de 
        rendez-vous avant de selectionner une date</p>';

        $RdvType = new App\Plugin\Appointment\RdvType();
        $RdvType->setIdAgenda($Agenda->getId());
        if ($rdvTypes = $RdvType->showAll()):

            $rdvTypes = extractFromObjToSimpleArr($rdvTypes, 'id', 'name');
            $currentYear = date('Y');
            $years = [$currentYear - 1, $currentYear, $currentYear + 1];

            $html .= '<div class="row" id="getRdvGrid">';
            $html .= '<div class="col-12 col-md-6">' . Form::select('Types de rendez-vous', 'rdvTypes', $rdvTypes) . '</div>';
            $html .= '<div class="col-12 col-md-3">' . Form::select('Années', 'rdvYear', array_combine($years, $years), $currentYear) . '</div>';
            $html .= '<div class="col-12 col-md-3">' . Form::select('Mois', 'rdvMonth', getMonth(), date('m')) . '</div>';
            $html .= '</div>';
            $html .= '<div id="rdvCalendar" class="d-flex flex-column my-2"></div>';
        endif;
    endif;
    return $html;
}

/**
 * @param $idRdvType
 * @param $year
 * @param $month
 * @return string
 * @throws Exception
 */
function appointment_rdv_admin_getGrid($idRdvType, $year, $month)
{
    $html = '';
    $RdvType = new RdvType();
    $RdvType->setId($idRdvType);
    if ($RdvType->show()):

        $weekDays = appointment_getWeekDays();
        $currentDay = date('d');
        $currentMonth = date('m');
        $currentYear = date('Y');
        $Date = new DateTime($year . '-' . $month . '-01');

        $Availability = new Availabilities();
        $Availability->setIdAgenda($RdvType->getIdAgenda());
        $allAvailabilities = $Availability->showAll();

        $Exception = new Exception();
        $Exception->setIdAgenda($RdvType->getIdAgenda());
        $Exception->setDate($Date->format('Y-m-d'));
        $allExceptions = $Exception->showAllFromDate();

        $allRdv = appointment_getRdv($RdvType->getIdAgenda(), $Date->format('Y-m-d'), $Date->format('Y-m-t'));

        ob_start(); ?>
        <table id="calendar" class="mt-3" data-id-rdv-type="<?= $idRdvType; ?>">
            <tr class="weekdays">
                <?php foreach ($weekDays as $day): ?>
                    <th scope="col"><?= substr($day, 0, 3); ?></th>
                <?php endforeach; ?>
            </tr>

            <?php for ($row = 1; $row <= 5; $row++):
                foreach ($weekDays as $day => $dayName):

                    if ($row == 5 && $day == 6 && $Date->format('t') > $Date->format('d')
                        && $month == $Date->format('m') && $year == $Date->format('Y')) {
                        $row = 4;
                    }

                    if ($day < $Date->format('w')) {
                        $Date->sub(new DateInterval('P' . ($Date->format('w') - $day) . 'D'));
                    }

                    $chosenDay = $Date->format('d') === $currentDay && $Date->format('m') == $currentMonth && $Date->format('Y') == $currentYear ? 'currentDay' : '';
                    $disabledDay = !appointment_isAvailableDay($Date->format('Y-m-d'), $allAvailabilities, $allExceptions) ? 'disabledDay' : '';
                    $otherMonth = $Date->format('m') != $month || $Date->format('Y') != $year ? 'other-month' : '';
                    $hasRdv = is_array($allRdv) && array_key_exists($Date->format('Y-m-d'), $allRdv) ? '<span class="shapeRdv"></span>' : '';

                    if ($day === 0): ?>
                        <tr class="days">
                    <?php endif; ?>

                    <td class="day <?= $chosenDay ?> <?= $disabledDay; ?> <?= $otherMonth; ?>"
                        data-date="<?= $Date->format('Y-m-d'); ?>">
                        <div class="date"><?= $Date->format('j'); ?></div><?= $hasRdv; ?>
                    </td>

                    <?php if ($day === 6): ?>
                    </tr>
                <?php endif;

                    $Date->add(new DateInterval('P1D'));
                endforeach;
            endfor; ?>
        </table>
        <div class="d-none d-md-flex justify-content-between my-3">
            <div><span class="shapeCurrentDay mr-2"></span> Jour J</div>
            <div><span class="shapeSelectedDay mr-2"></span> Jour sélectionné</div>
            <div><span class="shapeUnavailabaleDay mr-2"></span> Jour indisponible</div>
            <div><span class="shapeRdv mr-2"></span> Rendez-vous</div>
        </div>
        <div id="rdvAvailabilities" class="mt-3"></div>
        <?php $html .= ob_get_clean();
    endif;
    return $html;
}

/**
 * @param $idRdvType
 * @param $date
 * @return string
 * @throws Exception
 */
function appointment_rdv_admin_getAvailabilities($idRdvType, $date)
{
    $html = '';
    $RdvType = new RdvType();
    $RdvType->setId($idRdvType);
    if ($RdvType->show()):

        $Date = new DateTime($date);

        $Availability = new Availabilities();
        $Availability->setIdAgenda($RdvType->getIdAgenda());
        $Availability->setDay($Date->format('w'));
        $availabilities = $Availability->showAllByDay();

        $Exception = new Exception();
        $Exception->setIdAgenda($RdvType->getIdAgenda());
        $Exception->setDate($Date->format('Y-m-d'));
        $allExceptions = $Exception->showAllFromDateRecurrence();

        $html .= '<div id="rdvList" data-id-agenda="' . $RdvType->getIdAgenda() . '" 
        data-id-rdv-type="' . $idRdvType . '" data-date="' . $Date->format('Y-m-d') . '">';
        $html .= '<div class="d-flex justify-content-between align-items-center">';
        $html .= '<h5 id="currentDateTitle" class="agendaTitle my-0">' . displayCompleteDate($Date->format('Y-m-d'), false, '%A %d %B') . '</h5>';

        if (appointment_isAvailableDay($Date->format('Y-m-d'), $availabilities, $allExceptions)) {
            $html .= '<button type="button" class="btn btn-sm btn-secondary makeTheDayUnavailable">Rendre ce jour indisponible</button>';
        } else {

            if (appointment_isAvailableDay($Date->format('Y-m-d'), $availabilities, [], true)) {
                $html .= '<button type="button" class="btn btn-sm btn-secondary makeTheDayAvailable">Rendre ce jour disponible</button>';
            }
            $availabilities = false;
        }

        $html .= '</div>';

        if ($availabilities):

            $allRdv = appointment_getRdvByDate($RdvType->getIdAgenda(), $Date->format('Y-m-d'));

            ob_start();

            $nbTimeSlots = 0;
            $dayTimeSlotStart = 0;
            $dayTimeSlotEnd = 1440;
            foreach ($availabilities as $availability) {
                $nbTimeSlots++;

                if ($nbTimeSlots === 1) {
                    $dayTimeSlotStart = $availability->start;
                }
                $html .= appointment_admin_availabilities_get($allRdv, $allExceptions, $availability->start, $availability->end, $RdvType->getDuration(), false);
                if ($nbTimeSlots == count($availabilities)) {
                    $dayTimeSlotEnd = $availability->end;
                }
            }

            $Client = new Client();
            $allClients = $Client->showAll();
            $selectClient[0] = 'Aucun';
            foreach ($allClients as $client) {
                $selectClient[$client->id] = $client->lastName . ' ' . $client->firstName . ' : ' . $client->email;
            }

            $RdvTypeForm = new RdvTypeForm();
            $RdvTypeForm->setIdRdvType($RdvType->getId());
            $forms = $RdvTypeForm->showAll();
            ?>

            <div class="modal fade" id="addNewRdvForm" tabindex="-1" aria-labelledby="addNewRdvFormLabel"
                 aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content rounded-0">
                        <div class="modal-header py-0 border-0">
                            <h5 class="modal-title agendaTitle" id="addNewRdvFormLabel">Nouveau rendez-vous</h5>
                            <button type="button" class="close m-0" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="addNewRdvClientForm" method="post" data-ftype="appointment" action="<?= APPOINTMENT_URL; ?>ajax/mail.php">
                                <input type="hidden" name="idAgenda" value="<?= $RdvType->getIdAgenda(); ?>">
                                <input type="hidden" name="idRdvType" value="<?= $RdvType->getId(); ?>">
                                <input type="hidden" name="rdvDate" value="<?= $Date->format('Y-m-d'); ?>">
                                <div class="row d-flex flex-wrap align-items-center justify-content-between">
                                    <div class="col-12 col-lg-4 mb-3" data-date-reminder="<?= displayCompleteDate($Date->format('Y-m-d')); ?>"
                                         id="addNewRdvFormDate"></div>
                                    <div class="col-12 col-lg-4 mb-3"><?= Form::selectTimeSlot('rdvBegin', ['title' => 'Heure début', 'required' => true, 'stepMin' => 5, 'startMin' => $dayTimeSlotStart, 'endMin' => $dayTimeSlotEnd]); ?></div>
                                    <div class="col-12 col-lg-4 mb-3"><?= Form::selectTimeSlot('rdvEnd', ['title' => 'Heure fin', 'required' => true, 'stepMin' => 5, 'startMin' => $dayTimeSlotStart, 'endMin' => $dayTimeSlotEnd]); ?></div>
                                </div>
                                <div class="row my-4 py-3 bg-light">
                                    <div class="col-12"><?= Form::select('Client existant', 'selectClient', $selectClient, 0); ?></div>
                                </div>
                                <div class="row my-2">
                                    <div class="col-12 col-lg-6 mb-3">
                                        <?= Form::input('appointment_lastName', ['title' => 'Nom *', 'required' => true]); ?>
                                    </div>
                                    <div class="col-12 col-lg-6 mb-3">
                                        <?= Form::input('appointment_firstName', ['title' => 'Prénom *', 'required' => true]); ?>
                                    </div>
                                    <div class="col-12 col-lg-6 mb-3">
                                        <?= Form::input('appointment_email', ['title' => 'Adresse Email *', 'type' => 'email', 'required' => true]); ?>
                                    </div>
                                    <div class="col-12 col-lg-6 mb-3">
                                        <?= Form::input('appointment_tel', ['title' => 'Téléphone *', 'type' => 'tel', 'required' => true]); ?>
                                    </div>
                                </div>
                                <div class="row my-2">
                                    <?php foreach ($forms as $form): ?>
                                        <div class="col-12 col-lg-6 mb-3">
                                            <div class="form-group">
                                                <label for="appointment_<?= $form->slug; ?>"><?= $form->name . ($form->required ? ' *' : ''); ?></label>
                                                <?php if ($form->type === 'textarea'): ?>
                                                    <textarea rows="4" id="appointment_<?= $form->slug; ?>"
                                                              name="appointment_<?= $form->slug; ?>"
                                                              class="form-control"
                                                              placeholder="<?= $form->placeholder; ?>"<?= ($form->required ? 'required="true"' : ''); ?>></textarea>
                                                <?php else: ?>
                                                    <input type="<?= $form->type; ?>"
                                                           id="appointment_<?= $form->slug; ?>"
                                                           name="appointment_<?= $form->slug; ?>" value=""
                                                           class="form-control"
                                                           placeholder="<?= $form->placeholder; ?>" <?= ($form->required ? 'required="true"' : ''); ?>>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <?= getTokenField(); ?>
                                <div class="row my-2">
                                    <div class="col-12"><?= Form::btn('OK', 'ADDRDVSUBMIT'); ?></div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <?php $html .= ob_get_clean();
        endif;
        $html .= '</div>';
    endif;

    return $html;
}

function appointment_getFormClientById($idClient)
{
    $clientData = array();

    $Client = new Client();
    $Client->setId($idClient);
    if ($Client->show() && $Client->getStatus()) {
        $clientData['appointment_lastName'] = $Client->getLastName();
        $clientData['appointment_firstName'] = $Client->getFirstName();
        $clientData['appointment_email'] = $Client->getEmail();
        $clientData['appointment_tel'] = $Client->getTel();

        if ($options = unserialize($Client->getOptions())) {
            foreach ($options as $key => $val) {
                $clientData['appointment_' . $key] = $val;
            }
        }
    }
    return $clientData;
}

/**
 * @param $idAgenda
 * @param $idRdvType
 * @return string
 */
function appointment_rdvTypeForm_admin_getAll($idAgenda, $idRdvType)
{
    $html = '';
    $Agenda = new Agenda();
    $Agenda->setId($idAgenda);
    if ($Agenda->show()):

        $types = ['text' => 'Texte', 'email' => 'E-mail', 'tel' => 'Téléphone', 'number' => 'Nombre réel', 'textarea' => 'Zone de texte'];
        $positions = array_combine(range(1, 10), range(1, 10));

        $html .= '<form id="addTypeRdvForm" class="mt-4 mb-5" data-id-agenda="' . $idAgenda . '" data-id-rdv-type="' . $idRdvType . '"><div class="form-row">';
        $html .= '<div class="col-12 col-lg-4">' . Form::input('name', ['title' => 'Nom du champs']) . '</div>';
        $html .= '<div class="col-12 col-lg-2">' . Form::select('Type', 'type', $types, 'text', true) . '</div>';
        $html .= '<div class="col-12 col-lg-3">' . Form::input('placeholder', ['title' => 'Placeholder']) . '</div>';
        $html .= '<div class="col-12 col-lg-1"><div>Obligatoire</div>' . Form::switch('required', ['val' => 'false', 'parentClass' => 'd-flex justify-content-center align-items-center']) . '</div>';
        $html .= '<div class="col-12 col-lg-1">' . Form::select('Position', 'position', $positions, 1, true) . '</div>';
        $html .= '<div class="col-12 col-lg-1 d-flex align-items-end">' . Form::btn('OK', 'ADDTYPERDVFORMSUBMIT') . '</div>';
        $html .= '</div></form>';

        $RdvTypeForm = new RdvTypeForm();
        $RdvTypeForm->setIdRdvType($idRdvType);
        if ($rdvTypeForms = $RdvTypeForm->showAll()):

            foreach ($rdvTypeForms as $rdvTypeForm):
                $html .= '<form class="py-4 mb-2 border-top rdvTypForm" data-id-rdv-type-form="' . $rdvTypeForm->id . '"><div class="form-row">';
                $html .= '<div class="col-12 col-lg-4">' . Form::input('rdvTypeFormName-' . $rdvTypeForm->id, ['title' => 'Nom du champs', 'val' => $rdvTypeForm->name]) . '</div>';
                $html .= '<div class="col-12 col-lg-2">' . Form::select('Type', 'rdvTypeFormType-' . $rdvTypeForm->id, $types, $rdvTypeForm->type, true) . '</div>';
                $html .= '<div class="col-12 col-lg-3">' . Form::input('rdvTypeFormPlaceholder-' . $rdvTypeForm->id, ['title' => 'Placeholder', 'val' => $rdvTypeForm->placeholder]) . '</div>';
                $html .= '<div class="col-12 col-lg-1"><div>Obligatoire</div>' . Form::switch('rdvTypeFormRequired-' . $rdvTypeForm->id, ['val' => ($rdvTypeForm->required == 1 ? 'true' : ''), 'parentClass' => 'd-flex justify-content-center align-items-center']) . '</div>';
                $html .= '<div class="col-12 col-lg-1">' . Form::select('Position', 'rdvTypeFormPosition-' . $rdvTypeForm->id, $positions, $rdvTypeForm->position, true) . '</div>';
                $html .= '<div class="col-12 col-lg-1 d-flex align-items-end"><button type="button" class="btn deleteRdvTypeForm mb-2 mb-lg-0"><i class="far fa-trash-alt"></i></button></div>';
                $html .= '</div></form>';
            endforeach;
        endif;
    endif;
    return $html;
}

/**
 * @param $agendaName
 * @return bool
 */
function appointment_addAgenda($agendaName)
{
    $Agenda = new Agenda();
    $Agenda->setName($agendaName);
    $Agenda->setStatus(0);
    return $Agenda->save();
}

/**
 * @param $idAgenda
 * @param $day
 * @param $start
 * @param $end
 * @return bool|string
 */
function appointment_addAvailability($idAgenda, $day, $start, $end)
{
    $Availability = new Availabilities();
    $Availability->setIdAgenda($idAgenda);
    $Availability->setDay($day);
    if ($availabilities = $Availability->showAllByDay()) {
        foreach ($availabilities as $availability) {
            if (($start >= $availability->start && $start <= $availability->end) ||
                ($end >= $availability->start && $end <= $availability->end) ||
                ($start <= $availability->start && $end >= $availability->end)) {
                return 'Ce créneau n\'est pas disponible';
            }
        }
    }
    $Availability->setStart($start);
    $Availability->setEnd($end);
    return $Availability->save();
}

/**
 * @param $idAgenda
 * @param $name
 * @param $duration
 * @param string $information
 * @return bool
 */
function appointment_addRdvType($idAgenda, $name, $duration, $information = '')
{
    $RdvType = new RdvType();
    $RdvType->setIdAgenda($idAgenda);
    $RdvType->setName($name);
    $RdvType->setDuration($duration);
    $RdvType->setInformation($information);
    return $RdvType->save();
}

/**
 * @param $idAgenda
 * @param $idRdvType
 * @param $name
 * @param $slug
 * @param $type
 * @param $placeholder
 * @param $required
 * @param $position
 * @return bool
 */
function appointment_addRdvTypeForm($idAgenda, $idRdvType, $name, $slug, $type, $placeholder, $required, $position)
{
    $RdvTypeForm = new RdvTypeForm();
    $RdvTypeForm->setIdRdvType($idRdvType);
    $RdvTypeForm->setIdAgenda($idAgenda);
    $RdvTypeForm->setName($name);
    $RdvTypeForm->setSlug($slug);
    $RdvTypeForm->setType($type);
    $RdvTypeForm->setPlaceholder($placeholder);
    $RdvTypeForm->setRequired($required != 'false' ? 1 : 0);
    $RdvTypeForm->setPosition($position);
    return $RdvTypeForm->save();
}

/**
 * @param $idRdvTypeForm
 * @param $name
 * @param $slug
 * @param $type
 * @param $placeholder
 * @param $required
 * @param $position
 * @return bool
 */
function appointment_updateRdvTypeForm($idRdvTypeForm, $name, $slug, $type, $placeholder, $required, $position)
{
    $RdvTypeForm = new RdvTypeForm();
    $RdvTypeForm->setId($idRdvTypeForm);
    $RdvTypeForm->setName($name);
    $RdvTypeForm->setSlug($slug);
    $RdvTypeForm->setType($type);
    $RdvTypeForm->setPlaceholder($placeholder);
    $RdvTypeForm->setRequired($required != 'false' ? 1 : 0);
    $RdvTypeForm->setPosition($position);
    return $RdvTypeForm->update();
}

/**
 * @param $idAgenda
 * @param $agendaName
 * @return bool
 */
function appointment_changeAgendaName($idAgenda, $agendaName)
{
    $Agenda = new Agenda();
    $Agenda->setId($idAgenda);
    if ($Agenda->show()) {
        $Agenda->setName($agendaName);
        return $Agenda->update();
    }
    return false;
}

/**
 * @param $idAgenda
 * @return bool
 */
function appointment_changeAgendaStatus($idAgenda)
{
    $Agenda = new Agenda();
    $Agenda->setId($idAgenda);
    if ($Agenda->show()) {
        $newStatus = $Agenda->getStatus() == 1 ? 0 : 1;
        $Agenda->setStatus($newStatus);
        return $Agenda->update();
    }
    return false;
}

/**
 * @param $idRdvType
 * @param $rdvTypeName
 * @return bool
 */
function appointment_changeRdvTypeName($idRdvType, $rdvTypeName)
{
    $RdvType = new RdvType();
    $RdvType->setId($idRdvType);
    if ($RdvType->show()) {
        $RdvType->setName($rdvTypeName);
        return $RdvType->update();
    }
    return false;
}

/**
 * @param $idRdvType
 * @param $duration
 * @return bool
 */
function appointment_changeRdvTypeDuration($idRdvType, $duration)
{
    $RdvType = new RdvType();
    $RdvType->setId($idRdvType);
    if ($RdvType->show()) {
        $RdvType->setDuration($duration);
        return $RdvType->update();
    }
    return false;
}

/**
 * @param $idRdvType
 * @param $information
 * @return bool
 */
function appointment_changeRdvTypeInformation($idRdvType, $information)
{
    $RdvType = new RdvType();
    $RdvType->setId($idRdvType);
    if ($RdvType->show()) {
        $RdvType->setInformation($information);
        return $RdvType->update();
    }
    return false;
}

/**
 * @param $idRdvType
 * @return bool
 */
function appointment_changeRdvTypeStatus($idRdvType)
{
    $RdvType = new RdvType();
    $RdvType->setId($idRdvType);
    if ($RdvType->show()) {
        $newStatus = $RdvType->getStatus() == 1 ? 0 : 1;
        $RdvType->setStatus($newStatus);
        return $RdvType->update();
    }
    return false;
}

/**
 * @param $idAgenda
 * @return bool
 */
function appointment_deleteAgenda($idAgenda)
{
    $Agenda = new Agenda();
    $Agenda->setId($idAgenda);
    return $Agenda->delete();
}

/**
 * @param $idAvailability
 * @return bool
 */
function appointment_deleteAvailability($idAvailability)
{
    $Availability = new Availabilities();
    $Availability->setId($idAvailability);
    return $Availability->delete();
}

/**
 * @param $idRdvType
 * @return bool
 */
function appointment_deleteRdvType($idRdvType)
{
    $RdvType = new RdvType();
    $RdvType->setId($idRdvType);
    return $RdvType->delete();
}

/**
 * @param $idRdv
 * @return bool
 */
function appointment_deleteRdv($idRdv)
{
    $Rdv = new Rdv();
    $Rdv->setId($idRdv);
    if ($Rdv->show()) {

        $Client = new Client();
        $Client->setId($Rdv->getIdClient());

        if ($Client->show()) {

            if ($Client->getStatus() == 1 && $Rdv->getDate() >= date('Y-m-d')) {

                $Agenda = new Agenda();
                $Agenda->setId($Rdv->getIdAgenda());
                $Agenda->show();

                $RdvType = new RdvType();
                $RdvType->setId($Rdv->getIdTypeRdv());
                $RdvType->show();

                $rdvRemind = displayCompleteDate($Rdv->getDate()) . ' à ' . minutesToHours($Rdv->getStart());

                $html = '<p>Bonjour,<br><br>Votre rendez-vous du ' . $rdvRemind . ' pour ' . $RdvType->getName() . ' chez ' . $Agenda->getName() . ' a été <strong>annulé</strong>.
        <br>Vous pouvez  <a href="' . urlAppointment() . '">redemander un rendez-vous</a> sur notre site.</p>';

                $data = array(
                    'toEmail' => $Client->getEmail(),
                    'toName' => $Client->getLastName() . ' ' . $Client->getFirstName(),
                    'object' => 'Votre rendez-vous chez ' . $Agenda->getName() . ' a été annulé',
                    'message' => $html,
                );

                sendMail($data, [], ['viewSenderSource' => false]);
            }
        }
    }

    return $Rdv->delete();
}

/**
 * @param $idRdv
 * @return bool
 */
function appointment_confirmRdv($idRdv)
{
    $Rdv = new Rdv();
    $Rdv->setId($idRdv);
    if ($Rdv->show() && $Rdv->getStatus() == 0) {
        $Rdv->setStatus(1);
        return $Rdv->update();
    }
    return false;
}

/**
 * @param $idClient
 * @return bool
 */
function appointment_confirmClient($idClient)
{
    $Client = new Client();
    $Client->setId($idClient);
    if ($Client->show() && $Client->getStatus() == 0) {
        $Client->setStatus(1);

        $Option = new \App\Option();
        $Option->setType('CONFIRMATIONMAIL');
        $Option->setKey($Client->getEmail());
        if ($confirmEmail = $Option->showByKey()) {
            $Option->setId($confirmEmail->id);

            if ($Client->update() && $Option->delete()) {
                return true;
            }
        }
    }
    return false;
}

function appointment_makeTheTimeSlotUnavailable($idAgenda, $date, $start, $end)
{
    $Exception = new Exception();
    $Exception->setIdAgenda($idAgenda);
    $Exception->setDate($date);
    $Exception->setStart($start);
    $Exception->setEnd($end);

    if (!$Exception->exist()) {
        return $Exception->save();
    }

    if ($Exception->showByTime()) {
        $Exception->setAvailability('UNAVAILABLE');
        return $Exception->update();
    }

    return false;
}

function appointment_makeTheTimeSlotAvailable($id, $date, $start, $end)
{
    $Exception = new Exception();
    $Exception->setId($id);
    return $Exception->delete();

    /*if ($Exception->show()) {

        //If the exception is for only one time slot
        if ($Exception->getStart() == $start && $Exception->getEnd() == $end) {
            return $Exception->delete();
        }

        $Exception->setDate($date);
        $Exception->setStart($start);
        $Exception->setEnd($end);
        $Exception->setAvailability('AVAILABLE');

        if (!$Exception->exist()) {
            return $Exception->save();
        }

        if ($Exception->showByTime()) {
            $Exception->setAvailability('AVAILABLE');
            return $Exception->update();
        }
    }*/
}

function appointment_makeTheDayAvailable($idAgenda, $date)
{
    $Exception = new Exception();

    $Exception->setIdAgenda($idAgenda);
    $Exception->setDate($date);
    $Exception->setStart(0);
    $Exception->setEnd(1440);
    if ($Exception->showByTime()) {
        return $Exception->delete();
    }
    return false;
}

/**
 * @param $idRdvTypeForm
 * @return bool
 */
function appointment_deleteRdvTypeForm($idRdvTypeForm)
{
    $RdvTypeForm = new RdvTypeForm();
    $RdvTypeForm->setId($idRdvTypeForm);
    return $RdvTypeForm->delete();
}

/********************************** FRONT **************************************/

/**
 * Send a reminder Email
 */
function appointment_cron()
{
    $Rdv = new Rdv();
    $Rdv->setDate(date('Y-m-d'));
    if ($allRdv = $Rdv->showAllFromDate()) {
        foreach ($allRdv as $rdv) {

            $DateRdv = new DateTime($rdv->date);
            $Today = new DateTime();
            $Tomorrow = new DateTime();
            $Tomorrow->add(new DateInterval('P1D'));
            list($dateUpdate, $hour) = explode(' ', $rdv->updated_at);

            if ($DateRdv->format('Y-m-d') == $Tomorrow->format('Y-m-d') && $dateUpdate < $Today->format('Y-m-d') && $rdv->status > 0) {

                $Client = new Client();
                $Client->setId($rdv->idClient);
                if ($Client->show() && $Client->getStatus()) {
                    appointment_sendInfosEmail($rdv->id);
                }
            }
        }
    }
}

function urlAppointment()
{
    if (defined('APPOINTMENT_FILENAME') && function_exists('getPageByFilename')) {
        $Cms = getPageByFilename(APPOINTMENT_FILENAME);
        return WEB_DIR_URL . $Cms->getSlug() . DIRECTORY_SEPARATOR;
    }
    return null;
}

/**
 * @param $idRdv
 * @param $url
 * @return bool
 * @throws Exception
 */
function appointment_sendInfosEmail($idRdv, $url = null)
{

    $Rdv = new Rdv();
    $Rdv->setId($idRdv);
    if ($Rdv->show()) {

        $Client = new Client();
        $Client->setId($Rdv->getIdClient());
        if ($Client->show()) {

            $Agenda = new Agenda();
            $Agenda->setId($Rdv->getIdAgenda());
            $Agenda->show();

            $RdvType = new RdvType();
            $RdvType->setId($Rdv->getIdTypeRdv());
            $RdvType->show();

            $AgendaMeta = new AgendaMeta();
            $AgendaMeta->setIdAgenda($Agenda->getId());
            $agendaMetas = $AgendaMeta->showAll();

            $rdvRemind = displayCompleteDate($Rdv->getDate()) . ' à ' . minutesToHours($Rdv->getStart());

            $message = '<p style="text-align:center;margin-bottom:0;">' . $RdvType->getName() . '</p>';
            $message .= '<h2 style="text-align:center;margin-bottom:4px;"><strong>' . $rdvRemind . '</strong></h2>';
            $message .= '<p style="text-align:center;margin-bottom:15px;">Chez ' . $Agenda->getName() . '</p>';

            if (!empty($RdvType->getInformation())) {
                $message .= '<p style="margin-bottom:15px;">' . $RdvType->getInformation() . '</p>';
            }

            if (is_null($url)) {
                $url = urlAppointment();
            }

            if (!is_null($url)) {

                $historyRdv = $url . '?historyRdv=OK&idClient=' . base64_encode($Client->getId());
                $url = $url . '?idRdv=' . base64_encode($idRdv);
                $removeRdv = $url . '&removeRdv=OK';
                $editRdv = $url . '&editRdv=OK';

                $message .= '<p><img src="' . APPOINTMENT_URL . 'img/rappel-de-rdv-professionnel.png"></p>';
                $message .= '<p style="text-align:center;margin-bottom:15px;"><a class="btn" style="margin:10px;" href="' . $editRdv . '" title="Déplacer le rendez vous">Déplacer le rendez-vous</a>';
                $message .= '<a class="btn" style="background-color:#ff394f;border-color:#ff394f;" href="' . $removeRdv . '" title="Annuler le rendez vous">Annuler le rendez-vous</a></p>';
                $message .= '<p style="text-align:center;margin-top:30px;margin-bottom:30px;">Vous pouvez modifier ou annuler votre rendez-vous à tout moment à partir de cet email.<br>
                <a href="' . $historyRdv . '" title="Consulter l\'historique des rendez-vous">Consulter l\'historique des rendez-vous</a></p>';
            }

            if (!empty($agendaMetas)) {
                $message .= '<h3 style="text-align:center;margin-bottom:7px;">Informations complémentaires</h3><p style="text-align:center;">';
                foreach ($agendaMetas as $key => $meta) {
                    $message .= '<strong>' . $meta->metaKey . ':</strong> ' . $meta->metaValue . ($key != count($agendaMetas) ? '<br>' : '');
                }
                $message .= '</p>';
            }

            /*
            $timeStart = str_replace('-', '', $Rdv->getDate()) . 'T' . minutesToHours($Rdv->getStart(), '%s%s') . '00';
            $timeEnd = str_replace('-', '', $Rdv->getDate()) . 'T' . minutesToHours($Rdv->getEnd(), '%s%s') . '00';
            $urlGoogle = 'https://calendar.google.com/calendar/u/0/r/eventedit?';
            $Gtitle = 'text=' . $RdvType->getName() . ' chez ' . $Agenda->getName();
            $Gdates = '&dates=' . $timeStart . '/' . $timeEnd;
            $Gdetails = '&details=Consulter l\'historique des rendez-vous: ' . $historyRdv;
            $Gtimezone = '&ctz=' . date_default_timezone_get();
            $Glocation = '&location=';
            $Gurl = $urlGoogle . $Gtitle . $Gdates . $Gdetails . $Gtimezone . $Glocation;
            $message .= '<p style="text-align:center;margin-top:20px;"><a class="btn" href="' . $Gurl . '" title="Ajouter le rendez-vous à mon agenda">Ajouter le rendez-vous à mon agenda</a></p>';
            */

            $data = array(
                'fromEmail' => 'noreply@' . $_SERVER['HTTP_HOST'],
                'fromName' => WEB_TITLE,
                'toName' => $Client->getLastName() . ' ' . $Client->getFirstName(),
                'toEmail' => $Client->getEmail(),
                'object' => 'Informations pour votre RDV de ' . $rdvRemind,
                'message' => $message
            );

            if ($data && sendMail($data, [], ['viewSenderSource' => false, 'priority' => 1])) {
                return true;
            }

        }
    }

    return false;
}

/**
 * @return string
 * @throws Exception
 */
function appointment_agenda_getBtns($idAgenda = '')
{
    $_SESSION['appointmentSlug'] = WEB_DIR_URL . getPageSlug() . '/';
    $html = '';

    //Remove unconfirmed RDV
    $Rdv = new Rdv();
    $Rdv->setStatus(0);
    if ($pendingRdv = $Rdv->showPending()) {
        foreach ($pendingRdv as $rdv) {
            if ((strtotime($rdv->updated_at) + (APPOINTMENT_TIMEOUT_VALIDATION * 60 * 60)) < time()) {
                $Rdv->setId($rdv->id);
                $Rdv->delete();
            }
        }
    }

    //Remove unconfirmed Client
    $Client = new Client();
    $Client->setStatus(0);
    if ($pendingClient = $Client->showPending()) {
        foreach ($pendingClient as $client) {
            if ((strtotime($client->updated_at) + (APPOINTMENT_TIMEOUT_VALIDATION * 60 * 60)) < time()) {

                $Option = new \App\Option();
                $Option->setType('CONFIRMATIONMAIL');
                $Option->setKey($client->email);
                $Option->deleteByTypeAndKey();

                $Client->setId($client->id);
                $Client->delete();
            }
        }
    }

    if (empty($idAgenda)) {

        //See RDV history
        if (isset($_GET['historyRdv']) && !empty($_GET['idClient'])) {

            $Client->setId(base64_decode($_GET['idClient']));

            if ($Client->show() && $Client->getStatus()) {


                $Rdv->setIdClient($Client->getId());
                if ($allRdv = $Rdv->showAllByClient()) {

                    $Agenda = new Agenda();

                    $html .= '<section id="appointment-appoe" class="appointmentAppoe">';
                    $TypeRdv = new RdvType();
                    foreach ($allRdv as $rdv) {

                        $Agenda->setId($rdv->idAgenda);
                        $Agenda->show();

                        $rdvRemind = displayCompleteDate($rdv->date) . ' à ' . minutesToHours($rdv->start);

                        $TypeRdv->setId($rdv->idTypeRdv);
                        $TypeRdv->show();
                        $html .= '<div style="margin-bottom: 50px;border-left: 1px solid #000;padding-left: 10px;">';
                        $html .= '<p>' . $Agenda->getName() . '<br><strong>' . $rdvRemind . '</strong><br>' . $TypeRdv->getName() . '</p>';

                        if ($rdv->date > date('Y-m-d')) {

                            $url = $_SESSION['appointmentSlug'] . '?idRdv=' . base64_encode($rdv->id);
                            $removeRdv = $url . '&removeRdv=OK';
                            $editRdv = $url . '&editRdv=OK';

                            $html .= '<p><a class="button btn-round grey" target="_blank" title="Déplacer le Rdv" href="' . $editRdv . '">Déplacer le Rdv</a>
                            <a class="button btn-round grey" target="_blank" title="Annuler le Rdv" href="' . $removeRdv . '">Annuler le Rdv</a></p>';
                        }
                        $html .= '</div>';
                    }
                    $html .= '</section>';
                } else {
                    $html .= 'Vous n\'avez pas de rendez-vous enregistrés';
                }
            }
            return $html;
        }

        //Edit or remove RDV
        if (!empty($_GET['idRdv']) && (isset($_GET['editRdv']) || isset($_GET['removeRdv']))) {

            $idRdv = base64_decode($_GET['idRdv']);
            if (is_numeric($idRdv)) {

                $Rdv->setId($idRdv);
                if ($Rdv->show() && $Rdv->getStatus()) {

                    $Agenda = new Agenda();
                    $Agenda->setId($Rdv->getIdAgenda());
                    $Agenda->show();

                    $rdvRemind = displayCompleteDate($Rdv->getDate()) . ' à ' . minutesToHours($Rdv->getStart());

                    //Remove RDV
                    if (!empty($_GET['removeRdv'])) {
                        if ($Rdv->delete()) {
                            $html .= 'Votre rendez vous du <strong>' . $rdvRemind . '</strong> chez ' . $Agenda->getName() . ' a bien été annulé.';
                        }

                        //Edit RDV
                    } elseif (!empty($_GET['editRdv'])) {
                        $_SESSION['editRdv'] = ['idRdv' => $idRdv, 'idClient' => $Rdv->getIdClient()];
                        $html .= '<p id="editRdvMsg">Choisissez simplement un nouveau rendez-vous, il remplacera automatiquement celui du <strong>' . $rdvRemind . '</strong> chez ' . $Agenda->getName() . '</p>';
                        $html .= appointment_rdvType_getBtns($Rdv->getIdAgenda());


                    }
                    return $html;
                }
            }
            return 'Ce rendez-vous a déjà été déplacé ou annulé. Consultez l\'historique de vos rendez-vous depuis votre email récapitulatif.';
        }

        if (isset($_SESSION['editRdv'])) {
            unset($_SESSION['editRdv']);
        }

        //Confirm client email
        if (!empty($_GET['email']) && !empty($_GET['key']) && !empty($_GET['idClient'])) {

            if ($email = approveEmail($_GET, APPOINTMENT_TIMEOUT_VALIDATION)) {

                $Client->setId(base64_decode($_GET['idClient']));
                if ($Client->show() && !$Client->getStatus()) {

                    $Client->setStatus(1);
                    if ($Client->update()) {
                        $html .= '<div id="appointment-appoe"><div class="appointmentAppoeReminder">';
                        $html .= '<img src="' . APPOINTMENT_URL . 'img/check.svg" width="30px">Votre adresse email <strong>' . $email . '</strong> a bien été confirmé.<br><br>';

                        $Rdv->setIdClient($Client->getId());
                        $Rdv->setStatus(0);
                        if ($Rdv->showByPendingClient()) {
                            $Rdv->setStatus(1);
                            if ($Rdv->update()) {
                                $html .= 'Votre rendez-vous du <strong>' . displayCompleteDate($Rdv->getDate()) . '</strong>';
                                $html .= ' à <strong>' . minutesToHours($Rdv->getStart()) . '</strong> est enregistré.<br>';
                                $html .= '<br>Vous recevrez bientôt un email récapitulatif.';
                                appointment_sendInfosEmail($Rdv->getId(), $_SESSION['appointmentSlug']);
                            }
                        } else {
                            $html .= 'Votre rendez-vous n\'a pas été enregistré !';
                        }
                        $html .= '</div></div>';
                    } else {
                        $html .= 'Un problème est survenu lors de la confirmation de votre adresse email <strong>' . $email . '</strong>.';
                    }
                }
            } else {
                $html .= 'Le délai d\'attente a expiré ! Vous pouvez reprendre un <a href="' . $_SESSION['appointmentSlug'] . '">rendez-vous</a>.';
            }

            return $html;
        }

        //Display agendas
        $Agenda = new Agenda();
        if ($agendas = $Agenda->showByStatus()) {

            if (count($agendas) > 1) {
                $html .= '<section id="agendas" class="appointmentAppoe"><h2>' . APPOINTMENT_AGENDA_CHOICE_TITLE . '</h2>';
                foreach ($agendas as $agenda) {
                    $html .= '<button class="button btn-round grey agendaChoice ' . (count($agendas) == 1 ? 'activeAgendaBtn' : '') . '" data-id-agenda="' . $agenda->id . '">' . $agenda->name . '</button>';
                }
                $html .= '</section>';
            } elseif (count($agendas) == 1) {
                $html .= appointment_rdvType_getBtns($agendas[0]->id);
            } else {
                $html .= 'Aucun agenda n\'est encore disponible';
            }
        }

    } else {
        $Agenda = new Agenda();
        $Agenda->setId($idAgenda);
        if ($Agenda->show()) {
            $html .= appointment_rdvType_getBtns($idAgenda);
        } else {
            $html .= 'Aucun agenda n\'est encore disponible';
        }
    }
    return $html;
}

/**
 * @param $idAgenda
 * @return string
 */
function appointment_rdvType_getBtns($idAgenda)
{
    $html = '';
    $RdvType = new RdvType();
    $RdvType->setIdAgenda($idAgenda);
    if ($rdvTypes = $RdvType->showByStatus()) {
        if (count($rdvTypes) > 1) {
            $html .= '<section id="agendaRdvType" class="appointmentAppoe"><h2>' . APPOINTMENT_RDVTYPE_CHOICE_TITLE . '</h2>';
            foreach ($rdvTypes as $rdvType) {
                $html .= '<button class="button btn-round grey rdvTypeChoice" data-id-agenda="' . $idAgenda . '" 
                data-rdv-duration="' . $rdvType->duration . '" data-id-rdv-type="' . $rdvType->id . '">' . $rdvType->name . '</button>';
            }
            $html .= '</section>';
        } elseif (count($rdvTypes) == 1) {
            $html .= appointment_dates_get($idAgenda, $rdvTypes[0]->id);
        } else {
            $html .= 'Pas de disponibilités';
        }

    }
    return $html;
}


/**
 * @param $idAgenda
 * @param $idRdvType
 * @return string
 * @throws Exception
 */
function appointment_dates_get($idAgenda, $idRdvType)
{
    $numberOfDays = 90;
    $counter = 0;
    $Date = new DateTime();
    $NextWeek = new DateTime();
    $NextWeek->add(new DateInterval('P7D'));

    $html = '<section id="agendaDatesRdv" class="appointmentAppoe"><h2>' . APPOINTMENT_DATES_CHOICE_TITLE . '</h2>';
    $html .= '<button id="appointmentPrevWeek">‹</button> <button id="appointmentCurrentWeek" style="color:#ff394f !important;">•</button> <button id="appointmentNextWeek">›</button> ';
    $html .= '<small id="appointmentNextWeekInfos">' . $Date->format('d') . ' - ' . displayCompleteDate($NextWeek->format('Y-m-d'), false, '%d %B %Y') . '</small>';
    $html .= '<div id="appointmentSwipeCalendar" class="owl-carousel owl-theme center">';

    $Availability = new Availabilities();
    $Availability->setIdAgenda($idAgenda);
    $allAvailabilities = $Availability->showAll();

    $RdvType = new RdvType();
    $RdvType->setId($idRdvType);
    $RdvType->show();

    $Exception = new Exception();
    $Exception->setIdAgenda($idAgenda);
    $Exception->setDate($Date->format('Y-m-d'));
    $allExceptions = $Exception->showAllFromDate();

    while ($counter <= $numberOfDays) {

        $time = strtotime($Date->format('Y-m-d'));
        list($dayName, $dayNum, $monthName) = explode('-', strftime('%A-%d-%B', $time));
        $current = $Date->format('Y-m-d') == date('Y-m-d') ? 'D-day' : '';
        $disabledDay = !appointment_isAvailableDay($Date->format('Y-m-d'), $allAvailabilities, $allExceptions) ? 'disabledDay' : '';

        $html .= '<div data-id-agenda="' . $idAgenda . '" data-id-rdv-type="' . $RdvType->getId() . '" data-rdv-duration="' . $RdvType->getDuration() . '" 
        data-date-choice="' . $Date->format('Y-m-d') . '" class="dayBox ' . $current . ' ' . $disabledDay . '" 
        data-date-reminder="' . displayCompleteDate($Date->format('Y-m-d')) . '"><span class="day">' . ucwords($dayName) . '</span><span class="date">' . $dayNum . '</span>
        <span class="month">' . ucwords($monthName) . '</span></div>';

        $Date->add(new DateInterval('P1D'));
        $counter++;
    }

    $html .= '</div></section>';
    //$html .= appointment_availabilities_get($idAgenda, date('Y-m-d'), $rdvTypeDuration);
    return $html;
}

/**
 * @param $idAgenda
 * @param $date
 * @param $rdvTypeDuration
 * @return string
 */
function appointment_availabilities_get($idAgenda, $date, $rdvTypeDuration)
{

    $html = '';
    $Rdv = new Rdv();
    $Rdv->setIdAgenda($idAgenda);
    $Rdv->setDate($date);

    $dayInWeek = date('w', strtotime($date));

    $Availability = new Availabilities();
    $Availability->setIdAgenda($idAgenda);
    $Availability->setDay($dayInWeek);

    if ($availabilities = $Availability->showAllByDay()) {

        foreach ($availabilities as $availability) {

            // Get availabilities by RDV
            $html .= appointment_availabilities_getBtns($idAgenda, $date, $Rdv->showAll(), $availability->start, $availability->end, $rdvTypeDuration, true);
        }
    }
    $html .= '<div class="appointmentAppoeReminder hoursRemind"><img src="' . APPOINTMENT_URL . 'img/check.svg" width="30px">Rendez-vous le <strong></strong></div>';
    return $html;
}

/**
 * @param $idAgenda
 * @param $date
 * @param $booking
 * @param $start
 * @param $end
 * @param $rdvTypeDuration
 * @param $compressHour
 * @return string
 */
function appointment_availabilities_getBtns($idAgenda, $date, $booking, $start, $end, $rdvTypeDuration, $compressHour)
{
    $html = '<section class="agendaAvailabilities appointmentAppoe">';
    $availabilities = 0;
    $time = $start;
    $Exception = new Exception();
    $Exception->setIdAgenda($idAgenda);
    $Exception->setDate($date);
    $allExceptions = $Exception->showAllFromDateRecurrence();

    while ($time <= $end) {

        if ($time + $rdvTypeDuration > $end) {
            break;
        }

        if (appointment_isUnvailableHour($time, $allExceptions, $rdvTypeDuration, $compressHour)) {
            continue;
        }

        if (appointment_isBooked($time, $booking, $rdvTypeDuration, $compressHour)) {
            continue;
        }

        $availabilities++;
        if ($availabilities === 1) {
            $html .= sprintf('<p>De <strong>%s</strong> à <strong>%s</strong></p>', minutesToHours($start), minutesToHours($end));
            $html .= '<div class="hourBox">';
        }

        $html .= '<button class="button btn-round border availabilityChoice" data-start="' . $time . '" data-end="' . ($time + $rdvTypeDuration) . '">' .
            minutesToHours($time) . ' - ' . minutesToHours($time + $rdvTypeDuration) . '</button>';
        $time += $rdvTypeDuration;
    }

    if ($availabilities > 0) {
        $html .= '</div>';
    } else {
        $html .= sprintf('<p>Pas de disponibilités entre <strong>%s</strong> et <strong>%s</strong></p>', minutesToHours($start), minutesToHours($end));
    }

    $html .= '</section>';
    return $html;
}

/**
 * @param $allRdv
 * @param $allExceptions
 * @param $start
 * @param $end
 * @param $rdvTypeDuration
 * @param $compressHour
 * @return string
 */
function appointment_admin_availabilities_get($allRdv, $allExceptions, $start, $end, $rdvTypeDuration, $compressHour = false)
{
    $html = '<ul class="list-group mt-4 mb-3">';
    $time = $start;

    $Client = new Client();

    while ($time <= $end) {

        if ($time + $rdvTypeDuration > $end) {
            break;
        }

        $html .= '<li class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">';

        if ($exception = appointment_admin_isUnvailableHour($time, $allExceptions, $rdvTypeDuration)) {
            $html .= minutesToHours($time) . ' - ' . minutesToHours($time + $rdvTypeDuration);
            $html .= '<div><button class="btn btn-sm btn-dark MakeTheTimeSlotAvailable" data-start="' . $time . '" 
            data-end="' . ($time + $rdvTypeDuration) . '" data-id-exception="' . $exception->id . '">Rendre disponible</button></div>';
            $time += $rdvTypeDuration;
            continue;
        }

        if ($rdv = appointment_admin_isBooked($time, $allRdv, $rdvTypeDuration)) {

            $Client->setId($rdv->idClient);
            $Client->show();

            $RdvType = new RdvType();
            $RdvType->setId($rdv->idTypeRdv);
            $RdvType->show();

            $html .= '<div>' . minutesToHours($rdv->start) . ' - ' . minutesToHours($rdv->end) . '<br>';
            $html .= '<strong>' . $RdvType->getName() . '</strong><br>';
            $html .= $Client->getLastName() . ' ' . $Client->getFirstName();

            if (!$Client->getStatus()) {
                $html .= '<button class="btn btn-sm btn-link p-0 ml-2 confirmClient" data-id-client="' . $Client->getId() . '">Confirmer le client</button>';
            }

            $html .= '<hr class="my-1 mx-0" style="width:50px;">';
            $html .= '<strong>Email : </strong>' . $Client->getEmail();
            $html .= '<br><strong>Tel : </strong>' . $Client->getTel();

            if (!empty($Client->getOptions())) {
                $options = unserialize($Client->getOptions());

                $RdvTypeForm = new RdvTypeForm();
                $RdvTypeForm->setIdRdvType($rdv->idTypeRdv);
                if ($rdvForm = $RdvTypeForm->showAll()) {
                    $rdvForm = extractFromObjToSimpleArr($rdvForm, 'slug', 'name');

                    foreach ($options as $key => $val) {
                        if (!empty($val)) {
                            $html .= '<br><strong>' . $rdvForm[$key] . ' : </strong>' . $val;
                        }
                    }
                }
            }

            $html .= '</div>';

            if ($rdv->status) {
                $html .= '<div><button class="btn btn-sm btn-outline-danger deleteRdv" data-id-rdv="' . $rdv->id . '">Annuler ce rdv</button></div>';
            } else {
                $html .= '<div><button class="btn btn-sm btn-warning confirmRdv" data-id-rdv="' . $rdv->id . '">Confirmer le rdv</button></div>';
            }

            $time = $rdv->end;

            $timeSlotRange = range($start, $end, $rdvTypeDuration);
            if (!$compressHour && !in_array($time, $timeSlotRange)) {
                foreach ($timeSlotRange as $timeSlot) {
                    if ($time < $timeSlot) {
                        $time = $timeSlot;
                        break;
                    }
                }
            }

            continue;
        }

        $html .= minutesToHours($time) . ' - ' . minutesToHours($time + $rdvTypeDuration);
        $html .= '<div><button class="btn btn-sm btn-outline-primary addNewRdv" data-toggle="modal" 
        data-target="#addNewRdvForm" data-start="' . $time . '" data-end="' . ($time + $rdvTypeDuration) . '">Ajouter un rdv</button> ';
        $html .= '<button class="btn btn-sm btn-secondary MakeTheTimeSlotUnavailable " data-start="' . $time . '" 
        data-end="' . ($time + $rdvTypeDuration) . '">Rendre indisponible</button></div>';
        $time += $rdvTypeDuration;

        $html .= '</li>';
    }

    $html .= '</ul>';

    return $html;
}

/**
 * @param $idRdvType
 * @return string
 */
function appointment_rdvTypeForm_get($idRdvType)
{
    $html = '<section id="agendaForm" class="appointmentAppoe" data-id-rdv-type="' . $idRdvType . '"><h2>' . APPOINTMENT_FORM_TITLE . '</h2>';
    $html .= '<form id="appointmentFormulaire" data-ftype="appointment" action="' . APPOINTMENT_URL . 'ajax/mail.php">';

    $lastName = $firstName = $email = $tel = $options = '';
    if (isset($_SESSION['editRdv'])) {

        $Client = new Client();
        $Client->setId($_SESSION['editRdv']['idClient']);
        if ($Client->show()) {
            $lastName = $Client->getLastName();
            $firstName = $Client->getFirstName();
            $email = $Client->getEmail();
            $tel = $Client->getTel();
            $options = unserialize($Client->getOptions());
            $html .= '<input type="hidden" name="idRdvToRemove" value="' . $_SESSION['editRdv']['idRdv'] . '">';
            $html .= '<input type="hidden" name="idClient" value="' . $Client->getId() . '">';
        }
    }

    $html .= '<div id="defaultFields"><label for="appointment_lastName">Nom *<input type="text" name="appointment_lastName" id="appointment_lastName" required="true" value="' . $lastName . '" placeholder="Entrez votre nom"></label>';
    $html .= '<label for="appointment_firstName">Prénom *<input type="text" id="appointment_firstName" name="appointment_firstName" required="true"  value="' . $firstName . '" placeholder="Entrez votre prénom"></label>';
    $html .= '<label for="appointment_email">Adresse mail *<input type="email" name="appointment_email" id="appointment_email" required="true"  value="' . $email . '" placeholder="Entrez votre e-mail"></label>';
    $html .= '<label for="appointment_tel">Téléphone *<input type="tel" name="appointment_tel" id="appointment_tel" required="true" value="' . $tel . '" placeholder="Entrez votre téléphone"></label></div>';

    $RdvTypeForm = new RdvTypeForm();
    $RdvTypeForm->setIdRdvType($idRdvType);
    if ($forms = $RdvTypeForm->showAll()) {

        $html .= '<div id="customFields">';
        foreach ($forms as $form) {
            $value = !empty($options) && !empty($options[$form->slug]) ? $options[$form->slug] : '';
            $html .= '<label for="appointment_' . $form->slug . '">' . $form->name . ($form->required ? ' *' : '');

            if ($form->type === 'textarea') {
                $html .= '</label><textarea rows="4" id="appointment_' . $form->slug . '" name="appointment_' . $form->slug . '"
                placeholder="' . $form->placeholder . '" ' . ($form->required ? 'required="true"' : '') . '>' . $value . '</textarea>';
            } else {
                $html .= '<input type="' . $form->type . '" id="appointment_' . $form->slug . '" name="appointment_' . $form->slug . '" value="' . $value . '"
                placeholder="' . $form->placeholder . '" ' . ($form->required ? 'required="true"' : '') . '></label>';
            }
        }
        $html .= '</div>';
    }

    $html .= '<div class="mb-20"><strong>* Champs obligatoires</strong></div>' . getTokenField();
    $html .= '<button type="submit" class="btn-round">Enregistrez mon RDV</button></form></section>';
    return $html;
}

/**
 * @param $idAgenda
 * @param $start
 * @param $end
 * @return array|false
 */
function appointment_getRdv($idAgenda, $start, $end)
{
    $Rdv = new Rdv();
    $Rdv->setIdAgenda($idAgenda);
    $Rdv->setStart($start);
    $Rdv->setEnd($end);
    if ($allRdv = $Rdv->showBetweenDates()) {
        return groupMultipleKeysObjectsArray($allRdv, 'date');
    }

    return false;
}

/**
 * @param $idAgenda
 * @param $date
 * @return array|false
 */
function appointment_getRdvByDate($idAgenda, $date)
{
    $Rdv = new Rdv();
    $Rdv->setIdAgenda($idAgenda);
    $Rdv->setDate($date);
    if ($allRdv = $Rdv->showAll()) {
        return $allRdv;
    }

    return false;
}

/**
 * @param $email
 * @return int|false
 */
function appointment_client_check($email)
{
    $Client = new Client();
    $Client->setEmail($email);
    if ($Client->exist()) {
        $Client->showByEmail();

        if ($Client->getStatus()) {
            return $Client->getId();
        }
    }

    return false;
}

/**
 * @param $day
 * @param array $availabilities
 * @param array $exceptions
 * @return bool
 */
function appointment_isAvailableDay($day, array $availabilities, array $exceptions = [], $onlyAvailability = false)
{
    $dayInWeek = date('w', strtotime($day));

    if (is_array($availabilities) && is_array($exceptions)) {

        foreach ($availabilities as $availability) {
            if ($availability->day == $dayInWeek) {

                if (!$onlyAvailability) {
                    foreach ($exceptions as $exception) {
                        if (($exception->date == $day || ($exception->endDate && $exception->date <= $day && $exception->endDate >= $day))
                            && $exception->start == 0 && $exception->end == 1440) {
                            return false;
                        }
                    }
                }

                return true;
            }
        }
    }

    return false;
}

/**
 * @param $time
 * @param $booking
 * @param $rdvDuration
 * @param $compressHour
 * @return bool
 */
function appointment_isBooked(&$time, $booking, $rdvDuration, $compressHour)
{
    if (!empty($booking)) {
        foreach ($booking as $rdv) {
            if (($time >= $rdv->start && $time < $rdv->end) ||
                ($time < $rdv->start && ($time + $rdvDuration) > $rdv->start)) {
                $time = $rdv->end;
                if (!$compressHour) {
                    $time += (60 - ($rdv->end - $rdv->start)) % $rdvDuration;
                }
                return true;
            }
        }
    }
    return false;
}

/**
 * @param $time
 * @param $allRdv
 * @param $rdvDuration
 * @return bool|object
 */
function appointment_admin_isBooked($time, $allRdv, $rdvDuration)
{
    if (is_array($allRdv) && !empty($allRdv)) {
        foreach ($allRdv as $rdv) {
            if (($time >= $rdv->start && $time < $rdv->end) ||
                ($time < $rdv->start && ($time + $rdvDuration) > $rdv->start)) {
                return $rdv;
            }
        }
    }
    return false;
}

/**
 * @param $time
 * @param $allExceptions
 * @param $rdvDuration
 * @param $compressHour
 * @return bool
 */
function appointment_isUnvailableHour(&$time, $allExceptions, $rdvDuration, $compressHour)
{

    if (!empty($allExceptions)) {
        foreach ($allExceptions as $exception) {

            if (($exception->start >= $time && $exception->end < $time) ||
                ($exception->start <= $time && $exception->end > $time) ||
                ($exception->start > $time && $exception->start < ($time + $rdvDuration)) ||
                ($exception->start < $time && ($exception->start + $rdvDuration) > $time)) {

                $time = ($exception->start != 0 && $exception->end != 1440) ? $exception->end : ($time + $rdvDuration);
                if (!$compressHour) {
                    $time += (60 - ($exception->end - $exception->start)) % $rdvDuration;
                }

                return true;
            }
        }
    }
    return false;
}

/**
 * @param $time
 * @param $allExceptions
 * @param $rdvDuration
 * @return bool|object
 */
function appointment_admin_isUnvailableHour($time, $allExceptions, $rdvDuration)
{
    if (!empty($allExceptions)) {
        foreach ($allExceptions as $exception) {

            if (($exception->start >= $time && $exception->end < $time) ||
                ($exception->start <= $time && $exception->end > $time) ||
                ($exception->start > $time && $exception->start < ($time + $rdvDuration)) ||
                ($exception->start < $time && ($exception->start + $rdvDuration) > $time)) {

                /*if ($item = isValInMultiArrObj($allExceptions, 'start', $time, 'obj')) {
                    if ($item->availability == 'AVAILABLE' && ($item->start == $time && ($time + $rdvDuration) == $item->end)) {

                        return false;
                    }
                }*/

                return $exception;
            }
        }
    }
    return false;
}

/**
 * @return string[]
 */
function appointment_getWeekDays()
{
    return array(0 => 'Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi');
}