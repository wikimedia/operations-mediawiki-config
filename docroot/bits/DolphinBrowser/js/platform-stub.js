(function() {

/**
 * Synchronously load relevant platform PhoneGap scripts during startup.
 */

// Is there a way that PhoneGap can more reliably identify its presence and platform before deviceready?
var platform = 'unknown',
	includes = ['platform.js'],
	ua = navigator.userAgent;

if (window.DolphinPhoneGap && window.DolphinPhoneGap.available()) {
	platform = 'dolphin';
}

var detectorClasses = [];

if(platform === 'dolphin') {
	detectorClasses.push('android');
	if(ua.match(/Android 2/)) {
		detectorClasses.push('android-2');
	} else if(ua.match(/Android 3/)) {
		detectorClasses.push('android-3');
	} else if(ua.match(/Android 4/)) {
		detectorClasses.push('android-4');
	}
}

$('html').addClass(detectorClasses.join(' '));

if (platform == 'unknown') {
	// Assume we're a generic web browser.
	platform = 'web';
} else {
	includes.push('cordova.dolphin.js');
	var plugins = {
		dolphin: [
			'menu/menu.android.js',
			'urlcache/URLCache.js',
			'softkeyboard/softkeyboard.js',
			'toast/phonegap-toast.js',
			'share/share.js',
			'cachemode/cachemode.js',
			'webintent/webintent.js',
			'globalization/globalization.js',
			'preferences/preferences.js'
		],
	};
	if (platform in plugins) {
		$.each(plugins[platform], function(i, path) {
			includes.push('plugins/' + path);
		})
	}
}

function includePlatformFile(name) {
	var path = platform + '/' + name,
		line = '<script type="text/javascript" charset="utf-8" src="' + path + '"></script>';
	document.writeln(line);
}

$.each(includes, function(i, path) {
	includePlatformFile(path);
});

})();
