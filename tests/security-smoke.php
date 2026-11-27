<?php
/** Run with wp eval-file /path/to/repo/tests/security-smoke.php and plugin active. */
if (vibe_photo_galleries_safe_file(__FILE__) !== false || vibe_photo_galleries_safe_file(array()) !== false) {
    throw new RuntimeException('Non-upload paths must be rejected');
}
if (vibe_photo_galleries_sanitize_api_key(array('invalid')) !== '' || vibe_photo_galleries_sanitize_api_key('<b>key</b>') !== 'key') {
    throw new RuntimeException('API key sanitization failed');
}
$translation_filter = function($translated, $text, $domain) {
    return $domain === 'vibe-photo-galleries' && $text === 'Close lightbox' ? 'Translated close label' : $translated;
};
add_filter('gettext', $translation_filter, 10, 3);
try {
    $strings = vibe_photo_galleries_viewer_strings();
    if ($strings['Close lightbox'] !== 'Translated close label') throw new RuntimeException('Viewer gettext mapping failed');
} finally {
    remove_filter('gettext', $translation_filter, 10);
}
echo "Path confinement, key sanitization, and viewer translation checks passed.\n";
