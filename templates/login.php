<?php
/**
 * The Template for displaying login form
 *
 * Override template in your theme by copying it to:
 * YOUR_THEME_DIR/templates/login.php
 * or YOUR_THEME_DIR/templates/wpu/login.php
 * or YOUR_THEME_DIR/wpu/login.php
 *
 * @package 	ST-User/Templates
 * @version     1.0
 */
if ( !isset( $in_modal ) ) {
    $in_modal = false;
}

if ( ! isset ( $login_redirect_url ) ) {
    $login_redirect_url = '';
}

$id = uniqid('f');

if ( ! is_user_logged_in() ) {
?>

<form id="<?php esc_attr_e( $in_modal ? 'wpu-login' : 'wpu-f-login' ); ?>" class="wpu-form wpu-login-form form ui" action="<?php echo site_url('/'); ?>" method="post">
    <?php if( WP_Users()->settings['form_login_header'] ) { ?>
    <div class="wpu-form-header">
        <h3><?php echo esc_html( WP_Users()->settings['login_header_title'] ); ?></h3>
    </div>
    <?php } ?>

    <div class="wpu-form-body">
        <?php do_action( 'wp_users_before_login_form' ); ?>
        <p class="fieldset wpu_input wp_usersname_email">
            <label class="wpuname" for="signin-username<?php echo $id; ?>"><?php _e( 'Username or email', 'wp-users' ); ?></label>
            <input name="wp_usersname" class="full-width has-padding has-border" id="signin-username<?php echo $id; ?>" type="text" placeholder="<?php echo esc_attr( __( 'Username or email', 'wp-users' ) ); ?>">
            <span class="wpu-error-msg"></span>
        </p>

        <p class="fieldset wpu_input wpu_pwd">
            <label class="image-replace wpu-password" for="signin-password<?php echo $id; ?>"><?php _e('Password','wp-users'); ?></label>
            <span class="wpu-pwd-toggle">
                <input name="wpu_pwd" class="full-width has-padding has-border" id="signin-password<?php echo $id; ?>" type="password"  placeholder="<?php echo esc_attr( __( 'Password', 'wp-users' ) ); ?>">
                <a href="#0" class="hide-password"><?php _e( 'Show', 'wp-users' ) ?></a>
            </span>
            <span class="wpu-error-msg"></span>
        </p>
        <p class="forgetmenot fieldset">
            <label> <input type="checkbox" value="forever" name="wpu_rememberme" checked> <?php _e( 'Remember me','wp-users' ); ?></label>
            <a class="wpu-lost-pwd-link" href="<?php echo wp_lostpassword_url(); ?>"><?php _e( 'Forgot password ?', 'wp-users' ); ?></a>
        </p>
        <?php do_action('wp_users_before_submit_login_form'); ?>
        <p class="fieldset">
            <input class="<?php echo esc_attr( apply_filters( 'wp_users_form_submit_btn_class', 'login-submit button btn' ) ); ?>" type="submit" value="<?php echo esc_attr__( 'Login', 'wp-users' ); ?>">
            <input type="hidden" value="<?php echo apply_filters( 'wp_users_logged_in_redirect_to', $login_redirect_url ); ?>" name="wpu_redirect_to" >
        </p>

        <?php do_action('wp_users_after_login_form', $in_modal, $login_redirect_url ); ?>
    </div>

    <div class="wpu-form-footer">
        <p>
        <?php
            printf( __( 'Don\'t have an account ? <a  class="wpu-register-link" href="%1$s">Sign Up</a>', 'wp-users'  ), wp_registration_url() );
        ?>
        </p>
    </div>
</form>

<?php } else {

    // user logged in info
    $user = wp_get_current_user();
    ?>

    <div class="wpu-logged-in wpu-profile-mini" >
        <div class="wpu-form-header">
            <?php do_action( 'wp_users_profile_header' , $user, false , false );  ?>
        </div>
        <div class="wpu-form-body wpu-links">
            <a href="<?php echo WP_Users()->get_profile_link( $user ); ?>"><?php _e( 'Profile', 'wp-users' ) ?></a>
            <a href="<?php echo wp_logout_url() ; ?>"><?php _e( 'Logout', 'wp-users' ) ?></a>
            <?php do_action( 'wp_users_logged_in_links',  $user ); ?>
        </div>
    </div>

<?php }?>