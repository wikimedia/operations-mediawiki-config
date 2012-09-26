window.appHistory = function() {
	var MAX_LIMIT = 50;
	/**
	 * @fixme has side effects of adding things into history, not obvious from the name
	 */
	function addCurrentPage() {

		var title = app.getCurrentTitle();
		var url = app.getCurrentUrl();
		if(url == 'about:blank') {
			return;
		}

		var historyDB = new Lawnchair({name:"historyDB"},function() {
			this.keys(function(records) {
				if (records.length > MAX_LIMIT) {
					cleanupHistory(addCurrentPage);
				}else{			
					if (records.length == 0 || records[records.length - 1].value !== url) {
						// Add if the last thing we saw wasn't the same URL
						this.save({key: Date.now(), title: title, value: url});
					}
				}
			});
		});
	}

	// Removes first element from history
	function cleanupHistory(success) {
		var historyDB = new Lawnchair({name:"historyDB"}, function() {
			this.each(function(record, index) {
				if (index == 0) {
					// remove the first item, then add the latest item
					this.remove(record.key, success);
				}
			});
		});
	}

	// Removes all the elements from history
	function onClearHistory() {
		chrome.confirm(mw.message('clear-all-history-prompt').plain()).done(function(answer) {
			if (answer) {
				var historyDB = new Lawnchair({name:"historyDB"}, function() {
					this.nuke();
					chrome.showContent();
				});
			}
		});
	}

	function onHistoryItemClicked() {
		var parent = $(this).parents(".listItemContainer");
		var url = parent.data("page-url");
		app.navigateToPage(url);
	}

	function showHistory() {	
		var template = templates.getTemplate('history-template');
		var historyDB = new Lawnchair({name:"historyDB"}, function() {
			this.all(function(history) {
				$('#historyList').html(template.render({'pages': history.reverse()}));
				$(".historyItem").click(onHistoryItemClicked);
				$("#history .cleanButton").unbind('click', onClearHistory).bind('click', onClearHistory);
				chrome.hideOverlays();
				chrome.hideContent();
				$('#history').localize().show();
				chrome.setupScrolling('#history .scroller');
			});
		});

	}

	return {
		addCurrentPage: addCurrentPage,
		showHistory: showHistory
	};
}();
