<?php
namespace Modern_Tribe\Idea_Garden\Ninja_Fu;

use NF_Abstracts_ModelFactory;
use NF_Database_Models_Field;
use NF_Database_Models_Submission;
use stdClass;
use WP_Query;

class Public_List {
	/** @var int */
	private $form_id = 0;

	/** @var NF_Abstracts_ModelFactory */
	private $form;

	/** @var array */
	private $field_refs = [];

	private $ideas = [];
	private $vars = [];

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


	private function load_submissions() {
		add_action( 'pre_get_posts', [ $this, 'idea_query_constraints' ] );
		add_filter( 'posts_orderby', [ $this, 'idea_query_ordering' ] );

		$ninja_submissions = Ninja_Forms()->form( $this->form_id )->get_subs();

		remove_action( 'pre_get_posts', [ $this, 'idea_query_constraints' ] );
		remove_filter( 'posts_orderby', [ $this, 'idea_query_ordering' ] );

		/** @var NF_Database_Models_Submission $submission_object */
		foreach ( $ninja_submissions as $submission_object ) {
			$this->ideas[] = new Submitted_Idea( $submission_object, $this->field_refs );
		}
	}

	public function idea_query_constraints( WP_Query $query ) {
		if ( 'nf_sub' !== $query->get( 'post_type' ) ) {
			return;
		}

		// Make sure our statuses are legit
		$statuses = $this->idea_statuses_to_retrieve();

		$query->set( 'post_status', $statuses );
		$query->set( 'suppress_filters', false );
	}

	public function idea_query_ordering( string $order_sql ): string {
		$ordering = 'CASE ';
		$counter  = 1;

		foreach ( $this->idea_statuses_to_retrieve() as $status ) {
			$counter++;
			$status = esc_sql( $status );

			$ordering .= "WHEN post_status = '$status' THEN $counter ";
		}

		return " $ordering END ASC, $order_sql ";
	}

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
}