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
project_logos_path = "static/images/project-logos"
project_svgs_path = "static/images/mobile/copyright"
project_icons_path = "static/images/icons"
project_logos = DIR.parent / project_logos_path
project_svgs = DIR.parent / project_svgs_path
project_icons = DIR.parent / project_icons_path


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
                for variant_name, variant_info in info["variants"].items():
                    if not variant_name.startswith(site + "-"):
                        # Variant name must start with site name
                        raise RuntimeError(f"{site}: variant {variant_name} must " \
                            "start with {site}, connected with a dash")
                    for variant_type, variant_commons in variant_info.items():
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


def download_svg(commons: str, name: str, svg_type: str, data: dict, variant=None):
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
    wiki = name
    proj, lang = transform_name(data, name)
    name = ""
    if lang is None:
        name = f"{proj}-{svg_type}"
    else:
        name = f"{proj}-{svg_type}-{lang}"
    if variant is not None:
        # "zhwiki-hans" -> "hans"
        name = f"{name}-{variant[len(wiki) + 1:]}"
    filename = f"{name}.svg"
    (project_svgs / filename).write_bytes(req.content)
    print(f"Saved {svg_type} {filename}")

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
        resize_svg(filename, str(width), str(height))

    optimize_svg(filename)


def download_icon(commons: str, name: str, data: dict, variant=None):
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
    
    wiki = name
    if variant is not None:
        name = f"{name}-{variant[len(wiki) + 1:]}"
    filename = f"{name}.svg"

    (project_icons / filename).write_bytes(req.content)
    print(f"Saved icon {filename}")

    width, height = get_svg_size(filename, project_icons)
    if width != height:
        raise RuntimeError(f"Icon {filename} is not square")
    if width > 100:
        width = height = 100
        print(f"File {filename} too big, resizing to {width} x {height}")
        resize_svg(filename, str(width), str(height), project_icons)

    optimize_svg(filename, project_icons)


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
            url = f"/{project_logos_path}/{filename}"
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
    local_key = f"local_{svg_type}"
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
                text += f"\t'{site}' => {url},"
                if comment_key in info:
                    text += f" // {info[comment_key]}"
                text += "\n"
                continue
            if commons_key not in info and selected_key not in info and local_key not in info:
                # Skip, doesn't have this type
                continue
            # It should not contains any variant, default to site name
            selected = info.get(f"selected_{svg_type}", site)
            proj, lang = transform_name(data, selected)
            if lang is None:
                name = f"{proj}-{svg_type}"
            else:
                name = f"{proj}-{svg_type}-{lang}"
            if "variants" in info and "selected" in info:
                # If selected is set, it should be a variant
                variant = info["selected"]
                if commons_key in info["variants"][variant]:
                    name = f"{name}-{variant[len(site) + 1:]}"
            filename = f"{name}.svg"
            if not (project_svgs / filename).exists():
                raise RuntimeError(f"Error: {filename} doesn't exist!")
            url = f"/{project_svgs_path}/{filename}"
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


def make_block_icon(data: dict):
    commons_key = "commons_icon"
    local_key = "local_icon"
    comment_key = "comment_icon"
    selected_key = "selected_icon"
    text = f"'wmgSiteLogoIcon' => [\n"
    for group, sites in data.items():
        text += f"\t// {group}\n"
        for site, info in sites.items():
            if info is None:
                # Default values
                info = {}
            url = ""
            if "no_icon" in info and info["no_icon"]:
                url = "null"
                text += f"\t'{site}' => {url},"
                if comment_key in info:
                    text += f" // {info[comment_key]}"
                text += "\n"
                continue
            if commons_key not in info and local_key not in info and selected_key not in info \
                and not ("variants" in info and "selected" in info \
                    and commons_key in info["variants"][info["selected"]]):
                continue
            name = info.get(selected_key, site)
            if "variants" in info and "selected" in info:
                # If selected is set, it should be a variant
                variant = info["selected"]
                if commons_key in info["variants"][variant]:
                    name = f"{name}-{variant[len(site) + 1:]}"
            ext = "svg"
            if "no_svg_icon" in info and info["no_svg_icon"]:
                ext = "png"
            filename = f"{name}.{ext}"
            if not (project_icons / filename).exists():
                raise RuntimeError(f"Error: {filename} doesn't exist!")
            url = f"/{project_icons_path}/{filename}"
            if comment_key in info:
                comment = f" // {info[comment_key]}"
            else:
                comment = ""
            text += f"\t'{site}' => '{url}',{comment}\n"
        text += "\n"
    text += "],\n\n"
    return text


def make_block_lang_variant(data: dict):
    text = "'wmgSiteLogoVariants' => [\n"
    for group, sites in data.items():
        text += f"\t// {group}\n"
        for site, info in sites.items():
            if info is None:
                info = {}
            if "lang_variants" not in info:
                continue
            text += f"\t'{site}' => [\n"
            comment = ""
            if "comment_lang_variants" in info:
                comment = f" // {info['comment_lang_variants']}"
            for lang, lang_info in info["lang_variants"].items():
                text += f"\t\t'{lang}' => [{comment}\n"
                if "selected_lang" in lang_info:
                    text += make_block_lang_single(site,
                        lang_info["selected_lang"],
                        info["lang_variants"][lang_info["selected_lang"]],
                        data)
                else:
                    text += make_block_lang_single(site, lang, lang_info, data)
                text += "\t\t],\n"
            text += "\t],\n"
        text += "\n"
    text += "],\n\n"
    return text


