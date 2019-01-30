<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/app/main.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/app/plugin/pdf/include/functions_pdf.php');

$tableData = $_POST['tableData'];

$params = array(
    '{{titre}}' => 'Synthèse',
    '{{table_lines}}' => $tableData
);
getPdf('table', $params, 'L');