<?php
namespace Modern_Tribe\Idea_Garden\Ninja_Fu;

use NF_Database_Models_Submission;

class Ninja_Forms {
	private static $form_field_arrays = [];

	/**
	 * Returns a map of field slugs (the field label, slugified and using underscores) mapped to
	 * the field reference (which is the string used internally by Ninja Forms to reference field
	 * data, etc).
	 *
	 * @param NF_Database_Models_Submission $form_submission
	 *
	 * @return array
	 */
	public static function get_form_fields_array( NF_Database_Models_Submission $form_submission ) {
		$form_id = $form_submission->get_form_id();

		if ( ! empty( self::$form_field_arrays[ $form_id ] ) ) {
			return self::$form_field_arrays[ $form_id ];
		}

		$field_map = [];

		foreach ( ninja_forms()->form( $form_id )->get_fields() as $field ) {
			$label = $field->get_setting( 'label' );
			$slugged_label = preg_replace( '/[^a-z0-9]/', '_', sanitize_title( strtolower( $label ) ) );
			$field_map[ $slugged_label ] = $field->get_setting( 'key' );
		}

		self::$form_field_arrays[ $form_id ] = $field_map;
		return $field_map;
	}
}