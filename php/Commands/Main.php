<?php
namespace Modern_Tribe\Idea_Garden\Commands;

use Faker\Factory as Content_Generator;
use Modern_Tribe\Idea_Garden\Votes;
use WP_CLI;
use WP_User_Query;

/**
 * Registers "wp ideagarden generate" to quickly build sample data for testing purposes.
 */
class Main {
	private $content_generator;

	public function __construct() {
		if ( ! class_exists( 'WP_CLI' ) ) {
			return;
		}

		$this->content_generator = Content_Generator::create();

		WP_CLI::add_command( 'ideas', $this );
	}

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
	 * [--do_fake_votes]
	 * : If there are insufficient user accounts, register votes from non-existent users
	 */
	public function generate( $args, $assoc_args ) {
		$defaults = [
			'num_ideas'     => 100,
			'min_votes'     => 0,
			'max_votes'     => 5,
			'do_fake_votes' => false,
		];

		$args = array_merge( $defaults, $assoc_args );

		for ( $i = 0; $i < $args['num_ideas']; $i++ ) {
			$this->generate_idea(
				(int)  $args['min_votes'],
				(int)  $args['max_votes'],
				(bool) $args['do_fake_votes']
			);
		}
	}

	/**
	 * Creates a new idea submission and applies votes.
	 *
	 * @param int $min_votes
	 * @param int $max_votes
	 * @param bool $do_fake_votes
	 */
	private function generate_idea( int $min_votes = 0, int $max_votes = 100, bool $do_fake_votes ) {
		$long_text = $this->get_paragraphs();
		$short_text = $this->content_generator->realText( rand( 30, 80 ) );
		$this->add_votes( $idea_id, $min_votes, $max_votes, $do_fake_votes );
	}

	private function get_paragraphs() {
		$paragraphs = [];

		// Mostly just want one or two paragraphs
		$weighted_rand = rand( 1, 2 );

		// If we roll a two, we re-eval and consider one to *three* paragraphs
		if ( $weighted_rand === 2 ) {
			$weighted_rand = rand( 1, 3 );
		}

		for ( $i = 0; $i < $weighted_rand; $i++ ) {
			$paragraphs[] = $this->content_generator->realText( rand( 100, 400 ) );
		}

		return join( "\n\n", $paragraphs );
	}

	/**
	 * Apply votes to our latest submission.
	 *
	 * @param int  $idea_id
	 * @param int  $min_votes
	 * @param int  $max_votes
	 * @param bool $do_fake_votes
	 */
	private function add_votes( int $idea_id, int $min_votes, int $max_votes, bool $do_fake_votes ) {
		$votes = new Votes( $idea_id );
		$voters = $this->get_voters( rand( $min_votes, $max_votes ), $do_fake_votes );

		foreach ( $voters as $voter_id ) {
			$votes->add_supporter( $voter_id );
		}
	}

	/**
	 * Supply a randomish number of user IDs to act as voters.
	 *
	 * @param int $how_many
	 * @param bool $do_fake_votes
	 *
	 * @return array
	 */
	private function get_voters( int $how_many, bool $do_fake_votes ): array {
		$voter_list = [];
		$user_ids = $this->user_id_list();
		shuffle( $user_ids );

		for ( $i = 0; $i < $how_many; $i++ ) {
			// Pull from our list of real users to start with
			if ( ! empty( $user_ids ) ) {
				$voter_list[] = array_shift( $user_ids );
				continue;
			}

			// If we are not allowing fake votes, bail here
			if ( ! $do_fake_votes ) {
				break;
			}

			// Otherwise ... let's create some fake user IDs to make up the quota
			while ( true ) {
				$pos_rand_id = rand( 1, 10000000 );

				if ( ! in_array( $pos_rand_id, $voter_list ) ) {
					$voter_list[] = $pos_rand_id;
					break;
				}
			}
		}

		return $voter_list;
	}

	/**
	 * Returs a list of all (or almost all!) user IDs.
	 *
	 * @return array
	 */
	private function user_id_list(): array {
		static $user_id_list = [];

		if ( empty( $user_id_list ) ) {
			$query = new WP_User_Query( [
				'number' => 100000,
				'fields' => 'ID',
			] );

			$user_id_list = (array) $query->get_results();
		}

		return $user_id_list;
	}
}