<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    WP_Users
 * @subpackage WP_Users/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    WP_Users
 * @subpackage WP_Users/admin
 * @author     SmoothThemes
 */
class WP_Users_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $wp_users    The ID of this plugin.
	 */
	private $wp_users;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $wp_users       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $wp_users, $version ) {

		$this->wp_users = $wp_users;
		$this->version = $version;
        add_action( 'admin_menu', array( $this ,'add_option_menu' ) );

	}

    public function add_option_menu() {
        add_options_page( __( 'WP Users', 'wp-users' ), __( 'WP Users','wp-users' ), 'edit_users', 'wp-users', array( $this, 'option_settings' ));
    }

    function option_settings() {
       include  dirname(__FILE__).'/partials/admin-display.php';
    }

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in WP_Users_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The WP_Users_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->wp_users, plugin_dir_url( __FILE__ ) . 'css/wpu-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in WP_Users_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The WP_Users_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->wp_users, plugin_dir_url( __FILE__ ) . 'js/wpu-admin.js', array( 'jquery' ), $this->version, false );

	}

}





