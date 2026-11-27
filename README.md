# 🎨 Vibe Photo Theme - Distribution Package

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

## Portfolio UI

The homepage presents posts and Photo Gallery entries as a compact grid of square cover images, inspired by Format's Coat template. Each cover has an always-visible title overlay and a “View gallery” label (hidden on the narrowest screens).

- Set a **Featured image** to choose a homepage cover, or use a gallery with local image attachments for an automatic first-image fallback. Plain entries without either remain outside the grid.
- The homepage displays up to **12 entries per page**, newest first, with pagination when needed.
- Covers are cropped to squares using `object-fit: cover`; original media files are unchanged.
- The grid uses four columns on wide screens, three on desktops, and two on smaller screens.
- Clicking a cover opens its post. Clicking an image in the post's gallery opens the lightbox.
- Regular single posts use a wider content area and no longer repeat the featured cover above the gallery.
- **Oranienbaum** is used for the site name, cover titles, and regular single-post titles, with sans-serif text for navigation and body copy. The font is bundled locally under `assets/fonts/oranienbaum/`, with Georgia as a fallback.
- On mobile, the site name stays centered with search at the top right. The unused Menu button has been removed.

### Lightbox loading feedback

An animated bar and “Loading photo…” message appear while the selected full-size image loads. This is an indeterminate activity indicator, not a download percentage. It clears on success or failure; if the full-size request fails, the viewer falls back to the thumbnail. Stale image-load callbacks are ignored when navigating rapidly or closing the viewer. Reduced-motion preferences disable the bar's animation.

### Creating a gallery post

1. Create a post and add a title and optional introduction.
2. Insert a WordPress **Gallery** block and select your photographs.
3. Set the post's **Featured image** to choose its homepage cover.
4. Publish the post and visit the homepage to check the cover and title overlay.

## 🤔 Motivation

I am a photographer. I wanted to host my images on a WordPress site hosted on my own shared server. WordPress' native gallery is great but does not offer any full size image views, just thumbnails. The paid galleries are great and nimble but don't do everything I wanted and are not open source.

Being home with COVID a few months ago, I started working with Claude Code Sonnet (4.0 originally) in VS Code with Github CoPilot. This is the outcome.

## 🎨 Theme Features

### ✨ Advanced Lightbox

- EXIF data display (camera settings, lens info)
  - **Format Support:** JPEG and TIFF images only
  - **Not Supported:** WebP, PNG, GIF, and other formats
  - Automatically detects image format and gracefully handles unsupported types
- GPS coordinates with reverse geocoding (requires Google Cloud API key)
- Deep linking for individual image sharing
- Social sharing (Facebook, Twitter, Pinterest, Tumblr)
- Image navigation with keyboard support
- Full-screen viewing
- Download functionality
- Copy link to clipboard

### 📱 Responsive Design

- Mobile-first approach
- Foundation CSS framework
- Flexible grid layouts
- Touch-friendly navigation

### 🖼️ Gallery Support

- WordPress Gallery block enhancement
- Custom Photo Gallery post type
- Masonry grid layouts
- Thumbnail optimization

### 🎯 SEO & Performance

- Clean, semantic HTML5
- Optimized image loading
- Fast CSS/JS delivery
- Search engine friendly

### ✅ Core Theme Files

- `style.css` - Main stylesheet with theme information
- `index.php` - Main template file
- `front-page.php` - Homepage template (paginated cover grid for posts and photo galleries with featured images)
- `functions.php` - Theme functionality and features
- `single.php` - Single post template
- `single-photo_gallery.php` - Photo gallery single view
- `archive-photo_gallery.php` - Photo gallery archive
- `screenshot.jpg` - Theme preview image

### ✅ Assets

- `assets/js/lightbox.js` - Advanced lightbox functionality
- `assets/js/gallery.js` - Gallery enhancements
- `assets/js/navigation.js` - Navigation and menu functionality
- `assets/css/` - Additional stylesheets directory
- `assets/images/` - Theme images directory

### ✅ Documentation

- `README.md` - Developer documentation
- `README.txt` - WordPress.org style readme

## 🚀 Distribution Methods

### Method 1: WordPress.org Repository (Recommended)

