<h3><?php _e( 'Google Kalender-API: Erstelle eine neue Google-Anwendung', 'appointments' ); ?></h3>
<h4><?php esc_html_e( 'Anleitung:', 'appointments' ); ?></h4>

<div class="gcal-slider">
	<ul>
		<li>
			<img src="<?php echo appointments_plugin_url() . 'images/gcal-instructions-01.png'; ?>" alt="">
			<p>1. <?php printf( __( 'Gehe zu %s und erstelle ein neues Projekt. Z.B. "Terminmanager APP", dann klicke "Create".', 'appointments' ), sprintf( '<a target=_blank" href="https://console.developers.google.com/project">%s</a>', __( 'Google Developer Console Projects', 'appointments' ) ) ); ?></p>
		</li>
		<li>
			<img src="<?php echo appointments_plugin_url() . 'images/gcal-instructions-02.png'; ?>" alt="">
			<p>2. <?php _e( 'Klicke im Dashboard auf "Enable and manage APIs".', 'appointments' ); ?></p>
		</li>
		<li>
			<img src="<?php echo appointments_plugin_url() . 'images/gcal-instructions-03.png'; ?>" alt="">
			<p>3. <?php _e( 'Klicke auf "Calendar API".', 'appointments' ); ?></p>
		</li>
		<li>
			<img src="<?php echo appointments_plugin_url() . 'images/gcal-instructions-04.png'; ?>" alt="">
			<p>4. <?php _e( 'Aktiviere die "Calendar API".', 'appointments' ); ?></p>
		</li>
		<li>
			<img src="<?php echo appointments_plugin_url() . 'images/gcal-instructions-05.png'; ?>" alt="">
			<p>5. <?php _e( 'Klicke auf der linken Seite auf "Credentials"...', 'appointments' ); ?></p>
		</li>
		<li>
			<img src="<?php echo appointments_plugin_url() . 'images/gcal-instructions-06.png'; ?>" alt="">
			<p>6. <?php _e( '... und dann "OAuth consent screen" Tab. Wähle einen Produktnamen, der den Benutzern angezeigt wird, z.B. "Terminmanager".', 'appointments' ); ?></p>
		</li>
		<li>
			<img src="<?php echo appointments_plugin_url() . 'images/gcal-instructions-07.png'; ?>" alt="">
			<p>7. <?php _e( 'Klicke erneut auf "Credentials" Tab und dann Create Credentials.', 'appointments' ); ?></p>
		</li>
		<li>
			<img src="<?php echo appointments_plugin_url() . 'images/gcal-instructions-08.png'; ?>" alt="">
			<p>8. <?php _e( 'Wähle die "Oauth client ID" Option.', 'appointments' ); ?></p>
		</li>
		<li>
			<img src="<?php echo appointments_plugin_url() . 'images/gcal-instructions-09.png'; ?>" alt="">
			<p>9. <?php _e( 'Wähle "Other" Anwendungstyp mit einem beliebigen Namen, der Name ist nicht wichtig.', 'appointments' ); ?></p>
		</li>
		<li>
			<img src="<?php echo appointments_plugin_url() . 'images/gcal-instructions-10.png'; ?>" alt="">
			<p>10. <?php _e( 'Notiere Dir die Client-ID und das Client-Secret und fülle das folgende Formular aus.', 'appointments' ); ?></p>
		</li>
	</ul>
</div>

<table class="form-table">
	<tr>
		<th scope="row">
			<label for="app-client-id"><?php _e( 'Client ID', 'appointments' ); ?></label>
		</th>
		<td>
			<input type="text" class="widefat" name="client_id" id="app-client-id" value="">
		</td>
	</tr>
	<tr>
		<th scope="row">
			<label for="app-client-secret"><?php _e( 'Client Secret', 'appointments' ); ?></label>
		</th>
		<td>
			<input type="text" name="client_secret" class="widefat" id="app-client-secret" value="">
		</td>
	</tr>
</table>

<?php wp_nonce_field( 'app-submit-gcalendar' ); ?>
<input type="hidden" name="action" value="step-1">
<?php submit_button( __( 'Übermitteln', 'appointments' ), 'primary', 'app-submit-gcalendar' ); ?>
