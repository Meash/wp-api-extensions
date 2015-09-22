<?php

class RestApi_WpmlLanguages {
	private $URL = 'languages';

	public function register_routes() {
		register_rest_route($this->URL, '/wpml', array(
			'methods' => WP_REST_Server::READABLE,
			'callback' => array($this, 'get_wpml_languages'),
		));
	}

	public function get_wpml_languages() {
		$languages = icl_get_languages('skip_missing=0&orderby=KEY&order=DIR&link_empty_to=str');

		$result = array();
		foreach ($languages as $item) {
			$result[] = $this->prepare_item($item);
		}
		return $result;
	}

	private function prepare_item($language) {
		print_r($language);
		return [
			'short_name' => $language->short_name,
			'long_name' => $language->long_name,
			'icon' => $language->icon,
		];
	}
}
