#!/bin/bash

# Vibe Photo Theme Packaging Script
# This script creates a distribution-ready ZIP file of the theme

echo "🎨 Packaging Vibe Photo Theme for distribution..."

# Set variables
THEME_NAME="vibe-photo-theme"
VERSION="1.20.0.2"
VERSIONED_THEME_NAME="${THEME_NAME}-v${VERSION}"
PACKAGE_NAME="${VERSIONED_THEME_NAME}"
BUILD_DIR="build"
DIST_DIR="dist"

# Create build directory
echo "📁 Creating build directory..."
mkdir -p $BUILD_DIR
mkdir -p $DIST_DIR

# Copy theme files (excluding development files)
echo "📋 Copying theme files..."
rsync -av --exclude='*.log' \
         --exclude='*.tmp' \
         --exclude='.DS_Store' \
         --exclude='Thumbs.db' \
         --exclude='debug-gallery.php' \
         --exclude='node_modules/' \
         --exclude='.git/' \
         --exclude='.gitignore' \
         --exclude='package*.json' \
         --exclude='*.scss' \
         --exclude='*.sass' \
         --exclude='*.map' \
         --exclude='gulpfile.js' \
         --exclude='webpack.config.js' \
         --exclude='dist/' \
         $THEME_NAME/ $BUILD_DIR/$VERSIONED_THEME_NAME/

# Create ZIP file
echo "📦 Creating distribution ZIP file..."
cd $BUILD_DIR
zip -r "../${DIST_DIR}/${PACKAGE_NAME}.zip" $VERSIONED_THEME_NAME/
cd ..

# Cleanup
echo "🧹 Cleaning up..."
rm -rf $BUILD_DIR

echo "✅ Theme packaged successfully!"
echo "📍 Location: ${DIST_DIR}/${PACKAGE_NAME}.zip"
echo ""
echo "🚀 Installation Instructions:"
echo "1. Upload ${PACKAGE_NAME}.zip to WordPress admin"
echo "2. Go to Appearance > Themes > Add New > Upload Theme"
echo "3. Choose the ZIP file and install"
echo "4. Activate the theme"
echo ""
echo "📝 Files included in package:"
echo "   ✓ style.css (theme header)"
echo "   ✓ index.php (main template)"
echo "   ✓ front-page.php (homepage)"
echo "   ✓ functions.php (theme functions)"
echo "   ✓ Template files (single, archive)"
echo "   ✓ Assets (CSS, JS, images)"
echo "   ✓ README files"
echo "   ✓ screenshot.png"
