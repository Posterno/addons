<?php
/**
 * Handles licenses registration, activation & deactivation of Posterno's premium addons.
 *
 * @package     posterno-addons
 * @copyright   Copyright (c) 2019, Sematico, LTD
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1.0
 */

namespace PosternoAddons;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Handles updates for premium addons.
 */
class License {

	/**
	 * Addon's file.
	 *
	 * @var string
	 */
	private $file;

	/**
	 * Addon's currently stored license.
	 *
	 * @var string
	 */
	private $license;

	/**
	 * Name of the addon.
	 *
	 * @var string
	 */
	private $addon_name;

	/**
	 * Addon ID number from the API.
	 *
	 * @var string
	 */
	private $addon_id;

	/**
	 * Addon shortname used to store various details.
	 *
	 * @var string
	 */
	private $addon_shortname;

	/**
	 * Addon's version.
	 *
	 * @var string
	 */
	private $version;

	/**
	 * Addon's author.
	 *
	 * @var string
	 */
	private $author;

	/**
	 * Api url.
	 *
	 * @var string
	 */
	private $api_url = 'http://pno.local';

	/**
	 * Get things started.
	 *
	 * @param string $file see above.
	 * @param string $addon_name see above.
	 * @param string $addon_id see above.
	 * @param string $version see above.
	 * @param string $author see above.
	 * @param string $_api_url see above.
	 */
	public function __construct( $file, $addon_name, $addon_id, $version, $author, $_api_url = null ) {

		$this->file       = $file;
		$this->addon_name = $addon_name;
		$this->addon_id   = $addon_id;
		$this->version    = $version;
		$this->author     = $author;

		if ( ! empty( $_api_url ) ) {
			$this->api_url = $_api_url;
		}

		$this->addon_shortname = 'pno_addon_' . preg_replace( '/[^a-zA-Z0-9_\s]/', '', str_replace( ' ', '_', strtolower( $this->addon_name ) ) );

		$this->register_addon();
		$this->hooks();

	}

	/**
	 * Register premium addons within the system.
	 *
	 * @return void
	 */
	private function register_addon() {

		add_filter(
			'pno_registered_premium_addons',
			function( $addons ) {

				$addons[ $this->addon_shortname ] = [
					'name' => $this->addon_name,
				];

				return $addons;

			}
		);

	}

	/**
	 * Load hooks for this addon.
	 *
	 * @return void
	 */
	public function hooks() {

		add_action( 'admin_init', [ $this, 'updater' ], 0 );
		add_action( 'admin_init', [ $this, 'activate_license' ] );

	}

	/**
	 * Trigger updates for the addon.
	 *
	 * @return void
	 */
	public function updater() {

		$license_key = pno_get_option( $this->addon_shortname, false );

		$edd_updater = new \EDD_SL_Plugin_Updater(
			$this->api_url,
			$this->file,
			array(
				'version' => $this->version,
				'license' => $license_key,
				'item_id' => $this->addon_id,
				'author'  => $this->author,
				'beta'    => false,
			)
		);

	}

	/**
	 * Activate a license.
	 *
	 * @return void
	 */
	public function activate_license() {

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( ! isset( $_POST['posterno_licenses_nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['posterno_licenses_nonce'], 'verify_posterno_licenses_form' ) ) {
			return;
		}

		$submitted_license = isset( $_POST['pno_licenses'][ $this->addon_shortname ] ) ? sanitize_text_field( $_POST['pno_licenses'][ $this->addon_shortname ] ) : false;

		$api_params = array(
			'edd_action' => 'activate_license',
			'license'    => $submitted_license,
			'item_name'  => rawurlencode( $this->addon_name ), // the name of our product in EDD
			'url'        => home_url(),
		);

		$response = wp_remote_post(
			$this->api_url,
			array(
				'timeout'   => 15,
				'sslverify' => false,
				'body'      => $api_params,
			)
		);

		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = esc_html__( 'An error occurred, please try again.' );
			}
		} else {

			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			if ( false === $license_data->success ) {
				switch ( $license_data->error ) {
					case 'expired':
						$message = sprintf(
							esc_html__( 'Your license key expired on %s.' ),
							date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
						);
						break;
					case 'disabled':
					case 'revoked':
						$message = esc_html__( 'Your license key has been disabled.' );
						break;
					case 'missing':
						$message = esc_html__( 'Invalid license.' );
						break;
					case 'invalid':
					case 'site_inactive':
						$message = esc_html__( 'Your license is not active for this URL.' );
						break;
					case 'item_name_mismatch':
						$message = sprintf( esc_html__( 'This appears to be an invalid license key for %s.' ), $this->addon_name );
						break;
					case 'no_activations_left':
						$message = esc_html__( 'Your license key has reached its activation limit.' );
						break;
					default:
						$message = esc_html__( 'An error occurred, please try again.' );
						break;
				}
			}
		}

		$base_url = admin_url( 'tools.php?page=posterno-tools&tab=licenses' );

		if ( ! empty( $message ) ) {
			$redirect = add_query_arg(
				array(
					'sl_activation' => 'false',
					'message'       => rawurlencode( $message ),
				),
				$base_url
			);
			wp_safe_redirect( $redirect );
			exit();
		}

		pno_update_option( $this->addon_shortname, $submitted_license );
		pno_update_option( $this->addon_shortname . '_status', sanitize_text_field( $license_data->license ) );

		$redirect = add_query_arg(
			array(
				'sl_activation' => 'true',
			),
			$base_url
		);
		wp_safe_redirect( $redirect );
		exit();

	}

}
