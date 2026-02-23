<table class="form-table">

	<tr>
		<th scope="row"><label for="auto_confirm"><?php _e( 'Automatische Bestätigung', 'appointments' ) ?></label></th>
		<td>
            <?php _appointments_html_chceckbox( $options, 'auto_confirm' ) ?>
			<p class="description"><?php _e( 'Wenn Du dies auf Ja setzt, werden automatisch alle Terminanträge für den Fall bestätigt, dass keine Zahlung erforderlich ist. Hinweis: Für den Fall "Zahlung erforderlich" ist weiterhin eine Zahlung erforderlich.', 'appointments' ) ?></p>
		</td>
	</tr>

	<tr>
		<th scope="row"><label
				for="allow_cancel"><?php _e( 'Clienten erlauben, eigene Termine abzusagen', 'appointments' ) ?></label></th>
		<td>
            <?php _appointments_html_chceckbox( $options, 'allow_cancel' ) ?>
			<p class="description"><?php _e( 'Ob Clienten ihre Termine über den Link in Bestätigungs- und Erinnerungs-E-Mails oder über meine Termintabelle oder für angemeldete Benutzer stornieren können, indem sie die Kontrollkästchen auf ihren Profilseiten verwenden. Für den E-Mail-Fall musst Du außerdem den unten stehenden E-Mail-Nachrichteneinstellungen den Platzhalter CANCEL hinzufügen. Für meine Termintabelle musst Du dem Shortcode den Parameter allow_cancel = "1" hinzufügen. Hinweis: Administrator und Dienstanbieter erhalten immer eine Benachrichtigungs-E-Mail.', 'appointments' ) ?></p>
		</td>
	</tr>

	<tr>
		<th scope="row"><label for="cancel_page"><?php _e( 'Terminstorno Seite', 'appointments' ) ?></label>
		</th>
		<td>
			<?php wp_dropdown_pages( array(
				'show_option_none'   => __( 'Startseite', 'appointments' ),
				'option_none_value ' => 0,
				'name'               => 'cancel_page',
				'selected'           => @$options['cancel_page'],
			) ) ?>
			<br>
			<p class="description"><?php _e( 'Falls er über den E-Mail-Link storniert, wird die Seite auf die der Client weitergeleitet wird weitergeleitet, nachdem er seinen Termin abgesagt hat.', 'appointments' ) ?></p>
		</td>
	</tr>

	<tr>
		<th scope="row"><label for="thank_page"><?php _e( 'Termin Danke Seite', 'appointments' ) ?></label></th>
		<td>
			<?php wp_dropdown_pages( array(
				'show_option_none'   => __( 'Nicht umleiten', 'appointments' ),
				'option_none_value ' => 0,
				'name'               => 'thank_page',
				'selected'           => @$options['thank_page'],
			) ) ?>
			<br>
			<p class="description"><?php _e( 'Seite auf die der Benutzer nach der Bearbeitung eines Termins weitergeleitet wird.', 'appointments' ) ?></p>
		</td>
	</tr>

	<tr>
		<th scope="row"><label
				for="allow_worker_wh"><?php _e( 'Zulassen, dass der Dienstanbieter die Arbeitszeiten festlegt', 'appointments' ) ?></label>
		</th>
		<td>
            <?php _appointments_html_chceckbox( $options, 'allow_worker_wh' ) ?>
			<p class="description"><?php _e( 'Ob Du Dienstanbietern erlaubst, ihre Arbeits-/Pausenzeiten, außergewöhnlichen Tage über ihre Profilseite oder ihre Navigationsregisterkarte in BuddyPress festzulegen.', 'appointments' ) ?></p>
		</td>
	</tr>

	<tr>
		<th scope="row"><label
				for="allow_worker_confirm"><?php _e( 'Ermögliche dem Dienstanbieter, eigene Termine zu bestätigen', 'appointments' ) ?></label>
		</th>
		<td colspan="2">
            <?php _appointments_html_chceckbox( $options, 'allow_worker_confirm' ) ?>
			<p class="description"><?php _e( 'Gibt an, ob Dienstanbieter ausstehende Termine, die ihnen zugewiesen wurden, über ihre Profilseite bestätigen können.', 'appointments' ) ?></p>
		</td>
	</tr>


	<tr>
		<th scope="row"><label
				for="dummy_assigned_to"><?php _e( 'Weise Dummy-Dienstanbieter zu', 'appointments' ) ?></label></th>
		<td colspan="2">
			<?php
			wp_dropdown_users( array(
				'show_option_all' => __( 'Keinen', 'appointments' ),
				'show'            => 'user_login',
				'selected'        => isset( $options['dummy_assigned_to'] ) ? $options['dummy_assigned_to'] : 0,
				'name'            => 'dummy_assigned_to',
			) );
			?>
			<span
				class="description"><?php _e( 'Du kannst "Dummy" -Dienstanbieter definieren, um Deine Dienstanbieteralternativen zu bereichern und Deine Arbeitspläne zu variieren. Sie verhalten sich genau wie normale Benutzer, außer dass die E-Mails, die sie erhalten sollen, an den hier ausgewählten Benutzer weitergeleitet werden. Hinweis: Du kannst keinen anderen Dummy-Benutzer auswählen. Es muss ein Benutzer sein, der nicht als Dummy festgelegt ist.', 'appointments' ) ?></span>
		</td>
	</tr>

	<tr>
		<th scope="row"><label for="login_required"><?php _e( 'Login benötigt', 'appointments' ) ?></label></th>
		<td colspan="2" class="app_login_required">
            <?php _appointments_html_chceckbox( $options, 'login_required', 'api_detail' ); ?>
			<p class="description"><?php _e( 'Gibt an, ob sich der Kunde auf der Website anmelden muss, um einen Termin zu vereinbaren. Mit dem Plugin können sie sich im Front-End anmelden, ohne die Front-End-Terminseite verlassen zu müssen.', 'appointments' ) ?></p>
		</td>
	</tr>

	<?php
	if ( 'yes' != $options['login_required'] ) {
		$style = 'style="display:none"';
	} else {
		$style = '';
	}
	?>

	<tr class="api_detail" <?php echo $style ?>>
		<th scope="row"><label
				for="accept_api_logins"><?php _e( 'Akzeptiere die Anmeldung im Frontend', 'appointments' ) ?></label></th>
		<td>
            <?php _appointments_html_chceckbox( $options, 'accept_api_logins' ); ?>
			<p class="description"><?php _e( 'Ermöglicht die Anmeldung auf der Website vom Frontend über Facebook, Twitter, Google oder ClassicPress.', 'appointments' ) ?></p>
		</td>
	</tr>

	<tr class="api_detail" <?php echo $style ?>>
		<th scope="row"><label
				for="facebook-no_init"><?php _e( 'Meine Website nutzt bereits Facebook', 'appointments' ) ?></label></th>
		<td>
            <?php _appointments_html_chceckbox( $options, 'facebook-no_init' ); ?>
			<p class="description"><?php _e( 'Standardmäßig wird das Facebook-Skript vom Plugin geladen. Wenn Du bereits Facebook-Skripte ausführst, aktiviere diese Option, um Konflikte zu vermeiden.', 'appointments' ) ?></p>
		</td>
	</tr>

	<tr class="api_detail" <?php echo $style ?>>
		<th scope="row"><label for="facebook-app_id"><?php _e( 'Facebook App ID', 'appointments' ) ?></label></th>
		<td>
			<input type="text" class="widefat" name="facebook-app_id" id="facebook-app_id"
			       value="<?php if ( isset( $options['facebook-app_id'] ) ) {
						echo esc_attr( $options['facebook-app_id'] );
} ?>"/>
			<p class="description"><?php printf( __( "Gib hier Deine App-ID ein. Wenn Du noch keine Facebook-App hast, musst Du eine <a href='%s'>HIER</a> erstellen", 'appointments' ), 'https://developers.facebook.com/apps' ) ?></p>
		</td>
	</tr>

	<tr class="api_detail" <?php echo $style ?>>
		<th scope="row"><label for="twitter-app_id"><?php _e( 'Twitter Consumer Key', 'appointments' ) ?></label></th>
		<td>
			<input type="text" class="widefat" name="twitter-app_id" id="twitter-app_id"
			       value="<?php if ( isset( $options['twitter-app_id'] ) ) {
						echo esc_attr( $options['twitter-app_id'] );
} ?>"/>
			<p class="description"><?php printf( __( 'Gib hier Deine Twitter App ID-Nummer ein. Wenn Du noch keine Twitter-App hast, musst Du eine <a href="%s">HIER</a> erstellen', 'appointments' ), 'https://dev.twitter.com/apps/new' ) ?></p>
		</td>
	</tr>

	<tr class="api_detail" <?php echo $style ?>>
		<th scope="row"><label for="twitter-app_secret"><?php _e( 'Twitter Consumer Secret', 'appointments' ) ?></label>
		</th>
		<td>
			<input type="text" class="widefat" name="twitter-app_secret" id="twitter-app_secret"
			       value="<?php if ( isset( $options['twitter-app_secret'] ) ) {
						echo esc_attr( $options['twitter-app_secret'] );
} ?>"/>
			<p class="description"><?php _e( 'Gib hier Deine Twitter App ID Secret ein.', 'appointments' ) ?></p>
		</td>
	</tr>
	<?php do_action( 'app-settings-accessibility_settings', $style ); ?>
</table>
