<?php
namespace Modern_Tribe\Idea_Garden;

/**
 * @var Public_List $helper
 */

// If this condition is met we don't need pagination or something bizarro has
// happened, so let's bail
if ( $helper->get_max_pages() < 2 || $helper->get_current_page() < 1 ) {
	return;
}

for ( $i = 1; $i <= $helper->get_max_pages(); $i++ ) {
	$url = esc_url( add_query_arg( 'ig-page', $i ) );
	$classes = $i === $helper->get_current_page() ? 'active' : '';
	echo '<a href="' . esc_attr( $url ) . '" class="' . esc_attr( $classes ) . '">' . $i . '</a> ';
}