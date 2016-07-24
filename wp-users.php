<?php
/**
 * Plugin Name:       WP Users
 * Plugin URI:        http://smooththemes.com/wp-users/
 * Description:       Advance Ajax WordPress Login & Register Form.
 * Version:           1.0.1
 * Author:            SmoothThemes
 * Author URI:        http://smoothemes.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wpu
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
/**
 * Make sure this plugin load one time
 *
 */
if ( ! defined( 'WPU_PATH' ) ) {

	define('WPU_URL', trailingslashit(plugins_url('', __FILE__)));
	define('WPU_PATH', trailingslashit(plugin_dir_path(__FILE__)));

	global $WP_Users;

	/**
	 * The code that runs during plugin activation.
	 * This action is documented in includes/class-wpu-activator.php
	 */
	function activate_wp_users()
	{
		require_once plugin_dir_path(__FILE__) . 'includes/class-activator.php';
		WP_Users_Activator::activate();
	}

	/**
	 * The code that runs during plugin deactivation.
	 * This action is documented in includes/class-wpu-deactivator.php
	 */
	function deactivate_wp_users()
	{
		require_once plugin_dir_path(__FILE__) . 'includes/class-deactivator.php';
		WP_Users_Deactivator::deactivate();
	}

	register_activation_hook(__FILE__, 'activate_wp_users');
	register_deactivation_hook(__FILE__, 'deactivate_wp_users');

	/**
	 * The core plugin class that is used to define internationalization,
	 * admin-specific hooks, and public-facing site hooks.
	 */
	require_once plugin_dir_path(__FILE__) . 'includes/class-user.php';

	/**
	 * Begins execution of the plugin.
	 *
	 * Since everything within the plugin is registered via hooks,
	 * then kicking off the plugin from this point in the file does
	 * not affect the page life cycle.
	 *
	 * @since    1.0.0
	 */
	function wp_users_init()
	{
		global $WP_Users;
        if ( ! isset( $WP_Users ) ) {
            $plugin = new WP_Users();
			$WP_Users = $plugin;
        }

	}
	add_action('init', 'wp_users_init');

    /**
     * A Alias of class WP_USERS
     *
     * @see WP_Users
     * @return WP_Users
     */
    function WP_Users(){
        global $WP_Users;
		if ( ! $WP_Users instanceof WP_Users ) {
			$WP_Users = new WP_Users();
		}
        return $WP_Users;
    }
}