1. Create a WordPress.org developer account
2. Submit your theme for review
3. Follow WordPress theme guidelines
4. Once approved, users can install directly from WordPress admin

### Method 2: Direct Distribution

Users can install your theme by:

1. Downloading the `vibe-photo-theme-1.0.0.zip` file
2. Going to **WordPress Admin > Appearance > Themes**
3. Clicking **Add New > Upload Theme**
4. Uploading the ZIP file
5. Activating the theme

### Method 3: Manual Installation

For developers:

1. Extract the ZIP file
2. Upload the `vibe-photo-theme` folder to `/wp-content/themes/`
3. Activate from WordPress admin

## 🔧 Installation Requirements

### Server Requirements

- **WordPress:** 5.0 or higher
- **PHP:** 7.4 or higher (with EXIF extension enabled)
- **MySQL:** 5.6 or higher

### PHP Extensions

- **EXIF Extension:** Required for displaying camera metadata
  - Most shared hosting providers have this enabled by default
  - To verify: Check `phpinfo()` for "exif" support
  - To enable: Add `extension=exif` to your `php.ini` file
  - **Note:** EXIF data is only available for JPEG and TIFF images. WebP and other formats are not supported by PHP's EXIF functions.

### Browser Support

- Chrome 60+
- Firefox 55+
- Safari 12+
- Edge 79+

## 📋 Post-Installation Setup

### 1. Homepage Configuration

```
WordPress Admin > Settings > Reading
Set "Your homepage displays" to "Your latest posts"
```

### 2. Menu Setup

```
WordPress Admin > Appearance > Menus
Create menu and assign to "Footer Menu" location
```

### 3. Google Cloud API (Optional - for GPS Reverse Geocoding)

To enable location names from GPS coordinates:

1. Get a Google Cloud API key from https://console.cloud.google.com
2. Enable the "Geocoding API" in your Google Cloud project
3. Go to `WordPress Admin > Settings > Media`
4. Enter your API key in the "Google Maps API Key" field
5. Location data will be cached to minimize API usage

### 4. Photo Gallery Usage

**Option A: Custom Post Type**

```
WordPress Admin > Photo Galleries > Add New
```

**Option B: WordPress Gallery Blocks**

```
Add Gallery block to any post/page
Theme automatically enhances with lightbox
```

## 🛠️ Customization Options

### CSS Variables (in style.css)

```css
:root {
  --primary-color: #2c3e50; /* Main theme color */
  --secondary-color: #34495e; /* Secondary elements */
  --accent-color: #e74c3c; /* Accent/hover color */
  --text-color: #2c3e50; /* Main text color */
  --light-gray: #ecf0f1; /* Light backgrounds */
  --dark-gray: #7f8c8d; /* Muted text */
}
```

### Custom CSS

Add custom styles via:

```
WordPress Admin > Appearance > Customize > Additional CSS
```

## 📞 Support & Documentation

### For End Users

- Include README.txt with installation instructions
- Provide setup guide for Photo Galleries
- Document customization options

### For Developers

- Clean, commented code
- WordPress coding standards
- Extensible architecture
- Hook system for modifications

## 📄 License Information

**License:** MIT License  
**License URI:** https://opensource.org/licenses/MIT

This means users can:

- ✅ Use the theme for any purpose
- ✅ Modify and customize
- ✅ Redistribute
- ✅ Use for commercial projects
- ✅ Sublicense and distribute modified versions

## 🔄 Version Management

Current version: **1.3**

For future updates:

1. Update version in `style.css` header
2. Update version in `package-theme.sh`
3. Update `README.txt` changelog
4. Run packaging script
5. Test installation on fresh WordPress

## 📊 Quality Checklist

Before distribution, verify:

- ✅ Theme activates without errors
- ✅ All templates load correctly
- ✅ Lightbox functionality works
- ✅ Gallery blocks are enhanced
- ✅ Responsive design functions
- ✅ No JavaScript errors
- ✅ EXIF data displays
- ✅ Social sharing works
- ✅ Navigation functions properly
- ✅ WordPress coding standards met

Your Vibe Photo Theme is now ready for the world! 🌟

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

