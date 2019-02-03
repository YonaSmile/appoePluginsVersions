function pdfSend(url, data) {

    var html = '';
    $.each(data, function (inputName, inputVal) {
        html += '<input type="text" name="' + inputName + '" value="' + inputVal + '" />';
    });

    var form = $('<form action="' + url + '" method="post" target="_blank">' + html + '</form>');
    $('body').append(form);
    form.submit();
}