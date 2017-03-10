<?php

namespace CbsiamMetrics;

use GuzzleHttp\Pool;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\ClientException;

class HttpHelper {

	const PAGESPEED_URL = 'https://www.googleapis.com/pagespeedonline/v2/runPagespeed';

	public $pool;

	private $client;

	private $dataHelper;

	private $links;

	public function __construct(array $links, DataHelper &$dataHelper) {
		$this->client = new Client();
		$this->dataHelper = $dataHelper;
		$this->links = $links;
		$this->generateRequests($links);
	}

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

	private function saveScoreData($url, $scoreKey, $response) {
		$scoreData = [
			'speedScore' => $response->ruleGroups->SPEED->score,
		];

		foreach ($response->pageStats as $key => $val) {
			$scoreData[$key] = $val;
		}

		$this->dataHelper->savePageScoreData($url, $scoreKey, $scoreData);
	}

	private function saveRuleData($scoreKey, $response) {
		$ruleData = [];

		foreach($response->formattedResults->ruleResults as $key => $val) {
			$ruleData[$key . ':name'] = $val->localizedRuleName;
			$ruleData[$key . ':impact'] = $val->ruleImpact;
			$rules = new PageRuleCollection($scoreKey, $ruleData);
			$this->dataHelper->savePageRuleData($rules);
		}
	}
}
