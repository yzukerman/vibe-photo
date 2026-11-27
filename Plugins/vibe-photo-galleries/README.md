# Vibe Photo Galleries

## Theme and companion plugin split (submission preparation)

The repository now has two independently installable components:

- `Themes/vibe-photo-theme/`: typography, navigation, cover grid, post/archive templates, and visual refinements.
- `Plugins/vibe-photo-galleries/`: the `photo_gallery` post type, `[vibe_gallery]` shortcode, image-selection metadata, enhanced viewer and sharing, EXIF AJAX endpoint, optional Google geocoding, and its Media settings.

### Installation and compatibility

Install the theme under `wp-content/themes/vibe-photo/` and the plugin under `wp-content/plugins/vibe-photo-galleries/`. Activate the plugin separately through Plugins. The theme works without it using regular posts and WordPress Gallery blocks. With the plugin active, Photo Gallery entries also appear in the cover grid. The plugin uses WordPress jQuery and its own CSS; it does not require Foundation or this theme.

Existing `photo_gallery` entries, `_gallery_images` values, `[vibe_gallery]` content, `vibe_photo_google_api_key` setting, and viewer AJAX action names are preserved. No migration or deletion of content is performed. Reactivating the plugin restores custom gallery routes; deactivation keeps saved data. Legacy selected image IDs render through the content filter, including with other themes. The editor supports native Gallery blocks and a basic attachment-ID field. The shortcode delegates layout to WordPress's native gallery renderer.

The viewer JavaScript and base/loading CSS moved into the plugin. The theme no longer enqueues the viewer, processes gallery content, disables WordPress's native `[gallery]` shortcode, creates sample posts, or exposes its former debug/test actions. The dedicated gallery template now renders normal post content so it does not duplicate Gallery blocks. EXIF requests require the existing nonce and a URL inside the local uploads directory; traversal-style URLs are rejected. Saved image IDs are normalized and protected by permissions and a nonce.

### Distribution

The WordPress.org theme and plugin must be submitted and installed separately. Do not upload the existing historical ZIPs as submission-ready packages: they predate this split. A GitHub bundle may contain both installation ZIPs plus instructions, but WordPress installs each separately. No automatic plugin installation or dependency enforcement is included. A directory recommendation should be added only after the plugin is available on WordPress.org.

### Local development

The existing theme symlink is unchanged. The plugin is symlinked from this checkout into Local's `wp-content/plugins/vibe-photo-galleries/` and activated. Edits to either component take effect in Local. Google geocoding is optional: entering a key enables requests to Google containing photo coordinates; leave the key empty to avoid those requests.

### Scope and remaining submission work

This change separates responsibilities; it does not certify directory compliance. CDN removal in the theme, full asset/license credits, screenshot replacement, navigation/accessibility, translation cleanup, debug-log cleanup, theme fallback markup, and a full Theme Check/security review remain. The viewer retains existing behavior and needs broader accessibility and failure-path testing. No distribution ZIP has been rebuilt for this refactor.

## Modules

- `galleries.php`: registration, shortcode, legacy rendering, image-ID editor and saves.
- `enhancements.php`: existing gallery markup enhancement filters.
- `metadata.php`: EXIF endpoint and reverse geocoding.
- `settings.php`: existing Media settings.
- `assets/`: viewer JavaScript and standalone base CSS.

License: GPL-2.0-or-later.

### Verification performed for the split

- PHP syntax checks passed for the new plugin modules and changed theme templates; viewer JavaScript passed `node --check`.
- Local WordPress checks confirmed registration of `photo_gallery`, both gallery shortcodes, and legacy shortcode image rendering.
- Theme with plugin active served the moved viewer asset; theme with plugin inactive retained the cover grid and native Gallery block markup.
- Twenty Twenty-Five with the plugin active served the viewer assets independently. The original Vibe Photo theme and active plugin were restored afterward.
- These are integration smoke checks, not a full visual/accessibility review or WordPress.org certification. Google geocoding was not called and no content was deleted.

