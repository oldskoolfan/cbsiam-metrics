<?php

/**
 * @api
 *
 * Save page score data
 * Any score data can be sent as key=val post data
 *
 * @param string id The url of the school website, to use for key
 */

require __DIR__ . '/../vendor/autoload.php';

$dataHelper = new CbsiamMetrics\DataHelper();

$id = null;
$url = null;
$scoreData = [];
foreach ($_POST as $key => $val) {
	switch ($key) {
		case 'id':
			$id = $val;
			break;
		case 'url':
			$url = $val;
			break;
		default:
			$scoreData[$key] = $val;
	}
}

$data = $dataHelper->savePageScoreData($url, $id, $scoreData);

header('Content-type: application/json');
echo json_encode($data);
