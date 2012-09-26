window.l10n = function() {
	/**
	 * Validate and normalize a language code.
	 * Doesn't guarantee we know it or it's legit, but confirms the format is safe.
	 *
	 * @param string lang
	 * @returns string normalized to lowercase
	 * @throws Error on invalid input
	 */
	function normalizeLanguageCode(lang) {
		if (typeof lang !== "string") {
			throw new Error("Invalid type for language name");
		}
		if (!lang.match(/^[a-z0-9]+(-[a-z0-9]+)*$/i)) {
			throw new Error("Invalid language name format: " + lang);
		}
		lang = lang.toLowerCase();
		var map = {
			'zh-cn': 'zh-hans',
			'zh-tw': 'zh-hant',
			'sr-sr': 'sr-ec', // Serbian Cyrillic
			'sr': 'sr-ec', // Serbian Cyrillic
			'iw': 'he' // Hebrew, just in case Android prefers Java-style 'iw'
		};
		if (lang in map) {
			return map[lang];
		} else {
			return lang;
		}
	}

	/**
	 * Load up messages for given language, synchronously to keep things simple.
	 * @param string lang
	 * @param callback
	 */
	function loadMessages(lang, callback) {
		//console.log('loading messages for ' + lang);
		lang = normalizeLanguageCode(lang);
		var url = ROOT_URL + 'messages/messages-' + lang + '.properties';
		console.log('Loading messages: ' + url);
		$.ajax({
			url: url,
			//async: false, // fails on WinPhone7.1
			async: true,
			dataType: 'text',
			success: function(data) {
				console.log('success loading ' + url);
				try {
					var messages = propertiesFileReader.parse(data);
				} catch (e) {
					// We have no messages for this particular language code
					callback(false);
					return;
				}
				$.each(messages, function(key, val) {
					mw.messages.set(key, val);
				});
				callback(true);
			},
			error: function(xhr, status, err) {
				console.log('failed to load ' + url + ': ' + status + '; ' + err);
				// We seem to get "success" on file not found, which feels wrong...
				// We kinda expect to get 404 errors or similar?
				callback(false);
			}
		});
	}

	function navigatorLang(success) {
		var lang = navigator.language;
		if (lang == 'en') {
			/**
			 * @fixme navigator.language is always 'en' on Android? https://code.google.com/p/android/issues/detail?id=4641
			 * Workaround grabbing from userAgent: http://comments.gmane.org/gmane.comp.handhelds.phonegap/7908
			 */
			var matches = navigator.userAgent.match(/; Android [^;]+; ([A-Za-z0-9]+(?:-[A-Za-z0-9]+)*);/);
			if (matches) {
				lang = matches[1];
			}
		}
		success(lang);
	}

	function initLanguages() {
		// Always load english as a fallback
		var langs = ['en'],
			lang = preferencesDB.get('locale'); // may be eg "en-us" or "zh-tw"
			console.log(lang);
		var	baseLang = lang.replace(/-.*?$/, ''); // strip country code, eg "en" or "zh"
			console.log(baseLang);

		if (baseLang != 'en') {
			// Load the base language, eg 'en', 'fr', 'zh'
			langs.push(baseLang);
		}
		if (lang != baseLang) {
			// Load the variant language, eg 'en-us', 'fr-ca', 'zh-cn'
			langs.push(lang);
		}

		console.log('langs are: ' + langs.join(','));
		var i = 0;
		var step = function() {
			if (i < langs.length) {
				var sub = langs[i];
				i++;
				loadMessages(sub, function(ok) {
					step();
				});
			} else {
				$(document).trigger('mw-messages-ready');
			}
		};
		step();
	}

	function isLangRTL(lang) {
		var rtl_langs = [
			"arc", "arz", "ar", "bcc", "bqi", "ckb", "dv", "fa", "glk", "ha", "he",
			"khw", "ks", "mzn", "pnb", "ps", "sd", "ug", "ur", "yi"
		];
		return $.inArray(lang, rtl_langs) !== -1;
	}
	return {
		initLanguages: initLanguages,
		navigatorLang: navigatorLang,
		normalizeLanguageCode: normalizeLanguageCode,
		isLangRTL: isLangRTL
	};
}();
