<?php
namespace Modern_Tribe\Idea_Garden;

class Idea_List {
	public function setup() {
		add_shortcode( 'idea_list', [ $this, 'do_idea_list' ] );
	}

	public function do_idea_list() {
		$this->prepare_assets();
		return View::render( 'public-list', [
			'ideas' => $this->fetch_ideas(),
			'selected_product' => '',
		] );
	}

	private function fetch_ideas() {
		$args = apply_filters( 'idea_garden.idea_list.query', [
			'post_type' => Ideas::POST_TYPE,
		] );

		return get_posts( $args );
	}

	private function prepare_assets() {
		if ( ! did_action( 'wp_enqueue_scripts' ) ) {
			add_action( 'wp_enqueue_scripts', [ $this, 'frontend_assets' ] );
		}
		else {
			$this->frontend_assets();
		}
	}

	public function frontend_assets() {
		if ( is_admin() ) {
			return;
		}

		wp_enqueue_style( 'idea-garden-styles', main()->url() . 'styles/css/style.min.css' );
		wp_enqueue_script( 'idea-garden-script', main()->url() . 'js/scripts.js' );
	}
}