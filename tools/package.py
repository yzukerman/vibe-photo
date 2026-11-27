#!/usr/bin/env python3
"""Build separate, installable theme/plugin ZIPs; never overwrite a release."""
from pathlib import Path
import argparse
import re
import zipfile

ROOT = Path(__file__).resolve().parents[1]
parser = argparse.ArgumentParser(description=__doc__)
parser.add_argument('component', choices=['theme', 'plugin'])
parser.add_argument('--output', type=Path, default=ROOT / 'dist')
args = parser.parse_args()
is_theme = args.component == 'theme'
source = ROOT / ('Themes/vibe-photo-theme' if is_theme else 'Plugins/vibe-photo-galleries')
slug = 'vibe-photo' if is_theme else 'vibe-photo-galleries'
header = source / ('style.css' if is_theme else 'vibe-photo-galleries.php')
version = re.search(r'^\s*(?:\* )?Version:\s*(\S+)', header.read_text(), re.M).group(1)
args.output.mkdir(parents=True, exist_ok=True)
archive = args.output / f'{slug}-v{version}.zip'
with zipfile.ZipFile(archive, 'x', zipfile.ZIP_DEFLATED) as bundle:
    for path in sorted(source.rglob('*')):
        relative = path.relative_to(source)
        if not path.is_file() or any(part.startswith('.') or part in {'node_modules', 'dist'} for part in relative.parts):
            continue
        if path.name in {'debug-gallery.php', 'clear-location-cache.php', 'Thumbs.db'} or path.suffix in {'.log', '.tmp', '.map', '.zip'}:
            continue
        bundle.write(path, Path(slug) / relative)
with zipfile.ZipFile(archive) as bundle:
    if bundle.testzip():
        raise RuntimeError('ZIP integrity check failed')
print(archive)
