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

<div class="wrap-licenses">
	<table class="form-table">
		<tbody>
		<?php foreach ( $addons as $addon_id => $addon ) : ?>
			<?php
				$license = $addon['license'];
				$data    = $addon['data'];
				$name    = $addon['name'];
			?>
			<tr>
				<th scope="row"><?php echo esc_html( $name ); ?></th>
				<td>
					<form method="post" action="options.php">
						<input
							type="text"
							placeholder="<?php esc_html_e( 'Enter your license key' ); ?>"
							class="regular-text"
							id="<?php echo esc_attr( $addon_id ); ?>"
							name="<?php echo esc_attr( $addon_id ); ?>"
							value="<?php echo esc_attr( $license ); ?>"
						>
						<?php if ( $license && isset( $data->license ) && $data->license === 'valid' ) : ?>
						<div class="carbon-wp-notice notice-success is-alt">
							<p><strong><?php echo sprintf( esc_html__( 'License expires on %s' ), date_i18n( get_option( 'date_format' ), strtotime( $data->expires ) ) ); ?></strong></p>
						</div>
						<?php elseif ( $license && isset( $data->license ) && $data->license !== 'valid' ) : ?>
						<div class="carbon-wp-notice notice-error is-alt">
							<p><strong><?php esc_html_e( 'This license is no longer valid and it\'s not receiving updates or support.' ); ?></strong></p>
						</div>
						<?php elseif ( ! $license ) : ?>
						<div class="carbon-wp-notice notice-warning is-alt">
							<p><?php printf( esc_html__( 'To receive updates, please enter your valid "%s" license key.' ), $name ); ?></p>
						</div>
						<?php endif; ?>
						<div class="edd-license-data">
							<?php if ( ! $license || isset( $data->license ) && $data->license !== 'valid' ) : ?>
								<?php
									if ( isset( $data->license ) && $data->license !== 'valid' ) {
										submit_button( 'Re-validate license', 'submit button-primary large-button', 'submit_' . $addon_id, false );
									} else {
										submit_button( 'Activate license', 'submit button-primary large-button', 'submit_' . $addon_id, false );
									}
								?>
							<?php elseif ( $license ) : ?>
								<a href="<?php echo esc_url( $this->get_deactivation_url( $addon_id ) ); ?>" class="button button-large"><?php esc_html_e( 'Deactivate' ); ?></a>
							<?php endif; ?>
						</div>
						<?php
							wp_nonce_field( "verify_posterno_licenses_{$addon_id}_form", "posterno_licenses_{$addon_id}_nonce" );
						?>
					</form>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</div>
