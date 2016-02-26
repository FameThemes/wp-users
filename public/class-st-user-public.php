<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    ST_User
 * @subpackage ST_User/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    ST_User
 * @subpackage ST_User/public
 * @author     SmoothThemes
 */
class ST_User_Public {

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
	 * @param      string    $st_user       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */

    /**
     * Instance classs ST_User
     * @since 1.0
     * @var ST_User
     */
    private  $instance;

    /**
     * Current action of plugin
     * @since 1.0.0
     */
    private  $current_action;

	public function __construct( $instance ) {

        $this->instance = $instance;
        $this->current_action = isset( $_REQUEST['st_action'] ) ? sanitize_key( $_REQUEST['st_action'] ) : '';

		$this->st_user = $this->instance->get_st_user();
		$this->version = $this->instance->get_version();

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in ST_User_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The ST_User_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */


        wp_register_style( $this->st_user, ST_USER_URL.'public/css/st-user.css' );
        wp_enqueue_style( $this->st_user );
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in ST_User_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The ST_User_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

        wp_enqueue_script('jquery');
        wp_enqueue_script('json2');
        wp_enqueue_script('modernizr', ST_USER_URL.'public/js/modernizr.js',array('jquery'), '2.7.1', true  );
        wp_enqueue_script( $this->st_user , ST_USER_URL.'public/js/st-user.js',array('jquery'), '1.0', true  );

        wp_localize_script( $this->st_user , 'ST_User',
            apply_filters('st_user_localize_script', array(
                'ajax_url'          => admin_url( 'admin-ajax.php' ),
                'current_action'    => $this->current_action,
                'hide_txt'          => __('Hide','st-user'),
                'show_txt'          => __('Show','st-user'),
                'current_url'       => $_SERVER['REQUEST_URI'],
                '_wpnonce'          => wp_create_nonce()
            ) )
        );

    }

    /**
     *  Display modal
     * @since 1.0
     */
    function modal() {
        echo $this->instance->get_template_content('modal.php', array('current_action' => $this->current_action ) ) ;
    }


}
