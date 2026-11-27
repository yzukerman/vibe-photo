# Vibe Photo Theme

A modern, responsive WordPress theme designed specifically for photography portfolios and photo galleries with advanced lightbox functionality, WordPress metadata integration, and comprehensive archive and menu support.

Note: Claude Code Sonnet 4.5 developed the code with my guidance. No warranty is implied or otherwise. Use at your own risk.

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

- Set a **Featured image** on each post or Photo Gallery entry to include it in the homepage grid. Entries without a featured image are omitted from this grid, but remain accessible at their own URLs.
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

## Features

- **Advanced Lightbox**: Custom lightbox with EXIF data display, social sharing, and navigation
- **WordPress Metadata Integration**: Displays image titles, captions, and alt text from WordPress
- **Smart Attachment Detection**: Handles WordPress scaled images for proper metadata retrieval
- **WordPress Gallery Block Support**: Enhanced Gallery blocks with lightbox functionality
- **Custom Photo Gallery Post Type**: Dedicated post type for photo galleries
- **Archive Templates**: Beautiful category and tag archive pages with grid layouts
- **Footer Menu Support**: Separate footer navigation menu location for legal pages and links
- **Page Template**: Dedicated template for static pages with clean layout
- **Horizontal Pagination**: Styled page numbers with intuitive navigation
- **Privacy Policy Filtering**: Automatically removes privacy policy from header menu while keeping it in footer
- **EXIF Format Support**: Intelligent detection of image formats (JPEG, TIFF) for EXIF reading
- **Responsive Design**: Mobile-first design built with Foundation CSS framework
- **Social Sharing**: Share photos on Facebook, Twitter, Pinterest, and Tumblr
- **EXIF Data Display**: Shows camera settings and technical details
- **Conditional Field Display**: Metadata fields only appear when they have content
- **Professional Typography**: Clean, readable fonts optimized for photography

## Installation

### Method 1: Upload via WordPress Admin

1. Download the theme ZIP file
2. Go to your WordPress admin panel
3. Navigate to **Appearance > Themes**
4. Click **Add New** then **Upload Theme**
5. Choose the ZIP file and click **Install Now**
6. Activate the theme

### Method 2: FTP Upload

1. Extract the theme ZIP file
2. Upload the `vibe-photo-theme` folder to `/wp-content/themes/`
3. Go to **Appearance > Themes** in WordPress admin
4. Activate the Vibe Photo Theme

## Setup

### Homepage Configuration

1. Go to **Settings > Reading**
2. Set "Your homepage displays" to "Your latest posts" OR create a custom front page
3. The theme will automatically display your latest posts and photo galleries

### Creating Photo Galleries

You can create galleries in two ways:

**Option 1: Custom Photo Gallery Posts**

1. Go to **Photo Galleries > Add New** in your WordPress admin
2. Add images and content
3. Publish your gallery

**Option 2: WordPress Gallery Blocks**

1. Create a new post or page
2. Add a **Gallery block**
3. Upload your images
4. The theme will automatically enhance it with lightbox functionality

### Menu Setup

1. Go to **Appearance > Menus**
2. Create menus for your site:
   - **Primary Menu**: Main navigation in the header
   - **Footer Menu**: Links in the footer (e.g., Privacy Policy, Terms of Use)
3. Assign your menus to their respective locations

**Note**: The theme automatically filters the Privacy Policy page from the primary menu and shows it only in the footer menu.

## Customization

### Colors

You can customize the theme colors by modifying the CSS variables in `style.css`:

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

### Adding Custom CSS

1. Go to **Appearance > Customize**
2. Click **Additional CSS**
3. Add your custom styles

## Technical Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- Modern web browser with JavaScript enabled

## Browser Support

- Chrome 60+
- Firefox 55+
- Safari 12+
- Edge 79+

## Troubleshooting

### Lightbox Not Working

- Ensure JavaScript is enabled in your browser
- Check that jQuery is loading properly
- Verify that Foundation CSS/JS are loading

### Images Not Loading in Lightbox

- Check that image URLs are accessible
- Verify file permissions on uploads directory
- Ensure images exist in full size

### Gallery Block Not Enhanced

- The theme automatically enhances WordPress Gallery blocks
- If not working, check browser console for JavaScript errors

## Development

The theme is built with:

- Foundation CSS Framework 6.8.1
- jQuery (included with WordPress)
- Custom JavaScript for lightbox functionality
- PHP 7.4+ compatible code

## File Structure