## Local presentation dependencies (WordPress.org preparation)

Foundation Sites **6.8.1** CSS and JavaScript are bundled under `assets/vendor/foundation/` instead of loaded from jsDelivr. Oranienbaum Regular is bundled as an unmodified TrueType font under `assets/fonts/oranienbaum/`, with a local `@font-face` stylesheet and `font-display: swap`. No Google Fonts request is needed. WordPress still provides jQuery.

The theme's `ASSETS.md` lists the source URLs, copyright holders, licenses, modifications, and SHA-256 checksums. Foundation's MIT license and Oranienbaum's SIL Open Font License 1.1 are shipped beside their assets. Source-map references were removed from the minified Foundation files because their maps are not distributed. Commented-out CDN enqueues for unused Masonry/imagesLoaded code were removed; those scripts remain disabled.

Validation: PHP and bundled JavaScript syntax checks passed. The Local homepage no longer references jsDelivr, Google Fonts, or Google font hosting; the local CSS, JavaScript, font stylesheet, and font file all returned HTTP 200, and the TrueType signature was verified. A temporary theme ZIP passed integrity checks and contained the dependencies, licenses, and asset inventory. A browser visual/offline regression check is still pending; these HTTP checks do not establish visual equivalence. No new release version or published distribution was created for this change.

This removes external requests needed to load the framework and font; the companion plugin's optional Google Geocoding service is unchanged. Other WordPress.org submission work remains, including screenshot licensing, accessibility/navigation, template cleanup, and Theme Check.

Manual validation reported by the site owner: the theme and companion plugin work independently of each other.

## Lightbox keyboard navigation and focus

The companion plugin now routes keyboard and previous/next/close controls through a single active viewer controller instead of two competing keyboard handlers tied to different image arrays.

- **Left / Right arrows:** previous / next photograph while the viewer is open; navigation stops at the first and last image.
- **Escape:** close the viewer, clear its deep-link hash, and return focus to the originating gallery control.
- **Tab / Shift+Tab:** cycle through enabled, visible viewer controls. Opening places focus on Close; background page sections are made inert until closing, with their previous inert state restored.
- Unlinked gallery images receive keyboard focus, button semantics, and Enter/Space activation. Viewer controls and gallery triggers have visible focus outlines.
- Arrow shortcuts do not intercept editable fields or modified key combinations. Browser-native fullscreen shortcuts are left to the browser; the viewer fullscreen button remains available.
- The viewer exposes dialog semantics and an accessible name. This is an incremental improvement, not a complete screen-reader/accessibility certification; broader translation, linked-image, dynamic-gallery, and cross-browser coverage still needs review.

Validation on Local's Pittsburgh Part 3 gallery (24 images): Enter opened image 1; Right moved to 2 and Left returned to 1; Space opened image 24; Right stayed at 24 and Left moved to 23. Shift+Tab wrapped from Close to the last control and Tab returned to Close. Escape closed the dialog and restored focus to the original first/last gallery image. A pointer click on Next advanced exactly one image. JavaScript syntax and Git whitespace checks passed. No release ZIP or version change was made for this update.

## Accessible theme navigation

The first theme keyboard stop is now **Skip to content**. It becomes visible on focus and targets `main-content`; each existing main template has that ID and `tabindex="-1"` so focus can move there. The smooth-scroll handler excludes the skip link, and the skip action explicitly focuses the content target.

Primary navigation is rendered only when a menu is assigned to the **Primary Menu** location under Appearance > Menus. Desktop and mobile navigation have descriptive landmark labels. Mobile uses a native `<details>`/`<summary>` Menu control at the left, with the site name centered and search at the right. Native disclosure provides keyboard activation and expanded/collapsed state without depending on Foundation's toggler; basic opening works without JavaScript. No configured primary menu means no Menu control or empty navigation landmark.

With JavaScript enabled, Escape closes an open mobile menu and restores focus to its control. Clicking outside or following a menu link closes it. Resizing to desktop closes the mobile disclosure and moves focus to a desktop navigation link when necessary. Standard links keep native keyboard behavior; the old custom link-key handler and duplicate Foundation initialization were removed. Links, form fields, buttons, and disclosure summaries have visible focus outlines. Navigation script cache versions now follow file modification time.

