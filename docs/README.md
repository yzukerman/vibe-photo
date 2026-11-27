# Vibe Photo project website

A buildless GitHub Pages site. HTML and CSS live here with the locally bundled Oranienbaum font and the existing GPL-licensed portfolio screenshot. No JavaScript, external fonts, analytics, or package installation is required.

## Preview

From the repository root:

```sh
python3 -m http.server 10091 --bind 127.0.0.1 --directory docs
```

Open http://127.0.0.1:10091/ . Stop the server with Ctrl+C when finished.

## Publish on GitHub Pages

1. Merge this directory into `main` and push it to GitHub.
2. Open the repository's **Settings → Pages**.
3. Under **Build and deployment**, select **Deploy from a branch**.
4. Choose **main**, select **/docs**, and save.
5. Check the Pages deployment under Actions. When successful, the site will be at https://yzukerman.github.io/vibe-photo/ .

The `.nojekyll` file keeps this a plain static site. Relative stylesheet/image/font URLs work below the project URL. GitHub Pages hosts this project information site; the working WordPress demonstration stays at https://yuvalzukerman.com/ .

## Update downloads

The download links in `index.html` point to the theme 1.3 and plugin 1.0.1 ZIPs at the fixed commit `6c3ae8a15d2a5e990167e8503d69a5c60f97d9b4`. They do not depend on an unmerged or subsequently deleted branch. For future releases, update the version labels, requirements, and both URLs to a commit containing the verified packages (or to published GitHub Release assets).

## Assets and validation

- `assets/portfolio.jpg`: the existing theme screenshot, including its photographs, © 2026 Yuval Zukerman; GPL v2 or later. This permission covers the screenshot, not separately distributed original photographs.
- `assets/Oranienbaum-Regular.ttf`: Oranienbaum, SIL Open Font License 1.1; full license in `assets/OFL.txt`. See the theme's `ASSETS.md` for provenance.
- New page HTML/CSS: GPL v2 or later, consistent with this repository.

Checked desktop and narrow mobile rendering, screenshot aspect ratio, absence of horizontal overflow, native FAQ expansion, local asset responses, in-page link targets, and download endpoints. The website files do not change the WordPress theme/plugin distributions or their version numbers.
