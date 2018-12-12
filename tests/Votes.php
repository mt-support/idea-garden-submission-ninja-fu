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

		$idea_1->votes()->add_vote( $user );
		$idea_2->votes()->add_vote( $user );

		// We don't know what the fake idea post IDs will be, but if we combine them we
		// will have a new value we know the user has noted voted for
		$unsupported_idea = $idea_1->id() + $idea_2->id();

		$this->assertContains( $idea_1->id(), $voter->supported_ideas(), 'List of ideas supported by user is accurate' );
		$this->assertContains( $idea_2->id(), $voter->supported_ideas(), 'List of ideas supported by user is accurate' );
		$this->assertNotContains( $unsupported_idea, $voter->supported_ideas(), 'List of ideas supported by user is accurate' );
	}

	private function generate_fake_idea(): int {
		return Idea_Garden\main()->ideas()->make_idea(
			Fake_Content::create()->title,
			Fake_Content::create()->text
		);
	}

	private function generate_fake_user(): int  {
		$usr = wp_insert_user( [
			'user_login' => Fake_Content::create()->name,
			'user_email' => Fake_Content::create()->email,
			'user_pass' => md5( time() ),
			'role' => 'subscriber',
		] );

		return $usr;
	}

}
