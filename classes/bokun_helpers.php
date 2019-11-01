<?php

class Bokun_helpers {
	public static function update_bokun_content( $post_id, $data ) {
		var_dump($post_id);
		if ( $post_id === null ) {
			return;
		}

		$basic_post_content = [
			'ID'           => $post_id,
			'post_title'   => $data->title,
			'post_content' => self::get_post_content( $data ),
			'post_excerpt' => $data->excerpt,
		];
		wp_update_post( $basic_post_content );

		//Create meta data
		//$this->update_post_meta_details_for_activity( $post_id, $data );
	}

	public static function get_post_content( $data ) {
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
}