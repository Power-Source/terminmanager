<?php

class App_Tutorial {

	public $set_textdomain;
    public $set_capability;

	private function __construct() {}

	public static function serve() {
		$me = new App_Tutorial;
		$me->_add_hooks();
	}

	private function _add_hooks() {
		add_action( 'admin_init', array( $this, 'tutorial1' ) );							// Add tutorial 1
		add_action( 'admin_init', array( $this, 'tutorial2' ) );							// Add tutorial 2
	}

	function tutorial1() {
		global $appointments;
		//load the file
		if ( ! class_exists( 'Pointer_Tutorial' ) ) {
			require_once( appointments_plugin_dir() . 'includes/external/pointer-tutorials.php' );
		}

		//create our tutorial, with default redirect prefs
		$tutorial = new Pointer_Tutorial( 'app_tutorial1', true, false );

		/**
		 * restart
		 */
		if ( isset( $_GET['tutorial'] ) && 'restart2' == $_GET['tutorial'] ) {
			$tutorial->restart();
		}
		//add our textdomain that matches the current plugin
		// Ensure the Pointer_Tutorial class has a set_textdomain method or remove this line if unnecessary.
		if (method_exists($tutorial, 'set_textdomain')) {
			// Ensure the Pointer_Tutorial class has a set_textdomain method or remove this line if unnecessary.
			if (method_exists($tutorial, 'set_textdomain')) {
				$tutorial->set_textdomain('appointments');
			}
		}

		//add the capability a user must have to view the tutorial
		$tutorial->set_capability(App_Roles::get_capability('manage_options', App_Roles::CTX_TUTORIAL));

		$tutorial->add_icon( $appointments->plugin_url . '/images/large-greyscale.png' );

		$title = sanitize_title( __( 'Terminmanager', 'appointments' ) );
		$settings = admin_url( 'admin.php?page=app_settings' );

		$tutorial->add_step($settings, $title . '_page_app_settings', '.menu-top .toplevel_page_appointments', __( 'Terminmanager Tutorial', 'appointments' ), array(
		    'content'  => '<p>' . esc_js( __( 'Willkommen beim PS Terminmanager Plugin. Dieses Tutorial wird Dir hoffentlich dabei helfen, einen schnellen Start zu erzielen, indem Du die wichtigsten Einstellungen an Deine Bedürfnisse anpasst. Du kannst dieses Tutorial jederzeit neu starten, indem Du auf den Link auf der FAQ-Seite klickst.', 'appointments' ) ) . '</p>',
		    'position' => array( 'edge' => 'left', 'align' => 'center' ),
		));

		$tutorial->add_step($settings, $title . '_page_app_settings', 'select[name="min_time"]', __( 'Zeitbasis', 'appointments' ), array(
		    'content'  => '<p>' . esc_js( __( 'Die Zeitbasis ist der wichtigste Parameter von PS Terminmanager. Dies ist die Mindestzeit, die Du für Deine Termine auswählen kannst. Wenn Du es zu hoch einstellst, kannst Du Deine Termine möglicherweise nicht optimieren. Wenn Du es zu niedrig einstelst, ist Dein Zeitplan zu voll und Du hast möglicherweise Schwierigkeiten, Deine Termine zu verwalten. Gib hier die Dauer des kürzesten von Dir angebotenen Dienstes ein.', 'appointments' ) ) . '</p>',
		    'position' => array( 'edge' => 'left', 'align' => 'center' ),
		));

		$tutorial->add_step($settings. '&step=1#section-display', $title . '_page_app_settings', 'select[name="app_page_type"]', __( 'Erstellen einer funktionalen Frontend-Terminseite', 'appointments' ), array(
		    'content'  => '<p>' . esc_js( __( 'Du kannst einen Zeitplantyp aus der Liste auswählen. Um zu sehen, wie sie aussehen, kannst Du auch mehrere Seiten einzeln erstellen und dann nicht verwendete Seiten löschen.', 'appointments' ) ).'</p>',
		    'position' => array( 'edge' => 'right', 'align' => 'center' ),
		));

		$tutorial->add_step($settings. '&step=1#section-display', $title . '_page_app_settings', '#app_create_page_button', __( 'Erstellen einer funktionalen Frontend-Terminseite', 'appointments' ), array(
		    'content'  => '<p>' . esc_js( __( 'Drücke die "Seite erstellen!" Schaltfläche, um alle Shortcodes in eine voll funktionsfähige Seite aufzunehmen. Du kannst diese Seite später bearbeiten.', 'appointments' ) ).'</p>',
		    'position' => array( 'edge' => 'left', 'align' => 'center' ),
		));

		$tutorial->add_step($settings . '&step=1#section-display', $title . '_page_app_settings', 'select[name="color_set"]', __( 'Auswählen eines Farbsatzes, der zu Deinem Theme passt', 'appointments' ), array(
		    'content'  => '<p>' . esc_js( __( 'Es ist möglich, Farbsätze für Deine Zeitplantabellen aus vordefinierten Sätzen auszuwählen oder anzupassen. Wenn Du Benutzerdefiniert auswählst, kannst Du Deine eigenen Farben für verschiedene Status festlegen (Besetzt, kostenlos, nicht möglich/funktioniert nicht).', 'appointments' ) ).'</p>',
		    'position' => array( 'edge' => 'right', 'align' => 'center' ),
		));

		$tutorial->add_step($settings . '#section-accesibility', $title . '_page_app_settings', '.app_login_required span.off', __( 'Benötigst Du einen Login?', 'appointments' ), array(
		    'content'  => '<p>' . esc_js( __( 'Du kannst festlegen, ob sich der Kunde auf der Webseite anmelden muss, um einen Termin zu beantragen. Wenn Du diese Einstellung als Ja auswählst, werden zusätzliche Einstellungen angezeigt.', 'appointments' ) ).'</p>',
		    'position' => array( 'edge' => 'left', 'align' => 'center' ),
		));

		$tutorial->add_step($settings . '&step=1#section-display', $title . '_page_app_settings', 'input:checkbox[name="ask_name"]', __( 'Informationen vom Kunden anfordern', 'appointments' ), array(
		    'content'  => '<p>' . esc_js( __( 'Du kannst den Kunden bitten, einige auswählbare Felder auszufüllen, damit er sich möglicherweise nicht auf Deiner Webseite registrieren muss.', 'appointments' ) ).'</p>',
		    'position' => array( 'edge' => 'right', 'align' => 'center' ),
		));

		$tutorial->add_step($settings . '#section-payments', $title . '_page_app_settings', '.app_payment_required span.off', __( 'Benötigst Du eine Zahlung?', 'appointments' ), array(
		    'content'  => '<p>' . esc_js( __( 'Du kannst festlegen, ob der Kunde um eine Zahlung gebeten wird, um seinen Termin zu akzeptieren. Wenn diese Einstellung als Ja ausgewählt ist, steht der Termin aus, bis eine erfolgreiche Paypal-Zahlung abgeschlossen ist. Nachdem Du dies ausgewählt hast, werden zusätzliche Felder für Dein Paypal-Konto, Einzahlungen und die Integration in das Mitgliedschafts-Plugin angezeigt.', 'appointments' ) ).'</p>',
		    'position' => array( 'edge' => 'left', 'align' => 'center' ),
		));

		$tutorial->add_step($settings . '&step=1#section-notifications', $title . '_page_app_settings', '.subsubsub .current', __( 'E-Mail Benachrichtigungen', 'appointments' ), array(
		    'content'  => '<p>' . esc_js( __( 'Es gibt verschiedene Benachrichtigungseinstellungen. Mit diesen kannst Du Deine Kunden und auch Deine Dienstleister bestätigen und daran erinnern.', 'appointments' ) ).'</p>',
		    'position' => array( 'edge' => 'left', 'align' => 'center' ),
		));

		$tutorial->add_step($settings . '&step=1#section-notifications', $title . '_page_app_settings', '.button-primary', __( 'Einstellungen speichern', 'appointments' ), array(
		    'content'  => '<p>' . esc_js( __( 'Vergiss nicht, Deine Einstellungen zu speichern.', 'appointments' ) ).'</p>',
		    'position' => array( 'edge' => 'left', 'align' => 'center' ),
		));

		$tutorial->add_step($settings . '&step=1#section-notifications', $title . '_page_app_settings', '#app_tab_working_hours', __( 'Festlegen Deiner Geschäftszeiten', 'appointments' ), array(
		    'content'  => '<p>' . esc_js( __( 'Jetzt solltest Du Deine Geschäftsarbeitszeiten festlegen. Klicke auf die Registerkarte Arbeitszeiten und dann auf Weiter.', 'appointments' ) ).'</p>',
		    'position' => array( 'edge' => 'left', 'align' => 'center' ),
		));

		$tutorial->add_step($settings . '&tab=working_hours', $title . '_page_app_settings', 'select[name="app_provider_id"]', __( 'Festlegen Deiner Geschäftszeiten', 'appointments' ), array(
		    'content'  => '<p>' . esc_js( __( 'Unten siehst Du zwei Tabellen, eine für Deine Arbeitszeit und eine für Deine Pausenzeiten während des Tages. Der zweite ist optional. Links siehst Du noch keine Auswahlmöglichkeiten. Wenn Du jedoch neue Dienstanbieter hinzufügst, kannst Du deren Arbeits- und Pausenzeiten festlegen, indem Du aus diesem Dropdown-Menü auswählst. Dies ist nur erforderlich, wenn sich die Arbeitszeiten von denen Deines Unternehmens unterscheiden.', 'appointments' ) ).'</p>',
		    'position' => array( 'edge' => 'left', 'align' => 'center' ),
		));

		$tutorial->add_step($settings . '&tab=working_hours', $title . '_page_app_settings', '.button-primary', __( 'Einstellungen speichern', 'appointments' ), array(
		    'content'  => '<p>' . esc_js( __( 'Vergiss nicht, Deine Einstellungen zu speichern.', 'appointments' ) ).'</p>',
		    'position' => array( 'edge' => 'left', 'align' => 'center' ),
		));

		$tutorial->add_step($settings . '&tab=working_hours', $title . '_page_app_settings', '#app_tab_exceptions', __( 'Gib Deinen Urlaub ein', 'appointments' ), array(
		    'content'  => '<p>' . esc_js( __( 'Klicke auf die Registerkarte Ausnahmen, um Deine Feiertage zu definierst, und klicke dann auf Weiter.', 'appointments' ) ).'</p>',
		    'position' => array( 'edge' => 'left', 'align' => 'center' ),
		));

		$tutorial->add_step($settings . '&tab=exceptions', $title . '_page_app_settings', 'select[name="app_provider_id"]', __( 'Außergewöhnliche Tage setzen', 'appointments' ), array(
		    'content'  => '<p>' . esc_js( __( 'Im Folgenden kannst Du Deine Feiertage und außergewöhnlichen Arbeitstage definieren, z.B. einen bestimmten Sonntag, an dem Du arbeiten möchtest. Diese Daten überschreiben Deinen wöchentlichen Arbeitsplan nur für diesen Tag. Beachte, dass Du diese außergewöhnlichen Tage für jeden Dienstanbieter einzeln festlegen kannst, wenn Du sie definierst.', 'appointments' ) ).'</p>',
		    'position' => array( 'edge' => 'left', 'align' => 'center' ),
		));

		$tutorial->add_step($settings . '&tab=exceptions', $title . '_page_app_settings', '#app_tab_services', __( 'Festlegen Deiner Dienste', 'appointments' ), array(
		    'content'  => '<p>' . esc_js( __( 'Klicke auf die Registerkarte Dienste, um Deine Dienste festzulegen, und klicke dann auf Weiter.', 'appointments' ) ).'</p>',
		    'position' => array( 'edge' => 'left', 'align' => 'center' ),
		));

		$tutorial->add_step($settings . '&tab=services', $title . '_page_app_settings', '.services-new-service', __( 'Festlegen Deiner Dienste', 'appointments' ), array(
		    'content'  => '<p>' . esc_js( __( 'Du kannst einen neuen Dienst hinzufügen, indem Du auf diese Schaltfläche klickst. Während der Installation sollte ein Standarddienst installiert worden sein. Du kannst das auch bearbeiten und sogar löschen, aber Du solltest mindestens einen Dienst in dieser Tabelle haben.', 'appointments' ) ).'</p>',
		    'position' => array( 'edge' => 'left', 'align' => 'center' ),
		));

		$tutorial->add_step($settings . '&tab=services', $title . '_page_app_settings', '.button-primary', __( 'Einstellungen speichern', 'appointments' ), array(
		    'content'  => '<p>' . esc_js( __( 'Vergiss nicht, Deinee Einstellungen zu speichern. Wenn Du auf die Schaltfläche Neuen Dienst hinzufügen klickst, wird dieser NICHT in der Datenbank gespeichert, bis Du auf die Schaltfläche Speichern klickst.', 'appointments' ) ).'</p>',
		    'position' => array( 'edge' => 'left', 'align' => 'center' ),
		));

		$tutorial->add_step($settings . '&tab=services', $title . '_page_app_settings', '#app_tab_workers', __( 'Hinzufügen und Festlegen Deiner Dienstanbieter', 'appointments' ), array(
		    'content'  => '<p>' . esc_js( __( 'Klicke auf die Registerkarte Dienstanbieter, um Deine Dienstanbieter festzulegen, und klicke dann auf Weiter.', 'appointments' ) ).'</p>',
		    'position' => array( 'edge' => 'right', 'align' => 'center' ),
		));

		$tutorial->add_step($settings . '&tab=workers', $title . '_page_app_settings', '.workers-new-worker', __( 'Hinzufügen von Dienstanbietern', 'appointments' ), array(
		    'content'  => '<p>' . esc_js( __( 'Das Hinzufügen von Dienstanbietern ist optional. Dies ist möglicherweise erforderlich, wenn der Arbeitsplan Deiner Dienstanbieter unterschiedlich ist oder der Kunde einen Anbieter anhand seines Namens auswählen soll. Du kannst einen neuen Dienstanbieter hinzufügen, indem Du auf diese Schaltfläche klickst. ', 'appointments' ) ).'</p>',
		    'position' => array( 'edge' => 'left', 'align' => 'center' ),
		));

		/*
		$tutorial->add_step($settings . '&tab=workers', $title . '_page_app_settings', '#app_tab_shortcodes', __( 'Additional Information', 'appointments' ), array(
		    'content'  => '<p>' . esc_js( __( 'You can find detailed information about shortcode parameters on the Shortcodes page and answers to common questions on the FAQ page. Of course we will be glad to help you on our Community pages too.', 'appointments' ) ).'</p>',
		    'position' => array( 'edge' => 'right', 'align' => 'center' ),
        ));
         */

		$tutorial->add_step($settings . '&tab=workers', $title . '_page_app_settings', 'a.wp-first-item:contains("Appointments")', __( 'Terminliste', 'appointments' ), array(
		    'content'  => '<p>' . esc_js( __( 'Sobald Du Termine erhältst, werden diese hier angezeigt. Klicke auf den Menüpunkt Termine, um das andere Lernprogramm zu starten, falls Du es noch nicht gesehen hast.', 'appointments' ) ).'</p>',
		    'position' => array( 'edge' => 'left', 'align' => 'center' ),
		));

		if ( isset( $_GET['tutorial'] ) && 'restart1' == $_GET['tutorial'] ) {
			$tutorial->restart();
		}

		//start the tutorial
		$tutorial->initialize();

		return $tutorial;
	}

