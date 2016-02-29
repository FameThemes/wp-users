<?php
/**
 * The Template for displaying Profile form
 *
 * Override template in your theme by copying it to:
 * YOUR_THEME_DIR/templates/profile.php
 * or YOUR_THEME_DIR/templates/wpu/profile.php
 * or YOUR_THEME_DIR/wpu/profile.php
 *
 * @package 	ST-User/Templates
 * @version     1.0
 */


$user = WP_Users()->get_user_profile();


if ( ! $user ) {
    ?>
    <div class="wpu-not-found">
        <h2><?php _e( 'Nothing found', 'wp-users' ); ?></h2>
    </div>
    <?php
} else {

    $current_user = wp_get_current_user();
    $action = isset( $_REQUEST['wpu_action'] ) ? strtolower( $_REQUEST['wpu_action'] ) : '';

    ?>
    <div class="st-profile-wrapper wpu-form-profile" >

        <div class="wpu-form-header">
            <?php do_action( 'wp_users_profile_header' , $user, $current_user , $action );  ?>
        </div>

        <div class="wpu-form-body clear-fix">
            <?php
            do_action( 'wp_users_profile_before_form_body', $user, $current_user , $action );
            do_action('wp_users_profile_form_body', $user, $current_user , $action );
            do_action( 'wp_users_profile_after_form_body' , $user, $current_user, $action );
            ?>
        </div>
    </div>

    <?php

}