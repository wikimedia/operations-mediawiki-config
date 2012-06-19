if ( navigator.userAgent.match(/iPhone/i) || navigator.userAgent.match(/iPod/i) ) {

    var ImageCacheManager = function() {
        this.imageCache = {};
        this.hasPendingDownload = false;
        this.downloadQueue = [];

        // TODO: this should get a list of all cached images so we don't double load them

    }

    ImageCacheManager.prototype.getCachedImage = function(uri, onSuccess, onError) {
        var request = {
            'uri':       uri,
            'onSuccess': onSuccess,
            'onError':   onError
        };
        
        this.downloadQueue.push(request);
        this._getNextImage();
    }

    ImageCacheManager.prototype._getNextImage = function() {
        if(this.downloadQueue.length > 0 && !this.hasPendingDownload)
        {
            this.hasPendingDownload = true;
            var request = this.downloadQueue.shift();
            
            var alias = this;
            var ftw = function(uri,path)
            {
                request.onSuccess(uri, path);
                alias.onSuccess(uri,path);
            }
            var wtf = function(uri,error)
            {
                request.onError(uri, error);
                alias.onFail(uri,error);
            }

            // Commented out because BlackBerry does not have this method.
            // We will fake it for now.
            //
            if (this.imageCache[request.uri]) {
             ftw(request.uri, this.imageCache[request.uri]);
            }
            else {
             window.plugins.urlCache.getCachedPathforURI(request.uri,ftw,wtf);
            }
        }
    }

    ImageCacheManager.prototype.onSuccess = function(uri,path) {
        this.imageCache[uri] = {url:path};
        this.hasPendingDownload = false;
        this._getNextImage();
    }

    ImageCacheManager.prototype.onFail = function(uri,error) {
        this.hasPendingDownload = false;
        this._getNextImage();
    }

    if ( !window.plugins ) 
        window.plugins = {}; 
            
    if ( !window.plugins.urlCacheManager ) 
        window.plugins.urlCacheManager = new ImageCacheManager();
}
else if ( navigator.userAgent.match(/browzr/i) ||
          navigator.userAgent.match(/blackberry\d*\/(5|6)\..*/i) || 
		  navigator.userAgent.match(/Android/i)) {

    var ImageCacheManager = function() {
        this.imageCache = {};
    }

    ImageCacheManager.prototype.getCachedImage = function(uri, success, fail) {
        var cachedPath = this.imageCache[uri], retVal = "";
        if (cachedPath) {
            success(uri, cachedPath);
        } else {
            retVal = window.plugins.urlCache.getCachedPathForURI(uri, success, fail);
        }
        return retVal;
    }

    PhoneGap.addPlugin('urlCacheManager', new ImageCacheManager());
}