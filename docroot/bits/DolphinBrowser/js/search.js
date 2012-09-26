window.search = function() {
	var curReq = null; // Current search request

	function stopCurrentRequest() {
		if(curReq !== null) {
			curReq.abort();
			curReq = null;
		}
	}

	function handleNetworkFailure(err, xhr) {
		// We abort previous requests before making a new one
		// So we don't need to be showing error messages when the error is an abort
		if(err.statusText !== "abort") {
			chrome.popupErrorMessage(xhr);
			chrome.hideSpinner();
		}
	}

	function performSearch(term, isSuggestion) {
		if(term == '') {
			chrome.showContent();
			return;
		}
		chrome.showSpinner();

		if(!isSuggestion) {
			return getFullTextSearchResults(term);
		} else {
			return getSearchResults(term);
		}
	}

	function getDidYouMeanResults(results) {
		// perform did you mean search
		stopCurrentRequest();
		curReq = app.makeAPIRequest({
			action: 'query',
			list: 'search',
			srsearch: results[0],
			srinfo: 'suggestion',
			format: 'json'
		}).done(function(data) {
			var suggestion_results = data;
			var suggestion = getSuggestionFromSuggestionResults(suggestion_results);
			if(suggestion) {
				getSearchResults(suggestion, 'true');
			}
		}).fail(handleNetworkFailure);
		chrome.setSpinningReq(curReq);
	}

	function getSuggestionFromSuggestionResults(suggestion_results) {
		if(typeof suggestion_results.query.searchinfo != 'undefined') {
			var suggestion = suggestion_results.query.searchinfo.suggestion;
			return suggestion;
		} else {
			return false;
		}
	}

	function getFullTextSearchResults(term) {
		stopCurrentRequest();
		curReq = app.makeAPIRequest({
			action: 'query',
			list: 'search',
			srsearch: term,
			srinfo: '',
			srprop: ''
		}).done(function(data) {
			var searchResults = [];
			for(var i = 0; i < data.query.search.length; i++) {
				var result = data.query.search[i];
				searchResults.push(result.title);
			}
			renderResults([term, searchResults], false);
		}).fail(handleNetworkFailure);
		chrome.setSpinningReq(curReq);
		return curReq;
	}

	function getSearchResults(term, didyoumean) {
		stopCurrentRequest();
		curReq = app.makeAPIRequest({
			action: 'opensearch',
			search: term
		}).done(function(data) {
			var results = data;
			if(results[1].length === 0) { 
				getDidYouMeanResults(results);
			} else {
				if(typeof didyoumean == 'undefined') {
					didyoumean = false;
				}
				renderResults(results, didyoumean);
			}
		}).fail(handleNetworkFailure);
		chrome.setSpinningReq(curReq);
		return curReq;
	}

	function onSearchResultClicked() {
		var parent = $(this).parents(".listItemContainer");
		var url = parent.data("page-url");
		$("#search").focus(); // Hides the keyboard
		app.navigateToPage(url);
	}

	function onDoFullSearch() {
		performSearch($("#searchParam").val(), false);
	}

	function renderResults(results, didyoumean) {
		var template = templates.getTemplate('search-results-template');
		if(results.length > 0) {
			var searchParam = results[0];
			var searchResults = results[1].map(function(title) {
				return {
					key: app.urlForTitle(title),
					title: title
				};
			});
			if(didyoumean) {
				var didyoumean_link = {
					key: app.urlForTitle(results[0]),
					title: results[0]
				};
				$("#resultList").html(template.render({'pages': searchResults, 'didyoumean': didyoumean_link}));
			} else {
				$("#resultList").html(template.render({'pages': searchResults}));
			}
			$("#resultList .searchItem").click(onSearchResultClicked);
		}
		$("#doFullSearch").click(onDoFullSearch);
		$("#resultList .searchItem").bind('touchstart', function() {
			$("#searchParam").blur();
		});
		chrome.hideSpinner();
		chrome.hideOverlays();
		if(!chrome.isTwoColumnView()) {
			$("#content").hide(); // Not chrome.hideContent() since we want the header
		} else {
			$("html").addClass('overlay-open');
		}
		$('#searchresults').localize().show();
		chrome.setupScrolling('#searchresults .scroller');
	}

	return {
		performSearch: performSearch
	};
}();

