<?php
namespace Modern_Tribe\Idea_Garden\Exceptions;

use Exception;

class Invalid_Idea_Exception extends Exception {
	public function __construct( $message = null, $code = 0 ) {
		parent::__construct(
			empty( $message ) ?__( 'Invalid reference to an idea post.', 'idea-garden' ) : null,
			empty( $code ) ? 'INVALID_IDEA' : $code
		);
	}
}