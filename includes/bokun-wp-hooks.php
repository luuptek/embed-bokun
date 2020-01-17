<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Actions hooks to fire on custom bokun product content
 *
 */

add_action( 'embed_bokun_custom_product', 'embed_bokun_create_images_carousel', 5, 2 );
add_action( 'embed_bokun_custom_product', 'embed_bokun_create_title', 10, 2 );
add_action( 'embed_bokun_custom_product', 'embed_bokun_create_excerpt', 15, 2 );
add_action( 'embed_bokun_custom_product', 'embed_bokun_create_duration', 20, 2 );
add_action( 'embed_bokun_custom_product', 'embed_bokun_create_content_columns', 30, 2 );

/**
 * Action to fire on default product widget
 */
add_action('embed_bokun_default_product', 'embed_bokun_create_default_product', 10, 1);