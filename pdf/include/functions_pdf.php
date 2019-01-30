<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/app/plugin/pdf/ini.php');

function getPdf($templateSlug, $params, $orientation = 'P', $pdfName = 'appoe')
{
    try {
        $html2pdf = new \App\Plugin\Pdf\Html2pdf($orientation, 'A4', 'fr', true, 'UTF-8', 12);
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->writeHTML(getPdfContent($templateSlug, $params), isset($_GET['vuehtml']));
        $html2pdf->Output($pdfName . '.pdf');
    } catch (Exception $e) {
        echo $e;
        exit;
    }
}

function getPdfContent($templateSlug, $params)
{
    ob_start();
    getPdfTemplate($templateSlug, $params);
    return ob_get_clean();
}

function getPdfTemplate($templateSlug, $params)
{
    if (defined('PDF_TEMPLATE_PATH')) {

        $file = PDF_TEMPLATE_PATH . $templateSlug . '.php';

        if (file_exists($file)) {

            $templateContent = getFileContent($file, false);

            if ($params && !isArrayEmpty($params)) {
                foreach ($params as $paramKey => $paramVal) {
                    $templateContent = str_replace($paramKey, $paramVal, $templateContent);
                }
            }

            echo $templateContent;

        } else {
            echo 'Le fichier désiré n\'existe pas.';
        }
    }
}

function generatePdfTemplateTableData(array $params)
{

    $content = '';
    if ($params && !isArrayEmpty($params)) {

        foreach ($params as $param) {

            if ($param && !isArrayEmpty($param)) {

                $content .= '<tr>';
                foreach ($param as $val) {
                    $content .= '<td>' . $val . '</td>';
                }
                $content .= '</tr>';
            }
        }
    }

    return $content;
}