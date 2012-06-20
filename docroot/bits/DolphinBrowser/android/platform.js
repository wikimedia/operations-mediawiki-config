// Android stuff

// @todo migrate menu setup in here?

// @Override
// navigator.lang always returns 'en' on Android
// use the Globalization plugin to request the proper value
l10n.navigatorLang = function(success) {
	var lang = navigator.language;

	var glob = new Globalization;
	glob.getLocaleName(function(result) {
		lang = result.value.toLowerCase().replace('_', '-');
		//console.log('globalization gave: ' + lang);
		success(lang);
	}, function(err) {
		//console.log('globalization error: ' + err);
		success(null);
	});
}

function getAboutVersionString() {
	return "1.2.1";
}

(function() {
	var ANDROIDCREDITS = [
		"<a href='https://github.com/phonegap/phonegap-plugins/tree/master/Android/Globalization'>PhoneGap Globalization Plugin</a>, <a href='http://www.opensource.org/licenses/MIT'>MIT License</a>",
		"<a href='https://github.com/phonegap/phonegap-plugins/tree/master/Android/Share'>PhoneGap Share Plugin</a>, <a href='http://www.opensource.org/licenses/MIT'>MIT License</a>",
		"<a href='https://github.com/m00sey/PhoneGap-Toast'>PhoneGap Toast Plugin</a>, <a href='http://www.opensource.org/licenses/MIT'>MIT License</a>",
		"<a href='https://github.com/phonegap/phonegap-plugins/tree/master/Android/SoftKeyboard'>PhoneGap SoftKeyboard Plugin</a>, <a href='http://www.opensource.org/licenses/MIT'>MIT License</a>",
		"<a href='https://github.com/phonegap/phonegap-plugins/tree/master/Android/WebIntent'>PhoneGap WebIntent Plugin</a>, <a href='http://www.opensource.org/licenses/MIT'>MIT License</a>"
	];

	window.CREDITS.push.apply(window.CREDITS, ANDROIDCREDITS);
})();

function _setMenuItemState(action, state) {
	window.plugins.SimpleMenu.setMenuState(action, state, function() {}, function() {});
}

function setPageActionsState(state) {
	setMenuItemState("read-in", state);
	setMenuItemState("save-page", state);
	setMenuItemState("share-page", state);
}

chrome.scrollTo = function(selector, posY) {
	// scrollTop seems completely useless on Android 2.x, unable to test so far on 4.x
	// This is the exact opposite of what we noticed on 2.x in the previous release
	// I've no idea why this is happening, neither does jon
	// This gives us what we want for now, but now for non-zero scroll positions the
	// behavior of scrollTo might be different across platforms.
	// Ugh. Will bite us when we try to do hashlinks.
	window.scrollTo(0, posY);
}

chrome.addPlatformInitializer(function() {
	if ($('html').hasClass('android-2')) {
		// Android 2.2/2.3 doesn't do overflow:scroll
		// so we need to engage alternate styles for phone view.
		$('html').removeClass('goodscroll').addClass('badscroll');
	}

	window.XMLHttpRequest = cordova.require("cordova/plugin/xhr");

	document.addEventListener("backbutton", onBackButton, false);
	document.addEventListener("searchbutton", onSearchButton, false);

	function onBackButton() {
		if (!hideMenu()) {
		chrome.goBack();
	}
	}

	function onSearchButton() {
		//hmmm...doesn't seem to set the cursor in the input field - maybe a browser bug???

		$('#searchParam').focus().addClass('active');
		$('#searchParam').bind('blur', function() {
			$('#searchParam').removeClass('active');
			plugins.SoftKeyBoard.hide();
			$('#searchParam').unbind('blur');
		});

		plugins.SoftKeyBoard.show();

	}

	navigator.app.hideGestureButton();
});


function selectText() {
	PhoneGap.exec(null, null, 'SelectTextPlugin', 'selectText', []);
}

function sharePage() {
	// @fixme if we don't have a page loaded, this menu item should be disabled...
	var title = app.getCurrentTitle(),
		url = app.getCurrentUrl().replace(/\.m\.wikipedia/, '.wikipedia');
	window.plugins.share.show(
		{
			subject: title,
			text: url
		}
	);
}

chrome.showNotification = function(text) {
	// Using PhoneGap-Toast plugin for Android's lightweight "Toast" style notifications.
	// https://github.com/m00sey/PhoneGap-Toast
	// http://developer.android.com/guide/topics/ui/notifiers/toasts.html
	window.plugins.ToastPlugin.show_short(text);
}

function _updateMenuState() {
	var d = $.Deferred();

	var menu_handlers = {
		'read-in': function() { languageLinks.showLangLinks(app.curPage); },
		'near-me': function() { geo.showNearbyArticles(); },
		'view-history': function() { appHistory.showHistory(); } ,
		'save-page': function() { savedPages.saveCurrentPage() },
		'view-saved-pages': function() { savedPages.showSavedPages(); },
		'share-page': function() { sharePage(); },
		'go-forward': function() { chrome.goForward(); },
		'select-text': function() { selectText(); },
		'view-settings': function() { appSettings.showSettings(); },
	};
	$('#appMenu command').each(function() {
		var $command = $(this),
			id = $command.attr('id'),
			msg = 'menu-' + id.replace(/Cmd$/, ''),
			label = mw.message(msg).plain();
		$command.attr('label', label);
	});

	window.plugins.SimpleMenu.loadMenu($('#appMenu')[0],
									menu_handlers,
									function(success) {
										console.log(success);
										d.resolve(success);
									},
									function(error) {
										console.log(error);
										d.reject(error);
									});
	return d;
};

window.preferencesDB.addOnSet(function(id, value) {
	window.plugins.preferences.set(id, value, function(){}, function(){});
});

savedPages.doSave = function(options) {
	console.log("Saving page");
	chrome.showSpinner();
	var page = app.curPage;
	var d = $.Deferred();
	var gotPath = function(cachedPage) {
		$('#main img').each(function() {
			var em = $(this);
			var target = this.src.replace('file:', 'https:');
			window.urlCache.getCachedPathForUrl(target).
				done(function(imageFile) {
					readLocalFile(imageFile, asDataUrl).done(function(dataUrl) {
						em.attr('src', dataUrl);
					});
				}).
				fail(function() {
					console.log("Error in image saving");
					d.reject();
				});
		});
		app.track('mobile.app.wikipedia.save-page');
		if(!options.silent) {
			chrome.showNotification(mw.message('page-saved', app.curPage.title).plain());
		}
		chrome.hideSpinner();
		d.resolve();
	}
	var gotError = function(uri, error) {
		console.log('Error: ' + JSON.stringify(error));
		chrome.hideSpinner();
		d.reject();
	}
	$.each(app.curPage.sections, function(i, section) {
		chrome.populateSection(section.id);
	});
	window.urlCache.getCachedPathForUrl(page.getAPIUrl()).done(gotPath).fail(gotError);
	return d;
}
