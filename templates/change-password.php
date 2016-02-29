<?php
/**
 * The Template for displaying Lost Password form
 *
 * Override template in your theme by copying it to:
 * YOUR_THEME_DIR/templates/change-password.php
 * or YOUR_THEME_DIR/templates/wpu/change-password.php
 * or YOUR_THEME_DIR/wpu/change-password.php
 *
 * @package 	ST-User/Templates
 * @version     1.0
 */

if ( !isset( $in_modal ) ) {
    $in_modal = false;
}
$id =  uniqid('f');
?>

<form  id="st-change-password" class="wpu-form wpu-form-change-password<?php echo $in_modal ? ' in-st-modal' : ''; ?> form ui" action="<?php echo site_url('/'); ?>" method="post" >

    <?php if( WP_Users()->settings['form_change_pass_header'] ) { ?>
    <div class="wpu-form-header">
        <h3><?php echo esc_html( WP_Users()->settings['change_pass_header_title'] ); ?></h3>
    </div>
    <?php } ?>

    <div class="s-form-body">
        <p class="wpu-msg"><?php echo sprintf( __( 'Your password has been reset. <a href="%1$s" class="wpu-login-link">Click here to login</a>', 'wp-users'), wp_login_url()  ); ?></p>
        <p class="wpu-msg st-errors-msg"></p>
        <div class="wpu-form-fields">
            <p class="fieldset wpu_input st-pwd pass1">
                <label class="image-replace st-password" for="signin-password<?php echo $id; ?>"><?php _e( 'New Password', 'wp-users'); ?></label>
                <input name="wpu_pwd" class="input full-width has-padding has-border" id="signin-password<?php echo $id; ?>" type="text"  placeholder="<?php echo esc_attr__( 'New Password', 'wp-users' ) ; ?>">
                <a href="#0" class="hide-password"><?php _e('Hide','wp-users') ?></a>
                <span class="st-error-message"></span>
            </p>
            <p class="fieldset wpu_input st-pwd pass2">
                <label class="image-replace st-password" for="signin-password<?php echo $id; ?>"><?php _e( 'Comfirm New Password', 'wp-users' ); ?></label>
                <input name="wpu_pwd2" class="input full-width has-padding has-border" id="signin-password<?php echo $id; ?>" type="text"  placeholder="<?php echo esc_attr__( 'Confirm New Password', 'wp-users' ) ; ?>">
                <a href="#0" class="hide-password"><?php _e( 'Hide', 'wp-users' ) ?></a>
                <span class="st-error-message"></span>
            </p>
            <p class="fieldset">
                <input class="<?php echo esc_attr( apply_filters( 'wp_users_form_submit_btn_class', 'change-pwd-submit button btn' ) ); ?>" type="submit" data-loading-text="<?php echo esc_attr__( 'Loading...', 'wp-users' ); ?>" value="<?php echo esc_attr__( 'Reset password','wp-users' ); ?>">
                <?php foreach ( $_GET as $k => $v ) {
                    ?>
                    <input type="hidden" name="<?php echo esc_attr( $k ); ?>" value="<?php echo esc_attr( (string) $v ); ?>">
                <?php } ?>
            </p>
        </div>
    </div>
    <div class="wpu-form-footer">
        <p><?php printf( __( 'Remember your password ? <a class="st-back-to-login" href="%1$s">Login</a>', 'wp-users' ), wp_login_url() ); ?></p>
    </div>
</form>

