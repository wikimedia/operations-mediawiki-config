var currentHistoryIndex = -1;

var pageHistory = [];

window.SAVED_PAGES_VERSION = 1;
window.PROTOCOL = 'https';
window.PROJECTNAME = 'wikipedia';
window.LICENSEPAGE = "Wikipedia:Text_of_Creative_Commons_Attribution-ShareAlike_3.0_Unported_License";

(function() {
	var url_parts = location.href.split('/');
	delete url_parts[url_parts.length - 1];
	window.ROOT_URL = url_parts.join('/');
})()

window.CREDITS = [
	"<a href='http://phonegap.com'>PhoneGap</a>, <a href='http://www.apache.org/licenses/LICENSE-2.0.html'>Apache License 2.0</a>",
	"<a href='http://jquery.com'>jQuery</a>, <a href='http://www.opensource.org/licenses/MIT'>MIT License</a>",
	"<a href='http://leaflet.cloudmade.com/'>Leaflet.js</a>, <a href='http://www.opensource.org/licenses/bsd-license.php'>2-Clause BSD License</a>",
	"<a href='http://zeptojs.com'>Zepto</a>, <a href='http://www.opensource.org/licenses/MIT'>MIT License</a>",
	"<a href='http://cubiq.org/iscroll-4'>iScroll</a>, <a href='http://www.opensource.org/licenses/MIT'>MIT License</a>",
	"<a href='http://twitter.github.com/hogan.js/'>Hogan.js</a>, <a href='http://www.apache.org/licenses/LICENSE-2.0.html'>Apache License 2.0</a>"
	];

function init() {
	$(document.body).addClass('jsEnabled');
	document.addEventListener("deviceready", function() {chrome.initialize(); }, true);
}

function aboutPage() {
	chrome.hideOverlays();
	$.get(ROOT_URL + 'AUTHORS').then(function(authors) {
		$("#about-version-string").text(getAboutVersionString());
		$("#about-contributors").text($.trim(authors).split('\n').join(', '));
		$("#about-credits").html(window.CREDITS.join('<br />'));
		$("#about-license").html("<br />" + mw.message("about-license"));
		chrome.hideOverlays();
		chrome.hideContent();
		$("#about-page-overlay").localize().show();
		$("#about-page-overlay a").bind('click', function(event) {
			// Force web links to open in external browser
			// instead of the app, where navigation will be broken.
			chrome.openExternalLink(this.href);
			event.preventDefault();
		});
		$("#aboutclose").unbind('click');
		$("#aboutclose").bind('click', function(){
			$("#about-page-overlay").hide();
			appSettings.showSettings();
		});
		chrome.setupScrolling('#about-page-overlay .scroller');
	});
}
