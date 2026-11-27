# Vibe Photo 1.3 release validation

Date: 2026-11-27. Branch: wp-submission-prep.

## Packages

- Theme: vibe-photo-v1.3.zip; installation folder vibe-photo.
- Companion plugin: vibe-photo-galleries-v1.0.0.zip; installation folder vibe-photo-galleries.
- Install each separately. Theme works without the plugin.

## Tested environment

Official WordPress 6.7.1 archive, Local-bundled PHP 8.2.29, Local MySQL service, isolated files/database. Packages installed into clean directories rather than repository symlinks. Current development site was not downgraded.

## Results

- Theme-alone homepage/gallery cover fallback: PASS.
- Gallery registration, shortcode, cover, and query-isolation smoke tests: PASS.
- Security/path/key/translation smoke tests: PASS.
- Populated, paginated, empty fallback templates: PASS.
- Plugin-provided legacy gallery covers and archive: PASS.
- Metadata AJAX: PASS.
- Fullscreen delayed/failed/racing-close lifecycle tests: 4 PASS.
- Live fullscreen close/focus restoration: passed on current Local and historical 6.7.1 installation.
- Theme Check: no REQUIRED failures or WARNING entries. Optional patterns/styles/header/background/widget recommendations remain.
- WordPress debug log: no entries during final package checks.
- ZIP integrity, stable folder names, license/assets/catalog inclusion: PASS.

## Limits and next actions

No WordPress.org submission or blog deployment has been performed. Directory review may require further changes. Other PHP versions, a full Theme Unit Test visual review, broad real-device/browser coverage, and non-English visual tests have not been completed. Google API integration was not deliberately exercised. The theme plugin-data boundary now uses optional filters rather than reading plugin metadata directly.

For WordPress.org, submit the theme ZIP separately. The plugin requires its own directory review before the theme can recommend it as a directory plugin. Theme submission version 1.3 sorts below historical manually distributed 1.20.x versions; do not assume automatic upgrades for those old installation slugs.
