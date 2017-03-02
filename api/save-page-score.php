<?php
require __DIR__ . '/../vendor/autoload.php';

$dataHelper = new CbsiamMetrics\DataHelper();

$url = null;
$scoreData = [];
foreach ($_POST as $key => $val) {
	if ($key === 'id') {
		$url = $val;
	} else {
		$scoreData[$key] = $val;
	}
}

if ($url !== null && count($scoreData) > 0) {
	// we have key and values, add timestamp
	$id = $url . ':' . time();
}

$data = $dataHelper->savePageScoreData($url, $id, $scoreData);

header('Content-type: application/json');
echo json_encode($data);
