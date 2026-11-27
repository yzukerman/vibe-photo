=== Vibe Photo Galleries ===
Contributors: yzukerman
Tags: gallery, photography, exif
Requires at least: 6.7.1
Requires PHP: 8.2
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Photo galleries with an enhanced image viewer, EXIF details, and optional location lookup.

== Description ==
Adds Photo Gallery posts, the [vibe_gallery] shortcode, and an enhanced viewer for WordPress galleries. The Vibe Photo theme is optional. Existing photo_gallery entries, _gallery_images selections, and the vibe_photo_google_api_key setting are preserved.

Optional external service: supplying a Google Cloud API key under Settings > Media enables reverse geocoding. Image GPS coordinates and your API key are sent to Google's Geocoding API when location lookup is needed. Leave the key empty to disable these requests. Google terms: https://cloud.google.com/maps-platform/terms ; privacy: https://policies.google.com/privacy . Image metadata, including GPS, is visible to site visitors through the viewer; use images without GPS if locations should remain private.

== Installation ==
1. Upload vibe-photo-galleries to wp-content/plugins, or install the plugin ZIP through Plugins > Add New.
2. Activate Vibe Photo Galleries.
3. Use WordPress Gallery blocks, Photo Gallery posts, or [vibe_gallery ids="1,2,3"].
4. Optionally configure a Google API key in Settings > Media.

== Frequently Asked Questions ==
= Is the Vibe Photo theme required? =
No. The plugin includes viewer styling and uses WordPress jQuery. The theme provides additional presentation.

= Does deactivation delete galleries? =
No. Saved content and settings remain. Reactivate to restore custom gallery routes and shortcode rendering.

= Which image formats support EXIF? =
Use JPEG (.jpg/.jpeg) for camera settings and GPS metadata. PNG, WebP, GIF, AVIF, and other formats are not read for EXIF, although gallery display and WordPress titles/captions can still work. The reader also permits TIFF, but browser/upload compatibility varies. PHP EXIF support and retained source metadata are required. Google location lookup requires embedded GPS coordinates.

== Changelog ==
= 1.0.0 =
* Extract gallery and viewer functionality from the Vibe Photo theme.
* Preserve legacy identifiers and settings.
* Keep native WordPress galleries enabled.
* Add local image URL checks and a protected image-ID editor.
