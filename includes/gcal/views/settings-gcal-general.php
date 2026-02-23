<h3><?php _e( 'Allgemeine Einstellungen für Google Kalender', 'appointments' ); ?></h3>
<table class="form-table">

	<tr valign="top">
		<th scope="row" ><label for="gcal_location"><?php _e('Standort des Google Kalenders','appointments')?></label></th>
		<td colspan="2">
			<input type="text" class="widefat" name="gcal_location" id="gcal_location" value="<?php echo esc_attr( $gcal_location ); ?>" />
			<br /><span class="description"><?php _e('Gib den Text ein, der als Standortfeld in Google Kalender verwendet wird. Wenn leer, wird stattdessen Deine Webseiten-Beschreibung gesendet. Hinweis: Du kannst ADDRESS und CITY Platzhalter verwenden, die durch ihre tatsächlichen Werte ersetzt werden.', 'appointments')?></span>
		</td>
	</tr>
</table>