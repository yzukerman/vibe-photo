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

## Submission 1.3 compatibility baseline

Submission version: **1.3**. The declared minimum is now **WordPress 6.7.1 / PHP 8.2**, replacing unverified WordPress 5.0 / PHP 7.4 claims. `Tested up to` is conservatively set to **6.7** for this isolated clean-install result; the development site also runs a newer WordPress version, but a complete current-release matrix remains pending. WordPress 6.7.1 was the January 2025-era baseline (released November 21, 2024). This is compatibility testing, not a recommendation to run an unpatched historical release in production.

On 2026-11-27 an isolated WordPress **6.7.1** installation was created from the official archive using Local's bundled **PHP 8.2.29** binary and running MySQL service, with a separate database and files under this chat's `work/compatibility/`. The test theme and plugin were installed from generated ZIPs, not development symlinks. This harness is not registered as a separate site in Local's UI. The original Local site was not downgraded or switched. WordPress debugging/logging was enabled and automatic cron disabled in the test installation.

Passed automated checks: clean installation and theme activation; theme-alone gallery post, first-image cover fallback, search/empty search, comments form, logo/menu output and editor-support registration; plugin activation; gallery/cover/security smoke checks; populated/paginated/empty fallback templates; metadata AJAX; and Theme Check with no REQUIRED/WARNING results. Test packages passed ZIP integrity checks. No WordPress debug log was created during these checks. Browser checks confirmed viewer opening, metadata for the test image, and ordinary Escape closing with focus restoration.

A fullscreen-close interaction still needs investigation: after entering fullscreen and closing the viewer, the browser's accessibility snapshot did not return to the page until reload. Ordinary non-fullscreen closing passed. This is a pending viewer defect, not a proven WordPress-version-specific incompatibility. The test image has no camera EXIF, so this run tested title/dimensions/file size rather than live camera/GPS/geocoding results. The full Theme Unit Test import, real editor visual checks, additional PHP versions, newer clean WordPress installs, and broad browser/device testing remain pending. PHP 8.3/8.4 compatibility is not claimed by these results.

Official baseline sources: https://wordpress.org/documentation/wordpress-version/version-6-7-1/ and https://make.wordpress.org/hosting/2024/11/05/wordpress-6-7-server-compatibility/ . Temporary packages in the test harness are validation artifacts, not final published distributions; regenerate final packages after remaining fixes. The old `1.20.x` deployment ZIPs remain unchanged. Since `1.3` sorts below `1.20.x`, existing manually installed deployments should not rely on an automatic version-based upgrade to this directory-submission release.

## Fullscreen viewer close correction

Investigation confirmed the close handlers hid the lightbox and restored focus without first exiting browser fullscreen. The browser could remain fullscreen on the hidden overlay, preventing the surrounding page from becoming accessible until reload. This was viewer lifecycle behavior, not a WordPress 6.7.1-specific failure.

All viewer close actions now use a shared asynchronous close request: wait for any pending fullscreen-entry request, exit fullscreen if this viewer owns it, then close the overlay and restore the original image focus/background state. Repeated close requests are ignored while closing. If fullscreen exit fails, the overlay stays visible so the user can retry or use the browser's exit control. Unrelated fullscreen elements are not exited. Fullscreen button labels track actual change events and settled API requests rather than optimistic click state; rejected requests are caught instead of leaving unhandled rejections. Ordinary non-fullscreen close still works. Browser-native Escape handling can vary: some browsers first leave fullscreen before delivering a later Escape to the viewer.

Validation: live in-app browser checks on the main Local site confirmed entering fullscreen then using Close or Escape restores the page accessibility tree and the original gallery image focus. The isolated WordPress 6.7.1 / PHP 8.2.29 test plugin was refreshed from a validation ZIP and passed fullscreen Close with the same restored page/focus result. This supersedes the pending fullscreen-close defect noted in the compatibility baseline. JavaScript syntax and whitespace checks passed.

Run `node --test tests/fullscreen-close.test.cjs` for regression coverage of delayed exit, repeated close requests, rejected exit with retry, close during pending entry, and unrelated fullscreen ownership. All four tests passed. These use mocked browser fullscreen promises plus actual viewer lifecycle code; native browser behavior was checked separately. Legacy prefixed fullscreen implementations and all mobile/browser combinations are not certified by these tests. No final release ZIP, version bump, remote push, or production deployment was made.

## Final package preparation and gallery-data boundary

