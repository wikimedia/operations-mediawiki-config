/**
 * Phonegap cachemode plugin for Android
 * Brion Vibber 2011
 */

var CacheMode = function() {
	this.setCacheMode = function(mode, success, fail) {
		return PhoneGap.exec(success, fail, 'CacheModePlugin', 'setCacheMode', [mode]);
	}
};
			
PhoneGap.addConstructor(function() {
	PhoneGap.addPlugin('CacheMode', new CacheMode());
});
