<?php
/**
 * Plugin Name: Bokun data importer
 * Description: Import Bokun data to WP automatically.
 * Version: 0.1
 * Author: Luuptek
 * Author URI: https://www.luuptek.fi
 * License: GPLv2
 */

/**
 * This plugin creates a metabox in post edit screen with a possibility to import data from Bokun
 */

/**
 * Security Note:
 * Consider blocking direct access to your plugin PHP files by adding the following line at the top of each of them,
 * or be sure to refrain from executing sensitive standalone PHP code before calling any WordPress functions.
 */
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

class Bokun_data_importer {

	/**
	 * Text domain for plugin
	 *
	 * @var string
	 */
	public $text_domain = 'bokun-data-importer';

	/**
	 * Settings group name
	 *
	 * @var string
	 */
	public $settings_group_name = 'bdi-settings-group';

	/**
	 * Access key settings name
	 *
	 * @var string
	 */
	public $access_key_settings_name = 'bdi_bdi_access_key';

	/**
	 * Secret key settings name
	 *
	 * @var string
	 */
	public $secret_key_settings_name = 'bdi_bdi_secret_key';

	/**
	 * Cron hook name
	 *
	 * @var string
	 */
	public $cron_hook_name = 'bdi_update_bokun_data';

	/**
	 * Constructor for class
	 * - init hooks
	 * - setup text domain
	 *
	 * Bokun_data_importer constructor.
	 */
	function __construct() {
		add_action( 'init', [ $this, 'initialize_hooks' ] );
		add_action( 'plugins_loaded', [ $this, 'load_text_domain' ] );
	}

	/**
	 * Init hooks
	 *
	 * 1. Create metabox for bokun id
	 * 2. Register settings for access key and secret key
	 * 3. Saving meta box value (bokun_id)
	 * 4. Create cron hook and add action for it
	 */
	public function initialize_hooks() {
		add_action( 'admin_menu', [ $this, 'create_meta_box' ] );
		add_action( 'admin_init', [ $this, 'register_settings' ] );
		add_action( 'save_post', [ $this, 'save_meta_box' ] );
		add_action( $this->cron_hook_name, [ $this, 'update_bokun_data_in_posts' ] );

		$this->register_cron_hook();
	}

	/**
	 * Register cron hook
	 */
	public function register_cron_hook() {
		// Make sure this event hasn't been scheduled
		if ( ! wp_next_scheduled( $this->cron_hook_name ) ) {
			// Schedule the event
			wp_schedule_event( time(), 'hourly', $this->cron_hook_name );
		}
	}

	/**
	 * Register settings
	 */
	public function register_settings() {
		register_setting( $this->settings_group_name, $this->access_key_settings_name );
		register_setting( $this->settings_group_name, $this->secret_key_settings_name );
	}

	/**
	 * Create settings page
	 */
	public function create_settings_page() {
		include_once plugin_dir_path( __FILE__ ) . 'partials/create-settings.php';
	}

	/**
	 * Update posts with Bokun data, where bokun id is applied
	 */
	public function update_bokun_data_in_posts() {
		if ( $this->has_settings_ok() ) {
			//Lets update...

		}
	}

	/**
	 * Create meta box for bokun id
	 */
	public function create_meta_box() {

		add_options_page(
			__( 'Bokun settings', $this->text_domain ),
			__( 'Bokun settings', $this->text_domain ),
			'edit_posts',
			'bdi-settings-page',
			[ $this, 'create_settings_page' ]
		);

		add_meta_box(
			'create_bokun_data_meta_box',
			__( 'Get Bokun data', $this->text_domain ),
			[ $this, 'create_bokun_data_importer_meta_box_content' ],
			apply_filters( 'bdi_support_post_types', [ 'post' ] ),
			'side',
			'low'
		);
	}

	/**
	 * Saving bokun id meta value
	 *
	 * @param $post_id
	 *
	 * @return mixed
	 */
	public function save_meta_box( $post_id ) {

		if ( ! isset( $_POST["bdi_meta_box_nonce"] ) || ! wp_verify_nonce( $_POST["bdi_meta_box_nonce"], basename( __FILE__ ) ) ) {
			return $post_id;
		}

		if ( ! current_user_can( "edit_post", $post_id ) ) {
			return $post_id;
		}

		if ( defined( "DOING_AUTOSAVE" ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		$fields = [
			'_bdi_bokun_id',
		];
		foreach ( $fields as $field ) {
			if ( array_key_exists( $field, $_POST ) ) {
				update_post_meta( $post_id, $field, sanitize_text_field( $_POST[ $field ] ) );
			}
		}
	}

	/**
	 * Creating meta box content
	 */
	public function create_bokun_data_importer_meta_box_content() {
		include_once plugin_dir_path( __FILE__ ) . 'partials/create-meta-box.php';
	}

	/**
	 * HELPER FUNCTION
	 *
	 * To check if settings has been filled
	 *
	 * @return bool
	 */
	public function has_settings_ok() {
		$access_key = ! empty( get_option( $this->access_key_settings_name ) ) ? get_option( $this->access_key_settings_name ) : null;
		$secret_key = ! empty( get_option( $this->secret_key_settings_name ) ) ? get_option( $this->secret_key_settings_name ) : null;

		if ( $access_key === null || $secret_key === null ) {
			return false;
		} else {
			return true;
		}
	}

	/**
	 * HELPER FUNCTION
	 *
	 * To return date with readable format
	 *
	 * @return string
	 */
	public function get_datetime_with_timezone_offset() {
		$timezone_offset = ! empty( get_option( 'timezone_string' ) ) ? get_option( 'timezone_string' ) : 'UTC';
		$timestamp       = wp_next_scheduled( $this->cron_hook_name );

		$dt = new DateTime();
		$dt->setTimestamp( $timestamp );
		$dt->setTimezone( new DateTimeZone( $timezone_offset ) );
		$datetime = $dt->format( apply_filters( 'bdi_cron_date_dormat', 'd.m.Y H:i:s' ) );

		return $datetime;
	}

	/**
	 * Load text domain for lang versioning
	 */
	public function load_text_domain() {
		load_plugin_textdomain( $this->text_domain, false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
	}
}

$bokun_data_importer = new Bokun_data_importer();