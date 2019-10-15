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
	 * Holds the url of the addons api.
	 *
	 * @var string
	 */
	public $api_url = null;

	/**
	 * Get things started.
	 */
	public function __construct() {
		$this->api_url = 'https://posterno.cdn.prismic.io/api/v2';
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
		add_submenu_page( 'edit.php?post_type=listings', esc_html__( 'Posterno Extensions', 'posterno' ), '<span class="dashicons dashicons-star-filled" style="font-size: 17px"></span> ' . esc_html__( 'Extensions', 'posterno' ), 'manage_options', 'posterno-addons', [ $this, 'display' ] );
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

		if ( $screen->id === 'listings_page_posterno-addons' ) {
			wp_enqueue_style( 'pno-options-panel', PNO_PLUGIN_URL . '/assets/css/admin/admin-settings-panel.min.css', false, PNO_VERSION );
		}
	}

	/**
	 * Display content of the page.
	 */
	public function display() {

		$addons = $this->get_addons();

		include PNO_PLUGIN_DIR . 'vendor/posterno/addons/includes/views/addons-page.php';

	}

	/**
	 * Get addons from the api.
	 *
	 * @return array
	 */
	public function get_addons() {

		$api_url = $this->api_url;

		$addons = remember_transient(
			'pno_addons_list',
			function () use ( $api_url ) {

				$found_addons = [];

				// Find the master branch REF for the api.
				$ref_request = wp_remote_get( $api_url );

				if ( is_wp_error( $ref_request ) ) {
					return [];
				}

				$ref_body = wp_remote_retrieve_body( $ref_request );

				if ( ! empty( $ref_body ) ) {
					$ref_body = \json_decode( $ref_body );
				}

				if ( isset( $ref_body->refs[0]->ref ) ) {
					$api_url = 'https://posterno.cdn.prismic.io/api/v1/documents/search?ref=' . esc_html( $ref_body->refs[0]->ref );
				} else {
					return [];
				}

				$request = wp_remote_get( $api_url );

				if ( is_wp_error( $request ) ) {
					return [];
				}

				$body = wp_remote_retrieve_body( $request );

				if ( ! empty( $body ) ) {
					$body = \json_decode( $body );
				}

				if ( isset( $body->results ) && is_array( $body->results ) && ! empty( $body->results ) ) {
					foreach ( $body->results as $addon ) {

						$id          = isset( $addon->slugs[0] ) ? wp_strip_all_tags( $addon->slugs[0] ) : false;
						$title       = isset( $addon->data->addons->title->value[0]->text ) ? wp_strip_all_tags( $addon->data->addons->title->value[0]->text ) : false;
						$description = isset( $addon->data->addons->description->value[0]->text ) ? wp_strip_all_tags( $addon->data->addons->description->value[0]->text ) : false;
						$url         = isset( $addon->data->addons->download_url->value->url ) ? wp_strip_all_tags( $addon->data->addons->download_url->value->url ) : false;
						$icon        = isset( $addon->data->addons->addon_icon->value->main->url ) ? esc_url( $addon->data->addons->addon_icon->value->main->url ) : false;
						$priority    = isset( $addon->data->addons->addon_order->value ) ? absint( $addon->data->addons->addon_order->value ) : 100;

						if ( $id && $title && $description && $url ) {
							$found_addons[ $id ] = [
								'title'    => $title,
								'desc'     => $description,
								'url'      => $url,
								'icon'     => $icon,
								'priority' => $priority,
							];
						}
					}
				}

				uasort( $found_addons, 'pno_sort_array_by_priority' );

				return $found_addons;

			},
			DAY_IN_SECONDS
		);

		return $addons;

	}

}
