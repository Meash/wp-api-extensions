<?php

require_once __DIR__ . '/RestApi_ExtensionBase.php';

/**
 * Retrieve the multisites defined in this network
 */
class RestApi_Multisites extends RestApi_ExtensionBase {
	const URL = 'multisites';

	public function __construct($pluginBaseUrl) {
		parent::__construct($pluginBaseUrl, self::URL);
	}


	public function register_routes() {
		parent::register_route('/', [
			'callback' => [$this, 'get_multisites']
		]);
	}

	public function get_multisites() {
		$multisites = wp_get_sites();

		$result = [];
		foreach ($multisites as $item) {
			$result[] = $this->prepare_item($item);
		}
		return $result;
	}

	private function prepare_item($blog) {
		$details = get_blog_details($blog);
		return [
			'id' => $blog['blog_id'],
			'name' => $details->blogname,
			'path' => $blog['path'],
			'description' => get_bloginfo($blog)
		];
	}
}
