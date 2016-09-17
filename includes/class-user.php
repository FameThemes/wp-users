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
 * @package    WP_Users
 * @subpackage WP_Users/includes
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
 * @subpackage WP_Users/includes
 * @author     SmoothThemes
 */
class WP_Users {


	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $wp_users    The string used to uniquely identify this plugin.
	 */
	protected $wp_users;

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
    public  $settings;

	public function __construct() {

		$this->wp_users = 'wp-users';
		$this->version = '1.0.0';

        $this->settings();
		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_public_hooks();

        flush_rewrite_rules() ;

        $this->profile_rewrite();
        $this->profile_rewrite_tag();

        // disable admin toolbar
        if ( ! current_user_can( 'edit_posts' ) ) {
            show_admin_bar( false );
        }

        do_action( 'wp_users_init', $this );

	}

    /**
     * Add rewrite rule
     * Example the link:  http://yoursite.com/user/admin
     */
    public function profile_rewrite() {
        // for paging
        $string =  'index.php?page_id='.intval( $this->settings['account_page'] ).'&wp_users_name=$matches[1]&wpu_paged=$matches[2]';
        add_rewrite_rule( '^'.$this->settings['profile_rewrite'].'/([^/]*)/page/?([0-9]{1,})/?', $string , 'top');

        $string =  'index.php?page_id='.intval( $this->settings['account_page'] ).'&wp_users_name=$matches[1]';
        add_rewrite_rule( '^'.$this->settings['profile_rewrite'].'/([^/]*)/?', $string , 'top');
    }

    /**
     * Add Write tags
     *
     * @usage: $wp_query->query_vars['wp_users_name']
     */
    public function profile_rewrite_tag() {
         add_rewrite_tag('%wp_users_name%', '([^&]+)');
         add_rewrite_tag('%wpu_paged%', '([^&]+)');
    }

    /**
     * Get user profile data
     *
     * @return bool|object|stdClass|WP_User
     */
    public function get_user_profile( ){
        global $wp_query;
        if ( isset ( $wp_query->query_vars['wp_users_name'] ) &&  $wp_query->query_vars['wp_users_name'] != '' ) {
            $user_data = get_user_by( 'login', $wp_query->query_vars['wp_users_name'] );
            return ( $user_data && $user_data->data->ID > 0 ) ?  $user_data->data : false;

        } else if ( is_user_logged_in () ){
            return wp_get_current_user();
        }

        return false;
    }

    function can_edit_profile(){
        $user = WP_Users()->get_user_profile();
        $current_user = wp_get_current_user();
        $is_current_user =  WP_Users()->is_current_user( $user, $current_user );
        return $is_current_user;
    }

    /**
     * Get user Profile link
     *
     * @see wp_get_current_user
     *
     * @param object $user
     * @return string
     */
    public function get_profile_link( $user = null ){
        if ( ! $user && ! is_user_logged_in() ) {
            return wp_login_url();
        }

        if ( ! $user ) {
            $user = wp_get_current_user();
        }

        global $wp_rewrite;

        if ( $wp_rewrite->using_permalinks() ){
            $url = trailingslashit( site_url() ).$this->settings['profile_rewrite'].'/'.$user->user_login;
        } else {
            $url = add_query_arg( array( 'wp_users_name' => $user->user_login ), $this->settings['url']  );
        }
        return $url;
    }

    /**
     * Get user edit link
     *
     * @see wp_get_current_user
     *
     * @param object $user
     * @return string
     */
    public function get_edit_profile_link( $user ){
        return add_query_arg( array( 'wpu_action' => 'edit' ), $this->get_profile_link( $user )  );
    }

    /**
     * Check if is current user
     *
     * @see wp_get_current_user
     *
     * @param object $user
     * @param object $user2
     */
    public function is_current_user( $user, $user2 = false ){
        return ( $user &&  $user2 && $user->ID >0  && $user->ID ==  $user2->ID ) ? true : false;
    }


	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
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
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-admin.php';

