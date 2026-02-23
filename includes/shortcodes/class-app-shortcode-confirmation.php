<?php

class App_Shortcode_Confirmation extends App_Shortcode {
	public function __construct() {
		$this->name = __( 'Bestätigung', 'appointments' );
	}

	public function get_defaults() {
		return array(
			'title' => array(
				'type' => 'text',
				'name' => __( 'Titel', 'appointments' ),
				'value' => sprintf( '<h3>%s</h3>', esc_html__( 'Bitte überprüfe die Termindetails unten und bestätige diese:', 'appointments' ) ),
				'help' => __( 'Text über Feldern. Standard: "Bitte überprüfe die unten stehenden Termindetails und bestätige diese:"', 'appointments' ),
			),
			'button_text' => array(
				'type' => 'text',
				'name' => __( 'Schaltflächentext', 'appointments' ),
				'value' => __( 'Bitte klicke hier, um diesen Termin zu bestätigen', 'appointments' ),
				'help' => __( 'Text der Schaltfläche, die den Kunden auffordert, den Termin zu bestätigen. Standard: "Bitte klicke hier, um diesen Termin zu bestätigen."', 'appointments' ),
			),
			'confirm_text' => array(
				'type' => 'text',
				'name' => __( 'Bestätigungs Text', 'appointments' ),
				'value' => __( 'Wir haben Deinen Termin erhalten. Vielen Dank!', 'appointments' ),
				'help' => __( 'Javascript-Text, der nach Erhalt des Termins angezeigt wird. Dies wird nur angezeigt, wenn sie keine Zahlung benötigen. Voreinstellung: "Wir haben Deinen Termin erhalten. Danke!"', 'appointments' ),
			),
			'warning_text' => array(
				'type' => 'text',
				'name' => __( 'Warnungstext', 'appointments' ),
				'value' => __( 'Bitte fülle das gewünschte Feld aus','appointments' ),
				'help' => __( ' Javascript-Text wird angezeigt, wenn der Client ein erforderliches Feld nicht ausfüllt. Voreinstellung: "Bitte fülle das gewünschte Feld aus"', 'appointments' ),
			),
			'name' => array(
				'type' => 'text',
				'name' => __( 'Name Label', 'appointments' ),
				'value' => __( 'Dein Name:','appointments' ),
				'help' => __( 'Beschreibender Titel des Feldes.', 'appointments' ),
			),
			'email' => array(
				'type' => 'text',
				'name' => __( 'Email Label', 'appointments' ),
				'value' => __( 'Deine eMail:','appointments' ),
				'help' => __( 'Beschreibender Titel des Feldes.', 'appointments' ),
			),
			'phone' => array(
				'type' => 'text',
				'name' => __( 'Telefonlabel', 'appointments' ),
				'value' => __( 'Deine Telefonnummer:','appointments' ),
				'help' => __( 'Beschreibender Titel des Feldes.', 'appointments' ),
			),
			'address' => array(
				'type' => 'text',
				'name' => __( 'Addresslabel', 'appointments' ),
				'value' => __( 'Deine Adresse:','appointments' ),
				'help' => __( 'Beschreibender Titel des Feldes.', 'appointments' ),
			),
			'city' => array(
				'type' => 'text',
				'name' => __( 'Stadt/Ort label', 'appointments' ),
				'value' => __( 'Stadt/Ort:','appointments' ),
				'help' => __( 'Beschreibender Titel des Feldes.', 'appointments' ),
			),
			'note' => array(
				'type' => 'text',
				'name' => __( 'Anmerkungen Label', 'appointments' ),
				'value' => __( 'Deine Anmerkungen:','appointments' ),
				'help' => __( 'Beschreibender Titel des Feldes.', 'appointments' ),
			),
			'gcal' => array(
				'type' => 'text',
				'name' => __( 'Google Cal Label', 'appointments' ),
				'value' => __( 'Google Kalender öffnen und Termin syncronisieren','appointments' ),
				'help' => __( 'Text, der neben dem Kontrollkästchen "Google Kalender" angezeigt wird. Standard: "Google Kalender öffnen und Termin syncronisieren"', 'appointments' ),
			),
		);
	}

	public function get_usage_info() {
		return '' .
		       __( 'Fügt ein Formular ein, das die Details des ausgewählten Termins anzeigt und Felder enthält, die vom Kunden ausgefüllt werden sollten.', 'appointments' ) .
		       '<br />' .
		       __( '<b>Dieser Shortcode ist immer erforderlich, um einen Termin abzuschließen.</b>', 'appointments' ) .
		       '';
	}

