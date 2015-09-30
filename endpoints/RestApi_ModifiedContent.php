<?php

require_once __DIR__ . '/RestApi_ExtensionBase.php';
require_once __DIR__ . '/helper/WpmlHelper.php';

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
		$this->wpml_helper = new WpmlHelper();
	}


	public function register_routes() {
		$args = [
			'since' => [
				'required' => true,
				'validate_callback' => [$this, 'validate_datetime']
			]
		];

		parent::register_route('/pages/', [
			'callback' => [$this, 'get_modified_pages'],
			'args' => $args
		]);
		parent::register_route('/posts/', [
			'callback' => [$this, 'get_modified_posts'],
			'args' => $args
		]);
	}

	public function get_modified_pages(WP_REST_Request $request) {
		return $this->get_modified_posts_by_type("page", $request);
	}

	public function get_modified_posts(WP_REST_Request $request) {
		return $this->get_modified_posts_by_type("post", $request);
	}

	private function get_modified_posts_by_type($type, WP_REST_Request $request) {
		$since = $request->get_param('since');
		$last_modified_gmt = $this
			->make_datetime($since)
			->setTimezone($this->datetime_zone_gmt)
			->format($this->datetime_query_format);

		$query_args = [
			'post_type' => $type,
			/* only after datetime */
			'date_query' => [
				'column' => 'post_modified_gmt',
				'after' => $last_modified_gmt,
			],
			/* keep order */
			'orderby' => 'menu_order',
			'order' => 'ASC',
			/* also show deleted items */
			'post_status' => ['publish', 'trash'],
			/* no pagination, show all */
			'posts_per_page' => -1,
		];
		$query = new WP_Query();
		$query_result = $query->query($query_args);

		$result = [];
		foreach ($query_result as $item) {
			$result[] = $this->prepare_item($item);
		}
		return $result;
	}

	private function prepare_item($post) {
		setup_postdata($post);
		return [
			'id' => $post->ID,
			'title' => $post->post_title,
			'type' => $post->post_type,
			'status' => $post->post_status,
			'modified_gmt' => $post->post_modified_gmt,
			'excerpt' => $this->prepare_excerpt($post),
			'content' => $post->post_content,
			'parent' => $post->post_parent,
			'order' => $post->menu_order,
			'available_languages' => $this->wpml_helper->get_available_languages($post->ID, $post->post_type)
		];
	}

	public function validate_datetime($arg) {
		return $this->make_datetime($arg) !== false;
	}

	private function make_datetime($arg) {
		return DateTime::createFromFormat($this->datetime_input_format, $arg);
	}

	private function prepare_excerpt($post) {
		return $post->post_excerpt ?:
			apply_filters('the_excerpt', apply_filters('get_the_excerpt', $post->post_excerpt));
	}
}
