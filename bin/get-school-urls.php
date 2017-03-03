#!/usr/local/bin/php
<?php
require __DIR__ . '/../vendor/autoload.php';

use CbsiamMetrics\Scraper;
use CbsiamMetrics\DataHelper;
use GuzzleHttp\Exception\ConnectException;
use Symfony\Component\DomCrawler\Crawler;

try {
	$dataHelper = new DataHelper();
	$scraper = new Scraper();
	$crawler = $scraper->getSchoolListPage();

	$linkElements = $crawler->filter('div.work-link a')->each(
		function(Crawler $node, int $i) {
			return $node->attr('href');
		}
	);

	foreach ($linkElements as $link) {
		$num = $dataHelper->redis->sAdd('schoolUrls', $link);
	}

	echo "School urls:\n";
	$links = $dataHelper->redis->sMembers('schoolUrls');

	foreach($links as $link) {
		echo "$link\n";
	}
} catch (ConnectException $ex) {
	echo $ex->getMessage();
}
