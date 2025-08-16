<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$bokun = new Bokun_WP();

if ( ! $bokun->has_settings_ok() ) {
	?>
    <p>
		<?php _e( 'Please setup Bokun details in settings => bokun settings to enable automatic data import for this post.', $bokun->text_domain ); ?>
    </p>
	<?php
} else {
	?>
    <p><?php _e( 'Enter bokun ID of the product (this is found in Bokun.io).', $this->text_domain ); ?></p>
	<?php wp_nonce_field( 'verify_embed_bokun_nonce', 'embed_bokun_meta_box_nonce' ); ?>
    <label class="l" for="embed_bokun_bokun_id">
		<?php _e( 'Bokun ID', $this->text_domain ) ?>
    </label>
    <input type="text" name="_embed_bokun_bokun_id" id="embed_bokun_bokun_id"
           value="<?php echo esc_attr( get_post_meta( get_the_ID(), '_embed_bokun_bokun_id', true ) ); ?>">
    <p>
		<?php echo sprintf( esc_html__( 'Automatic data import is scheduled next on %s', $this->text_domain ), $bokun->get_datetime_with_timezone_offset() ) ?>
    </p>
	<?php
}
