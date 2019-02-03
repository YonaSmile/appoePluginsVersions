<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/app/main.php');
/**
 * @param $templateSlug
 * @param $params
 * @param string $orientation
 * @param string $pdfName
 */
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

/**
 * @param $templateSlug
 * @param $params
 * @return false|string
 */
function getPdfContent($templateSlug, $params)
{
    ob_start();
    getPdfTemplate($templateSlug, $params);
    return ob_get_clean();
}

/**
 * @param $templateSlug
 * @param $params
 */
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
            echo 'Le template n\'existe pas.';
        }
    } else {
        echo 'Aucun emplacement des templates pdf, n\'est défini.';
    }
}

/**
 * @param array $params
 * @return string
 */
function generateTableFromData(array $params)
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