<?php

namespace CbsiamMetrics;

class PageScore {
	public $data;
	public $urlKey;

	public function __construct(string $urlKey, array $data) {
		$this->data = $data;
		$this->urlKey = $urlKey;
	}

	public function getTimestamp() {
		$urlKey = explode(':', $this->urlKey);
		return array_pop($urlKey);
	}
}
