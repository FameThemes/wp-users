<?php

/**
 * Filter the HTML attributes applied to a menu item's anchor element.
 *
 * @since 1.0.0
 * @since WP 3.6.0
 * @since WP 4.1.0 The `$depth` parameter was added.
 *
 * @param array $atts {
 *     The HTML attributes applied to the menu item's `<a>` element, empty strings are ignored.
 *
 *     @type string $title  Title attribute.
 *     @type string $target Target attribute.
 *     @type string $rel    The rel attribute.
 *     @type string $href   The href attribute.
 * }
 * @param object $item  The current menu item.
 * @param array  $args  An array of {@see wp_nav_menu()} arguments.
 * @param int    $depth Depth of menu item. Used for padding.
 */
function st_user_nav_menu_link_attributes( $atts, $item, $args = array(), $depth = false ){
    if ( get_post_meta( $item->ID, '_is_logout' , true ) == 'yes' ) {
        if ( is_user_logged_in() ) {
            //$title =  get_post_meta( $item->ID, '_logout_title', true );
            $atts['href']                   =  wp_logout_url( $atts['href'] );
            $atts['data-st-user-logout']    = 'true';
            $atts['class']                  = '';
        } else {
            //$title =  get_post_meta( $item->ID, '_logout_title', true );
            $atts['href']               =  wp_login_url( $atts['href'] );
            $atts['data-st-user-login'] = 'true';
            $atts['class']              = 'st-login-btn';
        }

    }
    return $atts;
}
add_filter( 'nav_menu_link_attributes','st_user_nav_menu_link_attributes', 99, 4 );
add_filter( 'megamenu_nav_menu_link_attributes','st_user_nav_menu_link_attributes', 99, 4 );


/** This filter is documented in wp-includes/post-template.php */
function st_user_nav_item_title( $title, $id ){
    if ( get_post_type( $id ) == 'nav_menu_item' ) {

        if ( get_post_meta( $id, '_is_logout', true ) == 'yes' ) {
            if ( is_user_logged_in() ){

                $new_title =  get_post_meta( $id, '_logout_title', true );
                return ( $new_title != '' ) ? $new_title :  $title;

            } else {
                $new_title =  get_post_meta( $id, '_login_title', true );
                return ( $new_title != '' ) ? $new_title :  $title;
            }
        }
    }
    return  $title;
}

add_filter( 'the_title', 'st_user_nav_item_title', 99, 2 );

/**
 * Check Menu item permissions
 *
 * @since 1.0.0
 *
 * @param (object) $item
 * @return bool
 */
function st_user_can_see_nav_item( $item ){
    /**
     * check menu condition by roles
     */
    $user_can_see = true;
    $who_can_see =  get_post_meta( $item->ID, '_see', true );
    if ( is_array( $who_can_see ) && count( $who_can_see ) ) {
        $user_can_see = false;
        $user = wp_get_current_user();
        foreach ( (array) $user->roles as $r ) {
            if ( isset( $who_can_see[ $r ] ) ) {
                $user_can_see = true;
            }
        }
    }

    /*
     * Hide Item when logged in if checked
     */
    if ( is_user_logged_in() ) {
        if ( 'yes' ==  get_post_meta( $item->ID, '_hide_loggedin', true ) ) {
            $user_can_see = false;
        }
    }

    return $user_can_see;
}



/**
 * Check User Menu Item conditions- Who can see this
 *
 * Filter Nav menu item output.
 *
 * @see Walker::start_el()
 *
 * @seee apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
 *
 * @since 1.0.0
 *
 * @param string $item_output Passed by reference. Used to append additional content.
 * @param object $item   Menu item data object.
 * @param int    $depth  Depth of menu item. Used for padding.
 * @param array  $args   An array of arguments. @see wp_nav_menu()
 */
