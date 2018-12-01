<?php
namespace Modern_Tribe\Idea_Garden;

use Modern_Tribe\Idea_Garden\Exceptions\Invalid_Idea_Exception;
use WP_Post;
use WP_Term;

class Idea {
	/** @var WP_Post */
	private $post;

	/**
	 * Encapsulates an existing idea or can be used to create a new idea.
	 *
	 * @param int|null $post_id
	 *
	 * @throws Invalid_Idea_Exception
	 */
	public function __construct( int $post_id = null ) {
		if ( is_int( $post_id ) ) {
			$this->load( $post_id );
		}
	}

	/**
	 * @param int $post_id
	 *
	 * @throws Invalid_Idea_Exception
	 */
	private function load( int $post_id ) {
		$this->post = get_post( $post_id );

		if ( $this->post->post_type !== Ideas::POST_TYPE ) {
			throw new Invalid_Idea_Exception();
		}
	}

	/**
	 * Gets or sets the idea status or statuses.
	 *
	 * @param int|string|array|null $status
	 *
	 * @return WP_Term[]
	 */
	public function status( $status = null ) {
		return $this->taxonomy_terms( $status, Taxonomies\Statuses::TAXONOMY );
	}

	/**
	 * Gets or sets the idea status or statuses.
	 *
	 * @param int|string|array|null $categories
	 *
	 * @return WP_Term[]
	 */
	public function categories( $categories = null ) {
		return $this->taxonomy_terms( $categories, Taxonomies\Categories::TAXONOMY );
	}

	/**
	 * Gets or sets the idea status or statuses.
	 *
	 * @param int|string|array|null $tags
	 *
	 * @return WP_Term[]
	 */
	public function tags( $tags = null ) {
		return $this->taxonomy_terms( $tags, Taxonomies\Tags::TAXONOMY );
	}

	/**
	 * Gets or sets the taxonomy terms for the current idea post.
	 *
	 * @param int|string|array|null $terms
	 * @param string $taxonomy
	 *
	 * @return WP_Term[]
	 */
	private function taxonomy_terms( $terms, string $taxonomy ) {
		if ( $terms !== null ) {
			wp_set_post_terms( $this->post->ID, $terms, $taxonomy );
		}

		$terms = wp_get_post_terms( $this->post->ID, $taxonomy );
		return is_array( $terms ) ? $terms : [];
	}
}