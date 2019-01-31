<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/app/main.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/app/plugin/pdf/include/functions_pdf.php');

$params = array(
    '{{employeName}}' => $_POST['employeName'],
    '{{siteName}}' => $_POST['siteName'],
    '{{date}}' => $_POST['date'],
    '{{typeVehicule}}' => $_POST['typeVehicule'],
    '{{puissance}}' => $_POST['puissance'],
    '{{taux}}' => $_POST['taux'],
    '{{indemniteKmTable}}' => $_POST['indemniteKmTable'],
    '{{total}}' => $_POST['total']
);
getPdf('indemniteKilometrique', $params, 'P', 'indemniteKilometrique-' . slugify($_POST['employeName']) . '-' . slugify($_POST['date']));