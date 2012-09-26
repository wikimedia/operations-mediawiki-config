window.urlCache = function() {
	function error(e) {
		console.log("ERROR!" + JSON.stringify(e));
	}

	function dataUrlForImage(img) {
		var d = $.Deferred();
		// Create an empty canvas element
		var canvas = document.createElement("canvas");
		canvas.width = img.width;
		canvas.height = img.height;

		// Copy the image contents to the canvas
		var ctx = canvas.getContext("2d");
		ctx.drawImage(img, 0, 0);

		var dataURL = canvas.toDataURL("image/png");
		d.resolve(dataURL);
		//return d;
		return dataURL;
		//return dataURL.replace(/^data:image\/(png|jpg);base64,/, "");
	}

	function getCachedPathForUrl(url) {
		var d = $.Deferred();

		var fileName = Crypto.MD5(url);
		var filePath = fileName;

		function cacheUrl() {
			function saveFile(fileEntry) {
				$.get(url, function(data) {
					fileEntry.createWriter(function(writer) {
						writer.write(data);
						console.log("Writing stuff to " + fileEntry.fullPath);
						writer.onwriteend = function() {
							console.log("written stuff!");
							d.resolve(fileEntry.fullPath);
						};
					});
				});
			}
			window.requestFileSystem(LocalFileSystem.PERSISTENT, 0,
					function(fs){
						fs.root.getFile(filePath, {create: true}, saveFile, error);
					});
			return d;
		}

		console.log("Trying to open " + filePath);
		window.requestFileSystem(LocalFileSystem.PERSISTENT, 0,
			function(fs) {
				fs.root.getFile(filePath, {create: false},
				function(fileEntry) {
					console.log("Found!");
					d.resolve(fileEntry.fullPath);
				}, function(error) {
					console.log("Not found!");
					cacheUrl();
				}
		);
			});
		return d;
	}

	function saveCompleteHtml(url, data, domParent) {
		// Converts images to Data URIs
		var d = $.Deferred();

		var fileName = Crypto.MD5(url);
		var filePath = fileName;

		console.log("Starting to save");
		var replacements = {};
		console.log("HTML Parsed");
		function saveFile(fileEntry) {
			fileEntry.createWriter(function(writer) {
				writer.write(data);
				console.log("Writing stuff to " + fileEntry.fullPath);
				writer.onwriteend = function() {
					console.log("written stuff!");
					d.resolve(fileEntry.fullPath);
				};
			});
		}
		console.log("About to map stuff");

		domParent.find("img").each(function(i, img) {
			replacements[$(img).attr("src")] =  urlCache.dataUrlForImage(img);
		});

		$.each(replacements, function(href, dataURI) {
			data = data.replace(href, dataURI);
		});

		console.log("Done mapping stuff");
		window.requestFileSystem(LocalFileSystem.PERSISTENT, 0,
			function(fs){
				console.log(filePath);
				fs.root.getFile(filePath, {create: true}, saveFile, error);
			});
		console.log("Technically done");
		return d;
	}

	function getCachedData(url) {
		var d = $.Deferred();

		var fileName = Crypto.MD5(url);
		var filePath = fileName;


		function readFile(fileEntry) {
			var reader = new FileReader();
			reader.onloadend = function(evt) {
				d.resolve(evt.target.result);
			};
			console.log('file path is ' + JSON.stringify(fileEntry));
			fileEntry.file(function(file) {reader.readAsText(file); });
		}
		window.requestFileSystem(LocalFileSystem.PERSISTENT, 0,
				function(fs){
					fs.root.getFile(filePath, {create: false}, readFile, error);
				});

		return d;
	}

	return {
		getCachedPathForUrl: getCachedPathForUrl,
		getCachedData: getCachedData,
		saveCompleteHtml: saveCompleteHtml,
		dataUrlForImage: dataUrlForImage
	};
}();
