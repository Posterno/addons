<?php
/**
 * Handles categorization of all Posterno addons within the WP plugins page.
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
 * Categorize Posterno plugin and addons within the plugin's window.
 */
class Categorize {

	/**
	 * Hook into WordPress.
	 *
	 * @return void
	 */
	public function hooks() {

		add_filter( 'views_plugins', [ $this, 'add_posterno_addons_tab' ] );
		add_filter( 'views_plugins-network', [ $this, 'add_posterno_addons_tab' ] );

		add_filter( 'show_advanced_plugins', [ $this, 'show_categorized_addons' ] );
		add_filter( 'show_network_active_plugins', [ $this, 'show_categorized_addons' ] );

		add_action( 'check_admin_referer', [ $this, 'prepare_referer' ], 10, 2 );

		add_filter( 'all_plugins', [ $this, 'filter' ] );

	}

	/**
	 * Register a new tab for the plugin's list table.
	 *
	 * @param array $views list of views.
	 * @return array
	 */
	public function add_posterno_addons_tab( $views ) {

		global $status, $plugins;

		if ( ! empty( $plugins['posterno'] ) ) {
			$class = '';

			if ( 'posterno' === $status ) {
				$class = 'current';
			}

			$views['posterno'] = sprintf(
				'<a class="%s" href="plugins.php?plugin_status=posterno"> %s <span class="count">(%s) </span></a>',
				$class,
				esc_html__( 'Posterno', 'posterno' ),
				count( $plugins['posterno'] )
			);
		}

		return $views;
	}

	/**
	 * Find all Posterno's plugins.
	 *
	 * @param string $plugin_menu menu item.
	 * @return string
	 */
	public function show_categorized_addons( $plugin_menu ) {

		global $plugins;

		foreach ( $plugins['all'] as $plugin_slug => $plugin_data ) {

			foreach ( $plugins['all'] as $plugin_slug => $plugin_data ) {

				if ( false !== strpos( $plugin_data['Name'], 'Posterno' ) && ( false !== strpos( $plugin_data['AuthorName'], 'Posterno' ) ) ) {
					$plugins['posterno'][ $plugin_slug ]           = $plugins['all'][ $plugin_slug ];
					$plugins['posterno'][ $plugin_slug ]['plugin'] = $plugin_slug;
					// replicate the next step.
					if ( current_user_can( 'update_plugins' ) ) {
						$current = get_site_transient( 'update_plugins' );
						if ( isset( $current->response[ $plugin_slug ] ) ) {
							$plugins['posterno'][ $plugin_slug ]['update'] = true;
						}
					}
				}
			}
		}

		return $plugin_menu;

	}

	/**
	 * Keep the "Posterno" window active when enabling or disabling a plugin from the posterno status.
	 *
	 * @param mixed $action action.
	 * @param mixed $result result.
	 * @return mixed
	 */
	public function prepare_referer( $action, $result ) {

		if ( ! function_exists( 'get_current_screen' ) ) {
			return;
		}
		$screen = get_current_screen();
		if ( is_object( $screen ) && $screen->base === 'plugins' && ! empty( $_REQUEST['plugin_status'] ) && $_REQUEST['plugin_status'] === 'posterno' ) {
			global $status;
			$status = 'posterno';
		}

	}

	/**
	 * Trigger filtering of the plugin's status when "posterno" is selected.
	 *
	 * @param array $plugins list of plugins.
	 * @return array
	 */
	public function filter( $plugins ) {

		global $status;

		if ( isset( $_REQUEST['plugin_status'] ) && 'posterno' === $_REQUEST['plugin_status'] ) {
			$status = 'posterno';
		}

		return $plugins;

	}

}
