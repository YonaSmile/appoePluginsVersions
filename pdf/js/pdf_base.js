/**
 * Send Post to Pdf Generator with required args: pdfTemplateFilename, pdfOutputName
 * @param data
 */
function pdfSend(data) {


    var url = window.location.protocol + '//' + window.location.hostname + '/app/plugin/pdf/index.php';
    var html = '';
    $.each(data, function (inputName, inputVal) {
        html += '<input type="text" name="' + inputName + '" value="' + inputVal + '" />';
    });

    var form = $('<form action="' + url + '" method="post" target="_blank">' + html + '</form>');
    $('body').append(form);
    form.submit();
}