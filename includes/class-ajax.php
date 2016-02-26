<?php
/**
 * Class ST_User_Ajax
 * Handle ajax requests class
 * @since 1.0
 */
class ST_User_Ajax{
    /**
     * Instance class ST_User
     * @since 1.0
     * @var ST_User
     */
    private  $instance;

    function __construct( $instance ) {
        $this->instance = $instance;
    }
    /**
     * Handle ajax requests
     * @since 1.0
     */
    public function  ajax( ) {
        $act = $_REQUEST['act'];
        switch ( $act ) {
            case 'login-template':
                echo $this->instance->get_template_content('login.php');
                break;
            case 'register-template':
                echo $this->instance->get_template_content('register.php') ;
                break;
            case 'lostpwd-template':
                echo $this->instance->get_template_content('lost-password.php') ;
                break;
            case 'reset-template':
                echo $this->instance->get_template_content('reset.php') ;
                break;
            case 'change-pwd-template':
                echo $this->instance->get_template_content('change-password.php') ;
                break;
            case 'profile-template':
                if ( ! is_user_logged_in() ) {
                    echo $this->instance->get_template_content('login.php');
                } else {
                    echo $this->instance->get_template_content('profile.php') ;
                }
                break;
            case 'modal-template':
                echo $this->instance->get_template_content('modal.php') ;
                break;
            case 'do_login':
                echo ST_User_Action::do_login();
                break;
            case 'do_register':
                echo ST_User_Action::do_register();
                break;
            case 'retrieve_password':
                echo ST_User_Action::retrieve_password();
                break;
            case 'do_reset_pass':
                echo ST_User_Action::reset_pass();
                break;
            case 'do_update_profile':
                echo ST_User_Action::update_profile();
                break;
        }
        exit();
    }
}