Validation: PHP syntax checks passed for the header, functions, and affected main templates; navigation JavaScript syntax and Git whitespace checks passed. HTTP checks confirmed one skip target on the homepage, search results, and a regular post. Live browser checks confirmed first-Tab skip-link focus, Enter moving focus to main content, and a temporary configured menu at a 390-pixel viewport: Enter opened it, Tab reached its gallery link, and Escape closed it and restored control focus. The temporary Local menu was removed and its prior assignment restored; HTML confirmed the control is absent with no menu assigned. The viewport override was reset after testing. No production settings were changed.

The malformed fallback template and manual title output are separate pending fixes. These navigation changes do not constitute a complete accessibility audit. No release version, published ZIP, or remote push was made.

### Empty-menu follow-up

An assigned menu can still contain zero saved links. The header now requires both a Primary Menu assignment and nonempty menu items before showing desktop navigation or the mobile Menu disclosure. This prevents a mobile button that opens an empty panel. To populate a menu, select it in Appearance > Menus, add pages or Custom Links using Add to Menu, and click Save Menu. Desktop shows the links directly; the mobile disclosure appears below 640px. Local inspection confirmed the reported Test Menu had no saved items even with legacy theme filtering bypassed. HTTP validation confirmed empty navigation controls are now omitted.

### Local menu persistence diagnosis

Follow-up inspection found the owner's Pittsburgh Part 1 and Berlin links as draft `nav_menu_item` posts with orphan metadata and no menu relationships. Thus the editor showed entries while the published menu API returned none; the links were not absent from the database. Local repair attached those existing items to Test Menu and published them in their existing order without changing their destinations. HTTP checks confirmed both desktop and mobile menu markup includes both links. This was a Local database correction, not a theme rendering fix or production change. Menu editor visibility alone does not prove items have been published and attached to a menu.

## Fallback template and document titles

The malformed `index.php` fallback has been replaced with a conventional main-query loop. It no longer closes the document early, emits an extra footer, replaces WordPress's global query, or runs unrelated Photo Gallery queries. It renders the query's posts, escaped dates, featured images, excerpts, pagination, and a searchable empty state. Missing URLs retain HTTP 404 and display a Page not found heading and search form. The existing homepage, archive, search, and single templates remain responsible for their own routes.

The header no longer manually emits a `<title>` using `wp_title()`. WordPress produces the document title through the theme's existing `title-tag` support, avoiding duplicate titles while preserving core/plugin title filters.

Validation: live HTTP checks for the homepage, regular post, matching/empty search, and missing URL confirmed one title, main element, footer, body, and HTML document. The missing URL returned 404 with the correct message and search form. PHP syntax and Git whitespace checks passed. Repeatable fallback tests in `tests/template-smoke.php` passed for populated, paginated, and empty main queries; run each in a separate WP-CLI process, e.g. `wp eval-file /path/to/repo/tests/template-smoke.php populated`, then `paginated` and `empty`. The test requires a populated WordPress test site and performs no database writes. Separate processes are necessary because WordPress includes header/footer templates once per request.

No release/version update, new distribution, or remote push was made. Theme Check and the broader submission audit remain pending.

## Submission screenshot replacement

Replaced the old screenshot with the site owner's supplied `yuvalzukerman.com_page_2.png` portfolio capture. The source was a 2400 × 1800 PNG with the correct 4:3 ratio; it was resized to 1200 × 900 without cropping and unnecessary metadata was stripped. The installed `screenshot.png` is now actual PNG data and shows the current cover-grid design without browser controls or the WordPress admin bar. This addresses the reported screenshot dimensions requirement. Screenshot photography licensing/attribution still needs confirmation before WordPress.org submission; supplying a screenshot alone does not establish a GPL-compatible redistribution license for its photographs.

### JPEG screenshot optimization

The current theme preview is `screenshot.jpg`, copied unchanged from the owner-supplied `yuvalzukerman.com_page_2.jpg`. It is a progressive JPEG at 1200 × 900 (4:3), approximately 233 KiB, replacing the 1.3 MiB PNG. The obsolete PNG was removed so WordPress uses the JPEG. Dimensions, format, and appearance were checked; screenshot-photograph licensing confirmation remains pending. No release package was rebuilt.

