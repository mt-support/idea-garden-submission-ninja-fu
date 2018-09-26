<?php
namespace Modern_Tribe\Idea_Garden;

use WP_Post;

class Idea_Statuses {
	/**
	 * Order is significant: by default we order results partly
	 * by post status - statuses that should be featured first
	 * ought to be listed closer to the end of the array.
	 */
	const STATES = [
		'internal' => [
			'label'   => 'Internal',
			'public'  => false,
			'default' => false,
		],
		'pending' => [
			'label'   => 'Pending Review',
			'public'  => false,
			'default' => false,
		],
		'rejected' => [
			'label'   => 'Rejected',
			'public'  => true,
			'private' => false,
			'default' => false,
		],
		'backlog' => [
			'label'   => 'Backlog',
			'public'  => true,
			'default' => true,
		],
		'planned' => [
			'label'   => 'Planned',
			'public'  => true,
			'default' => true,
		],
		'started' => [
			'label'   => 'Started',
			'public'  => true,
			'default' => true,
		],
		'in-development' => [
			'label'   => 'In Development',
			'public'  => true,
			'default' => true,
		],
		'in-testing' => [
			'label'   => 'In Testing',
			'public'  => true,
			'default' => true,
		],
		'complete' => [
			'label'   => 'Complete',
			'public'  => true,
			'default' => false,
		],
		'trash' => [
			'no_register' => true,
			'label'       => 'Trash',
			'public'      => false,
			'default'     => false,
		]
	];

	public function setup() {
		add_action( 'init', [ $this, 'register' ] );
		add_action( 'admin_print_scripts-post.php', [ $this, 'idea_statuses_script' ] );
		add_filter( 'get_post_status', [ $this, 'filter_post_status' ], 10, 2 );
	}

	public function register() {
		foreach ( self::STATES as $slug => $properties ) {
			if ( ! empty( $properties['no_register'] ) ) {
				continue;
			}

			if ( ! empty( $properties['public'] ) ) {
				$properties['private'] = false;
			}

			register_post_status( $slug, $properties );
		}
	}

	/**
	 * Having no particular need for the 'publish' status (default for new submissions)
	 * in relation to submitted ideas, let's map it to 'pending'.
	 *
	 * @param string  $status
	 * @param WP_Post $post
	 *
	 * @return string
	 */
	public function filter_post_status( $status, $post ) {
		if ( $post->post_type !== 'nf_sub' ) {
			return $status;
		}

		return 'publish' === $status ? 'pending' : $status;
	}

	public function idea_statuses_script() {
		// @todo decide to either abandon this or fix to work with standard WP post editor
		if ( 'nf_sub' === get_post_type() ) {
			wp_enqueue_script( 'idea-garden-statuses', main()->url() . 'js/idea-statuses.js' );
		}
	}

	public function default_statuses() {
		$statuses = [];

		foreach ( self::STATES as $slug => $possible_default_status ) {
			if ( ! empty( $possible_default_status['default'] ) ) {
				$statuses[] = $slug;
			}
		}

		return $statuses;
	}

	/**
	 * Filters out any elements in $statuses which are not valid idea
	 * post status slugs, and returns the remainder.
	 *
	 * @param array $statuses
	 *
	 * @return array
	 */
	public function filter_statuses( array $statuses ): array {
		return array_intersect( $statuses, array_keys( self::STATES ) );
	}
}