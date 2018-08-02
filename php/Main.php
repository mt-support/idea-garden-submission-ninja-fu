<?php
namespace Modern_Tribe\Idea_Garden\Ninja_Fu;

use stdClass;

class Main {
	private $plugin_dir = '';
	private $plugin_url = '';
	private $voting;

	public function __construct( string $plugin_dir, string $plugin_url ) {
		$this->plugin_dir = $plugin_dir;
		$this->plugin_url = $plugin_url;

		$this->voting();

		add_shortcode( 'idea-garden', [ $this, 'shortcode' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'frontend_assets' ] );
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

		return (string) new Public_List( $params );
	}

	public function frontend_assets() {
		if ( is_admin() ) {
			return;
		}

		wp_enqueue_style( 'idea-garden-styles', $this->url() . 'styles/css/style.css' );
		wp_enqueue_script( 'idea-gadden-script', $this->url() . 'js/scripts.js' );
	}

	public function dir(): string {
		return $this->plugin_dir;
	}

	public function url(): string {
		return $this->plugin_url;
	}

	public function voting(): Voting {
		return empty( $this->voting ) ? $this->voting = new Voting : $this->voting;
	}
}