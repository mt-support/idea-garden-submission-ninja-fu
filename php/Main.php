<?php
namespace Modern_Tribe\Idea_Garden\Ninja_Fu;

use stdClass;

class Main {
	private $plugin_dir = '';
	private $plugin_url = '';

	/** @var Template_Posts */
	private $template_posts;

	public function __construct( string $plugin_dir, string $plugin_url ) {
		$this->plugin_dir = $plugin_dir;
		$this->plugin_url = $plugin_url;

		$this->template_posts();

		add_shortcode( 'idea-garden', [ $this, 'shortcode' ] );
	}

	public function shortcode( array $params = [] ) {
		$params = (object) shortcode_atts( [
			'view' => 'public_list',
			'form' => '0'
		],
			$params,
			'idea-garden'
		);

		do_action( 'idea_garden.ninja_fu.cannot_route_shortcode_request', $params );
		return $this->route( $params->view, $params );
	}

	public function route( $view, stdClass $params ) {
		$handler = 'do_' . $view;

		if ( $params->form === 0 || ! method_exists( $this, $handler ) ) {
			do_action( 'idea_garden.ninja_fu.cannot_route_request', $params );
			return;
		}

		return call_user_func( [ $this, $handler ], $params );
	}

	public function do_public_list( stdClass $params ) {
		if ( ! isset( $params->form ) || ! is_numeric( $params->form ) || (int) $params->form === 0 ) {
			return '<p class="warning">Need a valid form ID</p>';
		}

		return (string) new Public_List( $params->form );
	}

	public function dir(): string {
		return $this->plugin_dir;
	}

	public function url(): string {
		return $this->plugin_url;
	}

	public function template_posts(): Template_Posts {
		return empty( $this->template_posts ) ? $this->template_posts = new Template_Posts : $this->template_posts;
	}
}