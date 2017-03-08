<?php

use PHPUnit\Framework\TestCase;
use CbsiamMetrics\PageRule;

class PageRuleTest extends TestCase {

	public function testGetRuleColorClass() {
		$bigImpact = 29.999;
		$medImpact = 8.5;
		$smallImpact = 0.45;
		$noImpact = 0;
		$noImpactFloat = 0.0000;
		$dangerClass = 'bg-danger';
		$warningClass = 'bg-warning';
		$successClass = 'bg-success';
		$key = 'http://www.example.com:148801234:optimizeImages';

		$rule = new PageRule($key, ['impact' => $bigImpact]);
		$this->assertEquals($dangerClass, $rule->colorClass);

		$rule = new PageRule($key, ['impact' => $medImpact]);
		$this->assertEquals($warningClass, $rule->colorClass);

		$rule = new PageRule($key, ['impact' => $smallImpact]);
		$this->assertEquals($warningClass, $rule->colorClass);

		$rule = new PageRule($key, ['impact' => $noImpact]);
		$this->assertEquals($successClass, $rule->colorClass);

		$rule = new PageRule($key, ['impact' => $noImpactFloat]);
		$this->assertEquals($successClass, $rule->colorClass);

		$rule = new PageRule($key, []);
		$this->assertNull($rule->colorClass);
	}
}
