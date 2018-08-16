<?php
namespace Modern_Tribe\Idea_Garden\Ninja_Fu;

use NF_Abstracts_ModelFactory;
use NF_Database_Models_Field;
use NF_Database_Models_Submission;
use stdClass;
use WP_Query;

class Public_List {
	private $ideas = [];
	private $field_refs = [];
	private $form_id = 0;
	private $max_pages = 0;
	private $page = 1;
	private $vars = [];

	/** @var NF_Abstracts_ModelFactory */
	private $form;

	/** @var WP_Query */
	private $query;

	public function __construct( stdClass $params ) {
		$this->form_id = (int) $params->form;
	}

	public function __toString() {
		$this->prepare();
		$this->vars['ideas'] = $this->ideas;
		$this->vars['helper'] = $this;
		return View::render( 'public-list', $this->vars );
	}

	private function prepare() {
		$this->form = ninja_forms()->form( $this->form_id );
		$this->load_submissions();
	}

	/**
	 * Load submissions (ideas) for the current form, applying pagination
	 * and other filters as requested.
	 */
	private function load_submissions() {
		add_action( 'pre_get_posts', [ $this, 'idea_query_constraints' ] );
		add_filter( 'posts_orderby', [ $this, 'idea_query_ordering' ] );

		$ninja_submissions = Ninja_Forms()->form( $this->form_id )->get_subs();

		remove_action( 'pre_get_posts', [ $this, 'idea_query_constraints' ] );
		remove_filter( 'posts_orderby', [ $this, 'idea_query_ordering' ] );

		/** @var NF_Database_Models_Submission $submission_object */
		foreach ( $ninja_submissions as $submission_object ) {
			$this->ideas[] = new Submitted_Idea( $submission_object );
		}

		$this->page = (int) $this->query->get( 'paged' );
		$this->max_pages = $this->query->max_num_pages;
	}

	/**
	 * Capture key query data and apply filtering/pagination.
	 *
	 * @param WP_Query $query
	 */
	public function idea_query_constraints( WP_Query $query ) {
		if ( 'nf_sub' !== $query->get( 'post_type' ) ) {
			return;
		}

		// Keep a reference to the query
		$this->query = $query;

		// Remove unwanted constraints
		$query->set( 'suppress_filters', false );
		$query->set( 'no_found_rows', false );

		// Filtering
		$query->set( 'post_status', $this->idea_statuses_to_retrieve() );
		$query->set( 'paged', $this->get_requested_page() );
		$query->set( 'posts_per_page', $this->number_of_idea_statuses_to_retrieve() );
	}

	/**
	 * Modify the query ordering (we want to prioritize by status first and foremost,
	 * etc).
	 *
	 * @param string $order_sql
	 *
	 * @return string
	 */
	public function idea_query_ordering( string $order_sql ): string {
		$ordering = 'CASE ';
		$counter  = 1;

		foreach ( $this->idea_statuses_to_retrieve() as $status ) {
			$counter++;
			$status = esc_sql( $status );

			$ordering .= "WHEN post_status = '$status' THEN $counter ";
		}

		return " $ordering END DESC, $order_sql ";
	}

	/**
	 * Return the max number of ideas/submissions we want per page.
	 *
	 * @return int
	 */
	private function number_of_idea_statuses_to_retrieve(): int {
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

		return $ideas_per_page;
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

		return $page_number;
	}

	/**
	 * Return an array of post status slugs we are interested in.
	 *
	 * @return array
	 */
	private function idea_statuses_to_retrieve(): array {
		// Has the user requested we look at specific statuses?
		$statuses = ! empty( $_REQUEST[ 'ig-idea-statuses'] )
			? (array) $_REQUEST[ 'ig-idea-statuses']
			: [];

		// If not, default to the following...
		if ( empty( $statuses ) ) {
			$statuses = main()->idea_statuses()->default_statuses();
		}

		// Make sure our statuses are legit
		return main()->idea_statuses()->filter_statuses( $statuses );
	}

	public function get_current_page(): int {
		return $this->page;
	}

	public function get_max_pages(): int {
		return $this->max_pages;
	}
}