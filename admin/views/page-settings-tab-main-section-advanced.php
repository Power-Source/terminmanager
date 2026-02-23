<table class="form-table">
	<tr valign="top">
		<th scope="row"><label for="force_cache"><?php _e( 'Cache löschen', 'appointments' ) ?></label></th>
		<td colspan="2">
            <?php _appointments_html_chceckbox( $options, 'force_cache' ); ?>
            <p class="description"><?php _e( 'Der Cache wird automatisch in regelmäßigen Abständen (Standard: 10 Minuten) oder beim Ändern einer Einstellung geleert. Um es manuell zu löschen, aktiviere dieses Kontrollkästchen.', 'appointments' ) ?></p>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><label for="always_load_scripts"><?php _e( 'Lade immer Skripte', 'appointments' ) ?></label></th>
		<td colspan="2">
            <?php _appointments_html_chceckbox( $options, 'always_load_scripts' ); ?>
            <p class="description"><?php _e( 'Standardmäßig werden einige Skripte nur geladen, wenn Buchungscodes erkannt werden. Bei einigen Themen und wenn das Buchungsformular im Popup angezeigt wird, muss diese Option möglicherweise aktiviert werden, um das Laden dieser Assets zu erzwingen.', 'appointments' ) ?></p>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><label for="allow_overwork"><?php _e( 'Überstunden zulassen (Tagesende)', 'appointments' ) ?></label></th>
		<td colspan="2">
            <?php _appointments_html_chceckbox( $options, 'allow_overwork' ) ?>
			<p class="description"><?php _e( 'Gibt an, ob Du Termine akzeptierst, die die Arbeitszeit am Ende des Tages überschreiten. Wenn Du beispielsweise bis 18 Uhr arbeitest und ein Kunde um 17:30 Uhr einen Termin für einen 60-minütigen Service anfordert, solltest Du diese Einstellung als Ja auswählen, um einen solchen Termin zuzulassen. Bitte beachte dass dies nur dann praktikabel ist, wenn der ausgewählte Dienst länger als die Basiszeit dauert. Solche Zeitfenster sind im Zeitplan als "nicht möglich" gekennzeichnet.', 'appointments' ) ?></p>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row"><label for="allow_overwork_break"><?php _e( 'Überarbeitung zulassen (Pausenstunden)', 'appointments' ) ?></label></th>
		<td colspan="2">
            <?php _appointments_html_chceckbox( $options, 'allow_overwork_break' ) ?>
			<p class="description"><?php _e( 'Wie oben, jedoch gültig für Pausenzeiten. Wenn Du Termine zulassen möchtest, die die Pausenzeiten überschreiten, wähle diese Option als Ja.', 'appointments' ) ?></p>
		</td>
	</tr>
    <tr valign="top">
		<th scope="row"><label for="keep_options_on_uninstall"><?php _e( 'Behalte die Optionen bei der Deinstallation bei', 'appointments' ) ?></label></th>
		<td colspan="2">
            <?php _appointments_html_chceckbox( $options, 'keep_options_on_uninstall' ); ?>
            <p class="description"><?php _e( 'Durch Aktivieren dieser Option kannst Du Deine Termine und Einstellungen beim Löschen des Plugins beibehalten.', 'appointments' ); ?></p>
		</td>
	</tr>
	<?php do_action( 'app-settings-advanced_settings' ); ?>
</table>
