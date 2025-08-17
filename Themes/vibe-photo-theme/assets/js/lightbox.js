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
            '.lightbox-container { max-width: 95vw; max-height: 95vh; background: white; border-radius: 8px; overflow: visible !important; display: flex; flex-direction: column; position: relative; }' +
            '.lightbox-header { padding: 10px 15px; background: #f8f9fa; border-bottom: 1px solid #dee2e6; display: flex; justify-content: space-between; align-items: center; flex-shrink: 0; z-index: 1000; position: relative; }' +
            '.lightbox-title { margin: 0; font-size: 16px; font-weight: 600; }' +
            '.lightbox-close { background: rgba(255,255,255,0.9); border: 2px solid #333; font-size: 20px; cursor: pointer; padding: 8px; width: 36px; height: 36px; display: flex; align-items: center; justify-content: center; border-radius: 50%; transition: all 0.3s; font-weight: bold; color: #333; }' +
            '.lightbox-close:hover { background: #ff4444; color: white; border-color: #ff4444; transform: scale(1.1); }' +
            '.lightbox-main { display: flex; align-items: center; position: relative; flex: 1; min-height: 0; overflow: visible !important; }' +
            '.lightbox-content { flex: 1; text-align: center; height: 100%; display: flex; align-items: center; justify-content: center; position: relative; }' +
            '.lightbox-image-container { position: relative; width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; }' +
            '.lightbox-image { max-width: 100%; max-height: 100%; object-fit: contain; z-index: 1 !important; position: relative; }' +
            '.lightbox-footer { padding: 15px 20px; background: #f8f9fa; border-top: 1px solid #dee2e6; flex-shrink: 0; max-height: 40vh; overflow-y: auto; z-index: 1000; position: relative; }' +
            '.lightbox-details { margin-bottom: 15px; }' +
            '.lightbox-navigation { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #dee2e6; }' +
            '.nav-link { color: #007cba; text-decoration: none; font-weight: 500; padding: 8px 12px; border-radius: 4px; transition: all 0.3s; }' +
            '.nav-link:hover { background: #007cba; color: white; text-decoration: none; }' +
            '.nav-link:disabled, .nav-link.disabled { color: #ccc; cursor: not-allowed; pointer-events: none; }' +
            '.nav-prev { text-align: left; }' +
            '.nav-next { text-align: right; }' +
            '.lightbox-info { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; }' +
            '.lightbox-exif h4, .lightbox-sharing h4 { margin: 0 0 15px 0; font-size: 16px; font-weight: 600; }' +
            '.exif-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; max-height: 300px; overflow-y: auto; }' +
            '.exif-item { display: flex; justify-content: space-between; padding: 4px 0; border-bottom: 1px solid #f0f0f0; }' +
            '.exif-item:last-child { border-bottom: none; }' +
            '.exif-label { font-weight: 500; color: #666; }' +
            '.exif-value { color: #333; text-align: right; max-width: 60%; word-break: break-word; }' +
            '.share-buttons { display: flex; flex-wrap: wrap; gap: 10px; }' +
            '.share-btn { display: flex; align-items: center; gap: 5px; padding: 8px 12px; background: #007cba; color: white; text-decoration: none; border-radius: 4px; border: none; cursor: pointer; font-size: 14px; }' +
            '.share-btn:hover { background: #005a87; color: white; }' +
            '.image-counter { font-size: 14px; color: #666; }' +
            '.lightbox-description { margin: 10px 0; color: #666; }' +
            'body.lightbox-open { overflow: hidden; }' +
            '/* Additional close button in top-right corner of overlay */' +
            '.lightbox-overlay-close { position: absolute; top: 20px; right: 20px; background: rgba(255,255,255,0.9); border: 2px solid #333; font-size: 24px; cursor: pointer; padding: 10px; width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; border-radius: 50%; transition: all 0.3s; font-weight: bold; color: #333; z-index: 10001; }' +
            '.lightbox-overlay-close:hover { background: #ff4444; color: white; border-color: #ff4444; transform: scale(1.1); }' +
            '@media (max-width: 768px) { ' +
                '.lightbox-container { max-width: 98vw; max-height: 98vh; } ' +
                '.lightbox-info { grid-template-columns: 1fr; gap: 20px; } ' +
                '.exif-grid { grid-template-columns: 1fr; } ' +
                '.lightbox-image { max-width: 100%; } ' +
                '.lightbox-footer { max-height: 50vh; padding: 10px 15px; } ' +
                '.lightbox-overlay-close { top: 15px; right: 15px; width: 45px; height: 45px; font-size: 20px; } ' +
            '}' +
            '@media (max-width: 480px) { ' +
                '.lightbox-image { max-width: 100%; } ' +
                '.lightbox-navigation { flex-direction: column; gap: 10px; text-align: center; } ' +
                '.nav-prev, .nav-next { text-align: center; } ' +
                '.lightbox-overlay-close { top: 10px; right: 10px; width: 40px; height: 40px; font-size: 18px; } ' +
            '}' +
            '</style>';
        
        var lightboxHTML = '<div id="vibe-lightbox" class="lightbox-overlay">' +
            '<!-- Additional close button in top-right corner -->' +
            '<button class="lightbox-overlay-close" aria-label="Close lightbox">&times;</button>' +
            '<div class="lightbox-container">' +
                '<div class="lightbox-header">' +
                    '<h3 class="lightbox-title"></h3>' +
                '</div>' +
                '<div class="lightbox-main">' +
                    '<div class="lightbox-content">' +
                        '<div class="lightbox-image-container">' +
                            '<img src="" alt="" class="lightbox-image">' +
                        '</div>' +
                    '</div>' +
                '</div>' +
                '<div class="lightbox-footer">' +
                    '<div class="lightbox-details">' +
                        '<div class="lightbox-navigation">' +
                            '<a href="#" class="nav-link nav-prev">‹ Previous</a>' +
                            '<a href="#" class="nav-link nav-next">Next ›</a>' +
                        '</div>' +
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
                                    '<span class="exif-label">Flash:</span>' +
                                    '<span class="exif-value" data-exif="flash">-</span>' +
                                '</div>' +
                                '<div class="exif-item">' +
                                    '<span class="exif-label">White Balance:</span>' +
                                    '<span class="exif-value" data-exif="white_balance">-</span>' +
                                '</div>' +
                                '<div class="exif-item">' +
                                    '<span class="exif-label">Exposure Mode:</span>' +
                                    '<span class="exif-value" data-exif="exposure_mode">-</span>' +
                                '</div>' +
                                '<div class="exif-item">' +
                                    '<span class="exif-label">Metering:</span>' +
                                    '<span class="exif-value" data-exif="metering_mode">-</span>' +
                                '</div>' +
                                '<div class="exif-item">' +
                                    '<span class="exif-label">Color Space:</span>' +
                                    '<span class="exif-value" data-exif="color_space">-</span>' +
                                '</div>' +
                                '<div class="exif-item">' +
                                    '<span class="exif-label">Size:</span>' +
                                    '<span class="exif-value" data-exif="size">-</span>' +
                                '</div>' +
                                '<div class="exif-item">' +
                                    '<span class="exif-label">File Size:</span>' +
                                    '<span class="exif-value" data-exif="file_size">-</span>' +
                                '</div>' +
                                '<div class="exif-item gps-item" style="display: none;">' +
                                    '<span class="exif-label">GPS:</span>' +
                                    '<span class="exif-value" data-exif="gps_coordinates">-</span>' +
                                '</div>' +
                                '<div class="exif-item software-item" style="display: none;">' +
                                    '<span class="exif-label">Software:</span>' +
                                    '<span class="exif-value" data-exif="software">-</span>' +
                                '</div>' +
                            '</div>' +
                        '</div>' +
                        '<div class="lightbox-sharing">' +
                            '<h4>Share this image</h4>' +
                            '<div class="share-buttons">' +
                                '<a href="#" class="share-btn facebook" target="_blank" rel="noopener">' +
                                    '<span class="share-text">Facebook</span>' +
                                '</a>' +
                                '<a href="#" class="share-btn twitter" target="_blank" rel="noopener">' +
                                    '<span class="share-text">Twitter</span>' +
                                '</a>' +
                                '<a href="#" class="share-btn tumblr" target="_blank" rel="noopener">' +
                                    '<span class="share-text">Tumblr</span>' +
                                '</a>' +
                                '<a href="#" class="share-btn pinterest" target="_blank" rel="noopener">' +
                                    '<span class="share-text">Pinterest</span>' +
                                '</a>' +
                                '<a href="#" class="share-btn download" download>' +
                                    '<span class="share-text">Download</span>' +
                                '</a>' +
                                '<button class="share-btn copy-link">' +
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
        $(document).on('click', '.lightbox-overlay-close', closeLightbox);
        $(document).on('click', '.nav-prev', function(e) {
            e.preventDefault();
            showPrevImage();
        });
        $(document).on('click', '.nav-next', function(e) {
            e.preventDefault();
            showNextImage();
        });

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

        // Social sharing click handlers for better UX
        $(document).on('click', '.share-btn.facebook, .share-btn.twitter, .share-btn.tumblr, .share-btn.pinterest', function(e) {
            // Allow default behavior but add some visual feedback
            var $btn = $(this);
            var originalText = $btn.find('.share-text').text();
            $btn.find('.share-text').text('Opening...');
            
            setTimeout(function() {
                $btn.find('.share-text').text(originalText);
            }, 1500);
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
            
            // Debug: Check basic lightbox state
            setTimeout(function() {
                console.log('Lightbox opened. Navigation links:', {
                    'prev exists': $('.nav-prev').length,
                    'next exists': $('.nav-next').length,
                    'total images': images.length
                });
            }, 100);
        }

        function closeLightbox() {
            $('#vibe-lightbox').removeClass('active');
            $('body').removeClass('lightbox-open');
        }

        function showPrevImage() {
            if (currentIndex > 0) {
                currentIndex = currentIndex - 1;
                showImage(currentIndex);
            }
        }

        function showNextImage() {
            if (currentIndex < images.length - 1) {
                currentIndex = currentIndex + 1;
                showImage(currentIndex);
            }
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
            
            // Load new image
            var newImg = new Image();
            newImg.onload = function() {
                $lightboxImage.attr('src', image.src).attr('alt', image.alt);
                
                // Load EXIF data and update sharing links
                loadImageMetadata(image.src, index);
                updateSharingLinks(image);
            };
            newImg.src = image.src;

            // Update navigation visibility and state
            var hasMultipleImages = images.length > 1;
            var isFirstImage = index === 0;
            var isLastImage = index === images.length - 1;
            
            if (hasMultipleImages) {
                $('.lightbox-navigation').show();
                
                // Handle previous link
                if (isFirstImage) {
                    $('.nav-prev').addClass('disabled');
                } else {
                    $('.nav-prev').removeClass('disabled');
                }
                
                // Handle next link
                if (isLastImage) {
                    $('.nav-next').addClass('disabled');
                } else {
                    $('.nav-next').removeClass('disabled');
                }
            } else {
                $('.lightbox-navigation').hide();
            }
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
                        } else {
                            $('.exif-value[data-exif="camera"]').text('EXIF data not available');
                        }
                    },
                    error: function(xhr, status, error) {
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
            if (exifData.flash) $('.exif-value[data-exif="flash"]').text(exifData.flash);
            if (exifData.white_balance) $('.exif-value[data-exif="white_balance"]').text(exifData.white_balance);
            if (exifData.exposure_mode) $('.exif-value[data-exif="exposure_mode"]').text(exifData.exposure_mode);
            if (exifData.metering_mode) $('.exif-value[data-exif="metering_mode"]').text(exifData.metering_mode);
            if (exifData.color_space) $('.exif-value[data-exif="color_space"]').text(exifData.color_space);
            if (exifData.file_size) $('.exif-value[data-exif="file_size"]').text(exifData.file_size);
            if (exifData.size) $('.exif-value[data-exif="size"]').text(exifData.size);
            
            // Show/hide optional fields based on availability
            if (exifData.gps_coordinates) {
                $('.exif-value[data-exif="gps_coordinates"]').text(exifData.gps_coordinates);
                $('.gps-item').show();
            } else {
                $('.gps-item').hide();
            }
            
            if (exifData.software) {
                $('.exif-value[data-exif="software"]').text(exifData.software);
                $('.software-item').show();
            } else {
                $('.software-item').hide();
            }
        }

        function updateSharingLinks(image) {
            var currentUrl = window.location.href;
            var imageUrl = image.src;
            var title = encodeURIComponent(image.title);
            var description = encodeURIComponent(image.description || 'Check out this photo');
            var hashtags = encodeURIComponent('photography,gallery');

            // Update sharing URLs with functional links
            // Facebook - share the current gallery page with the image
            $('.share-btn.facebook').attr('href', 'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(currentUrl) + '&quote=' + title + ' - ' + description);
            
            // Twitter - share with text, URL and hashtags
            $('.share-btn.twitter').attr('href', 'https://twitter.com/intent/tweet?text=' + title + ' - ' + description + '&url=' + encodeURIComponent(currentUrl) + '&hashtags=' + hashtags);
            
            // Tumblr - share as photo post with caption
            $('.share-btn.tumblr').attr('href', 'https://www.tumblr.com/widgets/share/tool?posttype=photo&tags=' + hashtags + '&caption=' + title + ' - ' + description + '&content=' + encodeURIComponent(imageUrl) + '&canonicalUrl=' + encodeURIComponent(currentUrl));
            
            // Pinterest - pin the image with description
            $('.share-btn.pinterest').attr('href', 'https://pinterest.com/pin/create/button/?url=' + encodeURIComponent(currentUrl) + '&media=' + encodeURIComponent(imageUrl) + '&description=' + title + ' - ' + description);
            
            // Download - direct link to full-size image
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
