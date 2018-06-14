jQuery(document).ready(function ($) {

    $('input.urlFile').each(function () {
        $('<button type="button" data-toggle="modal" data-target="#libraryModal" data-idcmsinput="' + $(this).data('idcmscontent') + '"' +
            ' class="btn btn-sm btn-info libraryButton">' +
            '<i class="far fa-file-alt"></i></button>').insertAfter(this);
    });

});