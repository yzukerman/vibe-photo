/**
 * Enhanced Lightbox functionality for Vibe Photo Theme
 * Features: Navigation, Social Sharing, EXIF Data, Image Details
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        var currentIndex = 0;
        var images = [];

        // Initialize lightbox for gallery images and WordPress Gallery blocks
        console.log('Lightbox: Checking for galleries...');
        console.log('Custom galleries found:', $('.masonry-gallery').length);
        console.log('Gallery links found:', $('.gallery-link').length);
        console.log('WordPress galleries found:', $('.vibe-lightbox-gallery').length);
        console.log('Lightbox images found:', $('.lightbox-image').length);
        console.log('All wp-block-gallery elements:', $('.wp-block-gallery').length);
        console.log('All images in wp-block-gallery:', $('.wp-block-gallery img').length);
        
        // Debug: Log what we actually find
        $('.wp-block-gallery').each(function(i) {
            console.log('Gallery block ' + i + ':', $(this).attr('class'));
            $(this).find('img').each(function(j) {
                console.log('  Image ' + j + ':', $(this).attr('src'), 'has lightbox class:', $(this).hasClass('lightbox-image'));
            });
        });
        
        function rebuildImageArrayAndOpen($clickedImg) {
            console.log('Rebuilding image array for clicked image...');
            
            // Clear current images array
            images = [];
            
            // Find all lightbox images in the same gallery
            var $gallery = $clickedImg.closest('.wp-block-gallery, .vibe-lightbox-gallery');
            var $galleryImages = $gallery.find('img');
            var clickedIndex = 0;
            
            $galleryImages.each(function(index) {
                var $img = $(this);
                var imgSrc = $img.attr('src');
                var fullSrc = $img.attr('data-full-src') || imgSrc;
                
                // Clean up protocol-relative URLs first
                if (imgSrc && imgSrc.indexOf('//') === 0) {
                    imgSrc = 'http:' + imgSrc;
                }
                if (fullSrc && fullSrc.indexOf('//') === 0) {
                    fullSrc = 'http:' + fullSrc;
                }
                
                if (imgSrc) {
                    // Try to get full size URL if we don't already have it
                    if (!$img.attr('data-full-src')) {
                        var sizeMatch = imgSrc.match(/-(\d+)x(\d+)\.(jpg|jpeg|png|gif|webp)$/i);
                        if (sizeMatch) {
                            fullSrc = imgSrc.replace(/-\d+x\d+\.([^.]+)$/i, '.$1');
                        } else {
                            fullSrc = imgSrc; // Use the same URL if no size pattern found
                        }
                    }
                    
                    console.log('Processing image:', imgSrc, '-> full:', fullSrc);
                    
                    images.push({
                        src: fullSrc,
                        title: $img.attr('alt') || $img.attr('title') || 'Gallery Image ' + (index + 1),
                        description: $img.attr('data-caption') || '',
                        thumb: imgSrc,
                        alt: $img.attr('alt') || '',
                        type: 'wp-gallery'
                    });
                    
                    // Check if this is the clicked image
                    if ($img[0] === $clickedImg[0]) {
                        clickedIndex = index;
                    }
                }
            });
            
            console.log('Rebuilt image array with', images.length, 'images. Clicked index:', clickedIndex);
            
            // Set current index and open lightbox
            currentIndex = clickedIndex;
            openLightbox();
        }
        
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

            console.log('Showing image:', image);
            console.log('Image src:', image.src);
            console.log('Image thumb:', image.thumb);

            // Update title and description
            $lightboxTitle.text(image.title);
            $lightboxDescription.text(image.description);
            $imageCounter.text((index + 1) + ' of ' + images.length);
            
            // Load new image
            var newImg = new Image();
            newImg.onload = function() {
                console.log('Image loaded successfully:', image.src);
                $lightboxImage.attr('src', image.src).attr('alt', image.alt);
                
                // Optimize image sizing based on aspect ratio
                optimizeLightboxImageSize($lightboxImage[0], newImg);
                
                // Load EXIF data and update sharing links
                loadImageMetadata(image.src, index);
                updateSharingLinks(image);
            };
            newImg.onerror = function() {
                console.log('Failed to load image:', image.src);
                console.log('Trying thumbnail instead:', image.thumb);
                // Fallback to thumbnail if full size fails
                $lightboxImage.attr('src', image.thumb).attr('alt', image.alt);
                
                // Still try to load metadata for the thumbnail
                loadImageMetadata(image.thumb, index);
                updateSharingLinks(image);
            };
            newImg.src = image.src;

            // Update navigation visibility and state
            var hasMultipleImages = images.length > 1;
            var isFirstImage = index === 0;
            var isLastImage = index === images.length - 1;
            
            if (hasMultipleImages) {
                $('.lightbox-navigation-bar').show();
                $('.nav-prev, .nav-next').show();
                
                // Handle previous button
                if (isFirstImage) {
                    $('.nav-prev').addClass('disabled').prop('disabled', true);
                } else {
                    $('.nav-prev').removeClass('disabled').prop('disabled', false);
                }
                
                // Handle next button
                if (isLastImage) {
                    $('.nav-next').addClass('disabled').prop('disabled', true);
                } else {
                    $('.nav-next').removeClass('disabled').prop('disabled', false);
                }
            } else {
                $('.lightbox-navigation-bar').show(); // Still show for info buttons
                $('.nav-prev, .nav-next').hide(); // But hide navigation buttons
            }
        }
        
        function loadImageMetadata(imageSrc, imageIndex) {
            // Reset EXIF data
            $('.exif-value').text('-');
            
            // Hide optional fields by default
            $('.title-item, .caption-item, .alt-text-item, .gps-item, .software-item').hide();
            
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
                            var exifData = response.data;
                            
                            // Debug: Log the response data
                            console.log('EXIF Data received:', exifData);
                            
                            // Update each EXIF field if it exists
                            Object.keys(exifData).forEach(function(key) {
                                var $field = $('[data-exif="' + key + '"]');
                                if ($field.length && exifData[key] && exifData[key] !== '-' && exifData[key] !== '') {
                                    $field.text(exifData[key]);
                                    
                                    // Always show fields that have data
                                    $field.closest('.exif-item').show();
                                    
                                    // Debug: Log what fields are being shown
                                    console.log('Showing field:', key, 'with value:', exifData[key]);
                                }
                            });
                        } else {
                            console.log('No data in response:', response);
                        }
                    },
                    error: function() {
                        console.log('Could not load EXIF data for image:', imageSrc);
                    }
                });
            }
            
            // Hide GPS and software fields by default (will be shown if data exists)
            $('.gps-item, .software-item').hide();
            
            // Show software field if we detect it has a value
            if ($('[data-exif="software"]').text() && $('[data-exif="software"]').text() !== '-') {
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
        
        if ($('.masonry-gallery').length || $('.gallery-link').length || $('.vibe-lightbox-gallery').length || $('.lightbox-image').length) {
            console.log('Lightbox: Creating lightbox...');
            createLightbox();
            bindLightboxEvents();
        } else {
            console.log('Lightbox: No galleries found, checking for wp-block-gallery...');
            if ($('.wp-block-gallery').length) {
                console.log('Found wp-block-gallery elements:', $('.wp-block-gallery').length);
                
                // Enhanced fallback: properly enhance WordPress gallery images
                $('.wp-block-gallery img').each(function() {
                    var $img = $(this);
                    var imgSrc = $img.attr('src');
                    
                    console.log('Processing gallery image:', imgSrc);
                    
                    if (!$img.hasClass('lightbox-image') && imgSrc) {
                        // Clean up protocol-relative URLs
                        if (imgSrc.indexOf('//') === 0) {
                            imgSrc = 'http:' + imgSrc;
                        }
                        
                        // Try to get full size URL
                        var fullSrc = imgSrc;
                        var sizeMatch = imgSrc.match(/-(\d+)x(\d+)\.(jpg|jpeg|png|gif|webp)$/i);
                        if (sizeMatch) {
                            fullSrc = imgSrc.replace(/-\d+x\d+\.([^.]+)$/i, '.$1');
                        }
                        
                        console.log('Adding lightbox to image - thumb:', imgSrc, 'full:', fullSrc);
                        
                        $img.addClass('lightbox-image');
                        $img.attr('data-lightbox', 'gallery');
                        $img.attr('data-full-src', fullSrc);
                        $img.attr('data-src', imgSrc);
                        
                        // Add to parent gallery
                        $img.closest('.wp-block-gallery').addClass('vibe-lightbox-gallery');
                    }
                });
                
                // Try again after enhancement
                if ($('.lightbox-image').length > 0) {
                    console.log('Lightbox: Enhanced images found, creating lightbox...');
                    createLightbox();
                    bindLightboxEvents();
                } else {
                    console.log('Lightbox: No images could be enhanced');
                }
            } else {
                console.log('Lightbox: No wp-block-gallery elements found');
            }
        }
        
        // Fallback: Add click handlers directly to any wp-block-gallery images
        // This works even if our PHP enhancement didn't work
        $('.wp-block-gallery img').on('click', function(e) {
            console.log('Direct gallery image clicked:', $(this).attr('src'));
            e.preventDefault();
            e.stopPropagation();
            
            // Create a simple lightbox for this image
            var $img = $(this);
            var imgSrc = $img.attr('src');
            
            if (imgSrc) {
                // Clean up protocol-relative URLs
                if (imgSrc.indexOf('//') === 0) {
                    imgSrc = 'http:' + imgSrc;
                }
                
                // Try to get full size URL
                var fullSrc = imgSrc;
                var sizeMatch = imgSrc.match(/-(\d+)x(\d+)\.(jpg|jpeg|png|gif|webp)$/i);
                if (sizeMatch) {
                    fullSrc = imgSrc.replace(/-\d+x\d+\.([^.]+)$/i, '.$1');
                }
                
                // Add lightbox attributes if not already present
                if (!$img.hasClass('lightbox-image')) {
                    $img.addClass('lightbox-image');
                    $img.attr('data-lightbox', 'gallery');
                    $img.attr('data-full-src', fullSrc);
                    $img.attr('data-src', imgSrc);
                    $img.closest('.wp-block-gallery').addClass('vibe-lightbox-gallery');
                }
                
                // Ensure lightbox exists
                if ($('#vibe-lightbox').length === 0) {
                    createLightbox();
                }
                
                // Rebuild image array and open lightbox
                rebuildImageArrayAndOpen($img);
            }
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
                '<div class="lightbox-main">' +
                    '<div class="lightbox-content">' +
                        '<div class="lightbox-image-container">' +
                            '<img src="" alt="" class="lightbox-image">' +
                        '</div>' +
                    '</div>' +
                '</div>' +
                '<!-- Navigation Bar (always visible at bottom) -->' +
                '<div class="lightbox-navigation-bar">' +
                    '<div class="nav-controls">' +
                        '<button class="nav-btn nav-prev" aria-label="Previous image">‹ Previous</button>' +
                        '<span class="image-counter"></span>' +
                        '<button class="nav-btn nav-next" aria-label="Next image">Next ›</button>' +
                    '</div>' +
                    '<div class="info-controls">' +
                        '<button class="info-btn" data-target="exif" aria-label="Show image data">Image Data</button>' +
                        '<button class="info-btn" data-target="share" aria-label="Show sharing options">Share</button>' +
                        '<button class="fullscreen-btn" aria-label="Toggle fullscreen">⛶ Fullscreen</button>' +
                    '</div>' +
                '</div>' +
                '<!-- Collapsible EXIF Container -->' +
                '<div class="lightbox-info-container exif-container" style="display: none;">' +
                    '<div class="info-header">' +
                        '<h4>Image Data</h4>' +
                        '<button class="close-info-btn" data-target="exif" aria-label="Close image data">&times;</button>' +
                    '</div>' +
                    '<div class="exif-grid">' +
                        '<div class="exif-item title-item" style="display: none;">' +
                            '<span class="exif-label">Title:</span>' +
                            '<span class="exif-value" data-exif="title">-</span>' +
                        '</div>' +
                        '<div class="exif-item caption-item" style="display: none;">' +
                            '<span class="exif-label">Caption:</span>' +
                            '<span class="exif-value" data-exif="caption">-</span>' +
                        '</div>' +
                        '<div class="exif-item alt-text-item" style="display: none;">' +
                            '<span class="exif-label">Alt Text:</span>' +
                            '<span class="exif-value" data-exif="alt_text">-</span>' +
                        '</div>' +
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
                '<!-- Collapsible Share Container -->' +
                '<div class="lightbox-info-container share-container" style="display: none;">' +
                    '<div class="info-header">' +
                        '<h4>Share this image</h4>' +
                        '<button class="close-info-btn" data-target="share" aria-label="Close sharing options">&times;</button>' +
                    '</div>' +
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
        '</div>';
        
        $('head').append(styles);
        $('body').append(lightboxHTML);
    }

    function bindLightboxEvents() {
        var currentIndex = 0;
        var images = [];

        console.log('Lightbox: Binding events...');

        // Collect all gallery images from custom galleries
        $('.gallery-link').each(function(index) {
            var $link = $(this);
            var $img = $link.find('img');
            var $parent = $link.closest('.photo-item');
            
            images.push({
                src: $link.attr('href'),
                title: $img.attr('alt') || $link.attr('title') || 'Untitled',
                description: $link.attr('data-caption') || $parent.find('.image-caption p').text() || '',
                thumb: $img.attr('src'),
                alt: $img.attr('alt') || '',
                type: 'custom-gallery'
            });

            // Prevent default link behavior and open lightbox
            $link.on('click', function(e) {
                e.preventDefault();
                currentIndex = index;
                openLightbox();
            });
        });

        console.log('Custom gallery images found:', $('.gallery-link').length);

        // Collect images from WordPress Gallery blocks and enhanced galleries
        $('.lightbox-image').each(function(index) {
            var $img = $(this);
            var galleryIndex = images.length + index; // Continue numbering from custom gallery images
            var imgSrc = $img.attr('src');
            var fullSrc = $img.attr('data-full-src') || imgSrc;
            
            console.log('Found lightbox image:', imgSrc);
            console.log('Full size src:', fullSrc);
            
            // Only add images that have a valid src
            if (imgSrc && imgSrc.length > 0) {
                images.push({
                    src: fullSrc,
                    title: $img.attr('alt') || $img.attr('title') || 'Gallery Image',
                    description: $img.attr('data-caption') || '',
                    thumb: imgSrc,
                    alt: $img.attr('alt') || '',
                    type: 'wp-gallery'
                });

                // Add click handler for WordPress Gallery images
                $img.on('click', function(e) {
                    console.log('Image clicked:', imgSrc);
                    e.preventDefault();
                    e.stopPropagation();
                    currentIndex = galleryIndex;
                    openLightbox();
                });
            } else {
                console.log('Skipping image with empty src');
            }
        });

        console.log('Total images in lightbox:', images.length);

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

        // New nimble lightbox event handlers
        $(document).on('click', '.info-btn', function(e) {
            e.preventDefault();
            var target = $(this).data('target');
            var $container = $('.' + target + '-container');
            var $btn = $(this);
            
            // Toggle container visibility
            if ($container.is(':visible')) {
                hideInfoContainer(target);
                $btn.removeClass('active');
            } else {
                // Hide other containers first
                $('.lightbox-info-container').hide();
                $('.info-btn').removeClass('active');
                
                // Show selected container
                showInfoContainer(target);
                $btn.addClass('active');
            }
        });

        $(document).on('click', '.close-info-btn', function(e) {
            e.preventDefault();
            var target = $(this).data('target');
            hideInfoContainer(target);
            $('.info-btn[data-target="' + target + '"]').removeClass('active');
        });

        // Fullscreen functionality
        $(document).on('click', '.fullscreen-btn', function(e) {
            e.preventDefault();
            toggleFullscreen();
        });

        function toggleFullscreen() {
            var lightboxElement = document.getElementById('vibe-lightbox');
            var $fullscreenBtn = $('.fullscreen-btn');
            
            if (!document.fullscreenElement && !document.webkitFullscreenElement && 
                !document.mozFullScreenElement && !document.msFullscreenElement) {
                // Enter fullscreen
                if (lightboxElement.requestFullscreen) {
                    lightboxElement.requestFullscreen();
                } else if (lightboxElement.webkitRequestFullscreen) {
                    lightboxElement.webkitRequestFullscreen();
                } else if (lightboxElement.mozRequestFullScreen) {
                    lightboxElement.mozRequestFullScreen();
                } else if (lightboxElement.msRequestFullscreen) {
                    lightboxElement.msRequestFullscreen();
                }
                $fullscreenBtn.addClass('active').html('⛶ Exit Fullscreen');
            } else {
                // Exit fullscreen
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                } else if (document.webkitExitFullscreen) {
                    document.webkitExitFullscreen();
                } else if (document.mozCancelFullScreen) {
                    document.mozCancelFullScreen();
                } else if (document.msExitFullscreen) {
                    document.msExitFullscreen();
                }
                $fullscreenBtn.removeClass('active').html('⛶ Fullscreen');
            }
        }

        // Listen for fullscreen change events
        $(document).on('fullscreenchange webkitfullscreenchange mozfullscreenchange MSFullscreenChange', function() {
            var $fullscreenBtn = $('.fullscreen-btn');
            
            if (!document.fullscreenElement && !document.webkitFullscreenElement && 
                !document.mozFullScreenElement && !document.msFullscreenElement) {
                // Exited fullscreen
                $fullscreenBtn.removeClass('active').html('⛶ Fullscreen');
            } else {
                // Entered fullscreen
                $fullscreenBtn.addClass('active').html('⛶ Exit Fullscreen');
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
                    case 122: // F11 for fullscreen toggle
                        e.preventDefault();
                        toggleFullscreen();
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
                $('.lightbox-navigation-bar').show();
                $('.nav-prev, .nav-next').show();
                
                // Handle previous button
                if (isFirstImage) {
                    $('.nav-prev').addClass('disabled').prop('disabled', true);
                } else {
                    $('.nav-prev').removeClass('disabled').prop('disabled', false);
                }
                
                // Handle next button
                if (isLastImage) {
                    $('.nav-next').addClass('disabled').prop('disabled', true);
                } else {
                    $('.nav-next').removeClass('disabled').prop('disabled', false);
                }
            } else {
                $('.lightbox-navigation-bar').show(); // Still show for info buttons
                $('.nav-prev, .nav-next').hide(); // But hide navigation buttons
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
        
        function rebuildImageArrayAndOpen($clickedImg) {
            console.log('Rebuilding image array for clicked image...');
            
            // Clear current images array
            images = [];
            
            // Find all lightbox images in the same gallery
            var $gallery = $clickedImg.closest('.wp-block-gallery, .vibe-lightbox-gallery');
            var $galleryImages = $gallery.find('img');
            var clickedIndex = 0;
            
            $galleryImages.each(function(index) {
                var $img = $(this);
                var imgSrc = $img.attr('src');
                var fullSrc = $img.attr('data-full-src') || imgSrc;
                
                // Clean up protocol-relative URLs
                if (imgSrc && imgSrc.indexOf('//') === 0) {
                    imgSrc = 'http:' + imgSrc;
                    fullSrc = 'http:' + fullSrc;
                }
                
                if (imgSrc) {
                    images.push({
                        src: fullSrc,
                        title: $img.attr('alt') || $img.attr('title') || 'Gallery Image ' + (index + 1),
                        description: $img.attr('data-caption') || '',
                        thumb: imgSrc,
                        alt: $img.attr('alt') || '',
                        type: 'wp-gallery'
                    });
                    
                    // Check if this is the clicked image
                    if ($img[0] === $clickedImg[0]) {
                        clickedIndex = index;
                    }
                }
            });
            
            console.log('Rebuilt image array with', images.length, 'images. Clicked index:', clickedIndex);
            
            // Set current index and open lightbox
            currentIndex = clickedIndex;
            openLightbox();
        }
    }

    // Global function for optimizing lightbox image sizing
    function optimizeLightboxImageSize(imgElement, loadedImg) {
        if (!imgElement || !loadedImg) return;
        
        // Get viewport dimensions
        var viewportWidth = window.innerWidth;
        var viewportHeight = window.innerHeight;
        
        // Get image natural dimensions
        var imgWidth = loadedImg.naturalWidth || loadedImg.width;
        var imgHeight = loadedImg.naturalHeight || loadedImg.height;
        
        // Calculate aspect ratio
        var aspectRatio = imgWidth / imgHeight;
        
        // Reserve space for navigation bar (60px) and padding (40px total)
        var availableWidth = viewportWidth - 40;
        var availableHeight = viewportHeight - 100; // 60px nav + 40px padding
        
        // Calculate optimal dimensions to perfectly fit viewport
        var targetWidth, targetHeight;
        
        // Calculate both possible sizing approaches
        var widthConstrained = {
            width: availableWidth,
            height: availableWidth / aspectRatio
        };
        
        var heightConstrained = {
            width: availableHeight * aspectRatio,
            height: availableHeight
        };
        
        // Choose the approach that fits both dimensions
        if (widthConstrained.height <= availableHeight) {
            // Width-constrained approach fits
            targetWidth = widthConstrained.width;
            targetHeight = widthConstrained.height;
        } else {
            // Height-constrained approach
            targetWidth = heightConstrained.width;
            targetHeight = heightConstrained.height;
        }
        
        // Apply the calculated dimensions
        $(imgElement).css({
            'width': targetWidth + 'px',
            'height': targetHeight + 'px',
            'max-width': targetWidth + 'px',
            'max-height': targetHeight + 'px',
            'object-fit': 'contain'
        });
        
        console.log('Optimized image size:', {
            original: imgWidth + 'x' + imgHeight,
            target: Math.round(targetWidth) + 'x' + Math.round(targetHeight),
            aspectRatio: aspectRatio.toFixed(2),
            viewport: viewportWidth + 'x' + viewportHeight,
            available: availableWidth + 'x' + availableHeight
        });
    }

    // Helper functions for info containers
    function showInfoContainer(target) {
        $('.' + target + '-container').show();
    }

    function hideInfoContainer(target) {
        $('.' + target + '-container').hide();
    }

    // Add window resize handler to re-optimize image size when viewport changes
    $(window).on('resize', function() {
        var $lightboxImage = $('.lightbox-image');
        if ($lightboxImage.length && $('#lightbox-modal').is(':visible')) {
            var imgElement = $lightboxImage[0];
            if (imgElement.naturalWidth) {
                optimizeLightboxImageSize(imgElement, imgElement);
            }
        }
    });

})(jQuery);
