<?php
namespace Modern_Tribe\Idea_Garden\Ninja_Fu;

class Public_List {
	private $form_id = 0;
	private $vars = [];

	public function __construct( $form_id = 0 ) {
		$this->form_id = $form_id;
	}

	public function __toString() {
		$this->prepare();
		return View::render( 'public-list', $this->vars );
	}

	private function prepare() {
		$form = ninja_forms()->form( $this->form_id );
		return;
	}
}