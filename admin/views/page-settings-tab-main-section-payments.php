<?php
if ( empty( $options['payment_required'] ) ) {
	$options['payment_required'] = 'no';
}
$use_payments = ( 'yes' == $options['payment_required'] );
?>
<table class="form-table">

	<tr>
		<th scope="row"><label for="payment_required"><?php _e( 'Bezahlung erforderlich', 'appointments' ) ?></label></th>
		<td class="app_payment_required">
            <?php _appointments_html_chceckbox( $options, 'payment_required', 'payment_row' ) ?>
			<p class="description"><?php printf( __( 'Ob Du eine Zahlung benötigst, um Termine anzunehmen. Wenn Ja ausgewählt ist, wird der Kunde aufgefordert, über Paypal zu zahlen. Der Termin steht noch aus, bis die Zahlung durch Paypal IPN bestätigt wird. Wenn Nein ausgewählt ist, befindet sich der Termin im Status Ausstehend, bis Du ihn manuell mit %s genehmigst, es sei denn, die automatische Bestätigung ist nicht auf Ja festgelegt.', 'appointments' ), '<a href="' . admin_url( 'admin.php?page=appointments' ) . '">' . __( 'Terminbuchungsseite', 'appointments' ) . '</a>' ) ?></p>
		</td>
	</tr>
	<tr>
		<th scope="row"><label for="currency"><?php _e( 'Währung', 'appointments' ) ?></label></th>
		<td colspan="2">
			<select name="currency" id="currency">
				<?php
				$sel_currency = ( $options['currency'] ) ? $options['currency'] : $options['currency'];
				$currencies   = App_Template::get_currencies();
				foreach ( $currencies as $k => $v ) {
					echo '<option value="' . $k . '"' . ( $k == $sel_currency ? ' selected' : '' ) . '>' . esc_html( $v, true ) . '</option>' . "\n";
				}
				?>
			</select>
		</td>
	</tr>
	<tr class="payment_row" <?php if ( ! $use_payments ) { echo 'style="display:none"'; } ?>>
		<th scope="row"><label for="mode"><?php _e( 'PayPal Modus', 'appointments' ) ?></label></th>
		<td>
			<select name="mode" id="mode">
				<option value="sandbox"<?php selected( $options['mode'], 'sandbox' ) ?>><?php _e( 'Sandbox', 'appointments' ) ?></option>
				<option value="live"<?php selected( $options['mode'], 'live' ) ?>><?php _e( 'Live', 'appointments' ) ?></option>
			</select>
		</td>
	</tr>

	<tr class="payment_row" <?php if ( ! $use_payments ) { echo 'style="display:none"'; } ?>>
		<th scope="row"><label for="merchant_email"><?php _e( 'PayPal Merchant E-mail', 'appointments' ) ?></label></th>
		<td colspan="2">
			<input value="<?php echo esc_attr( $options['merchant_email'] ); ?>" size="30" name="merchant_email" id="merchant_email" type="text"/>
			<p class="description"> <?php printf( __( 'Nur zu Deiner Information lautet Dein IPN-Link: <b>%s</b>. In einigen Fällen benötigst Du diese Informationen möglicherweise.', 'appointments' ), admin_url( 'admin-ajax.php?action=app_paypal_ipn' ) ); ?> </p>
		</td>
	</tr>

	<tr class="payment_row" <?php if ( ! $use_payments ) { echo 'style="display:none"'; } ?>>
		<th scope="row"><label for="return"><?php _e( 'Danke für Buchung-Seite', 'appointments' ) ?></label></th>
		<td colspan="2">
			<?php wp_dropdown_pages( array(
				'show_option_none'   => __( 'Startseite', 'appointments' ),
				'option_none_value ' => 0,
				'name'               => 'return',
				'selected'           => @$options['return'],
			) ) ?>
			<p class="description"><?php _e( 'Die Seite, auf der der Kunde zurückgegeben wird, wenn er auf der Paypal-Website auf den Rückgabelink klickt.', 'appointments' ) ?></p>
		</td>
	</tr>

	<tr class="payment_row" <?php if ( ! $use_payments ) { echo 'style="display:none"'; } ?>>
		<th scope="row"><label for="percent_deposit"><?php _e( 'Anzahlung (%)', 'appointments' ) ?></label></th>
		<td colspan="2">
			<input value="<?php echo esc_attr( @$options['percent_deposit'] ); ?>" style="width:50px" name="percent_deposit" id="percent_deposit" type="text"/>
			<p class="description"><?php _e( 'Möglicherweise möchtest Du einen bestimmten Prozentsatz des Servicepreises als Anzahlung verlangen, z. 25. Lasse dieses Feld leer, um nach dem vollen Preis zu fragen.', 'appointments' ) ?></p>
		</td>
	</tr>

	<tr class="payment_row" <?php if ( ! $use_payments ) { echo 'style="display:none"'; } ?>>
		<th scope="row"><label for="fixed_deposit"><?php _e( 'Anzahlung (Festbetrag)', 'appointments' ) ?></label></th>
		<td colspan="2">
			<input value="<?php echo esc_attr( @$options['fixed_deposit'] ); ?>" style="width:50px" name="fixed_deposit" id="fixed_deposit" type="text"/>
			<p class="description"><?php _e( 'Wie oben, jedoch wird vom Kunden pro Termin eine feste Anzahlung verlangt. Wenn beide Felder ausgefüllt sind, wird nur die feste Einzahlung berücksichtigt.', 'appointments' ) ?></p>
		</td>
	</tr>

	<tr class="payment_row" <?php if ( ! $use_payments ) { echo 'style="display:none"'; } ?>>
		<th scope="row"><label for="allow_free_autoconfirm"><?php _e( 'Ermögliche die automatische Bestätigung von Terminen mit Nullpreis', 'appointments' ) ?></label></th>
        <td colspan="2">
            <?php _appointments_html_chceckbox( $options, 'allow_free_autoconfirm' ); ?>
			<p class="description"><?php _e( 'Ermögliche die automatische Bestätigung für Termine ohne Preis in einer kostenpflichtigen Umgebung.', 'appointments' ) ?></p>
		</td>
	</tr>
	
	<?php
	/**
	 * Integrations or add-ons can use this action to add their own payment
	 * settings to the form.
	 */
	do_action( 'app_settings_form_payment', $options, $use_payments );
	?>

</table>
