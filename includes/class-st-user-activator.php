<?php
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    ST_User
 * @subpackage ST_User/includes
 * @author     SmoothThemes
 */
class ST_User_Activator {

	/**
	 * Run settings when plugin active
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
        $account_slug   = 'account';
        $shortcode_base = 'st_user';
        $page = array(
            'post_title'   => __('Account','st-user'),
            'post_name'    => $account_slug,
            'post_type'    => 'page',
            'post_status'  => 'publish',
            'post_content' => '['.$shortcode_base.']',

        );
        $page_id = 0;
        $p = get_page_by_path( $account_slug, OBJECT, 'page' );
        if ( $p ) {
            $page_id = $p->ID;

            if ( ! has_shortcode( $p->post_content, $shortcode_base ) ) {
                $p->post_content = '['.$shortcode_base.']'."\r\n \r\n". $p->post_content;
                wp_update_post( $p );
            }

        } else {
            $r = wp_insert_post( $page );
            if ( ! is_wp_error( $r ) && is_numeric( $r ) ) {
                $page_id = $r;
            }
        }

        $option_keys = array(
            'st_user_account_page'          => $page_id ,
            'st_user_disable_default_login' => 0 ,
            'st_user_login_redirect_url'    => '',
            'st_user_logout_redirect_url'   => '',
            'st_user_term_page'             => '',
        );

        foreach ( $option_keys as $k => $v ) {
            update_option( $k, $v );
        }
	}

}
