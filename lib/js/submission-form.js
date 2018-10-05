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
