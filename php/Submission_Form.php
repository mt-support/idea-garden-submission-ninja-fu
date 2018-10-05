<?php
namespace Modern_Tribe\Idea_Garden;

class Submission_Form {
	public function setup() {
		add_action( 'init', [ $this, 'listen' ] );
		add_shortcode( 'idea_garden_submission_form', [ $this, 'render' ] );
	}

	public function listen() {

	}

	public function render() {
		Helpers\Assets::add_script(
			'idea-garden-frontend-logic',
			main()->url() . 'js/scripts.js',
			[ 'jquery' ]
		);

		Helpers\Assets::add_data(
			'idea-garden-frontend-logic',
			'ideaGardenSubmissions', [
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'safety'  => wp_create_nonce( 'submit-idea' ),
			]
		);

		return View::render( 'submission-form', [
			'categories'  => main()->ideas()->categories()->list_all(),
			'description' => '',
			'title'       => '',
		] );
	}

}