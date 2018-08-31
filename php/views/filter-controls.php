<?php
namespace Modern_Tribe\Idea_Garden;

/**
 * @var Public_List $helper
 */

$product_field = Ninja_Forms::get_field_object( $helper->get_form(), 'products' );

$product_options = $product_field
    ? Ninja_Forms::get_field_options( $product_field )
    : [];
?>

<input id="panel-1" class="ig-accordion__panel" type="checkbox" name="panel" />
	<label class="ig-accordion__label" for="panel-1">Filters</label>
	<svg class="ig-accordion__icon" data-icon="chevron" data-containerTransform="translate(0 12)" data-width="null" data-height="null" xmlns="http://www.w3.org/2000/svg" version="1.1" viewBox="0 0 128 128">
		<path d="M0 .688v22.625l58.344 58.344 5.656 5.656 5.656-5.656 58.344-58.344v-22.625l-64 64-64-64z" transform="translate(0 12)" />
	</svg>

  <div class="ig-accordion__content">
    <nav class="ig-filters">
        <select class="ig-filters__filter">
            <option value="" selected="selected">All Products</option>
            <option value="The Events Calendar"></option>
            <option value="">Events Calendar PRO</option>
            <option value="">Event Tickets</option>
            <option value="">Event Tickets Plus</option>
            <option value="">Community Events</option>
            <option value="">Community Tickets</option>
            <option value="">Event Aggregator</option>
            <option value="">Filter Bar</option>
            <option value="">Eventbrite Tickets</option>
        </select>

        <select class="ig-filters__filter">
            <option value="" selected="selected">All Statuses</option>
            <option value="">Planned</option>
            <option value="">Started</option>
            <option value="">In Development</option>
            <option value="">In Testing</option>
            <option value="">Completed</option>
            <option value="">Backlog</option>
        </select>

        <select class="ig-filters__filter">
            <option value="" disabled="disabled" selected="selected">Sort By</option>
            <option value="">Date</option>
            <option value="">Idea Status</option>
            <option value="">Votes</option>
        </select>
    </nav>
  </div>