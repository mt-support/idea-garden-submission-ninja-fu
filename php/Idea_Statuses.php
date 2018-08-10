<?php
namespace Modern_Tribe\Idea_Garden\Ninja_Fu;

class Idea_Statuses {
	public function __construct() {
		add_action( 'init', [ $this, 'register' ] );
	}

	public function register() {
		register_post_status( 'internal', [
			'label'   => 'Internal',
			'public'  => false,
			'private' => true,
		] );

		register_post_status( 'pending', [
			'label'   => 'Pending Review',
			'public'  => false,
			'private' => true,
		] );
	}
}