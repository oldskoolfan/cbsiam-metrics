<?php

namespace CbsiamMetrics;

class PageScore {
	/**
	 * page score data
	 * @var array
	 */
	public $data;

	/**
	 * key for data built by url and timestamp
	 * e.g. www.example.com:148808808
	 * @var string
	 */
	public $urlKey;

	/**
	 * Overall speed score
	 * @var string
	 */
	public $speedScore;

	/**
	 * @param string $urlKey
	 * @param array  $data
	 */
	public function __construct(string $urlKey, array $data) {
		$scoreData = [];
		$this->speedScore = $data['speedScore'];
		unset($data['speedScore']);

		foreach ($data as $key => $val) {
			if (preg_match_all('/^([a-z]+)|([A-Z][a-z]+)/', $key, $matches)) {
				$scoreKey = strtolower(implode($matches[0], ' '));
				$scoreData[$scoreKey] = $val;
			}
		}

		$this->data = $scoreData;
		$this->urlKey = $urlKey;
	}

	/**
	 * get timestamp from key
	 * @return string
	 */
	public function getTimestamp() {
		$urlKey = explode(':', $this->urlKey);
		return array_pop($urlKey);
	}
}