function st_user_menu_item_output( $item_output, $item, $depth = false, $args = array() ) {
    if ( ! st_user_can_see_nav_item( $item ) ) {
        return '';
    }

    $output = apply_filters( 'st_user_menu_item', $item_output, $item, $depth , $args );
    return $output;
}

add_filter( 'walker_nav_menu_start_el', 'st_user_menu_item_output', 99, 4 );
add_filter( 'megamenu_walker_nav_menu_start_el', 'st_user_menu_item_output', 99, 4 );


/**
 * Filter the CSS class(es) applied to a menu item's list item element.
 *
 * @since 3.0.0
 * @since 4.1.0 The `$depth` parameter was added.
 *
 * @param array  $classes The CSS classes that are applied to the menu item's `<li>` element.
 * @param object $item    The current menu item.
 * @param array  $args    An array of {@see wp_nav_menu()} arguments.
 * @param int    $depth   Depth of menu item. Used for padding.
 */
function st_user_nav_menu_css_class( $classes, $item, $args = array(), $dept = false ){
    if ( ! st_user_can_see_nav_item(  $item ) ) {
        $classes['remove'] = 'js-remove-nav display-none hide';
    }
    return $classes;
}
add_filter( 'nav_menu_css_class', 'st_user_nav_menu_css_class', 99, 4 );
add_filter( 'megamenu_nav_menu_css_class', 'st_user_nav_menu_css_class', 99, 4 );


/**
 * Save update nav menu item
 *
 * @see hook do_action( 'wp_update_nav_menu_item', $menu_id, $menu_item_db_id, $args );
 * @see line 450: wp-includes/nav-menu.php
 *
 * @param $menu_id
 * @param $menu_item_db_id
 * @param $args
 */
function st_wp_update_nav_menu_item( $menu_id, $menu_item_db_id, $args ){
    $more_keys =   array(
        'menu-item-login-title'    => '',
        'menu-item-logout-title'   => '',
        'menu-item-see'            => '',
        'menu-item-is-logout'      => '',
        'menu-item-hide-loggedin'  => '',
    );

    $values = array();
    foreach ( $more_keys as $field => $v ) {
        $value = isset( $_POST[ $field ][ $menu_item_db_id ] ) ? $_POST[ $field ][ $menu_item_db_id ] : '';
        $option_name  =  str_replace( 'menu-item', '_', $field );
        $option_name  =  str_replace( '-', '_', $option_name );
        $option_name  =  str_replace( '__', '_', $option_name );
        $value = is_string( $value ) ? trim( $value ) : $value;
        update_post_meta( $menu_item_db_id, $option_name, $value );
        $values[ $option_name ] =  $value;
    }

    $old_classes = $args['menu-item-classes'];

    //var_dump( $classes );
    $classes = array();
    foreach ( $old_classes as $k => $v ) {
        $v = trim( ( string ) $v );
        if ( $v && strpos( $v, 'visible-') !== 0 ) {
            $classes[ $v ] = $v;
        }
    }

    if ( $values['_is_logout'] == 'yes' ) {
        $classes['is-logout-url'] = 'is-logout-url';
    } else {
        if ( isset( $classes['is-logout-url'] ) ) {
            unset( $classes['is-logout-url'] );
        }
    }

    if ( is_array( $values['_see'] ) ) {
        foreach ( $values['_see'] as $k => $v ){
            if ( $v != '' ){
                $classes['visible-'.$k] = 'visible-'.$k;
            } elseif ( isset( $classes['visible-'.$k] ) ) {
                unset( $classes['visible-'.$k] );
            }

        }
    }
    $classes =  array_unique( $classes );
    update_post_meta( $menu_item_db_id, '_menu_item_classes', $classes );
}
add_action( 'wp_update_nav_menu_item', 'st_wp_update_nav_menu_item', 16, 3 );




if( !class_exists('Walker_Nav_Menu_Edit') ){
    include_once (ABSPATH . 'wp-includes/nav-menu-template.php');
}

