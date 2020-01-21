<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 *
 * Class Bokun_auth
 */
class Bokun_auth {

	public $api_base_url = 'https://api.bokun.io';
	public $utc_datetime;
	public $bokun_access_key;
	public $bokun_secret_key;
	public $bokun_curl_header_string;
	public $bokun_json_path;
	public $bokun_http_method = 'GET';

	public function __construct( $http_method = 'GET', $json_path = null ) {
		$this->bokun_access_key         = get_option( 'embed_bokun_access_key' );
		$this->bokun_secret_key         = get_option( 'embed_bokun_secret_key' );
		$this->bokun_json_path          = $json_path;
		$this->bokun_http_method        = $http_method;
		$this->utc_datetime             = $this->get_date_in_utc();
		$this->bokun_curl_header_string = $this->get_request_headers_string();
	}

	/**
	 * Get the actual json content
	 *
	 * Use this function to get bokun data into array
	 *
	 * @return array|mixed|object
	 */
	public function get_bokun_data() {

		$request_uri  = $this->api_base_url . $this->bokun_json_path;
		$api_response = wp_remote_get( $request_uri, $this->get_curl_headers() );

		if ( ! is_wp_error( $api_response ) ) {
			if ( $api_response['response']['code'] === 200 ) {
				return json_decode( wp_remote_retrieve_body( $api_response ) );
			} else {
				return false;
			}
		} else {
			return false;
		}

	}

	/**
	 * Sets headers string for Bokun
	 * date in UTF . access key . http method . json path
	 *
	 * @return string
	 */
	public function get_request_headers_string() {
		return $this->utc_datetime . $this->bokun_access_key . $this->bokun_http_method . $this->bokun_json_path;
	}

	/**
	 * Get bokun signature
	 * - base64 encoded
	 * - use sha1 algorhytmn to set hash with secret key
	 *
	 * @return string
	 */
	private function get_bokun_signature() {
		$signature = hash_hmac( 'sha1', $this->bokun_curl_header_string, $this->bokun_secret_key, true );

		return base64_encode( $signature );
	}

	/**
	 * Get current timestamp in UTC
	 * @return false|string
	 */
	public function get_date_in_utc() {
		return gmdate( "Y-m-d H:i:s" );
	}

	/**
	 * Get curl http headers
	 *
	 * @return array
	 */
	private function get_curl_headers() {
		return [
			'headers' => [
				'Accept'            => 'application/json',
				'X-Bokun-AccessKey' => $this->bokun_access_key,
				'X-Bokun-Date'      => $this->utc_datetime,
				'X-Bokun-Signature' => $this->get_bokun_signature(),
			],
		];
	}


}
