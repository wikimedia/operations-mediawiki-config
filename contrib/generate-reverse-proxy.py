#!/usr/bin/python3
# SPDX-License-Identifier: Apache-2.0
import argparse
import re
from pathlib import Path

import yaml


# regex to match rack/row subnets
# this is a hack to filter out lvs/kube/analytics/etc networks
NET_REGEX = re.compile(r"^private\d(-\w{1,2}\d{0,2})?-\w{5}$")


def main():
    parser = argparse.ArgumentParser()
    parser.add_argument(
        "data_file",
        type=Path,
        help="Path to modules/network/data/data.yaml in a clone of operations/puppet.git",
    )
    args = parser.parse_args()

    data = yaml.safe_load(args.data_file.read_text())
    for site, realms in data["network::subnets"]["production"].items():
        print(f"	## {site}")
        for name, nets in sorted(realms["private"].items()):
            if not NET_REGEX.match(name):
                continue

            print(f"	'{nets['ipv4']}', # {name}")
            print(f"	'{nets['ipv6']}', # {name}")
        print("")


if __name__ == "__main__":
    main()
