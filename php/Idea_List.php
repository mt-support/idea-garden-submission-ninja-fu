<?php
namespace Modern_Tribe\Idea_Garden;

class Idea_List {
	public function render() {
		if ( did_action( 'wp_enqueue_scripts' ) ) {
			add_action( 'wp_enqueue_scripts', [ $this, 'frontend_assets' ] );
		}
		else {
			$this->frontend_assets();
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
}