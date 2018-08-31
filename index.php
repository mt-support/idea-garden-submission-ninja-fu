<?php
/**
 * Plugin name: Idea Garden Submission Ninja Fu
 * Description: Integrates with Ninja Forms and turns plain old form submissions - our raw building blocks - into mind-amplifiying ideas, through the provision of world class Idea Garden tooling.
 * Version:     2018.07.27.a
 * Author:      The Idea Garden Crew
 */

namespace Modern_Tribe\Idea_Garden;

require 'vendor/autoload.php';

function main(): Main {
	static $object;
	return empty( $object ) ? $object = new Main( __DIR__, plugin_dir_url( __FILE__ ) ) : $object;
}

main();