<?php

namespace CbsiamMetrics;

use Predis\Client;

class DataHelper {

	/**
	 * Redis client
	 * @var Predis\Client
	 */
	public $redis;

	/**
	 * Init redis client on class init
	 */
	public function __construct() {
		try {
			$client = new Client(getenv('REDIS_URL') ?? 'localhost');
			$this->redis = $client;
		} catch (\Exception $ex) {
			exit($ex->getMessage());
		}
	}

	/**
	 * For page init, we get all school urls, and for each url we get all
	 * scores that have been saved
	 *
	 * @return Page[] Array of Page objects
	 */
	public function getScoresForLinks() {
		try {
			$links = $this->redis->smembers('schoolUrls');
			$linkIds = [];
			$pages = [];

			// get all keys for each school url
			foreach ($links as $link) {
				$linkIds[$link] = $this->redis->sMembers($link);
			}

			// // get all data for each key
			foreach ($linkIds as $link => $keyArray) {
				// init page
				$page = new Page($link);
				foreach ($keyArray as $key) {
					array_push($page->scores,
						new PageScore(
							$key,
							$this->redis->hGetAll($key)
						)
					);
				}
				// sort scores by timestamp desc
				$sortScoresDesc = function($a, $b) {
					$tsA = $a->getTimestamp();
					$tsB = $b->getTimestamp();

					return $this->sort($tsA, $tsB, SortDirection::DESC);
				};
				usort($page->scores, $sortScoresDesc);
				array_push($pages, $page);
			}

			// sort pages alphabetically by url
			$sortPages = function ($a, $b) {
				$urlA = $a->getDisplayUrl();
				$urlB = $b->getDisplayUrl();

				return $this->sort($urlA, $urlB);
			};
			usort($pages, $sortPages);

			return $pages;
		} catch (\Exception $ex) {
			return false;
		}
	}

	/**
	 * Get rule results for a pagespeed score instance
	 * @param  string 		$scoreKey
	 * @return PageRule[]
	 */
	public function getPageRuleResults($scoreKey) {
		$rules = [];
		$ruleId = PageRuleCollection::getRuleKeyFromScoreKey($scoreKey);
		$ruleKeys = $this->redis->sMembers($ruleId);
		foreach ($ruleKeys as $key) {
			$rules[$key] = new PageRule($key, $this->redis->hGetAll($key));
		}

		$sortRulesDesc = function ($a, $b) {
			$impactA = $a->impact;
			$impactB = $b->impact;

			return $this->sort($impactA, $impactB, SortDirection::DESC);
		};
		usort($rules, $sortRulesDesc);

		return $rules;
	}

	/**
	 * Complete two redis operations to save page score data
	 * 1. save new key under url set
	 * 2. save data under key
	 * @param  string $url       	School url
	 * @param  string $id        	School url plus timestamp (www.example.com:148088080)
	 * @param  array $scoreData 	Array of score data
	 * @return array            	Status of save operation
	 */
	public function savePageScoreData($url, $id, array $scoreData) {
		try {
			// save new url id
			$this->redis->sAdd($url, $id);

			foreach ($scoreData as $key => $data) {
				if ($data !== null) {
					$this->redis->hSet($id, $key, $data);
				}
			}

			$data = $this->redis->hGetAll($id);
			$pageScore = new PageScore($id, $data);

			return [
				'key' => $id,
				'ts' => $pageScore->getTimestamp(),
				'status' => 0,
				'data' => $pageScore->data,
				'speedScore' => $pageScore->speedScore,
			];
		} catch (\Exception $ex) {
			return $this->getErrorStatus($ex);
		}
	}

	/**
	 * persist collection of page rules to redis
	 * @param  PageRuleCollection $ruleCollection
	 * @return array
	 */
	public function savePageRuleData(PageRuleCollection $ruleCollection) {
		try {
			foreach ($ruleCollection->rules as $rule) {
				$this->redis->sAdd($ruleCollection->id, $rule->key);
				$this->redis->hSet(
					$rule->key,
					PageRuleCollection::NAME,
					$rule->name
				);
				$this->redis->hSet(
					$rule->key,
					PageRuleCollection::IMPACT,
					$rule->impact
				);
			}

			return [
				'status' => 0,
				'rules' => $this->getPageRuleResults($ruleCollection->scoreKey),
			];
		} catch (\Exception $ex) {
			return $this->getErrorStatus($ex);
		}
	}

	/**
	 * Delete page score for key.
	 * Also delete key under url and associated rule data.
	 * @param  string $url 	School url
	 * @param  string $scoreKey 	Key for score
	 * @return Array 		Status of delete operation
	 */
	public function deleteScore($url, $scoreKey) {
		try {
			$ruleId = PageRuleCollection::getRuleKeyFromScoreKey($scoreKey);
			$ruleKeys = $this->redis->sMembers($ruleId);
			foreach($ruleKeys as $ruleKey) {
				$this->redis->del($ruleKey);
			}
			$this->redis->del($ruleId);
			$this->redis->sRem($url, $scoreKey);
			$this->redis->del($scoreKey);

			return [
				'status' => 0,
			];
		} catch (\Exception $ex) {
			return $this->getErrorStatus($ex);
		}
	}

	/**
	 * Shared sorting function
	 * @param  mixed $a
	 * @param  mixed $b
	 * @param  SortDirection $dir
	 * @return int
	 */
	private function sort($a, $b, $dir = SortDirection::ASC) {
		if ($a === $b) {
			return 0;
		}
		if (SortDirection::ASC === $dir) {
			return $a > $b ? 1 : -1;
		}

		return $a < $b ? 1 : -1;
	}

	/**
	 * Create array for api error response
	 * @param  \Exception $ex 	Exception that was thrown
	 * @return array     		Status array
	 */
	private function getErrorStatus($ex) {
		return [
			'status' => 1,
			'error' => $ex->getTrace(),
		];
	}
}
