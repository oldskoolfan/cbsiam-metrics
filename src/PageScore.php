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
	 * @param string $urlKey
	 * @param array  $data
	 */
	public function __construct(string $urlKey, array $data) {
		$this->data = $data;
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
