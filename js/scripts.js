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
jQuery( function( $ ) {
	var $publicList = $( '.ig-idea-list' );

	function addVote() {
		var $this = $( this );
		var $form = $this.parents( '.ig-voting-form' );

		$form.addClass( 'updating' );
		vote( $form, 'add' );
	}

	function removeVote() {
		var $this = $( this );
		var $form = $this.parents( '.ig-voting-form' );

		$form.removeClass( 'voted' );
		$form.addClass( 'updating' );
		vote( $form, 'remove' );
	}

	function voteUpdate( response ) {
		var ideaId      = response.data.idea_id;
		var userVoted   = response.data.user_voted;
		var voteCount   = response.data.votes;
		var $votingForm = $publicList.find( '.ig-voting-form[data-idea-id="' + ideaId + '"]' );

		if ( ! $votingForm.length ) {
			return;
		}

		$votingForm.removeClass( 'updating' );

		if ( userVoted ) {
			$votingForm.addClass( 'voted' );
		}

		$votingForm.find( '.ig-voting-form__votes' ).html( voteCount );
	}

	function vote( $form, action ) {
		var request = {
			'action':  'idea_garden.idea_vote',
			'mode':    action,
			'idea_id': $form.data( 'ideaId' ),
			'user_id': $form.data( 'userId' ),
			'check':   $form.data( 'check' )
		};

		$.post(
			ideaGarden.ajaxUrl,
			request,
			voteUpdate
		);
	}

	$publicList.on( 'click', '.ig-voting-form__vote', addVote );
	$publicList.on( 'click', '.ig-voting-form__remove', removeVote );
} );