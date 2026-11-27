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
- `screenshot.png` - Theme preview image

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

Current version: **1.20.0.3**

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
