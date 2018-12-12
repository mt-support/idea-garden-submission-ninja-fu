<?php
namespace Modern_Tribe\Idea_Garden;

class Voter {
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
}