<?php

abstract class RestApi_ExtensionBase {
	protected $baseUrl;
	private $DEFAULT_ROUTE_OPTIONS = [
		'methods' => WP_REST_Server::READABLE,
	];

	public function __construct($pluginBaseUrl, $extensionBaseUrl) {
		$this->baseUrl = $pluginBaseUrl . '/' . $extensionBaseUrl;
	}

	/**
	 * @param string $subPath the path after the base url
	 * @param array $options options for the route (need at least callback)
	 * @see register_rest_route
	 * @see self::DEFAULT_ROUTE_OPTIONS
	 */
	public function register_route($subPath, $options) {
		$routeOptions = array_merge($this->DEFAULT_ROUTE_OPTIONS, $options);
		register_rest_route($this->baseUrl, $subPath, $routeOptions);
	}
}
