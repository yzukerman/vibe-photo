=== Vibe Photo Theme v1.0.9 ===
Contributors: Yuval Zukerman and Visual Studio Code Copilot with Claude Sonnet. 
Tags: photography, portfolio, gallery, responsive, minimal, foundation, lightbox, exif, metadata, footer-menu
Requires at least: 5.0
Tested up to: 6.3
Requires PHP: 7.4
Stable tag: 1.0.9
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

A modern, responsive WordPress theme designed specifically for photography portfolios and photo galleries with advanced lightbox functionality, WordPress metadata integration, and comprehensive menu support.

**Note**: This version will install alongside previous versions without overwriting them, allowing you to safely test the new version.

== Description ==

Vibe Photo Theme is a professional photography theme built with Foundation CSS framework. It features:

* **Advanced Lightbox**: Custom lightbox with EXIF data display, social sharing, navigation, and fullscreen viewing
* **WordPress Metadata Integration**: Displays image titles, captions, and alt text from WordPress attachment data
* **Smart Attachment Detection**: Handles WordPress scaled images for proper metadata retrieval
* **WordPress Gallery Block Support**: Enhanced Gallery blocks with lightbox functionality
* **Custom Photo Gallery Post Type**: Dedicated post type for photo galleries
* **Footer Menu Support**: Separate footer navigation menu location for legal pages and links
* **Page Template**: Dedicated template for static pages with clean layout
* **Horizontal Pagination**: Styled page numbers with intuitive navigation
* **Privacy Policy Filtering**: Automatically removes privacy policy from header menu while keeping it in footer
* **Responsive Design**: Mobile-first design that looks great on all devices
* **Foundation CSS Framework**: Built on the solid Foundation by Zurb framework
* **Social Sharing**: Share photos on Facebook, Twitter, Pinterest, and Tumblr
* **EXIF Data Display**: Shows camera settings and technical details for images
* **Conditional Field Display**: Metadata fields only appear when they have content
* **Masonry Layout**: Beautiful grid layouts for photo galleries
* **SEO Optimized**: Clean, semantic HTML5 markup
* **Fullscreen Support**: True fullscreen image viewing with keyboard navigation

== Installation ==

1. Upload the theme folder to `/wp-content/themes/` directory
2. Activate the theme through the 'Themes' menu in WordPress
3. Configure your homepage settings under Settings > Reading
4. Start adding Photo Gallery posts or use WordPress Gallery blocks in your posts

== Features ==

= Lightbox Functionality =
* Custom advanced lightbox for all gallery images
* Full-screen viewing with F11 key or fullscreen button
* EXIF data display (camera, lens, settings) in collapsible container
* Social sharing buttons in collapsible container
* Image navigation with keyboard support (arrow keys)
* Keyboard controls: Escape (close), Left/Right arrows (navigate), F11 (fullscreen)
* Perfect image fitting with optimized viewport sizing

= Photo Gallery Support =
* Custom Photo Gallery post type
* WordPress Gallery block enhancement
* Masonry grid layouts
* Thumbnail generation
* Mobile-responsive galleries

= Customization =
* Foundation CSS framework
* Custom CSS variables for easy color changes
* Responsive navigation
* Clean, minimal design
* Professional typography

== Frequently Asked Questions ==

= How do I create photo galleries? =
You can create galleries in two ways:
1. Use the custom Photo Gallery post type from your WordPress admin
2. Add WordPress Gallery blocks to any post or page - they'll automatically get lightbox functionality

= Does the theme support EXIF data? =
Yes! The theme displays EXIF data including camera model, lens, aperture, shutter speed, ISO, and more in the lightbox.

= Is the theme mobile responsive? =
Absolutely! The theme is built with a mobile-first approach using Foundation CSS framework.

= Can I customize the colors? =
Yes, you can modify the CSS variables in style.css or add custom CSS through the WordPress customizer.

== Changelog ==

