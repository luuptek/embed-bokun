<?php
/**
 * Plugin Name: Embed Bokun
 * Description: Bokun plugin for WordPress.
 * Version: 0.23
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

class Bokun_WP {

	/**
	 * Text domain for plugin
	 *
	 * @var string
	 */
	public $text_domain = 'embed-bokun';

	/**
	 * Settings group name
	 *
	 * @var string
	 */
	public $settings_group_name = 'embed-bokun-settings-group';

	/**
	 * Access key settings name
	 *
	 * @var string
	 */
	public $access_key_settings_name = 'embed_bokun_access_key';

	/**
	 * Secret key settings name
	 *
	 * @var string
	 */
	public $secret_key_settings_name = 'embed_bokun_secret_key';

	/**
	 * Booking channel settings name
	 *
	 * @var string
	 */
	public $booking_channel_settings_name = 'embed_bokun_booking_channel_id';

	/**
	 * Currency unit settings name
	 *
	 * @var string
	 */
	public $currency_unit_settings_name = 'embed_bokun_currency_unit';

	/**
	 * Currency unit settings name
	 *
	 * @var string
	 */
	public $use_js_front_end_settings_name = 'embed_bokun_use_front_end_js';

	/**
	 * Cron hook name
	 *
	 * @var string
	 */
	public $cron_hook_name = 'embed_bokun_update_bokun_data';

	/**
	 * Array to query bokun posts
	 *
	 * @var array
	 */
	public $meta_query_array = [
		'post_type'      => 'any',
		'posts_per_page' => - 1,
		'meta_query'     => [
			[
				'key'     => '_embed_bokun_bokun_id',
				'compare' => 'EXISTS',
			]
		],
	];

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
	 * 5. Add assets
	 */
	public function initialize_hooks() {
		add_action( 'admin_menu', [ $this, 'create_meta_box' ] );
		add_action( 'admin_init', [ $this, 'register_settings' ] );
		add_action( 'save_post', [ $this, 'save_meta_box' ] );
		add_action( $this->cron_hook_name, [ $this, 'update_bokun_data_in_posts' ] );

		$this->register_cron_hook();
		$this->register_bokun_meta();
		$this->register_assets();
	}

	public function register_assets() {
		// Register block styles for both frontend + backend.
		wp_register_style(
			'bokun-product-widget-style-css', // Handle.
			plugins_url( 'dist/blocks.style.build.css', __FILE__ ), // Block style CSS.
			is_admin() ? array( 'wp-editor' ) : null, // Dependency to include the CSS after it.
			null // filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.style.build.css' ) // Version: File modification time.
		);

		// Register block editor script for backend.
		wp_register_script(
			'bokun-product-widget-block-js', // Handle.
			plugins_url( '/dist/blocks.build.js', __FILE__ ), // Block.build.js: We register the block here. Built with Webpack.
			array( 'jquery', 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor' ), // Dependencies, defined above.
			null, // filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.build.js' ), // Version: filemtime — Gets file modification time.
			true // Enqueue the script in the footer.
		);

		//Script for front end, only if setting ticked
		if ( get_option( $this->use_js_front_end_settings_name ) === '1' ) {
			wp_enqueue_script(
				'bokun-product-widget-block-js', // Handle.
				plugins_url( '/dist/blocks.build.js', __FILE__ ), // Block.build.js: We register the block here. Built with Webpack.
				array( 'jquery' ), // Dependencies, defined above.
				null, // filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.build.js' ), // Version: filemtime — Gets file modification time.
				true // Enqueue the script in the footer.
			);
		}

		// Register block editor styles for backend.
		wp_register_style(
			'bokun-product-widget-block-editor-css', // Handle.
			plugins_url( 'dist/blocks.editor.build.css', __FILE__ ), // Block editor CSS.
			array( 'wp-edit-blocks' ), // Dependency to include the CSS after it.
			null // filemtime( plugin_dir_path( __DIR__ ) . 'dist/blocks.editor.build.css' ) // Version: File modification time.
		);

		// WP Localized globals. Use dynamic PHP stuff in JavaScript via `cgbGlobal` object.
		wp_localize_script(
			'bokun-product-widget-block-js',
			'bokunWpGlobal', // Array containing dynamic data for a JS Global.
			[
				'pluginDirPath' => plugin_dir_path( __DIR__ ),
				'pluginDirUrl'  => plugin_dir_url( __DIR__ ),
				// Add more data here that you want to access from `cgbGlobal` object.
			]
		);

		/**
		 * Register Gutenberg block on server-side.
		 *
		 * Register the block on server-side to ensure that the block
		 * scripts and styles for both frontend and backend are
		 * enqueued when the editor loads.
		 *
		 * @link https://wordpress.org/gutenberg/handbook/blocks/writing-your-first-block-type#enqueuing-block-scripts
		 * @since 1.16.0
		 */
		register_block_type(
			'bokun/product-widget', array(
				// Enqueue blocks.style.build.css on both frontend & backend.
				'style'           => 'bokun-product-widget-style-css',
				// Enqueue blocks.build.js in the editor only.
				'editor_script'   => 'bokun-product-widget-block-js',
				// Enqueue blocks.editor.build.css in the editor only.
				'editor_style'    => 'bokun-product-widget-block-editor-css',
				'render_callback' => [ $this, 'render_callback_bokun_product' ],
			)
		);

		register_block_type(
			'bokun/product-list-widget', array(
				'render_callback' => [ $this, 'render_callback_bokun_product_list' ],
			)
		);
	}

	public function render_callback_bokun_product_list( $attributes ) {
		ob_start(); // Turn on output buffering

		embed_bokun_create_default_product_list( $attributes );

		$output = ob_get_contents(); // collect output
		ob_end_clean(); // Turn off ouput buffer

		return $output; // Print output
	}

	public function render_callback_bokun_product( $attributes ) {
		ob_start(); // Turn on output buffering

		//Padding fallback to medium if not set
		if ( ! isset( $attributes['paddingSize'] ) ) {
			$padding = 'medium-padding';
		} else {
			$padding = $attributes['paddingSize'];
		}

		if ( ! isset( $attributes['align'] ) ) {
			$attributes['align'] = '';
		}

		echo '<div class="wp-block-bokun-product-widget align' . esc_attr( $attributes['align'] ) . ' ' . esc_attr( $padding ) . '">';

		if ( ! isset( $attributes['useCustom'] ) ) {
			$attributes['useCustom'] = false;
		}

		if ( $attributes['useCustom'] ) {
			global $post;

			$data = get_post_meta( $post->ID, '_embed_bokun_product_api_response', true );

			if ( empty( $data ) ) {
				return esc_html__( 'WARNING, cannot show custom styled product! This post does not have api response saved to post meta.', 'embed-bokun' );
			}

			/**
			 * Hook: embed_bokun_before_custom_product
			 *
			 * @hook: none defined
			 */
			do_action( 'embed_bokun_before_custom_product', $data, $attributes );

			/**
			 * Hook: embed_bokun_custom_product
			 *
			 * @hook: embed_bokun_create_images_carousel - 5
			 * @hook: embed_bokun_create_title - 10
			 * @hook: embed_bokun_create_excerpt - 15
			 * @hook: embed_bokun_create_duration - 20
			 * @hook: embed_bokun_create_content_columns - 30
			 */
			do_action( 'embed_bokun_custom_product', $data, $attributes );

			/**
			 * Hook: embed_bokun_after_custom_product
			 *
			 * @hook: none defined
			 */
			do_action( 'embed_bokun_after_custom_product', $data, $attributes );
		} else {
			/**
			 * Hook: embed_bokun_default_product
			 *
			 * @hook: embed_bokun_create_default_product - 10
			 */
			do_action( 'embed_bokun_default_product', $attributes );
		}

		echo '</div>';

		$output = ob_get_contents(); // collect output
		ob_end_clean(); // Turn off ouput buffer

		return $output; // Print output
	}

	public function register_bokun_meta() {
		register_post_meta( '', '_embed_bokun_bokun_id', array(
			'show_in_rest'  => true,
			'single'        => true,
			'type'          => 'number',
			'auth_callback' => function () {
				return current_user_can( 'edit_posts' );
			}
		) );
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
		register_setting( $this->settings_group_name, $this->booking_channel_settings_name );
		register_setting( $this->settings_group_name, $this->currency_unit_settings_name );
		register_setting( $this->settings_group_name, $this->use_js_front_end_settings_name );
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
			$args = [
				'post_type'      => 'any',
				'posts_per_page' => - 1,
				'meta_query'     => $this->meta_query_array,
			];

			$posts = get_posts( $args );

			foreach ( $posts as $post ) {
				$bokun_id   = get_post_meta( $post->ID, '_embed_bokun_bokun_id', true );
				$bokun_auth = new Bokun_auth( 'GET', apply_filters( 'embed_bokun_post_update_api_path', '/activity.json/' . $bokun_id, $post->ID, $bokun_id ) );
				$data       = $bokun_auth->get_bokun_data();

				/**
				 * $bokun_auth->get_bokun_data() will return false, if not successful query
				 */
				if ( $data !== false ) {
					Bokun_helpers::update_bokun_content( $post->ID, $data );
				}
			}

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
			'embed-bokun-settings-page',
			[ $this, 'create_settings_page' ]
		);

		add_meta_box(
			'create_bokun_data_meta_box',
			__( 'Get Bokun data', $this->text_domain ),
			[ $this, 'create_bokun_data_importer_meta_box_content' ],
			apply_filters( 'embed_bokun_support_post_types', [ 'post' ] ),
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

		if ( ! isset( $_POST["embed_bokun_meta_box_nonce"] ) || ! wp_verify_nonce( $_POST["embed_bokun_meta_box_nonce"], 'verify_embed_bokun_nonce' ) ) {
			return $post_id;
		}

		if ( ! current_user_can( "edit_post", $post_id ) ) {
			return $post_id;
		}

		if ( defined( "DOING_AUTOSAVE" ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		$fields = [
			'_embed_bokun_bokun_id',
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
		$access_key      = ! empty( get_option( $this->access_key_settings_name ) ) ? get_option( $this->access_key_settings_name ) : null;
		$secret_key      = ! empty( get_option( $this->secret_key_settings_name ) ) ? get_option( $this->secret_key_settings_name ) : null;
		$booking_channel = ! empty( get_option( $this->booking_channel_settings_name ) ) ? get_option( $this->booking_channel_settings_name ) : null;

		if ( $access_key === null || $secret_key === null || $booking_channel === null ) {
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
		$datetime = $dt->format( apply_filters( 'embed_bokun_cron_date_dormat', 'd.m.Y H:i:s' ) );

		return $datetime;
	}

	/**
	 * Load text domain for lang versioning
	 */
	public function load_text_domain() {
		load_plugin_textdomain( $this->text_domain, false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
	}
}

// Require classes
include_once plugin_dir_path( __FILE__ ) . 'classes/bokun_auth.php';
include_once plugin_dir_path( __FILE__ ) . 'classes/bokun_helpers.php';
include_once plugin_dir_path( __FILE__ ) . 'includes/embed-bokun-hooks.php';
include_once plugin_dir_path( __FILE__ ) . 'includes/embed-bokun-functions.php';

$bokun_data_importer = new Bokun_WP();