Repeat registration checks using `wp eval-file /path/to/repo/tests/gallery-smoke.php` against a WordPress installation with the plugin active and media available. Build separate installation ZIPs with `python3 tools/package.py theme` and `python3 tools/package.py plugin`; outputs use stable folder slugs, and existing archives are never overwritten. The old shell packager is retained for historical releases; prefer the new packager for the split components.

Additional checks: EXIF AJAX succeeded for a local uploaded image and rejected invalid nonces and external URLs. Separate theme and plugin ZIPs were built in a temporary directory and passed integrity checks; these are validation builds, not published releases. The plugin readme documents the optional Google service and public GPS metadata.

The companion plugin includes the complete GNU GPL v2 license text in `LICENSE`, sourced from https://www.gnu.org/licenses/old-licenses/gpl-2.0.txt. Its code is licensed GPL v2 or later.

## Google location lookup setup

1. Activate **Vibe Photo Galleries** under **WP Admin → Plugins**.
2. In your Google Cloud project, enable the **Geocoding API** and create an API key authorized to use it. Google may require billing to be enabled.
3. In WordPress, open **Settings → Media → Vibe Photo - Google Cloud Settings**.
4. Paste the key into **Google Cloud API Key**, then click **Save Changes**.

The plugin uses GPS coordinates embedded in photographs to request place names from Google's Geocoding API. It does not determine the visitor's location or add GPS coordinates to photographs. Images without GPS metadata cannot provide a location for this lookup. Google receives the photo coordinates and API key when a lookup is made; results may be cached in attachment metadata.

Leave the key empty, or remove it and save, to disable Google requests. Removing the key does not erase previously cached location names. Never commit an API key to this repository or include it in a distribution ZIP.

## Lightbox keyboard navigation and focus

The companion plugin now routes keyboard and previous/next/close controls through a single active viewer controller instead of two competing keyboard handlers tied to different image arrays.

- **Left / Right arrows:** previous / next photograph while the viewer is open; navigation stops at the first and last image.
- **Escape:** close the viewer, clear its deep-link hash, and return focus to the originating gallery control.
- **Tab / Shift+Tab:** cycle through enabled, visible viewer controls. Opening places focus on Close; background page sections are made inert until closing, with their previous inert state restored.
- Unlinked gallery images receive keyboard focus, button semantics, and Enter/Space activation. Viewer controls and gallery triggers have visible focus outlines.
- Arrow shortcuts do not intercept editable fields or modified key combinations. Browser-native fullscreen shortcuts are left to the browser; the viewer fullscreen button remains available.
- The viewer exposes dialog semantics and an accessible name. This is an incremental improvement, not a complete screen-reader/accessibility certification; broader translation, linked-image, dynamic-gallery, and cross-browser coverage still needs review.

Validation on Local's Pittsburgh Part 3 gallery (24 images): Enter opened image 1; Right moved to 2 and Left returned to 1; Space opened image 24; Right stayed at 24 and Left moved to 23. Shift+Tab wrapped from Close to the last control and Tab returned to Close. Escape closed the dialog and restored focus to the original first/last gallery image. A pointer click on Next advanced exactly one image. JavaScript syntax and Git whitespace checks passed. No release ZIP or version change was made for this update.

## Licensing confirmation for submission

Theme and companion plugin code are licensed GPL v2 or later; each component includes the complete GPL v2 text in `LICENSE`. The repository's historical root MIT license does not replace the explicit licenses of these components or their bundled third-party assets. Foundation retains MIT and Oranienbaum retains SIL OFL 1.1, as documented in the theme's `ASSETS.md`.

On 2026-11-27, Yuval Zukerman confirmed copyright ownership of all six photographs visible in the supplied screenshot and authorized distribution of the screenshot, including the photographs reproduced within it, under GPL v2 or later. Attribution: **Photographs and screenshot © 2026 Yuval Zukerman**. The original full-resolution photographs are not included or separately licensed by this grant. The owner also confirmed there are no additional uncredited third-party assets beyond the dependencies already identified. This resolves the previously noted owner-permission question; it does not certify a full independent provenance/security review.

