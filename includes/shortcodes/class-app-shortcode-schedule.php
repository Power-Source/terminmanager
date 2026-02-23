<?php
/**
 * Weekly schedule calendar shortcode.
 */
class App_Shortcode_WeeklySchedule extends App_Shortcode {
	public function __construct() {
		$this->name = __( 'Wochenplan', 'appointments' );
	}

	public function get_defaults() {
		$_workers = appointments_get_workers();
		$workers = array(
			array( 'text' => __( 'Jeder Anbieter', 'appointments' ), 'value' => 0 ),
		);
		foreach ( $_workers as $worker ) {
			/** @var Appointments_Worker $worker */
			$workers[] = array( 'text' => $worker->get_name(), 'value' => $worker->ID );
		}

		$_services = appointments_get_services();
		$services = array(
			array( 'text' => __( 'Jeder Service', 'appointments' ), 'value' => 0 ),
		);
		foreach ( $_services as $service ) {
			/** @var Appointments_Service $service */
			$services[] = array( 'text' => $service->name, 'value' => $service->ID );
		}

		return array(
			'title' => array(
				'type' => 'text',
				'name' => __( 'Titel', 'appointments' ),
				'value' => sprintf( '<h3>%s</h3>', esc_html__( 'Unser Zeitplan von START bis ENDE', 'appointments' ) ),
				'help' => __( 'Text, der als Zeitplantitel angezeigt wird. Die Platzhalter START und END werden automatisch durch ihre tatsächlichen Werte ersetzt.', 'appointments' ),
			),
			'logged' => array(
				'type' => 'text',
				'name' => __( 'Eingeloggt Text', 'appointments' ),
				'value' => __( 'Klicke auf ein freies Zeitfenster, um einen Termin zu vereinbaren.', 'appointments' ),
				'help' => __( 'Text, der nach dem Titel nur für die angemeldeten Kunden angezeigt wird oder für die sie keine Anmeldung benötigen.', 'appointments' ),
				'example' => __( 'Klicke auf ein freies Zeitfenster, um einen Termin zu vereinbaren.', 'appointments' ),
			),
			'notlogged' => array(
				'type' => 'text',
				'name' => __( 'Abgemeldet Text', 'appointments' ),
				'value' => __( 'Du musst Dich anmelden, um einen Termin zu vereinbaren. Bitte klicke hier, um Dich zu registrieren/einzuloggen: LOGIN_PAGE', 'appointments' ),
				'help' => __( 'Text, der nach dem Titel nur für die Clients angezeigt wird, die nicht angemeldet sind und für die Sie eine Anmeldung benötigen. <code>LOGIN_PAGE</code> wird durch die Anmeldeseite Deiner Webseite ersetzt, <code>REGISTRATION_PAGE</code> wird durch die Registrierungsseite Deiner Webseite ersetzt.', 'appointments' ),
				'example' => __( 'You need to login to make an appointment. Please click here to register/login: LOGIN_PAGE', 'appointments' ),
			),
			'service' => array(
				'type' => 'select',
				'name' => __( 'Service', 'appointments' ),
				'options' => $services,
				'value' => 0,
				'help' => __( 'Dienst nur auswählen, wenn Du erzwingen möchtest, dass in der Tabelle der Dienst mit der eingegebenen ID angezeigt wird. Standard: Der Dienst wird per Dropdown ausgewählt.', 'appointments' ),
			),
			'worker' => array(
				'type' => 'select',
				'name' => __( 'Dienstleister', 'appointments' ),
				'options' => $workers,
				'value' => 0,
				'help' => __( 'Wähle den Dienstanbieter nur aus, wenn Du erzwingen möchtest, dass in der Tabelle der Dienstanbieter mit der eingegebenen ID angezeigt wird. Standard: Der Dienstanbieter wird per Dropdown ausgewählt.', 'appointments' ),
			),
			'long' => array(
				'type' => 'checkbox',
				'name' => __( 'Lange Wochentage', 'appointments' ),
				'value' => 0,
				'help' => __( 'Wenn diese Option aktiviert ist, werden in der Zeile der Zeitplantabelle lange Wochentage angezeigt, z.B. "Samstag" statt "Sa".', 'appointments' ),
			),
			'class' => array(
				'type' => 'text',
				'name' => __( 'CSS Class', 'appointments' ),
				'value' => '',
				'help' => __( 'Ein CSS-Klassenname für die Zeitplantabelle. Standard ist leer.', 'appointments' ),
			),
			'add' => array(
				'type' => 'text',
				'name' => __( 'Anzahl der hinzugefügten Monate', 'appointments' ),
				'value' => 0,
				'help' => __( 'Anzahl der Monate, die dem Zeitplan hinzugefügt werden sollen, der für die Zeitpläne der vorhergehenden Monate verwendet werden soll. Gib 1 für den nächsten Monat, 2 für den anderen Monat usw. ein. Standard: "0" (Aktueller Monat)', 'appointments' ),
			),
			'_noscript' => array(
				'value' => false,
			),
			'date' => array(
				'type' => 'text',
				'name' => __( 'Datum', 'appointments' ),
				'value' => '',
				'help' => __( 'Normalerweise beginnt der Kalender mit dem aktuellen Monat. Wenn Du erzwingen möchtest, dass es ab einem bestimmten Datum beginnt, gib dieses Datum hier ein. Die meisten Datumsformate werden unterstützt, jedoch wird JJJJ-MM-TT empfohlen. Hinweise: 1) Dieser Wert wirkt sich auch auf andere nachfolgende Kalender auf derselben Seite aus. 2) Es reicht aus, ein Datum innerhalb des Monats einzugeben. Standard: "0" (Aktueller Monat)', 'appointments' ),
			),
			'require_provider' => array(
				'type' => 'checkbox',
				'name' => __( 'Dienstleister anfordern', 'appointments' ),
				'value' => 0,
				'help' => __( 'Wenn Du dieses Argument aktivierst, wird kein Zeitplan erstellt, es sei denn, zuvor wurde ein Dienstanbieter ausgewählt.', 'appointments' ),
			),
			'required_message' => array(
				'type' => 'text',
				'name' => __( 'Erforderlich Nachricht', 'appointments' ),
				'value' => __( 'Bitte wähle einen Dienstanbieter.', 'appointments' ),
				'help' => __( 'Die Meldung, die angezeigt wird, wenn Dienstanbieter erforderlich sind.', 'appointments' ),
			),
			'require_service' => array(
				'type' => 'checkbox',
				'name' => __( 'Service anfordern', 'appointments' ),
				'value' => 0,
				'help' => __( 'Wenn Du dieses Argument aktivierst, wird kein Zeitplan erstellt, es sei denn, ein Dienst wurde zuvor ausgewählt.', 'appointments' ),
			),
			'required_service_message' => array(
				'type' => 'text',
				'name' => __( 'Erforderliche Servicemeldung', 'appointments' ),
				'value' => __( 'Bitte wähle einen Service.', 'appointments' ),
				'help' => __( 'Die Meldung, die angezeigt wird, wenn Dienste erforderlich sind.', 'appointments' ),
			),
		);
	}

