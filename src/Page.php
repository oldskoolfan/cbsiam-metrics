<?php

namespace CbsiamMetrics;

class Page {
	/**
	 * school website url
	 * @var string
	 */
	public $url;

	/**
	 * array of page scores
	 * @var PageScores[]
	 */
	public $scores;

	/**
	 * @param string $url
	 * @param array $scores
	 */
	public function __construct(string $url, array $scores = []) {
		$this->url = $url;
		$this->scores = $scores;
	}
}
