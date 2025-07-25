jQuery(document).ready(function ($) {
    function checkScroll() {
        if ($(window).scrollTop() > 200) {
            $('#back-to-top').addClass('show');
        } else {
            $('#back-to-top').removeClass('show');
        }
    }

    checkScroll();

    $(window).scroll(function () {
        checkScroll();
    });

    $('#back-to-top').click(function () {
        $('html, body').animate({ scrollTop: 0 }, 'slow');
        return false;
    });
});
