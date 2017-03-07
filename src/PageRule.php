<?php

namespace CbsiamMetrics;

class PageRule {

	public $key;

	public $name;

	public $impact;

	public function __construct($key, $name = null, $impact = null) {
		$this->key = $key;
		$this->name = $name;
		$this->impact = $impact;
	}
}
