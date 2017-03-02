<?php

namespace CbsiamMetrics;

class PageScore {
	public $data;
	public $url;

	public function __construct($url, array $data) {
		$this->data = $data;
		$this->url = $url;
	}
}
