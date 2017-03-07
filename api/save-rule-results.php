<?php

/**
 * @api
 *
 * Save page speed rule results and impact on score
 */
use CbsiamMetrics\DataHelper;
use CbsiamMetrics\PageRuleCollection;

require __DIR__ . '/../vendor/autoload.php';

$dataHelper = new DataHelper();

$id = null;
$ruleData = [];

foreach ($_POST as $key => $val) {
	if ($key === 'id') {
		$id = PageRuleCollection::getRuleKeyFromScoreKey($val);
	} else {
		$ruleData[$key] = $val;
	}
}

// convert raw data into PageRule objects
$rules = new PageRuleCollection($id, $ruleData);

$data = $dataHelper->savePageRuleData($rules);

header('Content-type: application/json');
echo json_encode($data);
