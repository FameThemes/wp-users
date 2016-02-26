<?php

/**
 * Class ST_User_Action
 * Handle all User actions
 * @since 1.0
 */
class ST_User_Action{

    /**
     * Let user login
     *
     * @since 1.0
     *
     * @return string
     */
    public  static function do_login() {
        $creds                  = array();
        $creds['user_login']    = $_POST['st_username'];
        $creds['user_password'] = $_POST['st_pwd'];
        $secure_cookie          = '';
        $msgs                   = array();
        if ( trim( $creds['user_login'] ) == '' ) {
            $msgs['invalid_username'] =  __('<strong>ERROR</strong>: Invalid username or email.', 'st-user');
        }

        if ( trim( $creds['user_password'] ) == '' ) {
            $msgs['incorrect_password'] =  __('<strong>ERROR</strong>: The password you entered for the username <strong>admin</strong> is incorrect.', 'st-user');
        }

        if ( is_email( $creds['user_login'] ) ) {
            $u =  get_user_by('email', $creds['user_login'] );
            if ( ! $u ) {
                $msgs['invalid_username'] =  __('<strong>ERROR</strong>: This email does not exists.', 'st-user');
            } else {
                $creds['user_login'] = $u->user_login;
            }
        }

        if ( !empty( $msgs ) ) {
            return ( json_encode( $msgs ) );
        }

        $creds['remember'] = isset( $_POST['st_rememberme'] )  && $_POST['st_rememberme'] !='' ? true :  false;

        // If the user wants ssl but the session is not ssl, force a secure cookie.
        if ( ! empty($creds['user_login']) && ! force_ssl_admin() ) {
            $user_name = sanitize_user( $creds['user_login'] );
            if ( $user = get_user_by( 'login', $user_name ) ) {
                if ( get_user_option( 'use_ssl', $user->ID ) ) {
                    $secure_cookie = true;
                    force_ssl_admin( true );
                }
            }
        }
        
        $user = wp_signon( $creds, $secure_cookie );

        if ( is_wp_error( $user ) ) {
            $codes = $user->get_error_codes();

            foreach ( $codes as $code ) {
                switch( $code ) {
                    case 'invalid_username':
                        $msgs['invalid_username'] =  __('<strong>ERROR</strong>: Invalid username.', 'st-user');
                        break;
                    case 'incorrect_password':
                        $msgs['incorrect_password'] =  __('<strong>ERROR</strong>: The password you entered for the username <strong>admin</strong> is incorrect.', 'st-user');
                        break;
                }
            }
            return json_encode( $msgs );
        } else {
            return 'logged_success';
        }

    }

    /**
     * Register new account
     *
     * @since 1.0
     * @return int|string int if register success string json if failure
     */
    public static function do_register() {
        $args = wp_parse_args( $_POST, array(
            'st_signup_email'       => '',
            'st_signup_password'    => '',
            'st_signup_username'    => '',
            'st_accept_terms'       => '',
        ) );

        $email      = $args['st_signup_email'];
        $pwd        = $args['st_signup_password'];
        $username   = $args['st_signup_username'];

        $msgs = array();
        $pwd_length =  apply_filters('st_user_pwd_leng', 6 );
        if ( empty( $username ) || ! validate_username( $username ) ) {
            $msgs['invalidate_username'] = __('Invalidate username','st-user');
        }
        if ( strlen( $pwd ) < $pwd_length ) {
            $msgs['incorrect_password'] = sprintf( __('Please enter your password more than %s characters', 'st-user'), $pwd_length );
        }
        if ( ! is_email( $email ) ) {
            $msgs['incorrect_email'] =  __('Please enter a correct your email', 'st-user');
        }

        // check if show term and term checked
        if ( apply_filters('st_user_register_show_term_link' , true ) ) {
            if ($args['st_accept_terms'] == '' ) {
                $msgs['accept_terms'] = __('You must agree our Terms and Conditions to continue', 'st-user');
            }
        }

        // if data invalid
        if ( !empty ( $msgs ) ) {
            return json_encode( $msgs );
        }
        $r = wp_create_user( $username, $pwd , $email );
        if ( is_wp_error( $r ) ) {
            foreach ( (array) $r->errors as $code => $messages ) {
                $msgs[  $code ] = $messages[0];
            }
           return json_encode( $msgs );
        } else {
            // __('Registration complete. Please check your e-mail.');
            wp_new_user_notification( $r, $pwd );
            return $r;
        }

    }

