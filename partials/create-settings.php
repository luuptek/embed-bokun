<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bokun = new Bokun_WP();
?>
<div class="wrap">
    <h1><?php _e( 'Bokun settings', $bokun->text_domain ); ?></h1>

    <form method="post" action="options.php">
		<?php settings_fields( $bokun->settings_group_name ); ?>
		<?php do_settings_sections( $bokun->settings_group_name ); ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row"><?php _e( 'Bokun access key', $bokun->text_domain ); ?></th>
                <td><input type="text" name="<?php echo $bokun->access_key_settings_name; ?>"
                           value="<?php echo esc_attr( get_option( $bokun->access_key_settings_name ) ); ?>"/></td>
            </tr>

            <tr valign="top">
                <th scope="row"><?php _e( 'Bokun secret key', $bokun->text_domain ); ?></th>
                <td><input type="text" name="<?php echo $bokun->secret_key_settings_name; ?>"
                           value="<?php echo esc_attr( get_option( $bokun->secret_key_settings_name ) ); ?>"/></td>
            </tr>

            <tr valign="top">
                <th scope="row"><?php _e( 'Booking channel ID (UUID)', $bokun->text_domain ); ?></th>
                <td><input type="text" name="<?php echo $bokun->booking_channel_settings_name; ?>"
                           value="<?php echo esc_attr( get_option( $bokun->booking_channel_settings_name ) ); ?>"/></td>
            </tr>

            <tr valign="top">
                <th scope="row"><?php _e( 'Currency unit', $bokun->text_domain ); ?></th>
                <td>
                    <input type="text" name="<?php echo $bokun->currency_unit_settings_name; ?>"
                           value="<?php echo esc_attr( get_option( $bokun->currency_unit_settings_name ) ); ?>"/>
                    <p class="description">
						<?php _e( 'Currency output sign when using custom widget.', $bokun->text_domain ) ?>
                    </p>
                </td>
            </tr>
        </table>

		<?php submit_button(); ?>

    </form>
</div>