<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    ST_User
 * @subpackage ST_User/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    WP_Users
 * @subpackage wp-users/includes
 * @author     SmoothThemes
 */
class WP_Users {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      WP_Users_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $st_user    The string used to uniquely identify this plugin.
	 */
	protected $st_user;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */

    /**
     *  The Settings of this plugins
     *
     *
     * @since 1.0.0
     */
    protected  $settings;

	public function __construct() {

		$this->st_user = 'wp-users';
		$this->version = '1.0.0';

        $this->settings();
		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

        if ( function_exists('csstricks_hide_admin_bar') ) {
            add_action( 'set_current_user', 'csstricks_hide_admin_bar' );
        }

        // disable admin toolbar
        if ( ! current_user_can( 'edit_posts' ) ) {
            show_admin_bar( false );
        }

        do_action( 'wp_users_init', $this );

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - WP_Users_Loader. Orchestrates the hooks of the plugin.
	 * - WP_Users_i18n. Defines internationalization functionality.
	 * - WP_Users_Admin. Defines all hooks for the admin area.
	 * - WP_Users_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-users-loader.php';
        $this->loader = new WP_Users_Loader();

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wp-users-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wp-users-admin.php';

        /**
         * Load Cores
         */
        include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/functions.php';
        include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-user-action.php';
        include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/nav-menu.php';
        include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-shortcodes.php';
        new ST_User_Shortcodes( $this );

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-wp-users-public.php';

        include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-ajax.php';

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the WP_Users_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {
		$plugin_i18n = new WP_Users_i18n();
		$plugin_i18n->set_domain( $this->get_wp_users() );
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		$plugin_admin = new WP_Users_Admin( $this->get_wp_users(), $this->get_version() );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}


	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new WP_Users_Public( $this );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'wp_footer', $plugin_public, 'modal' );

        /**
         * Add filter widget text shortcode
         */
        add_filter( 'widget_text', 'do_shortcode' );
        /**
         * Set default plugin page url
         */
        $this->loader->add_filter( 'wp_users_url', $this, 'page_url');

        /**
         * Set default logout redirect to url
         */
        $this->loader->add_filter( 'wp_users_logout_url', $this, 'logout_url' );
        $this->loader->add_filter( 'logout_redirect', $this, 'logout_url' );

        /**
         * Redirect to url when user logged in
         */
        $this->loader->add_filter('wp_users_logged_in_redirect_to', $this, 'logged_in_url');
        $this->loader->add_filter('login_redirect', $this, 'logged_in_url');

        /**
         * Login url
         */
        $this->loader->add_filter('wp_users_login_url', $this, 'login_url');

        // disable default login url

        if ( $this->get_setting('disable_default_login')  && !isset( $_GET['interim-login'] ) ) {
            if ( ! is_admin()  ) {
                $this->loader->add_filter( 'login_url', $this, 'login_url' );
            }elseif ( defined( 'DOING_AJAX' )  ) {
                $this->loader->add_filter( 'login_url', $this, 'login_url' );
            }
        }

        /**
         * Register url
         */
        $this->loader->add_filter( 'register_url', $this, 'register_url' );

        /**
         * Lost pwd url
         */
        $this->loader->add_filter( 'wp_users_lost_passoword_url', $this, 'lost_pwd_url' );
        $this->loader->add_filter( 'lostpassword_url', $this, 'lost_pwd_url' );

        /**
         * Change  term condition link
         */
        $this->loader->add_filter( 'wp_users_term_link', $this, 'term_link' );

        $ajax = new ST_User_Ajax( $this );

