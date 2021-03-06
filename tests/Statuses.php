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
}
