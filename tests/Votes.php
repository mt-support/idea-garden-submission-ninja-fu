<?php
namespace Modern_Tribe\Idea_Garden\Tests;

use Faker\Factory as Fake_Content;
use Modern_Tribe\Idea_Garden;
use Modern_Tribe\Idea_Garden\Idea;
use Modern_Tribe\Idea_Garden\Voter;
use WP_UnitTestCase as Base_Test;

class Votes extends Base_Test {
	/**
	 * @test
	 */
	function can_add_and_remove_votes() {
		$idea = new Idea( $this->generate_fake_idea() );
		$user_1 = $this->generate_fake_user();
		$user_2 = $this->generate_fake_user();

		$idea->votes()->add_vote( $user_1 );
		$idea->votes()->add_vote( $user_2 );
		$this->assertEquals( 2, $idea->votes()->count(), 'Added two user votes to the same idea' );

		$idea->votes()->remove_vote( $user_1 );
		$this->assertEquals( 1, $idea->votes()->count(), 'Removed one vote from an idea, leaving a total of one vote' );
	}

	/**
	 * @test
	 */
	function can_test_if_users_support_an_idea() {
		$user = $this->generate_fake_user();
		$idea_1 = new Idea( $this->generate_fake_idea() );
		$idea_2 = new Idea( $this->generate_fake_idea() );

		$idea_1->votes()->add_vote( $user );
		$idea_2->votes()->add_vote( $user );
		$idea_2->votes()->remove_vote( $user );

		$this->assertTrue( $idea_1->votes()->is_supporter( $user ), 'Can confirm user supports an idea' );
		$this->assertFalse( $idea_2->votes()->is_supporter( $user ), 'Can confirm user does not support an idea' );
	}

	/**
	 * @test
	 */
	function can_list_ideas_supported_by_a_user() {
		$user = $this->generate_fake_user();
		$voter = new Voter( $user );

		$idea_1 = new Idea( $this->generate_fake_idea() );
		$idea_2 = new Idea( $this->generate_fake_idea() );
		$idea_3 = new Idea( $this->generate_fake_idea() );

		$idea_1->votes()->add_vote( $user );
		$idea_2->votes()->add_vote( $user );

		$this->assertContains( $idea_1->id(), $voter->supported_ideas(), 'List of ideas the user supports includes the ID of an idea they voted for' );
		$this->assertContains( $idea_2->id(), $voter->supported_ideas(), 'List of ideas the user supports includes the ID of an idea they voted for' );
		$this->assertNotContains( $idea_3->id(), $voter->supported_ideas(), 'List of ideas the user supports does not include the ID of an idea they did not vote for' );
	}

	private function generate_fake_idea(): int {
		return Idea_Garden\main()->ideas()->make_idea(
			Fake_Content::create()->title,
			Fake_Content::create()->text
		);
	}

	private function generate_fake_user(): int  {
		return wp_insert_user( [
			'user_login' => Fake_Content::create()->name,
			'user_email' => Fake_Content::create()->email,
			'user_pass' => md5( time() ),
			'role' => 'subscriber',
		] );
	}

}
