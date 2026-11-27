# Bundled presentation assets

These files are included in theme installation ZIPs; the browser loads them from the WordPress site. No CDN account, API key, or external font request is required.

## Foundation Sites 6.8.1

- Copyright: 2011–2020 ZURB, Inc., as stated in the upstream license.
- License: MIT; complete text in `assets/vendor/foundation/LICENSE`.
- Project/source: https://github.com/foundation/foundation-sites/tree/v6.8.1
- Distribution CSS: https://cdn.jsdelivr.net/npm/foundation-sites@6.8.1/dist/css/foundation.min.css
- Distribution JavaScript: https://cdn.jsdelivr.net/npm/foundation-sites@6.8.1/dist/js/foundation.min.js
- Local files: `assets/vendor/foundation/foundation.min.css` and `foundation.min.js`.
- Modification: trailing source-map references removed because maps are not shipped. No runtime code or styling was intentionally changed.
- JavaScript uses WordPress's bundled jQuery. Embedded CSS data images are inline, not external requests.

## Oranienbaum Regular

- Copyright: 2012 Oleg Pospelov and Jovanny Lemonad; reserved font name Oranienbaum.
- License: SIL Open Font License 1.1; complete text and original copyright in `assets/fonts/oranienbaum/OFL.txt`.
- Source: https://github.com/google/fonts/tree/main/ofl/oranienbaum
- Original font: https://raw.githubusercontent.com/google/fonts/main/ofl/oranienbaum/Oranienbaum-Regular.ttf
- Local file: `assets/fonts/oranienbaum/Oranienbaum-Regular.ttf`, unmodified TrueType font.
- The SHA-256 manifest below identifies the exact bundled files, including the font snapshot retrieved on 2026-11-27. The upstream font URL is not a pinned release.
- `assets/fonts/fonts.css` defines the local face with `font-display: swap`. Existing Georgia/serif fallbacks remain.

## Theme code and original assets

Theme code is copyright © 2025–2026 Yuval Zukerman, licensed under GNU GPL v2 or later. The complete GPL v2 text is included in `LICENSE`; the “or later” grant is stated in the theme headers and documentation. Third-party resources retain the licenses listed above.

## Screenshot photographs

- File: `screenshot.jpg` (1200 × 900 JPEG).
- Attribution: **Photographs and screenshot © 2026 Yuval Zukerman**.
- Source: https://yuvalzukerman.com/page/2/ ; owner-supplied `yuvalzukerman.com_page_2.jpg`.
- License: **GNU General Public License v2 or later**, with full text in `LICENSE`.
- On 2026-11-27, Yuval Zukerman confirmed ownership and copyright of all six photographs shown and authorized GPL-v2-or-later distribution of the screenshot, including the photographs as reproduced within it.
- This grant covers the distributed screenshot and its depicted photograph reproductions. It does not separately offer the original full-resolution photograph files under that license; those files are not bundled.
- The owner confirmed there are no additional uncredited third-party images, icons, or copied code beyond the dependencies already identified. This records the owner's confirmation, not an independent provenance audit.

## Scope

The resource inventory includes the bundled Foundation framework, Oranienbaum font, original theme code, and screenshot imagery. Optional Google Geocoding is a service used by the separate plugin; service use does not change these bundled asset licenses. WordPress provides jQuery; it is not copied into either package.

## SHA-256 checksums

843c77d20bdacda0cf3d51773abd90b24abbd26283e89f44c6a65ef820a5622f  Themes/vibe-photo-theme/assets/vendor/foundation/LICENSE
fbe253a8637a0d7bdffd488151617505beda42a58fa7e364d412429ebfafd6d5  Themes/vibe-photo-theme/assets/vendor/foundation/foundation.min.css
fb84c759678f5312d00a1effc7018f5769d73dd2632e6c34481a7e10e00f3b28  Themes/vibe-photo-theme/assets/vendor/foundation/foundation.min.js
d941c5b788d47aa469ee54e30f4c12d6a43c45a295f10c568bdf9772f2893b7b  Themes/vibe-photo-theme/assets/fonts/oranienbaum/OFL.txt
d0c2527f106ddaf53bebce08e903b585d1fd1d830eddd23ed723c313b4fe2c6c  Themes/vibe-photo-theme/assets/fonts/oranienbaum/Oranienbaum-Regular.ttf

0360b70f84b3d5f5625592ff93f7d0454a10f032795fb67c53ec67e1aee56a33  Themes/vibe-photo-theme/screenshot.jpg