        $this->loader->add_action( 'wp_ajax_wp_users_ajax', $ajax, 'ajax' );
        $this->loader->add_action( 'wp_ajax_nopriv_wp_users_ajax', $ajax, 'ajax' );

	}

    /**
     * Setup plugin settings
     *
     * @since 1.0.0
     */
    private function settings( ) {
        $this->settings = array();

        /**
         * The url of St User page
         * Change it in admin setting
         */
        $page_id = get_option( 'wp_users_account_page' );
        $page_url =  get_permalink( $page_id );
        $this->settings['url'] = ($page_id) ?  $page_url :  site_url('/');

        $this->settings['disable_default_login'] = get_option( 'wp_users_disable_default_login' );

        $this->settings['logout_url'] =  get_option( 'wp_users_logout_redirect_url' );
        if ( $this->settings['logout_url'] == '' ) {
            $this->settings['logout_url'] = $this->settings['url'];
        }

        if ( ! ( $this->settings['logged_in_url'] = get_option( 'wp_users_login_redirect_url' ) ) ) {
            $this->settings['logged_in_url'] = $this->settings['url'];
        }

        $this->settings['lost_pwd_url'] = add_query_arg( array( 'st_action' => 'lost-pass' ), $page_url );
        $this->settings['register_url'] = add_query_arg( array( 'st_action' => 'register' ), $page_url );

        $this->settings['term_link'] = get_permalink( get_option( 'wp_users_term_page' ) );

        /**
         * Hook to change settings if you want
         * @since 1.0.0
         */
        $this->settings = apply_filters('wp_users_setup_settings', $this->settings, $this );
    }

    /**
     *  Get setting by key
     *
     * @param $key
     * @param bool $default
     * @return mixed
     */
    public function get_setting( $key , $default =  false ) {
        if ( isset( $this->settings[  $key ] ) ) {
            return  $this->settings[  $key ];
        } else {
            return $default;
        }
    }

    /**
     * Plugin page url
     *
     * @since 1.0.0
     * @param string $url
     * @return mixed
     */
    public function page_url( $url = '' ) {
        return $this->get_setting('url');
    }

    /**
     * Plugin page url
     *
     * @since 1.0.0
     * @param string $url
     * @return mixed
     */
    public function register_url( $url = '' ) {
        return $this->get_setting( 'register_url' );
    }

    /**
     * Redirect logged out url
     *
     * @since 1.0.0
     * @param string $url
     * @return mixed
     */
    public function logout_url( $url = '' ) {
        if (  $url == '' ){
            return $this->get_setting( 'logout_url' );
        }
        return $url;
    }

    /**
     * Redirect Logged in out url
     *
     * @since 1.0.0
     * @param string $url
     * @return mixed
     */
    public function logged_in_url( $url = '' ) {
         if ( current_user_can( 'editor' ) || current_user_can( 'administrator' ) ) {
            return $url;
         }
        return ( $this->get_setting( 'logged_in_url' ) != '' ) ? $this->get_setting( 'logged_in_url' ) : $url ;
    }

    /**
     * Set Plugin url it can be login/profile/register page
     *
     * @since 1.0.0
     * @param string $url
     * @return mixed
     */
    public function login_url( $url = '' ) {
        return $this->get_setting('url');
    }

    /**
     * Set lost password url
     *
     * @since 1.0.0
     * @param string $url
     * @return mixed
     */
    public function lost_pwd_url( $url = '' ) {
        return $this->get_setting( 'lost_pwd_url' );
    }


    /**
     * Set term and condition link
     *
     * @since 1.0.0
     * @param string $url
     * @return mixed
     */
    public function term_link( $url = '' ) {
        return $this->get_setting( 'term_link' );
    }

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_wp_users() {
		return $this->st_user;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    WP_Users_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}


    /**
     * Get Template path
     *
     * Override template in your theme
     * YOUR_THEME_DIR/templates/{$template}
     * or YOUR_THEME_DIR/templates/wp-users/{$template}
     * or YOUR_THEME_DIR/wp-users/{$template}
     *
     * @since 1.0
     * @param string $template
     * @return string
     */
    public function get_file_template( $template ='' ) {
        /**
         * Overridden template in your theme
         * YOUR_THEME_DIR/templates/{$template}
         * or YOUR_THEME_DIR/templates/wp-users/{$template}
         * or YOUR_THEME_DIR/wp-users/{$template}
         */
        $templates =  array(
            'templates/'.$template,
            'templates/wp-users/'.$template,
            'wp-users/'.$template,
        );

        if ( $overridden_template = locate_template( $templates ) ) {
            // locate_template() returns path to file
            // if either the child theme or the parent theme have overridden the template
            return $overridden_template;
        } else {
            // If neither the child nor parent theme have overridden the template,
            // we load the template from the 'templates' directory if this plugin
            return WP_USERS_PATH . 'public/partials/'.$template;
        }
    }


    /**
     * Get content form a file.
     *
     * @since 1.0
     * @param string $template full file path
     * @param array $custom_data
     * @return string
     */
    public function get_file_content( $template, $custom_data = array() ) {
        ob_start();
        $old_content = ob_get_clean();
        ob_start();
        if ( is_file( $template ) ) {
            if ( is_array( $custom_data ) ) {
                extract( $custom_data);
            }
            //load_template();
            require $template  ;
        }
        $content = ob_get_clean();
        echo $old_content;
        return $content;
    }

    /**
     * Get template content
     * @see
     * @see get_file_content
     * @see get_file_template
     * @param $template
     * @param array $custom_data
     * @return string
     */
    public  function get_template_content( $template,  $custom_data = array() ) {
        return  $this->get_file_content( $this->get_file_template( $template ) , $custom_data );
    }

}
