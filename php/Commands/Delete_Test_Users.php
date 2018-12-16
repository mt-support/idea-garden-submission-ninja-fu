<?php
namespace Modern_Tribe\Idea_Garden\Commands;

use Modern_Tribe\Idea_Garden\Ideas;
use WP_CLI;

class Delete_Test_Users {
	private $found = 0;
	private $deleted = 0;

	/**
	 * Deletes test users created by the `wp ideas generate` command.
	 *
	 * This can be useful for cleaning up test data.
	 */
	public function __invoke() {
		while ( $this->delete_batch_of_test_users() ) {}
		WP_CLI::line( WP_CLI::colorize ( " %bDeleted {$this->deleted} test users (of {$this->found} found)%n" ) );
	}

	private function delete_batch_of_test_users() {
		$test_users = get_users( [
			'fields' => 'ids',
			'meta_query' => [ [
				'key' => 'idea_garden_fake_user',
				'compare' => 'EXISTS',
			] ],
			'number' => 200,
		] );

		foreach ( $test_users as $user_id ) {
			print WP_CLI::colorize( '%p@%n' );
			$this->found++;

			if ( wp_delete_user( $user_id, true ) ) {
				$this->deleted++;
			}
		}

		return count( $test_users ) === 200 ? true : false;
	}
}