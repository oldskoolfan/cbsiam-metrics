<?php

namespace CbsiamMetrics;

class ViewHelper {

	/**
	 * @var \Mustache_Engine
	 */
	public $mustache;

	/**
	 * init Mustache_Engine w/ template path on init
	 */
	public function __construct() {
		$templatePath = $_SERVER['DOCUMENT_ROOT'] . '/assets/templates';
		$this->mustache = new \Mustache_Engine([
			'loader' => new \Mustache_Loader_FilesystemLoader($templatePath)
		]);
	}

	/**
	 * render mustache template with passed data array
	 * @param  string $templateName
	 * @param  array  $data
	 * @return string
	 */
	public function render($templateName, array $data) {
		return $this->mustache->render($templateName, $data);
	}
}
