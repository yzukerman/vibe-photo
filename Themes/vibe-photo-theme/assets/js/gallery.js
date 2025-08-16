/**
 * Gallery functionality for Vibe Photo Theme
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        initializeGallery();
        initializeInfiniteScroll();
        initializeFilters();
    });

    function initializeGallery() {
        // Add hover effects to gallery items
        $('.photo-item').on('mouseenter', function() {
            $(this).find('img').addClass('hover-effect');
        }).on('mouseleave', function() {
            $(this).find('img').removeClass('hover-effect');
        });

        // Lazy loading for images
        if ('IntersectionObserver' in window) {
            lazyLoadImages();
        }
    }

    function lazyLoadImages() {
        var imageObserver = new IntersectionObserver(function(entries, observer) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    var img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });

        document.querySelectorAll('img[data-src]').forEach(function(img) {
            imageObserver.observe(img);
        });
    }

    function initializeInfiniteScroll() {
        var loading = false;
        var page = 2;

        $(window).scroll(function() {
            if (loading) return;

            if ($(window).scrollTop() + $(window).height() >= $(document).height() - 1000) {
                loadMorePhotos();
            }
        });

        function loadMorePhotos() {
            loading = true;
            
            // Show loading indicator
            if (!$('.loading-indicator').length) {
                $('.photo-grid').after('<div class="loading-indicator">Loading more photos...</div>');
            }

            // AJAX request to load more posts
            $.ajax({
                url: vibePhoto.ajaxurl,
                type: 'POST',
                data: {
                    action: 'load_more_photos',
                    page: page,
                    nonce: vibePhoto.nonce
                },
                success: function(response) {
                    if (response.success && response.data.html) {
                        $('.photo-grid').append(response.data.html);
                        page++;
                        
                        // Reinitialize lightbox for new images
                        if (typeof bindLightboxEvents === 'function') {
                            bindLightboxEvents();
                        }
                    } else {
                        $('.loading-indicator').text('No more photos to load.');
                    }
                },
                error: function() {
                    $('.loading-indicator').text('Error loading photos.');
                },
                complete: function() {
                    loading = false;
                    if (page > 2) {
                        $('.loading-indicator').remove();
                    }
                }
            });
        }
    }

    function initializeFilters() {
        // Category filter functionality
        $('.gallery-filters').on('click', '.filter-btn', function(e) {
            e.preventDefault();
            
            var $btn = $(this);
            var filter = $btn.data('filter');
            
            // Update active state
            $('.filter-btn').removeClass('active');
            $btn.addClass('active');
            
            // Filter gallery items
            if (filter === 'all') {
                $('.photo-item').fadeIn();
            } else {
                $('.photo-item').each(function() {
                    var $item = $(this);
                    var categories = $item.data('categories');
                    
                    if (categories && categories.includes(filter)) {
                        $item.fadeIn();
                    } else {
                        $item.fadeOut();
                    }
                });
            }
        });
    }

    // Masonry layout for gallery
    function initializeMasonry() {
        if ($.fn.masonry) {
            $('.photo-grid').masonry({
                itemSelector: '.photo-item',
                columnWidth: '.photo-item',
                percentPosition: true,
                gutter: 20
            });
        }
    }

    // Search functionality
    function initializeSearch() {
        var $searchInput = $('.gallery-search input');
        var searchTimeout;

        $searchInput.on('input', function() {
            clearTimeout(searchTimeout);
            var query = $(this).val().toLowerCase();
            
            searchTimeout = setTimeout(function() {
                $('.photo-item').each(function() {
                    var $item = $(this);
                    var title = $item.find('h2 a').text().toLowerCase();
                    var description = $item.find('.photo-meta').text().toLowerCase();
                    
                    if (query === '' || title.includes(query) || description.includes(query)) {
                        $item.fadeIn();
                    } else {
                        $item.fadeOut();
                    }
                });
            }, 300);
        });
    }

    // Image preloader
    function preloadImages() {
        $('.photo-item img').each(function() {
            var img = new Image();
            img.src = $(this).attr('src');
        });
    }

    // Initialize everything when document is ready
    $(window).on('load', function() {
        initializeMasonry();
        preloadImages();
    });

})(jQuery);