        /**
         * Load Cores
         */
        include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/functions.php';
        include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-action.php';
        include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/nav-menu.php';
        include_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-shortcodes.php';
        new WP_Users_Shortcodes( $this );

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-public.php';

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
		add_action( 'admin_enqueue_scripts', array( $plugin_admin, 'enqueue_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $plugin_admin, 'enqueue_scripts' ) );
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

		add_action( 'wp_enqueue_scripts', array( $plugin_public, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $plugin_public, 'enqueue_scripts' ) );
		add_action( 'wp_footer', array( $plugin_public, 'modal' ) );

        /**
         * Add filter widget text shortcode
         */
        add_filter( 'widget_text', 'do_shortcode' );
        /**
         * Set default plugin page url
         */
        add_filter( 'wp_users_url',array( $this, 'page_url' ) );

        /**
         * Set default logout redirect to url
         */
        add_filter( 'wp_users_logout_url', array( $this, 'logout_url'  ) );
        add_filter( 'logout_url', array( $this, 'logout_url' ), 15, 2 );

        /**
         * Redirect to url when user logged in
         */
        add_filter( 'wp_users_logged_in_redirect_to', array( $this, 'logged_in_url' ) );
        add_filter( 'login_redirect', array( $this, 'logged_in_url' ) );

        /**
         * Login url
         */
        add_filter( 'wp_users_login_url', array( $this, 'login_url' ) );

        // disable default login url

        if ( $this->get_setting('disable_default_login')  && !isset( $_GET['interim-login'] ) ) {
            if ( ! is_admin()  ) {
                add_filter( 'login_url', array( $this, 'login_url' ) );
            }elseif ( defined( 'DOING_AJAX' )  ) {
                add_filter( 'login_url', array( $this, 'login_url' ) );
            }
        }

        /**
         * Filter Register url
         */
        add_filter( 'register_url', array( $this, 'register_url' ) );

        /**
         * Lost pwd url
         */
        add_filter( 'wp_users_lost_passoword_url', array( $this, 'lost_pwd_url' ) );
        add_filter( 'lostpassword_url', array( $this, 'lost_pwd_url' ) );


        add_action( 'wp_ajax_wp_users_ajax', array( $this, 'ajax' ) );
        add_action( 'wp_ajax_nopriv_wp_users_ajax', array( $this, 'ajax' ) );

	}

    /**
     * Setup plugin settings
     *
     * @since 1.0.0
     */
    private function settings( ) {

        $default = array(
            'account_page'          => '',
            'profile_rewrite'       => 'user',
            'disable_default_login' => '',
            'login_redirect_url'    => '',
            'logout_redirect_url'   => '',
            'show_term'             => '',
            'term_mgs'              => '',
            'view_other_profiles'        =>'any', // logged,
            'form_login_header'          => 0,
            'form_register_header'       => 0,
            'form_reset_header'          => 0,
            'form_change_pass_header'    => 0,
            'form_profile_header'        => 0,

            'login_header_title'         => '',
            'register_header_title'      => '',
            'reset_header_title'         => '',
            'change_pass_header_title'   => '',

            'upload_dir'                 =>  WP_CONTENT_DIR . '/uploads/wpus/',
            'upload_url'                 =>  WP_CONTENT_URL . '/uploads/wpus/'
        );

        $this->settings = (array) get_option( 'wp_users_settings' );
        $this->settings = wp_parse_args( $this->settings,  $default );

        if ( $this->settings['login_header_title'] == '' ) {
            $this->settings['login_header_title'] =  __( 'Login', 'wp-users' );
        }

        if ( $this->settings['register_header_title'] == '' ) {
            $this->settings['register_header_title'] =  __( 'Sign up', 'wp-users' );
        }

        if ( $this->settings['reset_header_title'] == '' ) {
            $this->settings['reset_header_title'] =  __( 'Reset your password', 'wp-users' );
        }

        if ( $this->settings['change_pass_header_title'] == '' ) {
            $this->settings['change_pass_header_title'] =  __( 'Change your password', 'wp-users' );
        }


        /**
         * The url of St User page
         * Change it in admin setting
         */
        $page_url =  ( $this->settings['account_page'] ) ? get_permalink( $this->settings['account_page'] ) : site_url('/') ;

        $this->settings['url'] = $page_url;

        if ( $this->settings['logout_redirect_url'] == '' ) {
            $this->settings['logout_redirect_url'] = '';
        }

        if ( $this->settings['login_redirect_url'] != '' ) {
            $this->settings['login_redirect_url'] = $this->settings['url'];
        }

        $this->settings['lost_pwd_url'] = add_query_arg( array( 'wpu_action' => 'lost-pass' ), $page_url );
        $this->settings['register_url'] = add_query_arg( array( 'wpu_action' => 'register' ), $page_url );

        $this->settings['term_link'] = get_permalink( get_option( 'wp_users_term_page' ) );

        $this->settings['theme'] = apply_filters('wp_users_theme', 'smooth' ); ;

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
        return $this->get_setting( 'url' );
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
    public function logout_url( $url = '', $redirect = '' ) {
        if (  $redirect == '' ){
            $_redirect =  $this->get_setting( 'logout_redirect_url' );

            if ( $_redirect == '' ) {
                if ( ! defined('DOING_AJAX')) {
                    $_redirect = get_permalink();
                } else {

                }
            }

            if ( $_redirect != '' ) {
                $args = array( 'action' => 'logout' );
                if ( ! empty( $_redirect ) ) {
                    $args['redirect_to'] = urlencode( $_redirect );
                }

                $logout_url = add_query_arg( $args, site_url( 'wp-login.php', 'login' ) );
                $logout_url = wp_nonce_url( $logout_url, 'log-out' );
                return $logout_url;
            }

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
        return ( $this->get_setting( 'login_redirect_url' ) != '' ) ? $this->get_setting( 'login_redirect_url' ) : $url ;
    }

    /**
     * Set Plugin url it can be login/profile/register page
     *
     * @since 1.0.0
     * @param string $url
     * @return mixed
     */
    public function login_url( $url = '' ) {
        return $this->get_setting( 'url' );
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
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_wp_users() {
		return $this->wp_users;
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
     * or YOUR_THEME_DIR/templates/wpu/{$template}
     * or YOUR_THEME_DIR/wpu/{$template}
     *
     * @since 1.0
     * @param string $template
     * @return string
     */
    public function get_file_template( $template ='' ) {
        /**
         * Overridden template in your theme
         * YOUR_THEME_DIR/templates/{$template}
         * or YOUR_THEME_DIR/templates/wpu/{$template}
         * or YOUR_THEME_DIR/wpu/{$template}
         */
        $templates =  array(
            'templates/'.$template,
            'templates/wpu/'.$template,
            'wpu/'.$template,
        );

        if ( $overridden_template = locate_template( $templates ) ) {
            // locate_template() returns path to file
            // if either the child theme or the parent theme have overridden the template
            return $overridden_template;
        } else {
            // If neither the child nor parent theme have overridden the template,
            // we load the template from the 'templates' directory if this plugin
            return WPU_PATH . 'templates/'.$template;
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
        do_action( 'wp_users_before_content_load', $template , $custom_data );
        if ( is_file( $template ) ) {
            if ( is_array( $custom_data ) ) {
                extract( $custom_data);
            }
            //load_template();
            require $template  ;
        }
        do_action( 'wp_users_after_content_load', $template , $custom_data );
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

    /**
     * Get user media
     *
     * @param string $media_type
     * @param string $type url|path
     * @return bool|string
     */
    public static function get_user_media( $media_type = 'avatar', $type = 'url' ,  $user  = false ){
        if ( ! $user ) {
            $user =  wp_get_current_user();
        }
        $media_type =  strtolower( $media_type );
        $type = strtolower( $type );
        $media  = get_user_meta( $user->ID, 'wpu-'.$media_type , true );

        $r = false;

        if ( $media ) {
            $path = WP_Users()->settings['upload_dir'] . $media;
            if (file_exists($path)) {
                if ($type !== 'path') {
                    $r = WP_Users()->settings['upload_url'] . $media;
                } else {
                    $r = $path;
                }
            }
        }

        $r =  apply_filters( 'wp_users_get_user_media', $r, $media_type, $type, $user );
        if ( $r ) {
            $r =  add_query_arg( array( 't'=> uniqid() ), $r );
        }

        return $r;

    }

    /**
     * Ajax Handle
     * @since 1.0.0
     */
    public function  ajax( ) {
        $act = $_REQUEST['act'];
        switch ( $act ) {
            case 'login-template':
                echo $this->get_template_content( 'login.php' );
                break;
            case 'register-template':
                echo $this->get_template_content( 'register.php' ) ;
                break;
            case 'lostpwd-template':
                echo $this->get_template_content( 'lost-password.php' ) ;
                break;
            case 'reset-template':
                echo $this->get_template_content( 'reset.php' ) ;
                break;
            case 'change-pwd-template':
                echo $this->get_template_content( 'change-password.php' ) ;
                break;
            case 'profile-template':
                if ( ! is_user_logged_in() ) {
                    echo $this->get_template_content( 'login.php' );
                } else {
                    echo $this->get_template_content( 'profile.php' ) ;
                }
                break;
            case 'modal-template':
                echo $this->get_template_content( 'modal.php' ) ;
                break;
            case 'do_login':
                echo WP_Users_Action::do_login();
                break;
            case 'do_register':
                echo WP_Users_Action::do_register();
                break;
            case 'retrieve_password':
                echo WP_Users_Action::retrieve_password();
                break;
            case 'do_reset_pass':
                echo WP_Users_Action::reset_pass();
                break;
            case 'do_update_profile':
                echo WP_Users_Action::update_profile();
                break;
            case 'update_cover':
                echo WP_Users_Action::media_upload( 'cover' );
                break;
            case 'update_avatar':
                echo WP_Users_Action::media_upload( 'avatar' );
                break;
            case 'remove_media':
                // echo WP_Users_Action::media_upload( 'cover' );
                WP_Users_Action::remove_media( $_REQUEST['media_type'] );
                break;

        }
        exit();
    }

    public static function get_country_name( $code ){
        $country_list =  self::get_countries();
        foreach ( $country_list as $region ) {
            if ( isset( $region[ $code ] ) ) {
                return $region[ $code ];
            }
        }
        return false;
    }

    public static function get_countries(){

        $country_list = array(
            "Africa" => array(
                "DZ" => "Algeria",
                "AO" => "Angola",
                "BJ" => "Benin",
                "BW" => "Botswana",
                "BF" => "Burkina Faso",
                "BI" => "Burundi",
                "CM" => "Cameroon",
                "CV" => "Cape Verde",
                "CF" => "Central African Republic",
                "TD" => "Chad",
                "KM" => "Comoros",
                "CG" => "Congo - Brazzaville",
                "CD" => "Congo - Kinshasa",
                "CI" => "Côte d’Ivoire",
                "DJ" => "Djibouti",
                "EG" => "Egypt",
                "GQ" => "Equatorial Guinea",
                "ER" => "Eritrea",
                "ET" => "Ethiopia",
                "GA" => "Gabon",
                "GM" => "Gambia",
                "GH" => "Ghana",
                "GN" => "Guinea",
                "GW" => "Guinea-Bissau",
                "KE" => "Kenya",
                "LS" => "Lesotho",
                "LR" => "Liberia",
                "LY" => "Libya",
                "MG" => "Madagascar",
                "MW" => "Malawi",
                "ML" => "Mali",
                "MR" => "Mauritania",
                "MU" => "Mauritius",
                "YT" => "Mayotte",
                "MA" => "Morocco",
                "MZ" => "Mozambique",
                "NA" => "Namibia",
                "NE" => "Niger",
                "NG" => "Nigeria",
                "RW" => "Rwanda",
                "RE" => "Réunion",
                "SH" => "Saint Helena",
                "SN" => "Senegal",
                "SC" => "Seychelles",
                "SL" => "Sierra Leone",
                "SO" => "Somalia",
                "ZA" => "South Africa",
                "SD" => "Sudan",
                "SZ" => "Swaziland",
                "ST" => "São Tomé and Príncipe",
                "TZ" => "Tanzania",
                "TG" => "Togo",
                "TN" => "Tunisia",
                "UG" => "Uganda",
                "EH" => "Western Sahara",
                "ZM" => "Zambia",
                "ZW" => "Zimbabwe",
            ),
            "Americas" => array(
                "AI" => "Anguilla",
                "AG" => "Antigua and Barbuda",
                "AR" => "Argentina",
                "AW" => "Aruba",
                "BS" => "Bahamas",
                "BB" => "Barbados",
                "BZ" => "Belize",
                "BM" => "Bermuda",
                "BO" => "Bolivia",
                "BR" => "Brazil",
                "VG" => "British Virgin Islands",
                "CA" => "Canada",
                "KY" => "Cayman Islands",
                "CL" => "Chile",
                "CO" => "Colombia",
                "CR" => "Costa Rica",
                "CU" => "Cuba",
                "DM" => "Dominica",
                "DO" => "Dominican Republic",
                "EC" => "Ecuador",
                "SV" => "El Salvador",
                "FK" => "Falkland Islands",
                "GF" => "French Guiana",
                "GL" => "Greenland",
                "GD" => "Grenada",
                "GP" => "Guadeloupe",
                "GT" => "Guatemala",
                "GY" => "Guyana",
                "HT" => "Haiti",
                "HN" => "Honduras",
                "JM" => "Jamaica",
                "MQ" => "Martinique",
                "MX" => "Mexico",
                "MS" => "Montserrat",
                "AN" => "Netherlands Antilles",
                "NI" => "Nicaragua",
                "PA" => "Panama",
                "PY" => "Paraguay",
                "PE" => "Peru",
                "PR" => "Puerto Rico",
                "BL" => "Saint Barthélemy",
                "KN" => "Saint Kitts and Nevis",
                "LC" => "Saint Lucia",
                "MF" => "Saint Martin",
                "PM" => "Saint Pierre and Miquelon",
                "VC" => "Saint Vincent and the Grenadines",
                "SR" => "Suriname",
                "TT" => "Trinidad and Tobago",
                "TC" => "Turks and Caicos Islands",
                "VI" => "U.S. Virgin Islands",
                "US" => "United States",
                "UY" => "Uruguay",
                "VE" => "Venezuela",
            ),
            "Asia" => array(
                "AF" => "Afghanistan",
                "AM" => "Armenia",
                "AZ" => "Azerbaijan",
                "BH" => "Bahrain",
                "BD" => "Bangladesh",
                "BT" => "Bhutan",
                "BN" => "Brunei",
                "KH" => "Cambodia",
                "CN" => "China",
                "CY" => "Cyprus",
                "GE" => "Georgia",
                "HK" => "Hong Kong SAR China",
                "IN" => "India",
                "ID" => "Indonesia",
                "IR" => "Iran",
                "IQ" => "Iraq",
                "IL" => "Israel",
                "JP" => "Japan",
                "JO" => "Jordan",
                "KZ" => "Kazakhstan",
                "KW" => "Kuwait",
                "KG" => "Kyrgyzstan",
                "LA" => "Laos",
                "LB" => "Lebanon",
                "MO" => "Macau SAR China",
                "MY" => "Malaysia",
                "MV" => "Maldives",
                "MN" => "Mongolia",
                "MM" => "Myanmar [Burma]",
                "NP" => "Nepal",
                "NT" => "Neutral Zone",
                "KP" => "North Korea",
                "OM" => "Oman",
                "PK" => "Pakistan",
                "PS" => "Palestinian Territories",
                "YD" => "People's Democratic Republic of Yemen",
                "PH" => "Philippines",
                "QA" => "Qatar",
                "SA" => "Saudi Arabia",
                "SG" => "Singapore",
                "KR" => "South Korea",
                "LK" => "Sri Lanka",
                "SY" => "Syria",
                "TW" => "Taiwan",
                "TJ" => "Tajikistan",
                "TH" => "Thailand",
                "TL" => "Timor-Leste",
                "TR" => "Turkey",
                "TM" => "Turkmenistan",
                "AE" => "United Arab Emirates",
                "UZ" => "Uzbekistan",
                "VN" => "Vietnam",
                "YE" => "Yemen",
            ),
            "Europe" => array(
                "AL" => "Albania",
                "AD" => "Andorra",
                "AT" => "Austria",
                "BY" => "Belarus",
                "BE" => "Belgium",
                "BA" => "Bosnia and Herzegovina",
                "BG" => "Bulgaria",
                "HR" => "Croatia",
                "CY" => "Cyprus",
                "CZ" => "Czech Republic",
                "DK" => "Denmark",
                "DD" => "East Germany",
                "EE" => "Estonia",
                "FO" => "Faroe Islands",
                "FI" => "Finland",
                "FR" => "France",
                "DE" => "Germany",
                "GI" => "Gibraltar",
                "GR" => "Greece",
                "GG" => "Guernsey",
                "HU" => "Hungary",
                "IS" => "Iceland",
                "IE" => "Ireland",
                "IM" => "Isle of Man",
                "IT" => "Italy",
                "JE" => "Jersey",
                "LV" => "Latvia",
                "LI" => "Liechtenstein",
                "LT" => "Lithuania",
                "LU" => "Luxembourg",
                "MK" => "Macedonia",
                "MT" => "Malta",
                "FX" => "Metropolitan France",
                "MD" => "Moldova",
                "MC" => "Monaco",
                "ME" => "Montenegro",
                "NL" => "Netherlands",
                "NO" => "Norway",
                "PL" => "Poland",
                "PT" => "Portugal",
                "RO" => "Romania",
                "RU" => "Russia",
                "SM" => "San Marino",
                "RS" => "Serbia",
                "CS" => "Serbia and Montenegro",
                "SK" => "Slovakia",
                "SI" => "Slovenia",
                "ES" => "Spain",
                "SJ" => "Svalbard and Jan Mayen",
                "SE" => "Sweden",
                "CH" => "Switzerland",
                "UA" => "Ukraine",
                "SU" => "Union of Soviet Socialist Republics",
                "GB" => "United Kingdom",
                "VA" => "Vatican City",
                "AX" => "Åland Islands",
            ),
            "Oceania" => array(
                "AS" => "American Samoa",
                "AQ" => "Antarctica",
                "AU" => "Australia",
                "BV" => "Bouvet Island",
                "IO" => "British Indian Ocean Territory",
                "CX" => "Christmas Island",
                "CC" => "Cocos [Keeling] Islands",
                "CK" => "Cook Islands",
                "FJ" => "Fiji",
                "PF" => "French Polynesia",
                "TF" => "French Southern Territories",
                "GU" => "Guam",
                "HM" => "Heard Island and McDonald Islands",
                "KI" => "Kiribati",
                "MH" => "Marshall Islands",
                "FM" => "Micronesia",
                "NR" => "Nauru",
                "NC" => "New Caledonia",
                "NZ" => "New Zealand",
                "NU" => "Niue",
                "NF" => "Norfolk Island",
                "MP" => "Northern Mariana Islands",
                "PW" => "Palau",
                "PG" => "Papua New Guinea",
                "PN" => "Pitcairn Islands",
                "WS" => "Samoa",
                "SB" => "Solomon Islands",
                "GS" => "South Georgia and the South Sandwich Islands",
                "TK" => "Tokelau",
                "TO" => "Tonga",
                "TV" => "Tuvalu",
                "UM" => "U.S. Minor Outlying Islands",
                "VU" => "Vanuatu",
                "WF" => "Wallis and Futuna",
            ),
        );

        return $country_list;
    }

}
