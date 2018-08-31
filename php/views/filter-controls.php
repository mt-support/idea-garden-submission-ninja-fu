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

<input id="toggle1" type="checkbox" name="toggle" />
<label for="toggle1">Filters</label>

<section id="content1">
    <nav class="ig-filters">
        <select class="ig-filters__filter">
            <option value="" selected="selected">All Products</option>
            <?php foreach ( $product_options as $name => $value ): ?>
                <option value="<?php echo esc_attr( $value ); ?>">
                    <?php echo esc_html( $name ); ?>
                </option>
            <?php endforeach; ?>
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