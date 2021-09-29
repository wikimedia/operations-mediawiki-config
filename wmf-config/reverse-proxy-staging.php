<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

# our text CDN in beta labs gets forwarded requests
# from the ssl terminator, to 127.0.0.1, so adding that
# address to the NoPurge list so that XFF headers for it
# will be stripped; purge requests should get to it
# on the address in the CdnServers list

$wgCdnServersNoPurge = [
	'127.0.0.1',
	'172.16.1.181',  # deployment-cache-text06
	'172.16.1.160',  # deployment-cache-upload06
];
