<?php
/**
 * Displays the content of the licenses page.
 *
 * @package     posterno-addons
 * @copyright   Copyright (c) 2019, Sematico, LTD
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

?>

<p><?php esc_html_e( 'Enter your extension license keys here to receive updates for purchased extensions. If your license key has expired, please renew your license.' ); ?></p>

<form method="post" action="options.php" class="wrap-licenses">

	<table class="form-table">
		<tbody>
			<?php foreach ( $addons as $addon_id => $addon ) : ?>

			<tr>
				<th scope="row"><?php echo esc_html( $addon['name'] ); ?></th>
				<td>
					<input type="text" placeholder="<?php esc_html_e( 'Enter your license key' ); ?>" class="regular-text" id="pno_licenses[<?php echo esc_attr( $addon_id ); ?>]" name="pno_licenses[<?php echo esc_attr( $addon_id ); ?>]" value="">
					<div class="edd-license-data edd-license-empty">
						<p><?php printf( esc_html__( 'To receive updates, please enter your valid "%s" license key.' ), $addon['name'] ); ?></p>
					</div>
				</td>
			</tr>

			<?php endforeach; ?>

		</tbody>
	</table>

	<?php

		wp_nonce_field( 'verify_posterno_licenses_form', 'posterno_licenses_nonce' );
		submit_button( esc_html__( 'Save changes' ) );

	?>

</form>
