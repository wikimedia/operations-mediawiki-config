<?php
/**
 * Entry point for wikidata redirects based on site link (site id and page title)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @file
 *
 * @licence GNU GPL v2+
 * @author Katie FIlbert < aude.wiki@gmail.com >
 */
define( "MEDIAWIKI", true );

require_once( './MWVersion.php' );
include getMediaWiki( "includes/WebStart.php" );

class SiteExtractor {

	protected $siteGroupName;

	/**
	 * @param string $siteGroupName
	 */
	public function __construct( $siteGroupName ) {
		$this->siteGroupName = $siteGroupName;
	}

	/**
	 * @param string $serverName
	 *
	 * @return string
	 */
	protected function getSubdomain( $serverName ) {
		$subdomain = null;

		if ( preg_match( '/^(.*)\.' . 'cartowiki\.org$/', $serverName, $matches ) ) {
			$subdomain = $matches[1];
		}

		return $subdomain;
	}

	/**
	 * @param string $navId
	 *
	 * @return Site|null
 	 */
	protected function getSiteByNavId( $navId ) {
		$siteStore = SiteSQLStore::newInstance();
		$sites = $siteStore->getSites();

		$siteGroup = $sites->getGroup( $this->siteGroupName );
		$siteMap = array();

		foreach( $siteGroup  as $site ) {
			$siteGlobalId = $site->getGlobalId();
			$navIds = $site->getNavigationIds();

			foreach( $navIds as $id ) {
				if ( $navId === $id ) {
					return $site;
				}
			}
		}

		// site not found
		return null;
	}

	/**
	 * @param string $url
	 *
	 * @return Site|null
	 */
	public function getSiteFromUrl( $url ) {
		$subdomain = $this->getSubdomain( $url );
		return $this->getSiteByNavId( $subdomain );
	}

}

class RedirectFinder {

	/**
	 * @return string
	 */
	protected function getRequestTitle() {
		global $wgArticlePath;

		$scriptName = @$_SERVER['SCRIPT_NAME'];
		$queryString = @$_SERVER['QUERY_STRING'];

		$path = $queryString ? $scriptName . $queryString : $scriptName;

		$page = WebRequest::extractTitle( $path, $wgArticlePath );

		return $page['title'];
	}

	/**
	 * @return string|null;
	 */
	public function getRedirectUrl() {
		$serverName = @$_SERVER['SERVER_NAME'];

		if ( !$serverName ) {
			return null;
		}

		$siteExtractor = new SiteExtractor( 'wikipedia' );
		$site = $siteExtractor->getSiteFromUrl( $serverName );

		if ( !$site ) {
			return null;
		}

		$pageTitle = $this->getRequestTitle();

		$siteLink = new \Wikibase\SiteLink( $site, $pageTitle );
		$siteLinkTable = \Wikibase\StoreFactory::getStore()->newSiteLinkCache();
		$entityId = $siteLinkTable->getEntityIdForSiteLink( $siteLink );

		if ( $entityId instanceof \Wikibase\EntityId ) {
			$entityContentFactory = \Wikibase\Repo\WikibaseRepo::getDefaultInstance()->getEntityContentFactory();
			$title = $entityContentFactory->getTitleForId( $entityId );
			return $title->getFullURL();
		}

		return null;
	}

}

$redirectFinder = new RedirectFinder();
$url = $redirectFinder->getRedirectUrl();

// @todo handle not found in nicer way
$location = ( $url !== null ) ? $url : $wgServer;

header( "Content-Type: text/html; charset=utf-8" );
header( "Vary: Accept-Language" );
header( "Location:  $location" );
