function disabledAllFields(inputExlude) {
    $('input.sensibleField').not(inputExlude).attr('disabled', 'disabled');
}

function activateAllFields() {
    $('input.sensibleField').attr('disabled', false);
}
function adaptResponsiveTable() {

    var $element = $('.table-responsive');
    var tableWidth = $('table', $element).width();
    var tableHeight = $element.height();
    var windowWidth = $(window).width();
    var windowHeight = $(window).height();
    var height = '100%';

    if ((tableWidth + $('table', $element).offset().left) > windowWidth) {
        height = (windowHeight - $element.offset().top - parseReelFloat($('#mainContent').css('padding-bottom')) - 25) + 'px';
    }

    $element.css({height: height});
}