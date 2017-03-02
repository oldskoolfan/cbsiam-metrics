<?php

namespace CbsiamMetrics;

use Predis\Client;

class DataHelper {
	public $redis;

	public function __construct($host = 'localhost') {
		try {
			$client = new Client(getenv('REDIS_URL') ?? 'localhost');
			$client->connect($host);
			$this->redis = $client;
		} catch (\Exception $ex) {
			exit($ex->getMessage());
		}
	}

	public function getScoresForLinks() {
		try {
			$links = $this->redis->smembers('schoolUrls');
			$pageScores = [];
			foreach ($links as $link) {
				array_push($pageScores,
					$score = new PageScore(
						$link,
						$this->redis->hGetAll($link)
					)
				);
			}

			return $pageScores;
		} catch (\Exception $ex) {
			return false;
		}
	}

	public function savePageScoreData($id, $scoreData) {
		try {
			foreach ($scoreData as $key => $data) {
				if ($data !== null) {
					$this->redis->hset($id, $key, $data);
				}
			}

			return [
				'status' => 0,
				'data' => $this->redis->hGetAll($id),
			];
		} catch (\Exception $ex) {
			return [
				'status' => 1,
				'error' => $ex->getMessage(),
			];
		}
	}
}
