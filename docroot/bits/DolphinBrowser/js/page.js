(function() {
	window.Page = function(title, lead, sections, lang) { 
		this.title = title;
		this.lead = lead;
		this.sections = sections;
		this.lang = lang;
	};

	Page.fromRawJSON = function(title, rawJSON, lang) {
		var lead = {};
		var sections = [];
		var lastCollapsibleSection = {subSections: []};

		if(typeof rawJSON.mobileview.redirected !== "undefined") {
			// If we're redirected, use the final page name
			title = rawJSON.mobileview.redirected;
		}

		if(typeof rawJSON.mobileview.error !== "undefined") {
			// Only two types of errors possible when the mobileview api returns
			// One is a 404 (missingtitle), other is an invalid title (usually empty title)
			// We're redirecting empty title to main page in app.navigateTo
			if(rawJSON.mobileview.error.code === "missingtitle") {
				return null;
			}
		}

		$.each(rawJSON.mobileview.sections, function(index, section) {
			if(section.id === 0) {
				// Lead Section
				// We should also make sure that if there is a lead followed by
				// h3, h4, etc they all fold into the lead
				// Not sure why a page would do this though
				section.subSections = [];
				lead = section;
				lastCollapsibleSection = section;
				return;
			} 
			if(typeof section.references !== "undefined") {
				section.references = true;
			}
			// Only consider leve 2 sections as 'sections'
			// Group *all* subsections under them, no matter which level they are at
			if(section.level == 2) {
				section.subSections = [];
				lastCollapsibleSection = section;
				sections.push(section);
			} else {
				lastCollapsibleSection.subSections.push(section);
			}

		});
		return new Page(title, lead, sections, lang);
	};

	Page.requestFromTitle = function(title, lang) {
		// Make sure changes to this are also propogated to getAPIUrl
		return app.makeAPIRequest({
			action: 'mobileview',
			page: title,
			redirects: 'yes',
			prop: 'sections|text',
			sections: 'all',
			sectionprop: 'level|line',
			noheadings: 'yes'
		}, lang, {
			dataFilter: function(data) {
				return Page.fromRawJSON(title, JSON.parse(data), lang);
			}
		});	
	};

	Page.prototype.requestLangLinks = function() {
		if(this.langLinks) {
			var d = $.Deferred();
			d.resolve(this.langLinks);
			return d;
		}
		var that = this;
		return app.makeAPIRequest({
			action: 'parse',
			page: this.title,
			prop: 'langlinks'
		}, this.lang, {
			dataFilter: function(text) {
				var data = JSON.parse(text);
				var langLinks = [];
				$.each(data.parse.langlinks, function(i, langLink) {
					langLinks.push({lang: langLink.lang, title: langLink['*']});
				});
				that.langLinks = langLinks;
				return langLinks;
			}
		});
	};

	Page.prototype.getSectionHtml = function(id) {
		var sectionTemplate = templates.getTemplate('section-template');
		var foundSection = null;
		$.each(this.sections, function(i, section) {
			if(section.id == id) {
				foundSection = section;
				return;
			}
		});
		return sectionTemplate.render(foundSection);
	};

	Page.prototype.toHtml = function() {
		var contentTemplate = templates.getTemplate('content-template');
		return contentTemplate.render(this);
	};

	Page.prototype.serialize = function() {
		// Be more specific later on, but for now this does :)
		return JSON.stringify(this);
	};

	Page.prototype.getHistoryUrl = function() {
		return this.getCanonicalUrl() + "?action=history";
	}

	Page.prototype.getCanonicalUrl = function() {
		return app.baseUrlForLanguage(this.lang) + "/wiki/" + encodeURIComponent(this.title.replace(/ /g, '_'));
	}

	// Returns an API URL that makes a request that retreives this page
	// Should mimic params from Page.requestFromTitle
	Page.prototype.getAPIUrl = function() {
		return app.baseUrlForLanguage(this.lang) + '/w/api.php?format=json&action=mobileview&page=' + encodeURIComponent(this.title) + '&redirects=1&prop=sections|text&sections=all&sectionprop=level|line&noheadings=true';
	};

	Page.prototype.getCanonicalUrl = function() {
		return app.makeCanonicalUrl(this.lang, this.title);
	};

})();
