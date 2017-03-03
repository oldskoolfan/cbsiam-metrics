<?php

/**
 * @api
 *
 * Delete a page score record based on url and key
 *
 * @param string key
 * @param string url
 */

require __DIR__ . '/../vendor/autoload.php';

$dataHelper = new CbsiamMetrics\DataHelper();

$key = $_POST['key'] ?? null;
$url = $_POST['url'] ?? null;

header('Content-type: application/json');
echo json_encode($dataHelper->deleteScore($url, $key));
