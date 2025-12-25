# Vibe Photo Theme v1.0.10

A modern, responsive WordPress theme designed specifically for photography portfolios and photo galleries with advanced lightbox functionality, WordPress metadata integration, and comprehensive archive and menu support.

Note: Claude Code Sonnet 4.5 developed the code with my guidance. No warranty is implied or otherwise. Use at your own risk.

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
