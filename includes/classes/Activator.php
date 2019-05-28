<?php
/**
 * Handles display of the licenses settings page.
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
 * Handles registration of premium addons within the licenses activation screen.
 */
class Activator {

	/**
	 * Hook into WordPress.
	 *
	 * @return void
	 */
	public function init() {
		add_filter( 'pno_tools_tabs', [ $this, 'register_tool' ] );
		add_action( 'pno_tools_licenses', [ $this, 'display' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'assets' ] );
	}

	/**
	 * Get the list of registered addons.
	 *
	 * @return array
	 */
	public function get_addons() {
		return apply_filters( 'pno_registered_premium_addons', [] );
	}

	/**
	 * Add a new tab to the tools page if there's at least 1 premium addon.
	 *
	 * @param array $tabs registered tabs.
	 * @return array
	 */
	public function register_tool( $tabs ) {

		if ( ! empty( $this->get_addons() ) ) {
			$tabs['licenses'] = esc_html__( 'Licenses' );
		}

		return $tabs;

	}

	/**
	 * Load assets for the page.
	 *
	 * @return void
	 */
	public function assets() {

		$screen = get_current_screen();

		if ( $screen->id === 'tools_page_posterno-tools' && isset( $_GET['tab'] ) && $_GET['tab'] === 'licenses' ) {
			wp_enqueue_style( 'posterno-licenses-panel', PNO_PLUGIN_URL . 'vendor/posterno/addons/dist/css/licenses-table.css', false, PNO_VERSION );
		}
	}

	/**
	 * Display the content of the page.
	 *
	 * @return void
	 */
	public function display() {

		$addons = $this->get_addons();

		include PNO_PLUGIN_DIR . 'vendor/posterno/addons/includes/views/licenses-page.php';

	}

}
