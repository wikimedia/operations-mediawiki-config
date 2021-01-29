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

from pathlib import Path

import requests
import yaml


if sys.version_info < (3, 7):
    raise RuntimeError("You must use Python 3.7+ to run this script")

DIR = Path(__file__).parent
project_logos = DIR.parent / "static/images/project-logos"


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
                if not info["commons"].startswith("File:"):
                    raise RuntimeError(f"{site}: 'commons:' must start with File:")
                if not info["commons"].endswith(".svg"):
                    raise RuntimeError(f"{site}: 'commons:' must be a SVG file")
            for size in ["1x", "1_5x", "2x"]:
                if size != "1x" and info.get(f"no_{size}"):
                    # Skip, doesn't have this size
                    continue
                if size == "1x":
                    path_size = ""
                else:
                    path_size = "-" + size.replace("_", ".")
                variant = info.get("variant", site)
                filename = f"{variant}{path_size}.png"
                if not (project_logos / filename).exists():
                    raise RuntimeError(f"{site}: {filename} doesn't exist!")


def download(commons: str, variant: str):
    # Check dependencies first
    for dep in ["pngquant", "zopflipng"]:
        try:
            subprocess.check_output([dep, "--help"])
        except subprocess.CalledProcessError:
            raise RuntimeError(f"Error: {dep} not installed")

    req = requests.get(
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
        },
        headers={
            "User-Agent": "logos-manage (https://gerrit.wikimedia.org/g/operations/mediawiki-config/+/HEAD/logos/manage.py)"
        }
    )
    req.raise_for_status()
    info = req.json()["query"]["pages"][0]["imageinfo"][0]
    urls = {
        f"{variant}.png": info["thumburl"],
        f"{variant}-1.5x.png": info["responsiveUrls"]["1.5"].replace("203px", "202px"),
        f"{variant}-2x.png": info["responsiveUrls"]["2"],
    }
    for filename, url in urls.items():
        req = requests.get(url)
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
        # TODO: pngout step


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
            variant = info.get("variant", site)
            filename = f"{variant}{path_size}.png"
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

    return [
    """)
    for size in ["1x", "1_5x", "2x"]:
        text += make_block(size, data)
    text += "];\n"

    (DIR.parent / "wmf-config/logos.php").write_text(text)
    print("Updated logos.php")


def update(data: dict, wiki: str):
    info = None
    for group, sites in data.items():
        if wiki in sites:
            info = sites[wiki]
            if info is None:
                # Default values
                info = {}

    if info is None:
        raise RuntimeError(f"I can't find any configuration for {wiki}")
    variant = info.get("variant", wiki)
    if "commons" not in info:
        raise RuntimeError(
            "The update function can only be used if a 'commons' SVG is present in config.yaml"
        )
    download(info["commons"], variant)

    # Regenerate
    generate(data)


def main():
    parser = argparse.ArgumentParser(description="Manage Wikimedia site logos")
    subparsers = parser.add_subparsers(
        dest="action", required=True, help="action to execute"
    )
    subparsers.add_parser("generate", help="Generate logos.php")
    subparsers.add_parser("validate", help="Validate config.yaml")
    update_parser = subparsers.add_parser("update", help="Update a wiki's logos")
    update_parser.add_argument("wiki", help="Wiki to update")
    args = parser.parse_args()

    data = yaml.safe_load((DIR / "config.yaml").read_text())

    if args.action == "generate":
        validate(data)
        generate(data)
    elif args.action == "update":
        update(data, args.wiki)
        validate(data)
    elif args.action == "validate":
        validate(data)


if __name__ == "__main__":
    main()
