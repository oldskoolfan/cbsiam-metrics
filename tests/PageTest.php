<?php

use PHPUnit\Framework\TestCase;
use CbsiamMetrics\Page;

class PageTest extends TestCase {

	public function testGetDisplayUrl() {
		$fullUrl = 'http://www.example.com';
		$trailingUrl = 'http://www.example.com/';
		$noWwwUrl = 'http://example.com';
		$noHttpUrl = 'www.example.com';
		$noProtocolUrl = '//www.example.com';
		$subDomainUrl = 'http://subdomain.example.com';
		$displayUrl = 'example.com';

		$page = new Page($fullUrl);
		$this->assertEquals($displayUrl, $page->getDisplayUrl());

		$page = new Page($trailingUrl);
		$this->assertEquals($displayUrl, $page->getDisplayUrl());

		$page = new Page($noWwwUrl);
		$this->assertEquals($displayUrl, $page->getDisplayUrl());

		$page = new Page($noHttpUrl);
		$this->assertEquals($displayUrl, $page->getDisplayUrl());

		$page = new Page($noProtocolUrl);
		$this->assertEquals($displayUrl, $page->getDisplayUrl());

		$page = new Page($subDomainUrl);
		$this->assertEquals('subdomain.example.com', $page->getDisplayUrl());
	}
}
