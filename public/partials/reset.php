<?php
/**
* The Template for displaying Reset form
*
* Override template in your theme by copying it to:
* YOUR_THEME_DIR/templates/reset.php
* or YOUR_THEME_DIR/templates/wp-users/reset.php
* or YOUR_THEME_DIR/wp-users/reset.php
*
* @package 	ST-User/Templates
* @version     1.0
*/
if ( !isset( $in_modal ) ) {
    $in_modal = false;
}
?>
<div id="st-reset-password" class="st-form-w">
    <?php if ( !$in_modal ) { ?>
        <h3><?php _e('Lost password', 'wp-users'); ?></h3>
    <?php } ?>
    <form class="st-form st-form-reset-password" action="" method="post" >
        <p class="st-form-message"><?php _e( 'Lost your password? Please enter your email address. You will receive a link to create a new password.', 'wp-users' ); ?></p>
        <p class="wp-users-msg"><?php _e( 'Check your e-mail for the confirmation link.', 'wp-users' ); ?></p>
        <div class="form-fields">
            <p class="fieldset">
                <label class="image-replace st-email" for="reset-email"><?php _e('User name or E-mail', 'wp-users' ); ?></label>
                <input name="wp_users_login" class="full-width has-padding has-border" id="reset-email" type="text" placeholder="<?php echo esc_attr__( 'User name or E-mail', 'wp-users'); ?>">
                <span class="st-error-message"></span>
            </p>

            <p class="fieldset">
                <input class="full-width has-padding st-submit" data-loading-text="<?php echo esc_attr__( 'Loading...', 'wp-users' ); ?>" type="submit" value="<?php echo esc_attr__( 'Submit', 'wp-users' ); ?>">
            </p>
        </div>
    </form>

    <p class="st-form-bottom-message"><a class="st-back-to-login" href="<?php echo wp_login_url(); ?>"><?php _e( 'Back to log-in','wp-users' ); ?></a></p>
</div> <!-- st-reset-password -->