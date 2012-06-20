// iOS+PhoneGap-specific setup

// set iOS 4.2 to be HTTP not HTTPS
if(navigator.userAgent.match(/OS 4_2/g)) {
	window.PROTOCOL = 'http';
}

function getAboutVersionString() {
	return "3.2";
}

(function() {
	var iOSCREDITS = [
		"<a href='https://github.com/phonegap/phonegap-plugins/tree/master/iPhone/ActionSheet'>PhoneGap ActionSheet plugin</a>, <a href='http://www.opensource.org/licenses/MIT'>MIT License</a>",
		"<a href='https://github.com/davejohnson/phonegap-plugin-facebook-connect'>PhoneGap Facebook Connect Plugin</a>, <a href='http://www.opensource.org/licenses/MIT'>MIT License</a>",
		"<a href='https://github.com/facebook/facebook-ios-sdk'>Facebook iOS SDK</a>, <a href='http://www.apache.org/licenses/LICENSE-2.0.html'>Apache License 2.0</a>",
		"<a href='http://stig.github.com/json-framework/'>SBJSON</a>, <a href='http://www.opensource.org/licenses/bsd-license.php'>New BSD License</a>"
	];

	window.CREDITS.push.apply(window.CREDITS, iOSCREDITS);
})();

// Save page supporting code
app.loadCachedPage = function (url) {
	return urlCache.getCachedData(url).then(function(data) {
		var pageData = JSON.parse(data);
		var page = new Page(pageData.title, pageData.lead, pageData.sections, pageData.lang);
		app.setCurrentPage(page);
	}).fail(function(error) {
		console.log('Error: ' + error);
		chrome.hideSpinner();
	});
}

savedPages.doSave = function(options) {
	var d = $.Deferred();
	var url = app.curPage.getAPIUrl();
	var data = JSON.stringify(app.curPage);
	chrome.showSpinner();
	$.each(app.curPage.sections, function(i, section) {
		chrome.populateSection(section.id);
	});
	urlCache.saveCompleteHtml(url, data, $("#main")).done(function() {
		if(!options.silent) {
			chrome.showNotification(mw.message('page-saved', app.curPage.title).plain());
		}
		app.track('mobile.app.wikipedia.save-page');
		chrome.hideSpinner();
		d.resolve();
	}).fail(function() {
		d.reject()
	});
	return d;
}

// @Override
function popupMenu(items, callback, options) {
	if (options.origin) {
		var $origin = $(options.origin),
			pos = $origin.offset();
		options.left = pos.left;
		options.top = 0; // hack pos.top;
		options.width = $origin.width();
		options.height = $origin.height();
	}
	window.plugins.actionSheet.create('', items, callback, options);
}

chrome.addPlatformInitializer(function() {
	console.log("Logging in!");
	window.plugins.FB.init("[FB-APP-ID]", function() {
		console.log("failed FB init:(");
	});
	console.log("Logged in!");
	// Fix scrolling on iOS 4.x after orient change
	window.addEventListener('resize', function() {
		chrome.setupScrolling('#content');
	});
});

// @Override
function showPageActions(origin) {
	var pageActions = [
		mw.msg('menu-savePage'),
		mw.msg('menu-ios-open-safari'),
		mw.msg('menu-share-fb'),
		mw.msg('menu-cancel')
	];
	// iOS less than 5 does not have Twitter. 
	var cancelIndex = 3;
	if(navigator.userAgent.match(/OS 5/g)) {
		pageActions.splice(pageActions.length - 1, 0, mw.msg('menu-share-twitter'));
		cancelIndex = 4;
	}
	popupMenu(pageActions, function(value, index) {
		if (index == 0) {
			savedPages.saveCurrentPage();
		} else if (index == 1) {
			shareSafari();
		} else if (index == 2) {
			shareFB();
		} else if (index == 3 && cancelIndex != 3) {
			shareTwitter();
		}
	}, {
		cancelButtonIndex: cancelIndex,
		origin: origin
	});
}

function shareFB() {
	var url = app.getCurrentUrl().replace('.m.', '.');
	var title = app.getCurrentTitle();

	var share = function() {
		window.plugins.FB.dialog({
			method: 'feed',
			link: url,
			caption: title
		});
	};

	window.plugins.FB.getLoginStatus(function(status) {
		console.log("status is " + JSON.stringify(status));
		if(status.status === "connected") {
			share();
		} else {
			window.plugins.FB.login({scope: ""}, share);
		}
	});
}

function shareTwitter() {
	var url = app.getCurrentUrl().replace('.m.', '.');
	var title = app.getCurrentTitle();

	window.plugins.twitter.isTwitterAvailable(function(available) {
		if(!available) {
			chrome.showNotification(mw.message("twitter-not-available"));
			return;
		}
		window.plugins.twitter.composeTweet(function() {
			console.log("Success!");
		}, function() {
			console.log("Failed :(");
		}, title + " " + url);
	});
}

function shareSafari() {
	// Use the full URL; on phones we'll autodetect mobile on the other end...
	// on iPad we'll want to actually show the desktop version through.
	var url = app.getCurrentUrl().replace('.m.', '.');
	chrome.openExternalLink(url);
}

chrome.showNotification = function(message) {
	var d = $.Deferred();
	navigator.notification.alert(message, function() {
		d.resolve();
	}, "");
	return d;
};
if(navigator.userAgent.match(/OS 4/)) {
	chrome.setupScrolling = function(selector) {
		console.log("MODIFIED!");
		var $el = $(selector);
		var scroller = $el[0].scroller;
		if (scroller) {
			window.setTimeout(function() {
				scroller.refresh();
				console.log("Refreshing!");
			}, 200); // HACK: Making this zero does do the refresh properly
			// Quite possibly that the DOM takes a while to actually settle down
		} else {
			scroller = new iScroll($el[0]);
			$el[0].scroller = scroller;
		}
	}

	chrome.scrollTo = function(selector, offsetY) {
		var $el = $(selector);
		var scroller = $el[0].scroller;
		if(scroller) {
			scroller.scrollTo(0, offsetY, 200);
		}
	};
}
