<?php

namespace CbsiamMetrics;

class PageRule {

	/**
	 * @var string
	 */
	public $key;

	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var int
	 */
	public $impact;

	/**
	 * @param string $key
	 * @param string $name
	 * @param int $impact
	 */
	public function __construct($key, $name = null, $impact = null) {
		$this->key = $key;
		$this->name = $name;
		$this->impact = $impact;
	}
}
