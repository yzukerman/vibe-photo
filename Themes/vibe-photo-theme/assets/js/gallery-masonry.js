/**
 * Gallery functionality for Vibe Photo Theme
 * Includes masonry layout and lightbox features
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        console.log('Gallery JS loaded');
        console.log('jQuery version:', $.fn.jquery);
        console.log('Masonry available:', typeof $.fn.masonry !== 'undefined');
        console.log('ImagesLoaded available:', typeof $.fn.imagesLoaded !== 'undefined');
        
        initializeMasonry();
        initializeLightbox();
        initializeSlideshow();
        
        // Reinitialize on window resize
        $(window).on('resize', function() {
            reinitializeMasonry();
        });
    });

    /**
     * Initialize Masonry layout
     */
    function initializeMasonry() {
        const $masonryContainer = $('.masonry-gallery');
        
        console.log('Masonry containers found:', $masonryContainer.length);
        
        if ($masonryContainer.length) {
            // Check if masonry and imagesLoaded are available
            if (typeof $.fn.masonry === 'undefined') {
                console.error('Masonry library not loaded');
                fallbackMasonryCSS();
                return;
            }
            
            if (typeof $.fn.imagesLoaded === 'undefined') {
                console.error('ImagesLoaded library not loaded');
                // Try to initialize without imagesLoaded
                initMasonryDirect($masonryContainer);
                return;
            }
            
            // Wait for images to load
            console.log('Initializing masonry with imagesLoaded...');
            $masonryContainer.imagesLoaded(function() {
                console.log('Images loaded, initializing masonry');
                $masonryContainer.masonry({
                    itemSelector: '.masonry-item',
                    columnWidth: '.masonry-item',
                    percentPosition: true,
                    gutter: 0
                });
                console.log('Masonry initialized successfully');
            });
        }
    }

    /**
     * Initialize masonry directly without imagesLoaded
     */
    function initMasonryDirect($container) {
        setTimeout(function() {
            console.log('Initializing masonry directly...');
            $container.masonry({
                itemSelector: '.masonry-item',
                columnWidth: '.masonry-item',
                percentPosition: true,
                gutter: 0
            });
            console.log('Direct masonry initialized');
        }, 500);
    }

    /**
     * Fallback CSS Grid layout when masonry fails
     */
    function fallbackMasonryCSS() {
        console.log('Using CSS fallback for masonry...');
        $('.masonry-gallery').addClass('css-grid-fallback');
    }

    /**
     * Reinitialize masonry layout
     */
    function reinitializeMasonry() {
        const $masonryContainer = $('.masonry-gallery');
        
        if ($masonryContainer.length) {
            $masonryContainer.masonry('reloadItems');
            $masonryContainer.masonry('layout');
        }
    }

    /**
     * Initialize lightbox functionality
     */
    function initializeLightbox() {
        const $lightboxLinks = $('.gallery-lightbox');
        
        if ($lightboxLinks.length) {
            $lightboxLinks.on('click', function(e) {
                e.preventDefault();
                
                const imageUrl = $(this).attr('href');
                const caption = $(this).data('caption') || '';
                const title = $(this).attr('title') || '';
                
                openLightbox(imageUrl, caption || title);
            });
        }
    }

    /**
     * Open lightbox with image
     */
    function openLightbox(imageUrl, caption) {
        const lightboxHtml = `
            <div class="lightbox-overlay" id="lightbox-overlay">
                <div class="lightbox-container">
                    <button class="lightbox-close" id="lightbox-close">&times;</button>
                    <div class="lightbox-content">
                        <img src="${imageUrl}" alt="${caption}" class="lightbox-image">
                        ${caption ? `<div class="lightbox-caption">${caption}</div>` : ''}
                    </div>
                    <div class="lightbox-nav">
                        <button class="lightbox-prev" id="lightbox-prev">‹</button>
                        <button class="lightbox-next" id="lightbox-next">›</button>
                    </div>
                </div>
            </div>
        `;
        
        $('body').append(lightboxHtml);
        
        // Close lightbox events
        $('#lightbox-close, #lightbox-overlay').on('click', function(e) {
            if (e.target === this) {
                closeLightbox();
            }
        });
        
        // Keyboard navigation
        $(document).on('keydown.lightbox', function(e) {
            if (e.keyCode === 27) { // ESC key
                closeLightbox();
            } else if (e.keyCode === 37) { // Left arrow
                navigateLightbox('prev');
            } else if (e.keyCode === 39) { // Right arrow
                navigateLightbox('next');
            }
        });
        
        // Navigation buttons
        $('#lightbox-prev').on('click', function() {
            navigateLightbox('prev');
        });
        
        $('#lightbox-next').on('click', function() {
            navigateLightbox('next');
        });
    }

    /**
     * Close lightbox
     */
    function closeLightbox() {
        $('#lightbox-overlay').fadeOut(300, function() {
            $(this).remove();
        });
        $(document).off('keydown.lightbox');
    }

    /**
     * Navigate through lightbox images
     */
    function navigateLightbox(direction) {
        const $allImages = $('.gallery-lightbox');
        const currentSrc = $('.lightbox-image').attr('src');
        let currentIndex = -1;
        
        // Find current image index
        $allImages.each(function(index) {
            if ($(this).attr('href') === currentSrc) {
                currentIndex = index;
                return false;
            }
        });
        
        let newIndex;
        if (direction === 'next') {
            newIndex = (currentIndex + 1) % $allImages.length;
        } else {
            newIndex = currentIndex - 1;
            if (newIndex < 0) newIndex = $allImages.length - 1;
        }
        
        const $newImage = $allImages.eq(newIndex);
        const newSrc = $newImage.attr('href');
        const newCaption = $newImage.data('caption') || $newImage.attr('title') || '';
        
        // Update lightbox content
        $('.lightbox-image').fadeOut(200, function() {
            $(this).attr('src', newSrc).fadeIn(200);
        });
        
        if (newCaption) {
            $('.lightbox-caption').text(newCaption);
        }
    }

    /**
     * Initialize slideshow functionality
     */
    function initializeSlideshow() {
        let slideshowInterval;
        let isSlideshow = false;
        
        $('#slideshow-toggle').on('click', function() {
            const $button = $(this);
            
            if (!isSlideshow) {
                // Start slideshow
                isSlideshow = true;
                $button.text('Stop Slideshow');
                
                // Open first image in lightbox
                const $firstImage = $('.gallery-lightbox').first();
                if ($firstImage.length) {
                    $firstImage.trigger('click');
                    
                    // Auto-advance every 3 seconds
                    slideshowInterval = setInterval(function() {
                        if ($('#lightbox-overlay').length) {
                            navigateLightbox('next');
                        } else {
                            // Lightbox was closed, stop slideshow
                            clearInterval(slideshowInterval);
                            isSlideshow = false;
                            $button.text('Start Slideshow');
                        }
                    }, 3000);
                }
            } else {
                // Stop slideshow
                clearInterval(slideshowInterval);
                isSlideshow = false;
                $button.text('Start Slideshow');
                closeLightbox();
            }
        });
    }

})(jQuery);

/**
 * Images Loaded Plugin Fallback
 * Simple fallback if imagesLoaded plugin is not available
 */
if (typeof jQuery.fn.imagesLoaded === 'undefined') {
    jQuery.fn.imagesLoaded = function(callback) {
        const $images = this.find('img');
        let loadedCount = 0;
        const totalImages = $images.length;
        
        if (totalImages === 0) {
            callback && callback();
            return this;
        }
        
        $images.each(function() {
            const img = new Image();
            img.onload = img.onerror = function() {
                loadedCount++;
                if (loadedCount === totalImages) {
                    callback && callback();
                }
            };
            img.src = this.src;
        });
        
        return this;
    };
}

/**
 * Simple Masonry Implementation
 * Fallback if Masonry library is not available
 */
if (typeof jQuery.fn.masonry === 'undefined') {
    jQuery.fn.masonry = function(options) {
        return this.each(function() {
            const $container = jQuery(this);
            const $items = $container.find(options.itemSelector || '.masonry-item');
            
            // Simple column-based layout
            $items.css({
                'display': 'inline-block',
                'vertical-align': 'top',
                'width': '33.333%'
            });
        });
    };
}
