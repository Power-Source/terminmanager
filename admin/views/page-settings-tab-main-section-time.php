<table class="form-table">
	<tr>
		<th scope="row"><label for="min_time"><?php _e( 'Zeitbasis', 'appointments' ) ?></label></th>
		<td colspan="2">
			<select name="min_time" id="min_time">
				<?php foreach ( $base_times as $min_time ) :  ?>
					<option value="<?php echo esc_attr( $min_time ); ?>" <?php selected( $min_time, $min_time_setting ); ?>><?php echo esc_html( appointment_convert_minutes_to_human_format( $min_time ) ); ?></option>
				<?php endforeach; ?>
			</select>
			<br>
			<p class="description"><?php _e( 'Mindestzeit, die für Dauer, Termin und Zeitplanintervalle wirksam ist. Die Betriebsdauer kann nur als Vielfaches dieses Werts festgelegt werden. Standard: 30.', 'appointments' ) ?></p>
		</td>
	</tr>

	<tr>
		<th scope="row"><label for="additional_min_time"><?php _e( 'Zusätzliche Zeitbasis (Minuten)', 'appointments' ) ?></label></th>
		<td>
			<input type="text" class="small-text" name="additional_min_time" id="additional_min_time" value="<?php if ( isset( $options['additional_min_time'] ) ) { echo $options['additional_min_time']; } ?>"/>
			<br>
			<p class="description"><?php _e( 'Wenn die oben genannten Zeitgrundlagen nicht zu Deinem Unternehmen passen, kannst Du eine neue hinzufügen, z.B. 240. Hinweis: Nachdem Du diese zusätzliche Zeitbasis gespeichert hast, musst Du sie mit der obigen Einstellung auswählen. Hinweis: Die minimal zulässige Zeitbasiseinstellung beträgt 10 Minuten.', 'appointments' ) ?></p>
		</td>
	</tr>

	<tr>
		<th scope="row"><label for="admin_min_time"><?php _e( 'Zeitbasis auf der Admin-Seite (Minuten)', 'appointments' ) ?></label></th>
		<td>
			<input type="text" class="small-text" name="admin_min_time" id="admin_min_time" value="<?php if ( isset( $options['admin_min_time'] ) ) { echo $options['admin_min_time']; } ?>"/>
			<br>
			<p class="description"><?php _e( 'Diese Einstellung kann verwendet werden, um Flexibilität beim manuellen Festlegen und Bearbeiten der Termine zu bieten. Wenn Du hier beispielsweise 15 eingibst, kannst Du einen Termin in 15-Minuten-Intervalle verschieben, selbst wenn die ausgewählte Zeitbasis 45 Minuten beträgt. Wenn Du dies leer lässt, wird die oben ausgewählte Zeitbasis auf der Administratorseite angewendet.', 'appointments' ) ?></p>
		</td>
	</tr>

	<tr>
		<th scope="row"><label for="app_lower_limit"><?php _e( 'Terminbuchungsuntergrenze (Stunden)', 'appointments' ) ?></label></th>
		<td>
			<input type="text" style="width:50px" name="app_lower_limit" id="app_lower_limit" value="<?php if ( isset( $options['app_lower_limit'] ) ) { echo $options['app_lower_limit']; } ?>"/>
			<br>
			<p class="description"><?php _e( 'Dadurch werden Zeitfenster blockiert, die mit dem eingestellten Wert ab der aktuellen Zeit gebucht werden sollen. Wenn Du beispielsweise 2 Tage benötigst, um einen Termin zu bewerten und anzunehmen, gib hier 48 ein. Standard: 0 (keine Sperrung - Termine können vereinbart werden, wenn die Endzeit nicht überschritten wurde)', 'appointments' ) ?></p>
		</td>
	</tr>

	<tr>
		<th scope="row"><label for="app_limit"><?php _e( 'Terminbuchungsobergrenze (Tage)', 'appointments' ) ?></label></th>
		<td>
			<input type="text" class="small-text" name="app_limit" id="app_limit" value="<?php if ( isset( $options['app_limit'] ) ) { echo $options['app_limit']; } ?>"/>
			<br>
			<p class="description"><?php _e( 'Maximale Anzahl von Tagen ab heute, an denen ein Client einen Termin buchen kann. Standard: 365', 'appointments' ) ?></p>
	</tr>

	<tr>
		<th scope="row"><label for="clear_time"><?php _e( 'Deaktiviere ausstehende Termine nach (Minuten)', 'appointments' ) ?></label></th>
		<td>
			<input type="text" class="small-text" name="clear_time" id="clear_time" value="<?php if ( isset( $options['clear_time'] ) ) { echo $options['clear_time']; } ?>"/>
			<br>
			<p class="description"><?php _e( 'Ausstehende Termine werden nach dieser festgelegten Zeit automatisch entfernt (nicht gelöscht - Löschen ist nur manuell möglich) und diese Terminzeit wird freigegeben. Gib zum Deaktivieren 0 ein. Standard: 60. Bitte beachte dass ausstehende und von GCal reservierte Termine, deren Startzeit abgelaufen ist, unabhängig von anderen Einstellungen immer entfernt werden.', 'appointments' ) ?></p>
	</tr>

	<tr>
		<th scope="row"><label for="spam_time"><?php _e( 'Mindestdauer für einen neuen Termin (Sek.)', 'appointments' ) ?></label></th>
		<td>
			<input type="text" class="small-text" name="spam_time" id="spam_time" value="<?php if ( isset( $options['spam_time'] ) ) { echo $options['spam_time']; } ?>"/>
			<br>
			<p class="description"><?php _e( 'Du kannst die Häufigkeit der Terminanträge begrenzen, um zu verhindern, dass Spammer Deine Termine blockieren. Dies gilt nur für ausstehende Termine. Gib zum Deaktivieren 0 ein. Tipp: Um weitere Terminanträge eines Clienten vor einer Zahlung oder manuellen Bestätigung zu vermeiden, gib hier hier eine große Zahl ein.', 'appointments' ) ?></p>
	</tr>
	<?php do_action( 'app-settings-time_settings' ); ?>
</table>
