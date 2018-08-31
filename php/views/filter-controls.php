<?php
namespace Modern_Tribe\Idea_Garden;

/**
 * @var Public_List $helper
 */

$product_field = Ninja_Forms::get_field_object( $this->form, 'product' );
$product_options = Ninja_Forms::get_field_options( $product_field );
?>

<input id="toggle1" type="checkbox" name="toggle" />
<label for="toggle1">Filters</label>

<section id="content1">
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
</section>