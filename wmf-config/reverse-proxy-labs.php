<?php
# WARNING: This file is publicly viewable on the web. Do not put private data here.

# our text CDN in beta labs gets forwarded requests
# from the ssl terminator, to 127.0.0.1, so adding that
# address to the NoPurge list so that XFF headers for it
# will be stripped; purge requests should get to it
# on the address in the CdnServers list

$wgCdnServersNoPurge = [
	'127.0.0.1',
	// deployment-cache-text07
	'172.16.0.113',
	// deployment-cache-upload07
	'172.16.0.188',
	// deployment-cache-upload08
	'172.16.3.146',
];
