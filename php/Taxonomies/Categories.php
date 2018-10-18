<?php
namespace Modern_Tribe\Idea_Garden\Taxonomies;

class Categories extends Abstract_Taxonomy {
	const TAXONOMY = 'idea_garden_categories';

	public function get_args() {
		$args = [
			'hierarchical' => true,
			'labels'       => [
				'name'              => _x( 'Categories', 'idea category taxonomy', 'idea-garden' ),
				'singular_name'     => _x( 'Category', 'idea category taxonomy', 'idea-garden' ),
				'search_items'      => _x( 'Search Categories', 'idea category taxonomy', 'idea-garden' ),
				'all_items'         => _x( 'All Categories', 'idea category taxonomy', 'idea-garden' ),
				'edit_item'         => _x( 'Edit Item', 'idea category taxonomy', 'idea-garden' ),
				'update_item'       => _x( 'Update Item', 'idea category taxonomy', 'idea-garden' ),
				'add_new_item'      => _x( 'Add New Item', 'idea category taxonomy', 'idea-garden' ),
				'new_item_name'     => _x( 'Product Name', 'idea category taxonomy', 'idea-garden' ),
				'menu_name'         => _x( 'Idea Categories', 'idea category taxonomy', 'idea-garden' ),
				'parent_item'       => _x( 'Parent Category', 'idea category taxonomy', 'idea-garden' ),
				'parent_item_colon' => _x( 'Parent Category:', 'idea category taxonomy', 'idea-garden' ),
			],
			'query_var'    => true,
			'rewrite'      => [ 'slug' => self::TAXONOMY ],
			'show_admin'   => true,
			'show_in_rest' => true,
			'show_ui'      => true,
		];

		return $args;
	}

	public function is_valid_category_id( $category_id ): bool {
		return $this->is_valid_term_id( $category_id );
	}
}