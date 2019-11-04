<?php
/**
 * Displays the content of the addons page.
 *
 * @package     posterno-addons
 * @copyright   Copyright (c) 2019, Sematico, LTD
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

?>

<div class="pno-admin-title-area">
	<div class="wrap">
		<h1><?php esc_html_e( 'Posterno addons', 'posterno' ); ?></h1>
		<ul class="title-links hidden-sm-and-down">
			<li>
				<a href="https://posterno.com/extensions" rel="nofollow" target="_blank" class="page-title-action"><?php esc_html_e( 'View all addons', 'posterno' ); ?></a>
			</li>
			<li>
				<a href="https://docs.posterno.com/" rel="nofollow" target="_blank" class="page-title-action"><?php esc_html_e( 'Documentation', 'posterno' ); ?></a>
			</li>
		</ul>
	</div>
</div>

<div class="wrap">

	<h2><?php esc_html_e( 'Apps and Integrations for Posterno', 'posterno' ); ?></h2>

	<div class="upgrade">
		<p><?php esc_html_e( 'Gain access to all of our premium extensions with a single purchase and save hundreds compared to buying extensions individually.', 'posterno' ); ?></p>
		<p><?php esc_html_e( 'Use the coupon code below for a 10% off discount on all our bundles.', 'posterno' ); ?></p>
		<pre>GETSTARTED</pre>

		<a href="https://posterno.com/pricing" class="button"><?php esc_html_e( 'Get the bundle', 'posterno' ); ?> &raquo;</a>
	</div>

</div>

<?php if ( is_array( $addons ) && ! empty( $addons ) ) : ?>

<div id="the-list">

	<?php foreach ( $addons as $addon_id => $addon ) : ?>

	<div class="plugin-card <?php echo esc_attr( $addon_id ); ?>">
		<div class="plugin-card-top">
			<div class="name column-name">
				<h3>
					<a href="<?php echo esc_url( $addon['url'] ); ?>" rel="nofollow" target="_blank">
						<?php echo esc_html( $addon['title'] ); ?>
						<img src="<?php echo esc_url( $addon['icon'] ); ?>" class="plugin-icon" alt="">
					</a>
				</h3>
			</div>
			<div class="action-links">
				<ul class="plugin-action-buttons">
					<li><a class="button" href="<?php echo esc_url( $addon['url'] ); ?>" role="button" rel="nofollow" target="_blank"><?php esc_html_e( 'Get this extension', 'posterno' ); ?></a></li>
				</ul>
			</div>
			<div class="desc column-description">
				<p><?php echo esc_html( $addon['desc'] ); ?></p>
			</div>
		</div>
		<div class="plugin-card-bottom">
			<div class="column-compatibility">
				<a class="button" href="<?php echo esc_url( $addon['url'] ); ?>" role="button" rel="nofollow" target="_blank"><?php esc_html_e( 'Get this extension', 'posterno' ); ?></a>
			</div>
		</div>
	</div>

	<?php endforeach; ?>

</div>

<?php endif; ?>
