<?php
$bokun = new Bokun_data_importer();

if ( ! $bokun->has_settings_ok() ) {
	?>
    <p>
		<?php _e( 'Please setup bokun access key and secret key in settings => bokun settings to enable automatic data import for this post.', $bokun->text_domain ); ?>
    </p>
	<?php
} else {
	?>
    <p><?php _e( 'Enter bokun ID of the product (this is found in Bokun.io).', $this->text_domain ); ?></p>
	<?php wp_nonce_field( basename( __FILE__ ), 'bdi_meta_box_nonce' ); ?>
    <label class="l" for="bdi_bokun_id">
		<?php _e( 'Bokun ID', $this->text_domain ) ?>
    </label>
    <input type="text" name="_bdi_bokun_id" id="bdi_bokun_id"
           value="<?php echo get_post_meta( get_the_ID(), '_bdi_bokun_id', true ); ?>">
    <p>
		<?php echo sprintf( __( 'Automatic data import is scheduled next on %s', TEXT_DOMAIN ), $bokun->get_datetime_with_timezone_offset() ) ?>
    </p>
	<?php
}