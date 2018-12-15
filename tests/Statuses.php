<?php
namespace Modern_Tribe\Idea_Garden\Tests;

use Modern_Tribe\Idea_Garden;
use Modern_Tribe\Idea_Garden\Taxonomies\Idea_Statuses;
use WP_UnitTestCase as Base_Test;

class Statuses extends Base_Test {
	/**
	 * @test
	 */
	function can_set_status_to_public_or_internal() {
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
	 * @todo Consider moving to a test class for the Boolean_Flags trait
	 * 
	 * @test
	 */
	function defaults_are_respected_for_undefined_boolean_meta() {
		$idea_statuses = Idea_Garden\main()->idea_statuses();
		$tag_id = $idea_statuses->create_term( 'Our Little Test Status' );

		$this->assertTrue(
			$idea_statuses->get_boolean_meta( $tag_id, 'some-switch', true ),
			'Default value was returned as expected for undefined piece of boolean term meta'
		);

		$this->assertFalse(
			$idea_statuses->get_boolean_meta( $tag_id, 'some-switch', false ),
			'Default value was returned as expected for undefined piece of boolean term meta'
		);
	}

	/**
	 * @todo Consider moving to a test class for the Boolean_Flags trait
	 *
	 * @test
	 */
	function can_get_and_set_boolean_meta() {
		$idea_statuses = Idea_Garden\main()->idea_statuses();
		$tag_id = $idea_statuses->create_term( 'A Test Status' );

		$idea_statuses->set_boolean_meta( $tag_id, 'bool-flag', true );

		$this->assertTrue(
			$idea_statuses->get_boolean_meta( $tag_id, 'bool-flag', false ),
			'Expected value was returned for boolean term field'
		);

		$idea_statuses->set_boolean_meta( $tag_id, 'bool-flag', false );

		$this->assertFalse(
			$idea_statuses->get_boolean_meta( $tag_id, 'bool-flag', true ),
			'Boolean meta was updated and returned the expected value successfully'
		);
	}

	/**
	 * @todo Consider moving to a test class for the Boolean_Flags trait
	 *
	 * @test
	 */
	function can_delete_boolean_meta() {
		$idea_statuses = Idea_Garden\main()->idea_statuses();
		$tag_id = $idea_statuses->create_term( 'Indubitably a Status of Some Sort' );

		$idea_statuses->set_boolean_meta( $tag_id, 'foo-bar', true );

		$this->assertTrue(
			$idea_statuses->get_boolean_meta( $tag_id, 'foo-bar', false ),
			'Boolean field was successfully set for tag'
		);

		delete_term_meta( $tag_id, 'foo-bar' );

		$this->assertTrue(
			$idea_statuses->get_boolean_meta( $tag_id, 'foo-bar', true ),
			'Boolean field was successfully deleted for tag'
		);
	}
}
