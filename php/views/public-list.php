<?php
/**
 * @var array       $ideas
 * @var Public_List $helper
 */

namespace Modern_Tribe\Idea_Garden\Ninja_Fu;
?>

<section class="ig-idea-list">
<?php foreach ( $ideas as $idea ): ?>
	<div class="ig-idea-list__card">
		<div class="ig-idea-list__header">
			<?php echo get_avatar(); ?>
			<div class="ig-idea-list__title">
				<h1> <?php echo esc_html( $idea->idea ); ?> </h1>
			</div>
			<div class="ig-idea-list__meta">
				<span class="ig-idea-list__status">Open</span>
				<?php foreach( (array) $idea->product as $selected_product): ?>
					<span class="ig-idea-list__product"> <?php echo esc_html( $selected_product ); ?> </span>
				<?php endforeach; ?>
				<span class="ig-idea-list__date"><?php echo get_the_date( $d = 'M j, Y' ); ?></span>
			</div>
		</div>
		<div class="ig-idea-list__content">
			<p class="elevator"> <?php echo esc_html( $idea->elevator_pitch ); ?> </p>
		</div>

	<?php do_action( 'idea_garden.ninja_fu.submission_voting_form', $idea ); ?>
</div>
		<?php endforeach; ?>
 </section>