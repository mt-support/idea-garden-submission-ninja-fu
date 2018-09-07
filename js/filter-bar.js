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