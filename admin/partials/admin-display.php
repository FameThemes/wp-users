<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    WP_Users
 * @subpackage WP_Users/admin/partials
 */


$default = array(
    'account_page'          => '',
    'disable_default_login' => '',
    'login_redirect_url'    => '',
    'logout_redirect_url'   => '',
    'show_term'             => '',
    'term_mgs'              => '',
    'form_login_header'          => 0,
    'form_register_header'       => 0,
    'form_reset_header'          => 0,
    'form_change_pass_header'    => 0,
    'form_profile_header'        => 0,
    'login_header_title'         => '',
    'register_header_title'      => '',
    'reset_header_title'         => '',
    'change_pass_header_title'   => '',
);

if ( isset( $_POST['submit'] ) ) {
    $values = $_POST[ 'wp_users_settings' ] ;
    $values['term_mgs'] = trim( stripslashes( $_POST['wp_users_settings_mgs'] ) );
    update_option( 'wp_users_settings', $values );

}

$settings = (array) get_option( 'wp_users_settings' );
$settings = wp_parse_args( $settings,  $default );

?>
<h2><?php _e( 'WP Users Settings','wp-users' ); ?></h2>
<?php if ( isset( $_POST['submit'] ) ) { ?>
    <div class="updated notice notice-success is-dismissible below-h2" id="message">
        <p><?php _e( 'Your settings updated.' , 'wp-users' ); ?></p>
    </div>
<?php } ?>

