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

}
