<?php
namespace Modern_Tribe\Idea_Garden;
?>
<div class="ig-accordion">

	<input id="panel-1" class="ig-accordion__panel" type="checkbox" name="panel" />
	<div class="ig-accordion__header">
		<svg class="ig-accordion__icon" data-icon="settings" data-width="null" data-height="null" xmlns="http://www.w3.org/2000/svg" version="1.1" width="16" height="16" viewBox="0 0 16 16">
			<path d="M1.781 0a1 1 0 0 0-.781 1v1.188c.312-.113.649-.188 1-.188.355 0 .684.074 1 .188v-1.188a1 1 0 0 0-.688-1h-.531zm6 0a1 1 0 0 0-.781 1v7.188c.316-.114.645-.188 1-.188s.684.074 1 .188v-7.188a1 1 0 0 0-.688-1h-.531zm6 0a1 1 0 0 0-.781 1v1.188c.316-.114.645-.188 1-.188.351 0 .688.074 1 .188v-1.188a1 1 0 0 0-.688-1h-.531zm-11.781 3c-1.105 0-2 .895-2 2s.895 2 2 2 2-.895 2-2-.895-2-2-2zm12 0c-1.105 0-2 .895-2 2s.895 2 2 2 2-.895 2-2-.895-2-2-2zm-13 4.813v7.188a1 1 0 0 0 .813 1h.406a1 1 0 0 0 .781-1v-7.188c-.316.114-.645.188-1 .188-.351 0-.688-.074-1-.188zm12 0v7.188a1 1 0 0 0 .813 1h.406a1 1 0 0 0 .781-1v-7.188c-.312.113-.649.188-1 .188-.355 0-.684-.074-1-.188zm-5 1.188c-1.105 0-2 .895-2 2s.895 2 2 2 2-.895 2-2-.895-2-2-2zm-1 4.813v1.188a1 1 0 0 0 .813 1h.406a1 1 0 0 0 .781-1v-1.188c-.316.114-.645.188-1 .188s-.684-.074-1-.188z"/>
		</svg>
		<label class="ig-accordion__label" for="panel-1">Filters</label>
	</div>

	<div class="ig-accordion__content">

		<nav class="ig-filters">
			<select class="ig-filters__filter">
				<option value="" selected="selected">All Products</option>
				<option value="">The Events Calendar</option>
				<option value="">Events Calendar PRO</option>
				<option value="">Event Tickets</option>
				<option value="">Event Tickets Plus</option>
				<option value="">Community Events</option>
				<option value="">Community Tickets</option>
				<option value="">Event Aggregator</option>
				<option value="">Filter Bar</option>
				<option value="">Eventbrite Tickets</option>
			</select>

			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
				<path d="M0 0h24v24H0z" fill="none"/>
				<path d="M12 5.83L15.17 9l1.41-1.41L12 3 7.41 7.59 8.83 9 12 5.83zm0 12.34L8.83 15l-1.41 1.41L12 21l4.59-4.59L15.17 15 12 18.17z"/>
			</svg>

			<select class="ig-filters__filter">
				<option value="" selected="selected">All Statuses</option>
				<option value="">Planned</option>
				<option value="">Started</option>
				<option value="">In Development</option>
				<option value="">In Testing</option>
				<option value="">Completed</option>
				<option value="">Backlog</option>
			</select>

			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
				<path d="M0 0h24v24H0z" fill="none"/>
				<path d="M12 5.83L15.17 9l1.41-1.41L12 3 7.41 7.59 8.83 9 12 5.83zm0 12.34L8.83 15l-1.41 1.41L12 21l4.59-4.59L15.17 15 12 18.17z"/>
			</svg>

			<select class="ig-filters__filter">
				<option value="" disabled="disabled" selected="selected">Sort By</option>
				<option value="">Date</option>
				<option value="">Idea Status</option>
				<option value="">Votes</option>
			</select>

			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
				<path d="M0 0h24v24H0z" fill="none"/>
				<path d="M12 5.83L15.17 9l1.41-1.41L12 3 7.41 7.59 8.83 9 12 5.83zm0 12.34L8.83 15l-1.41 1.41L12 21l4.59-4.59L15.17 15 12 18.17z"/>
			</svg>
		</nav>

	</div>
</div>

<section class="ig-idea-list">

<?php foreach ( $ideas as $idea ): ?>
	<div class="ig-idea-list__card">
			<div class="ig-idea-list__title">
					<h1> <?php echo esc_html( $idea->idea ); ?> </h1>

					<span class="ig-idea-list__product">
						<?php echo esc_html( $selected_product ); ?> 
					</span>
			</div>

			<div class="ig-idea-list__footer">
				<?php echo '<?xml version="1.0" encoding="UTF-8"?>' ?>
				<svg viewBox="0 0 16 14" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
					<title>Heart</title>
					<g id="heart" fill="#FE7B8B" transform="translate(-1.000000, -3.000000)">
							<path d="M5,3 C3.896,3 2.91,3.433 2.187,4.156 C1.463,4.88 1,5.895 0.999,7 C0.999,8.105 1.463,9.088 2.187,9.813 L9,16.657 L15.844,9.813 C16.568,9.089 17,8.105 17,7 C17,5.895 16.568,4.88 15.844,4.156 L15.813,4.156 C15.089,3.432 14.104,3 13,3 C11.896,3 10.911,3.433 10.187,4.156 C9.463,4.88 9.115,5.723 8.999,6 C8.879,5.723 8.536,4.88 7.811,4.156 C7.087,3.432 6.103,3 4.998,3 L5,3 Z" id="Shape"></path>
						</g>
					</svg>
		
					<?php do_action( 'idea_garden.ninja_fu.submission_voting_form', $idea ); ?>

					<span class="ig-idea-list__status ig-idea-list__status--<?php echo get_post_status( $idea->id ) ?>">
						<?php echo esc_html( get_post_status_object( get_post_status( $idea->id ) )->label ); ?>
					</span>
			</div>
		</div>

		<?php endforeach; ?>
	
</section>