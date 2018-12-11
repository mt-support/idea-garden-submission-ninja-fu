<?php
namespace Modern_Tribe\Idea_Garden\Tests;

use Modern_Tribe\Idea_Garden;
use Modern_Tribe\Idea_Garden\Idea;
use WP_UnitTestCase as Base_Test;

class Categories extends Base_Test {
	/**
	 * @test
	 */
	function can_create_a_category() {
		$name = 'Test Category ' .uniqid();
		$category_id = Idea_Garden\main()->idea_categories()->create_term( $name );

		$this->assertTrue(
			is_int( $category_id ),
			'Newly created idea category ID is a positive integer'
		);

		$this->assertTrue(
			Idea_Garden\main()->idea_categories()->is_valid_category_id( $category_id ),
			'New category ID appears to be valid'
		);
	}

	/**
	 * @test
	 */
	function can_assign_a_category_to_an_idea() {
		$name = 'Test Category ' .uniqid();
		$category_id = Idea_Garden\main()->idea_categories()->create_term( $name );

		$idea = new Idea( Idea_Garden\main()->ideas()->make_idea(
			'Test idea ' . uniqid()
		) );

		$idea->categories( $category_id );
		$assigned_category_ids = wp_list_pluck( $idea->categories(), 'term_id' );

		$this->assertContains(
			$category_id,
			$assigned_category_ids,
			'Category was successfully assigned to the idea'
		);
	}
}
