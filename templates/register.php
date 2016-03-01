<?php
/**
 * The Template for displaying Register form
 *
 * Override template in your theme by copying it to:
 * YOUR_THEME_DIR/templates/register.php
 * or YOUR_THEME_DIR/templates/wpu/register.php
 * or YOUR_THEME_DIR/wpu/register.php
 *
 * @package 	ST-User/Templates
 * @version     1.0
 */
//global $custom_data;
//var_dump($custom_data, $a, $in_modal);
if ( !isset( $in_modal ) ) {
    $in_modal = false;
}

$id = uniqid('r-');
?>
<form id="<?php esc_attr_e( $in_modal ? 'wpu-signup' : 'wpu-f-signup' ); ?>" class="wpu-form wpu-register-form<?php echo $in_modal ? ' in-wpu-modal' : ''; ?> form ui"  action="<?php echo site_url('/'); ?>" method="post">

    <?php if( WP_Users()->settings['form_register_header'] ) { ?>
    <div class="wpu-form-header">
        <h3><?php echo esc_html( WP_Users()->settings['register_header_title'] ); ?></h3>
    </div>
    <?php } ?>

    <div class="wpu-form-body">
        <p class="wpu-msg wpu-hide">
            <?php echo sprintf( __( 'Registration complete ! <a class="wpu-login-link" href="%1$s" title="Login">Click here to login</a> ', 'wp-users' ), wp_login_url() ); ?>
        </p>
        <div class="wpu-form-fields">
            <?php do_action( 'wp_users_before_register_form' ); ?>
            <p class="fieldset wpu_input wp_usersname">
                <label class="image-replace wpuname" for="signup-username<?php echo $id; ?>"><?php _e( 'Username', 'wp-users' ) ?></label>
                <input name="wpu_signup_username" class="full-width has-padding has-border" id="signup-username<?php echo $id; ?>" type="text" placeholder="<?php echo esc_attr__('Username', 'wp-users'); ?>">
                <span class="wpu-error-msg"></span>
            </p>

            <p class="fieldset wpu_input wpu_email">
                <label class="image-replace wpu-email" for="signup-email<?php echo $id; ?>"><?php _e( 'E-mail', 'wp-users' ); ?></label>
                <input name="wpu_signup_email" class="full-width has-padding has-border" id="signup-email<?php echo $id; ?>" type="text" placeholder="<?php echo esc_attr__('E-mail','wp-users'); ?>">
                <span class="wpu-error-msg"></span>
            </p>

            <p class="fieldset wpu_input wpu_password">
                <label class="image-replace wpu-password" for="signup-password<?php echo $id; ?>"><?php _e('Password','wp-users') ?></label>
                <span class="wpu-pwd-toggle">
                    <input name="wpu_signup_password" class="full-width has-padding has-border" id="signup-password<?php echo $id; ?>" type="password"  placeholder="<?php echo esc_attr__('Password', 'wp-users'); ?>">
                    <a href="#" class="hide-password"><?php esc_attr_e('Show','wp-users') ?></a>
                </span>
                <span class="wpu-error-msg"></span>
            </p>
            <?php do_action( 'wp_after_before_register_form' ); ?>
            <?php
            // Filter  to show term link

            if ( WP_Users()->settings['show_term']  ) {
                ?>
                <div class="fieldset accept_terms <?php echo WP_Users()->settings['term_mgs'] != '' ? 'custom-terms' : ''; ?>">
                    <label>
                        <input name="wpu_accept_terms" value="i-agree" type="checkbox" class="wpu-accept-terms">
                        <?php
                        if ( WP_Users()->settings['term_mgs'] != '' ) {
                            echo '<div class="wpu-term-mgs">'.wp_kses_post( WP_Users()->settings['term_mgs'] ).'</div>';
                        } else {
                            esc_html_e( 'I agree to the Terms and Conditions', 'wp-users' );
                        }
                        ?>
                    </label>
                </div>
            <?php } ?>
            <p class="fieldset">
                <input class="<?php echo esc_attr( apply_filters( 'wp_users_form_submit_btn_class', 'signup-submit button btn' ) ); ?>"  type="submit" data-loading-text="<?php echo esc_attr__( 'Loading...', 'wp-users' ); ?>" value="<?php echo esc_attr__( 'Sign Up', 'wp-users' ); ?>">
            </p>
        </div>
    </div>

    <div class="wpu-form-footer">
        <p><?php
            printf( __( 'Already have an account ? <a class="wpu-back-to-login" href="%1$s">Login</a>', 'wp-users' ), wp_login_url() );
            ?></p>
    </div>
</form>
