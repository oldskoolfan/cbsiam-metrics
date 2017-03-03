<?php

namespace CbsiamMetrics;

use Predis\Client;

class DataHelper {
	public $redis;

	public function __construct() {
		try {
			$client = new Client(getenv('REDIS_URL') ?? 'localhost');
			$this->redis = $client;
		} catch (\Exception $ex) {
			exit($ex->getMessage());
		}
	}

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
						$score = new PageScore(
							$key,
							$this->redis->hGetAll($key)
						)
					);
				}
				array_push($pages, $page);
			}

			return $pages;
		} catch (\Exception $ex) {
			return false;
		}
	}

	public function savePageScoreData($url, $id, $scoreData) {
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
			];
		} catch (\Exception $ex) {
			return $this->getErrorStatus($ex);
		}
	}

	public function deleteScore($url, $key) {
		try {
			$this->redis->sRem($url, $key);
			$this->redis->del($key);
			return [
				'status' => 0,
			];
		} catch (\Exception $ex) {
			return $this->getErrorStatus($ex);
		}
	}

	private function getErrorStatus($ex) {
		return [
			'status' => 1,
			'error' => $ex->getMessage(),
		];
	}
}
