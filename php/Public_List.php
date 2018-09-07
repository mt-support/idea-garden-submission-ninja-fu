<?php
namespace Modern_Tribe\Idea_Garden;

use NF_Abstracts_ModelFactory;
use NF_Database_Models_Submission;
use stdClass;
use WP_Query;

/**
 * Generates a filterable list of ideas for public consumption.
 */
class Public_List {
	private $ideas = [];
	private $form_id = 0;
	private $list_only = false;
	private $max_pages = 0;
	private $page = 1;
	private $request_vars = [];
	private $vars = [];

	/** @var NF_Abstracts_ModelFactory */
	private $form;

	/** @var WP_Query */
	private $query;

	public function __construct( stdClass $params ) {
		$this->form_id = (int) $params->form;
		$this->list_only = (bool) @$params->list_only;
		$this->request_vars();
	}

	private function request_vars() {
		$key_map = [

		];
	}

	public function __toString() {
		$this->prepare();
		$this->vars['form_id']   = $this->form_id;
		$this->vars['ideas']     = $this->ideas;
		$this->vars['list_only'] = $this->list_only;
		$this->vars['helper']    = $this;
		return View::render( 'public-list', $this->vars );
	}

	private function prepare() {
		$this->form = ninja_forms()->form( $this->form_id );
		$this->load_submissions();
		$this->assets();
	}

	private function assets() {
		wp_enqueue_script( 'idea-garden-filter-bar', main()->url() . 'js/filter-bar.js', [ 'jquery' ], false, true );
		wp_localize_script( 'idea-garden-filter-bar', 'ideaGardenFilterBar', [
			'formId' => $this->form_id,
			'url'     => get_admin_url( false, 'admin-ajax.php' ),
		] );
	}

	/**
	 * Load submissions (ideas) for the current form, applying pagination
	 * and other filters as requested.
	 */
	private function load_submissions() {
		add_filter( 'posts_orderby', [ $this, 'order_by_idea_status' ], 10, 2 );

		$this->page = $this->get_requested_page();

		$this->query = new WP_Query( [
			'post_type'      => 'nf_sub',
			'post_status'    => $this->idea_statuses_to_retrieve(),
			'paged'          => $this->page,
			'posts_per_page' => $this->number_of_ideas_to_retrieve(),
			'meta_query'     => $this->meta_query(),
			'orderby'        => $this->orderby_sequence(),
		] );

		$this->max_pages = (int) $this->query->max_num_pages;
		remove_filter( 'posts_orderby', [ $this, 'order_by_idea_status' ] );

		/** @var NF_Database_Models_Submission $submission_object */
		foreach ( $this->query->posts as $submission_post ) {
			$this->ideas[] = new Submitted_Idea( $submission_post->ID, $this->form_id );
		}
	}

	/**
	 * Modify the query order clause to include ordering by post status.
	 *
	 * @param string   $order_sql
	 * @param WP_Query $query
	 *
	 * @return string
	 */
	public function order_by_idea_status( string $order_sql, WP_Query $query ): string {
		// Figure out how high (or low) 'status' ranks in the query's orderby property
		$orderby = (array) $query->get( 'orderby' );
		$status_priority = array_search( 'status', array_keys( $orderby ) );
		$direction = 'DESC';

		// If not explictly mentioned in $orderby, it should be the lowest priority
		if ( false === $status_priority ) {
			$status_priority = count( $orderby );
		}
		// Or, if it *was* described in the orderby property, let's also grab the direction
		else {
			$direction = $orderby['status'];
		}

		// Build the CASE clause required to implement status-based ordering
		$ordering = 'CASE ';
		$counter  = 1;

		foreach ( $this->idea_statuses_to_retrieve() as $status ) {
			$counter++;
			$status = esc_sql( $status );

			$ordering .= "WHEN post_status = '$status' THEN $counter ";
		}

		$ordering = " $ordering END $direction ";

		// Insert the status-based ordering clause into the rest of the $order_sql,
		// respecting the calculated priority level
		$order_sql_parts = array_filter( explode( ',', $order_sql ) );
		array_splice( $order_sql_parts, $status_priority, 0, $ordering );

		// Glue it back together and return
		$order_sql = implode( ' , ', $order_sql_parts );

		/**
		 * @param string   $order_sql
		 * @param string   $original_order_sql
		 * @param WP_Query $query
		 */
		return apply_filters( 'idea_garden.public_list.order_sql', " $order_sql ", $order_sql, $query );
	}

