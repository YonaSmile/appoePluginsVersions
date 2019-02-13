/**
 * Send Post to Pdf Generator with required args: pdfTemplateFilename, pdfOutputName
 * @param data
 * @param viewHtml
 */
function pdfSend(data, viewHtml = false) {

    var urlAdded = viewHtml ? '?vuehtml' : '';
    var url = window.location.protocol + '//' + window.location.hostname + '/app/plugin/pdf/index.php' + urlAdded;
    var html = '';
    $.each(data, function (inputName, inputVal) {
        html += '<input type="text" name="' + inputName + '" value="' + inputVal + '" />';
    });

    var form = $('<form action="' + url + '" method="post" target="_blank">' + html + '</form>');
    $('body').append(form);
    form.submit();
}