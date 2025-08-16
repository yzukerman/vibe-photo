/**
 * Enhanced Lightbox functionality for Vibe Photo Theme
 * Features: Navigation, Social Sharing, EXIF Data, Image Details
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        // Initialize lightbox for gallery images
        if ($('.masonry-gallery').length || $('.gallery-link').length) {
            createLightbox();
            bindLightboxEvents();
        }
    });

    function createLightbox() {
        // Create lightbox styles
        var styles = '<style>' +
            '#vibe-lightbox { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); z-index: 10000; display: none; align-items: center; justify-content: center; }' +
            '#vibe-lightbox.active { display: flex; }' +
            '.lightbox-container { max-width: 90vw; max-height: 90vh; background: white; border-radius: 8px; overflow: hidden; display: flex; flex-direction: column; }' +
            '.lightbox-header { padding: 15px 20px; background: #f8f9fa; border-bottom: 1px solid #dee2e6; display: flex; justify-content: space-between; align-items: center; }' +
            '.lightbox-title { margin: 0; font-size: 18px; font-weight: 600; }' +
            '.lightbox-close { background: none; border: none; font-size: 24px; cursor: pointer; padding: 0; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center; }' +
            '.lightbox-main { display: flex; align-items: center; position: relative; }' +
            '.lightbox-content { flex: 1; text-align: center; }' +
            '.lightbox-image-container { position: relative; }' +
            '.lightbox-image { max-width: 100%; max-height: 60vh; object-fit: contain; }' +
            '.lightbox-loading { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); }' +
            '.lightbox-nav { background: rgba(0,0,0,0.5); color: white; border: none; padding: 15px 20px; font-size: 24px; cursor: pointer; position: absolute; top: 50%; transform: translateY(-50%); z-index: 1; }' +
            '.lightbox-prev { left: 10px; }' +
            '.lightbox-next { right: 10px; }' +
            '.lightbox-footer { padding: 20px; background: #f8f9fa; border-top: 1px solid #dee2e6; }' +
            '.lightbox-info { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; }' +
            '.lightbox-exif h4, .lightbox-sharing h4 { margin: 0 0 15px 0; font-size: 16px; font-weight: 600; }' +
            '.exif-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }' +
            '.exif-item { display: flex; justify-content: space-between; }' +
            '.exif-label { font-weight: 500; }' +
            '.share-buttons { display: flex; flex-wrap: wrap; gap: 10px; }' +
            '.share-btn { display: flex; align-items: center; gap: 5px; padding: 8px 12px; background: #007cba; color: white; text-decoration: none; border-radius: 4px; border: none; cursor: pointer; font-size: 14px; }' +
            '.share-btn:hover { background: #005a87; color: white; }' +
            '.image-counter { font-size: 14px; color: #666; }' +
            '.lightbox-description { margin: 10px 0; color: #666; }' +
            'body.lightbox-open { overflow: hidden; }' +
            '@media (max-width: 768px) { .lightbox-info { grid-template-columns: 1fr; gap: 20px; } .exif-grid { grid-template-columns: 1fr; } }' +
            '</style>';
        
        var lightboxHTML = '<div id="vibe-lightbox" class="lightbox-overlay">' +
            '<div class="lightbox-container">' +
                '<div class="lightbox-header">' +
                    '<h3 class="lightbox-title"></h3>' +
                    '<button class="lightbox-close" aria-label="Close lightbox">&times;</button>' +
                '</div>' +
                '<div class="lightbox-main">' +
                    '<button class="lightbox-nav lightbox-prev" aria-label="Previous image">‹</button>' +
                    '<div class="lightbox-content">' +
                        '<div class="lightbox-image-container">' +
                            '<img src="" alt="" class="lightbox-image">' +
                            '<div class="lightbox-loading">Loading...</div>' +
                        '</div>' +
                    '</div>' +
                    '<button class="lightbox-nav lightbox-next" aria-label="Next image">›</button>' +
                '</div>' +
                '<div class="lightbox-footer">' +
                    '<div class="lightbox-details">' +
                        '<p class="lightbox-description"></p>' +
                        '<div class="lightbox-meta">' +
                            '<span class="image-counter"></span>' +
                        '</div>' +
                    '</div>' +
                    '<div class="lightbox-info">' +
                        '<div class="lightbox-exif">' +
                            '<h4>Image Details</h4>' +
                            '<div class="exif-grid">' +
                                '<div class="exif-item">' +
                                    '<span class="exif-label">Camera:</span>' +
                                    '<span class="exif-value" data-exif="camera">-</span>' +
                                '</div>' +
                                '<div class="exif-item">' +
                                    '<span class="exif-label">Lens:</span>' +
                                    '<span class="exif-value" data-exif="lens">-</span>' +
                                '</div>' +
                                '<div class="exif-item">' +
                                    '<span class="exif-label">Aperture:</span>' +
                                    '<span class="exif-value" data-exif="aperture">-</span>' +
                                '</div>' +
                                '<div class="exif-item">' +
                                    '<span class="exif-label">Shutter:</span>' +
                                    '<span class="exif-value" data-exif="shutter">-</span>' +
                                '</div>' +
                                '<div class="exif-item">' +
                                    '<span class="exif-label">ISO:</span>' +
                                    '<span class="exif-value" data-exif="iso">-</span>' +
                                '</div>' +
                                '<div class="exif-item">' +
                                    '<span class="exif-label">Focal Length:</span>' +
                                    '<span class="exif-value" data-exif="focal">-</span>' +
                                '</div>' +
                                '<div class="exif-item">' +
                                    '<span class="exif-label">Date Taken:</span>' +
                                    '<span class="exif-value" data-exif="date">-</span>' +
                                '</div>' +
                                '<div class="exif-item">' +
                                    '<span class="exif-label">Size:</span>' +
                                    '<span class="exif-value" data-exif="size">-</span>' +
                                '</div>' +
                            '</div>' +
                        '</div>' +
                        '<div class="lightbox-sharing">' +
                            '<h4>Share this image</h4>' +
                            '<div class="share-buttons">' +
                                '<a href="#" class="share-btn facebook" target="_blank" rel="noopener">' +
                                    '<span class="share-icon">📘</span>' +
                                    '<span class="share-text">Facebook</span>' +
                                '</a>' +
                                '<a href="#" class="share-btn twitter" target="_blank" rel="noopener">' +
                                    '<span class="share-icon">🐦</span>' +
                                    '<span class="share-text">Twitter</span>' +
                                '</a>' +
                                '<a href="#" class="share-btn pinterest" target="_blank" rel="noopener">' +
                                    '<span class="share-icon">📌</span>' +
                                    '<span class="share-text">Pinterest</span>' +
                                '</a>' +
                                '<a href="#" class="share-btn download" download>' +
                                    '<span class="share-icon">💾</span>' +
                                    '<span class="share-text">Download</span>' +
                                '</a>' +
                                '<button class="share-btn copy-link">' +
                                    '<span class="share-icon">🔗</span>' +
                                    '<span class="share-text">Copy Link</span>' +
                                '</button>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>' +
        '</div>';
        
        $('head').append(styles);
        $('body').append(lightboxHTML);
    }

    function bindLightboxEvents() {
        var currentIndex = 0;
        var images = [];

        // Collect all gallery images
        $('.gallery-link').each(function(index) {
            var $link = $(this);
            var $img = $link.find('img');
            var $parent = $link.closest('.photo-item');
            
            images.push({
                src: $link.attr('href'),
                title: $img.attr('alt') || $link.attr('title') || 'Untitled',
                description: $link.attr('data-caption') || $parent.find('.image-caption p').text() || '',
                thumb: $img.attr('src'),
                alt: $img.attr('alt') || ''
            });

            // Prevent default link behavior and open lightbox
            $link.on('click', function(e) {
                e.preventDefault();
                currentIndex = index;
                openLightbox();
            });
        });

        // Lightbox controls
        $(document).on('click', '.lightbox-close', closeLightbox);
        $(document).on('click', '.lightbox-prev', showPrevImage);
        $(document).on('click', '.lightbox-next', showNextImage);

        // Close on overlay click
        $(document).on('click', '.lightbox-overlay', function(e) {
            if (e.target === this) {
                closeLightbox();
            }
        });

        // Social sharing events
        $(document).on('click', '.copy-link', function(e) {
            e.preventDefault();
            copyImageLink();
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
            var $imageCounter = $('.image-counter');

            // Update title and description
            $lightboxTitle.text(image.title);
            $lightboxDescription.text(image.description);
            $imageCounter.text((index + 1) + ' of ' + images.length);

            // Add loading state
            $lightboxImage.addClass('loading');
            
            // Load new image
            var newImg = new Image();
            newImg.onload = function() {
                $lightboxImage.attr('src', image.src).attr('alt', image.alt);
                $lightboxImage.removeClass('loading');
                
                // Load EXIF data and update sharing links
                loadImageMetadata(image.src, index);
                updateSharingLinks(image);
            };
            newImg.src = image.src;

            // Update navigation visibility
            $('.lightbox-prev').toggle(images.length > 1);
            $('.lightbox-next').toggle(images.length > 1);
        }

        function loadImageMetadata(imageSrc, imageIndex) {
            // Reset EXIF data
            $('.exif-value').text('-');
            
            // Try to get EXIF data via WordPress AJAX
            if (typeof vibePhotoAjax !== 'undefined' && vibePhotoAjax.ajaxurl) {
                $.ajax({
                    url: vibePhotoAjax.ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'vibe_photo_get_image_exif',
                        image_url: imageSrc,
                        nonce: vibePhotoAjax.nonce
                    },
                    success: function(response) {
                        if (response.success && response.data) {
                            updateExifData(response.data);
                        }
                    },
                    error: function() {
                        // Fallback: try to extract basic info from image
                        extractBasicImageInfo(imageSrc);
                    }
                });
            } else {
                extractBasicImageInfo(imageSrc);
            }
        }

        function extractBasicImageInfo(imageSrc) {
            var img = new Image();
            img.onload = function() {
                $('.exif-value[data-exif="size"]').text(this.naturalWidth + ' × ' + this.naturalHeight + ' pixels');
            };
            img.src = imageSrc;
        }

        function updateExifData(exifData) {
            // Update EXIF fields based on available data
            if (exifData.camera) $('.exif-value[data-exif="camera"]').text(exifData.camera);
            if (exifData.lens) $('.exif-value[data-exif="lens"]').text(exifData.lens);
            if (exifData.aperture) $('.exif-value[data-exif="aperture"]').text(exifData.aperture);
            if (exifData.shutter) $('.exif-value[data-exif="shutter"]').text(exifData.shutter);
            if (exifData.iso) $('.exif-value[data-exif="iso"]').text(exifData.iso);
            if (exifData.focal_length) $('.exif-value[data-exif="focal"]').text(exifData.focal_length);
            if (exifData.date_taken) $('.exif-value[data-exif="date"]').text(exifData.date_taken);
            if (exifData.file_size) $('.exif-value[data-exif="size"]').text(exifData.file_size);
        }

        function updateSharingLinks(image) {
            var currentUrl = window.location.href;
            var imageUrl = image.src;
            var title = encodeURIComponent(image.title);
            var description = encodeURIComponent(image.description || 'Check out this photo');

            // Update sharing URLs
            $('.share-btn.facebook').attr('href', 'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(currentUrl));
            $('.share-btn.twitter').attr('href', 'https://twitter.com/intent/tweet?text=' + description + '&url=' + encodeURIComponent(currentUrl));
            $('.share-btn.pinterest').attr('href', 'https://pinterest.com/pin/create/button/?url=' + encodeURIComponent(currentUrl) + '&media=' + encodeURIComponent(imageUrl) + '&description=' + description);
            $('.share-btn.download').attr('href', imageUrl);
        }

        function copyImageLink() {
            var imageUrl = $('.lightbox-image').attr('src');
            
            if (navigator.clipboard) {
                navigator.clipboard.writeText(imageUrl).then(function() {
                    showCopySuccess();
                });
            } else {
                // Fallback for older browsers
                var textArea = document.createElement('textarea');
                textArea.value = imageUrl;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                showCopySuccess();
            }
        }

        function showCopySuccess() {
            var $copyBtn = $('.copy-link .share-text');
            var originalText = $copyBtn.text();
            $copyBtn.text('Copied!');
            setTimeout(function() {
                $copyBtn.text(originalText);
            }, 2000);
        }
    }

})(jQuery);
