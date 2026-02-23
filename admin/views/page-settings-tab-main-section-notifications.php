<table class="form-table">

	<tr valign="top">
		<th scope="row"><?php _e( 'Bestätigungs-E-Mail senden', 'appointments' ) ?></th>
        <td colspan="2">
            <?php _appointments_html_chceckbox( $options, 'send_confirmation' ) ?>
            <p class="description"><?php _e( 'Gibt an, ob nach Bestätigung des Termins eine E-Mail gesendet werden soll. Hinweis: Administrator und Dienstanbieter erhalten auch eine Kopie als separate E-Mails.', 'appointments' ) ?></p>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><?php _e( 'Sende eine Benachrichtigung an den Administrator, wenn eine Bestätigung erforderlich ist', 'appointments' ) ?></th>
		<td colspan="2">
            <?php _appointments_html_chceckbox( $options, 'send_notification' ) ?>
            <p class="description"><?php _e( 'Möglicherweise möchtest Du eine Benachrichtigungs-E-Mail erhalten, wenn ein neuer Termin vom Front-End im Status "Ausstehend" vereinbart wird. Diese E-Mail wird nur gesendet, wenn sie keine Zahlung benötigen, d.h. wenn Deine Zustimmung erforderlich ist. Hinweis: Eine Benachrichtigungs-E-Mail wird auch an den Dienstanbieter gesendet, wenn ein Anbieter vom Client ausgewählt wurde und "Dienstanbieter die Bestätigung eigener Termine zulassen" auf "Ja" gesetzt ist.', 'appointments' ) ?></p>
		</td>
	</tr>

	<tr>
		<th scope="row"><?php _e( 'Betreff der Bestätigungs-E-Mail', 'appointments' ) ?></th>
		<td>
			<input value="<?php echo esc_attr( $options['confirmation_subject'] ); ?>" size="90" name="confirmation_subject" type="text"/>
		</td>
	</tr>

	<tr>
		<th scope="row"><?php _e( 'Bestätigungs-Email-Nachricht', 'appointments' ) ?></th>
		<td>
			<textarea cols="90" rows="6" name="confirmation_message"><?php echo esc_textarea( $options['confirmation_message'] ); ?></textarea>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><?php _e( 'Sende eine Erinnerungs-E-Mail an den Client', 'appointments' ) ?></th>
		<td colspan="2">
            <?php _appointments_html_chceckbox( $options, 'send_reminder' ) ?>
			<p class="description"><?php _e( 'Gibt an, ob vor dem Termin Erinnerungs-E-Mails an die Clienten gesendet werden sollen.', 'appointments' ) ?></p>
		</td>
	</tr>

	<tr>
		<th scope="row"><?php _e( 'Erinnerungs-E-Mail Sendezeit für den Clienten (Stunden)', 'appointments' ) ?></th>
		<td>
			<input value="<?php echo esc_attr( $options['reminder_time'] ); ?>"
			       name="reminder_time" type="text"/>
			<br/>
						<span
							class="description"><?php _e( 'Legt fest, wie viele Stunden eine Erinnerung an den Kunden gesendet wird, bevor der Termin stattfindet. Mehrfacherinnerungen sind möglich. Gib dazu durch Komma getrennte Erinnerungsstunden ein, z.B. 48,24.', 'appointments' ) ?></span>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><?php _e( 'Sende eine Erinnerungs-E-Mail an den Provider', 'appointments' ) ?></th>
		<td colspan="2">
            <?php _appointments_html_chceckbox( $options, 'send_reminder_worker' ) ?>
			<p class="description"><?php _e( 'Gibt an, ob vor dem Termin Erinnerungs-E-Mails an den Dienstanbieter gesendet werden sollen.', 'appointments' ) ?></p>
		</td>
	</tr>

	<tr>
		<th scope="row"><?php _e( 'Erinnerungs-E-Mail Sendezeit für den Provider (Stunden)', 'appointments' ) ?></th>
		<td>
			<input value="<?php echo esc_attr( $options['reminder_time_worker'] ); ?>"
			       size="90" name="reminder_time_worker" type="text"/>
			<br/>
						<span
							class="description"><?php _e( 'Wie oben, definiert jedoch die Zeit für den Dienstanbieter.', 'appointments' ) ?></span>
		</td>
	</tr>

	<tr>
		<th scope="row"><?php _e( 'Erinnerung E-Mail Betreff', 'appointments' ) ?></th>
		<td>
			<input value="<?php echo esc_attr( $options['reminder_subject'] ); ?>"
			       size="90" name="reminder_subject" type="text"/>
		</td>
	</tr>

	<tr>
		<th scope="row"><?php _e( 'Erinnerungs-E-Mail-Nachricht', 'appointments' ) ?></th>
		<td>
						<textarea cols="90" rows="6"
						          name="reminder_message"><?php echo esc_textarea( $options['reminder_message'] ); ?></textarea>
		</td>
	</tr>

	<tr>
		<th scope="row"><?php _e( 'Sende eine Benachrichtigungs-E-Mail beim Entfernen des Termins', 'appointments' ) ?></th>
		<td colspan="2">
            <?php _appointments_html_chceckbox( $options, 'send_removal_notification' ) ?>
			<p class="description"><?php _e( 'Sende eine E-Mail an die entsprechenden Clienten und Provider, wenn ein Termin entfernt wurde.', 'appointments' ) ?><br/><?php _e( '<b>Hinweis:</b> Diese E-Mail wird nur für explizit entfernte Termine gesendet. Die Termine, die aufgrund des Ablaufs entfernt werden, sind nicht betroffen.', 'appointments' ) ?>
			</p>
		</td>
	</tr>

	<tr>
		<th scope="row"><?php _e( 'Benachrichtigungs-E-Mail-Betreff für die Entfernung', 'appointments' ) ?></th>
		<td>
			<?php
			$rn_subject = ! empty( $options['removal_notification_subject'] )
				? $options['removal_notification_subject']
				: App_Template::get_default_removal_notification_subject();
			?>
			<input value="<?php echo esc_attr( $rn_subject ); ?>" size="90"
			       name="removal_notification_subject" type="text"/>
		</td>
	</tr>

	<tr>
		<th scope="row"><?php _e( 'Benachrichtigungs-E-Mail-Nachricht beim Entfernen', 'appointments' ) ?></th>
		<td>
			<?php
			$rn_msg = ! empty( $options['removal_notification_message'] )
				? $options['removal_notification_message']
				: App_Template::get_default_removal_notification_message();
			?>
			<textarea cols="90" rows="6"
			          name="removal_notification_message"><?php echo esc_textarea( $rn_msg ); ?></textarea>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><?php _e( 'Protokoll Gesendete E-Mail-Datensätze', 'appointments' ) ?></th>
		<td colspan="2">
            <?php _appointments_html_chceckbox( $options, 'log_emails' ) ?>
			<p class="description"><?php _e( 'Gibt an, ob Bestätigungs- und Erinnerungs-E-Mail-Datensätze protokolliert werden sollen (nicht die E-Mails selbst).', 'appointments' ) ?></p>
		</td>
	</tr>
	<tr>

	<tr>
		<th scope="row">&nbsp;</th>
		<td>
	<span class="description">
	<?php _e( 'Für den oben genannten Betreff und E-Mail-Inhalt der E-Mail kannst Du die folgenden Platzhalter verwenden, die durch ihre tatsächlichen Werte ersetzt werden:', 'appointments' ) ?>
		&nbsp;SITE_NAME, CLIENT, SERVICE, SERVICE_PROVIDER, DATE_TIME, PRICE, DEPOSIT, <span
			class="app-has_explanation"
			title="(PRICE - DEPOSIT)">BALANCE</span>, PHONE, NOTE, ADDRESS, CITY, EMAIL <?php _e( "(Client's Email)", 'appointments' ) ?>
		<span style="display: none;">, CANCEL <?php _e( '(Fügt dem E-Mail-Text einen Stornierungslink hinzu.)', 'appointments' ) ?></span>
	</span>
		</td>
	</tr>

</table>
