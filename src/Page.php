<?php

namespace CbsiamMetrics;

class Page {
	public $url;
	public $scores;

	public function __construct(string $url, array $scores = []) {
		$this->url = $url;
		$this->scores = $scores;
	}
}
