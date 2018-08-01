<?php
namespace Modern_Tribe\Idea_Garden\Ninja_Fu;

use NF_Database_Models_Submission;

class Form_Submission {
	/** @var NF_Database_Models_Submission */
	private $submission_object;

	/** @var int */
	private $id;

	/** @var array */
	private $public_fields = [];

	/** @var array */
	private $field_refs = [];

	public function __construct( NF_Database_Models_Submission $submission_object, array $field_refs ) {
		$this->submission_object = $submission_object;
		$this->id                = $submission_object->get_id();
		$this->register_field_refs( $field_refs );
		$this->setup_public_fields();
	}
	
	private function register_field_refs( array $field_refs ) {
		foreach ( $field_refs as $label => $reference ) {
			$slug = preg_replace( '/[^a-z0-9]/', '_', sanitize_title( strtolower( $label ) ) );
			$this->field_refs[ $slug ] = $reference;
		}
	}

	private function setup_public_fields() {
		$this->public_fields = [
			'author'     => $this->submission_object->get_user(),
			'id'         => $this->id,
			'submission' => $this->submission_object,
		];
	}

	public function __get( $key ) {
		if ( in_array( $key, $this->public_fields ) ) {
			return $this->public_fields[ $key ];
		}

		if ( ! empty( $this->field_refs[ $key ] ) ) {
			return $this->submission_object->get_field_value( $this->field_refs[ $key ] );
		}

		return null;
	}
}