# 🎨 Vibe Photo Theme - Distribution Package

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
- `front-page.php` - Homepage template (displays posts + photo galleries)
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

Current version: **1.0.20**

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
