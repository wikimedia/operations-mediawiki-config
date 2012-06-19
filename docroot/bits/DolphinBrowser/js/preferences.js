// Preferences singleton, to access all 'preferences'
// localStorage is used for persistance
// Call initializeDefaults before using.

window.preferencesDB = {
	// Defaults for preferences
	// If it is a function, it should take only one param (another function)
	// And when done, should replace itself with the default value
	// And then call the 'success()' parameter
	// If it is not a function, is just used as is
	defaults: {
		'fontSize': '100%',
		// The locale. Default content language + UI language
		'locale': function(success) {
			var defaults = this;
			l10n.navigatorLang(function(lang) {
				defaults.locale = l10n.normalizeLanguageCode(lang || 'en');
				console.log('done with navigate');
				success();
			});
		},
		// The language. Only for content
		'language': function(success) {
			this.language = l10n.normalizeLanguageCode(preferencesDB.get('locale').replace(/-.*?$/, ''));
			success();
		},
		// UI Language. Only for UI. Not replaced, unlike language
		'uiLanguage': function(success) {
			this.uiLanguage = l10n.normalizeLanguageCode(preferencesDB.get('locale').replace(/-.*?$/, ''));
			success();
		}
	},
	// Ordering of default initializer functions to call
	defaultFunctions: [
		'locale',
		'language',
		'uiLanguage'
		],
	// Serializes default function calls
	initializeDefaults: function(success) {
		var defaults = this.defaults;
		var functions = this.defaultFunctions;
		var curFunction = 0;
		var recallFunction = function() {
			curFunction += 1;
			if(curFunction < functions.length) {
				// We have more functions to call!
				defaults[functions[curFunction]].apply(defaults, [recallFunction]);
			} else {
				// We are out of functions, let's say we succeeded
				success();
			}
		};
		defaults[functions[0]].apply(defaults, [recallFunction]);
	},
	get: function(pref) {
		var stored = localStorage.getItem(pref);
		if(!stored) {
			return this.defaults[pref];
		}
		return stored;
	},
	set: function(pref, value) {
		localStorage.setItem(pref, value);
		$.each(this.onSet, function(index, fun) {
			fun(pref, value);
		});
	},
	onSet: [],
	//add an event executed when set method is executed. They must take to params, the id of the preference and his value.
	addOnSet: function(fun) {
		this.onSet.push(fun);
	}
};
