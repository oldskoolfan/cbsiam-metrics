(function ($, factory) {
	'use strict';

	window.cbsiamMetrics = window.cbsiamMetrics || {};
	$.extend(window.cbsiamMetrics, factory($));

	$(function() {
		const DEL_SCORE_URL = '/api/delete-page-score.php';
		let $pageScores = $('#page-scores'),
			deleteScore = function () {
				let $delBtn = $(this),
					url = $delBtn.closest('.card').data('url'),
					key = $delBtn.data('key'),
					sure = confirm('Are you sure you want to delete this score?');
				console.debug(key);
				if (!sure) {
					return false;
				}
				cbsiamMetrics.sendAjaxRequest(
					DEL_SCORE_URL,
					'POST',
					{
						'key': key,
						'url': url
					}
				).then((response) => {
					$delBtn.closest('tr').remove();
				}).catch((err) => console.error(err));
			};
		$pageScores.find('.card').each(function(i, el) {
			let $el = $(el);
			$el.data('controller',
				new cbsiamMetrics.PageScoreController(el));
			$el.on('click', '.fa-close', deleteScore);
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
		this.gettingScore = false;
		this.$delBtn = this.$el.find('.fa-close');
	}

	PageScoreController.prototype = {
		getPageScore: function (e) {
			let controller = e.data.controller,
			icon = '<span id="loading-icon"><i class="fa fa-cog ' +
				'fa-spin fa-lg fa-fw"></i>Getting Pagespeed results...</span>';

			// throttling
			if (controller.gettingScore) {
				return false;
			}

			controller.gettingScore = true;

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
				controller.gettingScore = false;

				return controller.updateDataTable(controller, response);
			})
			.catch((err) => {
				controller.gettingScore = false;
				$('#loading-icon').remove();
				console.error(err);
			});
		},
		updateDataTable: function (controller, response) {
			return new Promise((resolve, reject) => {
				let dateTime = moment.unix(response.ts)
					.format('M-D-Y hh:mm:ss A');
				if (response.status == 0) {
					let $row = controller.$table.find('tr').
						first().clone();
					$row.children().last().children()
					.first()
						.html(response.data.speedScore)
					.end()
					.last()
						.find('span.dt')
							.html(dateTime)
						.end()
						.find('i.fa-close')
							.data('key', response.key)
						.end()
					.end();
					$row.show();
					controller.$table.prepend($row);
					resolve();
				} else {
					reject(response);
				}
				$('#loading-icon').remove();
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
