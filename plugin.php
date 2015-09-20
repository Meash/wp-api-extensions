<?php
/**
 * Plugin Name: WP API Modified Content
 * Description: Retrieve only modified content, given the modification datetime
 * Version: 0.1
 * Created: 19.09.2015 23:08
 * Author: Martin Schrimpf
 * Author URI: https://github.com/Meash
 * License: MIT
 */


add_action('rest_api_init', function () {
	$endpoint = new RESTAPIModifiedContent_API_Site();
	$endpoint->register();
});

class RESTAPIModifiedContent_API_Site {
	private $BASE_URL = 'modified_content';

	public function register() {
		register_rest_route($this->BASE_URL, '/posts_and_pages/(?P<last_modified_gmt>.*)', array(
			'methods' => WP_REST_Server::READABLE,
			'callback' => array($this, 'get_modified_posts_and_pages'),
		));
	}

	public function get_modified_posts_and_pages($data) {
		$last_modified_gmt = $data['last_modified_gmt'];
		if (!$this->validate_datetime($last_modified_gmt)) {
			return new WP_Error("wp-api-modified-content_datetime_invalid",
				"Invalid datetime '$last_modified_gmt'",
				array('status' => 400));
		}
		$args = array(
			'date_query' => array(
				array(
					'column' => 'post_modified_gmt',
					'after' => $last_modified_gmt,
				),
			),
			'posts_per_page' => -1,
		);
		$query = new WP_Query();
		$query_result = $query->query($args);

		$result = array();
		foreach ($query_result as $item) {
			$result[] = $this->prepare_item($item);
		}
		return $result;
	}

	private function prepare_item($item) {
		return [
			'id' => $item->ID,
			'title' => $item->post_title,
			'type' => $item->post_type,
			'modified_gmt' => $item->post_modified_gmt,
			'excerpt' => $item->post_excerpt,
			'content' => $item->post_content,
			'parent' => $item->post_parent
		];
	}

	private function validate_datetime($arg) {
		return DateTime::createFromFormat('Y-m-d G:i:s', $arg) !== false;
	}
}
