if ( navigator.userAgent.match(/iPhone/i) || navigator.userAgent.match(/iPod/i) ) {

    var URLCache = function() {
        this._cache = {};
        this._callbacks = {};
    }

    // internal callback method called by Obj-C for ALL URLCache operations that succeed
    URLCache.prototype._onCacheCallbackSuccess = function(uri,cachedURL) {
        this._cache[uri] = cachedURL;
        this._callbacks[uri].onSuccess(uri,cachedURL);

        // Commented out because if the same URI is requested multiple times, then the first
        // response will delete the callback and the second response will fail because there is no callback.
        //
        //delete this._callbacks[uri];
    }

    // internal callback method called by Obj-C for ALL URLCache operations that fail
    URLCache.prototype._onCacheCallbackFail = function(uri,error) {
        this._callbacks[uri].onFail(uri,error);

        // Commented out because if the same URI is requested multiple times, then the first
        // response will delete the callback and the second response will fail because there is no callback.
        //
        //delete this._callbacks[uri];
    }

    // returns url of cached resource ( null if resource has not been accessed )
    // success will be called if the resource is successfully downloaded to the device
    // onFail will be called if the uri is invalid, or network not available, ...
    URLCache.prototype.getCachedPathforURI = function(uri,onSuccess,onFail) {
        if(this._cache[uri] == null)
        {
            this._callbacks[uri] = {onSuccess:onSuccess,onFail:onFail};
            PhoneGap.exec("URLCache.getCachedPathforURI",uri);
        }
        return this._cache[uri];
    }

    PhoneGap.addConstructor(function() {
        if ( !window.plugins ) 
            window.plugins = {}; 

        if ( !window.plugins.urlCache ) 
            window.plugins.urlCache = new URLCache();
    });
}
else if ( navigator.userAgent.match(/browzr/i) ) {

    var Cache = function() {
    }

    Cache.prototype.getCachedPathForURI = function(uri, success, fail) {
        return PhoneGap.execAsync(function(args) {
            var response = JSON.parse(args.message);
            success(uri, response.file);
        }, function(args) {
            args = (typeof args !== 'string') ? JSON.stringify(args) : args;
            fail(uri, args);
        }, 'ca.rnao.bpg.plugins.URLCache', 'getCachedPathForURI', [uri, 'RNAO']);
    };

    PhoneGap.addPlugin('urlCache', new Cache());
}
else if ( navigator.userAgent.match(/blackberry\d*\/(5|6)\..*/i) ) {

    var Cache = function() {
    }

    Cache.prototype.getCachedPathForURI = function(uri, success, fail) {
        return PhoneGap.exec(
            function(args) {
                var response = JSON.parse(args);
                success(uri, response.file);
            },
            function(args) {
                args = (typeof args !== 'string') ? JSON.stringify(args) : args;
                fail(uri, args);
            },
            'URLCache',
            'getCachedPathForURI',
            [uri, 'RNAO']
        );
    };

    PhoneGap.addPlugin('urlCache', new Cache());
}
else if ( navigator.userAgent.match(/Android/i) ) {

    var Cache = function() {
    }

    Cache.prototype.getCachedPathForURI = function(uri, success, fail) {

       return PhoneGap.exec(
            success,
            fail,
            'URLCache',
            'getCachedPathForURI',
            [uri]
        );
    };

    PhoneGap.addPlugin('urlCache', new Cache());
//	PluginManager.addService("URLCache","com.nitobi.rnao.plugins.URLCache");
}