## Theme Check cleanup: debug files, tags, and core CSS

Removed `debug-gallery.php` and `clear-location-cache.php` from the theme source. These were development utilities, including hard-coded WordPress loading and cache mutation, not presentation templates. Historical ZIPs remain unchanged; the packagers retain their defensive exclusions.

The stylesheet header now uses only `photography`, `portfolio`, `grid-layout`, `one-column`, `custom-menu`, and `featured-images`. Unsupported custom tags and claims not yet validated (such as flexible headers and translation readiness) were removed. Tag reference: https://make.wordpress.org/themes/handbook/review/required/theme-tags/ . Full internationalization remains pending; removing a tag is not a replacement for translating visible strings.

Added WordPress-generated class styling for `.screen-reader-text` (visually hidden normally, visible on focus), captions, gallery captions, left/right/center alignment, sticky post titles, and author comments. Content floats clear after the entry; floated images stack on narrow screens. Screen-reader labels remain available to assistive technology rather than using `display: none`. Author-comment styling will apply when the planned theme-owned comments template is implemented.

Validation: a temporary theme ZIP passed integrity checks, excluded both deleted utilities, and contained `screenshot.jpg`. Checks confirmed all requested core classes exist, and homepage/post/search HTTP responses loaded without visible fatal errors. Git whitespace checks passed. Caption/alignment edge cases still need a full Theme Unit Test visual pass; this does not claim all Theme Check recommendations are resolved. No version bump, published distribution rebuild, or remote push was made.

## Theme-owned comments template

Added `comments.php` using core `wp_list_comments()`, `the_comments_pagination()`, and `comment_form()`. Core renders avatars, reply links, threaded comment markup, and short pingback/trackback entries; the template supplies a translated comment count, accessible section label, and closed-discussion message. Password-protected posts return before displaying comments. Existing post/page calls now use this theme template instead of WordPress's fallback; the Photo Gallery single template also calls it when a discussion is open or comments exist. The plugin's gallery editor capabilities and site discussion preferences are not changed.

The core `comment-reply` script is enqueued only for singular pages with open comments and threaded replies enabled. CSS covers comment lists, nested replies, avatars, author comments, metadata, forms, buttons, and pagination. Avatars follow WordPress's existing avatar setting/service; the theme introduces no separate avatar service. Comment submission, moderation, login requirements, and closed/open state remain governed by core settings. Adding the template does not re-enable comments on posts where they were disabled.

Validation on Local used a temporary post with five approved comments and two comments per page. The rendered open discussion contained avatars, the author class, list, pagination, form, and reply script. Closing it retained comments and removed form/reply-script markup. Adding a post password hid the discussion. The test post/comments were removed and original threading, pagination, per-page count, default comment page, and avatar settings restored. PHP syntax and Git whitespace checks passed. The fixture initially lacked a matching post author; setting its author correctly allowed verification of core's `bypostauthor` class. No real user comments were changed. A full visual/threaded-reply interaction pass and Theme Check rerun remain pending; no new ZIP, version bump, or push was made.

## Editor styles and WordPress block-style support

Enabled `wp-block-styles` alongside existing `editor-styles` support and registered the local font stylesheet plus `assets/css/editor-style.css` using `add_editor_style()`. WordPress loads/scopes these styles for authored editor content; Foundation and the site's header/navigation styles are not injected into the editor UI.

The editor stylesheet supplies the site's white background, charcoal text, sans-serif body, local Oranienbaum headings/title, caption colors, square image corners, quote styling, and normal/wide/full block widths. Matching authored-content heading/quote styles and container-bounded wide/full alignment rules were added on the front end. Full-aligned content fills its theme content container rather than overflowing the viewport. Font files remain local with Georgia fallback and `font-display: swap`.

