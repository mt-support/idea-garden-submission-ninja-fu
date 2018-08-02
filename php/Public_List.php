<?php
namespace Modern_Tribe\Idea_Garden\Ninja_Fu;

use NF_Abstracts_ModelFactory;
use NF_Database_Models_Field;
use NF_Database_Models_Submission;
use stdClass;

class Public_List {
	/** @var int */
	private $form_id = 0;

	/** @var NF_Abstracts_ModelFactory */
	private $form;

	/** @var array */
	private $field_refs = [];

	private $ideas = [];
	private $vars = [];

	public function __construct( stdClass $params ) {
		$this->form_id = (int) $params->form;
	}

	public function __toString() {
		$this->prepare();
		$this->vars['ideas'] = $this->ideas;
		$this->vars['helper'] = $this;
		return View::render( 'public-list', $this->vars );
	}

	private function prepare() {
		$this->form = ninja_forms()->form( $this->form_id );
		$this->load_submissions();
	}


	private function load_submissions() {
		$ninja_submissions = Ninja_Forms()->form( $this->form_id )->get_subs();

		/** @var NF_Database_Models_Submission $submission_object */
		foreach ( $ninja_submissions as $submission_object ) {
			$this->ideas[] = new Submitted_Idea( $submission_object, $this->field_refs );
		}
	}
}