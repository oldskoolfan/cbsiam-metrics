#!/usr/local/bin/php
<?php

/**
 * @internal
 *
 * this script will:
 * 1. flush the redis cache
 * 2. scrape the cbsiam partner site for school urls
 * 3. load those as keys in redis
 * 4. get results from pagespeed api, save to redis
 */

require __DIR__ . '/../vendor/autoload.php';

use CbsiamMetrics\Scraper;
use CbsiamMetrics\DataHelper;
use CbsiamMetrics\HttpHelper;
use GuzzleHttp\Exception\ConnectException;
use Symfony\Component\DomCrawler\Crawler;

try {
	$dataHelper = new DataHelper();
	$scraper = new Scraper();

	echo "getting school links...\n";
	$crawler = $scraper->getSchoolListPage();

	$linkElements = $crawler->filter('div.work-link a')->each(
		function(Crawler $node, int $i) {
			return $node->attr('href');
		}
	);

	// flush database
	echo "flushing database...\n";
	$dataHelper->redis->flushDb();

	echo "saving school links...\n";
	foreach ($linkElements as $link) {
		$num = $dataHelper->redis->sAdd('schoolUrls', $link);
	}

	$httpHelper = new HttpHelper($linkElements, $dataHelper);
	$promise = $httpHelper->pool->promise();
	$promise->wait();
	echo "\nProcess completed successfully\n";
} catch (ConnectException $ex) {
	echo $ex->getMessage();
}
