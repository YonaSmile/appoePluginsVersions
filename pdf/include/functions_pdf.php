<?php
/*
 * $html2pdf->output($name, $dest);
 * I: send the file inline to the browser (default). The plug-in is used if available. The name given by name is used when one selects the "Save as" option on the link generating the PDF.
 * D: send to the browser and force a file download with the name given by name.
 * F: save to a local server file with the name given by name.
 * S: return the document as a string (name is ignored).
 * FI: equivalent to F + I option
 * FD: equivalent to F + D option
 * E: return the document as base64 mime multi-part email attachment (RFC 2045)
 */

use App\Plugin\Pdf\Html2pdf;

require_once($_SERVER['DOCUMENT_ROOT'] . '/app/main.php');
/**
 * @param $templateSlug
 * @param $params
 * @param string $orientation
 * @param string $pdfName
 * @param string $destination
 */
function getPdf($templateSlug, $params, $orientation = 'P', $pdfName = 'appoe', $destination = 'I')
{
    try {
        $html2pdf = new Html2pdf($orientation, 'A4', 'fr', true, 'UTF-8', 12);
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->pdf->SetCreator('APPOE | AOE - Communication');
        $html2pdf->pdf->SetAuthor('APPOE | AOE - Communication');
        $html2pdf->pdf->SetTitle($pdfName);
        $html2pdf->pdf->SetSubject($pdfName);
        $html2pdf->pdf->SetKeywords($pdfName);
        $html2pdf->writeHTML(getPdfContent($templateSlug, $params), isset($_GET['vuehtml']));
        $html2pdf->Output($pdfName . '.pdf', $destination);
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

            if ($params && preg_match_all("/{{(.*?)}}/", $templateContent, $match)) {

                foreach ($match[1] as $i => $zone) {

                    //Set zones
                    $templateContent = str_replace($match[0][$i], sprintf('%s', !empty($params[$zone]) ? $params[$zone] : ''), $templateContent);

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