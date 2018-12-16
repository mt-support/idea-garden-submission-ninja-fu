<?php
namespace Modern_Tribe\Idea_Garden\Commands;

use Exception;
use Faker\Factory as Fake_Content;
use Modern_Tribe\Idea_Garden;
use Modern_Tribe\Idea_Garden\Votes;
use WP_CLI;
use WP_User_Query;

/**
 * Registers "wp ideas generate" to quickly build sample data for testing purposes.
 */
class Main {
	public function __construct() {
		if ( ! class_exists( 'WP_CLI' ) ) {
			return;
		}

		try {
			WP_CLI::add_command( 'ideas generate', new Generate );
			WP_CLI::add_command( 'ideas delete', new Delete );
		}
		catch ( Exception $e ) {}
	}
}