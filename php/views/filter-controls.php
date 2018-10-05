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
        <select class="ig-filters__filter" name="ig-product">
            <option value="" selected="selected">All Products</option>
            <?php foreach ( $product_options as $product_name => $product_value ): ?>
                <option value="<?php echo esc_attr( $product_value ); ?>">
                    <?php echo esc_html( $product_name ); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select class="ig-filters__filter" name="ig-status">
            <option value="" selected="selected">All Statuses</option>
            <?php foreach ( Idea_Statuses::STATES as $idea_slug => $idea_properties ): ?>
                <?php if ( $idea_properties['public'] ): ?>
                    <option value="<?php echo esc_attr( $idea_slug ); ?>">
                        <?php echo esc_html( $idea_properties['label'] ); ?>
                    </option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>

        <select class="ig-filters__filter" name="ig-order-sequence">
            <option value="" disabled="disabled" selected="selected">Sort By</option>
            <option value="date">Date</option>
            <option value="status">Idea Status</option>
            <option value="count">Votes</option>
        </select>
    </nav>
  </div>