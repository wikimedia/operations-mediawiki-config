#!/usr/bin/env python3
"""
Eases management of Wikimedia site logos
Copyright (C) 2021 Kunal Mehta <legoktm@member.fsf.org>

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <https://www.gnu.org/licenses/>.
"""

import argparse
import subprocess
import sys
import textwrap
import os
from math import ceil

from pathlib import Path
from typing import Optional

import requests
import yaml
import xml.etree.ElementTree as ET


if sys.version_info < (3, 7):
    raise RuntimeError("You must use Python 3.7+ to run this script")

DIR = Path(__file__).parent
project_logos = DIR.parent / "static/images/project-logos"
project_svgs = DIR.parent / "static/images/mobile/copyright"


def validate_commons(name: str, value: str):
    if not value.startswith("File:"):
        raise RuntimeError(f"{name}: 'commons:' must start with File:")
    if not value.endswith(".svg"):
        raise RuntimeError(f"{name}: 'commons:' must be a SVG file")


def validate(data: dict):
    # TODO: Remove some duplication with make_block()
    seen = set()
    for group, sites in data.items():
        for site, info in sites.items():
            if site in seen:
                raise RuntimeError(f"{site} is present multiple times.")
            seen.add(site)
            if info is None:
                # default values
                info = {}
            if "commons" in info:
                validate_commons(site, info["commons"])
            if "variants" in info:
                for variant_name, variant_commons in info["variants"].items():
                    if not variant_name.startswith(site):
                        # Variant name must start with site name
                        raise RuntimeError(f"{site}: variant {variant_name} must start with {site}")
                    validate_commons(f"{site} (variant: {variant_name})", variant_commons)
            for size in ["1x", "1_5x", "2x"]:
                if size != "1x" and info.get(f"no_{size}"):
                    # Skip, doesn't have this size
                    continue
                if size == "1x":
                    path_size = ""
                else:
                    path_size = "-" + size.replace("_", ".")
                selected = info.get("selected", site)
                filename = f"{selected}{path_size}.png"
                if not (project_logos / filename).exists():
                    raise RuntimeError(f"{site}: {filename} doesn't exist!")
            for svg_type in ["wordmark", "tagline"]:
                if f"commons_{svg_type}" in info:
                    validate_commons(site, info[f"commons_{svg_type}"])
                if f"commons_{svg_type}" in info or f"selected_{svg_type}" in info:
                    selected = info.get(f"selected_{svg_type}", site)
                    proj, lang = transform_name(data, selected)
                    if lang is None:
                        name = f"{proj}-{svg_type}"
                    else:
                        name = f"{proj}-{svg_type}-{lang}"
                    filename = f"{name}.svg"
                    if not (project_svgs / filename).exists():
                        raise RuntimeError(f"Error: {filename} doesn't exist!")


def download(commons: str, name: str):
    # Check dependencies first
    for dep in ["pngquant", "zopflipng"]:
        try:
            subprocess.check_output([dep, "--help"])
        except subprocess.CalledProcessError:
            raise RuntimeError(f"Error: {dep} not installed")

    s = requests.Session()
    s.headers.update({
        "User-Agent": "logos-manage (https://gerrit.wikimedia.org/g/operations/mediawiki-config/+/HEAD/logos/manage.py)"
    })

    req = s.get(
        "https://commons.wikimedia.org/w/api.php",
        params={
            "action": "query",
            "prop": "imageinfo",
            "titles": commons,
            "iiprop": "url",
            "iilimit": "1",
            "iiurlwidth": "135",
            "format": "json",
            "formatversion": "2",
        }
    )
    req.raise_for_status()
    info = req.json()["query"]["pages"][0]["imageinfo"][0]
    if info["thumbheight"] > 155:
        raise RuntimeError(f"{commons}: logo is taller than 155px, please resize it")
    urls = {
        f"{name}.png": info["thumburl"],
        f"{name}-1.5x.png": info["responsiveUrls"]["1.5"].replace("203px", "202px"),
        f"{name}-2x.png": info["responsiveUrls"]["2"],
    }
    for filename, url in urls.items():
        req = s.get(url)
        req.raise_for_status()
        (project_logos / filename).write_bytes(req.content)
        print(f"Saved {filename}")
        subprocess.check_call(
            [
                "pngquant",
                "--skip-if-larger",
                "--speed",
                "3",
                "--quality",
                "80-100",
                filename,
                "--ext",
                ".png",
                "--force",
            ],
            cwd=project_logos,
        )
        subprocess.check_call(
            ["zopflipng", "--lossy_transparent", "-m", "-y", filename, filename],
            cwd=project_logos,
        )

