<?php
/**
 * @var array       $submissions
 * @var Public_List $helper
 */
namespace Modern_Tribe\Idea_Garden\Ninja_Fu;
?>

<section class="idea-garden-public-list">
	<ol>
		<?php foreach ( $submissions as $submission ): ?>
			<li>
				<h1> <?php echo esc_html( $submission->idea ); ?> </h1>
				<p class="elevator"> <?php echo esc_html( $submission->elevator_pitch ); ?> </p>
				<p class="product">
					<?php foreach( $submission->product as $selected_product): ?>
						<span> <?php echo esc_html( $selected_product ); ?> </span>
					<?php endforeach; ?>
				</p>
			</li>
		<?php endforeach; ?>
	</ol>
 </section>