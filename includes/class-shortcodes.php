<?php

/**
 * Shortcode of plugins
 *
 * @class WP_Users_Shortcodes
 * @since 1.0
 */
class WP_Users_Shortcodes{

    /**
     * Instance class WP_Users
     * @since 1.0
     * @var WP_Users
     */
    private  $instance;

    function __construct( $instance ) {
        $this->instance = $instance;
        add_shortcode( 'wp_users',                  array( $this, 'user' ) );
        add_shortcode( 'wp_users_login',            array( $this, 'login' ) );
        add_shortcode( 'wp_users_register',         array( $this, 'register' ) );
        add_shortcode( 'wp_users_lost_password',    array( $this, 'lost_password' ) );
        add_shortcode( 'wp_users_reset_password',   array( $this, 'reset_password' ) );
        add_shortcode( 'wp_users_change_password',  array( $this, 'change_password' ) );
        add_shortcode( 'wp_users_profile',          array( $this, 'profile' ) );
        add_shortcode( 'st_login_btn',             array( $this, 'login_button' ) );
        add_shortcode( 'st_singup_btn',            array( $this, 'singup_button' ) );
    }

    /**
     * Login shortcode: Display Login form
     *
     * @usage: [wp_users_login ajax_load="true" redirect=""]
     * @since 1.0
     * @param (array) $atts
     * @param string $content
     * @return string
     */
    function login( $atts, $content = "" ) {
        $atts = shortcode_atts(array(
            //'ajax_load'            => 'false' ,
            'login_button'           => '', // use login button instead login form
            'logout_redirect_url'    => '', // the url will redirect when user logout
            'login_redirect_url'     => '', // use url will redirect when user login
        ), $atts );

        if ( ! is_user_logged_in() ) {
            if ( $atts['login_button'] == 1 ) {
                return $this->login_button( $atts );
            }
        }

        $atts['action'] = 'login-template';
        extract( $atts );

        $content =  $this->instance->get_template_content( 'login.php', $atts ) ;
        $html = '<div '.wp_users_array_to_html_atts( $atts ).' class="wpu-wrapper wpu-login-wrapper">'.$content.'</div>';
        return $html;
    }

    /**
     * Register Shortcode: Display Register form
     * @usage: [wp_users_register ajax_load="true" redirect=""]
     * @since 1.0
     *
     * @param $atts
     * @param string $content
     * @return string
     */
    function register( $atts, $content = "" ) {
        // do not display the form when user loggin
        if ( is_user_logged_in() ) {
            return '';
        }

        $atts = shortcode_atts(array(
            'ajax_load' => 'false' ,
        ), $atts );
        $atts['action'] = 'register-template';
        extract( $atts );
        $content ='';
        if ( wpu_is_true ( $atts['ajax_load'] ) ) {
            // leave content empty and load it via ajax
        } else {
            $content = $this->instance->get_template_content('register.php') ;
        }

        return '<div class="wpu-wrapper st-register-wrapper" ' . wp_users_array_to_html_atts( $atts ).'>'.$content.'</div>';
    }

    /**
     * Lost password Shortcode: Display reset pwd form
     * @usage [wp_users_reset_password ajax_load="true"]
     * @since 1.0
     *
     * @param $atts
     * @param string $content
     * @return string
     */
    function reset_password( $atts, $content = "" ) {

        // do not display the form when user loggin
        if ( is_user_logged_in() ) {
            return '';
        }

        $atts = shortcode_atts(array(
            'ajax_load' => 'false' ,
            //'redirect' => '', // page id or url
        ), $atts );
        $atts['action'] = 'reset-template';
        extract( $atts );
        $content ='';
        if ( wpu_is_true ( $atts['ajax_load'] ) ) {
            // leave content empty and load it via ajax
        } else {
            $content =  $this->instance->get_template_content('reset.php') ;
        }

        return '<div class="wpu-wrapper wpu-reset-password-wrapper" '.wp_users_array_to_html_atts( $atts ).'>'.$content.'</div>';
    }