def make_block_lang_single(site: str, lang: str, lang_info: dict, data: dict):
    text = ""
    if "selected_logo" in lang_info:
        selected = lang_info["selected_logo"]
        for size in ["1x", "1.5x", "2x"]:
            size1 = "" if size == "1x" else f"-{size}"
            filename = f"{selected}{size1}.png"
            if not (project_logos / filename).exists():
                raise RuntimeError(f"Error: {filename} doesn't exist!")
            url = f"/{project_logos_path}/{selected}{size1}.png"
            text += f"\t\t\t'{size}' => '{url}',\n"
    for svg_type in ["wordmark", "tagline"]:
        if f"selected_{svg_type}" in lang_info:
            proj, lang = transform_name(data, site)
            name = ""
            if lang is None:
                name = f"{proj}-{svg_type}"
            else:
                name = f"{proj}-{svg_type}-{lang}"
            name += f"-{lang_info[f'selected_{svg_type}'][len(site) + 1:]}"
            filename = f"{name}.svg"
            if not (project_svgs / filename).exists():
                raise RuntimeError(f"Error: {filename} doesn't exist!")
            url = f"/{project_svgs_path}/{filename}"
            width, height = get_svg_size(filename)
            width = ceil(width)
            height = ceil(height)
            text += f"\t\t\t'{svg_type}' => [\n"
            text += f"\t\t\t\t'src' => '{url}',\n"
            text += f"\t\t\t\t'width' => {width},\n"
            text += f"\t\t\t\t'height' => {height},\n\t\t\t],\n"
    if "selected_icon" in lang_info:
        selected = lang_info["selected_icon"]
        filename = f"{selected}.svg"
        if not (project_icons / filename).exists():
            raise RuntimeError(f"Error: {filename} doesn't exist!")
        url = f"/{project_icons_path}/{filename}"
        text += f"\t\t\t'icon' => '{url}',\n"
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

    // NOTE: These lists are ordered by *project family* for ease of maintenance.
    // The order is: Wikipedia, Wiktionary, Wikiquote, Wikibooks, Wikinews, Wikisource,
    // Wikiversity, Wikivoyage, chapter wikis, and finally special wikis

    // Inline comments are often used for noting the task(s) associated with specific configuration
    // and requiring comments to be on their own line would reduce readability for this file
    // phpcs:disable MediaWiki.WhiteSpace.SpaceBeforeSingleLineComment.NewLineComment

    # ⚠️ IMPORTANT!!!! ⚠️
    # When updating these logos, please note that official Wikimedia logos should not be
    # used on labs instances.
    # Please check that any overrides in InitialiseSettings-labs.php work per instructions
    # at https://wikitech.wikimedia.org/wiki/Wikitech:Cloud_Services_Terms_of_use
    #
    # When defining new wordmarks or taglines, ensure width <= 140px so that logos are
    # mobile friendly. Scale down them if necessary.

    return [
    """)
    for size in ["1x", "1_5x", "2x"]:
        text += make_block(size, data)
    for svg_type in ["wordmark", "tagline"]:
        text += make_block2(svg_type, data)
    text += make_block_icon(data)
    text += make_block_lang_variant(data)
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
    for svg_type in ["wordmark", "tagline"]:
        if f"commons_{svg_type}" in info:
            commons_svg = info[f"commons_{svg_type}"]
            if variant:
                try:
                    if f"commons_{svg_type}" in info["variants"][variant]:
                        commons_svg = info["variants"][variant][f"commons_{svg_type}"]
                        download_svg(commons_svg, name, svg_type, data, variant)
                except KeyError:
                    raise RuntimeError(f"Cannot find variant {variant} for site {wiki}")
            else:
                download_svg(commons_svg, name, svg_type, data)
    if "commons_icon" in info:
        commons_icon = info["commons_icon"]
        download_icon(commons_icon, name, data)
    if variant and "commons_icon" in info["variants"][variant]:
        commons_icon = info["variants"][variant]["commons_icon"]
        download_icon(commons_icon, name, data, variant)
    if "commons" in info:
        commons = info["commons"]
        if variant:
            try:
                commons = info["variants"][variant]["commons"]
                name = variant
            except KeyError:
                raise RuntimeError(f"Cannot find variant {variant} for site {wiki}")
        download(commons, name)
    elif "commons_wordmark" not in info and "commons_tagline" not in info \
            and "local_wordmark" not in info and "local_tagline" not in info \
            and "selected_wordmark" not in info and "selected_tagline" not in info \
            and "commons_icon" not in info and "local_icon" not in info \
            and "selected_icon" not in info:
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
        return name, None
    for project in projects:
        if name.endswith(project):
            if project == "wiki":
                project = "wikipedia"
                return project, name[: -len("wiki")]
            return project, name[: -len(project)]
    raise RuntimeError(f"Cannot find project for {name}")


def get_svg_size(filename: str, dir=project_svgs):
    with open(dir / filename, "r") as f:
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


def resize_svg(filename: str, width: str, height: str, dir=project_svgs):
    filename1 = "!" + filename
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
        cwd=dir,
    )
    os.rename(dir / filename1, dir / filename)


def optimize_svg(filename: str, dir=project_svgs):
    filename1 = "!" + filename
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
        cwd=dir,
    )
    os.rename(dir / filename1, dir / filename)
    subprocess.check_call(
        ["svgo", "-i", filename, "-o", filename],
        cwd=dir,
    )
    print("")


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
