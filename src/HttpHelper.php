<?php

namespace CbsiamMetrics;

use GuzzleHttp\Pool;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\ClientException;

class HttpHelper {
	/**
	 * @var string
	 */
	const PAGESPEED_URL = 'https://www.googleapis.com/pagespeedonline/v2/runPagespeed';

	/**
	 * @var GuzzleHttp\Pool
	 */
	public $pool;

	/**
	 * @var GuzzleHttp\Client
	 */
	private $client;

	/**
	 * @var DataHelper
	 */
	private $dataHelper;

	/**
	 * @var array
	 */
	private $links;

	/**
	 * @param array      $links
	 * @param DataHelper $dataHelper
	 */
	public function __construct(array $links, DataHelper &$dataHelper) {
		$this->client = new Client();
		$this->dataHelper = $dataHelper;
		$this->links = $links;
		$this->generateRequests($links);
	}

	/**
	 * Do the heavy lifting: create a promise pool
	 * firing 5 requests at a time to the pagespeed api
	 * @param  array  $links
	 * @return void
	 */
	private function generateRequests(array $links) {
		$promises = function ($links) {
			foreach ($links as $link) {
				yield function() use ($link) {
					return $this->client->getAsync(self::PAGESPEED_URL, [
						'query' => ['url' => $link],
					]);
				};
			}
		};
		$this->pool = new Pool($this->client, $promises($links), [
			'concurrency' => 5,
			'fulfilled' => function ($resp, $i) {
				$response = json_decode($resp->getBody());
				$url = $this->links[$i];
				$scoreKey = $url . ':' . time();
				$this->saveScoreData($url, $scoreKey, $response);
				$this->saveRuleData($scoreKey, $response);
				echo "data saved for $url...\n";
			},
			'rejected' => function (\Exception $e) {
				echo $e->getMessage();
			},
		]);
	}

	/**
	 * persist score data to redis via dataHelper
	 * @param  string 	$url
	 * @param  string 	$scoreKey
	 * @param  stdClass $response
	 * @return void
	 */
	private function saveScoreData($url, $scoreKey, $response) {
		$scoreData = [
			'speedScore' => $response->ruleGroups->SPEED->score,
		];

		foreach ($response->pageStats as $key => $val) {
			$scoreData[$key] = $val;
		}

		$this->dataHelper->savePageScoreData($url, $scoreKey, $scoreData);
	}

	/**
	 * persist rule data to redis via dataHelper
	 * @param  string 	$scoreKey
	 * @param  stdClass $response
	 * @return void
	 */
	private function saveRuleData($scoreKey, $response) {
		$ruleData = [];

		foreach($response->formattedResults->ruleResults as $key => $val) {
			$ruleData[$key . ':name'] = $val->localizedRuleName;
			$ruleData[$key . ':impact'] = $val->ruleImpact;
		}

		$rules = new PageRuleCollection($scoreKey, $ruleData);
		$this->dataHelper->savePageRuleData($rules);
	}
}
