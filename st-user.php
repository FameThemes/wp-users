<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://smoothemes.com
 * @since             1.0.0
 * @package           ST_User
 *
 * @wordpress-plugin
 * Plugin Name:       ST User
 * Plugin URI:        http://smooththemes.com/st-user/
 * Description:       Advance Ajax WordPress Login & Register Form.
 * Version:           1.0.0
 * Author:            SmoothThemes
 * Author URI:        http://smoothemes.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       st-user
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'ST_USER_URL', trailingslashit( plugins_url('', __FILE__) ) );
define( 'ST_USER_PATH', trailingslashit( plugin_dir_path( __FILE__) ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-st-user-activator.php
 */
function activate_st_user() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-st-user-activator.php';
	ST_User_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-st-user-deactivator.php
 */
function deactivate_st_user() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-st-user-deactivator.php';
	ST_User_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_st_user' );
register_deactivation_hook( __FILE__, 'deactivate_st_user' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-st-user.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_st_user() {
	$plugin = new ST_User();
	$plugin->run();
}
add_action( 'init', 'run_st_user' );