/**
 * Create HTML list of nav menu input items.
 *
 * @package WordPress
 * @since 1.0.0
 * @see Walker_Nav_Menu
 * @see Walker_Nav_Menu_Edit
 */
class ST_Walker_Nav_Menu_Edit extends Walker_Nav_Menu {
    /**
     * Starts the list before the elements are added.
     *
     * @see Walker_Nav_Menu::start_lvl()
     *
     * @since 3.0.0
     *
     * @param string $output Passed by reference.
     * @param int    $depth  Depth of menu item. Used for padding.
     * @param array  $args   Not used.
     */
    public function start_lvl( &$output, $depth = 0, $args = array() ) {}

    /**
     * Ends the list of after the elements are added.
     *
     * @see Walker_Nav_Menu::end_lvl()
     *
     * @since 3.0.0
     *
     * @param string $output Passed by reference.
     * @param int    $depth  Depth of menu item. Used for padding.
     * @param array  $args   Not used.
     */
    public function end_lvl( &$output, $depth = 0, $args = array() ) {}

    /**
     * Start the element output.
     *
     * @see Walker_Nav_Menu::start_el()
     * @since 3.0.0
     *
     * @param string $output Passed by reference. Used to append additional content.
     * @param object $item   Menu item data object.
     * @param int    $depth  Depth of menu item. Used for padding.
     * @param array  $args   Not used.
     * @param int    $id     Not used.
     */
    public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
        global $_wp_nav_menu_max_depth;
        $_wp_nav_menu_max_depth = $depth > $_wp_nav_menu_max_depth ? $depth : $_wp_nav_menu_max_depth;

        ob_start();
        $item_id = esc_attr( $item->ID );
        $removed_args = array(
            'action',
            'customlink-tab',
            'edit-menu-item',
            'menu-item',
            'page-tab',
            '_wpnonce',
        );

        $original_title = '';
        if ( 'taxonomy' == $item->type ) {
            $original_title = get_term_field( 'name', $item->object_id, $item->object, 'raw' );
            if ( is_wp_error( $original_title ) )
                $original_title = false;
        } elseif ( 'post_type' == $item->type ) {
            $original_object = get_post( $item->object_id );
            $original_title = get_the_title( $original_object->ID );
        }

        $classes = array(
            'menu-item menu-item-depth-' . $depth,
            'menu-item-' . esc_attr( $item->object ),
            'menu-item-edit-' . ( ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? 'active' : 'inactive' ),
        );

        $title = $item->title;

        if ( ! empty( $item->_invalid ) ) {
            $classes[] = 'menu-item-invalid';
            /* translators: %s: title of menu item which is invalid */
            $title = sprintf( __( '%s (Invalid)' ), $item->title );
        } elseif ( isset( $item->post_status ) && 'draft' == $item->post_status ) {
            $classes[] = 'pending';
            /* translators: %s: title of menu item in draft status */
            $title = sprintf( __('%s (Pending)'), $item->title );
        }

        $title = ( ! isset( $item->label ) || '' == $item->label ) ? $title : $item->label;

        $submenu_text = '';
        if ( 0 == $depth )
            $submenu_text = 'style="display: none;"';

