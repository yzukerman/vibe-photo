/**
 * Navigation functionality for Vibe Photo Theme using Foundation
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        
        initializeMobileMenu();
        initializeSmoothScroll();
        initializeHeaderScroll();
        highlightCurrentPage();
    });

    function initializeMobileMenu() {
        var menu = document.querySelector('.mobile-navigation');
        if (!menu) return;
        var toggle = menu.querySelector('summary');
        function closeMenu(restoreFocus) {
            menu.open = false;
            if (restoreFocus) toggle.focus();
        }
        $(menu).on('keydown', function(e) {
            if (e.key === 'Escape' && menu.open) {
                e.preventDefault();
                closeMenu(true);
            }
        });
        $(document).on('click', function(e) {
            if (menu.open && !menu.contains(e.target)) closeMenu(false);
        });
        $(menu).find('a').on('click', function() { closeMenu(false); });
        $(window).on('resize', function() {
            if (window.matchMedia('(min-width: 640px)').matches && menu.open) {
                var containedFocus = menu.contains(document.activeElement);
                closeMenu(false);
                if (containedFocus) {
                    var desktopLink = document.querySelector('#primary-menu a');
                    if (desktopLink) desktopLink.focus();
                }
            }
        });
    }

    function initializeSmoothScroll() {
        // Smooth scroll for anchor links
        $('a[href*="#"]:not([href="#"]):not(.skip-link)').on('click', function() {
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

    // Native links retain their standard keyboard behavior.
    $(document).on('click', '.skip-link', function() {
        var content = document.getElementById('main-content');
        if (content) content.focus();
    });

})(jQuery);
