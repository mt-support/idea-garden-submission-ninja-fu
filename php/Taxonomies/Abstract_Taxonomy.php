<?php
namespace Modern_Tribe\Idea_Garden\Taxonomies;

use Modern_Tribe\Idea_Garden\Ideas;

abstract class Abstract_Taxonomy {
	protected $args;

	public function __construct() {
		$this->args = $this->get_args();
	}

	abstract public function get_args();

	public function setup() {
		add_action( 'init', [ $this, 'register' ] );
	}

	public function register() {
		register_taxonomy( static::TAXONOMY, 			Ideas::POST_TYPE, $args );
		register_taxonomy_for_object_type( static::TAXONOMY, Ideas::POST_TYPE );
	}

	public function list_all(): array {
		$terms = get_terms( [
			'hide_empty' => false,
			'taxonomy'   => static::TAXONOMY,
		] );

		return is_array( $terms ) ? $terms : [];
	}

	public function is_valid_term_id( $term_id ): bool {
		$term = get_term( $term_id, static::TAXONOMY );
		return $term && ! is_wp_error( $term );
	}
}