<form novalidate="novalidate" action="" method="post">
    <h3><?php _e( 'General', 'wp-users' ); ?></h3>

    <table class="form-table">
        <tbody>
        <tr>
            <th scope="row"><label for="wp_users_account_page"><?php _e( 'Account page', 'wp-users' ); ?></label></th>
            <td>
                <?php
                wp_dropdown_pages (
                    array(
                    //'depth'                 => 0,
                    //'child_of'              => 0,
                    'selected'              => $settings['account_page'],
                    //'echo'                  => 1,
                    'name'                  => 'wp_users_settings[account_page]',
                    'id'                    => null, // string
                    'show_option_none'      =>  __( 'Select a page', 'wp-users' ), // string
                    'show_option_no_change' => null, // string
                    'option_none_value'     => null, // string
                ) );
                ?>
            </td>
        </tr>

        <tr>
            <th scope="row"><?php _e ( 'Login','wp-users' ); ?></th>
            <td>
                <fieldset>
                    <legend class="screen-reader-text"><span><?php _e( 'Login', 'wp-users' ); ?></span></legend>
                    <label >
                        <input type="checkbox" <?php checked ( $settings['disable_default_login'] , 1 ) ?> value="1"  name="wp_users_settings[disable_default_login]">
                        <?php _e( 'User Account page as login page.', 'wp-users' ); ?>
                    </label>
                    <p class="description"><?php _e( 'Default login page of WordPress will disable.', 'wp-users' ); ?></p>
                </fieldset>
            </td>
        </tr>

        <tr>
            <th scope="row"><label for="wp_users_login_redirect_url"><?php _e('Login Redirect (URL)', 'wp-users'); ?></label></th>
            <td>
                <input type="text" class="regular-text" value="<?php echo esc_attr( $settings['login_redirect_url'] ); ?>" id="wp_users_login_redirect_url" name="wp_users_settings[login_redirect_url]">
                <p class="description"><?php _e( 'The url will redirect when you logged in, leave empty to redirect current page.', 'wp-users' ); ?></p>
            </td>
        </tr>

        <tr>
            <th scope="row"><label for="wp_users_logout_redirect_url"><?php _e( 'Logout Redirect (URL)', 'wp-users' ); ?></label></th>
            <td>
                <input type="text" class="regular-text" value="<?php echo esc_attr( $settings['logout_redirect_url'] ); ?>" id="wp_users_logout_redirect_url" name="wp_users_settings[logout_redirect_url]">
                <p class="description"><?php _e( 'The url will redirect when you logout.', 'wp-users' ); ?></p>
            </td>
        </tr>

        <tr>
            <th scope="row"><label for="wp_users_term_page"><?php _e('Terms and Conditions','wp-users'); ?></label></th>
            <td>
                <label >
                    <input type="checkbox" <?php checked ( $settings['show_term'] , 1 ) ?> value="1"  name="wp_users_settings[show_term]">
                    <?php _e( 'Enable "Terms and Conditions" to sign up form.', 'wp-users' ); ?>
                </label>
                <br/>
                <br/>
                <label ><strong><?php _e( 'Terms and Conditions Message ', 'wp-users' ); ?></strong></label>
                <?php
                wp_editor( $settings['term_mgs'] , 'wp_users_settings_mgs', array(
                    'textarea_rows' => 6
                )  );
                ?>
            </td>
        </tr>

        <tr>
            <th scope="row"><?php _e ( 'Form Settings','wp-users' ); ?></th>
            <td>
                <fieldset>
                    <label >
                        <input type="checkbox" <?php checked ( $settings['form_login_header'] , 1 ) ?> value="1"  name="wp_users_settings[form_login_header]">
                        <?php _e( 'Show Login form header', 'wp-users' ); ?>
                    </label><br>
                    <label >
                        <?php _e( 'Login form header title', 'wp-users' ); ?><br/>
                        <input type="text" class="regular-text" value="<?php echo esc_attr( $settings['login_header_title']  ); ?>"  name="wp_users_settings[login_header_title]">
                    </label><br>


                    <label >
                        <input type="checkbox" <?php checked ( $settings['form_register_header'] , 1 ) ?> value="1"  name="wp_users_settings[form_register_header]">
                        <?php _e( 'Show Register form header', 'wp-users' ); ?>
                    </label><br>

                    <label >
                        <?php _e( 'Register form header title', 'wp-users' ); ?><br/>
                        <input type="text" class="regular-text" value="<?php echo esc_attr( $settings['register_header_title']  ); ?>"  name="wp_users_settings[register_header_title]">
                    </label><br>


                    <label >
                        <input type="checkbox" <?php checked ( $settings['form_reset_header'] , 1 ) ?> value="1"  name="wp_users_settings[form_reset_header]">
                        <?php _e( 'Show reset form header', 'wp-users' ); ?>
                    </label><br>
                    <label >
                        <?php _e( 'Reset form header title', 'wp-users' ); ?><br/>
                        <input type="text" class="regular-text" value="<?php echo esc_attr( $settings['reset_header_title']  ); ?>"  name="wp_users_settings[reset_header_title]">
                    </label><br>


                    <label >
                        <input type="checkbox" <?php checked ( $settings['form_change_pass_header'], 1 ) ?> value="1"  name="wp_users_settings[form_change_pass_header]">
                        <?php _e( 'Show change password form header', 'wp-users' ); ?>
                    </label><br/>

                    <label >
                        <?php _e( 'Change password form header title', 'wp-users' ); ?><br/>
                        <input type="text" class="regular-text" value="<?php echo esc_attr( $settings['change_pass_header_title']  ); ?>"  name="wp_users_settings[change_pass_header_title]">
                    </label><br>

                </fieldset>
            </td>
        </tr>

        <?php
        /**
         * hook you can add more fields if you want
         * @since 1.0.0
         */
        do_action( 'wp_users_settings_fields' );
        ?>
        </tbody>
    </table>

    <?php
    /**
     * hook you can add more table fields if you want
     * @since 1.0.0
     */
    do_action( 'wp_users_settings_table' );
    ?>

    <p class="submit">
        <input type="submit" value="<?php echo esc_attr__( 'Save Changes', 'wp-users' ); ?>" class="button button-primary" id="submit" name="submit">
    </p>
</form>

