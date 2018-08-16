<?php
namespace Modern_Tribe\Idea_Garden\Ninja_Fu;

/**
 * @var array       $ideas
 * @var Public_List $helper
 */
?>

<section class="ig-idea-list">
<?php foreach ( $ideas as $idea ): ?>
	<div class="ig-idea-list__card">
		<div class="ig-idea-list__header">
			<img src="http://design.tri.be/tec-brand/assets/tec-cal-color-simple.png">
			<?php foreach( (array) $idea->product as $selected_product): ?>
				<span class="ig-idea-list__product"> <?php echo esc_html( $selected_product ); ?> </span>
			<?php endforeach; ?>
				<span class="ig-idea-list__status ig-idea-list__status--<?php echo get_post_status( $idea->id ) ?>">
					<?php echo esc_html( get_post_status_object( get_post_status( $idea->id ) )->label ); ?>
				</span>
		</div>
		<div class="ig-idea-list__content">
			<div class="ig-idea-list__title">
				<h1> <?php echo esc_html( $idea->idea ); ?> </h1>
			</div>
			<div class="ig-idea-list__summary">
				<p> <?php echo esc_html( $idea->elevator_pitch ); ?> </p>
			</div>
		</div>
		<div class="ig-idea-list__meta">
			<?php echo '<?xml version="1.0" encoding="UTF-8"?>' ?>
			<svg viewBox="0 0 16 14" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
				<title>Heart</title>
				<g id="heart" fill="#FE7B8B" transform="translate(-1.000000, -3.000000)">
					<path d="M5,3 C3.896,3 2.91,3.433 2.187,4.156 C1.463,4.88 1,5.895 0.999,7 C0.999,8.105 1.463,9.088 2.187,9.813 L9,16.657 L15.844,9.813 C16.568,9.089 17,8.105 17,7 C17,5.895 16.568,4.88 15.844,4.156 L15.813,4.156 C15.089,3.432 14.104,3 13,3 C11.896,3 10.911,3.433 10.187,4.156 C9.463,4.88 9.115,5.723 8.999,6 C8.879,5.723 8.536,4.88 7.811,4.156 C7.087,3.432 6.103,3 4.998,3 L5,3 Z" id="Shape"></path>
				</g>
			</svg>
			<?php do_action( 'idea_garden.ninja_fu.submission_voting_form', $idea ); ?>
			<span class="ig-idea-list__date"> <?php echo get_the_date( $d = 'M j, Y' ); ?> </span>
		</div>

	</div>
	<?php endforeach; ?>

    <div class="ig-pagination">
        <?php echo View::render( 'pagination', [ 'helper' => $helper ] ); ?>
    </div>
 </section>