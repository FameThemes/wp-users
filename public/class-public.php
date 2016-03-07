<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://smooththemes.com
 * @since      1.0.0
 *
 * @package    WP_Users
 * @subpackage WP_Users/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    WP_Users
 * @subpackage WP_Users/public
 * @author     SmoothThemes
 */
class WP_Users_Public {

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
	 * @param      string    $wp_users       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */

    /**
     * Instance classs WP_Users
     * @since 1.0
     * @var WP_Users
     */
    private  $instance;

    /**
     * Current action of plugin
     * @since 1.0.0
     */
    private  $current_action;

	public function __construct( $instance ) {

        $this->instance = $instance;
        $this->current_action = isset( $_REQUEST['wpu_action'] ) ? sanitize_key( $_REQUEST['wpu_action'] ) : '';

		$this->wp_users = $this->instance->get_wp_users();
		$this->version = $this->instance->get_version();

        add_action( 'wp_users_profile_header', array( __CLASS__, 'profile_header' ), 15, 3 );
        add_action( 'wp_users_profile_before_form_body', array( __CLASS__, 'profile_sidebar' ), 15, 3 );
        add_action( 'wp_users_profile_form_body', array( __CLASS__, 'profile_content' ), 15, 3 );
        add_action( 'the_content', array( __CLASS__, 'account_content' ), 99  );
        add_action( 'wp_users_profile_meta', array( __CLASS__, 'socials' ), 15  );

	}

    /**
     * Filter account content
     *
     * @param $content
     * @return string
     */
    public  static function  account_content( $content ){
        // settings['account_page']
        $post = get_post();
        if (  is_page() && $post->ID == WP_Users()->settings['account_page'] ) {
            $content = do_shortcode('[wp_users]');
        }

        return $content;
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
		 * defined in WP_Users_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The WP_Users_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

        wp_register_style( $this->wp_users, WPU_URL.'public/assets/css/style.css' );

        if ( is_page( $this->instance->settings['account_page'] ) ) {
            wp_enqueue_style( 'dashicons' );
        }
        wp_enqueue_style( $this->wp_users );
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
		 * defined in WP_Users_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The WP_Users_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'json2' );

        wp_enqueue_script( $this->wp_users , WPU_URL.'public/assets/js/user.js', array('jquery'), '1.0', true  );

