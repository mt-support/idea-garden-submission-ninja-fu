<?php
namespace Modern_Tribe\Idea_Garden;

class Submission_Form {
	public function setup() {
		add_action( 'wp_ajax_submit_idea', [ $this, 'listen' ] );
		add_shortcode( 'idea_garden_submission_form', [ $this, 'render' ] );
	}

	public function listen() {
		if ( empty( $_POST ) || empty( $_POST['check'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['check'], 'submit-idea' ) ) {
			wp_send_json_error( [ 'message' => 'form-expired' ] );
		}

		$categories  = (array) $_POST['categories'];
		$description = filter_var( (string) $_POST['description'], FILTER_SANITIZE_STRING );
		$errors      = [];
		$title       = filter_var( (string) $_POST['title'], FILTER_SANITIZE_STRING );

		if ( ! $this->validate_categories( $categories ) ) {
			$errors['invalid-categories'] = __( 'One or more valid idea categories must be provided', 'idea-garden' );
		}

		if ( ! strlen( trim( $title ) ) ) {
			$errors['no-title'] = __( 'A title must be provided.', 'idea-garden' );
		}

		if ( ! empty( $errors ) ) {
			wp_send_json_error( $errors );
		}

		$this->save( $title, $description, $categories );
		wp_send_json_success();
	}

	private function save( $title, $description, $categories ) {
		$idea = wp_insert_post( [
			'post_author'  => wp_get_current_user()->ID,
			'post_content' => $description,
			'post_status'  => 'pending',
			'post_title'   => $title,
			'post_type'    => Ideas::POST_TYPE,
		] );

		if ( $idea ) {
			wp_set_post_terms( $idea, $categories, Idea_Categories::TAXONOMY );
		}
	}

	private function validate_categories( array $categories ) {
		$idea_categories = main()->ideas()->categories();

		foreach ( $categories as $possible_category_id ) {
			if ( ! $idea_categories->is_valid_category_id( $possible_category_id ) ) {
				return false;
			}
		}

		return true;
	}

	public function render() {
		Helpers\Assets::add_script(
			'idea-garden-frontend-logic',
			main()->url() . 'js/scripts.js',
			[ 'jquery' ]
		);

		Helpers\Assets::add_data(
			'idea-garden-frontend-logic',
			'ideaGardenSubmissions', [
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'safety'  => wp_create_nonce( 'submit-idea' ),
			]
		);

		return View::render( 'submission-form', [
			'categories'  => main()->ideas()->categories()->list_all(),
			'description' => '',
			'title'       => '',
		] );
	}

}