<?php
namespace Modern_Tribe\Idea_Garden\Tests;

use Faker\Factory as Fake_Content;
use Modern_Tribe\Idea_Garden;
use Modern_Tribe\Idea_Garden\Idea;
use WP_UnitTestCase as Base_Test;

class Ideas extends Base_Test {
	/**
	 * @test
	 */
	function can_create_an_idea() {
		$idea_title = 'Test Idea ' . uniqid();
		$idea_description = Fake_Content::create()->text;

		$idea_id = Idea_Garden\main()->ideas()->make_idea( $idea_title, $idea_description );

		$this->assertTrue(
			is_int( $idea_id ) && $idea_id > 0,
			'Newly created idea ID is a positive integer'
		);

		$idea = new Idea( $idea_id );

		$this->assertSame(
			$idea->title(),
			$idea_title,
			'Successfully loaded the test idea and confirmed it looked as expected'
		);
	}
}
