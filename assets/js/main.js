/**
 * @namespace cbsiamMetrics
 * Init cbsiamMetrics namespace and pageScoreController class
 */
(function ($, factory) {
	'use strict';

	window.cbsiamMetrics = window.cbsiamMetrics || {};
	$.extend(window.cbsiamMetrics, factory($));

	// load mustache template
	$.get('/assets/templates/page-score.mustache')
	.done((template) => {
		cbsiamMetrics.scores = template;
		Mustache.parse(cbsiamMetrics.scores);
	})
	.fail((err) => console.error(err));

	/**
	 * jQuery document.ready function - init pageScoreControllers and delete
	 * button event handlers
	 */
	$(function() {
		const DEL_SCORE_URL = '/api/delete-page-score.php';
		let $pageScores = $('#page-scores'),
			deleteScore = function () {
				let $delBtn = $(this),
				url = $delBtn.closest('.card').data('url'),
				key = $delBtn.data('key'),
				sure = confirm('Are you sure you want to delete this score?');

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
					// update count
					let $cardBlock = $delBtn.closest('div.card-block'),
						scoreCount = $cardBlock.data('scores');
					$cardBlock.data('scores', scoreCount - 1);

					$delBtn.closest('div.page-score').remove();
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
	const SAVE_RULES_URL = '/api/save-rule-results.php';

	/**
	 * @class
	 * @param {DOMElement} el Dom element this controller is attached to
	 */
	function PageScoreController(el) {
		this.$el = $(el);
		this.$cardBlock = this.$el.find('div.card-block');
		this.url = this.$el.data('url');
		this.$primaryBtn = this.$el.find('.btn-primary');
		this.$primaryBtn.bind('click', {
				controller: this
			},
			this.getPageScore
		);
		this.gettingScore = false;
	}

	PageScoreController.prototype = {
		/**
		 * Call google pagespeed api, then call our api to persist data
		 * @param  {Event} e
		 * @return {void}
		 */
		getPageScore: function (e) {
			let controller = e.data.controller,
			icon = '<span class="loading-icon"><i class="fa fa-cog ' +
				'fa-spin fa-lg fa-fw"></i>Getting Pagespeed results...</span>';

			// throttling
			if (controller.gettingScore) {
				return;
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
				controller.$el.find('.loading-icon').remove();
				console.error(err);
			});
		},

		/**
		 * Update the DOM to reflect changes we persisted; i.e. create a new
		 * page score table row
		 * @param  {PageScoreController} 	controller
		 * @param  {object} 				response	response from save api
		 * @return {Promise}
		 */
		updateDataTable: function (controller, allResponse) {
			return new Promise((resolve, reject) => {
				let scoreResponse = allResponse[0],
				ruleResponse = allResponse[1],
				response = $.extend({}, scoreResponse, ruleResponse);

				controller.$el.find('.loading-icon').remove();

				if (response.status !== 0) {
					reject(response);
				}

				let dateTime = moment.unix(response.ts).format('M-D-Y hh:mm:ss A'),
				cardId = controller.$cardBlock.data('id'),
				scoreId = controller.$cardBlock.data('scores'),
				rowId = '' + cardId + scoreId,
				scores = ((data) => {
					let scores = [];
					for (let key in data) {
						scores.push({ 'key': key, 'val': data[key] });
					}

					return scores;
				})(response.data),
				rules = ((data) => {
					let rules = [],
						rule;
					for (let key in data) {
						rules.push(data[key]);
					}

					return rules;
				})(response.rules),
				row = Mustache.render(cbsiamMetrics.scores, {
					rowId: rowId,
					cardId: cardId,
					dateTime: dateTime,
					urlKey: response.key,
					speedScore: response.speedScore,
					scores: scores,
					rules: rules
				});

				// update count
				controller.$cardBlock.data('scores', scoreId + 1);

				// add to card block
				controller.$cardBlock.prepend(row);
				resolve();
			});
		},

		/**
		 * Call our save api to persist page score data
		 * @param  {string} url     school website url
		 * @param  {object} results results from google pagespeed api
		 * @return {Promise}
		 */
		storeScoreResults: function (url, results) {
			let json = results,
			speedScore = json.ruleGroups.SPEED.score,
			id = url + ':' + Math.floor(Date.now() / 1000),
			scoreData = {
				id: id,
				url: url,
				speedScore: speedScore
			},
			ruleData = {
				id: id
			},
			rule;

			// get score data
			for (let key in results.pageStats) {
				scoreData[key] = results.pageStats[key];
			}

			// get rule data
			for (let key in results.formattedResults.ruleResults) {
				rule = results.formattedResults.ruleResults[key];
				ruleData[key + ':name'] = rule.localizedRuleName;
				ruleData[key + ':impact'] = rule.ruleImpact;
			}

			return Promise.all([
				cbsiamMetrics.sendAjaxRequest(
					SAVE_SCORE_URL,
					'POST',
					scoreData
				),
				cbsiamMetrics.sendAjaxRequest(
					SAVE_RULES_URL,
					'POST',
					ruleData
				)
			]);
		}
	};

	return {
		PageScoreController: PageScoreController
	}
});
