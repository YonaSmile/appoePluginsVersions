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

var theadSize = {};
function fixeTableHeader(top) {

    if ($('.fixed-header').length) {

        var tablePosition = parseInt($('.fixed-header').offset().top);
        $('.fixed-header thead th').each(function (index, val) {
            theadSize[index] = $(this).width();
        });

        if (top > tablePosition) {
            $('.fixed-header thead').stop().css({
                top: (top - tablePosition),
                left: 0,
                position: 'absolute'
            });
            $('.fixed-header thead th').each(function (index, val) {
                $(this).width(theadSize[index]);
            });
        } else {
            $('.fixed-header thead').css({top: 0, left: 0, position: 'static'});
        }

    }
}