	function tutorial2() {
		global $appointments;
		//load the file
		if ( ! class_exists( 'Pointer_Tutorial' ) ) {
			require_once( appointments_plugin_dir() . 'includes/external/pointer-tutorials.php' );
		}

		//create our tutorial, with default redirect prefs
		$tutorial = $this->get_pointer_tutorial( 'app_tutorial2' );

		//add our textdomain that matches the current plugin
		if ( method_exists( $tutorial, 'set_textdomain' ) ) {
			$tutorial->set_textdomain( 'appointments' );
		}

		//add the capability a user must have to view the tutorial
		$tutorial->set_capability(App_Roles::get_capability('manage_options', App_Roles::CTX_TUTORIAL));

		$tutorial->add_icon( $appointments->plugin_url . '/images/large-greyscale.png' );
		$appointments_page = admin_url( 'admin.php?page=appointments' );

		$tutorial->add_step($appointments_page, 'toplevel_page_appointments', '.info-button', __( 'Terminliste', 'appointments' ), array(
		    'content'  => '<p>' . esc_js( __( 'Termindatensätze werden nach ihrem Status gruppiert. Du kannst diese Gruppierungen anzeigen, indem Du auf das Info-Symbol klickst.', 'appointments' ) ).'</p>',
		    'position' => array( 'edge' => 'right', 'align' => 'center' ),
		));

		$tutorial->add_step($appointments_page, 'toplevel_page_appointments', '.add-new-h2', __( 'Manuellen Termin eingeben', 'appointments' ), array(
		    'content'  => '<p>' . esc_js( __( 'Wenn Du Termine von Deinen Kunden erhalten hast, werden diese automatisch zu dieser Seite hinzugefügt. Du kannst jedoch jederzeit manuell einen neuen Termin hinzufügen. Klicke auf den Link NEU HINZUFÜGEN und dann auf Weiter.', 'appointments' ) ).'</p>',
		    'position' => array( 'edge' => 'left', 'align' => 'top' ),
		));

		$tutorial->add_step($appointments_page, 'toplevel_page_appointments', 'select[name="status"]', __( 'Daten für den neuen Termin eingeben', 'appointments' ), array(
		    'content'  => '<p>' . esc_js( __( 'Wie Du siehst, kannst Du hier alle Parameter eingeben. Gib einige zufällige Werte ein und wähle für dieses Beispiel den Status PENDING. Klicke dann auf Weiter', 'appointments' ) ).'</p>',
		    'position' => array( 'edge' => 'right', 'align' => 'center' ),
		));

		$tutorial->add_step($appointments_page, 'toplevel_page_appointments', 'input[name="resend"]', __( 'Manuelles Senden von Bestätigungs-E-Mails', 'appointments' ), array(
		    'content'  => '<p>' . esc_js( __( 'Wenn Du eine Zahlung benötigst, wird nach einer Paypal-Zahlung automatisch eine Bestätigungs-E-Mail gesendet. Wenn Du jedoch Termine manuell bestätigst, solltest Du dieses Kontrollkästchen aktivieren, damit eine Bestätigungs-E-Mail gesendet wird. Du kannst diese Option auch zum erneuten Senden der Bestätigungs-E-Mail verwenden, z.B nach Terminverschiebung.', 'appointments' ) ).'</p>',
		    'position' => array( 'edge' => 'right', 'align' => 'center' ),
		));

		$tutorial->add_step($appointments_page, 'toplevel_page_appointments', '.save', __( 'Daten für den neuen Termin eingeben', 'appointments' ), array(
		    'content'  => '<p>' . esc_js( __( 'Speichere und klicke dann auf Weiter.', 'appointments' ) ).'</p>',
		    'position' => array( 'edge' => 'right', 'align' => 'center' ),
		));

		$tutorial->add_step($appointments_page, 'toplevel_page_appointments', '.error', __( 'Daten für den neuen Termin eingeben', 'appointments' ), array(
		    'content'  => '<p>' . esc_js( __( 'Das Ergebnis wird hier angezeigt. Normalerweise solltest Du eine Erfolgsmeldung erhalten. Andernfalls bedeutet dies, dass Du auf der Administratorseite ein Javascript-Problem hast.', 'appointments' ) ).'</p>',
		    'position' => array( 'edge' => 'left', 'align' => 'center' ),
		));

		$tutorial->add_step($appointments_page, 'toplevel_page_appointments', '.info-button', __( 'Neuen Termin speichern', 'appointments' ), array(
		    'content'  => '<p>' . esc_js( __( 'Wenn wir diesen Termin als "Ausstehend" hinzugefügt haben, wird er unter Ausstehende Termine angezeigt. Klicke auf Ausstehende Termine und dann auf Weiter.', 'appointments' ) ).'</p>',
		    'position' => array( 'edge' => 'left', 'align' => 'center' ),
		));

		$tutorial->add_step($appointments_page . '&type=pending', 'toplevel_page_appointments', '.info-button', __( 'Termin bearbeiten', 'appointments' ), array(
		    'content'  => '<p>' . esc_js( __( 'Du kannst jeden Termindatensatz bearbeiten. Bewege den Mauszeiger über den Datensatz und klicke dann auf Details anzeigen und Bearbeiten', 'appointments' ) ).'</p>',
		    'position' => array( 'edge' => 'left', 'align' => 'center' ),
		));

		$tutorial->add_step($appointments_page . '&type=pending', 'toplevel_page_appointments', '.cancel', _x( 'Abbrechen', 'Aktuelle Aktion löschen', 'appointments' ), array(
		    'content'  => '<p>' . esc_js( __( 'Es ist immer möglich abzubrechen. Bitte beachte, dass diese Datensätze NICHT gespeichert werden, bis Du auf die Schaltfläche Speichern klickst. Vielen Dank, dass Du PS Terminmanager verwendest', 'appointments' ) ).'</p>',
		    'position' => array( 'edge' => 'left', 'align' => 'center' ),
		));

		//start the tutorial
		$tutorial->initialize();

		return $tutorial;
	}

	private function get_pointer_tutorial( $id ) {
		if ( ! class_exists( 'Pointer_Tutorial' ) ) {
			require_once( appointments_plugin_dir() . 'includes/external/pointer-tutorials.php' );
		}
		return new Pointer_Tutorial( $id, true, false );
	}
}
