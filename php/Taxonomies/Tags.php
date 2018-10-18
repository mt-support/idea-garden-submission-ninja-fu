<?php
namespace Modern_Tribe\Idea_Garden\Taxonomies;

class Tags extends Abstract_Taxonomy {
	const TAXONOMY = 'idea_garden_tags';

	public function get_args() {
		$args = [
			'hierarchical' => true,
			'labels'       => [
				'name'              => _x( 'Tags', 'idea tag taxonomy', 'idea-garden' ),
				'singular_name'     => _x( 'Tag', 'idea tag taxonomy', 'idea-garden' ),
				'search_items'      => _x( 'Search Tags', 'idea tag taxonomy', 'idea-garden' ),
				'all_items'         => _x( 'All Tags', 'idea tag taxonomy', 'idea-garden' ),
				'edit_item'         => _x( 'Edit Item', 'idea tag taxonomy', 'idea-garden' ),
				'update_item'       => _x( 'Update Item', 'idea tag taxonomy', 'idea-garden' ),
				'add_new_item'      => _x( 'Add New Item', 'idea tag taxonomy', 'idea-garden' ),
				'new_item_name'     => _x( 'Product Name', 'idea tag taxonomy', 'idea-garden' ),
				'menu_name'         => _x( 'Idea Tags', 'idea tag taxonomy', 'idea-garden' ),
				'parent_item'       => _x( 'Parent Tag', 'idea tag taxonomy', 'idea-garden' ),
				'parent_item_colon' => _x( 'Parent Tag:', 'idea tag taxonomy', 'idea-garden' ),
			],
			'query_var'    => true,
			'rewrite'      => [ 'slug' => self::TAXONOMY ],
			'show_admin'   => true,
			'show_in_rest' => true,
			'show_ui'      => true,
		];

		return $args;
	}

	public function is_valid_tag_id( $tag_id ): bool {
		return $this->is_valid_term_id( $tag_id );
	}
}