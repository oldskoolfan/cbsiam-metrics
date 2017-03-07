<?php

namespace CbsiamMetrics;

class PageRuleCollection {

	/**
	 * @var string
	 */
	const NAME = 'name';

	/**
	 * @var string
	 */
	const IMPACT = 'impact';

	/**
	 * @var string
	 */
	const RULE_NAMESPACE = 'rules';

	public $id;

	public $rules = [];

	public function __construct(string $id, array $data) {
		$this->id = $id;
		foreach ($data as $key => $val) {
			$this->parseData($key, $val);
		}
	}

	public static function getRuleKeyFromScoreKey(string $scoreKey) {
		return $scoreKey .= ':' . self::RULE_NAMESPACE;
	}

	private function parseData(string $key, string $val) {
		$ruleKey = null;
		$ruleName = null;
		$ruleImpact = null;
		$keyArray = explode(':', $key);
		if (count($keyArray) === 2) {
			$ruleKey = $this->id . ':' . $keyArray[0];
			switch ($keyArray[1]) {
				case self::NAME:
					$ruleName = $val;
					break;
				case self::IMPACT:
					$ruleImpact = $val;
					break;
			}
		}
		if ($ruleKey === null) {
			return;
		}
		if (array_key_exists($ruleKey, $this->rules)) {
			$rule = $this->rules[$ruleKey];
		} else {
			$rule = new PageRule($ruleKey);
		}
		if ($ruleName !== null) {
			$rule->name = $ruleName;
		}
		if ($ruleImpact !== null) {
			$rule->impact = (int)$ruleImpact;
		}
		$this->rules[$ruleKey] = $rule;
	}
}
