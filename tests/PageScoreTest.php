<?php

use PHPUnit\Framework\TestCase;
use CbsiamMetrics\PageScore;

class PageScoreTest extends TestCase {

	public function testGetTimestamp() {
		$urlKey = 'http://www.hopkinssports.com:1489007020';
		$score = new PageScore($urlKey, ['speedScore' => '19']);

		$this->assertEquals(1489007020, $score->getTimestamp());
	}

	public function testSplitCamelCaseScoreKey() {
		$urlKey = 'http://www.hopkinssports.com:1489007020';
		$score = new PageScore($urlKey, [
			'speedScore' => '19',
			'numberResources' => '63',
			'numberHosts' => '33',
			'totalRequestBytes' => '18202',
		]);

		$this->assertTrue(
			array_key_exists('number resources', $score->data)
		);
		$this->assertTrue(
			array_key_exists('number hosts', $score->data)
		);
		$this->assertTrue(
			array_key_exists('total request bytes', $score->data)
		);
	}
}
