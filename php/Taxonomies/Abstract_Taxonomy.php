<?php
namespace Modern_Tribe\Idea_Garden\Taxonomies;

use Modern_Tribe\Idea_Garden\Ideas;
use WP_Term;

abstract class Abstract_Taxonomy {
	const TAXONOMY = '';

	abstract public function get_args();

	public function setup() {
		add_action( 'init', [ $this, 'register' ] );
	}

	/**
	 * Registers the taxonomy and associates it with the Ideas post type.
	 */
	public function register() {
		register_taxonomy( static::TAXONOMY, Ideas::POST_TYPE, $this->get_args() );
		register_taxonomy_for_object_type( static::TAXONOMY, Ideas::POST_TYPE );
	}

	/**
	 * Returns a list of all term objects for this taxonomy.
	 *
	 * @return WP_Term[]
	 */
	public function list_all(): array {
		$terms = get_terms( [
			'hide_empty' => false,
			'taxonomy'   => static::TAXONOMY,
		] );

		return is_array( $terms ) ? $terms : [];
	}

	/**
	 * Tests if the provided ID is a valid term for this taxonomy.
	 *
	 * @see self::term_exists()
	 *
	 * @param $term_id
	 *
	 * @return bool
	 */
	public function is_valid_term_id( $term_id ): bool {
		$term = get_term( $term_id, static::TAXONOMY );
		return $term && ! is_wp_error( $term );
	}

	/**
	 * Tests if the provided reference (can be either an ID or a slug) represents a currently
	 * existing term within this taxonomy.
	 *
	 * @see self::is_valid_term_id() if interested only in testing a numeric term ID
	 *
	 * @param $slug_or_id
	 *
	 * @return bool
	 */
	public function term_exists( $slug_or_id ): bool {
		return null !== term_exists( $slug_or_id, static::TAXONOMY );
	}

	/**
	 * Creates a new term within the current taxonomy.
	 *
	 * Returns the term ID if successfully created else zero.
	 *
	 * @param string string $name
	 * @param string string $slug
	 * @param string string $description
	 *
	 * @return int
	 */
	public function create_term( string $name = '', string $slug = '', string $description = '' ): int {
		$slug = empty( $slug )
			? sanitize_title( $name )
			: sanitize_title( $slug );

		$result = wp_insert_term( $name, static::TAXONOMY, [
			'description' => $description,
			'slug' => $slug,
		] );

		return ! is_wp_error( $result ) && is_array( $result )
			? $result['term_id']
			: 0;
	}


}