<?php

use App\Hook;
use App\Plugin\Appointment\Agenda;
use App\Plugin\Appointment\AgendaMeta;
use App\Plugin\Appointment\Availabilities;
use App\Plugin\Appointment\Client;
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

            if (is_null($url) && defined('APPOINTMENT_FILENAME') && function_exists('getPageByFilename')) {
                $Cms = getPageByFilename(APPOINTMENT_FILENAME);
                $url = WEB_DIR_URL . $Cms->getSlug() . DIRECTORY_SEPARATOR;
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
        if ($agendas = $Agenda->showAll()) {

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
    if ($rdvTypes = $RdvType->showAll()) {
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

    $Exception = new \App\Plugin\Appointment\Exception();
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
    $Exception = new \App\Plugin\Appointment\Exception();
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
            $value = !empty($options) && !empty($options[slugify($form->name)]) ? $options[slugify($form->name)] : '';
            $html .= '<label for="appointment_' . slugify($form->name) . '">' . $form->name . ($form->required ? ' *' : '') .
                '<input type="' . $form->type . '" id="appointment_' . slugify($form->name) . '" name="appointment_' . slugify($form->name) . '" value="' . $value . '"
                placeholder="' . $form->placeholder . '" ' . ($form->required ? 'required="true"' : '') . '></label>';
        }
        $html .= '</div>';
    }

    $html .= '<div class="mb-20"><strong>* Champs obligatoires</strong></div>' . getTokenField();
    $html .= '<button type="submit" class="btn-round">Enregistrez mon RDV</button></form></section>';
    return $html;
}

/**
 * @param $day
 * @param array $availabilities
 * @param array $exceptions
 * @return bool
 */
function appointment_isAvailableDay($day, array $availabilities, array $exceptions = [])
{
    $dayInWeek = date('w', strtotime($day));

    if (is_array($availabilities) && is_array($exceptions)) {

        foreach ($availabilities as $availability) {
            if ($availability->day == $dayInWeek) {

                foreach ($exceptions as $exception) {
                    if (($exception->date == $day || ($exception->endDate && $exception->date <= $day && $exception->endDate >= $day))
                        && $exception->start == 0 && $exception->end == 1440) {
                        return false;
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

                $time = $exception->end;
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
 * @return string[]
 */
function appointment_getWeekDays()
{
    return array(0 => 'Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi');
}

/**
 * @param $hours
 * @return float|int|mixed|string
 */
function hoursToMinutes($hours)
{
    $minutes = 0;
    if (strpos($hours, ':') !== false) {
        list($hours, $minutes) = explode(':', $hours);
        settype($minutes, 'integer');
    }
    settype($hours, 'integer');
    return $hours * 60 + $minutes;
}

/**
 * @param $time
 * @param string $format
 * @return int|string
 */
function minutesToHours($time, $format = '%s:%s')
{
    settype($time, 'integer');
    if ($time < 0 || $time >= 1440) {
        return 0;
    }
    $hours = floor($time / 60);
    $minutes = $time % 60;
    if ($hours < 10) {
        $hours = '0' . $hours;
    }
    if ($minutes < 10) {
        $minutes = '0' . $minutes;
    }
    return sprintf($format, $hours, $minutes);
}