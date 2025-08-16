/**
 * Lightbox functionality for Vibe Photo Theme
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        // Create lightbox overlay
        if ($('.photo-grid').length) {
            createLightbox();
            bindLightboxEvents();
        }
    });

    function createLightbox() {
        var lightboxHTML = `
            <div id="vibe-lightbox" class="lightbox-overlay">
                <div class="lightbox-container">
                    <button class="lightbox-close">&times;</button>
                    <button class="lightbox-prev">‹</button>
                    <button class="lightbox-next">›</button>
                    <div class="lightbox-content">
                        <img src="" alt="" class="lightbox-image">
                        <div class="lightbox-caption">
                            <h3 class="lightbox-title"></h3>
                            <p class="lightbox-description"></p>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        $('body').append(lightboxHTML);
    }

    function bindLightboxEvents() {
        var currentIndex = 0;
        var images = [];

        // Collect all gallery images
        $('.photo-item a, .gallery-link').each(function(index) {
            var $link = $(this);
            var $img = $link.find('img');
            
            images.push({
                src: $link.attr('href'),
                title: $img.attr('alt') || '',
                description: $link.closest('.photo-item').find('.photo-meta h2 a').text() || ''
            });

            // Prevent default link behavior and open lightbox
            $link.on('click', function(e) {
                e.preventDefault();
                currentIndex = index;
                openLightbox();
            });
        });

        // Lightbox controls
        $('.lightbox-close').on('click', closeLightbox);
        $('.lightbox-prev').on('click', showPrevImage);
        $('.lightbox-next').on('click', showNextImage);

        // Close on overlay click
        $('.lightbox-overlay').on('click', function(e) {
            if (e.target === this) {
                closeLightbox();
            }
        });

        // Keyboard navigation
        $(document).on('keydown', function(e) {
            if ($('#vibe-lightbox').hasClass('active')) {
                switch(e.keyCode) {
                    case 27: // Escape
                        closeLightbox();
                        break;
                    case 37: // Left arrow
                        showPrevImage();
                        break;
                    case 39: // Right arrow
                        showNextImage();
                        break;
                }
            }
        });

        function openLightbox() {
            if (images.length === 0) return;
            
            showImage(currentIndex);
            $('#vibe-lightbox').addClass('active');
            $('body').addClass('lightbox-open');
        }

        function closeLightbox() {
            $('#vibe-lightbox').removeClass('active');
            $('body').removeClass('lightbox-open');
        }

        function showPrevImage() {
            currentIndex = (currentIndex - 1 + images.length) % images.length;
            showImage(currentIndex);
        }

        function showNextImage() {
            currentIndex = (currentIndex + 1) % images.length;
            showImage(currentIndex);
        }

        function showImage(index) {
            if (!images[index]) return;
            
            var image = images[index];
            var $lightboxImage = $('.lightbox-image');
            var $lightboxTitle = $('.lightbox-title');
            var $lightboxDescription = $('.lightbox-description');

            // Add loading state
            $lightboxImage.addClass('loading');
            
            // Load new image
            var newImg = new Image();
            newImg.onload = function() {
                $lightboxImage.attr('src', image.src).attr('alt', image.title);
                $lightboxTitle.text(image.title);
                $lightboxDescription.text(image.description);
                $lightboxImage.removeClass('loading');
            };
            newImg.src = image.src;

            // Update navigation visibility
            $('.lightbox-prev').toggle(images.length > 1);
            $('.lightbox-next').toggle(images.length > 1);
        }
    }

})(jQuery);
