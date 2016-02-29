<?php
/**
 * The Template for displaying Modal
 *
 * Override template in your theme by copying it to:
 * YOUR_THEME_DIR/templates/modal.php
 * or YOUR_THEME_DIR/templates/wpu/modal.php
 * or YOUR_THEME_DIR/wpu/modal.php
 *
 * @package 	ST-User/Templates
 * @version     1.0
 */

if ( ! isset( $current_action ) ) {
    $current_action = false;
}
?>
<div class="wpu-modal wpu-wrapper"> <!-- this is the entire modal form, including the background -->
    <div class="wpu-modal-container"> <!-- this is the container wrapper -->
        <?php
        echo $this->get_template_content( 'login.php', array( 'in_modal' => true ) );
        echo $this->get_template_content( 'register.php', array( 'in_modal' => true ) );
        echo $this->get_template_content( 'reset.php', array( 'in_modal' => true ) );
        if ( $current_action == 'rp' ) {
            echo $this->get_template_content( 'change-password.php', array('in_modal' => true ) );
        }
        ?>
        <a href="#0" class="st-close-form"><?php _e('Close','wp-users'); ?></a>
    </div> <!-- wpu-modal-container -->
</div> <!-- wpu-modal -->