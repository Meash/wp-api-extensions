<?php

require_once __DIR__ . '/RestApi_ExtensionBase.php';

/**
 * Retrieve only content that has been modified since a given datetime
 */
class RestApi_ModifiedContent extends RestApi_ExtensionBase {
	const URL = 'modified_content';
	private $datetime_input_format = DateTime::ATOM;
	private $datetime_query_format = DateTime::ATOM;
	private $datetime_zone_gmt;

	public function __construct($namespace) {
		parent::__construct($namespace, self::URL);
		$this->datetime_zone_gmt = new DateTimeZone('GMT');
	}


	public function register_routes() {
		parent::register_route('/posts_and_pages/', [
			'callback' => [$this, 'get_modified_posts_and_pages'],
			'args' => [
				'since' => [
					'required' => true,
					'validate_callback' => [$this, 'validate_datetime']
				]
			]
		]);
	}

	public function get_modified_posts_and_pages(WP_REST_Request $request) {
		$since = $request->get_param('since');
		$last_modified_gmt = $this
			->make_datetime($since)
			->setTimezone($this->datetime_zone_gmt)
			->format($this->datetime_query_format);

		$query_args = [
			'post_type' => ['post', 'page'],
			'date_query' => [
				'column' => 'post_modified_gmt',
				'after' => $last_modified_gmt,
			],
			'posts_per_page' => -1 /* show all */,
		];
		$query = new WP_Query();
		$query_result = $query->query($query_args);

		$result = [];
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
			'excerpt' => $item->post_content ?: wp_trim_excerpt($item->post_content),
			'content' => $item->post_content,
			'parent' => $item->post_parent
		];
	}

	public function validate_datetime($arg) {
		return $this->make_datetime($arg) !== false;
	}

	private function make_datetime($arg) {
		return DateTime::createFromFormat($this->datetime_input_format, $arg);
	}
}
