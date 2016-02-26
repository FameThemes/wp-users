<?php

/**
 * Get Template path
 *
 * Override template in your theme
 * YOUR_THEME_DIR/templates/{$template}
 * or YOUR_THEME_DIR/templates/st-user/{$template}
 * or YOUR_THEME_DIR/st-user/{$template}
 *
 * @since 1.0
 * @param string $template
 * @return string
 */
function st_user_get_template( $template = '' ) {
    /**
     * Overridden template in your theme
     * YOUR_THEME_DIR/templates/{$template}
     * or YOUR_THEME_DIR/templates/st-user/{$template}
     * or YOUR_THEME_DIR/st-user/{$template}
     */
    $templates =  array(
        'templates/'.$template,
        'templates/st-user/'.$template,
        'st-user/'.$template,
    );

    if ( $overridden_template = locate_template( $templates ) ) {
        // locate_template() returns path to file
        // if either the child theme or the parent theme have overridden the template
       return $overridden_template;
    } else {
        // If neither the child nor parent theme have overridden the template,
        // we load the template from the 'templates' directory if this plugin
        return ST_USER_PATH . 'public/partials/'.$template;
    }
}


/**
 * Get content form a file.
 *
 * @since 1.0
 * @param $template
 * @param array $custom_data
 * @return string
 */
function st_user_get_content( $template, $custom_data = array() ) {
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
 * Convert a array to HTML attributes
 *
 * @since 1.0
 *
 * @param $array
 * @return string
 */
function st_user_array_to_html_atts( $array ) {
    $attr_html = array();
    foreach ( $array as $k => $v ) {
        $k = sanitize_title( $k );
        $k = str_replace('_', '-', $k );
        if ( is_array( $v ) || is_object( $v ) ) {
            $v = json_encode( $v );
        }

        if ( ! in_array( $k , array( 'href','id','name','class', 'style' ) ) ) {
            $k = 'data-'.$k;
        }

        $attr_html[] = $k.'="'.esc_attr( $v ).'"';
    }

    return join( " ", $attr_html );

}

if ( ! function_exists( 'st_is_true' ) ) {
    /**
    * Check a var is true ?
    *
    * Returns TRUE for "1", "true", "on" and "yes"
    * Returns FALSE for "0", "false", "off" and "no"
    *
    * @param $val
    * @return bool
    */
    function st_is_true( $val ) {
        $boolval = ( is_string($val) ? filter_var( $val, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) : (bool) $val );
        return $boolval;
    }
}
