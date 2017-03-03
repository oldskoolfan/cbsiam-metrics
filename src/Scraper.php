<?php

namespace CbsiamMetrics;

use Goutte\Client;
use GuzzleHttp\Exception\ConnectException;
use Symfony\Component\DomCrawler\Crawler;

class Scraper extends Client {
	/**
	 * public url for obtaining cbsiam school websites
	 * @var string
	 */
	const SCHOOL_URL = 'http://www.cbsiam.com/partner-list.html';

	/**
	 * Return a crawler for getting school urls
	 * @return Symfony\Component\DomCrawler\Crawler
	 */
	public function getSchoolListPage() {
		return $this->request('GET', self::SCHOOL_URL);
	}
}
