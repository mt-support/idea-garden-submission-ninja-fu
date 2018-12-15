<?php
namespace Modern_Tribe\Idea_Garden\Tests;

use Modern_Tribe\Idea_Garden;
use Modern_Tribe\Idea_Garden\Idea;
use WP_UnitTestCase as Base_Test;

class Taxonomies extends Base_Test {
	/**
	 * @test
	 */
	function defaults_are_respected_for_undefined_boolean_meta() {
		$idea_tags = Idea_Garden\main()->idea_tags();
		$tag_id = $idea_tags->create_term( 'Our Little Test Tag' );

		$this->assertTrue(
			$idea_tags->get_boolean_meta( $tag_id, 'some-switch', true ),
			'Default value was returned as expected for undefined piece of boolean term meta'
		);

		$this->assertFalse(
			$idea_tags->get_boolean_meta( $tag_id, 'some-switch', false ),
			'Default value was returned as expected for undefined piece of boolean term meta'
		);
	}

	/**
	 * @test
	 */
	function can_get_and_set_boolean_meta() {
		$idea_tags = Idea_Garden\main()->idea_tags();
		$tag_id = $idea_tags->create_term( 'Our Little Test Tag' );

		$idea_tags->set_boolean_meta( $tag_id, 'bool-flag', true );

		$this->assertTrue(
			$idea_tags->get_boolean_meta( $tag_id, 'bool-flag', false ),
			'Expected value was returned for boolean term field'
		);

		$idea_tags->set_boolean_meta( $tag_id, 'bool-flag', false );

		$this->assertFalse(
			$idea_tags->get_boolean_meta( $tag_id, 'bool-flag', true ),
			'Boolean meta was updated and returned the expected value successfully'
		);
	}

	/**
	 * @test
	 */
	function can_delete_boolean_meta() {
		$idea_tags = Idea_Garden\main()->idea_tags();
		$tag_id = $idea_tags->create_term( 'Indubitably a Tag of Some Sort' );

		$idea_tags->set_boolean_meta( $tag_id, 'foo-bar', true );

		$this->assertTrue(
			$idea_tags->get_boolean_meta( $tag_id, 'foo-bar', false ),
			'Boolean field was successfully set for tag'
		);

		$idea_tags->delete_meta( $tag_id, 'foo-bar' );

		$this->assertTrue(
			$idea_tags->get_boolean_meta( $tag_id, 'foo-bar', true ),
			'Boolean field was successfully deleted for tag'
		);
	}
}
