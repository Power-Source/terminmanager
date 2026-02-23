<?php
/**
 * @author: PSOURCE, Ignacio Cruz (igmoweb)
 * @version:
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Monthly schedule calendar.
 */
if ( ! class_exists( 'App_Shortcode_Monthly_Schedule' ) ) {
	class App_Shortcode_Monthly_Schedule extends App_Shortcode {

		public function __construct () {
			$this->name = __( 'Monatsplan', 'appointments' );
		}

		public function get_defaults() {
			$_workers = appointments_get_workers();
			$workers = array(
				array( 'text' => __( 'Jeder Dienstleister', 'appointments' ), 'value' => '' )
			);
			foreach ( $_workers as $worker ) {
				/** @var Appointments_Worker $worker */
				$workers[] = array( 'text' => $worker->get_name(), 'value' => $worker->ID );
			}

			$_services = appointments_get_services();
			$services = array(
				array( 'text' => __( 'Jeder Service', 'appointments' ), 'value' => '' )
			);
			foreach ( $_services as $service ) {
				/** @var Appointments_Service $service */
				$services[] = array( 'text' => $service->name, 'value' => $service->ID );
			}

			return array(
				'title' => array(
					'type' => 'text',
					'name' => __( 'Titel', 'appointments' ),
					'value' => sprintf( '<h3>%s</h3>', esc_html__('Unser Zeitplan für START', 'appointments') ),
					'help' => __('Text, der als Zeitplantitel angezeigt wird. Die Platzhalter START, WORKER und SERVICE werden automatisch durch ihre tatsächlichen Werte ersetzt.', 'appointments'),
				),
				'logged' => array(
					'type' => 'text',
					'name' => __( 'Nach dem Titeltext (abgemeldete Benutzer)', 'appointments' ),
					'value' => __('Klicke auf einen freien Tag, um einen Termin zu vereinbaren.', 'appointments'),
					'help' => __('Text, der nach dem Titel nur für die angemeldeten Kunden angezeigt wird oder für die sie keine Anmeldung benötigen.', 'appointments'),
				),
				'notlogged' => array(
					'type' => 'text',
					'name' => __( 'Nicht eingeloggt Text', 'appointments' ),
					'value' => __('Du musst Dich anmelden, um einen Termin zu vereinbaren. Bitte klicke hier, um Dich zu registrieren/einzuloggen: LOGIN_PAGE', 'appointments'),
					'help' => __('Text, der nach dem Titel nur für die Clients angezeigt wird, die nicht angemeldet sind und für die sie eine Anmeldung benötigen. <code>LOGIN_PAGE</code> will be replaced with your website\'s login page, while <code>REGISTRATION_PAGE</code> will be replaced with your website\'s registration page.', 'appointments'),
				),
				'hide_today_times' => array(
					'type' => 'checkbox',
					'name' => __( "Verstecke die heutigen Zeiten", 'appointments' ),
					'value' => 0,
					'help' => __('Durch Aktivieren dieses Arguments werden die verfügbaren Terminzeiten ausgeblendet, bis der Benutzer einen Tag auswählt.', 'appointments'),
				),
				'service' => array(
					'type' => 'select',
					'name' => __( 'Service', 'appointments' ),
					'value' => 0,
					'options' => $services,
					'help' => __('Wähle einen Dienst nur aus, wenn Du erzwingen möchtest, dass in der Tabelle der eingegebene Dienst angezeigt wird. Standard: Der Dienst wird per Dropdown ausgewählt.', 'appointments'),
				),
				'worker' => array(
					'type' => 'select',
					'name' => __( 'Dienstleister', 'appointments' ),
					'value' => 0,
					'options' => $workers,
					'help' => __('Wähle einen Anbieter nur aus, wenn Du erzwingen möchtest, dass in der Tabelle der eingegebene Anbieter angezeigt wird. Standard: Der Anbieter wird per Dropdown ausgewählt.', 'appointments'),
				),
				'long' => array(
					'type' => 'checkbox',
					'name' => __( 'Lange Wochentage', 'appointments' ),
					'value' => 0,
					'help' => __('Wenn diese Option aktiviert ist, werden in der Zeile der Zeitplantabelle lange Wochentage angezeigt, z. "Samstag" statt "Sa".', 'appointments'),
				),
				'class' => array(
					'type' => 'text',
					'name' => __( 'CSS Class', 'appointments' ),
					'value' => '',
					'help' => __('Ein CSS-Klassenname für die Zeitplantabelle. Standard ist leer.', 'appointments'),
				),
				'add' => array(
					'type' => 'text',
					'name' => __( 'Anzahl der hinzugefügten Monate', 'appointments' ),
					'value' => 0,
					'help' => __('Anzahl der Monate, die dem Zeitplan hinzugefügt werden sollen, der für die Zeitpläne der vorhergehenden Monate verwendet werden soll. Gib 1 für den nächsten Monat, 2 für den anderen Monat usw. ein. Standard: "0" (aktueller Monat)', 'appointments'),
				),
				'widget' => array(
					'value' => 0,
				),
				'date' => array(
					'type' => 'text',
					'name' => __( 'Datum', 'appointments' ),
					'value' => '',
					'help' => __('Normalerweise beginnt der Kalender mit dem aktuellen Monat. Wenn Du erzwingen möchtest, dass es ab einem bestimmten Datum beginnt, gib dieses Datum hier ein. Die meisten Datumsformate werden unterstützt, jedoch wird JJJJ-MM-TT empfohlen. Hinweise: 1) Dieser Wert wirkt sich auch auf andere nachfolgende Kalender auf derselben Seite aus. 2) Es reicht aus, ein Datum innerhalb des Monats einzugeben. Standard: "0" (aktueller Monat)', 'appointments'),
				),
				'require_provider' => array(
					'type' => 'checkbox',
					'name' => __( 'Dienstleister erforderlich', 'appointments' ),
					'value' => 0,
					'help' => __('Wenn Du dieses Argument aktivierst, wird kein Zeitplan erstellt, es sei denn, zuvor wurde ein Dienstanbieter ausgewählt.', 'appointments'),
				),
				'required_message' => array(
					'type' => 'text',
					'name' => __( 'Erforderliche Nachricht', 'appointments' ),
					'value' => __('Bitte wähle einen Dienstleister.', 'appointments'),
					'help' => __('Die Meldung, die angezeigt wird, wenn Dienstleister erforderlich sind.', 'appointments'),
				),
				'require_service' => array(
					'type' => 'checkbox',
					'name' => __( 'Service erforderlich', 'appointments' ),
					'value' => 0,
					'help' => __('Wenn Du dieses Argument aktivierst, wird kein Zeitplan erstellt, es sei denn, ein Dienst wurde zuvor ausgewählt.', 'appointments'),
				),
				'required_service_message' => array(
					'type' => 'text',
					'name' => __( 'Erforderliche Servicemeldung', 'appointments' ),
					'value' => __('Bitte wähle einen Service.', 'appointments'),
					'help' => __('Die Meldung, die angezeigt wird, wenn Dienste erforderlich sind.', 'appointments')
				),
			);
		}

		public function get_parameters( $args ) {
			$args = wp_parse_args($args, $this->_defaults_to_args());

			$appointments = appointments();

			$params = array();

			// Force service
			if ( $args['service'] ) {
				// Check if such a service exists
				if ( ! appointments_get_service( $args['service'] ) ) {
					return false;
				}
				$_REQUEST["app_service_id"] = $args['service'];
			}

			$appointments->get_lsw(); // This should come after Force service

			$workers_by_service = appointments_get_workers_by_service( $appointments->service );
			$workers_ids = wp_list_pluck( $workers_by_service, 'ID' );
			$params['single_worker'] = false;
			if ( 1 === count( $workers_by_service ) ) {
				$params['single_worker'] = $workers_by_service[0]->ID;
			}

			// Force worker or pick up the single worker
			$params['worker_id'] = 0;
			if ( $args['worker'] ) {
				// Check if such a worker exists
				if ( ! appointments_is_worker( $args['worker'] ) ) {
					return '';
				}
				$_REQUEST["app_provider_id"] = $args['worker'];
				$params['worker_id'] = $args['worker'];
			}
			elseif ( $params['single_worker'] ) {
				// Select the only provider if that is the case
				$_REQUEST["app_provider_id"] = $_GET["app_provider_id"] = $params['single_worker'];
				$args['worker'] = $params['single_worker'];
				$params['worker_id'] = $params['single_worker'];
			}
			elseif( isset( $args['require_provider'] ) && 
					$args['require_provider'] == 1 && 
					( ( ! isset( $_REQUEST["app_provider_id"] ) || ! in_array( $_REQUEST["app_provider_id"], $workers_ids ) ) || ! isset( $args['worker'] ) ) ){
				$params['worker_id'] = 0;
			}
			elseif( isset( $_REQUEST["app_provider_id"] ) && in_array( $_REQUEST["app_provider_id"], $workers_ids ) ){				
				$params['worker_id'] = (int)$_REQUEST["app_provider_id"];
				$args['worker'] = $params['worker_id'];				
            }
            /*
			else{
				if( is_array( $workers_by_service ) && ! empty( $workers_by_service ) ){
					$params['worker_id'] = $workers_by_service[0]->ID;

					if( isset( $_REQUEST["app_provider_id"] ) ){
						$_REQUEST["app_provider_id"] = $_GET["app_provider_id"] = $params['worker_id'];
					}
				}	
            }
             */

			// Force a date
			if ( $args['date'] && !isset( $_GET["wcalendar"] ) ) {
				$params['time'] = $appointments->first_of_month( strtotime( $args['date'], $appointments->local_time ), $args['add'] );
				$_GET["wcalendar"] = $params['time'];
			}
			else {
				if ( ! empty( $_GET['wcalendar_human'] ) ) {
					$_GET['wcalendar'] = strtotime( $_GET['wcalendar_human'] );
				}
				if ( isset( $_GET["wcalendar"] ) && (int) $_GET['wcalendar'] ) {
					$params['time'] = $appointments->first_of_month( (int) $_GET["wcalendar"], $args['add'] );
				} else {
					$params['time'] = $appointments->first_of_month( $appointments->local_time, $args['add'] );
				}
			}

			$params['year'] = date("Y", $params['time']);
			$params['month'] = date("m",  $params['time']);

			if ( ! empty( $args['title'] ) ) {
				$replacements = array(
					date_i18n("F Y",  strtotime("{$params['year']}-{$params['month']}-01")), // START
					appointments_get_worker_name( ( ! empty($_REQUEST['app_provider_id']) ? $_REQUEST['app_provider_id'] : null ) ),
					$appointments->get_service_name( ( ! empty($_REQUEST['app_service_id']) ? $_REQUEST['app_service_id'] : null ) ),
				);
				$params['title'] = str_replace(
					array("START", "WORKER", "SERVICE"),
					$replacements,
					$args['title']
				);
			} else {
				$params['title'] = '';
			}

			$params['class'] = $args['class'];
			$params['long'] = $args['long'];
			$params['widget'] = $args['widget'];
			$params['notlogged'] = $args['notlogged'];
			$params['logged'] = $args['logged'];
			$params['has_worker'] = ! empty( $params['worker_id'] );
			$params['has_service'] = ! empty( $_REQUEST['app_service_id'] ) ? $_REQUEST["app_service_id"] : false;
			$params['service_id'] = $params['has_service'];
			$params['require_provider'] = $args['require_provider'];
			$params['required_message'] = $args['required_message'];
			$params['require_service'] = $args['require_service'];
			$params['required_service_message'] = $args['required_service_message'];
			$params['hide_today_times'] = $args['hide_today_times'];

			return $params;
		}

		public function process_shortcode ($args=array(), $content='') {
			$options = appointments_get_options();

			$params = $this->get_parameters( $args );
			if ( ! $params ) {
				return '';
			}

			$codec = new App_Macro_GeneralCodec;
			$services = new App_Shortcode_Services;

			$cal_args = array(
				'service_id'       => $params['service_id'],
				'worker_id'        => $params['worker_id'],
				'class'            => $params['class'],
				'long'             => $params['long'],
				'echo'             => false,
				'widget'           => $params['widget'],
				'hide_today_times' => $params['hide_today_times']
			);

			ob_start();
			?>
			<div class="appointments-wrapper">
				<?php if ( ! $params['has_worker'] && ! empty( $params['require_provider'] ) ): ?>
					<?php echo ! empty( $params['required_message'] ) ? $params['required_message'] : __( 'Bitte wähleeinen Dienstleister.', 'appointments' ); ?>
				<?php elseif ( ! $params['has_service'] && ! empty( $params['require_service'] ) ): ?>
					<?php $required_service_message = ! empty( $params['required_service_message'] ) ? $params['required_service_message'] : __( 'Bitte wähle einen Dienst.', 'appointments' ); ?>
					<?php echo $services->process_shortcode( array(
							'select' => $required_service_message,
					) ) ?>
				<?php else: ?>
					<?php echo apply_filters( 'app-shortcodes-monthly_schedule-title', $params['title'], $args ); ?>
					<?php if ( is_user_logged_in() || 'yes' != $options["login_required"] ): ?>
						<?php if ( $params['logged'] ): ?>
							<div class='appointments-instructions'><?php echo $params['logged']; ?></div>
						<?php endif; ?>
					<?php else: ?>
						<?php if ( ! $options["accept_api_logins"] ): ?>
							<?php echo $codec->expand( $params['notlogged'], App_Macro_GeneralCodec::FILTER_BODY ); ?>
						<?php else: ?>
							<div class="appointments-login">
								<?php echo $codec->expand( $params['notlogged'], App_Macro_GeneralCodec::FILTER_BODY ); ?>
								<div class="appointments-login_inner">
								</div>
							</div>
						<?php endif; ?>
					<?php endif; ?>
					<div class="appointments-list">
						<?php echo appointments_monthly_calendar( $params['time'], $cal_args ); ?>
					</div>
				<?php endif; ?>
			</div>
			<?php

			return ob_get_clean();
		}

		public function get_usage_info () {
			return __('Erstellt einen Monatskalender sowie Zeitpläne, deren freie Zeitfenster angeklickt werden können, um einen Termin zu beantragen.', 'appointments');
		}
	}

}