    /**
     * Retrieve password
     *
     * @since 1.0
     *
     * @return string
     */
    public  static function retrieve_password() {
        global $wpdb, $wp_hasher;
        $errors =   array();
        $user_data =  false;
        if ( empty ( $_POST['st_user_login'] ) ) {
            $errors['invalid_combo'] = __( '<strong>ERROR</strong>: Enter a username or e-mail address.' );
        } elseif ( is_email( $_POST['st_user_login'] ) ) {
            $user_data = get_user_by( 'email', trim( $_POST['st_user_login'] ) );
            if ( empty( $user_data ) )
                $errors['invalid_combo'] = __( '<strong>ERROR</strong>: There is no user registered with that email address.' );
        } else {
            $login = trim( $_POST['st_user_login'] );
            $user_data = get_user_by( 'login', $login );
        }

        if ( ! $user_data ) {
            $errors['invalid_combo'] =  __( '<strong>ERROR</strong>: Invalid username or e-mail.' );
            return json_encode( $errors ) ;
        }

        // Redefining user_login ensures we return the right case in the email.
        $user_login = $user_data->user_login;
        $user_email = $user_data->user_email;


        /**
         * Fires before a new password is retrieved.
         *
         * @since 1.5.1
         *
         * @param string $user_login The user login name.
         */
        do_action( 'retrieve_password', $user_login );

        /**
         * Filter whether to allow a password to be reset.
         *
         * @since 2.7.0
         *
         * @param bool true           Whether to allow the password to be reset. Default true.
         * @param int  $user_data->ID The ID of the user attempting to reset a password.
         */
        $allow = apply_filters( 'allow_password_reset', true, $user_data->ID );

        if ( !$allow ) {
           $errors['msg'] = __('Password reset is not allowed for this user') ;
            return json_encode( $errors );
        }

        // Now insert the key, hashed, into the DB.
        if ( empty( $wp_hasher ) ) {
            require_once ABSPATH . WPINC . '/class-phpass.php';
            $wp_hasher = new PasswordHash( 8, true );
        }
        // Generate something random for a password reset key.
        $key = wp_generate_password( 20, false );
        $hashed = $wp_hasher->HashPassword( $key );

        /**
         * Fires when a password reset key is generated.
         *
         * @since 2.5.0
         *
         * @param string $user_login The username for the user.
         * @param string $key        The generated password reset key.
         */
        do_action( 'retrieve_password_key', $user_login, $key );

        $wpdb->update( $wpdb->users, array( 'user_activation_key' => $hashed ), array( 'user_login' => $user_login ) );

        $message = __('Someone requested that the password be reset for the following account:') . "\r\n\r\n";
        $message .= network_home_url( '/' ) . "\r\n\r\n";
        $message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
        $message .= __('If this was a mistake, just ignore this email and nothing will happen.') . "\r\n\r\n";
        $message .= __('To reset your password, visit the following address:') . "\r\n\r\n";

        $url = apply_filters( 'st_user_url', network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login') );
        $url = remove_query_arg( array( 'action', 'key', 'login' ), $url );
        $url =  add_query_arg( array(
                                    'st_action' => 'rp',
                                    'key'       => $key ,
                                    'login'     => $user_login ,
                                ), $url );

        $message .= '<' . $url . ">\r\n";

        if ( is_multisite() )
            $blogname = $GLOBALS['current_site']->site_name;
        else
            /*
             * The blogname option is escaped with esc_html on the way into the database
             * in sanitize_option we want to reverse this for the plain text arena of emails.
             */
            $blogname = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );

        $title = sprintf( __('[%s] Password Reset'), $blogname );

        /**
         * Filter the subject of the password reset email.
         *
         * @since 2.8.0
         *
         * @param string $title Default email title.
         */
        $title = apply_filters( 'retrieve_password_title', $title );

        /**
         * Filter the message body of the password reset mail.
         *
         * @since 2.8.0
         * @since 4.1.0 Added `$user_login` and `$user_data` parameters.
         *
         * @param string  $message    Default mail message.
         * @param string  $key        The activation key.
         * @param string  $user_login The username for the user.
         * @param WP_User $user_data  WP_User object.
         */
        $message = apply_filters( 'retrieve_password_message', $message, $key, $user_login, $user_data );

        if ( $message && !wp_mail( $user_email, wp_specialchars_decode( $title ), $message ) ) {
           // wp_die( __('The e-mail could not be sent.') . "<br />\n" . __('Possible reason: your host may have disabled the mail() function.') );
            $errors['msg'] = __('The e-mail could not be sent.');
            return json_encode($errors['msg']);
        }

        return 'sent';
    }

