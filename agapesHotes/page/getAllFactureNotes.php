<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/app/main.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/app/plugin/pdf/include/functions_pdf.php');

$params = array(
    '{{employeName}}' => $_POST['employeName'],
    '{{siteName}}' => $_POST['siteName'],
    '{{date}}' => $_POST['date'],
    '{{commentaires}}' => $_POST['commentaires'],
    '{{notesDeFraisTable}}' => $_POST['notesDeFraisTable'],
    '{{totalTTC}}' => $_POST['totalTTC'],
    '{{totalIndemniteKm}}' => $_POST['totalIndemniteKm']
);
getPdf('allNotes', $params, 'P', 'TousLesNotesDeFrais-' . slugify($_POST['employeName']) . '-' . slugify($_POST['date']));