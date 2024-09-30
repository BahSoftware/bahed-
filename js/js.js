// --------- Responsive Navbar Active Animation -----------
function test() {
    var $tabsNewAnim = $('#navbarSupportedContent');
    var $activeItemNewAnim = $tabsNewAnim.find('li.active');

    if ($activeItemNewAnim.length > 0) {
        var activeWidthNewAnimHeight = $activeItemNewAnim.innerHeight();
        var activeWidthNewAnimWidth = $activeItemNewAnim.innerWidth();
        var itemPosNewAnim = $activeItemNewAnim.position();

        // Ensure itemPosNewAnim is not undefined
        if (itemPosNewAnim) {
            $(".hori-selector").css({
                "top": itemPosNewAnim.top + "px",
                "left": itemPosNewAnim.left + "px",
                "height": activeWidthNewAnimHeight + "px",
                "width": activeWidthNewAnimWidth + "px"
            });
        }
    }
}

// Initialize on document ready
$(document).ready(function () {
    setTimeout(test, 100); // Adjusted timeout to ensure elements are loaded
});

// Recalculate on window resize
$(window).on('resize', function () {
    setTimeout(test, 500);
});

// Toggle navbar collapse and reinitialize on toggle
$(".navbar-toggler").click(function () {
    $(".navbar-collapse").slideToggle(300);
    setTimeout(test, 300); // Adjusted timeout to sync with slideToggle
});

// -------------- Add Active Class on Another Page Move ----------
jQuery(document).ready(function ($) {
    var path = window.location.pathname.split("/").pop();

    if (path === '') {
        path = 'index.html';
    }

    var $target = $('#navbarSupportedContent ul li a[href="' + path + '"]');
    if ($target.length > 0) {
        $target.parent().addClass('active');
        // Optionally, trigger the test function to update the selector
        setTimeout(test, 100);
    }
});
