<?php
namespace Modern_Tribe\Idea_Garden;

use Modern_Tribe\Idea_Garden\Exceptions\Invalid_Idea_Exception;
use WP_Post;
use WP_Term;

class Idea {
	/** @var WP_Post */
	private $post;

	/** @var Votes */
	private $votes;

	/**
	 * Represents an idea.
	 *
	 * @param int $post_id
	 *
	 * @throws Invalid_Idea_Exception
	 */
	public function __construct( int $post_id ) {
		$this->post = get_post( $post_id );

		if ( $this->post->post_type !== Ideas::POST_TYPE ) {
			throw new Invalid_Idea_Exception();
		}
	}

	/**
	 * Returns the idea's post ID.
	 *
	 * @return int
	 */
	public function id(): int {
		return $this->post->ID;
	}

	/**
	 * Gets or sets the idea title.
	 *
	 * @param string|null $title
	 *
	 * @return string|null
	 */
	public function title( string $title = null ) {
		return $this->post_field( 'post_title', $title );
	}

	/**
	 * Gets or sets the idea description.
	 *
	 * @param string|null $description
	 *
	 * @return string|null
	 */
	public function description( string $description = null ) {
		return $this->post_field( 'post_content', $description );
	}

	/**
	 * Gets or sets the idea excerpt.
	 *
	 * @param string|null $excerpt
	 *
	 * @return string|null
	 */
	public function excerpt( string $excerpt = null ) {
		return $this->post_field( 'post_excerpt', $excerpt );
	}

	/**
	 * Gets or sets the idea author.
	 *
	 * @param int|null $user_id
	 *
	 * @return int|null
	 */
	public function author( int $user_id = null ) {
		return $this->post_field( 'post_author', $user_id );
	}

	/**
	 * Gets or sets the specified post field.
	 *
	 * @param string $field
	 * @param null $value
	 *
	 * @return mixed
	 */
	private function post_field( string $field, $value = null ) {
		if ( $value !== null ) {
			wp_update_post( [
				'ID' => $this->post->ID,
				$field => $value
			] );
		}

		return get_post_field( $field, $this->post->ID );
	}

	/**
	 * @return Votes
	 */
	public function votes(): Votes {
		if ( empty( $this->votes ) ) {
			$this->votes = new Votes( $this->post->ID );
		}

		return $this->votes;
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