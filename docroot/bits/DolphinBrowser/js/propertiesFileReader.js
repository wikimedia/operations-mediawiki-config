/**
 * Copyright (c) Brion Vibber 2011
 * Written initially for Wikipedia Mobile app
 *
 * Reader for Java-style .properties files to be used for
 * l10n message files.
 *
 * http://download.oracle.com/javase/7/docs/api/java/util/Properties.html#load%28java.io.Reader%29
 */

propertiesFileReader = window.propertiesFileReader = {
	/**
	 * @param string data: full string of source file
	 * @return Object: map of keys to strings
	 * @throws Error on invalid input format
	 */
	parse: function(text) {
		if (typeof text !== "string") {
			throw new Error("Non-string passed to propertiesFileReader.parse");
		}
		var data = {},
			lines = text.split(/\r?\n/),
			blank = /^\s*$/,
			comment = /^\s*[#!]/,
			keyonly = /^\s*([^=:\s]+)\s*()$/,
			keyval = /^\s*([^=:\s]+)(?:\s*[=:]\s*|\s+)(.*)$/,
			continued = /^\s*(.*)$/;
		for (var i = 0; i < lines.length; i++) {
			var line = lines[i];
			if (line.match(blank)) {
				continue;
			}
			if (line.match(comment)) {
				continue;
			}
			var matches = line.match(keyonly) || line.match(keyval);
			if (matches) {
				var key = matches[1],
					val = matches[2];

				while (val.substr(-1, 1) == "\\") {
					// Line continuation
					i++;
					if (i >= lines.length) {
						throw new Error("Line continuation at end of file at line " + i);
					}
					line = lines[i];
					if (matches = line.match(continued)) {
						val = val.substr(0, val.length - 1) + matches[1];
					} else {
						throw new Error("Invalid line after line continuation at line " + i);
					}
				}

				data[key] = propertiesFileReader.unescape(val);

				continue;
			}
			throw new Error("Invalid .properties format at line " + (i + 1) + ": " + line);
		}
		return data;
	},

	unescape: function(str) {
		// @fixme add \u escapes -- won't be used in our files though
		str = str.replace(/\\n/g, "\n");
		str = str.replace(/\\t/g, "\t");
		str = str.replace(/\\(.)/g, "$1");
		return str;
	}
};

