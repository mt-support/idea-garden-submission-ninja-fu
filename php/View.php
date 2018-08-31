<?php
namespace Modern_Tribe\Idea_Garden;

class View {
	private $file = '';
	private $vars = [];

	public static function render( $view_file, array $vars = [], $print = false ) {
		$view = (string) new self( $view_file, $vars );

		if ( $print ) {
			print $view;
		}

		return $view;
	}

	public function __construct( string $view_file, array $vars = [] ) {
		$this->file = $view_file;
		$this->vars = $vars;
	}

	public function __toString() {
		$path = main()->dir() . '/php/views/' . $this->file . '.php';

		if ( file_exists( $path ) ) {
			ob_start();
			extract( $this->vars );
			include $path;
			return ob_get_clean();
		}

		return '';
	}
}