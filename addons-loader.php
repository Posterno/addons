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

( new PosternoAddons\Page() )->hooks();

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
				'name' => esc_html__( 'Listings' ),
				'url'  => admin_url( 'edit.php?post_type=listings' ),
			),
			'integrations' => array(
				'name' => esc_html__( 'Extensions' ),
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
			<?php esc_html_e( 'Add new' ); ?>
		</a>
	</h2>
	<br />
	<?php
}
