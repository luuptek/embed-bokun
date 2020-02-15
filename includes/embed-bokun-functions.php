<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function embed_bokun_create_default_product( $attributes ) {
	?>
    <script type="text/javascript"
            src="https://widgets.bokun.io/assets/javascripts/apps/build/BokunWidgetsLoader.js?bookingChannelUUID=<?php echo $attributes['bookingChannelId'] ?>"
            async></script>

    <div class="bokunWidget"
         data-src="https://widgets.bokun.io/online-sales/<?php echo $attributes['bookingChannelId'] ?>/experience/<?php echo $attributes['productId'] ?>"></div>
    <noscript>Please enable javascript in your browser to book</noscript>
	<?php
}

function embed_bokun_create_default_product_list( $attributes ) {
	?>
    <div class="wp-block-bokun-product-list-widget align<?php echo $attributes['align']; ?>">
        <script type="text/javascript"
                src="https://widgets.bokun.io/assets/javascripts/apps/build/BokunWidgetsLoader.js?bookingChannelUUID=<?php echo $attributes['bookingChannelId'] ?>"
                async></script>

        <div class="bokunWidget"
             data-src="https://widgets.bokun.io/online-sales/<?php echo $attributes['bookingChannelId'] ?>/product-list/<?php echo $attributes['productListId'] ?>"></div>
        <noscript>Please enable javascript in your browser to book</noscript>
    </div>

	<?php
}

function embed_bokun_get_product_description( $data ) {
	return Bokun_helpers::get_description_content( $data );
}

function embed_bokun_get_carousel_images( $data ) {
	return Bokun_helpers::get_images_carousel_data( $data );
}

function embed_bokun_create_images_carousel( $data, $attributes ) {
	$images = embed_bokun_get_carousel_images( $data );

	if ( is_array( $images ) ) {
		if ( count( $images ) > 0 ) {
			echo '<div class="embed-bokun-product-images-carousel">';

			foreach ( $images as $image ) {
				?>
                <img src="<?php echo $image ?>"/>
				<?php
			}

			echo '</div>';
		}
	}
}

function embed_bokun_create_title( $data, $attributes ) {
	echo '<h2 class="wp-block-bokun-product-widget__title">' . $data->title . '</h2>';
}

function embed_bokun_create_excerpt( $data, $attributes ) {
	echo '<h3 class="wp-block-bokun-product-widget__excerpt">' . $data->excerpt . '</h3>';
}

function embed_bokun_create_duration( $data, $attributes ) {
	?>
    <div class="wp-block-bokun-product-widget__duration">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
            <path class="wp-block-bokun-product-widget__svg-path"
                  d="M256 48C141.1 48 48 141.1 48 256s93.1 208 208 208 208-93.1 208-208S370.9 48 256 48zm14 226c0 7.7-6.3 14-14 14h-96c-7.7 0-14-6.3-14-14s6.3-14 14-14h82V128c0-7.7 6.3-14 14-14s14 6.3 14 14v146z"/>
        </svg>
        <span class="wp-block-bokun-product-widget__duration-text"><?php echo sprintf( __( 'Duration: %s', 'embed-bokun' ), $data->durationText ) ?></span>
    </div>
	<?php
}

function embed_bokun_create_content_columns( $data, $attributes ) {
	?>
    <div class="wp-block-bokun-product-widget__content-row">
        <div class="wp-block-bokun-product-widget__content-row__column-left">
            <div class="wp-block-bokun-product-widget__bordered-content">
				<?php
				echo embed_bokun_get_product_description( $data );
				?>
            </div>
        </div>

        <div class="wp-block-bokun-product-widget__content-row__column-right">
            <div class="wp-block-bokun-product-widget__bordered-content">
                <h2 class="wp-block-bokun-product-widget__title wp-block-bokun-product-widget__title--booking">
					<?php _e( 'Book online', 'embed-bokun' ); ?>
                </h2>
                <script type="text/javascript"
                        src="https://widgets.bokun.io/assets/javascripts/apps/build/BokunWidgetsLoader.js?bookingChannelUUID=<?php echo $attributes['bookingChannelId']; ?>"
                        async></script>

                <div class="bokunWidget"
                     data-src="https://widgets.bokun.io/online-sales/<?php echo $attributes['bookingChannelId']; ?>/experience-calendar/<?php echo $data->id; ?>"></div>
                <noscript>Please enable javascript in your browser to book</noscript>

            </div>
        </div>
    </div>
	<?php
}

function embed_bokun_get_post_update_api_path( $post_id, $bokun_id ) {
	return Bokun_helpers::get_api_path( $bokun_id );
}