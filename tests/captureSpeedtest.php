<?php declare( strict_types = 1 );

$id = $argv[1] ?? '';
$url = $argv[2] ?? '';

if ( PHP_VERSION_ID < 80000 ) {
	print "error: PHP 8.0+ required\n";
	exit( 1 );
}
if ( !$url || count( $argv ) !== 3 ) {
	print "usage: php {$argv[0]} <id> <url>\n";
	exit( 1 );
}

ini_set( 'user_agent', 'WmfCaptureSpeedtest-Bot <https://gerrit.wikimedia.org/g/operations/mediawiki-config/>' );

// phpcs:ignore MediaWiki.Files.ClassMatchesFilename.NotMatch
class WmfCaptureSpeedtest {
	private const KEY_URL = 0;
	private const KEY_ATTR = 1;
	private array $urls = [];
	private string $dir;
	private string $url;
	private array $expandUrl;

	public function __construct( string $dir, string $url ) {
		$this->dir = $dir;
		$this->url = $url;
		$this->expandUrl = [
			'scheme' => parse_url( $url, PHP_URL_SCHEME ),
			'host' => parse_url( $url, PHP_URL_HOST ),
		];
	}

	/**
	 * @param string $url HTML-encoded URL from an attribute
	 * @param string|null $defExt
	 */
	private function downloadUrlIfNew( string $url, string $defExt = null ): void {
		// Strip any query parameters
		$urlPath = parse_url( $url, PHP_URL_PATH );
		// Keep only a flat filename
		$name = basename( $urlPath );
		$ext = pathinfo( $name, PATHINFO_EXTENSION );
		// Optionally add a file extension
		// - Add .css to load.php stylesheet
		// - Add .png to extension-less CentralAutoLogin/start image
		// - Add .png to load.php?image=..&format=rasterized
		// - Add .svg to load.php?image=..&format=original
		if ( $defExt && ( !$ext || $ext === 'php' ) ) {
			if ( str_contains( $url, 'format=original' ) ) {
				$name .= '.svg';
			} else {
				$name .= '.' . $defExt;
			}
		}
		if ( isset( $this->urls[$name] ) && $this->urls[$name][self::KEY_ATTR] !== $url ) {
			// Make more unique if needed
			$uniq = hash( 'crc32b', $url );
			$name = pathinfo( $name, PATHINFO_FILENAME )
				. '_' . $uniq
				. '.' . pathinfo( $name, PATHINFO_EXTENSION );
		}
		if ( !isset( $this->urls[$name] ) ) {
			$decoded = html_entity_decode( $url );
			// Expand to include any missing protocol or hostname
			$expanded = parse_url( $decoded ) + $this->expandUrl;
			$fullUrl = $expanded['scheme'] . '://' . $expanded['host']
				. $expanded['path']
				. ( isset( $expanded['query'] ) ? "?{$expanded['query']}" : '' );

			$this->urls[$name] = [
				self::KEY_ATTR => $url,
				self::KEY_URL => $fullUrl,
			];

			$dest = "{$this->dir}/{$name}";
			if ( !is_file( $dest ) ) {
				print "... write $name from <$fullUrl>\n";
				$data = file_get_contents( $fullUrl );
				if ( $defExt === 'css' ) {
					$this->crawlCssResources( $data );
					$data = $this->rewriteUrls( $data );
				} elseif ( $defExt === 'js' ) {
					$data = $this->rewriteJs( $data );
				}
				file_put_contents( $dest, $data );
			}
		}
	}

	private function crawlStylesheets( string $data ): void {
		if ( preg_match_all( '/<link rel="stylesheet" href="([^"]+)"/', $data, $m ) ) {
			foreach ( $m[1] as $url ) {
				$this->downloadUrlIfNew( $url, 'css' );
			}
		}
	}

	private function crawlScripts( string $data ): void {
		if ( preg_match_all( '/<script[^>]*\s+src="([^"]+)"/', $data, $m ) ) {
			foreach ( $m[1] as $url ) {
				$this->downloadUrlIfNew( $url, 'js' );
			}
		}
	}

	private function crawlImages( string $data ): void {
		if ( preg_match_all( '/<img[^>]*\s+src="([^"]+)"/', $data, $m ) ) {
			foreach ( $m[1] as $url ) {
				$this->downloadUrlIfNew( $url, 'png' );
			}
		}
		if ( preg_match_all( '/<img[^>]*\s+srcset="([^"]+)"/', $data, $im ) ) {
			foreach ( $im[1] as $srcset ) {
				if ( preg_match_all( '/(\S+)\s+[\d.]+x/', $srcset, $sm ) ) {
					foreach ( $sm[1] as $url ) {
						$this->downloadUrlIfNew( $url, 'png' );
					}
				}
			}
		}
	}

	private function crawlCssResources( string $data ): void {
		// background:center top url(/w/skins/Vector/resources/common/images/search.svg?ac00d)
		// background:url("data:image/svg+xml,...")
		// background-image:transparent,url(/w/load.php?image=foo)
		if ( preg_match_all( '/[\s:,]url\(([^)]+)\)/', $data, $m ) ) {
			foreach ( $m[1] as $url ) {
				if ( str_starts_with( $url, '"' ) ) {
					$url = substr( $url, 1, -1 );
				}
				if ( str_starts_with( $url, 'data:' ) ) {
					continue;
				}
				$this->downloadUrlIfNew( $url, 'png' );
			}
		}
	}

	private function rewriteUrls( string $data ): string {
		$replacements = [];
		foreach ( $this->urls as $localName => $entry ) {
			$replacements[ $entry[self::KEY_ATTR] ] = './' . $localName;
		}
		return strtr( $data, $replacements );
	}

	private function rewriteJs( string $data ): string {
		// Disable second stage JS. This would change over time, making it not a static snapshot.
		// > mw.loader.load(window.RLPAGEMODULES||[]);
		return preg_replace( '/;mw.loader.load[^;]+;/', ';/* ignore RLPAGEMODULES */;', $data );
	}

	public function capture(): void {
		@mkdir( $this->dir );
		$html = file_get_contents( $this->url );
		$this->crawlStylesheets( $html );
		$this->crawlScripts( $html );
		$this->crawlImages( $html );
		$html = $this->rewriteUrls( $html );
		file_put_contents( "{$this->dir}/index.html", $html );
	}
}

$dir = dirname( __DIR__ ) . '/docroot/wikipedia.org/speed-tests/' . $id;
$cli = new WmfCaptureSpeedtest( $dir, $url );
$cli->capture();