	public function process_shortcode( $args = array(), $content = '' ) {
		$appointments = appointments();

		$current_time = current_time( 'timestamp' );
		$options = appointments_get_options();
		$args = wp_parse_args( $args, $this->_defaults_to_args() );

		$service_id = isset( $_REQUEST['app_service_id'] ) ? absint( $_REQUEST['app_service_id'] ) : 0;
		if ( appointments_get_services_min_id() && ! $service_id ) {
			$service_id = appointments_get_services_min_id();
		}

		$worker_id = isset( $_REQUEST['app_provider_id'] ) ? absint( $_REQUEST['app_provider_id'] ) : 0;
		if ( ! $worker_id ) {
			$worker_id = isset( $_REQUEST['app_worker_id'] ) ? absint( $_REQUEST['app_worker_id'] ) : 0;
		}
		$location_id = isset( $_REQUEST['app_location_id'] ) ? absint( $_REQUEST['app_location_id'] ) : 0;

		// Force service
		if ( $args['service'] ) {
			// Check if such a service exists
			if ( ! appointments_get_service( $args['service'] ) ) {
				return '';
			}
			$service_id = absint( $args['service'] );
			$_REQUEST['app_service_id'] = $args['service'];
		}

		$workers_by_service = appointments_get_workers_by_service( $service_id );
		$workers_ids = wp_list_pluck( $workers_by_service, 'ID' );
		$single_worker = false;
		if ( 1 === count( $workers_by_service ) ) {
			$single_worker = $workers_by_service[0]->ID;
		}

		if ( $args['worker'] ) {
			// Check if such a worker exists
			if ( ! appointments_is_worker( $args['worker'] ) ) {
				return '';
			}
			$worker_id = absint( $args['worker'] );
			$_REQUEST['app_provider_id'] = $args['worker'];
		} elseif ( $single_worker ) {
			// Select the only provider if that is the case
			$_REQUEST['app_provider_id'] = $single_worker;
			$args['worker']              = $single_worker;
			$worker_id = absint( $args['worker'] );
		} elseif ( isset( $args['require_provider'] ) &&
			$args['require_provider'] == 1 &&
			( ( ! isset( $_REQUEST['app_provider_id'] ) || ! in_array( $_REQUEST['app_provider_id'], $workers_ids ) ) || ! isset( $args['worker'] ) ) ) {
			$worker_id = 0;
		} elseif ( isset( $_REQUEST['app_provider_id'] ) && in_array( $_REQUEST['app_provider_id'], $workers_ids ) ) {
			$worker_id = (int) $_REQUEST['app_provider_id'];
			$args['worker'] = $worker_id;
		} else {
			/*
			if( is_array( $workers_by_service ) && ! empty( $workers_by_service ) ){
				$worker_id = $workers_by_service[0]->ID;
				if( isset( $_REQUEST["app_provider_id"] ) ){
					$_REQUEST["app_provider_id"] = $_GET["app_provider_id"] = $worker_id;
				}
            }
             */
		}

		// Force a date
		if ( $args['date'] && ! isset( $_GET['wcalendar'] ) ) {
			$time              = strtotime( $args['date'], $current_time ) + ( $args['add'] * 7 * 86400 );
			$_GET['wcalendar'] = $time;
		} else {
			if ( isset( $_GET['wcalendar'] ) && (int) $_GET['wcalendar'] ) {
				$time = (int) $_GET['wcalendar'] + ( $args['add'] * 7 * 86400 );
			} else {
				$time = $current_time + ( $args['add'] * 7 * 86400 );
			}
		}

		$slots = array();
		if ( is_array( $workers_by_service ) && ! empty( $workers_by_service ) ) {
			$slots = appointments_get_weekly_schedule_slots( $time, $service_id, $workers_by_service[0]->ID, $location_id );
		} else {
			$slots = appointments_get_weekly_schedule_slots( $time, $service_id, $worker_id, $location_id );
		}

		if ( '' != $args['title'] ) {
			$start_day = current( $slots['the_week'] );
			end( $slots['the_week'] );
			$end_day = current( $slots['the_week'] );
			reset( $slots['the_week'] );
			$args['title'] = str_replace(
				array( 'START', 'END' ),
				array(
					date_i18n( appointments_get_date_format( 'date' ), strtotime( $start_day ) ),
					date_i18n( appointments_get_date_format( 'date' ), strtotime( $end_day ) ),
				),
				$args['title']
			);
		} else {
			$args['title'] = '';
		}

		$has_worker = ! empty( $args['require_provider'] ) ? ( isset( $_GET['app_provider_id'] ) && is_numeric( $_GET['app_provider_id'] ) ) : ! empty( $worker_id ) || ( is_array( $workers_by_service ) && ! empty( $workers_by_service ) );
		$has_service = ! empty( $args['require_service'] ) ? ( isset( $_GET['app_service_id'] ) && is_numeric( $_GET['app_service_id'] ) ) : ! empty( $service_id );
		$defaults = $this->get_defaults();

		$c = '';
		$c .= '<div class="appointments-wrapper">';

		if ( ! $has_service && ! empty( $args['require_service'] ) ) {
			$c .= ! empty( $args['required_service_message'] )
				? $args['required_service_message']
				: $defaults['required_service_message'];
		} elseif ( ! $has_worker && ! empty( $args['require_provider'] ) ) {
			$c .= ! empty( $args['required_message'] )
				? $args['required_message']
				: $defaults['required_message'];
		} else {
			$c .= $args['title'];

			if ( is_user_logged_in() || 'yes' != $options['login_required'] ) {
				$c .= $args['logged'] ? "<div class='appointments-instructions'>{$args['logged']}</div>" : '';
			} else {
				$codec = new App_Macro_GeneralCodec;
				if ( ! $options['accept_api_logins'] ) {
					//$c .= str_replace( 'LOGIN_PAGE', '<a class="appointments-login_show_login" href="'.site_url( 'wp-login.php').'">'. __('Login','appointments'). '</a>', $notlogged );
					$c .= $codec->expand( $args['notlogged'], App_Macro_GeneralCodec::FILTER_BODY );
				} else {
					$c .= '<div class="appointments-login">';
					//$c .= str_replace( 'LOGIN_PAGE', '<a class="appointments-login_show_login" href="javascript:void(0)">'. __('Login','appointments'). '</a>', $args['notlogged ']);
					$c .= $codec->expand( $args['notlogged'], App_Macro_GeneralCodec::FILTER_BODY );
					$c .= '<div class="appointments-login_inner">';
					$c .= '</div>';
					$c .= '</div>';
				}
			}

			$workers = array();
			if ( is_array( $workers_by_service ) && ! empty( $workers_by_service ) ) {
				foreach ( $workers_by_service as $one ) {
					$workers[] = $one->ID;
				}
			} else {
				$workers = array( $worker_id );
			}

			$c .= '<div class="appointments-list">';

			$current_time = current_time( 'timestamp' );
			$date         = $time ? $time : $current_time;
			$c           .= appointments_weekly_calendar( $date, array(
				'workers'     => $workers,
				'worker_id'   => $worker_id,
				'service_id'  => $service_id,
				'location_id' => $location_id,
				'long'        => $args['long'],
				'class'       => $args['class'],
				'echo'        => false,
			));

			$c .= '</div>';
		}
		$c .= '</div>'; // .appointments-wrapper

		$script = '';

		if ( ! $args['_noscript'] ) {
			$appointments->add2footer( $script );
		}

		return $c;
	}

	public function get_usage_info() {
		return __( 'Erstellt eine wöchentliche Tabelle, deren Zellen anklickbar sind, um einen Termin zu beantragen.', 'appointments' );
	}
}
