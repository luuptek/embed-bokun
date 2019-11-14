<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Bokun_helpers {
	public static function update_bokun_content( $post_id, $data ) {
		if ( $post_id === null ) {
			return;
		}

		//Create meta data
		self::update_post_meta_details_for_activity( $post_id, $data );
	}

	private static function update_post_meta_details_for_activity( $post_id, $data ) {
		update_post_meta( $post_id, '_bokun_wp_product_api_response', $data );
		//update_post_meta( $post_id, '_bokun_activity_description', self::get_post_content( $data ) );
		//update_post_meta( $post_id, '_bokun_activity_vendor_title', $data->actualVendor->title );
		//update_post_meta( $post_id, '_bokun_activity_starting_price', $data->nextDefaultPrice );
		//update_post_meta( $post_id, '_bokun_activity_duration', $data->durationText );
		//update_post_meta( $post_id, '_bokun_activity_minimum_age', $data->minAge );
	}

	public static function get_description_content( $data ) {
		$content = $data->description;

		if ( ! empty( $data->included ) ) {
			$content .= '<h2>' . __( 'What\'s included?' ) . '</h2>' . $data->included;
		}

		if ( ! empty( $data->requirements ) ) {
			$content .= '<h2>' . __( 'Requirements' ) . '</h2>' . $data->requirements;
		}

		if ( ! empty( $data->attention ) ) {
			$content .= '<h2>' . __( 'Attention' ) . '</h2>' . $data->attention;
		}

		if ( ! empty( $data->excluded ) ) {
			$content .= '<h2>' . __( 'Exclusions' ) . '</h2>' . $data->excluded;
		}

		return $content;


	}

	public static function get_images_carousel_data( $data ) {
		$array = [];
		$base_url       = 'https://bokunprod.imgix.net';

		foreach ( $data->photos as $photo ) {
			$image_url              = $base_url . $photo->fileName . '?h=850&w=1500&fit=crop&crop=center&auto=format';
			array_push($array, $image_url);

		}

		return $array;
	}
}