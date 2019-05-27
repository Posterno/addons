<?php
/**
 * Handles display of the addons page.
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
 * Handles display and content of the addons page.
 */
class Page {

	/**
	 * Get things started.
	 */
	public function __construct() {

	}

	/**
	 * Hook into WP.
	 *
	 * @return void
	 */
	public function hooks() {
		add_action( 'admin_menu', [ $this, 'admin_menu' ], 20 );
		add_action( 'admin_enqueue_scripts', [ $this, 'assets' ] );
	}

	/**
	 * Add new menu item.
	 *
	 * @return void
	 */
	public function admin_menu() {
		add_submenu_page( 'edit.php?post_type=listings', esc_html__( 'Posterno Extensions', 'posterno' ), esc_html__( 'Extensions', 'posterno' ), 'manage_options', 'posterno-addons', [ $this, 'display' ] );
	}

	/**
	 * Load assets.
	 *
	 * @return void
	 */
	public function assets() {

		$screen = get_current_screen();

		if ( $screen->id === 'edit-listings' ) {
			wp_enqueue_style( 'posterno-addons-listings-table', PNO_PLUGIN_URL . 'vendor/posterno/addons/dist/css/list-table.css', false, PNO_VERSION );
		}
	}

	/**
	 * Display content of the page.
	 */
	public function display() {
		echo 'adsasd';
	}

}
