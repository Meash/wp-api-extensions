<?php

/**
 * Retrieve the multisites defined in this network
 */
class RestApi_Multisites {
	private $URL = 'multisites';

	public function register_routes() {
		register_rest_route($this->URL, '/', array(
			'methods' => WP_REST_Server::READABLE,
			'callback' => array($this, 'get_multisites'),
		));
	}

	public function get_multisites() {
		$multisites = get_blog_list(0, 'all');

		$result = array();
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
