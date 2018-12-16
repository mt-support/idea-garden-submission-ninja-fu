<?php
namespace Modern_Tribe\Idea_Garden\Commands;

use Faker\Factory as Fake_Content;
use Modern_Tribe\Idea_Garden;
use Modern_Tribe\Idea_Garden\Votes;
use WP_CLI;
use WP_User_Query;

class Generate {
	private $make_test_users = false;
	private $generated = 0;
	private $generated_users = 0;
	private $max_votes = 0;
	private $min_votes = 0;
	private $num_ideas = 0;
	private $user_id_pool = [];

	/**
	 * Generates fake idea data for testing.
	 *
	 * ## OPTIONS
	 *
	 * [--num_ideas=<num_ideas>]
	 * : The number of ideas that should be generated (defaults to 100)
	 *
	 * [--min_votes=<min_votes>]
	 * : The minimum number of votes to be applied to the generated ideas (defaults to 0)
	 *
	 * [--max_votes=<max_votes>]
	 * : The maximum number of votes to be applied to the generated ideas (defaults to 5)
	 *
	 * [--make_test_users]
	 * : If there are insufficient user accounts, create fake/test accounts
	 */
	public function __invoke( $args, $assoc_args ) {
		$defaults = [
			'num_ideas'     => 100,
			'min_votes'     => 0,
			'max_votes'     => 5,
			'make_test_users' => false,
		];

		$args = array_merge( $defaults, $assoc_args );
		$this->make_test_users = (bool) $args['make_test_users'];
		$this->max_votes = (int) $args['max_votes'];
		$this->min_votes = (int) $args['min_votes'];
		$this->num_ideas = (int) $args['num_ideas'];

		WP_CLI::line( 'Generating...' );
		$this->build_user_id_pool();

		for ( $i = 0; $i < $this->num_ideas; $i++ ) {
			$this->generate_idea();
		}

		WP_CLI::line( WP_CLI::colorize ( " %bGenerated {$this->generated} ideas%n " ) );
	}

	/**
	 * Creates a new idea submission and applies votes.
	 */
	private function generate_idea() {
		print WP_CLI::colorize( '%p#%n' );

		$idea_id = Idea_Garden\main()->ideas()->make_idea(
			Fake_Content::create()->sentence( rand( 3, 8 ) ),
			Fake_Content::create()->sentences( rand( 1, 4 ), true )
		);

		if ( $idea_id ) {
			$this->add_votes( $idea_id );
			$this->generated++;
		}
	}

	/**
	 * Apply votes to our latest submission.
	 *
	 * @param int  $idea_id
	 */
	private function add_votes( int $idea_id ) {
		$votes = new Votes( $idea_id );
		$voters = $this->get_voters( rand( $this->min_votes, $this->max_votes ) );

		foreach ( $voters as $user_id ) {
			$votes->add_vote( $user_id );
		}
	}

	/**
	 * Creates a pool of user IDs from which to assign votes to ideas.
	 *
	 * Real/existing user IDs will be used where possible, but if generation of test users
	 * is allowed we will create additional accounts.
	 */
	private function build_user_id_pool() {
		$query = new WP_User_Query( [
			'number' => 100000,
			'fields' => 'ID',
		] );

		$this->user_id_pool = (array) $query->get_results();

		$ideal_size_of_user_pool = max(
			$this->max_votes,
			absint( $this->num_ideas / rand( 2, 4 ) )
		);

		// If we don't have the ideal number of user IDs and generation of test users
		// is allowed, let's create extra accounts to bolster things
		if ( $this->make_test_users && $ideal_size_of_user_pool > count( $this->user_id_pool ) ) {
			$this->generate_test_users( $ideal_size_of_user_pool );
		}
	}

	private function generate_test_users( int $target_pool_size ) {
		$to_generate = $target_pool_size - count( $this->user_id_pool );

		for ( $i = 0; $i < $to_generate; $i++ ) {
			print WP_CLI::colorize( '%p@%n' );

			$test_user_id = wp_insert_user( [
				'user_login' => Fake_Content::create()->name,
				'user_email' => Fake_Content::create()->email,
				'user_pass' => md5( time() ),
				'role' => 'subscriber',
			] );

			$this->generated_users++;
			update_user_meta( $test_user_id, 'idea_garden_fake_user', true );
		}

		WP_CLI::line( WP_CLI::colorize ( " %bGenerated {$this->generated_users} test users%n " ) );
	}

	/**
	 * Supply a randomish number of user IDs to act as voters.
	 *
	 * @param int $how_many
	 *
	 * @return array
	 */
	private function get_voters( int $how_many ): array {
		$voter_list = [];
		$user_ids = $this->user_id_pool;
		shuffle( $user_ids );

		for ( $i = 0; $i < $how_many; $i++ ) {
			// Pull from our list of real users to start with
			if ( empty( $user_ids ) ) {
				break;
			}

			$voter_list[] = array_shift( $user_ids );
		}

		return $voter_list;
	}
}