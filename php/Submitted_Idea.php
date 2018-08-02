<?php
namespace Modern_Tribe\Idea_Garden\Ninja_Fu;

use NF_Database_Models_Submission;
use WP_User;

/**
 * @property-read WP_User $author
 * @property-read int $id
 * @property-read NF_Database_Models_Submission $submission
 *
 */
class Submitted_Idea {
	/** @var NF_Database_Models_Submission */
	private $submission_object;

	/** @var int */
	private $id;

	/** @var array */
	private $public_fields = [];

	/** @var array */
	private $field_refs = [];

	public function __construct( NF_Database_Models_Submission $submission_object ) {
		$this->submission_object = $submission_object;
		$this->id                = (int) $submission_object->get_id();
		$this->field_refs        = Ninja_Forms::get_form_fields_array( $this->submission_object );
		$this->setup_public_fields();
	}

	private function setup_public_fields() {
		$this->public_fields = [
			'author'     => $this->submission_object->get_user(),
			'id'         => $this->id,
			'submission' => $this->submission_object,
		];
	}

	public function __get( $key ) {
		if ( isset( $this->public_fields[ $key ] ) ) {
			return $this->public_fields[ $key ];
		}

		if ( ! empty( $this->field_refs[ $key ] ) ) {
			return $this->submission_object->get_field_value( $this->field_refs[ $key ] );
		}

		return null;
	}
}