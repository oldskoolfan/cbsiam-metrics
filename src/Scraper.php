<?php

namespace CbsiamMetrics;

use Goutte\Client;
use GuzzleHttp\Exception\ConnectException;
use Symfony\Component\DomCrawler\Crawler;

class Scraper extends Client {
	const SCHOOL_URL = 'http://www.cbsiam.com/partner-list.html';

	public function getSchoolListPage() {
		return $this->request('GET', self::SCHOOL_URL);
	}
}
