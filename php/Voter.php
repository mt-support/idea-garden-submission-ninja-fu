<?php
namespace Modern_Tribe\Idea_Garden;

class Voter {
	const ADDED_VOTE = 'added_vote';
	const REMOVED_VOTE = 'removed_vote';

	/** @var int */
	private $user_id = 0;

	public function __construct( int $user_id ) {
		$this->user_id = $user_id;
	}

	/**
	 * Returns the idea IDs for which this user has added support.
	 *
	 * @return array
	 */
	public function supported_ideas(): array {
		return array_filter( get_user_meta( $this->user_id, Votes::SUPPORTERS_KEY ), 'absint' );
	}

	/**
	 * Logs the voting habits of the user.
	 *
	 * @param int $idea_id
	 * @param string $action
	 */
	public function log_activity( int $idea_id, string $action ) {
		$date = date_i18n( 'Y-m-d' );
		add_user_meta( $this->user_id, $action, "{$idea_id}:{$date}" );
	}
}