Validation: WordPress confirmed both support flags and registered stylesheet paths; local editor/font CSS URLs returned HTTP 200. A temporary theme ZIP passed integrity checks and included the editor/font stylesheets. PHP syntax and Git whitespace checks passed. An initial assertion expecting a combined `wp-block-library-theme` handle did not pass. Inspection confirmed this WordPress installation enables separate core block assets; core attaches supported theme styles per block instead of requiring that combined handle, so the assertion was inappropriate for this configuration. Visual comparison in the block/classic editors and a range of blocks remains pending; registration checks alone do not establish visual equivalence. No block patterns, custom block styles, release version, published ZIP, or push were added.

## Custom logo support

Upload a logo through **Appearance → Customize → Site Identity → Select logo**, then publish the Customizer change. Remove the logo there to restore the text site name. The core custom-logo API handles image selection, cropping controls, homepage linking, and alternative text; the header does not wrap its output in another link.

The suggested source size is 360 × 120 pixels, with flexible width and height; square or differently proportioned logos are also allowed. Header CSS preserves proportions, limits desktop display to 18rem wide / 4rem tall, and caps mobile height at 2.5rem within the centered branding column so the Menu and Search controls remain clear. The logo replaces the visible site-name link; the WordPress site title remains configured for document titles and accessibility.

Validation: WordPress confirmed `custom-logo` support. A temporary existing Local image produced core logo markup without a nested text-branding link, and removing the setting restored the site-name fallback. The original logo setting was restored after the check. Changed PHP files passed syntax checks and Git whitespace checks passed. The CLI rendering test emitted SERVER_NAME warnings from WordPress because it was outside an HTTP request; visual Customizer/mobile checks with the owner's chosen logo remain pending. No media was uploaded or removed, and no release ZIP, version change, or push was made.

## Local installation slug corrected

Local now loads the theme through `~/Local Sites/vibe-photo/app/public/wp-content/themes/vibe-photo`, a symlink to this repository's `Themes/vibe-photo-theme/` source directory. The former `vibe-photo-theme` installation symlink was removed after activation and checks. The repository source directory is intentionally unchanged; the installed folder and submission ZIP folder both use the `vibe-photo` slug matching the text domain.

The existing theme modifications were copied to `theme_mods_vibe-photo` before activation and restored afterward, preserving the logo, primary menu assignment, and saved sidebar configuration. No Additional CSS was present to migrate; a backup of the prior mods and CSS was saved locally under `/tmp/vibe-photo-slug-backup.json` for this operation. The old theme-mods option was retained as a recovery source; no posts, media, plugins, or real comments were deleted. A fresh WordPress process confirmed the full backed-up settings match the new theme's settings. An initial same-process verification after switching did not reflect the final state; independent verification was used before removing the old link.

Homepage, post, and search HTTP checks confirmed the logo and menu links remain present and theme asset URLs now use `/themes/vibe-photo/` with no references to the old installed folder. The companion plugin stays active through its existing plugin symlink. Refresh browser caches and rerun Theme Check against Vibe Photo to confirm the directory warning clears. This is a Local installation change; the source-folder name does not need to match when the packager writes a correct stable slug. No distribution rebuild, release-version change, or remote push was made.

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

## Automatic collection cover fallback

Homepage covers now prefer a usable Featured image, then the first usable local image attachment from a WordPress Gallery block (including nested image blocks and legacy gallery IDs), then explicit `[gallery]` / `[vibe_gallery]` IDs, then saved `_gallery_images` selections. The fallback renders a cover without writing or overwriting `_thumbnail_id`. Set a Featured image whenever you want to control the cover explicitly.

The grid query now includes published posts with gallery content or saved gallery images as well as posts with featured images. The eligibility condition is scoped to the cover-grid query, keeping WordPress SQL pagination rather than loading every post into PHP. Plain posts without a featured image or gallery remain excluded. Invalid/deleted or external-only gallery images may leave a text-only tile; this fallback uses local image attachment IDs, not arbitrary remote images. Shortcodes without explicit image IDs are not resolved into attached-media covers in this version.

Validation on Local: More gallery (post 208), previously missing because it has no Featured image, now appears with a gallery-image cover. Existing Pittsburgh covers retain their Featured images. Hello world stays excluded. Changed PHP syntax, whitespace, featured-image precedence, and query isolation checks passed. Repeat non-mutating checks with `wp eval-file /path/to/repo/tests/cover-smoke.php`. No saved Featured images were changed; no release/version bump, distribution rebuild, or push was made.

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

