<?php
namespace Modern_Tribe\Idea_Garden\Ninja_Fu;

use WP_Post;

/**
 * @todo add logic making 'pending' the default status for idea submissions
 */
class Idea_Statuses {
	const STATES = [
		'internal' => [
			'label'   => 'Internal',
			'public'  => false,
			'private' => true,
		],
		'pending' => [
			'label'   => 'Pending Review',
			'public'  => false,
			'private' => true,
		],
		'rejected' => [
			'label'   => 'Rejected',
			'public'  => true,
			'private' => false,
		],
		'planned' => [
			'label'   => 'Planned',
			'public'  => true,
			'private' => false,
		],
		'started' => [
			'label'   => 'Started',
			'public'  => true,
			'private' => false,
		],
		'in-development' => [
			'label'   => 'In Development',
			'public'  => true,
			'private' => false,
		],
		'in-testing' => [
			'label'   => 'In Testing',
			'public'  => true,
			'private' => false,
		],
		'complete' => [
			'label'   => 'Complete',
			'public'  => true,
			'private' => false,
		],
		'trash' => [
			'no_register' => true,
			'label'       => 'Trash',
			'public'      => false,
			'private'     => true,
		]
	];

	public function __construct() {
		add_action( 'init', [ $this, 'register' ] );
		add_action( 'admin_print_scripts-post.php', [ $this, 'idea_statuses_script' ] );
		add_action( 'nf_sub_edit_after_status', [ $this, 'status_selector' ] );
		add_filter( 'get_post_status', [ $this, 'filter_post_status' ], 10, 2 );
	}

	public function register() {
		foreach ( self::STATES as $slug => $properties ) {
			if ( ! empty( $properties['no_register'] ) ) {
				continue;
			}

			register_post_status( $slug, $properties );
		}
	}

	public function status_selector( $post ) {
		print '<select name="post_status" id="idea-status-selector">';

		$status = get_post_status( $post );

		foreach ( self::STATES as $slug => $properties ) {
			print '<option value="' . esc_attr( $slug ) . '" ' . selected( $slug === $status ) . '>'
			    . esc_html( $properties['label'] )
				. '</option>';
		}

		print '</select>';
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
		if ( 'nf_sub' === get_post_type() ) {
			wp_enqueue_script( 'idea-garden-statuses', main()->url() . 'js/idea-statuses.js' );
		}
	}
}