The theme's abbreviated GPL notice was replaced with the complete license text already included in the companion plugin. Asset/source/license documentation and the screenshot checksum were updated. Temporary package checks verified both components contain full license texts and the theme contains the screenshot and resource inventory. No runtime behavior, license of Foundation/Oranienbaum, version number, published distribution, or remote branch was changed.

## Translation and security review fixes

### Metadata endpoint and settings

The public EXIF endpoint now validates scalar nonce/URL inputs, resolves exact upload-relative Media Library filenames (including resized/scaled variants), and requires an image attachment. Arbitrary file fallback and partial-basename database searches were removed. Real filesystem paths must remain within the real uploads directory, preventing symlink escape. Unauthenticated requests cannot retrieve metadata for private, unpublished, or password-protected parent content. Password-protected attachments and trashed media are rejected. Responses no longer contain debug arrays, absolute file paths, or EXIF diagnostic keys; routine server-path logging was removed.

These checks intentionally support local Media Library images only. Offloaded media, external image hosts, and files not registered as attachments are not supported by this endpoint. Public unattached image metadata remains available by design. GPS can still be shown for public images; the plugin documentation explains that disclosure. The nonce is an anti-CSRF/request check, not a substitute for attachment visibility checks.

Google lookups now validate coordinate ranges, encode the API key in the fixed Google endpoint URL, bound response size, and reject non-200 responses. The key field is masked and disables autocomplete; a scalar-only settings sanitizer avoids array-input errors. Gallery ID-save inputs likewise reject malformed arrays before normalization. These changes do not add a geocoding billing quota/rate limit; API key restrictions and billing controls remain the site owner's responsibility.

### Translation and template output

Added normal theme/plugin text-domain loading hooks and generated `languages/vibe-photo.pot` and `languages/vibe-photo-galleries.pot` catalogs. The plugin maps viewer labels, accessibility labels, loading/status messages, image counters, fullscreen text, and fallback names through literal PHP gettext messages and passes the translated map to JavaScript. Static viewer translations are applied as text/attributes, not injected as HTML; user image titles and captions remain user content. EXIF descriptive lookup values now use gettext. No finished non-English translations are supplied.

Replaced the regular post's duplicated/manual inline JavaScript navigation with translated core post-navigation links, removed its debug output, escaped the archive admin URL and image counts, and removed a stray gallery-archive markup character. Placeholder messages include translator context. Other legacy/unused scripts and less common editor/content paths still need broader review before claiming full translation coverage.

### Validation and limits

All theme/plugin PHP syntax and viewer JavaScript syntax checks passed. Catalog generation completed without placeholder warnings. Public AJAX integration checks verified valid local metadata without debug/path leakage; rejection of invalid nonces, array inputs, external/traversal/missing URLs; and rejection for private/password-protected parents. Temporary access-test posts were removed and the attachment's original parent restored. `tests/security-smoke.php` provides repeatable non-mutating WP-CLI checks for path confinement, API key sanitization, and translated viewer labels.

Live browser checks confirmed gallery opening, one-image arrow navigation, EXIF display, Escape closing, and restored image focus. Theme Check against `vibe-photo` still has no REQUIRED failures or WARNING entries; optional block-pattern/style, header/background, and widget recommendations remain. This targeted source review is not a penetration test, a complete privacy review, or a guarantee of directory approval. No deliberate live Google API integration test or non-English visual test was performed. No version bump, published ZIP, production deployment, or remote push was made.

## EXIF image-format limitations

**Use JPEG (`.jpg` or `.jpeg`) photographs for EXIF camera settings and GPS/location display.** PNG, WebP, GIF, AVIF, and other formats are not read for EXIF by this plugin. Those images can still display in galleries and the viewer when supported by WordPress and the browser, but camera/GPS fields will be unavailable. WordPress titles, captions, and alt text do not depend on EXIF and may still be displayed.

The current reader also allows TIFF (`.tif` / `.tiff`) through PHP's EXIF extension, but TIFF upload and browser display support vary; JPEG is the recommended portfolio format. The server must have PHP's EXIF extension enabled, and the uploaded file must retain its metadata. Converting to another format or stripping metadata can remove camera settings and GPS information. Google geocoding cannot restore missing GPS coordinates.