```
vibe-photo-theme/
├── style.css                 # Main stylesheet with theme info
├── index.php                 # Main template file
├── front-page.php            # Homepage template
├── page.php                  # Static page template
├── single.php                # Single post template
├── archive.php               # Category/tag archive template
├── functions.php             # Theme functions and features
├── header.php                # Header template
├── footer.php                # Footer template with menu support
├── single-photo_gallery.php  # Photo gallery single view
├── archive-photo_gallery.php # Photo gallery archive
├── screenshot.png            # Theme screenshot
├── README.txt               # WordPress.org style readme
├── README.md               # Development readme
└── assets/
    ├── css/                # Additional stylesheets
    ├── js/                 # JavaScript files
    │   ├── lightbox.js     # Main lightbox functionality
    │   ├── gallery.js      # Gallery enhancements
    │   └── navigation.js   # Menu and navigation
    └── images/             # Theme images
```

## Changelog

### Version 1.0.10 (December 25, 2025)

- **New**: Archive template (archive.php) for category and tag pages
- **New**: Styled archive pages with grid layout and post cards
- **Enhanced**: Archive pages display title, description, and pagination
- **Improved**: EXIF reading now checks file format (JPEG/TIFF only, skips WebP)
- **Fixed**: Category and tag pages no longer appear blank
- **Enhanced**: Archive post cards with hover effects and thumbnails
- **Improved**: Better handling of WebP images (graceful degradation for EXIF)

### Version 1.0.9.1 (December 25, 2025)

- **Fixed**: Header no longer shows automatic page listings when no menu is assigned
- **Enhanced**: Added debug logging for EXIF data troubleshooting
- **Improved**: Better error diagnostics for attachment ID detection and file permissions

### Version 1.0.9 (December 25, 2025)

- **New**: Footer menu support - separate navigation location for footer links
- **New**: Page template (page.php) for displaying static pages properly
- **New**: Horizontal pagination styling with numbered page navigation
- **New**: Privacy policy filtering - automatically removes privacy policy from header menu
- **Enhanced**: Footer now displays navigation menu above copyright
- **Enhanced**: Menu filtering system to handle WordPress automatic page listings
- **Improved**: CSS styling for footer navigation with hover effects
- **Improved**: Pagination now uses flexbox for consistent horizontal layout
- **Fixed**: Pages now display correctly with dedicated template
- **Fixed**: Privacy policy link positioning between header and footer

### Version 1.0.8

- **New**: WordPress metadata integration - lightbox now displays image titles, captions, and alt text from WordPress attachment data
- **Enhanced**: Smart attachment detection handles WordPress scaled images (-scaled suffix)
- **Improved**: Conditional field display - metadata fields only appear when they have content
- **Enhanced**: Better filename pattern matching for attachment ID detection
- **Fixed**: Border styling consistency across all data fields in lightbox
- **Updated**: CSS optimizations for visual polish

### Previous Versions

- **1.0.7**: Enhanced lightbox functionality and EXIF data display
- **1.0.6**: Foundation CSS integration and responsive improvements
- **1.0.5**: Initial stable release with core functionality

## Credits

- Foundation CSS Framework: https://get.foundation/
- Font Awesome Icons: https://fontawesome.com/
- Developed by Yuval Zukerman

## License

GPL v2 or later - https://www.gnu.org/licenses/gpl-2.0.html

## Support

For questions, feature requests, or bug reports, please contact the theme developer.

### Verification performed for the split

- PHP syntax checks passed for the new plugin modules and changed theme templates; viewer JavaScript passed `node --check`.
- Local WordPress checks confirmed registration of `photo_gallery`, both gallery shortcodes, and legacy shortcode image rendering.
- Theme with plugin active served the moved viewer asset; theme with plugin inactive retained the cover grid and native Gallery block markup.
- Twenty Twenty-Five with the plugin active served the viewer assets independently. The original Vibe Photo theme and active plugin were restored afterward.
- These are integration smoke checks, not a full visual/accessibility review or WordPress.org certification. Google geocoding was not called and no content was deleted.

Repeat registration checks using `wp eval-file /path/to/repo/tests/gallery-smoke.php` against a WordPress installation with the plugin active and media available. Build separate installation ZIPs with `python3 tools/package.py theme` and `python3 tools/package.py plugin`; outputs use stable folder slugs, and existing archives are never overwritten. The old shell packager is retained for historical releases; prefer the new packager for the split components.

Additional checks: EXIF AJAX succeeded for a local uploaded image and rejected invalid nonces and external URLs. Separate theme and plugin ZIPs were built in a temporary directory and passed integrity checks; these are validation builds, not published releases. The plugin readme documents the optional Google service and public GPS metadata.

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
