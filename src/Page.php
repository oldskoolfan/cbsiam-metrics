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

	/**
	 * remove http://www. and trailing slashes when displaying
	 * school urls
	 * @return string
	 */
	public function getDisplayUrl() {
		return preg_replace(
			'/(^(http:)*\/\/(www\.)*)|(^www\.)|(\/$)/',
			'',
			$this->url
		);
	}
}
