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
		return View::render( 'submission-form', [
			'categories'  => main()->ideas()->categories()->list_all(),
			'description' => '',
			'title'       => '',
		] );
	}

}