        wp_localize_script( $this->wp_users , 'WP_Users',
            apply_filters('wp_users_localize_script', array(
                'ajax_url'          => admin_url( 'admin-ajax.php' ),
                'current_action'    => $this->current_action,
                'is_current_user'   => WP_Users()->can_edit_profile(),
                'hide_txt'          => __('Hide','wp-users'),
                'show_txt'          => __('Show','wp-users'),
                'current_url'       => $_SERVER['REQUEST_URI'],
                'invalid_file_type' => esc_html__('This is not an allowed file type.','wp-users'),
                '_wpnonce'          => wp_create_nonce(),
                'cover_text'        => __('Cover image','wp-users'),
                'avatar_text'       => __('Avatar','wp-users'),
                'remove_text'       => __('Default','wp-users'),
                //'remove_avatar'     => __('Default','wp-users'),
                'upload_text'       => __('Upload Photo','wp-users'),
            ) )
        );

    }


    /**
     *  Display modal
     * @since 1.0
     */
    function modal() {
        // Show modal when user not logged in only
        if ( ! is_user_logged_in() ) {
            echo $this->instance->get_template_content( 'modal.php', array('current_action' => $this->current_action ) ) ;
        }
    }

    /**
     * Display profile header
     * @param $user
     */
    public static function profile_header( $user, $current_user, $action = '' ){
        $is_edit =  false;
        if ( 'edit' == $action && WP_Users()->is_current_user( $user, $current_user ) ) {
            $is_edit =  true;
        }

        $image_url = WP_Users()->get_user_media( 'cover', 'url',  $user );
        $avatar_url = WP_Users()->get_user_media( 'avatar', 'url',  $user );

        $is_avatar = true;

        $default_avatar_url = get_avatar_url( $user->user_email, array( 'size'=> 150, 'default'=> 'mystery' ) );

        if ( $avatar_url == '' ){
            $is_avatar = false;
            $avatar_url = $default_avatar_url;
        }

        ?>
        <div id="wpu-profile-cover" data-change="<?php echo $is_edit ? 'true' : 'false'; ?>" class="wpu-profile-cover coppic" <?php echo ( $image_url !='' ) ? ' style="background-image: url(\''.esc_attr( $image_url ).'\');"' : '';   ?> data-cover="<?php echo ( $image_url ) ? $image_url : '';  ?>"></div>

        <div class="wpu-profile-meta clear-fix">
            <div data-change="<?php echo $is_edit ? 'true' : 'false'; ?>"  <?php echo ( $avatar_url !='' ) ? ' style="background-image: url(\''.esc_attr( $avatar_url ).'\');"' : '';   ?> data-default="<?php echo esc_attr( $default_avatar_url ); ?>" data-cover="<?php echo ( $avatar_url && $is_avatar ) ? $avatar_url : '';  ?>" class="wpu-profile-avatar coppic"></div>

            <div class="wpu-profile-meta-info">
                <span class="wpu-display-name"><?php echo esc_html( $user->display_name ); ?></span>
                <div class="list-meta-info">
                    <?php
                    $country = get_user_meta( $user->ID, 'country', true );
                    $name = WP_Users()->get_country_name( $country );
                    if ( $name != '' ){
                    ?>
                    <span class="user-country">
                        <i class="dashicons dashicons-admin-site"></i>
                        <?php esc_html_e( $name ); ?>
                    </span>
                    <?php } ?>
                    <span class="user-join-date">
                        <i class="dashicons dashicons-calendar-alt"></i>
                        <?php
                        printf( __( 'Joined %s', 'wp-users' ),  date_i18n( get_option('date_format'), strtotime( $user->user_registered ) ) );
                        ?>
                    </span>
                </div>
            </div>

            <?php do_action( 'wp_users_profile_meta',  $user ); ?>

        </div>
        <?php
    }

    public static function socials( $user ){
        ?>
        <div class="wpu-socials">
            <?php if (  get_user_meta( $user->ID, 'facebook', true )   != '' ) {  ?>
                <a href="<?php echo esc_attr( get_user_meta( $user->ID, 'facebook', true ) ); ?>"><span class="dashicons dashicons-facebook-alt"></span></a>
            <?php } ?>
            <?php if (  get_user_meta( $user->ID, 'twitter', true )   != '' ) {  ?>
                <a href="<?php echo esc_attr( get_user_meta( $user->ID, 'twitter', true ) ); ?>"><span class="dashicons dashicons-twitter"></span></a>
            <?php } ?>
            <?php if (  get_user_meta( $user->ID, 'google', true )   != '' ) {  ?>
                <a href="<?php echo esc_attr( get_user_meta( $user->ID, 'google', true ) ); ?>"><span class="dashicons dashicons-googleplus"></span></a>
            <?php } ?>
        </div>
        <?php
    }

    /**
     * Display profile sidebar
     * @param $user
     */
    public static function profile_sidebar( $user, $current_user, $action = false ){
        $is_edit =  false;
        if ( 'edit' == $action && WP_Users()->is_current_user( $user, $current_user ) ) {
            $is_edit =  true;
        }
        $link =  WP_Users()->get_profile_link( $user );
        ?>
        <ul class="wpu-form-sidebar">
            <li class="<?php echo $is_edit ? '' : 'active'; ?>"><a class="wpu-profile-link" href="<?php echo $link; ?>"><?php _e( 'Public profile', 'wp-users' ); ?></a></li>
            <?php if ( WP_Users()->is_current_user( $user, $current_user ) ){ ?>
            <li class="<?php echo $is_edit ? 'active' : ''; ?>"><a class="st-edit-link" href="<?php echo WP_Users()->get_edit_profile_link( $user ); ?>"><?php _e( 'Edit profile', 'wp-users' ); ?></a></li>
            <?php } ?>
        </ul>
        <?php
    }

    public static function profile_content( $user, $current_user,  $action =  false ){

        $is_edit =  false;
        $is_current_user =  WP_Users()->is_current_user( $user, $current_user );
        if ( 'edit' == $action && $is_current_user ) {
            $is_edit =  true;
        }

        if ( ! $is_edit && $action == 'edit' ) {
            $action = '';
        }

        if ( ! $is_edit  &&  ( empty( $action ) || $action == ''  ) ) {
        ?>
        <div class="wpu-form-profile clear-fix"  >

            <div class="wpu-form-fields viewing-info">
                <p class="fieldset wpu_input wpuname">
                    <label class=""><?php _e( 'User Name:', 'wp-users' ); ?></label>
                    <span>
                        <?php echo esc_html( $user->user_login ); ?>
                    </span>
                </p>
                <?php if ( WP_Users()->is_current_user( $user, $current_user ) ){ ?>
                    <p class="fieldset wpu_input wpu-email">
                        <label class=""><?php _e( 'E-mail:', 'wp-users' ); ?></label>
                        <span>
                            <?php echo esc_html( $user->user_email ); ?>
                        </span>
                    </p>
                <?php } ?>

                <?php if (  get_user_meta( $user->ID, 'first_name', true ) != '' ){ ?>
                    <p class="fieldset wpu_input st-firstname">
                        <label class=""><?php _e( 'First Name:', 'wp-users' ); ?></label>
                        <span class="">
                            <?php
                            echo esc_html( get_user_meta( $user->ID, 'first_name', true ) ); ?>
                        </span>
                    </p>
                <?php } ?>

                <?php if (  get_user_meta( $user->ID, 'last_name', true ) != '' ){ ?>
                    <p class="fieldset wpu_input st-lastname">
                        <label class=""><?php _e( 'Last Name:', 'wp-users' ); ?></label>
                        <span class="">
                            <?php echo  esc_html( get_user_meta( $user->ID, 'last_name', true ) ); ?>
                        </span>
                    </p>
                <?php } ?>

                <?php if (  $user->display_name!= '' ){ ?>
                    <p class="fieldset wpu_input">
                        <label class=""><?php _e( 'Display Name:', 'wp-users' ); ?></label>
                        <span><?php  echo esc_html( $user->display_name );  ?></span>
                    </p>
                <?php } ?>

                <?php if ( $user->user_url  != '' ){ ?>
                    <p class="fieldset wpu_input st-website">
                        <label class="" for="signin-password"><?php _e( 'Website:', 'wp-users' ); ?></label>
                        <span class="">
                            <?php echo esc_html( $user->user_url ); ?>
                        </span>
                    </p>
                <?php } ?>

                <?php if (  get_user_meta( $user->ID, 'description', true ) != '' ){ ?>
                    <p class="fieldset wpu_input">
                        <label class=""><?php _e( 'Bio:', 'wp-users' ); ?></label>
                        <span>
                            <?php echo  esc_html( get_user_meta( $user->ID, 'description', true ) ); ?>
                        </span>
                    </p>
                <?php } ?>

            </div>
        </div>

        <?php
        } elseif ( $is_edit ) {
            self::settings( $user );
        }
    }

    /**
     * Display edit profile form
     *
     * @param $user
     */
    public static function settings( $user ){
        ?>
        <form class="wpu-form-profile wpu-form form ui" action="<?php echo site_url('/'); ?>" method="post" >
            <div class="wpu-msg <?php echo isset( $_REQUEST['wpu_profile_updated'] ) &&  $_REQUEST['wpu_profile_updated']  == 1 ? 'st-show' : ''; ?> ui success message">
                <i class="close icon right"></i>
                <div class="header"><?php _e( 'Your profile updated.', 'wp-users' ); ?></div>
            </div>
            <p class="wpu-msg wpu-errors-msg"></p>

            <div class="wpu-form-fields">
                <p class="fieldset wpu_input wpuname">
                    <label><?php _e( 'User Name', 'wp-users' ); ?></label>
                    <input value="<?php echo esc_attr( $user->user_login ); ?>" readonly="readonly" class="input full-width has-padding has-border" type="text"  placeholder="<?php echo esc_attr__( 'Your username', 'wp-users' ) ; ?>">
                </p>

                <p class="fieldset wpu_input wpu-email">
                    <label><?php _e( 'E-mail', 'wp-users' ); ?></label>
                    <input name="wp_users_data[user_email]" value="<?php echo esc_attr( $user->user_email ); ?>" class="full-width has-padding has-border" type="email" placeholder="<?php echo esc_attr__( 'E-mail', 'wp-users' ); ?>">
                    <span class="wpu-error-msg"></span>
                </p>

                <p class="fieldset wpu_input wpu-firstname">
                    <label><?php _e( 'First Name', 'wp-users' ); ?></label>
                    <input name="wp_users_data[first_name]" value="<?php echo esc_attr( get_user_meta( $user->ID, 'first_name', true ) ); ?>" class="input full-width has-padding has-border" type="text"  placeholder="<?php echo esc_attr__( 'First name', 'wp-users' ) ; ?>">
                </p>

                <p class="fieldset wpu_input wpu-lastname">
                    <label><?php _e( 'Last Name', 'wp-users' ); ?></label>
                    <input name="wp_users_data[last_name]" value="<?php echo esc_attr( get_user_meta( $user->ID, 'last_name', true ) ); ?>" class="input full-width has-padding has-border"  type="text"  placeholder="<?php echo esc_attr__('Last name','wp-users') ; ?>">
                </p>

                <p class="fieldset wpu_input wpu-input-display-name">
                    <label><?php _e( 'Display Name', 'wp-users' ); ?></label>
                    <input name="wp_users_data[display_name]" value="<?php echo esc_attr( $user->display_name ); ?>" class="input full-width has-padding has-border"  type="text"  placeholder="<?php echo esc_attr__( 'Display name','wp-users' ) ; ?>">
                </p>

                <p class="fieldset wpu_input wpu-website">
                    <label><?php _e( 'Website', 'wp-users' ); ?></label>
                    <input name="wp_users_data[user_url]" value="<?php echo esc_attr( $user->user_url ); ?>" class="input full-width has-padding has-border"  type="text"  placeholder="<?php echo esc_attr__( 'Website', 'wp-users' ) ; ?>">
                </p>

                <p class="fieldset wpu_input wpu-website">
                    <label><?php _e( 'Country', 'wp-users' ); ?></label>
                    <select name="wp_users_data[country]">
                        <option value=""><?php _e( 'Select your country', 'wp-users' ); ?></option>
                        <?php
                        $country = get_user_meta( $user->ID, 'country', true ) ;
                        foreach ( WP_Users()->get_countries() as $region_name => $region ) {
                            echo  '<optgroup label="'.esc_attr( $region_name ).'">';
                            foreach( $region as $code => $name ) {
                                echo  "<option ".selected( $country, $code, false )." value='".esc_attr( $code )."'>".esc_html( $name )."</option>";
                            }
                            echo '</optgroup>';
                        }
                        ?>
                    </select>
                </p>

                <p class="fieldset wpu_input wpu-pwd pass1">
                    <label><?php _e( 'New Password', 'wp-users' ); ?></label>
                    <span class="wpu-pwd-toggle">
                        <input name="wp_users_data[user_pass]" autocomplete="off" class="input full-width has-padding has-border" type="password"  placeholder="<?php echo esc_attr__( 'New Password', 'wp-users' ) ; ?>">
                        <a href="#0" class="hide-password"><?php _e('Show','wp-users') ?></a>
                     </span>
                    <span class="wpu-error-msg"></span>
                </p>
                <p class="fieldset wpu_input wpu-pwd pass2">
                    <label><?php _e( 'Comfirm New Password', 'wp-users' ); ?></label>
                    <span class="wpu-pwd-toggle">
                        <input name="wp_users_pwd2" autocomplete="off" class="input full-width has-padding has-border" type="password"  placeholder="<?php echo esc_attr__( 'Confirm New Password','wp-users' ) ; ?>">
                        <a href="#0" class="hide-password"><?php _e( 'Show', 'wp-users' ) ?></a>
                     </span>
                    <span class="wpu-error-msg"></span>
                </p>
                <p class="fieldset wpu_input wpu-bio">
                    <label><?php _e( 'Bio', 'wp-users' ); ?></label>
                    <textarea class="full-width" name="wp_users_data[description]"><?php echo esc_attr( get_user_meta( $user->ID, 'description', true ) ); ?></textarea>
                </p>
                <p class="fieldset wpu_input">
                    <label><?php _e( 'Facebook URL', 'wp-users' ); ?></label>
                    <input name="wp_users_data[facebook]" value="<?php echo esc_attr( get_user_meta( $user->ID, 'facebook', true ) ); ?>" class="input full-width has-padding has-border" type="text"  placeholder="<?php echo esc_attr__( 'Facebook URL', 'wp-users' ) ; ?>">
                </p>
                <p class="fieldset wpu_input">
                    <label><?php _e( 'Twitter URL', 'wp-users' ); ?></label>
                    <input name="wp_users_data[twitter]" value="<?php echo esc_attr( get_user_meta( $user->ID, 'twitter', true ) ); ?>" class="input full-width has-padding has-border" type="text"  placeholder="<?php echo esc_attr__( 'Twitter URL', 'wp-users' ) ; ?>">
                </p>
                <p class="fieldset wpu_input">
                    <label ><?php _e( 'Google+ URL', 'wp-users' ); ?></label>
                    <input name="wp_users_data[google]" value="<?php echo esc_attr( get_user_meta( $user->ID, 'google', true ) ); ?>" class="input full-width has-padding has-border" type="text"  placeholder="<?php echo esc_attr__( 'Google+ URL', 'wp-users' ) ; ?>">
                </p>
                <?php

                /**
                 * Hook to add more setting for profile if want
                 */
                do_action( 'wp_users_profile_more_fields', $user );
                ?>
                <p class="fieldset">
                    <input class="<?php echo esc_attr( apply_filters( 'wp_users_form_submit_btn_class', 'profile-submit button btn' ) ); ?>" type="submit" data-loading-text="<?php echo esc_attr__( 'Loading...', 'wp-users' ); ?>" value="<?php echo esc_attr__( 'Update Profile','wp-users' ); ?>">
                </p>
            </div>
        </form>
        <?php
    }

}
