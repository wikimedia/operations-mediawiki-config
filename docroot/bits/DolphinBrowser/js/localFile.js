var asText = 0,
	asDataUrl = 1;

function readLocalFile(fileUrl, type) {
	var d = $.Deferred();

	function onError() {
		d.reject();
	}

	function onResolve(fileEntry) {
		function onGotFile(file) {
			var reader = new FileReader();
			reader.onload = function(evt) {
				var data = evt.target.result;
				d.resolve(data);
			};

			reader.onerror = onError;

			if (type === asText) {
				reader.readAsText(file);
			} else if (type === asDataUrl) {
				reader.readAsDataURL(file);
			}
		}

		fileEntry.file(onGotFile, onError);
	}

	window.resolveLocalFileSystemURI(fileUrl, onResolve, onError);
	return d;
}

