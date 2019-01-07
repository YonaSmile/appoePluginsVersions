if ($('.fixed-header').length) {
    var theadSize = {};

    $(window).scroll(function (e) {
        var tablePosition = parseInt($('.fixed-header').offset().top);
        $('.fixed-header thead th').each(function (index, val) {
            theadSize[index] = $(this).width();
        });
        var top = this.scrollY, left = this.scrollX;

        if (top >= tablePosition) {
            $('.fixed-header thead').stop().css({
                top: (top - tablePosition) + $('#navbarUser').outerHeight() - 10,
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