	public function process_shortcode( $args = array(), $content = '' ) {
		$args = wp_parse_args( $args, $this->_defaults_to_args() );
		extract( $args );

		global $appointments;

		// Get user form data from his cookie
		$data = Appointments_Sessions::get_visitor_personal_data();

		$n = isset( $data['n'] ) ? sanitize_text_field( $data['n'] ) : ''; // Name
		$e = isset( $data['e'] ) ? sanitize_text_field( $data['e'] ) : ''; // Email
		$p = isset( $data['p'] ) ? sanitize_text_field( $data['p'] ) : ''; // Phone
		$a = isset( $data['a'] ) ? sanitize_text_field( $data['a'] ) : ''; // Address
		$c = isset( $data['c'] ) ? sanitize_text_field( $data['c'] ) : ''; // City
		$g = isset( $data['g'] ) ? sanitize_text_field( $data['g'] ) : ''; // GCal selection
		if ( $g ) {
			$gcal_checked = ' checked="checked"'; } else { 			$gcal_checked = ''; }

		// User may have already saved his data before
		if ( is_user_logged_in() ) {
			global $current_user;
			$user_info = get_userdata( $current_user->ID );

			$name_meta = get_user_meta( $current_user->ID, 'app_name', true );
			if ( $name_meta ) {
				$n = $name_meta; } else if ( $user_info->display_name ) {
				$n = $user_info->display_name; } else if ( $user_info->user_nicename ) {
					$n = $user_info->user_nicename; } else if ( $user_info->user_login ) {
					$n = $user_info->user_login; }

					$email_meta = get_user_meta( $current_user->ID, 'app_email', true );
					if ( $email_meta ) {
						$e = $email_meta; } else if ( $user_info->user_email ) {
						$e = $user_info->user_email; }

						$phone_meta = get_user_meta( $current_user->ID, 'app_phone', true );
						if ( $phone_meta ) {
							$p = $phone_meta; }

						$address_meta = get_user_meta( $current_user->ID, 'app_address', true );
						if ( $address_meta ) {
							$a = $address_meta; }

						$city_meta = get_user_meta( $current_user->ID, 'app_city', true );
						if ( $city_meta ) {
							$c = $city_meta; }
		}
		$ret = '';
		/**
		 * GDPR
		 */
		$gdpr_checkbox_show = appointments_get_option( 'gdpr_checkbox_show' );
		$gdpr_checkbox_show = 'yes' === $gdpr_checkbox_show;
		ob_start();
		?>
		<div class="appointments-confirmation-wrapper">
			<fieldset class="<?php echo esc_attr( $gdpr_checkbox_show? 'check-gdpr-agree':'' ); ?>">
				<legend><?php echo $args['title']; ?></legend>
				<div class="appointments-confirmation-service"></div>
				<div class="appointments-confirmation-service_location" style="display:none"></div>
				<div class="appointments-confirmation-worker" style="display:none"></div>
				<div class="appointments-confirmation-worker_location" style="display:none"></div>
				<div class="appointments-confirmation-start"></div>
				<div class="appointments-confirmation-end"></div>
				<div class="appointments-confirmation-price" style="display:none"></div>

				<div class="appointments-name-field" style="display:none">
					<label>
						<span><?php echo $args['name']; ?><b class="required">*</b></span>
						<input type="text" class="appointments-name-field-entry" id="<?php echo esc_attr( apply_filters( 'app-shortcode-confirmation-name_field_id', 'appointments-field-customer_name' ) ); ?>" value="<?php echo esc_attr( $n ); ?>" />
					</label>
				</div>
				<div class="appointments-email-field" style="display:none">
					<label>
						<span><?php echo $args['email']; ?><b class="required">*</b></span>
						<input type="text" class="appointments-email-field-entry" id="<?php echo esc_attr( apply_filters( 'app-shortcode-confirmation-email_field_id', 'appointments-field-customer_email' ) ); ?>" value="<?php echo esc_attr( $e ); ?>" />
					</label>
				</div>
				<div class="appointments-phone-field" style="display:none">
					<label>
						<span><?php echo $args['phone']; ?><b class="required">*</b></span>
						<input type="text" class="appointments-phone-field-entry" id="<?php echo esc_attr( apply_filters( 'app-shortcode-confirmation-phone_field_id', 'appointments-field-customer_phone' ) ); ?>" value="<?php echo esc_attr( $p ); ?>" />
					</label>
				</div>
				<div class="appointments-address-field" style="display:none">
					<label>
						<span><?php echo $args['address']; ?><b class="required">*</b></span>
						<input type="text" class="appointments-address-field-entry" id="<?php echo esc_attr( apply_filters( 'app-shortcode-confirmation-address_field_id', 'appointments-field-customer_address' ) ); ?>" value="<?php echo esc_attr( $a ); ?>" />
					</label>
				</div>
				<div class="appointments-city-field" style="display:none">
					<label>
						<span><?php echo $args['city']; ?><b class="required">*</b></span>
						<input type="text" class="appointments-city-field-entry" id="<?php echo esc_attr( apply_filters( 'app-shortcode-confirmation-city_field_id', 'appointments-field-customer_city' ) ); ?>" value="<?php echo esc_attr( $c ); ?>" />
					</label>
				</div>
				<div class="appointments-note-field" style="display:none">
					<label>
						<span><?php echo $args['note']; ?><b class="required">*</b></span>
						<input type="text" class="appointments-note-field-entry" id="<?php echo esc_attr( apply_filters( 'app-shortcode-confirmation-note_field_id', 'appointments-field-customer_note' ) ); ?>" />
					</label>
				</div>
				<div class="appointments-gcal-field" style="display:none">
					<label>
						<span><?php echo $appointments->gcal_image; ?></span>
						<input type="checkbox" class="appointments-gcal-field-entry" id="<?php echo esc_attr( apply_filters( 'app-shortcode-confirmation-gcal_field_id', 'appointments-field-customer_gcal' ) ); ?>" <?php echo $gcal_checked; ?> />&nbsp;
						<?php echo $args['gcal']; ?>
					</label>
				</div>
				<?php
					$ret = ob_get_clean();
					$ret = apply_filters( 'app_additional_fields', $ret );
					ob_start();
				?>
				<div style="clear:both"></div>
<?php if ( $gdpr_checkbox_show ) { ?>
				<div class="appointments-gdpr-confirmation">
					<label data-alert="<?php echo esc_attr( appointments_get_option( 'gdpr_checkbox_alert' ) ); ?>">
						<input type="checkbox" class="appointments-gdpr-agree" id="<?php echo esc_attr( apply_filters( 'app-shortcode-confirmation-gdpr_field_id', 'appointments-field-gdpr-agree' ) ); ?>" /><b class="required">*</b>&nbsp;<?php echo appointments_get_option( 'gdpr_checkbox_text' ); ?>
					</label>
				</div>
<?php } ?>
				<div class="appointments-confirmation-buttons">
					<input type="hidden" class="appointments-confirmation-final-value" />
					<input type="button" class="appointments-confirmation-button" value="<?php echo esc_attr( $button_text ); ?>" />
					<input type="button" class="appointments-confirmation-cancel-button" value="<?php echo esc_attr_x( 'Stornieren', 'Aktuelle Aktion löschen', 'appointments' ); ?>" />
				</div>
			</fieldset>
		</div>

		<?php
		$ret_confirmation_fields = ob_get_clean();
		$ret_confirmation_fields = apply_filters( 'app_confirmation_fields', $ret_confirmation_fields );
		$ret = $ret . $ret_confirmation_fields;

		_appointments_enqueue_sweetalert();
		wp_enqueue_script( 'app-shortcode-confirmation', appointments_plugin_url() . 'includes/shortcodes/js/app-confirmation.js', array( 'jquery', 'app-sweetalert' ) );
		$schema = is_ssl()? 'https':'http';
		$i10n = array(
			'waitingGif' => appointments_plugin_url() . 'images/waiting.gif',
			'isUserLoggedIn' => is_user_logged_in(),
			'loginRequired' => $appointments->options['login_required'],
			'ajaxurl' => admin_url( 'admin-ajax.php', $schema ),
			'nonce' => wp_create_nonce( 'AppShortcodeConfirmation' ),
			'askName' => $appointments->options['ask_name'],
			'askEmail' => $appointments->options['ask_email'],
			'askPhone' => $appointments->options['ask_phone'],
			'askAddress' => $appointments->options['ask_address'],
			'askCity' => $appointments->options['ask_city'],
			'askNote' => $appointments->options['ask_note'],
			'askGDPR' => $appointments->options['ask_note'],
			'askGCal' => isset( $appointments->options['gcal'] ) && 'yes' == $appointments->options['gcal'],
			'askGDPR' => isset( $appointments->options['gdpr_checkbox_show'] ) && 'yes' == $appointments->options['gdpr_checkbox_show'],
			'warningText' => esc_js( $args['warning_text'] ),
			'confirmationText' => esc_js( $args['confirm_text'] ),
			'connectionErrorText' => esc_js( __( 'A connection problem occurred. Please try again.','appointments' ) ),
			'GDPRmissingText' => esc_js( $appointments->options['gdpr_checkbox_alert'] ),
			'errorTitle' => esc_js( __( 'Error', 'appointments' ) ),
		);
		wp_localize_script( 'app-shortcode-confirmation', 'AppShortcodeConfirmation', $i10n );
		return $ret;
	}
}
