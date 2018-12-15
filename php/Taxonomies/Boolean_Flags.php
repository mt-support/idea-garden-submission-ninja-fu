<?php
namespace Modern_Tribe\Idea_Garden\Taxonomies;

trait Boolean_Flags {
	/**
	 * Returns the true/false value of a piece of boolean meta data for the specified term.
	 *
	 * @param int $term_id
	 * @param string $name
	 * @param bool $default
	 *
	 * @return bool
	 */
	public function get_boolean_meta( int $term_id, string $name, bool $default = false ): bool {
		$value = get_term_meta( $term_id, $name, true );

		// If the data has not been set for this term we will receive an empty string
		//
		// The explicit and strict test to check we don't have the string '0' is because
		// empty() returns true for this value
		if ( is_string( $value ) && '0' !== $value && empty( $value ) ) {
			return $default;
		}

		return (bool) $value;
	}

	/**
	 * Sets the value of a piece of boolean meta data for the specified term.
	 *
	 * @param int $term_id
	 * @param string $name
	 * @param bool $value
	 *
	 * @return bool
	 */
	public function set_boolean_meta( int $term_id, string $name, bool $value ): bool {
		$result = update_term_meta( $term_id, $name, (int) $value );
		return ( false === $result || is_wp_error( $result ) ) ? false : true;
	}
}