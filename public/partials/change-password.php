<?php
/**
 * The Template for displaying Lost Password form
 *
 * Override template in your theme by copying it to:
 * YOUR_THEME_DIR/templates/change-password.php
 * or YOUR_THEME_DIR/templates/st-user/change-password.php
 * or YOUR_THEME_DIR/st-user/change-password.php
 *
 * @package 	ST-User/Templates
 * @version     1.0
 */

if ( !isset( $in_modal ) ) {
    $in_modal = false;
}

?>
<div id="st-change-password" class="st-form-w">
    <?php if ( ! $in_modal ) { ?>
        <h3><?php _e( 'Change your password', 'st-user' ); ?></h3>
    <?php } ?>
    <form class="st-form st-form-change-password<?php echo $in_modal ? ' in-st-modal' : ''; ?>" action="<?php echo site_url('/'); ?>" method="post" >
        <p class="st-form-message"><?php _e( 'Change your password', 'st-user' ); ?></p>
        <p class="st-user-msg"><?php echo sprintf( __( 'Your password has been reset. <a href="%1$s" class="st-login-link">Click here to login</a>', 'st-user'), wp_login_url()  ); ?></p>
        <p class="st-user-msg st-errors-msg"></p>
        <div class="form-fields">
            <p class="fieldset st-pwd pass1">
                <label class="image-replace st-password" for="signin-password"><?php _e( 'New Password', 'st-user'); ?></label>
                <input name="st_pwd" class="input full-width has-padding has-border" id="signin-password" type="text"  placeholder="<?php echo esc_attr__( 'New Password', 'st-user' ) ; ?>">
                <a href="#0" class="hide-password"><?php _e('Hide','st-user') ?></a>
                <span class="st-error-message"></span>
            </p>
            <p class="fieldset st-pwd pass2">
                <label class="image-replace st-password" for="signin-password"><?php _e( 'Comfirm New Password', 'st-user' ); ?></label>
                <input name="st_pwd2" class="input full-width has-padding has-border" id="signin-password" type="text"  placeholder="<?php echo esc_attr__( 'Confirm New Password', 'st-user' ) ; ?>">
                <a href="#0" class="hide-password"><?php _e( 'Hide', 'st-user' ) ?></a>
                <span class="st-error-message"></span>
            </p>
            <p class="fieldset">
                <input class="full-width has-padding st-submit" type="submit" data-loading-text="<?php echo esc_attr__( 'Loading...', 'st-user' ); ?>" value="<?php echo esc_attr__( 'Reset password','st-user' ); ?>">
                <?php foreach ( $_GET as $k => $v ) {
                    ?>
                    <input type="hidden" name="<?php echo esc_attr( $k ); ?>" value="<?php echo esc_attr( (string) $v ); ?>">
                <?php } ?>
            </p>
        </div>
    </form>
    <p class="st-form-bottom-message"><a class="st-back-to-login" href="#"><?php _e( 'Back to log-in', 'st-user' ); ?></a></p>
</div> <!-- st-reset-password -->