<?php
namespace Modern_Tribe\Idea_Garden\Tests;

use Modern_Tribe\Idea_Garden;
use Modern_Tribe\Idea_Garden\Taxonomies\Idea_Statuses;
use WP_UnitTestCase as Base_Test;

class Statuses extends Base_Test {
	/**
	 * @test
	 */
	public function can_set_visibility_to_public_or_internal() {
		$idea_statuses = Idea_Garden\main()->idea_statuses();
		$status = $idea_statuses->create_term( 'A Lovely Status' );

		$idea_statuses->set_public( $status, true );

		$this->assertTrue(
			$idea_statuses->is_public( $status ),
			'Status is public'
		);

		$idea_statuses->set_public( $status, false );

		$this->assertFalse(
			$idea_statuses->is_public( $status ),
			'Status is not public (it is internal)'
		);
	}

	/**
	 * @test
	 */
	public function default_visibility_returned_if_not_already_set() {
		$idea_statuses = Idea_Garden\main()->idea_statuses();
		$status = $idea_statuses->create_term( 'Status of Liberty' );

		$this->assertTrue(
			$idea_statuses->is_public( $status, true ),
			'Default visibility (public) was indicated as expected'
		);

		$this->assertFalse(
			$idea_statuses->is_public( $status, false ),
			'Default visibility (internal) was indicated as expected'
		);
	}

	/**
	 * @test
	 */
	public function can_list_public_statuses() {
		$idea_statuses = Idea_Garden\main()->idea_statuses();

		$status_1 = $idea_statuses->create_term( 'A Status Among the Pigeons' );
		$status_2 = $idea_statuses->create_term( 'The Inbearable Lightness of Statuses' );
		$status_3 = $idea_statuses->create_term( 'Status You Later' );

		$idea_statuses->set_public( $status_1, true );  # Public
		$idea_statuses->set_public( $status_2, true );  # Public
		$idea_statuses->set_public( $status_3, false ); # Internal

		$public_status_ids = wp_list_pluck( $idea_statuses->list_public(), 'term_id' );

		$this->assertContains(
			$status_1,
			$public_status_ids,
			'List of public statuses included status we set as public'
		);

		$this->assertContains(
			$status_2,
			$public_status_ids,
			'List of public statuses included status we set as public'
		);

		$this->assertNotContains(
			$status_3,
			$public_status_ids,
			'List of public statuses did not include status we set as internal'
		);
	}

	/**
	 * @test
	 */
	public function can_list_internal_statuses() {
		$idea_statuses = Idea_Garden\main()->idea_statuses();

		$status_1 = $idea_statuses->create_term( 'The Great Wall of Statuses' );
		$status_2 = $idea_statuses->create_term( 'Homer J Status' );
		$status_3 = $idea_statuses->create_term( 'Status Status Status!' );

		$idea_statuses->set_public( $status_1, false ); # Internal
		$idea_statuses->set_public( $status_2, true );  # Public
		$idea_statuses->set_public( $status_3, false ); # Internal

		$internal_status_ids = wp_list_pluck( $idea_statuses->list_internal(), 'term_id' );

		$this->assertContains(
			$status_1,
			$internal_status_ids,
			'List of internal statuses included status we set as internal'
		);

		$this->assertNotContains(
			$status_2,
			$internal_status_ids,
			'List of internal statuses did not include status we set as public'
		);

		$this->assertContains(
			$status_3,
			$internal_status_ids,
			'List of internal statuses included status we set as internal'
		);
	}

	/**
	 * @test
	 */
	public function can_list_public_status_terms_plus_terms_with_undefined_visibility() {
		$idea_statuses = Idea_Garden\main()->idea_statuses();

		$status_1 = $idea_statuses->create_term( 'Horatio Status' );
		$status_2 = $idea_statuses->create_term( 'Status Khan' );
		$status_3 = $idea_statuses->create_term( 'Count Status' );

		// Important: we are not setting the visibility of $status_3
		$idea_statuses->set_public( $status_1, true );  # Public
		$idea_statuses->set_public( $status_2, false ); # Internal

		// Get internal statuses and *include statuses for which visibility is not defined*
		$internal_status_ids = wp_list_pluck( $idea_statuses->list_internal( false ), 'term_id' );

		$this->assertNotContains(
			$status_1,
			$internal_status_ids,
			'Non-strict list of internal statuses did not include status we set as public'
		);

		$this->assertContains(
			$status_2,
			$internal_status_ids,
			'Non-strict list of internal statuses included status we set as internal'
		);

		$this->assertContains(
			$status_3,
			$internal_status_ids,
			'Non-strict list of internal statuses included status for which visibility was not explicitly set'
		);
	}
}
