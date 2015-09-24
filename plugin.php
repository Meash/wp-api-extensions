<?php
/**
 * Plugin Name: WP API Extensions
 * Description: Collection of extensions to the Wordpress REST API
 * Version: 0.1
 * Author: Martin Schrimpf
 * Author URI: https://github.com/Meash
 * License: MIT
 */

require_once __DIR__ . '/endpoints/RestApi_ModifiedContent.php';
require_once __DIR__ . '/endpoints/RestApi_WpmlLanguages.php';
require_once __DIR__ . '/endpoints/RestApi_Multisites.php';

const BASE_URL = 'extensions';
const API_VERSION = 0;

add_action('rest_api_init', function () {
	$pluginBaseUrl = BASE_URL . '/v' . API_VERSION;
	$endpoints = [
		new RestApi_ModifiedContent($pluginBaseUrl),
		new RestApi_WpmlLanguages($pluginBaseUrl),
		new RestApi_Multisites($pluginBaseUrl)
	];
	foreach ($endpoints as $endpoint) {
		$endpoint->register_routes();
	}
});
