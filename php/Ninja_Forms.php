<?php
namespace Modern_Tribe\Idea_Garden;

use NF_Database_Models_Field;
use NF_Database_Models_Form;
use NF_Abstracts_ModelFactory;
use NF_Database_Models_Submission;

class Ninja_Forms {
	private static $form_field_arrays = [];

	/**
	 * Returns a map of field slugs (the field label, slugified and using underscores) mapped to
	 * the field reference (which is the string used internally by Ninja Forms to reference field
	 * data, etc).
	 *
	 * @param int|NF_Database_Models_Submission $identifier
	 *
	 * @return array
	 */
	public static function get_form_fields_array( $identifier ) {
		// Support discovery of the form ID from a form submission
		if ( is_object( $identifier ) && is_a( $identifier, 'NF_Database_Models_Submission' ) ) {
			$form_id = $identifier->get_form_id();
		}
		// Default to treating the identifier as the form ID
		else {
			$form_id = (int) $identifier;
		}

		if ( ! empty( self::$form_field_arrays[ $form_id ] ) ) {
			return self::$form_field_arrays[ $form_id ];
		}

		$field_map = [];

		foreach ( ninja_forms()->form( $form_id )->get_fields() as $field ) {
			$label = $field->get_setting( 'label' );
			$slugged_label = preg_replace( '/[^a-z0-9]/', '_', sanitize_title( strtolower( $label ) ) );
			$field_map[ $slugged_label ] = $field->get_id();
		}

		self::$form_field_arrays[ $form_id ] = $field_map;
		return $field_map;
	}

	/**
	 * @param NF_Abstracts_ModelFactory $form
	 * @param $field_identifier
	 *
	 * @return NF_Database_Models_Field|bool
	 */
	public static function get_field_object( NF_Abstracts_ModelFactory $form, $field_identifier ) {
		$fields = self::get_form_fields_array( $form->get()->get_id() );

		if ( ! isset( $fields[ $field_identifier ] ) ) {
			return false;
		}

		return $form->get_field( $fields[ $field_identifier ] );
	}

	/**
	 * Returns all possible field options.
	 *
	 * The resulting array (which may be empty if this is not a suitable field type) will
	 * take the form of [ label => value, ... ].
	 *
	 * @param NF_Database_Models_Field $field
	 *
	 * @return array
	 */
	public static function get_field_options( NF_Database_Models_Field $field ) {
		$options = [];

		$field_settings = $field->get_settings();

		if ( empty( $field_settings['options'] ) ) {
			return $options;
		}

		foreach ( $field_settings['options'] as $possible_options ) {
			$options[ $possible_options['label'] ] = $possible_options['value'];
		}

		return $options;
	}
}