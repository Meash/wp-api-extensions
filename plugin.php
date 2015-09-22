<?php
/**
 * Plugin Name: WP API Extensions
 * Description: Collection of extensions to the Wordpress REST API (WP API)
 * Version: 0.1
 * Author: Martin Schrimpf
 * Author URI: https://github.com/Meash
 * License: MIT
 */

require_once __DIR__ . '/endpoints/RestApi_ModifiedContent.php';
require_once __DIR__ . '/endpoints/RestApi_WpmlLanguages.php';
require_once __DIR__ . '/endpoints/RestApi_Multisites.php';

add_action('rest_api_init', function () {
	$endpoints = [
		new RestApi_ModifiedContent(),
		new RestApi_WpmlLanguages(),
		new RestApi_Multisites()
	];
	foreach ($endpoints as $endpoint) {
		$endpoint->register_routes();
	}
});
