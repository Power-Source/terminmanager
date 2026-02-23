<p class="description"><?php _e( 'Die Allgemeine Datenschutzverordnung (DSGVO) (EU) 2016/679 ist eine Verordnung im EU-Recht zum Datenschutz und zur Wahrung der Privatsphäre für alle Personen innerhalb der Europäischen Union. Es befasst sich auch mit dem Export personenbezogener Daten außerhalb der EU. Die DSGVO zielt in erster Linie darauf ab, Bürgern und Einwohnern die Kontrolle über ihre personenbezogenen Daten zu geben und das regulatorische Umfeld für internationale Geschäfte zu vereinfachen, indem die Regulierung innerhalb der EU vereinheitlicht wird.', 'appointments' ); ?></p>
<?php
global $wp_version;
$is_less_496 = version_compare( $wp_version, '4.9.6', '<' );
if ( $is_less_496 ) {
	echo '<div class="notice notice-error inline notice-app-wp-version">';
	echo wpautop( __( 'Datenschutz-Einstellungen sind für ClassicPress-Versionen unter 4.9.6 nicht verfügbar. Bitte aktualisiere zuerst Dein ClassicPress.', 'appointments' ) );
	echo '</div>';
	return;
}
?>
<table class="form-table">
	<tr>
		<th scope="row"><?php _e( 'Benutzer kann löschen nach', 'appointments' ) ?></th>
		<td>
        <input value="<?php echo esc_attr( $options['gdpr_number_of_days_user_erease'] ); ?>" name="gdpr_number_of_days_user_erease" type="number" min="1" /> <?php esc_html_e( 'Tagen', 'appointments' ); ?>
			<p class="description"><?php _e( 'Abgeschlossene Termine können vom Benutzer nach der ausgewählten Anzahl von Tagen nach einem Termin gelöscht werden.', 'appointments' ) ?></p>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><label for="gdpr_delete"><?php _e( 'Termine löschen', 'appointments' ) ?></label></th>
		<td>
			<?php _appointments_html_chceckbox( $options, 'gdpr_delete', 'appointments_gdpr_delete' ); ?>
			<p class="description"><?php _e( 'Du kannst das Löschen abgeschlossener Termine erzwingen.', 'appointments' ) ?></p>
		</td>
	</tr>
	<tr class="appointments_gdpr_delete">
		<th scope="row"><?php _e( 'Automatisches Löschen nach', 'appointments' ) ?></th>
		<td>
			<input value="<?php echo esc_attr( $options['gdpr_number_of_days'] ); ?>" name="gdpr_number_of_days" type="number" min="1" /> <?php esc_html_e( 'Tagen', 'appointments' ); ?>
			<p class="description"><?php _e( 'Abgeschlossene Termine werden gelöscht nach.', 'appointments' ) ?></p>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><label for="gdpr_delete"><?php _e( 'Zustimmung', 'appointments' ) ?></label></th>
		<td>
			<?php _appointments_html_chceckbox( $options, 'gdpr_checkbox_show', 'appointments_gdpr_checkbox_show' ); ?>
			<p class="description"><?php _e( 'Füge ein Kontrollkästchen hinzu, in dem der Benutzer des Formulars speziell gefragt wird, ob er damit einverstanden ist, dass Sie seine persönlichen Daten speichern und verwenden, um wieder mit ihm in Kontakt zu treten.', 'appointments' ) ?></p>
		</td>
	</tr>
	<tr class="appointments_gdpr_checkbox_show">
		<th scope="row"><?php _e( 'Kontrollkästchentext', 'appointments' ) ?></th>
		<td><input type="text" class="large-text" value="<?php echo esc_attr( $options['gdpr_checkbox_text'] ); ?>" name="gdpr_checkbox_text" /></td>
	</tr>
	<tr class="appointments_gdpr_checkbox_show">
		<th scope="row"><?php _e( 'Fehlermeldung', 'appointments' ) ?></th>
		<td><input type="text" class="large-text" value="<?php echo esc_attr( $options['gdpr_checkbox_alert'] ); ?>" name="gdpr_checkbox_alert" /></td>
	</tr>
	<?php do_action( 'app-settings-gdpr_settings' ); ?>
</table>
