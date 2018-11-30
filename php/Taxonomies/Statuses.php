<?php
namespace Modern_Tribe\Idea_Garden\Taxonomies;

class Statuses extends Abstract_Taxonomy {
	const TAXONOMY = 'idea_garden_statuses';

	public function setup() {
		parent::setup();
		add_action( self::TAXONOMY . '_add_form_fields', [ $this, 'new_status_form' ] );
		add_action( self::TAXONOMY . '_edit_form_fields', [ $this, 'edit_status_form' ] );
	}

	public function get_args() {
		return [
			'hierarchical' => true,
			'labels'       => [
				'name'              => _x( 'Statuses', 'idea status taxonomy', 'idea-garden' ),
				'singular_name'     => _x( 'Status', 'idea status taxonomy', 'idea-garden' ),
				'search_items'      => _x( 'Search Statuses', 'idea status taxonomy', 'idea-garden' ),
				'all_items'         => _x( 'All Statuses', 'idea status taxonomy', 'idea-garden' ),
				'edit_item'         => _x( 'Edit Item', 'idea status taxonomy', 'idea-garden' ),
				'update_item'       => _x( 'Update Item', 'idea status taxonomy', 'idea-garden' ),
				'add_new_item'      => _x( 'Add New Item', 'idea status taxonomy', 'idea-garden' ),
				'new_item_name'     => _x( 'Product Name', 'idea status taxonomy', 'idea-garden' ),
				'menu_name'         => _x( 'Idea Statuses', 'idea status taxonomy', 'idea-garden' ),
				'parent_item'       => _x( 'Parent Status', 'idea status taxonomy', 'idea-garden' ),
				'parent_item_colon' => _x( 'Parent Status:', 'idea status taxonomy', 'idea-garden' ),
			],
			'query_var'    => true,
			'rewrite'      => [ 'slug' => self::TAXONOMY ],
			'show_admin'   => true,
			'show_in_rest' => true,
			'show_ui'      => true,
		];
	}

	public function is_valid_Status_id( $Status_id ): bool {
		return $this->is_valid_term_id( $Status_id );
	}

	public function new_status_form() {
		$label = __( 'Visibility', 'idea-garden' );
		$public = _x( 'Public', 'status visibility', 'idea-garden' );
		$internal = _x( 'Internal', 'status visibility', 'idea-garden' );
		$explanation = __( 'If set to internal, the status will not be exposed to front-end users.', 'idea-garden' );

		print "
			<div class='form-field internal-flag-wrap'>
				<label for='internal'> $label </label>
				<select name='internal'>
					<option value='public'> $public </option>
					<option value='internal'> $internal </option>				
				</select>
				<p> $explanation </p>
			</div>
		";
	}

	public function edit_status_form() {
		$label = __( 'Visibility', 'idea-garden' );
		$public = _x( 'Public', 'status visibility', 'idea-garden' );
		$internal = _x( 'Internal', 'status visibility', 'idea-garden' );
		$explanation = __( 'If set to internal, the status will not be exposed to front-end users.', 'idea-garden' );

		print "
			<tr class='form-field internal-flag-wrap'>
				<th scope='row'>
					<label for='internal'> $label </label>
				</th>
				<td>
					<select name='internal'>
						<option value='public'> $public </option>
						<option value='internal'> $internal </option>				
					</select>
					<p class='description'> $explanation </p>
				</td>
			</tr>
		";
	}
}