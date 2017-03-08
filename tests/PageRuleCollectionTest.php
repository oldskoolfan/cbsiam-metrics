<?php

use PHPUnit\Framework\TestCase;
use CbsiamMetrics\PageRule;
use CbsiamMetrics\PageRuleCollection;

class PageRuleCollectionTest extends TestCase {

	public function testGetRuleKeyFromScoreKey() {
		$scoreKey = 'someScoreKey';

		$this->assertEquals('someScoreKey:rules',
			PageRuleCollection::getRuleKeyFromScoreKey($scoreKey));
	}

	public function testParseData() {
		$id = 'http://www.hopkinssports.com:1489007020';
		$redirectKey = $id . ':rules:AvoidLandingPageRedirects';
		$gzipKey = $id . ':rules:EnableGzipCompression';
		$ruleData = [
			'AvoidLandingPageRedirects:name' => 'Avoid landing page redirects',
			'AvoidLandingPageRedirects:impact' => '0',
			'EnableGzipCompression:name' => 'Enable compression',
			'EnableGzipCompression:impact' => '41.10830000000002',
		];
		$rules = new PageRuleCollection($id, $ruleData);
		$redirectRule = $rules->rules[$redirectKey];
		$gzipRule = $rules->rules[$gzipKey];
		$this->assertEquals('Avoid landing page redirects',
			$redirectRule->name);
		$this->assertEquals(0, $redirectRule->impact);
		$this->assertEquals('Enable compression', $gzipRule->name);
		$this->assertEquals(41.1083, $gzipRule->impact);
	}
}
