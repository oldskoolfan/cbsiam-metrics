(function ($, factory) {
	'use strict';
	window.cbsiamMetrics = window.cbsiamMetrics || {};
	$.extend(window.cbsiamMetrics, factory($));

	$(function() {
		let $pageScores = $('#page-scores');
		$pageScores.find('.card').each(function(i, el) {
			$(el).data('controller',
				new cbsiamMetrics.PageScoreController(el));
		});
	});
})(jQuery, function ($) {
	const PAGESPEED_URL = 'https://www.googleapis.com/pagespeedonline/v2/runPagespeed';
	const SAVE_SCORE_URL = '/api/save-page-score.php';

	function PageScoreController(el) {
		this.$el = $(el);
		this.$table = this.$el.find('table');
		this.url = this.$el.data('url');
		this.$primaryBtn = this.$el.find('.btn-primary');
		this.$primaryBtn.bind('click', {
				controller: this
			},
			this.getPageScore
		);
	}

	PageScoreController.prototype = {
		getPageScore: function (e) {
			let controller = e.data.controller,
			icon = '<span id="loading-icon"><i class="fa fa-cog ' +
				'fa-spin fa-lg fa-fw"></i>Getting Pagespeed results...</span>';

			// show loading icon
			controller.$primaryBtn.after(icon);

			cbsiamMetrics.sendAjaxRequest(
				PAGESPEED_URL,
				'GET', {
					url: controller.url
				}
			)
			.then((data) => {
				return controller.storeScoreResults(controller.url, data);
			})
			.then((response) => {
				return controller.updateDataTable(controller, response);
			})
			.catch((err) => {
				$('#loading-icon').remove();
				console.error(err);
			});
		},
		updateDataTable: function (controller, response) {
			return new Promise((resolve, reject) => {
				$('#loading-icon').remove();
				console.debug(response);
				if (response.status == 0) {
					let $row = controller.$table.find('tr').
						first().clone();
					$row.find('span').first()
						.html(response.data.speedScore)
					.end()
					.last()
						.html(response.ts)
					.end();
					$row.show();
					controller.$table.prepend($row);
					resolve();
				} else {
					reject(response);
				}
			});
		},
		storeScoreResults: function (url, results) {
			let json = results,
				speedScore = json.ruleGroups.SPEED.score,
				postData = {
					id: url,
					speedScore: speedScore
				};
			return cbsiamMetrics.sendAjaxRequest(
				SAVE_SCORE_URL,
				'POST',
				postData
			);
		}
	};

	return {
		PageScoreController: PageScoreController
	}
});