	private function meta_query(): array {
		$meta_query = [
			'form_selection' => [
				'key'   => '_form_id',
				'value' => $this->form_id,
			],
			'ig_supporter_count' => [
				'key'  => Votes::SUPPORTERS_COUNT_KEY,
				'type' => 'UNSIGNED',
			],
		];

		// Get the ID of the products field
		$products_field = Ninja_Forms::get_field_object( $this->form, 'products' );

		$products_field_id = ! empty( $products_field ) && is_object( $products_field )
			? (int) $products_field->get_id()
			: 0;

		// If the product filter is applied, let's narrow down by product
		if ( ! empty( $_REQUEST['ig-product'] ) && $products_field_id ) {
			$meta_query['product_selection'] = [
				'key'   => '_field_' . $products_field_id,
				'value' => filter_var( $_REQUEST['ig-product'], FILTER_SANITIZE_STRIPPED ),
			];
		}

		return $meta_query;
	}

	/**
	 * Return the max number of ideas/submissions we want per page.
	 *
	 * @return int
	 */
	private function number_of_ideas_to_retrieve(): int {
		$default = 15;

		// Has the user specified a number of ideas per page they want to see?
		$ideas_per_page = ! empty( $_REQUEST[ 'ig-ideas-per-page'] )
			? filter_var( $_REQUEST[ 'ig-ideas-per-page'], FILTER_SANITIZE_NUMBER_INT )
			: $default;

		$ideas_per_page = (int) $ideas_per_page;

		// Requests for zero or unlimited ideas per page aren't acceptable - use the default
		if ( $ideas_per_page <= 0 ) {
			$ideas_per_page = $default;
		}

		// More than 100 ideas per page isn't acceptable - cap at 100
		if ( $ideas_per_page > 100 ) {
			$ideas_per_page = 100;
		}

		return apply_filters( 'idea_garden.public_list.ideas_per_page', $ideas_per_page );
	}

	private function orderby_sequence() {
		$default_sequence = [
			'status' => 'DESC',
			'count'  => 'DESC',
			'date'   => 'DESC',
		];

		$query_keys = [
			'status' => 'status',
			'count'  => 'ig_supporter_count',
			'date'   => 'post_date',
		];

		// Use the requested order sequence if available
		$sequence = ! empty( $_REQUEST['ig-order-sequence'] )
			? (array) $_REQUEST['ig-order-sequence']
			: $default_sequence;

		// Remove any invalid strings; fall back on the $default_sequence
		$sequence = array_intersect_key( $sequence, $default_sequence );
		$sequence = empty( $sequence ) ? $default_sequence : $sequence;

		// Ensure we have a valid direction (ASC|DESC) for each (else use the default)
		foreach ( $sequence as $key => $direction ) {
			if ( $direction !== 'DESC' && $direction !== 'ASC' ) {
				$sequence[ $key ] = $default_sequence[ $key ];
			}
		}

		// Last but not least, rebuild the array with the correct keys for usage in our query
		// Example - [ 'count' => 'ASC' ] becomes [ 'ig_supporter_count' => 'ASC' ]
		foreach ( $sequence as $key => $direction ) {
			// Unset each element (by doing this and recreating each element, we preserve
			// the order of the array)
			unset( $sequence[ $key ] );

			// Use replacement key if required
			if ( $query_keys[ $key ] !== $key ) {
				$sequence[ $query_keys[ $key ] ] = $direction;
			}
			// Or maintain the previous key
			else {
				$sequence[ $key ] = $direction;
			}
		}

		return $sequence;
	}

	/**
	 * Return the requested page.
	 *
	 * @return int
	 */
	private function get_requested_page(): int {
		$page_number = ! empty( $_REQUEST[ 'ig-page'] )
			? filter_var( $_REQUEST[ 'ig-page'], FILTER_SANITIZE_NUMBER_INT )
			: 1;

		if ( $page_number < 1 ) {
			$page_number = 1;
		}

		return apply_filters( 'idea_garden.public_list.page_number', $page_number );
	}

	/**
	 * Return an array of post status slugs we are interested in.
	 *
	 * @return array
	 */
	private function idea_statuses_to_retrieve(): array {
		// Has the user requested we look at specific statuses?
		$statuses = ! empty( $_REQUEST[ 'ig-status'] )
			? (array) $_REQUEST[ 'ig-status']
			: [];

		// If not, default to the following...
		if ( empty( $statuses ) ) {
			$statuses = main()->idea_statuses()->default_statuses();
		}

		// Make sure our statuses are legit
		return main()->idea_statuses()->filter_statuses( $statuses );
	}

	public function get_form() {
		return $this->form;
	}

	public function get_current_page(): int {
		return $this->page;
	}

	public function get_max_pages(): int {
		return $this->max_pages;
	}
}