<?php

/**
 * Class WP_Users_Action
 * Handle all User actions
 * @since 1.0
 */
class WP_Users_Action{

    /**
     * Let user login
     *
     * @since 1.0
     *
     * @return string
     */
    public static function do_login() {
        $creds                  = array();
        $creds['user_login']    = $_POST['wp_usersname'];
        $creds['user_password'] = $_POST['wpu_pwd'];
        $secure_cookie          = '';
        $msgs                   = array();
        if ( trim( $creds['user_login'] ) == '' ) {
            $msgs['wp_usersname_email'] =  __( 'Invalid username or email.', 'wp-users');
        }

        if ( trim( $creds['user_password'] ) == '' ) {
            $msgs['wpu_pwd'] =  __( 'Please enter your password', 'wp-users');
        }

        if ( is_email( $creds['user_login'] ) ) {
            $u =  get_user_by('email', $creds['user_login'] );
            if ( ! $u ) {
                $msgs['wp_usersname_email'] =  __( 'This email does not exists.', 'wp-users');
            } else {
                $creds['user_login'] = $u->user_login;
            }
        }

        if ( ! empty( $msgs ) ) {
            return ( json_encode( $msgs ) );
        }

        $creds['remember'] = isset( $_POST['st_rememberme'] )  && $_POST['st_rememberme'] !='' ? true :  false;

        // If the user wants ssl but the session is not ssl, force a secure cookie.
        if ( ! empty( $creds['user_login'] ) && ! force_ssl_admin() ) {
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
                        $msgs['wp_usersname_email'] =  __('Invalid username.', 'wp-users');
                        break;
                    case 'incorrect_password':
                        $msgs['wpu_pwd'] =  __('The password you entered for the username <strong>admin</strong> is incorrect.', 'wp-users');
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
        $pwd_length =  apply_filters( 'wp_users_pwd_leng', 6 );
        if ( empty( $username ) || ! validate_username( $username ) ) {
            $msgs['wp_usersname'] = __('Invalidate username','wp-users');
        }
        if ( strlen( $pwd ) < $pwd_length ) {
            $msgs['st_password'] = sprintf( __('Please enter your password more than %s characters', 'wp-users'), $pwd_length );
        }
        if ( ! is_email( $email ) ) {
            $msgs['st_email'] =  __('Please enter a correct your email', 'wp-users');
        }

        // check if show term and term checked
        if ( WP_Users()->settings['show_term']  ) {
            if ( $args['st_accept_terms'] == '' ) {
                $msgs['accept_terms'] = __('You must agree our Terms and Conditions to continue', 'wp-users');
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
        if ( empty ( $_POST['wp_users_login'] ) ) {
            $errors['st_input_combo'] = __( 'Enter a username or e-mail address.' );
        } elseif ( is_email( $_POST['wp_users_login'] ) ) {
            $user_data = get_user_by( 'email', trim( $_POST['wp_users_login'] ) );
            if ( empty( $user_data ) )
                $errors['st_input_combo'] = __( 'There is no user registered with that email address.' );
        } else {
            $login = trim( $_POST['wp_users_login'] );
            $user_data = get_user_by( 'login', $login );
        }

        if ( ! $user_data ) {
            $errors['st_input_combo'] =  __( 'Invalid username or e-mail.' );
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
        $message .= __( 'If this was a mistake, just ignore this email and nothing will happen.' ) . "\r\n\r\n";
        $message .= __( 'To reset your password, visit the following address:' ) . "\r\n\r\n";

        $url = apply_filters( 'wp_users_url', network_site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login' ) );
        $url = remove_query_arg( array( 'action', 'key', 'login' ), $url );
        $url =  add_query_arg( array(
                                    'wpu_action' => 'rp',
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
            return __( 'The e-mail could not be sent.', 'wp-users' );
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

        if ( !isset( $_REQUEST['wpu_pwd'] )  || $_REQUEST['wpu_pwd']  == '' ) {
            $errors['pass1'] =  __( 'Please enter your password' ,'wp-users');
        }

        if ( isset($_REQUEST['wpu_pwd']) && $_REQUEST['wpu_pwd'] != $_REQUEST['wpu_pwd2'] ) {
            $errors['pass2'] =  __( 'The passwords do not match.' ,'wp-users');
        }

        $user = check_password_reset_key( $rp_key, $rp_login );

        if ( ! $user || is_wp_error( $user ) ) {
            if ( $user && $user->get_error_code() === 'expired_key' )
                $errors['error'] =  __( 'Your key is expired' ,'wp-users' );
            else
                $errors['error'] =  __( 'Your key is invalid' ,'wp-users' );

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
            reset_password($user, $_POST['wpu_pwd']);
            //<p class="message reset-pass">' . __( 'Your password has been reset.' )
            return 'changed';
        } else {
            return json_encode( $errors );
        }
    }

    public static function update_profile() {
        if ( ! is_user_logged_in() ) {
            $errors['error'] =  __( 'Please login to continue.' ,'wp-users');
            return json_encode( $errors );
        }

        $user_data =  wp_parse_args( $_POST['wp_users_data'] , array(
            'user_email' => '',
        ));
        $errors = array();

        $c_user =  wp_get_current_user();

        // check email
        if ( ! is_email( $user_data['user_email'] ) ) {
            $errors['st-email'] =  __( 'Invalid email.' ,'wp-users' );
        } else {
            $check_u =  get_user_by('email', $user_data['user_email'] );
            if ( !empty( $check_u ) ) {
                if ( $check_u->ID != $c_user->ID ) {
                    $errors['st-email'] = __( 'Sorry, that email address is already used by other account!', 'wp-users' ); // __( 'This email is used by other account.' ,'wp-users');
                }
            }
        }

        // check password if enter
        if ( isset( $user_data['user_pass'] ) && $user_data['user_pass'] != '' ) {
            $pass2  = isset( $_POST['wp_users_pwd2'] ) ? trim( $_POST['wp_users_pwd2'] ) : '';
            if ( $pass2 == '' ) {
                $errors['pass2'] =  __( 'Please enter your confirm password.' ,'wp-users' );
            }else if ( $user_data['user_pass'] != $pass2  ) {
                $errors['pass2'] =  __( 'The passwords do not match.' ,'wp-users' );
            }
        }

        // do no update pwd if it empty
        if ( isset( $user_data['user_pass'] ) &&  trim( $user_data['user_pass'] ) =='' ) {
            unset( $user_data['user_pass'] );
        }

        /**
         * Hook to add data
         */
        do_action( 'wp_users_update_profile', $user_data, $errors );
        $user_data = apply_filters( 'wp_users_update_profile_data', $user_data );
        $errors = apply_filters( 'wp_users_update_profile_errors', $errors );

        if ( !empty( $errors ) ) {
            return json_encode( $errors );
        }


        global $wpdb;

        $sql = "SHOW COLUMNS FROM ".$wpdb->users;
        $user_cols_db = $wpdb->get_results( $sql, ARRAY_A );

        // update for current user only
        $user_data['ID'] = $c_user->ID;
        $fields_not_used = array(
            'user_activation_key', 'user_registered', 'user_status'
        );
        $black_meta_keys = apply_filters( 'st_profile_back_meta_keys', array( 'wp_capabilities', 'wp_user_level', 'session_tokens', 'default_password_nag' ) );

        $table_users_data =  array();
        foreach ( $user_cols_db as $col ) {
            if ( ! in_array( $col['Field'], $fields_not_used ) ) {
                $user_cols[ $col['Field'] ] = $col['Field'];
                if ( isset ( $user_data[ $col['Field'] ] ) ) {
                    $table_users_data[ $col['Field'] ] = $user_data[ $col['Field'] ];
                    unset ( $user_data[ $col['Field']  ] );
                }
            }
        }

        foreach ( $black_meta_keys as $k ) {
            if ( isset( $user_data[ $k ] ) ) {
                unset( $user_data[ $k ] );
            }
        }

        $r = wp_update_user( $table_users_data );

        if ( is_wp_error( $r ) ) {
            $errors['error'] =  __( 'Something wrong, please try again.' ,'wp-users');
            return json_encode( $errors );
        } else {
            // Success!
            foreach( $user_data as $k => $v ) {
                if ( $v ) {
                    update_user_meta( $c_user->ID , $k, $v );
                } else {
                    delete_user_meta( $c_user->ID , $k );
                }
            }
        }


        return 'updated';

    }

    /**
     * Upload image form local
     *
     * @see  wp_upload_dir()
     * @see wp_handle_upload()
     *
     *
     * @return bool
     */
    public static function media_upload( $media_type = 'avatar' ){
        $dir = WP_Users()->settings['upload_dir'];
        $url = WP_Users()->settings['upload_url'];

        $media_type = sanitize_title( $media_type , 'avatar' );

        if ( ! is_user_logged_in() ){
            $response = Array(
                "status" => 'error',
                "message" => __( 'You not have permission to upload.', 'wp-users' )
            );
            return json_encode( $response );
        }

        $user =  wp_get_current_user();
        $sub_path = "{$user->ID}/";

        $image_path = $dir.$sub_path;
        $image_url  = $url.$sub_path;

        $allowed_exts = array( "gif", "jpeg", "jpg", "png", "GIF", "JPEG", "JPG", "PNG" );
        $temp = explode( ".", $_FILES["img"]["name"] );
        $extension = end( $temp );

        // check if is image
        if ( ! in_array( $extension, $allowed_exts ) )
        {
            $response = array(
                "status" => 'error',
                "message" => __( 'Please select an image file' , 'wp-users' ),
            );
            return json_encode( $response );
        }

        // Create Directory
        // Make sure we have an uploads directory.
        if ( ! wp_mkdir_p( $image_path ) ) {
            $response = Array(
                "status" => 'error',
                "message" => 'Can\'t upload File. No write Access'
            );
            return  json_encode( $response ) ;
        }


        if ( $_FILES["img"]["error"] > 0 )
        {
            $response = array(
                "status" => 'error',
                "message" => 'ERROR Code: '. $_FILES["img"]["error"],
            );
        }
        else
        {

            $filename = $_FILES["img"]["tmp_name"];
            $new_name = $media_type.'.'.$extension;
            $new_file = $image_path.$new_name;
            list($width, $height) = getimagesize( $filename );

            $move_new_file = @ move_uploaded_file( $filename, $new_file );

            if (  false === $move_new_file) {
                // The uploaded file could not be moved to
                $response = array(
                    "status" => 'error',
                    "message" => __( 'The uploaded file could not be moved', 'wp-users' ),
                );
            } else {
                // Set correct file permissions.
                $stat = stat( dirname( $new_file ));
                $perms = $stat['mode'] & 0000666;
                @ chmod( $new_file, $perms );

                $response = array(
                    "status" => 'success',
                    "url" => $image_url.$new_name.'?t='.uniqid(),
                    "width" => $width,
                    "height" => $height
                );
                update_user_meta( $user->ID, 'wpu-'.$media_type, $sub_path.$new_name );
                update_user_meta( $user->ID, 'wpu-'.$media_type.'-img', $sub_path.$new_name );
            }

        }

        return  json_encode( $response ) ;
    }

    public static function remove_media( $media_type = 'avatar' ){
        $user =  wp_get_current_user();

        $image_path = WP_Users()->get_user_media($media_type, 'path');
        $thumb_path = WP_Users()->get_user_media($media_type.'-img', 'path' );
        if ( file_exists( $image_path ) ){
            @unlink( $image_path );
        }

        if ( file_exists( $thumb_path ) ){
            @unlink( $thumb_path );
        }

        delete_user_meta( $user->ID, 'wpu-'.$media_type );
        delete_user_meta( $user->ID, 'wpu-'.$media_type.'-img');
    }


    /**
     * Crop avatar
     * wp_crop_image
     *
     *
     * @param string $media_type
     * @return mixed|string|void
     */
    public static function crop_media( $media_type = 'avatar' ){

        $image_url = WP_Users()->get_user_media($media_type);
        $edited_image_url = WP_Users()->get_user_media($media_type.'-img');
        if ( !$edited_image_url ) {
            $edited_image_url = $image_url;
        }

        return  array(
            "status" => 'success',
            "url" => $edited_image_url.'?t='.uniqid()
        );
        // Return

        /*

        $media_type = sanitize_title( $media_type , 'avatar' );

        if ( ! is_user_logged_in() ){
            $response = Array(
                "status" => 'error',
                "message" => __( 'You not have permission to edit image', 'wp-users' )
            );
            return json_encode( $response );
        }

        $img_edit_path =  WP_Users()->get_user_media('cover', 'path');

        if( ! $img_edit_path ) {
            $response = Array(
                "status" => 'error',
                "message" => __( 'File not exists', 'wp-users' )
            );
            return json_encode( $response );
        }

        $user =  wp_get_current_user();

        $dir = WP_Users()->settings['upload_dir'];
        $url = WP_Users()->settings['upload_url'];

        $sub_path = "{$user->ID}/".$media_type.'-img';

        $temp = explode( ".", $img_edit_path );
        $extension = end( $temp );
        $sub_path .= '.'.$extension;


        require_once(ABSPATH . 'wp-admin/includes/image.php');
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');


        // original sizes
        $imgInitW = $_POST['imgInitW'];
        $imgInitH = $_POST['imgInitH'];
        // resized sizes
        $imgW = $_POST['imgW'];
        $imgH = $_POST['imgH'];
        // offsets
        $imgY1 = $_POST['imgY1'];
        $imgX1 = $_POST['imgX1'];
        // crop box
        $cropW = $_POST['cropW'];
        $cropH = $_POST['cropH'];
        // rotation angle
        $angle = $_POST['rotation'];


        $settings_height = 150;

        $imgInitW = $_POST['imgInitW'];
        $imgInitH = $_POST['imgInitH'];
        // resized sizes
        $diff = ( $_POST['imgH'] / $imgInitH ) ;
        // offsets
        $imgY1 = $_POST['imgY1'];

        $src_y = $imgY1 + ( $imgY1 - $imgY1 * $diff );
        // crop box
        $cropH = $settings_height+( $diff * $settings_height );

        wp_delete_file( $dir.$sub_path );
        $cropped = wp_crop_image( $img_edit_path , 1, $src_y, $imgInitW, $imgInitH, $imgInitW, $cropH, true, $dir.$sub_path );

        if ( $cropped && ! is_wp_error( $cropped ) ) {
            $response = Array(
                "status" => 'success',
                'post' => $_POST,
                'diff' => $diff,
                'src_y' => $src_y,
                "url" => $url.$sub_path.'?t='.uniqid()
            );
            update_user_meta( $user->ID, 'wpu-'.$media_type.'-img', $sub_path );
        } else {
            $response = Array(
                "status" => 'error',
                "message" => __( 'Something went wrong', 'wp-users' )
            );
        }

        return json_encode($response);
        */
    }

}