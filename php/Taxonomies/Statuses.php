<?php
namespace Modern_Tribe\Idea_Garden\Taxonomies;

use WP_Term;

class Statuses extends Abstract_Taxonomy {
	const TAXONOMY = 'idea_garden_statuses';
	const PUBLIC_INTERNAL_FLAG = 'idea_garden_public_status';

	public function setup() {
		parent::setup();

		add_action( self::TAXONOMY . '_add_form_fields', [ $this, 'add_status_field' ] );
		add_action( self::TAXONOMY . '_edit_form_fields', [ $this, 'add_status_field' ] );

		add_action( 'created_'. self::TAXONOMY, [ $this, 'save_status_field' ] );
		add_action( 'edited_'. self::TAXONOMY, [ $this, 'save_status_field' ] );
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

	public function add_status_field() {
		$label = __( 'Visibility', 'idea-garden' );
		$public = _x( 'Public', 'status visibility', 'idea-garden' );
		$internal = _x( 'Internal', 'status visibility', 'idea-garden' );
		$explanation = __( 'If set to internal, the status will not be exposed to front-end users.', 'idea-garden' );

		$tag_id = (int) ( $GLOBALS['tag_ID'] ?? 0 );
		$is_public_selected = selected( $this->is_public( $tag_id, false ), true, false );
		$is_internal_selected = selected( ! $is_public_selected, true, false );
		$security = wp_nonce_field( 'set_status_flag', 'status_public', false, true );

		print "
			<tr class='form-field internal-flag-wrap'>
				<th scope='row'>
					<label for='internal'> $label </label>
				</th>
				<td>
					<select name='status_public_internal'>
						<option value='public' $is_public_selected> $public </option>
						<option value='internal' $is_internal_selected> $internal </option>				
					</select>
					<p class='description'> $explanation </p>
					$security
				</td>
			</tr>
		";
	}

	/**
	 * Fires when a status term is created or updated and sets the public/internal flag.
	 *
	 * @param int $tag_id
	 */
	public function save_status_field( int $tag_id ) {
		if (
			! isset( $_POST['status_public_internal'] )
			|| ! current_user_can( get_taxonomy( self::TAXONOMY )->cap->edit_terms )
			|| ! wp_verify_nonce( $_POST['status_public'] ?? '', 'set_status_flag' )
		) {
			return;
		}

		$set_to_public = $_POST['status_public_internal'] === 'public';
		$this->set_public( $tag_id, $set_to_public );
	}

	/**
	 * Marks the specified status as 'public' or, if false is passed, as 'internal'.
	 *
	 * @param int $status_id
	 * @param bool $public
	 *
	 * @return bool
	 */
	public function set_public( int $status_id, bool $public = true ): bool {
		$update = update_term_meta( $status_id, self::PUBLIC_INTERNAL_FLAG, $public ? 'public' : 'internal' );
		return $update === false ? false : true;
	}

	/**
	 * Returns true if the specified status is 'public', or false if 'internal'.
	 *
	 * If the public flag has not explicitly been set, returns false by default (however the
	 * default can be optionally specified).
	 *
	 * @param int $status_id
	 * @param bool $default
	 *
	 * @return bool
	 */
	public function is_public( int $status_id, bool $default = false ): bool {
		$status = get_term_meta( $status_id, self::PUBLIC_INTERNAL_FLAG, true );
		return empty( $status ) ? $default : $status === 'public';
	}

	/**
	 * Returns a list of all terms marked as intended to be publicly visible.
	 *
	 * By default, only terms where the visibility flag is explicitly set to
	 * 'public' will be returned. Setting optional param $strict to false will
	 * ensure that terms where the flag has not been set at all are also
	 * included.
	 *
	 * @param bool $strict
	 *
	 * @return WP_Term[]
	 */
	public function list_public( bool $strict = true ): array {
		// Always look for terms where the visibility flag is 'public'
		$meta_query = [ [
			'key' => self::PUBLIC_INTERNAL_FLAG,
			'value' => 'public',
		] ];

		// If not strict, also include terms where the visibility flag has not been set
		if ( ! $strict ) {
			$meta_query['relation'] = 'OR';

			$meta_query[] = [
				'key' => self::PUBLIC_INTERNAL_FLAG,
				'compare' => 'NOT EXISTS',
			];
		}

		$terms = get_terms( [
			'hide_empty' => false,
			'taxonomy'   => static::TAXONOMY,
			'meta_query' => $meta_query,
		] );

		return is_array( $terms ) ? $terms : [];
	}

	/**
	 * Returns a list of all terms marked as intended to be used internally only.
	 *
	 * By default, only terms where the visibility flag is explicitly set to
	 * 'internal' will be returned. Setting optional param $strict to false will
	 * ensure that terms where the flag has not been set at all are also
	 * included.
	 *
	 * @param bool $strict
	 *
	 * @return WP_Term[]
	 */
	public function list_internal( bool $strict = true ): array {
		// Always look for terms where the visibility flag is 'public'
		$meta_query = [ [
			'key' => self::PUBLIC_INTERNAL_FLAG,
			'value' => 'internal',
		] ];

		// If not strict, also include terms where the visibility flag has not been set
		if ( ! $strict ) {
			$meta_query['relation'] = 'OR';

			$meta_query[] = [
				'key' => self::PUBLIC_INTERNAL_FLAG,
				'compare' => 'NOT EXISTS',
			];
		}

		$terms = get_terms( [
			'hide_empty' => false,
			'taxonomy'   => static::TAXONOMY,
			'meta_query' => $meta_query,
		] );

		return is_array( $terms ) ? $terms : [];
	}
}