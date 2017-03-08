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
	 * @var string
	 */
	public $colorClass;

	/**
	 * @param string $key
	 * @param string $name
	 * @param int $impact
	 * @param string $class
	 */
	public function __construct($key, array $vals = []) {
		$impact = isset($vals['impact']) ? (float) $vals['impact'] : null;
		$name = $vals['name'] ?? null;

		if ('double' === gettype($impact)) {
			$impact = round($impact, 4);
			$this->colorClass = $this->getRuleColorClass($impact);
		}

		$this->key = $key;
		$this->name = $name;
		$this->impact = $impact;
	}

	private function getRuleColorClass(float $impact) {
		if ($impact > 10) {
			return 'bg-danger';
		}
		if ($impact > 0) {
			return 'bg-warning';
		}

		return 'bg-success';
	}
}