    /**
     * Change Password Shortcode: Display change pwd form
     * @usage [wp_users_change_password ajax_load="true"]
     * @since 1.0
     *
     * @param $atts
     * @param string $content
     * @return string
     */
    function change_password( $atts, $content = "" ) {

        // do not display the form when user loggin user profile form instead
        if ( is_user_logged_in() ) {
            return '';
        }

        $atts = shortcode_atts(array(
            'ajax_load' => 'false' ,
            //'redirect' => '', // page id or url
        ), $atts );
        $atts['action'] = 'change-pwd-template';
        extract( $atts );
        $content ='';
        if ( wpu_is_true ( $atts['ajax_load'] ) ) {
            // leave content empty and load it via ajax
        } else {
            $content =  $this->instance->get_template_content('change-password.php') ;
        }

        return '<div class="wpu-wrapper wpu-change-password-wrapper" '.wp_users_array_to_html_atts( $atts ).'>'.$content.'</div>';
    }

    /**
     *  User profile Shortcode: Display user profile
     * @usage [wp_users_reset_password ajax_load="true"]
     * @since 1.0
     *
     * @param $atts
     * @param string $content
     * @return string
     */
    public function profile( $atts, $content = "" ) {
        // if user not logged in display login form instead
        if ( $this->instance->settings['view_other_profiles'] != 'any' ) {
            if ( ! is_user_logged_in() ) {
                return self::login( $atts, $content );
            }
        }

        $atts = shortcode_atts(array(
            'ajax_load' => 'false' ,
        ), $atts );

        $atts['action'] = 'profile-template';

        extract( $atts );
        $content ='';
        if ( wpu_is_true ( $atts['ajax_load'] ) ) {
            // leave content empty and load it via ajax
        } else {
            $content =  $this->instance->get_template_content( 'profile.php' ) ;
        }
        return '<div class="wpu-wrapper wpu-profile-wrapper" '.wp_users_array_to_html_atts( $atts ).'>'.$content.'</div>';
    }

    /**
     * Login button shortcode
     *
     * @description Display a button/link if user logged in it will be logout button else it's login button
     *
     * @usage: [st_login_btn class="" login_text="" logout_text="" ]
     * @since 1.0
     *
     * @param $atts
     * @return string
     */
    public  function login_button( $atts ) {
        $atts = shortcode_atts(array(
            'class'         => '' ,
            'login_text'    => __('Login', 'wp-users'),
            'logout_text'   => __("Logout", 'wp-users'),
        ), $atts );
        extract( $atts );
        $atts['class'].=' wpu-login-btn';

        if ( is_user_logged_in() ) {
            $url = wp_logout_url();
            $atts['is_logged'] = 'true';
            $text = $logout_text;
        } else {
            $url =  wp_login_url();
            $atts['is_logged'] = 'false';
            $text = $login_text;
        }
        return  '<a href="'.$url.'" '.wp_users_array_to_html_atts( $atts ).'>'.$text.'</a>';
    }

    /**
     *  Display a singup button/link
     *
     * @description call a singup modal form
     * @since 1.0
     *
     * @param $atts
     * @return string
     */
    public  function singup_button( $atts ) {
        // disable when user logged in

        if ( is_user_logged_in() ) {
            return '';
        }
        $atts = shortcode_atts(array(
            'class'             => '' ,
            'hide_when_logged'  =>  'true' ,
            'text'              => __('Singup', 'wp-users'),
            'ajax'              => 'true'
        ), $atts );
        extract( $atts );
        $atts['class'].=' wpu-singup-btn';

        if ( ! wpu_is_true( $ajax ) ) {
            $url = get_permalink();
            $url =  add_query_arg( array('logout'=>'true') , $url );
        } else {
            $url ='#';
        }
        return  '<a href="'.$url.'" '.wp_users_array_to_html_atts( $atts ).'>'.$text.'</a>';
    }

    /**
     * Main Plugin shortcode
     * @since 1.0
     * @see login
     * @see profile
     * @see reset_password
     * @param $atts array that containers all attributes of other WP Users shortcode
     * @param string $content
     * @return string
     */
    public  function user( $atts, $content ='' ) {
        $user = WP_Users()->get_user_profile();

        if ( $user && $this->instance->settings['view_other_profiles'] == 'any' ) {
            return $this->profile( $atts, $content );
        } else{
            if ( isset( $_REQUEST['wpu_action'] )  && $_REQUEST['wpu_action']  == 'lost-pass' ) {
                return $this->reset_password( $atts, $content );
            }
            if ( isset( $_REQUEST['wpu_action'] )  && $_REQUEST['wpu_action']  == 'register' ) {
                return $this->register( $atts, $content );
            }
            if ( $user || is_user_logged_in() ) {
                return $this->profile( $atts, $content );
            } else {
                return $this->login( $atts, $content );
            }
        }

    }

}

