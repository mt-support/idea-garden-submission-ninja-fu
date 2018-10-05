<?php
namespace Modern_Tribe\Idea_Garden;

use WP_Term;

/**
 * @var WP_Term[] $categories
 */
?>

<section class="ig-idea-submission-form">
	<form method="post">
        <p id="ig-idea-submission-form__more-detail-needed">
            <?php esc_html_e( 'More detail needed! Please add more detail.', 'idea-garden' ); ?>
        </p>

		<label for="ig-idea-submission__idea">
			<?php _e( 'Idea:', 'idea-garden' ); ?>
		</label>
		<input type="text" id="ig-idea-submission__idea" name="idea-title" value="" />

		<?php if ( ! empty( $categories ) ): ?>
			<label> <?php _e( 'Categories:', 'idea-garden' ); ?> </label>
		<?php endif; ?>

		<ol>
			<?php foreach ( $categories as $category ): ?>
				<li>
					<input
						type="checkbox"
						name="idea-categories[]"
						id="ig-idea-submission__category__<?php echo esc_attr( sanitize_title( $category->slug ) )?>"
						value="<?php echo esc_attr( $category->term_id ); ?>"
					/>
					<label for="ig-idea-submission__category__<?php echo esc_attr( sanitize_title( $category->slug ) )?>">
						<?php echo esc_html( $category->name ); ?>
					</label>
				</li>
			<?php endforeach; ?>
		</ol>

		<label for="ig-idea-submission__description">
			<?php _e( 'Description:', 'idea-garden' ); ?>
		</label>
		<textarea
			id="ig-idea-submission__description"
            name="idea-description"
			cols="60"
			rows="10"
		></textarea>

		<input type="submit" value="<?php esc_attr_e( 'Submit', 'idea-garden' ); ?>" />
	</form>
</section>