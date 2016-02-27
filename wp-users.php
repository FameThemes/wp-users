<?php

/**
 * Plugin Name:       WP Users
 * Plugin URI:        http://smooththemes.com/wp-users/
 * Description:       Advance Ajax WordPress Login & Register Form.
 * Version:           1.0.0
 * Author:            SmoothThemes
 * Author URI:        http://smoothemes.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-users
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'WP_USERS_URL', trailingslashit( plugins_url('', __FILE__) ) );
define( 'WP_USERS_PATH', trailingslashit( plugin_dir_path( __FILE__) ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wp-users-activator.php
 */
function wp_users_activate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-users-activator.php';
	WP_Users_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wp-users-deactivator.php
 */
function wp_users_deactivate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-users-deactivator.php';
	WP_Users_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'wp_users_activate' );
register_deactivation_hook( __FILE__, 'wp_users_deactivate' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wp-users.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function wp_users_init() {
	$plugin = new WP_Users();
	$plugin->run();
}
add_action( 'init', 'wp_users_init' );
