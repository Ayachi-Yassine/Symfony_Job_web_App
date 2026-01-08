(function ($) {
    "use strict";

    // Dark Mode Toggle - Initialize immediately
    function initDarkMode() {
        const html = document.documentElement;

        // Load saved preference from localStorage
        const savedMode = localStorage.getItem('darkMode');
        if (savedMode === 'enabled') {
            html.classList.add('dark-mode');
        } else if (savedMode === null) {
            // Check system preference if no saved preference
            if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                html.classList.add('dark-mode');
                localStorage.setItem('darkMode', 'enabled');
            }
        }

        // Setup toggle button click handler
        function setupToggleListener() {
            const darkModeToggle = document.getElementById('darkModeToggle');
            if (darkModeToggle) {
                darkModeToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    html.classList.toggle('dark-mode');

                    // Save preference
                    if (html.classList.contains('dark-mode')) {
                        localStorage.setItem('darkMode', 'enabled');
                    } else {
                        localStorage.setItem('darkMode', 'disabled');
                    }
                });
            }
        }

        // Try to setup immediately
        setupToggleListener();
        
        // Also setup on DOMContentLoaded if not already found
        if (document.readyState !== 'loading') {
            setupToggleListener();
        } else {
            document.addEventListener('DOMContentLoaded', setupToggleListener);
        }
    }

    // Initialize immediately
    initDarkMode();

    // Spinner
    var spinner = function () {
        setTimeout(function () {
            if ($('#spinner').length > 0) {
                $('#spinner').removeClass('show');
            }
        }, 1);
    };
    spinner();

    //..
    // Initiate the wowjs
    new WOW().init();


    // Sticky Navbar

    $(window).scroll(function () {
        if ($(this).scrollTop() > 300) {
            $('.sticky-top').css('top', '0px');
        } else {
            $('.sticky-top').css('top', '-100px');
        }
    });


    // Back to top button
    $(window).scroll(function () {
        if ($(this).scrollTop() > 300) {
            $('.back-to-top').fadeIn('slow');
        } else {
            $('.back-to-top').fadeOut('slow');
        }
    });
    $('.back-to-top').click(function () {
        $('html, body').animate({scrollTop: 0}, 1500, 'easeInOutExpo');
        return false;
    });


    // Header carousel
    $(".header-carousel").owlCarousel({
        autoplay: true,
        smartSpeed: 1500,
        items: 1,
        dots: true,
        loop: true,
        nav : true,
        navText : [
            '<i class="bi bi-chevron-left"></i>',
            '<i class="bi bi-chevron-right"></i>'
        ]
    });


    // Testimonials carousel
    $(".testimonial-carousel").owlCarousel({
        autoplay: true,
        smartSpeed: 1000,
        center: true,
        margin: 24,
        dots: true,
        loop: true,
        nav : false,
        responsive: {
            0:{
                items:1
            },
            768:{
                items:2
            },
            992:{
                items:3
            }
        }
    });

})(jQuery);

