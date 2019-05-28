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

<?php foreach ( $addons as $addon_id => $addon ) : ?>

	<?php

		$license = $addon['license'];
		$status  = $addon['status'];
		$name    = $addon['name'];

	?>

	<form method="post" action="options.php" class="wrap-licenses">
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row"><?php echo esc_html( $name ); ?></th>
					<td>
						<input
							type="text"
							placeholder="<?php esc_html_e( 'Enter your license key' ); ?>"
							class="regular-text"
							id="<?php echo esc_attr( $addon_id ); ?>"
							name="<?php echo esc_attr( $addon_id ); ?>"
							value="<?php echo esc_attr( $license ); ?>"
						>

						<div class="edd-license-data">
							<?php if ( ! $license ) : ?>
								<p><?php printf( esc_html__( 'To receive updates, please enter your valid "%s" license key.' ), $name ); ?></p>
								<?php submit_button( 'Activate license', 'submit button-primary large-button', 'submit_'. $addon_id, false ); ?>
							<?php elseif ( $license && $status === 'valid' ) : ?>
								<a href="<?php echo esc_url( $this->get_deactivation_url( $addon_id ) ); ?>" class="button button-large"><?php esc_html_e( 'Deactivate' ); ?></a>
							<?php endif; ?>
						</div>
					</td>
				</tr>
			</tbody>
		</table>

		<?php
			wp_nonce_field( "verify_posterno_licenses_{$addon_id}_form", "posterno_licenses_{$addon_id}_nonce" );
		?>
	</form>
<?php endforeach; ?>
