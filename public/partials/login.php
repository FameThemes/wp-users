<?php
/**
 * The Template for displaying login form
 *
 * Override template in your theme by copying it to:
 * YOUR_THEME_DIR/templates/login.php
 * or YOUR_THEME_DIR/templates/wp-users/login.php
 * or YOUR_THEME_DIR/wp-users/login.php
 *
 * @package 	ST-User/Templates
 * @version     1.0
 */
if ( !isset( $in_modal ) ) {
    $in_modal = false;
}

if ( ! is_user_logged_in() ) {
?>
<div id="st-login"> <!-- log in form -->
    <?php if ( !$in_modal ) { ?>
        <h3><?php _e( 'Login', 'wp-users' ); ?></h3>
    <?php } ?>
    <form class="st-form st-login-form" action="<?php echo site_url('/'); ?>" method="post">
        <?php do_action( 'wp_users_before_login_form' ); ?>
        <p class="fieldset wp-usersname">
            <label class="image-replace wp-usersname" for="signin-username"><?php _e( 'Username or email', 'wp-users' ); ?></label>
            <input name="st_username" class="full-width has-padding has-border" id="signin-username" type="text" placeholder="<?php echo esc_attr( __( 'Username or email', 'wp-users' ) ); ?>">
            <span class="st-error-message"></span>
        </p>

        <p class="fieldset st-pwd">
            <label class="image-replace st-password" for="signin-password"><?php _e('Password','wp-users'); ?></label>
            <input name="st_pwd" class="full-width has-padding has-border" id="signin-password" type="password"  placeholder="<?php echo esc_attr( __( 'Password', 'wp-users' ) ); ?>">
            <a href="#0" class="hide-password"><?php _e('Show','wp-users') ?></a>
            <span class="st-error-message"></span>
        </p>
        <p class="forgetmenot fieldset">
            <label> <input type="checkbox" value="forever" name="st-rememberme" checked> <?php _e( 'Remember me','wp-users' ); ?></label>
        </p>
        <?php do_action('wp_users_before_submit_login_form'); ?>
        <p class="fieldset">
            <input class="full-width" type="submit" value="<?php echo esc_attr__( 'Login', 'wp-users' ); ?>">
            <input type="hidden" value="<?php echo apply_filters( 'wp_users_logged_in_redirect_to', $login_redirect_url ); ?>" name="st_redirect_to" >
        </p>

        <?php do_action( 'wp_users_after_login_form', $in_modal, $login_redirect_url ); ?>
    </form>
    <p class="st-form-bottom-message">
        <a class="st-lost-pwd-link" href="<?php echo wp_lostpassword_url(); ?>"><?php _e( 'I don\'t know my password', 'wp-users' ); ?></a>
        <?php if ( ! $in_modal ) { ?>
        <a class="st-register-link" href="<?php echo wp_registration_url();  ?>"><?php _e( 'Singup', 'wp-users' ); ?></a>
        <?php } ?>
    </p>
    <!-- <a href="#0" class="st-close-form">Close</a> -->
</div> <!-- st-login -->
<?php } else {

    // user logged in info
    $user = wp_get_current_user();
    ?>
    <div id="st-login" class="st-form-w st-logged-in"> <!-- log in form -->
        <h3><?php echo sprintf( __( 'Welcome <span class="display-name">%s</span>', 'wp-users' ), $user->display_name ); ?></h3>
         <div class="wp-users-info">
             <div class="st-ui wp-usersname">
                 <?php
                 echo get_avatar( $user->ID, '80' );
                 ?>
                 <div class="logged-info">
                     <p><?php
                     echo sprintf(
                         __( 'Logged in as <a href="%1$s"><strong>%2$s</strong></a>' ),
                         apply_filters( 'wp_users_url', '#' ),
                         $user->user_login
                         );
                     ?></p>
                     <p><?php
                          echo sprintf( __( 'Member since <strong>%1$s</strong>', 'wp-users' ), date_i18n( get_option( 'date_format' ),  strtotime( $user->user_registered ) ) );
                         ?></p>
                 </div>
             </div>
             <div class="st-ui wp-users-links">
                 <a href="<?php echo esc_attr( apply_filters( 'wp_users_url', '#' ) ) ?>"><?php _e( 'Profile', 'wp-users' ) ?></a>
                 <a href="<?php echo wp_logout_url( $logout_redirect_url ) ; ?>"><?php _e( 'Logout', 'wp-users' ) ?></a>
                 <?php do_action( 'wp_users_logged_in_links',  $user ); ?>
             </div>
             <?php do_action( 'wp_users_logged_in_info',  $user ); ?>

         </div>
    </div> <!-- st-login -->
<?php }?>