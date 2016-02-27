<?php
/**
 * The Template for displaying Register form
 *
 * Override template in your theme by copying it to:
 * YOUR_THEME_DIR/templates/register.php
 * or YOUR_THEME_DIR/templates/wp-users/register.php
 * or YOUR_THEME_DIR/wp-users/register.php
 *
 * @package 	ST-User/Templates
 * @version     1.0
 */
//global $custom_data;
//var_dump($custom_data, $a, $in_modal);
if ( !isset( $in_modal ) ) {
    $in_modal = false;
}
?>
<div id="st-signup" class="st-form-w"> <!-- sign up form -->
    <?php if ( !$in_modal ) { ?>
        <h3><?php _e( 'Singup', 'wp-users' ); ?></h3>
    <?php } ?>
    <form class="st-form st-register-form<?php echo $in_modal ? ' in-st-modal' : ''; ?>"  action="<?php echo site_url('/'); ?>" method="post">
        <p class="wp-users-msg">
            <?php echo sprintf( __( 'Registration complete ! <a class="st-login-link" href="%1$s" title="Login">Click here to login</a> ', 'wp-users' ), apply_filters( 'st_login_url', '#' ) ); ?>
        </p>
        <div class="form-fields">
            <p class="fieldset wp-usersname">
                <label class="image-replace wp-usersname" for="signup-username"><?php _e( 'Username', 'wp-users' ) ?></label>
                <input name="st_signup_username" class="full-width has-padding has-border" id="signup-username" type="text" placeholder="<?php echo esc_attr__('Username', 'wp-users'); ?>">
                <span class="st-error-message"></span>
            </p>

            <p class="fieldset st-email">
                <label class="image-replace st-email" for="signup-email"><?php _e( 'E-mail', 'wp-users' ); ?></label>
                <input name="st_signup_email" class="full-width has-padding has-border" id="signup-email" type="email" placeholder="<?php echo esc_attr__('E-mail','wp-users'); ?>">
                <span class="st-error-message"></span>
            </p>

            <p class="fieldset st-password">
                <label class="image-replace st-password" for="signup-password"><?php _e('Password','wp-users') ?></label>
                <input name="st_signup_password" class="full-width has-padding has-border" id="signup-password" type="password"  placeholder="<?php echo esc_attr__('Password', 'wp-users'); ?>">
                <a href="#" class="hide-password"><?php _e('Show','wp-users') ?></a>
                <span class="st-error-message"></span>
            </p>
            <?php
            // Filter  to show term link
            if ( apply_filters( 'wp_users_register_show_term_link' , true ) ) {
            ?>
            <p class="fieldset accept-terms">
                <label><input name="st_accept_terms" value="i-agree" type="checkbox" id="st-accept-terms"> <?php echo sprintf( __( 'I agree to the <a href="%s" target="_blank">Terms and Conditions</a>', 'wp-users' ),  apply_filters( 'wp_users_term_link' , '#' ) ); ?></label>

                <span class="st-error-message"><?php _e('You must agree our Terms and Conditions to continue', 'wp-users'); ?></span>
            </p>
            <?php } ?>
            <p class="fieldset">
                <input class="st-submit full-width has-padding"  type="submit" data-loading-text="<?php echo esc_attr__( 'Loading...', 'wp-users' ); ?>" value="<?php echo esc_attr__( 'Create account', 'wp-users' ); ?>">
            </p>
        </div>
    </form>
    <?php if ( ! $in_modal ) { ?>
    <p class="st-form-bottom-message"><a class="st-back-to-login" href="<?php echo wp_login_url(); ?>"><?php _e( 'Back to log-in','wp-users' ); ?></a></p>
    <?php } ?>
    <!-- <a href="#0" class="st-close-form">Close</a> -->
</div> <!-- st-signup -->