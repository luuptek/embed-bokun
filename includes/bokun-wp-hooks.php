<?php

/**
 * Actions hooks to fire on custom bokun product content
 *
 */

add_action( 'bokun_wp_custom_product', 'bokun_wp_create_images_carousel', 5, 2 );
add_action( 'bokun_wp_custom_product', 'bokun_wp_create_title', 10, 2 );
add_action( 'bokun_wp_custom_product', 'bokun_wp_create_excerpt', 15, 2 );
add_action( 'bokun_wp_custom_product', 'bokun_wp_create_duration', 20, 2 );
add_action( 'bokun_wp_custom_product', 'bokun_wp_create_content_columns', 30, 2 );

/**
 * Action to fire on default product widget
 */
add_action('bokun_wp_default_product', 'bokun_wp_create_default_product', 10, 1);