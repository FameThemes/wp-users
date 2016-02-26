<?php
/**
 * The Template for displaying login form
 *
 * Override template in your theme by copying it to:
 * YOUR_THEME_DIR/templates/login.php
 * or YOUR_THEME_DIR/templates/st-user/login.php
 * or YOUR_THEME_DIR/st-user/login.php
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
        <h3><?php _e( 'Login', 'st-user' ); ?></h3>
    <?php } ?>
    <form class="st-form st-login-form" action="<?php echo site_url('/'); ?>" method="post">
        <?php do_action( 'st_user_before_login_form' ); ?>
        <p class="fieldset st-username">
            <label class="image-replace st-username" for="signin-username"><?php _e( 'Username or email', 'st-user' ); ?></label>
            <input name="st_username" class="full-width has-padding has-border" id="signin-username" type="text" placeholder="<?php echo esc_attr( __( 'Username or email', 'st-user' ) ); ?>">
            <span class="st-error-message"></span>
        </p>

        <p class="fieldset st-pwd">
            <label class="image-replace st-password" for="signin-password"><?php _e('Password','st-user'); ?></label>
            <input name="st_pwd" class="full-width has-padding has-border" id="signin-password" type="password"  placeholder="<?php echo esc_attr( __( 'Password', 'st-user' ) ); ?>">
            <a href="#0" class="hide-password"><?php _e('Show','st-user') ?></a>
            <span class="st-error-message"></span>
        </p>
        <p class="forgetmenot fieldset">
            <label> <input type="checkbox" value="forever" name="st-rememberme" checked> <?php _e( 'Remember me','st-user' ); ?></label>
        </p>
        <?php do_action('st_user_before_submit_login_form'); ?>
        <p class="fieldset">
            <input class="full-width" type="submit" value="<?php echo esc_attr__( 'Login', 'st-user' ); ?>">
            <input type="hidden" value="<?php echo apply_filters( 'st_user_logged_in_redirect_to', $login_redirect_url ); ?>" name="st_redirect_to" >
        </p>

        <?php do_action( 'st_user_after_login_form', $in_modal, $login_redirect_url ); ?>
    </form>
    <p class="st-form-bottom-message">
        <a class="st-lost-pwd-link" href="<?php echo wp_lostpassword_url(); ?>"><?php _e( 'I don\'t know my password', 'st-user' ); ?></a>
        <?php if ( ! $in_modal ) { ?>
        <a class="st-register-link" href="<?php echo wp_registration_url();  ?>"><?php _e( 'Singup', 'st-user' ); ?></a>
        <?php } ?>
    </p>
    <!-- <a href="#0" class="st-close-form">Close</a> -->
</div> <!-- st-login -->
<?php } else {

    // user logged in info
    $user = wp_get_current_user();
    ?>
    <div id="st-login" class="st-form-w st-logged-in"> <!-- log in form -->
        <h3><?php echo sprintf( __( 'Welcome <span class="display-name">%s</span>', 'st-user' ), $user->display_name ); ?></h3>
         <div class="st-user-info">
             <div class="st-ui st-username">
                 <?php
                 echo get_avatar( $user->ID, '80' );
                 ?>
                 <div class="logged-info">
                     <p><?php
                     echo sprintf(
                         __( 'Logged in as <a href="%1$s"><strong>%2$s</strong></a>' ),
                         apply_filters( 'st_user_url', '#' ),
                         $user->user_login
                         );
                     ?></p>
                     <p><?php
                          echo sprintf( __( 'Member since <strong>%1$s</strong>', 'st-user' ), date_i18n( get_option( 'date_format' ),  strtotime( $user->user_registered ) ) );
                         ?></p>
                 </div>
             </div>
             <div class="st-ui st-user-links">
                 <a href="<?php echo esc_attr( apply_filters( 'st_user_url', '#' ) ) ?>"><?php _e( 'Profile', 'st-user' ) ?></a>
                 <a href="<?php echo wp_logout_url( $logout_redirect_url ) ; ?>"><?php _e( 'Logout', 'st-user' ) ?></a>
                 <?php do_action( 'st_user_logged_in_links',  $user ); ?>
             </div>
             <?php do_action( 'st_user_logged_in_info',  $user ); ?>

         </div>
    </div> <!-- st-login -->
<?php }?>