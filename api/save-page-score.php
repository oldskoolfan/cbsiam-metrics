<?php
require __DIR__ . '/../vendor/autoload.php';

$dataHelper = new CbsiamMetrics\DataHelper();

$id = $_POST['id'] ?? null;
$scoreData = [
	'speedScore' => $_POST['speedScore'] ?? null,
];

$data = $dataHelper->savePageScoreData($id, $scoreData);

header('Content-type: application/json');
echo json_encode($data);
