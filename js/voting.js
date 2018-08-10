jQuery( function( $ ) {
	var $publicList = $( '.ig-idea-list' );

	function addVote() {
		var $this = $( this );
		var $form = $this.parents( '.voting-form' );

		$form.addClass( 'updating' );
		vote( $form, 'add' );
	}

	function removeVote() {
		var $this = $( this );
		var $form = $this.parents( '.voting-form' );

		$form.removeClass( 'voted' );
		$form.addClass( 'updating' );
		vote( $form, 'remove' );
	}

	function voteUpdate( response ) {
		var ideaId      = response.data.idea_id;
		var userVoted   = response.data.user_voted;
		var voteCount   = response.data.votes;
		var $votingForm = $publicList.find( '.voting-form[data-idea-id="' + ideaId + '"]' );

		if ( ! $votingForm.length ) {
			return;
		}

		$votingForm.removeClass( 'updating' );

		if ( userVoted ) {
			$votingForm.addClass( 'voted' );
		}

		$votingForm.find( '.num-votes' ).html( voteCount );
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

	$publicList.on( 'click', '.add-vote', addVote );
	$publicList.on( 'click', '.remove-vote', removeVote );
} );