<?php
/**
* The Template for displaying Reset form
*
* Override template in your theme by copying it to:
* YOUR_THEME_DIR/templates/reset.php
* or YOUR_THEME_DIR/templates/wpu/reset.php
* or YOUR_THEME_DIR/wpu/reset.php
*
* @package 	ST-User/Templates
* @version     1.0
*/
if ( !isset( $in_modal ) ) {
    $in_modal = false;
}
$id = uniqid('f');
?>
<form  id="wpu-reset-password" class="wpu-form wpu-form-reset-password form ui" action="" method="post" >
    <?php if( WP_Users()->settings['form_reset_header'] ) { ?>
    <div class="wpu-form-header">
        <h3><?php echo esc_html( WP_Users()->settings['reset_header_title'] ); ?></h3>
    </div>
    <?php } ?>

    <div class="wpu-form-body">
        <p class="wpu-form-message"><?php _e( 'Please enter your email address. You will receive a link to create a new password.', 'wp-users' ); ?></p>
        <p class="wpu-msg"><?php _e( 'Check your e-mail for the confirmation link.', 'wp-users' ); ?></p>
        <div class="wpu-form-fields">
            <p class="fieldset wpu_input wpu_input_combo">
                <label class="wpu-email" for="reset-email<?php echo $id; ?>"><?php _e('User name or E-mail', 'wp-users' ); ?></label>
                <input name="wp_users_login" class="full-width has-padding has-border" id="reset-email<?php echo $id; ?>" type="text" placeholder="<?php echo esc_attr__( 'User name or E-mail', 'wp-users'); ?>">
                <span class="wpu-error-msg"></span>
            </p>
            <p class="fieldset">
                <input class="<?php echo esc_attr( apply_filters( 'wp_users_form_submit_btn_class', 'reset-submit button btn' ) ); ?>" data-loading-text="<?php echo esc_attr__( 'Loading...', 'wp-users' ); ?>" type="submit" value="<?php echo esc_attr__( 'Submit', 'wp-users' ); ?>">
            </p>
        </div>
    </div>
    <div class="wpu-form-footer">
        <p><?php printf( __( 'Remember your password ? <a class="wpu-back-to-login" href="%1$s">Login</a>', 'wp-users' ), wp_login_url() ); ?></p>
    </div>
</form>