<?php
namespace Modern_Tribe\Idea_Garden\Ninja_Fu\Commands;

use Faker\Factory as Content_Generator;
use Modern_Tribe\Idea_Garden\Ninja_Fu\Ninja_Forms;
use Modern_Tribe\Idea_Garden\Ninja_Fu\Votes;
use NF_Abstracts_ModelFactory;
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

		WP_CLI::add_command( 'ideagarden', $this );
	}

	/**
	 * Generates fake idea data for testing.
	 *
	 * ## OPTIONS
	 *
	 * --form_id=<form_id>
	 * : The ID of the idea submission form
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
		if ( ! function_exists( 'ninja_forms' ) ) {
			WP_CLI::error( 'Cannot compute // cannot compute // ninja forms must be active // error error brrr gz beep' );
		}

		$defaults = [
			'form_id'       => 0,
			'num_ideas'     => 100,
			'min_votes'     => 0,
			'max_votes'     => 5,
			'do_fake_votes' => false,
		];

		$args = array_merge( $defaults, $assoc_args );
		$form = ninja_forms()->form( $args['form_id'] );
		$fields = Ninja_Forms::get_form_fields_array( $args['form_id'] );

		if ( empty( $fields ) ) {
			WP_CLI::error( 'Invalid form ID or else the form could not be loaded.' );
		}

		for ( $i = 0; $i < $args['num_ideas']; $i++ ) {
			$this->generate_idea(
				$form,
				(int) $args['form_id'],
				$fields,
				(int) $args['min_votes'],
				(int) $args['max_votes'],
				(bool) $args['do_fake_votes']
			);
		}
	}

	/**
	 * Creates a new idea submission and applies votes.
	 *
	 * @param NF_Abstracts_ModelFactory $form
	 * @param int $form_id
	 * @param array $fields
	 * @param int $min_votes
	 * @param int $max_votes
	 * @param bool $do_fake_votes
	 */
	private function generate_idea( NF_Abstracts_ModelFactory $form, int $form_id, array $fields, int $min_votes, int $max_votes, bool $do_fake_votes ) {
		$fields     = $form->get_fields();
		$submission = $form->sub()->get();

		foreach( $fields as $field_id => $field ) {
			$options = $field->get_setting( 'options' );

			// Pick a random option, if this is a select/multiselect field
			if ( ! empty( $options ) ) {
				$value = (array) $options[ rand( 0, count( $options ) - 1 ) ]['value'];
			}
			// Otherwise supply lots of random text for textarea fields
			elseif ( 'textarea' === $field->get_setting( 'type' ) ) {
				$value = $this->get_paragraphs();
			}
			// Or slightly less random text for other fields
			else {
				$value = $this->content_generator->realText( rand( 30, 80 ) );
			}

			$submission->update_field_value( $field_id, $value );
		}

		$submission->save();
		$this->add_votes( $submission->get_id(), $min_votes, $max_votes, $do_fake_votes );
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

	private function add_votes( int $submission_id, int $min_votes, int $max_votes, bool $do_fake_votes ) {
		$votes = new Votes( $submission_id );
		$voters = $this->get_voters( rand( $min_votes, $max_votes ), $do_fake_votes );

		foreach ( $voters as $voter_id ) {
			$votes->add_supporter( $voter_id );
		}
	}

	private function get_voters( int $how_many, bool $do_fake_votes ): array {
		$voter_list = [];
		$user_ids = $this->user_id_list();
		shuffle( $user_ids );

		for ( $i = 0; $i < $how_many; $i++ ) {
			if ( ! empty( $user_ids ) ) {
				$voter_list[] = array_shift( $user_ids );
				continue;
			}

			if ( ! $do_fake_votes ) {
				break;
			}

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

	private function user_id_list() {
		static $user_id_list;

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