Final validation packages are `dist/vibe-photo-v1.3.zip` and `dist/vibe-photo-galleries-v1.0.0.zip`, each with one stable installation folder. Historical deployment ZIPs are retained separately and should not be used for directory submission. Install the theme and plugin separately. The theme can be submitted on its own; do not recommend automatic installation or claim a WordPress.org plugin listing before the companion plugin is approved there.

Release checks use the exact ZIPs in the isolated WordPress 6.7.1 / PHP 8.2.29 harness, with clean component directories and separate test data. The repository's cover, gallery, security, template, and fullscreen lifecycle tests are the repeatable checks; Theme Check must have no REQUIRED or WARNING entries. A release validation report accompanies the downloadable artifacts. Test success is not a guarantee of directory acceptance or a full browser/device/security certification. No WordPress.org submission or production deployment is authorized by this preparation step.

### Final 1.3 ZIP validation results

The exact theme/plugin release ZIPs were extracted into clean component directories on the isolated WordPress 6.7.1 / Local PHP 8.2.29 installation. Passed: theme-alone cover/homepage behavior, gallery/cover/security smoke checks, populated/paginated/empty fallback templates, plugin-provided legacy covers, gallery archive data-provider behavior, and public metadata AJAX. Temporary legacy fixture content was removed. Theme Check returned no REQUIRED or WARNING messages, and the WordPress debug log remained empty. Four fullscreen lifecycle regression tests also passed. Documentation-only packaging updates were followed by ZIP integrity checks. Production and WordPress.org deployment were not performed.

## Plugin submission scanner corrections — 1.0.1

The first directory upload was rejected for a missing `Tested up to` readme header and eight unescaped translated strings in the Media settings output. Version 1.0.1 adds `Tested up to: 7.1`, after verifying on the existing Local WordPress 7.1.5 installation, while retaining the WordPress 6.7.1 / PHP 8.2 minimum. All translated settings output now uses `esc_html__()`; the Google Console link has fixed markup, an escaped URL, and a separate translated/escaped label. No scan suppression was added.

Local Plugin Check 2.2.0 additionally identified an unprefixed module variable, discouraged explicit plugin text-domain loading, and direct metadata-query concerns. The loader now prefixes its variable and declares `Domain Path: /languages` for WordPress's on-demand translation loading. Exact local attachment URL lookup now uses core `attachment_url_to_postid()` instead of a direct database query, retaining the visibility and realpath checks.

Validation: Plugin Check's default CLI run on the isolated WordPress 6.7.1 installation returned “Checks complete. No errors found.” Metadata integration tests on the current Local site still passed for valid media, rejected malformed/nonce/external/traversal/missing requests, and protected-parent access; gallery/security smoke tests passed on WordPress 7.1.5. Both versions are documented as exercised configurations, not a complete version matrix. Changed PHP syntax, catalog generation, and ZIP integrity checks passed. `dist/vibe-photo-galleries-v1.0.1.zip` replaces 1.0.0 for the next plugin upload; the historical ZIP remains unchanged. Passing local checks does not guarantee directory approval, and no automatic re-submission was performed.


### Footer menu visibility fix (November 27, 2026)

Fixed a legacy footer navigation rule that rendered menu links white on the portfolio footer's white background. Footer menu links now use dark gray, and the horizontal menu wraps when space is limited. Assign a menu under Appearance → Menus → Manage Locations → Footer Menu to display it.

Validation: verified the assigned Pittsburgh Part 1 and Berlin links on the Local site and confirmed their computed text color is `rgb(85, 89, 79)`. `git diff --check` passed. Existing distribution ZIPs have not been rebuilt for this fix.


### Rebuilt submission theme distribution (November 27, 2026)

Rebuilt `dist/vibe-photo-v1.3.zip` from the restored submission branch to include the footer menu contrast and wrapping fix. The submission version remains 1.3. The gallery plugin package remains 1.0.1. Verified ZIP integrity, the `vibe-photo/` installation directory, and byte-for-byte agreement with the packaged theme source files.
