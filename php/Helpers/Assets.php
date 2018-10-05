<?php
namespace Modern_Tribe\Idea_Garden\Helpers;

class Assets {
	/**
	 * Enqueue/add the specified JS script file.
	 *
	 * This can be called directly and takes care of using the appropriate action to
	 * set things up.
	 *
	 * @param string $handle
	 * @param string $source
	 * @param array  $dependencies
	 * @param bool   $version
	 * @param bool   $in_footer
	 */
	public static function add_script(
		string $handle,
		string $source,
		array $dependencies = [],
		$version = false,
		$in_footer = false
	) {
		if ( did_action( 'wp_enqueue_scripts' ) ) {
			wp_enqueue_script( $handle, $source, $dependencies, $version, $in_footer );
			return;
		}

		add_action( 'wp_enqueue_scripts', function() use ( $handle, $source, $dependencies, $version, $in_footer ) {
			wp_enqueue_script( $handle, $source, $dependencies, $version, $in_footer );
		} );
	}

	public static function add_data( string $handle, string $object_name, array $data ) {
		if ( did_action( 'wp_enqueue_scripts' ) ) {
			wp_localize_script( $handle, $object_name, $data );
			return;
		}

		add_action( 'wp_enqueue_scripts' , function() use ( $handle, $object_name, $data ) {
			wp_localize_script( $handle, $object_name, $data );
		} );
	}

	/**
	 * Enqueue/add the specified CSS file.
	 *
	 * This can be called directly and takes care of using the appropriate action to
	 * set things up.
	 *
	 * @param string $handle
	 * @param string $source
	 * @param array  $dependencies
	 * @param bool   $version
	 * @param string $media
	 */
	public static function add_style(
		string $handle,
		string $source,
		array $dependencies = [],
		$version = false,
		string $media = 'all'
	) {
		if ( did_action( 'wp_enqueue_scripts' ) ) {
			wp_enqueue_style( $handle, $source, $dependencies, $version, $media );
			return;
		}

		add_action( 'wp_enqueue_scripts', function() use ( $handle, $source, $dependencies, $version, $media ) {
			wp_enqueue_style( $handle, $source, $dependencies, $version, $media );
		} );
	}
}