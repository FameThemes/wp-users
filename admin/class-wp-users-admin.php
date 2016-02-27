<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    ST_User
 * @subpackage ST_User/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    ST_User
 * @subpackage ST_User/admin
 * @author     SmoothThemes
 */
class WP_Users_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $st_user    The ID of this plugin.
	 */
	private $st_user;

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
	 * @param      string    $st_user       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $st_user, $version ) {

		$this->st_user = $st_user;
		$this->version = $version;
        add_action( 'admin_menu', array( $this ,'add_option_menu' ) );

	}

    public function add_option_menu() {
        add_options_page( __( 'WP Users', 'wp-users' ), __( 'WP Users','wp-users' ), 'edit_users', 'wp-users', array( $this, 'option_settings' ));
    }

    function option_settings() {
       include  dirname(__FILE__).'/partials/plugin-wp-users-display.php';
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

		wp_enqueue_style( $this->st_user, plugin_dir_url( __FILE__ ) . 'css/wp-users-admin.css', array(), $this->version, 'all' );

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

		wp_enqueue_script( $this->st_user, plugin_dir_url( __FILE__ ) . 'js/wp-users-admin.js', array( 'jquery' ), $this->version, false );

	}

}





