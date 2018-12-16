<?php
namespace Modern_Tribe\Idea_Garden\Commands;

use Modern_Tribe\Idea_Garden\Ideas;
use WP_CLI;

class Delete {
	private $found = 0;
	private $deleted = 0;

	/**
	 * Delete ideas.
	 *
	 * This can be useful for cleaning up test data.
	 *
	 * ## OPTIONS
	 *
	 * [--test_users_only]
	 * : Only delete fake (test) users
	 *
	 * [--include_test_users]
	 * : Additionally delete fake (test) users
	 */
	public function __invoke( $args, $assoc_args ) {
		$test_users_only = (bool) $assoc_args['test_users_only'];
		$include_test_users = (bool) $assoc_args['include_test_users'];
		WP_CLI::line( 'Deleting...' );

		if ( $test_users_only || $include_test_users ) {
			( new Delete_Test_Users() )();
		}

		if ( ! $test_users_only ) {
			$this->delete_ideas();
		}
	}

	private function delete_ideas() {
		while ( $this->delete_batch_of_ideas() ) {}
		WP_CLI::line( WP_CLI::colorize ( " %bDeleted {$this->deleted} ideas (of {$this->found} found)%n" ) );
	}

	private function delete_batch_of_ideas() {
		$ideas = get_posts( [
			'fields' => 'ids',
			'posts_per_page' => 200,
			'post_status' => 'any',
			'post_type' => Ideas::POST_TYPE,
		] );

		foreach ( $ideas as $idea_id ) {
			print WP_CLI::colorize( '%p#%n' );

			$this->found++;

			if ( wp_delete_post( $idea_id, true ) ) {
				$this->deleted++;
			}
		}

		return count( $ideas ) === 200 ? true : false;
	}
}