jQuery( function( $ ) {
	var $filterBar       = $( 'nav.ig-filters' );
	var $ideaList        = $( 'section.ig-idea-list' );
	var $productSelector = $filterBar.find( '[name="ig-product"]' );
	var $statusSelector  = $filterBar.find( '[name="ig-status"]' );
	var $orderSelector   = $filterBar.find( '[name="ig-order-sequence"]' );
	var updateDelay      = false;
	var updateInProgress = false;

	/**
	 * Wait until the users changes on of our filters ... then initiate a
	 * slightly delayed update to our list.
	 */
	$filterBar.on( 'change', function() {
		$filterBar.addClass( 'ig-waiting' );
		initDelayedUpdate();
	} );

	/**
	 * Wait 0.5 seconds then update (we wait in case the user modifies
	 * one of the other filters).
	 */
	function initDelayedUpdate() {
		// If an update is in progress, do nothing until it completes
		if ( updateInProgress ) {
			return;
		}

		// If the 'update delay' is already set, reset it
		if ( updateDelay ) {
			console.log('clearing timeout');
			clearTimeout( updateDelay );
			updateDelay = false;
		}

		updateDelay = setTimeout( updateList, 2000 );
	}

	/**
	 * Go fetch the revised list of idea statuses.
	 */
	function updateList() {
		$filterBar.removeClass( 'ig-waiting' );
		$filterBar.addClass( 'ig-updating' );

		var request = {
			'action':            'update_idea_garden_public_list',
			'form_id':           ideaGardenFilterBar.formId,
			'ig-product':        $productSelector.find( 'option:selected' ).val(),
			'ig-status':         $statusSelector.find( 'option:selected' ).val(),
			'ig-order-sequence': $orderSelector.find( 'option:selected' ).val()
		};

		$.ajax( {
			type:     'POST',
			dataType: 'json',
			url:      ideaGardenFilterBar.url,
			data:     request,
			error:    updateRequestFailed,
			success:  renderUpdatedList
		} );

		updateInProgress = true;
		console.log( 'Sent request' );
	}

	/**
	 * Update the list with beautiful fresh idea data.
	 *
	 * @param response
	 */
	function renderUpdatedList( response ) {
		requestComplete();
		$ideaList.replaceWith( response.data.html );
		$ideaList = $( 'section.ig-idea-list' );
	}

	/**
	 * If the update request bombed.
	 */
	function updateRequestFailed() {
		requestComplete();
	}

	/**
	 * Reset state once our response comes back from the server.
	 */
	function requestComplete() {
		updateInProgress = false;
		$filterBar.removeClass( 'ig-updating' );
	}
} );
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
	var $form        = $( '.ig-idea-submission-form form' );
	var $categories  = $form.find( 'input[name="idea-categories[]"]' );
	var $description = $form.find( 'textarea[name="idea-description"]' );
	var $moreDetail  = $( '#ig-idea-submission-form__more-detail-needed' );
	var $title       = $form.find( 'input[name="idea-title"]' );

	var submissionInProgress = false;

	function wordCount( text ) {
		var count = 0;
		var tokens = text.split( ' ' );

		// Filter out empty strings (needed because the split operation
		// will turn a triple space into two empty strings, etc)
		for ( i = 0; i < tokens.length; i++ ) {
			if ( tokens[ i ].length ) {
				count++;
			}
		}

		return count;
	}

	function countCategoriesSelected() {
		return $categories.filter( ':checked' ).length;
	}

	function conditionallyHighlight( $element, highlight ) {
		if ( highlight ) {
			$element.addClass( 'highlight' );
		} else {
			$element.removeClass( 'highlight' );
		}
	}

	function checkIfReadyToSubmit() {
		var ready = false;

		var titleWordCount = wordCount( $title.val() );
		var descriptionWordCount = wordCount( $description.val() );
		var totalWordCount = titleWordCount + descriptionWordCount;
		var categoriesSelected = countCategoriesSelected();

		// We want a title, a description and at least 12 words across each
		// ...at least one category must also be selected
		if ( titleWordCount && descriptionWordCount && totalWordCount > 11 && categoriesSelected ) {
			ready = true;
		}

		/**
		 * @todo Improve feedback if fields are incomplete/invalid, etc
		 *
		 *       The conditionallyHighlight() stuff is crude and rather
		 *       than develop it further we should wait on some strategy
		 *       being brought to bear.
		 */
		conditionallyHighlight( $categories,  ! categoriesSelected );
		conditionallyHighlight( $moreDetail,  totalWordCount < 6 );
		conditionallyHighlight( $title,       ! titleWordCount );

		return ready;
	}

	function onSubmission( event ) {
		event.preventDefault();

		if ( ! checkIfReadyToSubmit() || submissionInProgress ) {
			return;
		}

		submitFormData();
	}

	function getCheckedCategories() {
		var categories = [];

		$categories.filter( ':checked' ).each( function() {
			categories.push( $( this ).val() );
		} );

		return categories;
	}

	function submitFormData() {
		var data = {
			categories:  getCheckedCategories(),
			check:       ideaGardenSubmissions.safety,
			description: $description.val(),
			title:       $title.val()
		};

		$.ajax( {
			action:   'submit_idea',
			data:     data,
			dataType: 'json',
			error:    badSubmission,
			success:  goodSubmission,
			type:     'POST',
			url:      ideaGardenSubmissions.ajaxUrl
		} );

		submissionInProgress = true;
	}

	function badSubmission( response ) {
		submissionInProgress = false;
		// @todo Flag that the submission had gnarly attributes in need of review
	}

	function goodSubmission( response ) {
		submissionInProgress = false;
		// @todo flaf that the submission was received loud and lcear
	}

	$form.on( 'submit', onSubmission );
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