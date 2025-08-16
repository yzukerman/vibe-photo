/**
 * Navigation functionality for Vibe Photo Theme using Foundation
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        // Initialize Foundation
        $(document).foundation();
        
        initializeMobileMenu();
        initializeSmoothScroll();
        initializeHeaderScroll();
        highlightCurrentPage();
    });

    function initializeMobileMenu() {
        // Mobile menu toggle functionality using Foundation's toggler
        $('.menu-toggle').on('click', function(e) {
            e.preventDefault();
            
            const targetMenu = $('#responsive-menu');
            targetMenu.foundation('toggle');
        });

        // Close mobile menu when clicking outside
        $(document).on('click', function(e) {
            const target = $(e.target);
            const mobileMenu = $('#responsive-menu');
            
            if (!target.closest('.menu-toggle').length && 
                !target.closest('#responsive-menu').length && 
                mobileMenu.hasClass('is-active')) {
                mobileMenu.foundation('close');
            }
        });

        // Close menu when clicking on menu items
        $('.main-navigation a').on('click', function() {
            const mobileMenu = $('#responsive-menu');
            if (mobileMenu.hasClass('is-active')) {
                mobileMenu.foundation('close');
            }
        });

        // Handle window resize - close mobile menu on desktop view
        $(window).on('resize', function() {
            if ($(window).width() >= 640) { // Foundation's medium breakpoint
                const mobileMenu = $('#responsive-menu');
                if (mobileMenu.hasClass('is-active')) {
                    mobileMenu.foundation('close');
                }
            }
        });
    }

    function initializeSmoothScroll() {
        // Smooth scroll for anchor links
        $('a[href*="#"]:not([href="#"])').on('click', function() {
            if (location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') && location.hostname == this.hostname) {
                var target = $(this.hash);
                target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
                
                if (target.length) {
                    $('html, body').animate({
                        scrollTop: target.offset().top - 80 // Account for fixed header
                    }, 800);
                    return false;
                }
            }
        });
    }

    function initializeHeaderScroll() {
        var lastScrollTop = 0;
        var header = $('.site-header');
        var headerHeight = header.outerHeight();

        $(window).scroll(function() {
            var scrollTop = $(this).scrollTop();

            // Add/remove scrolled class for styling
            if (scrollTop > 50) {
                header.addClass('scrolled');
            } else {
                header.removeClass('scrolled');
            }

            // Hide/show header on scroll (optional)
            if (scrollTop > lastScrollTop && scrollTop > headerHeight) {
                // Scrolling down
                header.addClass('header-hidden');
            } else {
                // Scrolling up
                header.removeClass('header-hidden');
            }

            lastScrollTop = scrollTop;
        });
    }

    // Add current menu item highlighting
    function highlightCurrentPage() {
        var currentUrl = window.location.href;
        $('.main-navigation a').each(function() {
            if (this.href === currentUrl) {
                $(this).addClass('current-page');
            }
        });
    }

    // Add keyboard navigation support
    $('.main-navigation a').on('keydown', function(e) {
        if (e.which === 13 || e.which === 32) {
            e.preventDefault();
            $(this)[0].click();
        }
    });

    // Initialize Foundation's equalizer for photo grids
    if ($('[data-equalizer]').length) {
        $('[data-equalizer]').foundation('equalizer', 'reflow');
    }

})(jQuery);
