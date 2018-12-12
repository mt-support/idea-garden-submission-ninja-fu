<?php
namespace Modern_Tribe\Idea_Garden;

class Votes {
	const SUPPORTERS_KEY = 'ig_supporters_list';
	const SUPPORTERS_COUNT_KEY = 'ig_supporters_count';

	private $idea_id = 0;

	public function __construct( int $idea_id ) {
		$this->idea_id = $idea_id;
	}

	/**
	 * Returns the current idea's ID.
	 *
	 * @return int
	 */
	public function id(): int {
		return (int) $this->idea_id;
	}

	/**
	 * Returns an array of user objects representing supporters
	 * of this idea. If optional parameter $ids_only is set to
	 * true, only the user IDs will be returned.
	 *
	 * @param bool $ids_only
	 *
	 * @return array
	 */
	public function get_supporters( $ids_only = false ): array {
		return get_users( [
			'fields' => $ids_only ? 'ID' : 'all',
			'meta_key' => self::SUPPORTERS_KEY,
			'meta_value' => $this->idea_id,
		] );
	}

	/**
	 * Indicates how many supporters there are for the current idea.
	 *
	 * @return int
	 */
	public function count(): int {
		return absint( get_post_meta( $this->idea_id, self::SUPPORTERS_COUNT_KEY, true ) );
	}

	/**
	 * Indicates if the specified user is a supporter of the current idea.
	 *
	 * @param int $user_id
	 *
	 * @return bool
	 */
	public function is_supporter( int $user_id ): bool {
		return in_array( $this->idea_id, get_user_meta( $user_id, self::SUPPORTERS_KEY ) );
	}

	/**
	 * Records that the specified user supports the current idea.
	 *
	 * @param int $user_id
	 */
	public function add_vote( int $user_id ) {
		if ( $this->is_supporter( $user_id ) ) {
			return;
		}

		add_user_meta( $user_id, self::SUPPORTERS_KEY, $this->idea_id );
		$this->recount();
	}

	/**
	 * Removes the specified user's support for the current idea.
	 *
	 * @param int $user_id
	 */
	public function remove_vote( int $user_id ) {
		delete_user_meta( $user_id, self::SUPPORTERS_KEY, $this->idea_id );
		$this->recount();
	}

	/**
	 * Recount votes for the current idea.
	 */
	public function recount() {
		$new_count = count( $this->get_supporters( true ) );
		update_post_meta( $this->idea_id, self::SUPPORTERS_COUNT_KEY, $new_count );
	}
}