The theme no longer reads `_gallery_images` or parses `[vibe_gallery]` storage itself. It handles Featured images and native WordPress galleries, then exposes `vibe_photo_collection_image_ids`, `vibe_photo_collection_image_count`, and `vibe_photo_collection_query_extra_where` presentation filters. The companion plugin supplies its legacy metadata/shortcode cover candidates, counts, and cover-grid eligibility through those filters. Custom post type templates may remain in the theme; registration and storage stay in the plugin. Without the plugin, native Gallery block covers remain available.

Final validation packages are `dist/themes/vibe-photo-v1.3.zip` and `dist/plugins/vibe-photo-galleries-v1.0.0.zip`, each with one stable installation folder. Historical deployment ZIPs are retained separately and should not be used for directory submission. Install the theme and plugin separately. The theme can be submitted on its own; do not recommend automatic installation or claim a WordPress.org plugin listing before the companion plugin is approved there.

Release checks use the exact ZIPs in the isolated WordPress 6.7.1 / PHP 8.2.29 harness, with clean component directories and separate test data. The repository's cover, gallery, security, template, and fullscreen lifecycle tests are the repeatable checks; Theme Check must have no REQUIRED or WARNING entries. A release validation report accompanies the downloadable artifacts. Test success is not a guarantee of directory acceptance or a full browser/device/security certification. No WordPress.org submission or production deployment is authorized by this preparation step.

### Final 1.3 ZIP validation results

The exact theme/plugin release ZIPs were extracted into clean component directories on the isolated WordPress 6.7.1 / Local PHP 8.2.29 installation. Passed: theme-alone cover/homepage behavior, gallery/cover/security smoke checks, populated/paginated/empty fallback templates, plugin-provided legacy covers, gallery archive data-provider behavior, and public metadata AJAX. Temporary legacy fixture content was removed. Theme Check returned no REQUIRED or WARNING messages, and the WordPress debug log remained empty. Four fullscreen lifecycle regression tests also passed. Documentation-only packaging updates were followed by ZIP integrity checks. Production and WordPress.org deployment were not performed.

## Plugin submission scanner corrections — 1.0.1

The first directory upload was rejected for a missing `Tested up to` readme header and eight unescaped translated strings in the Media settings output. Version 1.0.1 adds `Tested up to: 7.1`, after verifying on the existing Local WordPress 7.1.5 installation, while retaining the WordPress 6.7.1 / PHP 8.2 minimum. All translated settings output now uses `esc_html__()`; the Google Console link has fixed markup, an escaped URL, and a separate translated/escaped label. No scan suppression was added.

Local Plugin Check 2.2.0 additionally identified an unprefixed module variable, discouraged explicit plugin text-domain loading, and direct metadata-query concerns. The loader now prefixes its variable and declares `Domain Path: /languages` for WordPress's on-demand translation loading. Exact local attachment URL lookup now uses core `attachment_url_to_postid()` instead of a direct database query, retaining the visibility and realpath checks.

Validation: Plugin Check's default CLI run on the isolated WordPress 6.7.1 installation returned “Checks complete. No errors found.” Metadata integration tests on the current Local site still passed for valid media, rejected malformed/nonce/external/traversal/missing requests, and protected-parent access; gallery/security smoke tests passed on WordPress 7.1.5. Both versions are documented as exercised configurations, not a complete version matrix. Changed PHP syntax, catalog generation, and ZIP integrity checks passed. `dist/plugins/vibe-photo-galleries-v1.0.1.zip` replaces 1.0.0 for the next plugin upload; the historical ZIP remains unchanged. Passing local checks does not guarantee directory approval, and no automatic re-submission was performed.


### Separate distribution directories (November 27, 2026)

Installable theme ZIPs, including historical releases, now live in `dist/themes/`; gallery plugin ZIPs live in `dist/plugins/`. Current packages are `dist/themes/vibe-photo-v1.3.zip` and `dist/plugins/vibe-photo-galleries-v1.0.1.zip`. Archive contents and release versions are unchanged by this move.

Run `python3 tools/package.py theme` or `python3 tools/package.py plugin` from the repository root to build into the respective directory. `--output` still accepts an explicit destination. The historical shell theme packager also targets the repository's `dist/themes/` directory. Existing release ZIPs are never overwritten by the Python packager. Validation confirmed moved ZIP checksums are unchanged, archive integrity passes, and both packaging destinations work.