= 1.0.5 =
* Added fullscreen viewing capability for lightbox images
* Fullscreen toggle button in navigation bar with green active state
* F11 key support for fullscreen toggle
* Enhanced keyboard navigation with fullscreen controls
* Cross-browser fullscreen API support (Chrome, Firefox, Safari, Edge)
* Automatic fullscreen state detection and button updates
* Mobile-responsive fullscreen button styling

= 1.0.4 =
* Redesigned nimble lightbox interface with collapsible information containers
* Images now perfectly fit browser viewport with improved sizing algorithm
* Added bottom navigation bar with "Image Data" and "Share" buttons
* EXIF data and sharing options now in collapsible containers with full-width display
* Enhanced mobile responsiveness with reorganized navigation layout
* Improved user experience with cleaner, less cluttered lightbox design

= 1.0.3 =
* Enhanced sample page removal - now removes from all menu types including fallback menus
* Fixed lightbox image overflow for portrait orientation images
* Added dynamic image sizing optimization based on viewport and aspect ratio
* Improved lightbox responsiveness with automatic resize handling
* Better viewport utilization preventing image cropping or scrolling

= 1.0.2 =
* Removed non-functional "Photo Metadata" meta box from post editor
* Streamlined admin interface - EXIF data is now automatically extracted from images
* Improved editor experience for content creators
* Changed theme name and folder structure to prevent overwriting previous versions
* Theme now installs alongside previous versions for safe testing

= 1.0.1 =
* Removed default "Sample Page" links from navigation menus
* Hide Photo Galleries section on homepage when no galleries exist
* Improved theme distribution packaging
* Enhanced documentation

= 1.0.0 =
* Initial release
* Advanced lightbox functionality
* WordPress Gallery block support
* Custom Photo Gallery post type
* EXIF data display
* Social sharing integration
* Responsive design
* Foundation CSS framework integration

== Credits ==

* Built with Foundation CSS Framework (https://get.foundation/)
* Icons from Font Awesome (https://fontawesome.com/)
* Developed by Yuval Zukerman

== Support ==

For support and documentation, please visit the theme's repository or contact the developer.

== Changelog ==

= 1.0.9 =
* New: Footer menu support - separate navigation location for footer links
* New: Page template (page.php) for displaying static pages properly
* New: Horizontal pagination styling with numbered page navigation
* New: Privacy policy filtering - automatically removes privacy policy from header menu
* Enhanced: Footer now displays navigation menu above copyright
* Enhanced: Menu filtering system to handle WordPress automatic page listings
* Improved: CSS styling for footer navigation with hover effects
* Improved: Pagination now uses flexbox for consistent horizontal layout
* Fixed: Pages now display correctly with dedicated template
* Fixed: Privacy policy link positioning between header and footer

= 1.0.8 =
* New: WordPress metadata integration - lightbox now displays image titles, captions, and alt text from WordPress attachment data
* Enhanced: Smart attachment detection handles WordPress scaled images (-scaled suffix)
* Improved: Conditional field display - metadata fields only appear when they have content
* Enhanced: Better filename pattern matching for attachment ID detection
* Fixed: Border styling consistency across all data fields in lightbox
* Updated: CSS optimizations for visual polish

= 1.0.7 =
* Enhanced lightbox functionality and EXIF data display

= 1.0.6 =
* Foundation CSS integration and responsive improvements

= 1.0.5 =
* Initial stable release with core functionality

== License ==

This theme is licensed under the GPL v2 or later.

== Warranty Disclaimer ==

THIS THEME IS PROVIDED "AS IS" WITHOUT WARRANTY OF ANY KIND, EITHER EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE IMPLIED WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE, AND NON-INFRINGEMENT. THE ENTIRE RISK AS TO THE QUALITY AND PERFORMANCE OF THE THEME IS WITH YOU.

IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE THEME OR THE USE OR OTHER DEALINGS IN THE THEME.

This includes, but is not limited to:
* Any damages resulting from the use or inability to use the theme
* Loss of data or profits
* Business interruption
* Any other commercial damages or losses

By installing and using this theme, you acknowledge that you have read this disclaimer and agree to use the theme at your own risk.
