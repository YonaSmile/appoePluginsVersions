if ($('.fixed-header').length) {
    var tablePosition = $('.fixed-header').offset();
    var theadSize = {};

    $(window).scroll(function (e) {
        $('.fixed-header thead th').each(function (index, val) {
            theadSize[index] = $(this).width();
        });
        var top = this.scrollY, left = this.scrollX;
        if (top >= parseInt(tablePosition.top)) {
            $('.fixed-header thead').stop().css({
                top: (top - parseInt(tablePosition.top)) + $('#navbarUser').outerHeight() - 10,
                left: 0,
                position: 'absolute'
            });
            $('.fixed-header thead th').each(function (index, val) {
                $(this).width(theadSize[index]);
            });
        } else {
            $('.fixed-header thead').css({top: 0, left: 0, position: 'static'});
        }
    });
}
