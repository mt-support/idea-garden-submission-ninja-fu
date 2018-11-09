<?php
namespace Modern_Tribe\Idea_Garden;

use Modern_Tribe\Idea_Garden\Taxonomies\Categories as Idea_Categories;
use Modern_Tribe\Idea_Garden\Taxonomies\Tags as Idea_Tags;

class Ideas {
	const POST_TYPE = 'idea_garden_idea';

	private $categories;
	private $statuses;

	public function setup() {
		$this->categories();
		$this->statuses();
		$this->tags();

		add_action( 'init', [ $this, 'register_post_type' ] );
	}

	public function register_post_type() {
		register_post_type( self::POST_TYPE, [
			'description'        => __( 'An individual idea, suggestion or request.', 'idea-garden' ),
			'label'              => _x( 'Idea', 'idea post type label', 'idea-garden' ),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => [ 'slug' => 'idea' ],
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'show_in_rest'       => true,
			'supports'           => [
				'title',
				'editor',
				'author',
				'thumbnail',
				'excerpt',
				'comments'
			],
		] );
	}

	public function categories(): Idea_Categories {
		if ( empty( $this->categories ) ) {
			$this->categories = new Idea_Categories;
			$this->categories->setup();
		}

		return $this->categories;
	}

	public function statuses(): Idea_Statuses {
		if ( empty( $this->statuses ) ) {
			$this->statuses = new Idea_Statuses;
			$this->statuses->setup();
		}

		return $this->statuses;
	}

	public function tags(): Idea_Tags {
		if ( empty( $this->tags ) ) {
			$this->tags = new Idea_Tags;
			$this->tags->setup();
		}

		return $this->tags;
	}
}