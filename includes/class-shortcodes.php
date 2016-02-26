<?php

/**
 * Shortcode of plugins
 *
 * @class ST_User_Shortcodes
 * @since 1.0
 */
class ST_User_Shortcodes{

    /**
     * Instance class ST_User
     * @since 1.0
     * @var ST_User
     */
    private  $instance;

    function __construct( $instance ) {
        $this->instance = $instance;
        add_shortcode( 'st_user',                  array( $this, 'user' ) );
        add_shortcode( 'st_user_login',            array( $this, 'login' ) );
        add_shortcode( 'st_user_register',         array( $this, 'register' ) );
        add_shortcode( 'st_user_lost_password',    array( $this, 'lost_password' ) );
        add_shortcode( 'st_user_reset_password',   array( $this, 'reset_password' ) );
        add_shortcode( 'st_user_change_password',  array( $this, 'change_password' ) );
        add_shortcode( 'st_user_profile',          array( $this, 'profile' ) );
        add_shortcode( 'st_login_btn',             array( $this, 'login_button' ) );
        add_shortcode( 'st_singup_btn',            array( $this, 'singup_button' ) );
    }

    /**
     * Login shortcode: Display Login form
     *
     * @usage: [st_user_login ajax_load="true" redirect=""]
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
        $html = '<div '.st_user_array_to_html_atts( $atts ).' class="st-user-wrapper st-login-wrapper">'.$content.'</div>';
        return $html;
    }

    /**
     * Register Shortcode: Display Register form
     * @usage: [st_user_register ajax_load="true" redirect=""]
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
        if ( st_is_true ( $atts['ajax_load'] ) ) {
            // leave content empty and load it via ajax
        } else {
            $content = $this->instance->get_template_content('register.php') ;
        }

        return '<div class="st-user-wrapper st-register-wrapper" ' . st_user_array_to_html_atts( $atts ).'>'.$content.'</div>';
    }

    /**
     * Lost password Shortcode: Display reset pwd form
     * @usage [st_user_reset_password ajax_load="true"]
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
        if ( st_is_true ( $atts['ajax_load'] ) ) {
            // leave content empty and load it via ajax
        } else {
            $content =  $this->instance->get_template_content('reset.php') ;
        }

        return '<div class="st-user-wrapper st-reset-password-wrapper" '.st_user_array_to_html_atts( $atts ).'>'.$content.'</div>';
    }

    /**
     * Change Password Shortcode: Display change pwd form
     * @usage [st_user_change_password ajax_load="true"]
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
        if ( st_is_true ( $atts['ajax_load'] ) ) {
            // leave content empty and load it via ajax
        } else {
            $content =  $this->instance->get_template_content('change-password.php') ;
        }

        return '<div class="st-user-wrapper st-change-password-wrapper" '.st_user_array_to_html_atts( $atts ).'>'.$content.'</div>';
    }

    /**
     *  User profile Shortcode: Display user profile
     * @usage [st_user_reset_password ajax_load="true"]
     * @since 1.0
     *
     * @param $atts
     * @param string $content
     * @return string
     */
    public function profile( $atts, $content = "" ) {
        // if user not logged in display login form instead
        if ( !is_user_logged_in() ) {
            return self::login( $atts, $content );
        }

        $atts = shortcode_atts(array(
            'ajax_load' => 'false' ,
        ), $atts );
        $atts['action'] = 'profile-template';
        extract( $atts );
        $content ='';
        if ( st_is_true ( $atts['ajax_load'] ) ) {
            // leave content empty and load it via ajax
        } else {
            $content =  $this->instance->get_template_content('profile.php') ;
        }
        return '<div class="st-user-wrapper st-profile-wrapper" '.st_user_array_to_html_atts( $atts ).'>'.$content.'</div>';
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
    public   function login_button( $atts ) {
        $atts = shortcode_atts(array(
            'class'         => '' ,
            'login_text'    => __('Login', 'st-user'),
            'logout_text'   => __("Logout", 'st-user'),
        ), $atts );
        extract( $atts );
        $atts['class'].=' st-login-btn';

        if ( is_user_logged_in() ) {
            $url = wp_logout_url();
            $atts['is_logged'] = 'true';
            $text = $logout_text;
        } else {
            $url =  wp_login_url();
            $atts['is_logged'] = 'false';
            $text = $login_text;
        }
        return  '<a href="'.$url.'" '.st_user_array_to_html_atts( $atts ).'>'.$text.'</a>';
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
            'text'              => __('Singup', 'st-user'),
            'ajax'              => 'true'
        ), $atts );
        extract( $atts );
        $atts['class'].=' st-singup-btn';

        if ( ! st_is_true( $ajax ) ) {
            $url = get_permalink();
            $url =  add_query_arg( array('logout'=>'true') , $url );
        } else {
            $url ='#';
        }
        return  '<a href="'.$url.'" '.st_user_array_to_html_atts( $atts ).'>'.$text.'</a>';
    }

    /**
     * Main Plugin shortcode
     * @since 1.0
     * @see login
     * @see profile
     * @see reset_password
     * @param $atts array that containers all attributes of other ST User shortcode
     * @param string $content
     * @return string
     */
    public  function user( $atts, $content ='' ) {
        if ( isset( $_REQUEST['st_action'] )  && $_REQUEST['st_action']  =='lost-pass' ) {
            return $this->reset_password( $atts, $content );
        }
        if ( isset( $_REQUEST['st_action'] )  && $_REQUEST['st_action']  =='register' ) {
            return $this->register( $atts, $content );
        }
        if ( is_user_logged_in() ) {
            return $this->profile( $atts, $content );
        } else {
            return $this->login( $atts, $content );
        }
    }

}

