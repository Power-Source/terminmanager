<h3><?php _e( 'Einstellungen für die Google Kalender-Schaltfläche', 'appointments' ); ?></h3>
<table class="form-table">
	<tr valign="top">
		<th scope="row" ><label for="gcal"><?php _e( 'Googlekalender Schaltfläche hinzufügen', 'appointments' )?></label></th>
		<td colspan="2">
            <?php _appointments_html_chceckbox( $options, 'gcal' ) ?>
			<p class="description"><?php _e( 'Gibt an, ob der Kunde über die Schaltfläche "Google Kalender" auf sein Google Kalender-Konto zugreifen soll. Im Bestätigungsbereich werden die Schaltfläche sowie der Shortcode "Meine Termine" und gegebenenfalls die Benutzerseite/Registerkarte eingefügt.', 'appointments' ) ?></p>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row" ><label for="gcal_same_window"><?php _e( 'Öffne Google Kalender im selben Fenster', 'appointments' )?></label></th>
		<td colspan="2">
            <?php _appointments_html_chceckbox( $options, 'gcal_same_window' ) ?>
			<p class="description"><?php _e( 'Standardmäßig wird Google Kalender in einem neuen Tab oder Fenster geöffnet. Wenn Du diese Option aktivierst, wird der Nutzer von der Terminseite zu Google Kalender weitergeleitet, ohne einen neuen Tab oder ein neues Fenster zu öffnen. Hinweis: Bei der Beantragung des Termins ist dies wirksam, wenn keine Zahlung erforderlich ist oder der Preis Null ist (andernfalls würde der Zahlungsknopf/das Zahlungsformular verloren gehen).', 'appointments' ) ?></p>
		</td>
	</tr>
</table>
