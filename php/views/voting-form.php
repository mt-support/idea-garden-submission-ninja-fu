<?php
/**
 * @var int   $idea_id
 * @var bool  $user_voted
 * @var Votes $votes
 */

namespace Modern_Tribe\Idea_Garden\Ninja_Fu;
?>

<div
        class="voting-form <?php echo $votes->is_supporter( get_current_user_id() ) ? 'voted' : ''; ?>"
        data-idea-id="<?php echo esc_attr( $idea_id ); ?>"
        data-user-id="<?php echo esc_attr( get_current_user_id() ); ?>"
        data-user-voted="<?php echo $votes->is_supporter( get_current_user_id() ) ? '1' : '0'; ?>"
        data-check="<?php echo esc_attr( wp_create_nonce( "vote-$idea_id" ) ); ?>"
>
    <span class="num-votes"> <?php echo esc_html( $votes->supporter_count() ); ?> </span>
	<span class="remove-vote"> Remove my vote </span>
    <span class="add-vote"> Vote for this! </span>
    <span class="updating">Updating&hellip;</span>
    <span class="logged-out">If you want to vote, please login first!</span>
</div>
