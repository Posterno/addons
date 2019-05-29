<?php
/**
 * Hooks the addons component to WordPress.
 *
 * @package     posterno-addons
 * @copyright   Copyright (c) 2019, Sematico, LTD
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

( new PosternoAddons\Categorize() )->hooks();
( new PosternoAddons\Page() )->hooks();
( new PosternoAddons\Activator() )->init();

/**
 * Add addons links to post type list.
 *
 * @param string $views current views.
 * @return string
 */
function pno_admin_listings_tabs( $views ) {
	pno_display_admin_listings_tabs();
	return $views;
}
add_filter( 'views_edit-listings', 'pno_admin_listings_tabs', 10, 1 );

/**
 * Display tabs for the listings post type and addons page.
 *
 * @return void
 */
function pno_display_admin_listings_tabs() {
	?>
	<h2 class="nav-tab-wrapper">
		<?php

		$tabs       = array(
			'listings'     => array(
				'name' => esc_html__( 'Listings', 'posterno' ),
				'url'  => admin_url( 'edit.php?post_type=listings' ),
			),
			'integrations' => array(
				'name' => esc_html__( 'Extensions', 'posterno' ),
				'url'  => admin_url( 'edit.php?post_type=listings&page=posterno-addons' ),
			),
		);
		$active_tab = isset( $_GET['page'] ) && $_GET['page'] === 'posterno-addons' ? 'integrations' : 'listings';

		foreach ( $tabs as $tab_id => $tab ) {
			$active = $active_tab === $tab_id ? ' nav-tab-active' : '';
			echo '<a href="' . esc_url( $tab['url'] ) . '" class="nav-tab' . $active . '">';
			echo esc_html( $tab['name'] );
			echo '</a>';
		}
		?>

		<a href="<?php echo admin_url( 'post-new.php?post_type=listings' ); ?>" class="page-title-action">
			<?php esc_html_e( 'Add new', 'posterno' ); ?>
		</a>
	</h2>
	<br />
	<?php
}

/**
 * Query the EDD Api to check the status of a license.
 *
 * @param string $addon_shortname addon shortname.
 * @param string $addon_name name of the addon in EDD.
 * @param string $addon_id ID of the addon in EDD.
 * @param string $api_url api url.
 * @return void
 */
function pno_check_addon_license( $addon_shortname, $addon_name, $addon_id, $api_url ) {

	if ( ! $addon_shortname ) {
		return;
	}

	$license_key = pno_get_option( $addon_shortname, false );

	if ( ! $license_key ) {
		return;
	}

	$api_params = array(
		'edd_action' => 'check_license',
		'license'    => $license_key,
		'item_name'  => rawurlencode( $addon_name ),
		'url'        => home_url(),
	);

	if ( ! empty( $addon_id ) ) {
		$api_params['item_id'] = $addon_id;
	}

	$response = wp_remote_post(
		$api_url,
		array(
			'timeout'   => 15,
			'sslverify' => false,
			'body'      => $api_params,
		)
	);

	if ( is_wp_error( $response ) ) {
		return false;
	}

	$license_data = json_decode( wp_remote_retrieve_body( $response ) );

	posterno()->admin_notices->restore_notice( 'pno_invalid_license_data_' . $addon_shortname );

	update_option( $addon_shortname . '_license_data', $license_data );

}
