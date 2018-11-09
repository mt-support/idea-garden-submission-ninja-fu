<?php
namespace Modern_Tribe\Idea_Garden;

use Modern_Tribe\Idea_Garden\Taxonomies\Categories as Idea_Categories;
use Modern_Tribe\Idea_Garden\Taxonomies\Tags as Idea_Tags;
use Modern_Tribe\Idea_Garden\Commands\Main as Commands;
use stdClass;

class Main {
	private $plugin_dir = '';
	private $plugin_url = '';

	private $commands;
	private $idea_categories;
	private $idea_list;
	private $idea_tags;
	private $ideas;
	private $submission_form;
	private $voting;

	public function __construct( string $plugin_dir, string $plugin_url ) {
		$this->plugin_dir = $plugin_dir;
		$this->plugin_url = $plugin_url;

		$this->commands();
		$this->ideas();
		$this->submission_form();
		$this->idea_categories();
		$this->idea_list();
		$this->idea_tags();
		$this->voting();

		add_action( 'wp_ajax_update_idea_garden_public_list', [ $this, 'public_list_updates' ] );
		add_action( 'wp_ajax_nopriv_update_idea_garden_public_list', [ $this, 'public_list_updates' ] );
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
			'view' => 'idea_list',
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
			return false;
		}

		return call_user_func( [ $this, $handler ], $params );
	}

	public function do_idea_list( stdClass $params ) {

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

	public function idea_categories(): Idea_Categories {
		return empty( $this->idea_categories ) ? $this->idea_categories = new Idea_Categories : $this->idea_categories;
	}

	public function idea_list(): Idea_List {
		if ( empty( $this->idea_list ) ) {
			$this->idea_list = new Idea_List;
			$this->idea_list->setup();
		}

		return $this->idea_list;
	}

	public function idea_tags(): Idea_Tags {
		return empty( $this->idea_tags ) ? $this->idea_tags = new Idea_Tags : $this->idea_tags;
	}

	public function ideas(): Ideas {
		if ( empty( $this->ideas ) ) {
			$this->ideas = new Ideas;
			$this->ideas->setup();
		}

		return $this->ideas;
	}

	public function submission_form(): Submission_Form {
		if ( empty( $this->submission_form ) ) {
			$this->submission_form = new Submission_Form;
			$this->submission_form->setup();
		}

		return $this->submission_form;
	}

	public function voting(): Voting {
		return empty( $this->voting ) ? $this->voting = new Voting : $this->voting;
	}
}