    /**
     * Reset password
     * @since 1.0
     * @return string
     */
    static function  reset_pass() {
        $rp_key =  isset( $_REQUEST['key'] ) ?  $_REQUEST['key'] : null;
        $rp_login = isset( $_REQUEST['login'] ) ?  $_REQUEST['login'] :  null;

        // check if not request change in modal
        if ( empty( $rp_key ) || empty ( $rp_login ) ) {
            $current_url = $_REQUEST['current_url'];
            $current_url =explode('?', $current_url );
            if ( count( $current_url ) > 1 ) {
                $current_url = $current_url[1];
            }
            $data = wp_parse_args( $current_url , array(
                'key'   =>'',
                'login' =>'',
            ) );
            $rp_key =  $data['key'];
            $rp_login =  $data['login'];
        }

        $errors =  array();

        if ( !isset( $_REQUEST['st_pwd'] )  || $_REQUEST['st_pwd']  == '' ) {
            $errors['pass1'] =  __( 'Please enter your password' ,'st-user');
        }

        if ( isset($_REQUEST['st_pwd']) && $_REQUEST['st_pwd'] != $_REQUEST['st_pwd2'] ) {
            $errors['pass2'] =  __( 'The passwords do not match.' ,'st-user');
        }

        $user = check_password_reset_key( $rp_key, $rp_login );

        if ( ! $user || is_wp_error( $user ) ) {
            if ( $user && $user->get_error_code() === 'expired_key' )
                $errors['error'] =  __( 'Your key is expired' ,'st-user');
            else
                $errors['error'] =  __( 'Your key is invalid' ,'st-user');

        }

        /**
         * Fires before the password reset procedure is validated.
         *
         * @since 3.5.0
         *
         * @param object           $errors WP Error object.
         * @param WP_User|WP_Error $user   WP_User object if the login and reset key match. WP_Error object otherwise.
         */
        do_action( 'validate_password_reset', $errors, $user );

        if ( empty( $errors )) {
            reset_password($user, $_POST['st_pwd']);
            //<p class="message reset-pass">' . __( 'Your password has been reset.' )
            return 'changed';
        } else {
            return json_encode( $errors );
        }
    }

    public static function update_profile() {
        if ( ! is_user_logged_in() ) {
            $errors['error'] =  __( 'Please login to continue.' ,'st-user');
            return json_encode( $errors );
        }

        $user_data =  wp_parse_args( $_POST['st_user_data'] , array(
            'user_email'        => '',
            'user_firstname'    => '',
            'user_lastname'     => '',
        ));
        $errors = array();

        $c_user =  wp_get_current_user();

        // check email
        if ( !is_email( $user_data['user_email'] ) ) {
            $errors['st-email'] =  __( 'Invalid email.' ,'st-user' );
        } else {
            $check_u =  get_user_by('email', $user_data['user_email'] );
            if ( !empty( $check_u ) ) {
                if ( $check_u->ID != $c_user->ID ) {
                    $errors['st-email'] = __( 'Sorry, that email address is already used by other account!', 'st-user' ); // __( 'This email is used by other account.' ,'st-user');
                }
            }
        }

        // check password if enter
        if ( isset( $user_data['user_pass'] ) && $user_data['user_pass'] != '' ) {
            $pass2  = isset( $_POST['st_user_pwd2'] ) ? trim( $_POST['st_user_pwd2'] ) : '';
            if ( $pass2 == '' ) {
                $errors['pass2'] =  __( 'Please enter your confirm password.' ,'st-user' );
            }else if ( $user_data['user_pass'] != $pass2  ) {
                $errors['pass2'] =  __( 'The passwords do not match.' ,'st-user' );
            }
        }

        // do no update pwd if it empty
        if ( isset( $user_data['user_pass'] ) &&  trim( $user_data['user_pass'] ) =='' ) {
            unset( $user_data['user_pass'] );
        }

        /**
         * Hook to add data
         */
        do_action( 'st_user_update_profile', $user_data, $errors );
        $user_data = apply_filters( 'st_user_update_profile_data', $user_data );
        $errors = apply_filters( 'st_user_update_profile_errors', $errors );

        if ( !empty( $errors ) ) {
            return json_encode( $errors );
        }

        // update for current user only
        $user_data['ID'] = $c_user->ID;
        $r = wp_update_user( $user_data );

        if ( is_wp_error( $r ) ) {
            $errors['error'] =  __( 'Something wrong, please try again.' ,'st-user');
            return json_encode( $errors );
        } else {
            // Success!
        }

        return 'updated';

    }

}