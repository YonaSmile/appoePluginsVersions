<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/app/main.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/app/plugin/pdf/include/functions_pdf.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (getUserIdSession()) {

        $orientation = !empty($_POST['pdfTemplateOrientation']) ? $_POST['pdfTemplateOrientation'] : 'P';
        $templateFile = !empty($_POST['pdfTemplateFilename']) ? $_POST['pdfTemplateFilename'] : '';
        $pdfOutputName = !empty($_POST['pdfOutputName']) ? $_POST['pdfOutputName'] : '';

        //Delete useless keys
        unset($_POST['pdfTemplateOrientation']);
        unset($_POST['pdfTemplateFilename']);
        unset($_POST['pdfOutputName']);

        getPdf($templateFile, $_POST, $orientation, $pdfOutputName);
    }
}