<?php
namespace Modern_Tribe\Idea_Garden\Ninja_Fu\Commands;

use Badcow\LoremIpsum\Generator;
use Modern_Tribe\Idea_Garden\Ninja_Fu\Ninja_Forms;
use WP_CLI;

/**
 * @todo requires voting logic to be added
 */
class Main {
	public function __construct() {
		if ( ! class_exists( 'WP_CLI' ) ) {
			return;
		}

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

	private function generate_idea( $form, int $form_id, array $fields, int $min_votes, int $max_votes, bool $do_fake_votes ) {
		$fields     = $form->get_fields();
		$submission = $form->sub()->get();

		foreach( $fields as $field_id => $field ) {
			$options = $field->get_setting( 'options' );
			$generator = new Generator;

			// Pick a random option, if this is a select/multiselect field
			if ( ! empty( $options ) ) {
				$value = (array) $options[ rand( 0, count( $options ) - 1 ) ]['value'];
			}
			// Otherwise supply lots of random text for textarea fields
			elseif ( 'textarea' === $field->get_setting( 'type' ) ) {
				$value = implode( ' ', $generator->getRandomWords( rand( 6, 60 ) ) );
			}
			// Or slightly less random text for other fields
			else {
				$value = implode( ' ', $generator->getRandomWords( rand( 4, 10 ) ) );
			}

			$submission->update_field_value( $field_id, $value );
		}

		$submission->save();
	}
 }