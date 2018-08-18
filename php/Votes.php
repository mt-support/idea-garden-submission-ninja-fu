<?php
namespace Modern_Tribe\Idea_Garden;

class Votes {
	const SUPPORTERS_KEY = 'ig_supporters_list';
	const SUPPORTERS_COUNT_KEY = 'ig_supporters_count';

	private $idea_id = 0;
	private $supporters = [];

	public function __construct( int $idea_id ) {
		$this->idea_id = $idea_id;
		$supporters = get_post_meta( $idea_id, self::SUPPORTERS_KEY, true );
		$this->supporters = ! is_array( $supporters ) ? [] : $supporters;
	}

	public function id() {
		return (int) $this->idea_id;
	}

	public function get_supporters() {
		return $this->supporters;
	}

	public function supporter_count() {
		return count( $this->supporters );
	}

	public function is_supporter( int $user_id ) {
		return isset( $this->supporters[ $user_id ] );
	}

	public function add_supporter( int $user_id ) {
		if ( ! isset( $this->supporters[ $user_id ] ) ) {
			$this->supporters[ $user_id ] = [
				'voted_on' => date_i18n( 'Y-m-d' ),
			];

			$this->save();
		}
	}

	public function remove_supporter( int $user_id ) {
		if ( isset( $this->supporters[ $user_id ] ) ) {
			unset( $this->supporters[ $user_id ] );
			$this->save();
		}
	}

	public function save() {
		update_post_meta( $this->idea_id, self::SUPPORTERS_KEY, $this->supporters );
		update_post_meta( $this->idea_id, self::SUPPORTERS_COUNT_KEY, $this->supporter_count() );
	}
}