def download_svg(commons: str, name: str, svg_type: str, data: dict):
    # Check dependencies first
    for dep in ["svgo", "rsvg-convert"]:
        try:
            subprocess.check_output([dep, "--help"])
        except subprocess.CalledProcessError:
            raise RuntimeError(f"Error: {dep} not installed")

    s = requests.Session()
    s.headers.update({
        "User-Agent": "logos-manage (https://gerrit.wikimedia.org/g/operations/mediawiki-config/+/HEAD/logos/manage.py)"
    })

    req = s.get(
        "https://commons.wikimedia.org/w/api.php",
        params={
            "action": "query",
            "prop": "imageinfo",
            "titles": commons,
            "iiprop": "url",
            "iilimit": "1",
            "format": "json",
            "formatversion": "2",
        }
    )
    req.raise_for_status()
    url = req.json()["query"]["pages"][0]["imageinfo"][0]["url"]

    req = s.get(url)
    req.raise_for_status()
    proj, lang = transform_name(data, name)
    name = ""
    if lang is None:
        name = f"{proj}-{svg_type}"
    else:
        name = f"{proj}-{svg_type}-{lang}"
    filename = f"{name}.svg"
    filename1 = f"{name}-tmp.svg"
    (project_svgs / filename).write_bytes(req.content)
    print(f"Saved {filename}")

    width, height = get_svg_size(filename)
    if width > 140 or height > 40:
        tmp_height = 140 * height / width
        if tmp_height > 40:
            new_width = 40 * width / height
            width = ceil(new_width)
            height = 40
        else:
            new_height = 140 * height / width
            height = ceil(new_height)
            width = 140
        print(f"File {filename} too wide or too tall, resizing to {width} x {height}")
        resize_svg(filename, filename1, str(width), str(height))

    subprocess.check_call(
        [
            "scour",
            "-i",
            filename,
            "-o",
            filename1,
            "--enable-id-stripping",
            "--enable-comment-stripping",
            "--shorten-ids",
            "--strip-xml-prolog",
            "--remove-descriptive-elements",
            "--create-groups",
            "--enable-viewboxing",
            "--set-c-precision=3",
            "--indent=none",
            "--no-line-breaks",
        ],
        cwd=project_svgs,
    )
    os.rename(project_svgs / filename1, project_svgs / filename)
    subprocess.check_call(
        ["svgo", "-i", filename, "-o", filename],
        cwd=project_svgs,
    )
    print("")


def make_block(size: str, data: dict):
    if size == "1x":
        path_size = ""
        comment_key = "comment"
    else:
        path_size = "-" + size.replace("_", ".")
        comment_key = f"comment_{size}"
    text = f"'wmgSiteLogo{size}' => [\n"
    for group, sites in data.items():
        text += f"\t// {group}\n"
        for site, info in sites.items():
            if info is None:
                # Default values
                info = {}
            if size != "1x" and info.get(f"no_{size}"):
                # Skip, doesn't have this size
                continue
            # Default to site name
            selected = info.get("selected", site)
            filename = f"{selected}{path_size}.png"
            if not (project_logos / filename).exists():
                raise RuntimeError(f"Error: {filename} doesn't exist!")
            url = f"/static/images/project-logos/{filename}"
            if comment_key in info:
                comment = f" // {info[comment_key]}"
            else:
                comment = ""
            text += f"\t'{site}' => '{url}',{comment}\n"
        text += "\n"
    text += "],\n\n"
    return text


def make_block2(svg_type: str, data: dict):
    commons_key = f"commons_{svg_type}"
    comment_key = f"comment_{svg_type}"
    selected_key = f"selected_{svg_type}"
    text = f"'wmgSiteLogo{svg_type.capitalize()}' => [\n"
    for group, sites in data.items():
        text += f"\t// {group}\n"
        for site, info in sites.items():
            if info is None:
                # Default values
                info = {}
            # "no_wordmark" to generate null value
            url = ""
            if f"no_{svg_type}" in info and info[f"no_{svg_type}"]:
                url = "null"
                text += f"\t'{site}' => {url},\n"
                continue
            if commons_key not in info and selected_key not in info:
                # Skip, doesn't have this type
                continue
            # It should not contains any variant, default to site name
            selected = info.get(f"selected_{svg_type}", site)
            proj, lang = transform_name(data, selected)
            if lang is None:
                name = f"{proj}-{svg_type}"
            else:
                name = f"{proj}-{svg_type}-{lang}"
            filename = f"{name}.svg"
            if not (project_svgs / filename).exists():
                raise RuntimeError(f"Error: {filename} doesn't exist!")
            url = f"/static/images/mobile/copyright/{filename}"
            width, height = get_svg_size(filename)
            width = ceil(width)
            height = ceil(height)
            # override width and height if specified
            if f"width_{svg_type}" in info:
                width = info[f"width_{svg_type}"]
            if f"height_{svg_type}" in info:
                height = info[f"height_{svg_type}"]
            if comment_key in info:
                comment = f" // {info[comment_key]}"
            else:
                comment = ""
            text += f"\t'{site}' => [{comment}\n"
            text += f"\t\t'src' => '{url}',\n"
            text += f"\t\t'width' => {width},\n"
            text += f"\t\t'height' => {height},\n\t],\n"
        text += "\n"
    text += "],\n\n"
    return text


