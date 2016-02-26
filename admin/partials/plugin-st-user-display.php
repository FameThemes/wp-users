<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    ST_User
 * @subpackage ST_User/admin/partials
 */

if ( isset( $_POST['submit'] ) ) {
    $option_keys = array(
        'st_user_account_page'          => '',
        'st_user_disable_default_login' => '',
        'st_user_login_redirect_url'    => '',
        'st_user_logout_redirect_url'   => '',
        'st_user_term_page'             => '',
    );
    $option_keys = apply_filters( 'st_settings_keys', $option_keys );

    foreach ( $option_keys as $k => $v ) {
        // if the key container "st_user_"  at begin of string the save it
        if ( isset( $_POST[ $k ] ) ) {
            update_option( $k, $_POST[ $k ] );
        } else {
            delete_option( $k );
        }
    }
}


?>
<h2><?php _e( 'ST User Settings','st-user' ); ?></h2>
<form novalidate="novalidate" action="" method="post">
    <h3><?php _e( 'General', 'st-user' ); ?></h3>
    <?php if ( isset( $_POST['submit'] ) ) { ?>
    <div class="updated notice is-dismissible" id="message"><p>Your settings updated.</p><button class="notice-dismiss" type="button"><span class="screen-reader-text"><?php _e('Dismiss this notice.'); ?></span></button></div>
    <?php } ?>
    <table class="form-table">
        <tbody>
        <tr>
            <th scope="row"><label for="st_user_account_page"><?php _e( 'Account page', 'st-user' ); ?></label></th>
            <td>
                <?php
                wp_dropdown_pages (
                    array(
                    //'depth'                 => 0,
                    //'child_of'              => 0,
                    'selected'              => get_option( 'st_user_account_page' ),
                    //'echo'                  => 1,
                    'name'                  => 'st_user_account_page',
                    'id'                    => null, // string
                    'show_option_none'      =>  __( 'Select a page', 'st-user' ), // string
                    'show_option_no_change' => null, // string
                    'option_none_value'     => null, // string
                ) );
                ?>
            </td>
        </tr>

        <tr>
            <th scope="row"><?php _e ( 'Login','st-user' ); ?></th>
            <td>
                <fieldset>
                    <legend class="screen-reader-text"><span><?php _e( 'Login', 'st-user' ); ?></span></legend>
                    <label >
                        <input type="checkbox" <?php checked ( get_option( 'st_user_disable_default_login' ) , 1 ) ?> value="1"  name="st_user_disable_default_login">
                        <?php _e( 'User Account page as login page.', 'st-user' ); ?>
                    </label>
                    <p class="description"><?php _e( 'Default login page of WordPress will disable.', 'st-user' ); ?></p>
                </fieldset>
            </td>
        </tr>

        <tr>
            <th scope="row"><label for="st_user_login_redirect_url"><?php _e('Login Redirect (URL)', 'st-user'); ?></label></th>
            <td>
                <input type="text" class="regular-text" value="<?php echo esc_attr( get_option( 'st_user_login_redirect_url' ) ); ?>" id="st_user_login_redirect_url" name="st_user_login_redirect_url">
                <p class="description"><?php _e( 'The url will redirect when you logged in, leave empty to redirect home page.', 'st-user' ); ?></p>
            </td>
        </tr>

        <tr>
            <th scope="row"><label for="st_user_logout_redirect_url"><?php _e( 'Logout Redirect (URL)', 'st-user' ); ?></label></th>
            <td>
                <input type="text" class="regular-text" value="<?php echo esc_attr( get_option( 'st_user_logout_redirect_url' ) ); ?>" id="st_user_logout_redirect_url" name="st_user_logout_redirect_url">
                <p class="description"><?php _e( 'The url will redirect when you logout, leave empty to redirect home page.', 'st-user' ); ?></p>
            </td>
        </tr>


        <tr>
            <th scope="row"><label for="st_user_term_page"><?php _e('Term and Condition','st-user'); ?></label></th>
            <td>
                <?php
                wp_dropdown_pages(
                    array(
                        //'depth'                 => 0,
                        //'child_of'              => 0,
                        'selected'              => get_option('st_user_term_page'),
                        //'echo'                  => 1,
                        'name'                  => 'st_user_term_page',
                        'id'                    => null, // string
                        'show_option_none'      => __('Select a page','st-user'), // string
                        'show_option_no_change' => null, // string
                        'option_none_value'     => null, // string
                    ) );
                ?>
            </td>
        </tr>

        <?php
        /**
         * hook you can add more fields if you want
         * @since 1.0.0
         */
        do_action( 'st_user_settings_fields' );
        ?>
        </tbody>
    </table>

    <?php
    /**
     * hook you can add more table fields if you want
     * @since 1.0.0
     */
    do_action( 'st_user_settings_table' );
    ?>

    <p class="submit">
        <input type="submit" value="<?php echo esc_attr__( 'Save Changes', 'st-user' ); ?>" class="button button-primary" id="submit" name="submit">
    </p>
</form>