        ?>
    <li id="menu-item-<?php echo $item_id; ?>" class="<?php echo implode(' ', $classes ); ?>">
        <dl class="menu-item-bar">
            <dt class="menu-item-handle">
                <span class="item-title"><span class="menu-item-title"><?php echo esc_html( $title ); ?></span> <span class="is-submenu" <?php echo $submenu_text; ?>><?php _e( 'sub item' ); ?></span></span>
					<span class="item-controls">
						<span class="item-type"><?php echo esc_html( $item->type_label ); ?></span>
						<span class="item-order hide-if-js">
							<a href="<?php
                            echo wp_nonce_url(
                                add_query_arg(
                                    array(
                                        'action'    => 'move-up-menu-item',
                                        'menu-item' => $item_id,
                                    ),
                                    remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) )
                                ),
                                'move-menu_item'
                            );
                            ?>" class="item-move-up"><abbr title="<?php esc_attr_e('Move up'); ?>">&#8593;</abbr></a>
							|
							<a href="<?php
                            echo wp_nonce_url(
                                add_query_arg(
                                    array(
                                        'action' => 'move-down-menu-item',
                                        'menu-item' => $item_id,
                                    ),
                                    remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) )
                                ),
                                'move-menu_item'
                            );
                            ?>" class="item-move-down"><abbr title="<?php esc_attr_e('Move down'); ?>">&#8595;</abbr></a>
						</span>
						<a class="item-edit" id="edit-<?php echo $item_id; ?>" title="<?php esc_attr_e('Edit Menu Item'); ?>" href="<?php
                        echo ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? admin_url( 'nav-menus.php' ) : add_query_arg( 'edit-menu-item', $item_id, remove_query_arg( $removed_args, admin_url( 'nav-menus.php#menu-item-settings-' . $item_id ) ) );
                        ?>"><?php _e( 'Edit Menu Item' ); ?></a>
					</span>
            </dt>
        </dl>

        <div class="menu-item-settings" id="menu-item-settings-<?php echo $item_id; ?>">


            <?php if ( 'custom' == $item->type ) : ?>
                <p class="field-url description description-wide">
                    <label for="edit-menu-item-url-<?php echo $item_id; ?>">
                        <?php _e( 'URL' ); ?><br />
                        <input type="text" id="edit-menu-item-url-<?php echo $item_id; ?>" class="widefat code edit-menu-item-url" name="menu-item-url[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->url ); ?>" />
                    </label>
                </p>
            <?php endif; ?>

            <p class="description description-thin">
                <label for="edit-menu-item-title-<?php echo $item_id; ?>">
                    <?php _e( 'Navigation Label' ); ?><br />
                    <input type="text" id="edit-menu-item-title-<?php echo $item_id; ?>" class="widefat edit-menu-item-title" name="menu-item-title[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->title ); ?>" />
                </label>
            </p>

            <p class="description description-thin">
                <label for="edit-menu-item-attr-title-<?php echo $item_id; ?>">
                    <?php _e( 'Title Attribute' ); ?><br />
                    <input type="text" id="edit-menu-item-attr-title-<?php echo $item_id; ?>" class="widefat edit-menu-item-attr-title" name="menu-item-attr-title[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->post_excerpt ); ?>" />
                </label>
            </p>

            <?php
            //-------------------------------------------------
            // condition menu
            $id = uniqid('st_con_');

            $see = get_post_meta( $item_id, '_see', true );
            $see =  is_array( $see ) ? $see :  (array) $see ;
            ?>

            <script type="text/javascript">
                function stm_<?php echo $id; ?>( a ){
                    var o = jQuery( a );
                    var id = <?php echo json_encode( $id ); ?>;
                    console.debug( '#'+id  );
                    if ( o.is( ':checked' ) ) {
                        jQuery( '.more-title', jQuery( '#'+id ) ).show();
                    }else{
                        jQuery( '.more-title', jQuery( '#'+id ) ).hide();
                    }
                }
            </script>

            <div id="<?php echo $id; ?>" class="st-user-condition">
                <div class="field-link-target description description-wide">
                    <label style="font-style: italic;"><?php _e( 'Who can see this menu' ); ?></label><br/>
                    <p class="description" style="clear: both; border: 1px solid #dfdfdf; padding: 7px;" >
                        <?php

                        $editable_roles = array_reverse( get_editable_roles() );

                        foreach ( $editable_roles as $role => $details ) {
                            $name = translate_user_role($details['name'] );
                            ?>

                            <label>
                                <input type="checkbox" id="edit-menu-item-see-<?php echo $item_id; ?>" value="<?php echo esc_attr($role); ?>" name="menu-item-see[<?php echo $item_id; ?>][<?php echo esc_attr($role); ?>]"<?php checked( isset( $see[$role] ) ? $see[$role] : false, $role ); ?> />
                                <?php echo $name; ?>
                            </label>
                            <br/>

                        <?php
                        }

                        ?>
                    </p>
                </div>

                <p class="description">
                    <label>
                        <input type="checkbox" id="edit-menu-item-is-logout-<?php echo $item_id; ?>" onclick="stm_<?php  echo $id; ?>(this);" value="yes" name="menu-item-is-logout[<?php echo $item_id; ?>]"<?php checked( get_post_meta( $item_id, '_is_logout', true ), 'yes' ); ?> />
                        <?php _e( 'Login/Logout link. If user is logged in this item will be logout link' ); ?>
                    </label>
                </p>

                <div class="more-title" style="<?php echo get_post_meta( $item_id, '_is_logout', true ) == 'yes' ? '' : ' display: none; '; ?>">
                    <p class="description description-thin">
                        <label for="edit-menu-login-title-<?php echo $item_id; ?>">
                            <?php _e( 'Login Label' ); ?><br />
                            <input type="text" id="edit-menu-login-title-<?php echo $item_id; ?>" class="widefat edit-menu-login-title" name="menu-item-login-title[<?php echo $item_id; ?>]" value="<?php echo esc_attr( get_post_meta( $item_id, '_login_title', true ) ); ?>" />
                        </label>
                    </p>

                    <p class="description description-thin">
                        <label for="edit-menu-item-logout-title-<?php echo $item_id; ?>">
                            <?php _e( 'Logout Label' ); ?><br />
                            <input type="text" id="edit-menu-item-logout-title-<?php echo $item_id; ?>" class="widefat edit-menu-item-logout-title" name="menu-item-logout-title[<?php echo $item_id; ?>]" value="<?php echo esc_attr( get_post_meta( $item_id, '_logout_title', true ) ); ?>" />
                        </label>
                    </p>
                    <br style="clear: both;"/>

                </div>

                <p class="description">
                    <label>
                        <input type="checkbox" id="edit-menu-item-hide-loggedin-<?php echo $item_id; ?>"  value="yes" name="menu-item-hide-loggedin[<?php echo $item_id; ?>]"<?php checked( get_post_meta( $item_id, '_hide_loggedin', true ), 'yes' ); ?> />
                        <?php _e( 'Hide item when user logged in' ); ?>
                    </label>
                </p>
                <br style="clear: both;"/>

            </div>
            <br style="clear: both;"/>

            <?php
            // END condition menu
            //-------------------------------------------------
            ?>
            <p class="field-link-target description">
                <label for="edit-menu-item-target-<?php echo $item_id; ?>">
                    <input type="checkbox" id="edit-menu-item-target-<?php echo $item_id; ?>" value="_blank" name="menu-item-target[<?php echo $item_id; ?>]"<?php checked( $item->target, '_blank' ); ?> />
                    <?php _e( 'Open link in a new window/tab' ); ?>
                </label>
            </p>
            <p class="field-css-classes description description-thin">
                <label for="edit-menu-item-classes-<?php echo $item_id; ?>">
                    <?php _e( 'CSS Classes (optional)' ); ?><br />
                    <input type="text" id="edit-menu-item-classes-<?php echo $item_id; ?>" class="widefat code edit-menu-item-classes" name="menu-item-classes[<?php echo $item_id; ?>]" value="<?php echo esc_attr( implode(' ', $item->classes ) ); ?>" />
                </label>
            </p>
            <p class="field-xfn description description-thin">
                <label for="edit-menu-item-xfn-<?php echo $item_id; ?>">
                    <?php _e( 'Link Relationship (XFN)' ); ?><br />
                    <input type="text" id="edit-menu-item-xfn-<?php echo $item_id; ?>" class="widefat code edit-menu-item-xfn" name="menu-item-xfn[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->xfn ); ?>" />
                </label>
            </p>
            <p class="field-description description description-wide">
                <label for="edit-menu-item-description-<?php echo $item_id; ?>">
                    <?php _e( 'Description' ); ?><br />
                    <textarea id="edit-menu-item-description-<?php echo $item_id; ?>" class="widefat edit-menu-item-description" rows="3" cols="20" name="menu-item-description[<?php echo $item_id; ?>]"><?php echo esc_html( $item->description ); // textarea_escaped ?></textarea>
                    <span class="description"><?php _e('The description will be displayed in the menu if the current theme supports it.'); ?></span>
                </label>
            </p>

            <p class="field-move hide-if-no-js description description-wide">
                <label>
                    <span><?php _e( 'Move' ); ?></span>
                    <a href="#" class="menus-move menus-move-up" data-dir="up"><?php _e( 'Up one' ); ?></a>
                    <a href="#" class="menus-move menus-move-down" data-dir="down"><?php _e( 'Down one' ); ?></a>
                    <a href="#" class="menus-move menus-move-left" data-dir="left"></a>
                    <a href="#" class="menus-move menus-move-right" data-dir="right"></a>
                    <a href="#" class="menus-move menus-move-top" data-dir="top"><?php _e( 'To the top' ); ?></a>
                </label>
            </p>

            <div class="menu-item-actions description-wide submitbox">
                <?php if ( 'custom' != $item->type && $original_title !== false ) : ?>
                    <p class="link-to-original">
                        <?php printf( __('Original: %s'), '<a href="' . esc_attr( $item->url ) . '">' . esc_html( $original_title ) . '</a>' ); ?>
                    </p>
                <?php endif; ?>
                <a class="item-delete submitdelete deletion" id="delete-<?php echo $item_id; ?>" href="<?php
                echo wp_nonce_url(
                    add_query_arg(
                        array(
                            'action' => 'delete-menu-item',
                            'menu-item' => $item_id,
                        ),
                        admin_url( 'nav-menus.php' )
                    ),
                    'delete-menu_item_' . $item_id
                ); ?>"><?php _e( 'Remove' ); ?></a> <span class="meta-sep hide-if-no-js"> | </span> <a class="item-cancel submitcancel hide-if-no-js" id="cancel-<?php echo $item_id; ?>" href="<?php echo esc_url( add_query_arg( array( 'edit-menu-item' => $item_id, 'cancel' => time() ), admin_url( 'nav-menus.php' ) ) );
                ?>#menu-item-settings-<?php echo $item_id; ?>"><?php _e('Cancel'); ?></a>
            </div>

            <input class="menu-item-data-db-id" type="hidden" name="menu-item-db-id[<?php echo $item_id; ?>]" value="<?php echo $item_id; ?>" />
            <input class="menu-item-data-object-id" type="hidden" name="menu-item-object-id[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->object_id ); ?>" />
            <input class="menu-item-data-object" type="hidden" name="menu-item-object[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->object ); ?>" />
            <input class="menu-item-data-parent-id" type="hidden" name="menu-item-parent-id[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->menu_item_parent ); ?>" />
            <input class="menu-item-data-position" type="hidden" name="menu-item-position[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->menu_order ); ?>" />
            <input class="menu-item-data-type" type="hidden" name="menu-item-type[<?php echo $item_id; ?>]" value="<?php echo esc_attr( $item->type ); ?>" />
        </div><!-- .menu-item-settings-->
        <ul class="menu-item-transport"></ul>
        <?php
        $output .= ob_get_clean();
    }

} // Walker_Nav_Menu_Edit

function st_user_menu_edit( $class_name = '' ){
    return 'ST_Walker_Nav_Menu_Edit';
}

add_filter('wp_edit_nav_menu_walker', 'st_user_menu_edit' , 26 );