def generate(data: dict):
    text = textwrap.dedent("""\
    <?php
    # WARNING: This file is publicly viewable on the web. Do not put private data here.

    # This file is automatically generated. DO NOT EDIT IT BY HAND.
    # See ../logos/README.md for instructions.
    #
    # Load tree:
    #  |-- wmf-config/InitialiseSettings.php
    #      |
    #      `-- wmf-config/logos.php
    #

    // Inline comments are often used for noting the task(s) associated with specific configuration
    // and requiring comments to be on their own line would reduce readability for this file
    // phpcs:disable MediaWiki.WhiteSpace.SpaceBeforeSingleLineComment.NewLineComment

    # ⚠️ IMPORTANT!!!! ⚠️
    # When updating these logos, please note that official Wikimedia logos should not be
    # used on labs instances.
    # Please check that any overrides in InitialiseSettings-labs.php work per instructions
    # at https://wikitech.wikimedia.org/wiki/Wikitech:Cloud_Services_Terms_of_use
    # When defining new wordmarks or taglines, ensure width <= 140px so that logos are
    # mobile friendly. Scale down them if necessary.

    return [
    """)
    for size in ["1x", "1_5x", "2x"]:
        text += make_block(size, data)
    for svg_type in ["wordmark", "tagline"]:
        text += make_block2(svg_type, data)
    text += "];\n"

    (DIR.parent / "wmf-config/logos.php").write_text(text)
    print("Updated logos.php")


def update(data: dict, wiki: str, variant: Optional[str]):
    info = None
    for group, sites in data.items():
        if wiki in sites:
            info = sites[wiki]
            if info is None:
                # Default values
                info = {}

    if info is None:
        raise RuntimeError(f"I can't find any configuration for {wiki}")
    name = wiki
    if "commons_wordmark" in info or "commons_tagline" in info:
        for svg_type in ["wordmark", "tagline"]:
            if f"commons_{svg_type}" in info:
                commons_svg = info[f"commons_{svg_type}"]
                download_svg(commons_svg, name, svg_type, data)
    if "commons" in info:
        commons = info["commons"]
        name = wiki
        if variant:
            try:
                commons = info["variants"][variant]
                name = variant
            except KeyError:
                raise RuntimeError(f"Cannot find variant {variant} for site {wiki}")
        download(commons, name)
    elif "commons_wordmark" not in info and "commons_tagline" not in info \
            and "selected_wordmark" not in info and "selected_tagline" not in info:
        raise RuntimeError(
            "The update function can only be used if a 'commons' SVG is present in config.yaml"
        )

    # Regenerate
    generate(data)


def transform_name(data: dict, name: str):
    # "zhwikiquote" -> ("wikiquote", "zh"), for wordmark/tagline file naming
    projects = list(data["Projects"].keys())
    projects.extend(["wiki", "wikimedia"])
    specials = list(data["Special wikis"].keys())
    if name in projects:
        return name, None
    if name in specials:
        if name.endswith("wiki"):
            return name[:-4], None
        else:
            return name, None
    for project in projects:
        if name.endswith(project):
            if project == "wiki":
                project = "wikipedia"
                return project, name[: -len("wiki")]
            return project, name[: -len(project)]
    raise RuntimeError(f"Cannot find project for {name}")


def get_svg_size(filename: str):
    with open(project_svgs / filename, "r") as f:
        svg = f.read()
        attr = ET.fromstring(svg).attrib
        width = attr["width"] if "width" in attr else None
        height = attr["height"] if "height" in attr else None
        viewbox = attr["viewBox"] if "viewBox" in attr else None
        if width is None and height is None and viewbox is None:
            raise RuntimeError(f"{filename}: file doesn't have width, height or viewBox")
        # Some optimized svg files don't have "width" and "height" attributes,
        # so extract them from viewBox and store them for future use
        if width is None or height is None or \
                not width.replace('.','',1).isdigit() or not height.replace('.','',1).isdigit():
                # some svg files has "width" and "height" with unit "pt" or "mm"
            width, height = viewbox.split(" ")[2:]

        return float(width), float(height)


def resize_svg(filename: str, filename1: str, width: str, height: str):
    subprocess.run(
        [
            "rsvg-convert",
            "-a",
            "-w",
            width,
            "-h",
            height,
            "-f",
            "svg",
            "-o",
            filename1, # tmp file
            filename,
        ],
        check=True,
        cwd=project_svgs,
    )
    os.rename(project_svgs / filename1, project_svgs / filename)


def main():
    parser = argparse.ArgumentParser(description="Manage Wikimedia site logos")
    subparsers = parser.add_subparsers(
        dest="action", required=True, help="action to execute"
    )
    subparsers.add_parser("generate", help="Generate logos.php")
    subparsers.add_parser("validate", help="Validate config.yaml")
    update_parser = subparsers.add_parser("update", help="Update a wiki's logos")
    update_parser.add_argument("wiki", help="Wiki to update")
    update_parser.add_argument("--variant", required=False, help="Variant to update")
    args = parser.parse_args()

    data = yaml.safe_load((DIR / "config.yaml").read_text())

    if args.action == "generate":
        validate(data)
        generate(data)
    elif args.action == "update":
        update(data, args.wiki, args.variant)
        validate(data)
    elif args.action == "validate":
        validate(data)


if __name__ == "__main__":
    main()
