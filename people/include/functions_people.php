<?php
function people_addPersonFormFields(array $excludesFields = array(), array $dataFields = array(), array $requiredFields = array(), $formName = 'ADDPERSON', $showType = true, $showSaveBtn = true)
{
    //defaults fields
    $natureF = $nameF = $firstNameF = $birthDateF = $emailF = $telF = $addressF = $zipF = $cityF = $countryF = true;

    //defaults required fields
    $natureR = $nameR = $firstNameR = $birthDateR = $emailR = $telR = $addressR = $zipR = $cityR = $countryR = false;

    //default fields value
    $type = $nature = $name = $firstName = $birthDate = $email = $tel = $address = $zip = $city = '';
    $country = 'FR';

    //replaces defaults values from function's arguments
    extract($excludesFields, EXTR_OVERWRITE);
    extract($dataFields, EXTR_OVERWRITE);
    extract($requiredFields, EXTR_OVERWRITE);

    //html form
    $html = getTokenField();
    $html .= '<div class="my-4"></div><div class="row">';
    $html .= $showType ? '<div class="col my-2">' . App\Form::select('Enregistrement de type', 'type', getAppTypes(), $type, true) . '</div>' : '';

    $html .= $natureF ? '<div class="col my-2">' . App\Form::select('Nature', 'nature', PEOPLE_NATURE, $nature, $natureR) . '</div>' : '';
    $html .= $nameF ? '<div class="col my-2">' . App\Form::text('Nom', 'name', 'text', $name, $nameR, 150) . '</div>' : '';

    $html .= '</div><div class="row">';
    $html .= $firstNameF ? '<div class="col my-2">' . App\Form::text('Prénom', 'firstName', 'text', $firstName, $firstNameR, 150) . '</div>' : '';
    $html .= $birthDateF ? '<div class="col my-2">' . App\Form::text('Date de naissance', 'birthDate', 'date', $birthDate, $birthDateR, 10) . '</div>' : '';

    $html .= '</div><div class="row">';
    $html .= $emailF ? '<div class="col my-2">' . App\Form::text('Adresse Email', 'email', 'email', $email, $emailR, 255) . '</div>' : '';
    $html .= $telF ? '<div class="col my-2">' . App\Form::text('Téléphone', 'tel', 'tel', $tel, $telR, 10) . '</div>' : '';

    $html .= '</div><div class="row">';
    $html .= $addressF ? '<div class="col my-2">' . App\Form::text('Adresse postale', 'address', 'text', $address, $addressR, 255) . '</div>' : '';
    $html .= $zipF ? '<div class="col col-lg-3 my-2">' . App\Form::text('Code postal', 'zip', 'tel', $zip, $zipR, 7) . '</div>' : '';

    $html .= '</div><div class="row">';
    $html .= $cityF ? '<div class="col my-2">' . App\Form::text('Ville', 'city', 'text', $city, $cityR, 100) . '</div>' : '';
    $html .= $countryF ? '<div class="col my-2">' . App\Form::select('Pays', 'country', listPays(), $country, $countryR) . '</div>' : '';

    $html .= '</div>';
    $html .= App\Form::target($formName);
    $html .= $showSaveBtn ? '<div class="my-2"><div class="row"><div class="col-12">' . App\Form::submit('Enregistrer', $formName . 'SUBMIT') . '</div></div>' : '';

    return $html;
}