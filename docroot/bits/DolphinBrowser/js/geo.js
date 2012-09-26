window.geo = function() {

	var shownURLs = [];

	function showNearbyArticles( args ) {
		var args = $.extend(
			{
				lat: 0,
				lon: 0,
				current: true
			},
			args
		);

		chrome.hideOverlays();
		chrome.hideContent();
		$("#nearby-overlay").localize().show();

		if (!geo.map) {
			// Disable webkit 3d CSS transformations for tile positioning
			// Causes lots of flicker in PhoneGap for some reason...
			L.Browser.webkit3d = false;
			options = {};
			if (navigator.userAgent.match(/Android 2/)) {
				// Android 2.x
				// @todo enable the pinch-zoom plugin
			} else {
				options.touchZoom = true;
				options.zoomControl = false;
			}
			geo.map = new L.Map('map', options);
			//var tiles = new L.TileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
			var tiles = new L.TileLayer('http://otile{s}.mqcdn.com/tiles/1.0.0/osm/{z}/{x}/{y}.png', {
				maxZoom: 18,
				subdomains: '1234' // for MapQuest tiles
			});
			geo.map.addLayer(tiles);

			geo.map.attributionControl.setPrefix("");
			geo.map.attributionControl.addAttribution('<span class="map-attribution">' + mw.message("attribution-mapquest") + '</span>');
			geo.map.attributionControl.addAttribution("<br /><span class='map-attribution'>" + mw.message("attribution-osm") + '</span>');

			$(".map-attribution a").bind('click', function(event) {
				// Force web links to open in external browser
				// instead of the app, where navigation will be broken.
				chrome.openExternalLink(this.href);
				event.preventDefault();
			});
		}

		// @fixme load last-seen coordinates
		geo.map.setView(new L.LatLng(args.lat, args.lon), 18);

		var findAndDisplayNearby = function( lat, lon ) {
			geoLookup( lat, lon, preferencesDB.get("language"), function( data ) {
				geoAddMarkers( data );
			}, function(err) {
				chrome.popupErrorMessage("error");
				chrome.showContent();
				console.log(JSON.stringify(err));
			});
		};

		var ping = function() {
			var pos = geo.map.getCenter();
			findAndDisplayNearby( pos.lat, pos.lng );
		};

		if ( args.current ) {
			geo.map.on('viewreset', ping);
			geo.map.on('locationfound', ping);
			geo.map.on('moveend', ping);
			geo.map.locate({
				setView: true,
				maxZoom: 18,
				enableHighAccuracy: true
			});
		}
		else {
			findAndDisplayNearby( args.lat, args.lon );
		}
	}

	function getFloatFromDMS( dms ) {
		var multiplier = /[sw]/i.test( dms ) ? -1 : 1;
		var bits = dms.match(/[\d.]+/g);

		var coord = 0;

		for ( var i = 0, iLen=bits.length; i<iLen; i++ ) {
			coord += bits[i] / multiplier;
			multiplier *= 60;
		}

		return coord;
	}

	function addShowNearbyLinks() {
		$( 'span.geo-dms' ).each( function() {
			var $coords = $( this ),
			lat = $coords.find( 'span.latitude' ).text(),
			lon = $coords.find( 'span.longitude' ).text();

			$coords.closest( 'a' ).attr( 'href', '#' ).click( function() {
				showNearbyArticles( {
					'lat': getFloatFromDMS( lat ),
					'lon': getFloatFromDMS( lon ),
					'current': false,
				} );				
			} );
		} );
	}

	function geoLookup(latitude, longitude, lang, success, error) {
		var requestUrl = "http://ws.geonames.net/findNearbyWikipediaJSON?formatted=true&";
		requestUrl += "lat=" + latitude + "&";
		requestUrl += "lng=" + longitude + "&";
		requestUrl += "username=wikimedia&";
		requestUrl += "lang=" + lang;
		$.ajax({
			url: requestUrl,
			success: function(data) {
				success(data);
			},
			error: error
		});
	}

	function geoAddMarkers( data ) {
		$.each(data.geonames, function(i, item) {
			var summary, html,
				url = item.wikipediaUrl.replace(/^([a-z0-9-]+)\.wikipedia\.org/, window.PROTOCOL + '://$1.m.wikipedia.org');
			if($.inArray(url, shownURLs) === -1) {
				var marker = new L.Marker(new L.LatLng(item.lat, item.lng));
				summary = item.summary || '';

				html = "<div><strong>" + item.title + "</strong><p>" + summary + "</p></div>";
				var popupContent = $(html).click(function() {
					app.navigateToPage(url, {hideCurrent: true});
				})[0];
				marker.bindPopup(popupContent, {closeButton: false});
				geo.map.addLayer(marker);
				shownURLs.push(url);
			}
		});
	}

	return {
		showNearbyArticles: showNearbyArticles,
		addShowNearbyLinks: addShowNearbyLinks,
		map: null
	};

}();
