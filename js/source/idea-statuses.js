jQuery( function( $ ) {
	var $status_control = $( '.nf-sub-info-status' );
	var $status_label   = $status_control.find( 'strong' );
	var $status_picker  = $status_control.find( 'select' );
	var waiting_on_save = false;

	/**
	 * By default the label contains the post status slug
	 * (ugly!) not the true status label, let's fix that.
	 */
	function set_label() {
		var $option = $status_picker.find( ':selected' );

		if ( ! $option.length ) {
			return;
		}

		var text = $option.html();

		if ( waiting_on_save ) {
			text = '<em>' + text + '</em>';
		}

		$status_label.html( text );
	}

	function show_picker() {
		$status_label.hide();
		$status_picker.show();
	}

	function hide_picker() {
		$status_label.show();
		$status_picker.hide();
		waiting_on_save = true;
		set_label();
	}

	function init() {
		set_label();
		$status_picker.hide();
		$status_label.click( show_picker );
		$status_picker.change( hide_picker );
	}

	init();
} );