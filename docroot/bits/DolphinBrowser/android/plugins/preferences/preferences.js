/**
 * Phonegap Preferences plugin
 *
 */
var Preferences = function() {

};

Preferences.prototype.get = function(id, success, fail) {
	return PhoneGap.exec(function(args) {
        success(args);
    }, function(args) {
        fail(args);
    }, 'Preferences', 'get', [{'id': id}]);
};

Preferences.prototype.set = function(id, value, success, fail) {
	return PhoneGap.exec(function(args) {
        success(args);
    }, function(args) {
        fail(args);
    }, 'Preferences', 'set', [{'id': id, 'value': value}]);
};

PhoneGap.addConstructor(function() {
	PhoneGap.addPlugin('preferences', new Preferences());
});
