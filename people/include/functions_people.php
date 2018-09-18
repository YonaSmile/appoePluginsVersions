<?php
function addPersonForm(array $data = array(), array $required = array(), $formName = 'ADDPERSON', $showType = true)
{
    //defaults required fields
    $natureR = $nameR = $firstNameR = $birthDateR = $emailR = $telR = $addressR = $zipR = $cityR = $countryR = false;

    //default fields value
    $type = $nature = $name = $firstName = $birthDate = $email = $tel = $address = $zip = $city = '';
    $country = 'FR';

    //replaces defaults values from function's arguments
    extract($data, EXTR_OVERWRITE);
    extract($required, EXTR_OVERWRITE);

    //html form
    $html = '<form action="" method="post" id="addPersonForm">';
    $html .= getTokenField();
    $html .= '<div class="my-4"></div><div class="row">';
    $html .= $showType ? '<div class="col my-2">' . App\Form::select('Enregistrement de type', 'type', getAppTypes(), $type, true) . '</div>' : '';
    $html .= '<div class="col my-2">';
    $html .= App\Form::select('Nature', 'nature', PEOPLE_NATURE, $nature, $natureR);
    $html .= '</div><div class="col my-2">';
    $html .= App\Form::text('Nom', 'name', 'text', $name, $nameR, 150);
    $html .= '</div></div><div class="row"><div class="col my-2">';
    $html .= App\Form::text('Prénom', 'firstName', 'text', $firstName, $firstNameR, 150);
    $html .= '</div><div class="col my-2">';
    $html .= App\Form::text('Date de naissance', 'birthDate', 'date', $birthDate, $birthDateR, 10);
    $html .= '</div></div><div class="row"><div class="col my-2">';
    $html .= App\Form::text('Adresse Email', 'email', 'email', $email, $emailR, 255);
    $html .= '</div><div class="col my-2">';
    $html .= App\Form::text('Téléphone', 'tel', 'tel', $tel, $telR, 10);
    $html .= '</div></div><div class="row"><div class="col my-2">';
    $html .= App\Form::text('Adresse postale', 'address', 'text', $address, $addressR, 255);
    $html .= '</div><div class="col col-lg-2 my-2">';
    $html .= App\Form::text('Code postal', 'zip', 'tel', $zip, $zipR, 7);
    $html .= '</div></div><div class="row"><div class="col my-2">';
    $html .= App\Form::text('Ville', 'city', 'text', $city, $cityR, 100);
    $html .= '</div><div class="col my-2">';
    $html .= App\Form::select('Pays', 'country', listPays(), $country, $countryR);
    $html .= '</div></div><div class="my-2"><div class="row"><div class="col-12">';
    $html .= App\Form::target($formName);
    $html .= App\Form::submit('Enregistrer', $formName . 'SUBMIT');
    $html .= '</div></div></form>';

    return $html;
}