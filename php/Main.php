<?php
namespace Modern_Tribe\Idea_Garden;

use Modern_Tribe\Idea_Garden\Commands\Main as Commands;
use stdClass;

class Main {
	private $plugin_dir = '';
	private $plugin_url = '';

	private $commands;
	private $idea_statuses;
	private $voting;


	public function __construct( string $plugin_dir, string $plugin_url ) {
		$this->plugin_dir = $plugin_dir;
		$this->plugin_url = $plugin_url;

		$this->commands();
		$this->idea_statuses();
		$this->voting();

		add_shortcode( 'idea-garden', [ $this, 'shortcode' ] );
		add_action( 'wp_ajax_update_idea_garden_public_list', [ $this, 'public_list_updates' ] );
		add_action( 'wp_ajax_nopriv_update_idea_garden_public_list', [ $this, 'public_list_updates' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'frontend_assets' ] );
	}

	public function public_list_updates() {
		if ( empty( $_POST['form_id'] ) ) {
			wp_send_json_error();
		}

		wp_send_json_success( [
			'html' => $this->route( 'public_list', (object) [
				'list_only' => true,
				'form'      => (int) $_POST['form_id']
			] ),
		] );
	}

	public function shortcode( array $params = [] ) {
		$params = (object) shortcode_atts( [
			'view' => 'public_list',
			'form' => '0'
		],
			$params,
			'idea-garden'
		);

		do_action( 'idea_garden.cannot_route_shortcode_request', $params );
		return $this->route( $params->view, $params );
	}

	public function route( $view, stdClass $params ) {
		$handler = 'do_' . $view;

		if ( $params->form === 0 || ! method_exists( $this, $handler ) ) {
			do_action( 'idea_garden.cannot_route_request', $params );
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

		wp_enqueue_style( 'idea-garden-styles', $this->url() . 'styles/css/style.min.css' );
		wp_enqueue_script( 'idea-garden-script', $this->url() . 'js/scripts.js' );
	}

	public function dir(): string {
		return $this->plugin_dir;
	}

	public function url(): string {
		return $this->plugin_url;
	}

	public function commands(): Commands {
		return empty( $this->commands ) ? $this->commands = new Commands : $this->commands;
	}

	public function idea_statuses(): Idea_Statuses {
		return empty( $this->idea_statuses ) ? $this->idea_statuses = new Idea_Statuses : $this->idea_statuses;
	}

	public function voting(): Voting {
		return empty( $this->voting ) ? $this->voting = new Voting : $this->voting;
	}
}