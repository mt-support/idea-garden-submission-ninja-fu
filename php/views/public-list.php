<?php
/**
 * @var array       $ideas
 * @var Public_List $helper
 */

namespace Modern_Tribe\Idea_Garden\Ninja_Fu;
?>

<section class="idea-garden-public-list">
	<ol>
		<?php foreach ( $ideas as $idea ): ?>
			<li>
				<h1> <?php echo esc_html( $idea->idea ); ?> </h1>
				<p class="elevator"> <?php echo esc_html( $idea->elevator_pitch ); ?> </p>
				<p class="product">
					<?php foreach( (array) $idea->product as $selected_product): ?>
						<span> <?php echo esc_html( $selected_product ); ?> </span>
					<?php endforeach; ?>
				</p>

                <?php do_action( 'idea_garden.ninja_fu.submission_voting_form', $idea ); ?>
			</li>
		<?php endforeach; ?>
	</ol>
 </section>