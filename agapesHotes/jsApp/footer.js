if ($.isFunction(window.fixeTableHeader)) {

    $('.table-responsive').on('scroll', function () {
        fixeTableHeader(parseInt($(this).offset().top));
    });

    $(window).scroll(function () {
        fixeTableHeader(this.scrollY + $('#navbarUser').innerHeight());
    });
}
