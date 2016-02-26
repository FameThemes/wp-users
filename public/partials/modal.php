<?php
/**
 * The Template for displaying Modal
 *
 * Override template in your theme by copying it to:
 * YOUR_THEME_DIR/templates/modal.php
 * or YOUR_THEME_DIR/templates/st-user/modal.php
 * or YOUR_THEME_DIR/st-user/modal.php
 *
 * @package 	ST-User/Templates
 * @version     1.0
 */

if ( ! isset( $current_action ) ) {
    $current_action = false;
}
?>
<div class="st-user-modal"> <!-- this is the entire modal form, including the background -->
    <div class="st-user-modal-container"> <!-- this is the container wrapper -->
        <ul class="st-switcher">
            <li><a href="#0"><?php _e( 'Sign in', 'st-user' );  ?></a></li>
            <li><a href="#0"><?php _e( 'New account','st-user' ); ?></a></li>
        </ul>
        <?php

        echo $this->get_template_content( 'login.php', array('in_modal' => true ) );
        echo $this->get_template_content( 'register.php', array('in_modal' => true ) );
        echo $this->get_template_content( 'reset.php', array('in_modal' => true ) );
        if ( $current_action == 'rp' ) {
            echo $this->get_template_content( 'change-password.php', array('in_modal' => true ) );
        }
        ?>
        <a href="#0" class="st-close-form"><?php _e('Close','st-user'); ?></a>
    </div> <!-- st-user-modal-container -->
</div> <!-- st-user-modal -->