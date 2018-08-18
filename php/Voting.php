<?php
namespace Modern_Tribe\Idea_Garden;

class Voting {
	private $voting_assets_enqueued = false;

	public function __construct() {
		add_action( 'idea_garden.submission_voting_form', [ $this, 'voting_form' ] );
		add_action( 'wp_ajax_idea_garden.idea_vote', [ $this, 'vote_action' ] );
	}

	public function voting_form( Submitted_Idea $idea ) {
		$votes = new Votes( (int) $idea->id );

		View::render( 'voting-form', [
				'idea_id'    => $idea->id,
				'votes'      => $votes,
				'user_voted' => $votes->is_supporter( get_current_user_id() ),
			],
			true
		);

		if ( ! $this->voting_assets_enqueued ) {
			$this->enqueue_voting_assets();
		}
	}

	private function enqueue_voting_assets() {
		wp_enqueue_script( 'idea-garden-voting', main()->url() . 'js/voting.js', [ 'jquery' ], false, true );
		wp_localize_script( 'idea-garden-voting', 'ideaGarden', [
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		] );

		$this->voting_assets_enqueued = true;
	}

	public function vote_action() {
		$action  = $_POST['mode'];
		$check   = $_POST['check'];
		$idea_id = $_POST['idea_id'];
		$user_id = $_POST['user_id'];
		$votes = new Votes( $idea_id );

		if ( ! wp_verify_nonce( $check, "vote-$idea_id" ) ) {
			wp_send_json_error();
		}

		if ( 'add' === $action ) {
			$votes->add_supporter( $user_id );
		}
		elseif ( 'remove' === $action ) {
			$votes->remove_supporter( $user_id );
		}

		wp_send_json_success( [
			'idea_id'    => $idea_id,
			'votes'      => $votes->supporter_count(),
			'user_voted' => $votes->is_supporter( get_current_user_id() )
		] );
	}
}