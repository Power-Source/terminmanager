<?php
/**
 * PS Smart CRM Integration für Terminmanager
 * 
 * Synchronisiert Kunden, Termine und Daten zwischen beiden Systemen
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class App_CRM_Integration {
	
	private static $instance = null;
	private $is_crm_active = false;
	private $options = array();
	
	/**
	 * Singleton Instance
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
	/**
	 * Constructor
	 */
	private function __construct() {
		$this->is_crm_active = $this->check_crm_active();
		$this->options = get_option( 'app_crm_integration', array() );
		
		if ( $this->is_crm_active ) {
			$this->init_hooks();
		}
		
		// Admin-Hooks immer laden für Settings
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_filter( 'appointments_tabs', array( $this, 'add_settings_tab' ) );
	}
	
	/**
	 * Prüft ob PS Smart CRM aktiv ist
	 */
	private function check_crm_active() {
		return defined( 'WPsCRM_TABLE' ) && function_exists( 'WPsCRM_crm_install' );
	}
	
	/**
	 * Initialisiert alle Hooks wenn CRM aktiv ist
	 */
	private function init_hooks() {
		// Kunden-Synchronisation
		add_action( 'user_register', array( $this, 'sync_user_to_crm' ), 10, 1 );
		add_action( 'profile_update', array( $this, 'update_crm_customer' ), 10, 2 );
		add_action( 'WPsCRM_advanced_buttons', array( $this, 'render_crm_customer_backlinks' ), 10, 1 );
		
		// CRM Integrationskarte
		add_filter( 'WPsCRM_accounting_integrations', array( $this, 'register_crm_accounting_integration' ) );
		
		// Termin-Synchronisation
		add_action( 'wpmudev_appointments_insert_appointment', array( $this, 'sync_appointment_to_crm' ), 10, 1 );
		add_action( 'appointments_appointment_updated', array( $this, 'update_crm_appointment' ), 10, 2 );
		add_action( 'app-appointment-status_changed', array( $this, 'handle_appointment_status_change' ), 10, 2 );
		
		// AJAX für manuelle Sync
		add_action( 'wp_ajax_app_crm_sync_customers', array( $this, 'ajax_sync_customers' ) );
		add_action( 'wp_ajax_app_crm_sync_appointments', array( $this, 'ajax_sync_appointments' ) );
		
		// Admin-Hinweise
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
	}
	
	/**
	 * Synchronisiert einen WordPress User zum CRM
	 */
	public function sync_user_to_crm( $user_id ) {
		if ( ! $this->get_option( 'sync_customers', true ) ) {
			return;
		}
		
		global $wpdb;
		$table = WPsCRM_TABLE . 'kunde';
		
		// Prüfen ob User bereits im CRM existiert
		$crm_id = $this->get_crm_customer_id( $user_id );
		if ( $crm_id ) {
			return $crm_id;
		}
		
		$user = get_userdata( $user_id );
		if ( ! $user ) {
			return false;
		}
		
		// Daten vorbereiten
		$data = array(
			'user_id' => $user_id,
			'name' => $user->first_name ?: $user->display_name,
			'nachname' => $user->last_name ?: '',
			'email' => $user->user_email,
			'telefono1' => get_user_meta( $user_id, 'billing_phone', true ),
			'adresse' => get_user_meta( $user_id, 'billing_address_1', true ),
			'cap' => get_user_meta( $user_id, 'billing_postcode', true ),
			'standort' => get_user_meta( $user_id, 'billing_city', true ),
			'nation' => get_user_meta( $user_id, 'billing_country', true ),
			'einstiegsdatum' => current_time( 'mysql' ),
			'provenienza' => 'Terminmanager',
			'tipo_cliente' => 1,
			'categoria' => 'Terminkunde',
		);
		
		$inserted = $wpdb->insert( $table, $data );
		
		if ( $inserted ) {
			$crm_id = $wpdb->insert_id;
			update_user_meta( $user_id, '_app_crm_customer_id', $crm_id );
			return $crm_id;
		}
		
		return false;
	}
	
	/**
	 * Aktualisiert CRM-Kunde bei User-Update
	 */
	public function update_crm_customer( $user_id, $old_user_data ) {
		if ( ! $this->get_option( 'sync_customers', true ) ) {
			return;
		}
		
		$crm_id = $this->get_crm_customer_id( $user_id );
		if ( ! $crm_id ) {
			// Kunde existiert noch nicht, erstellen
			$this->sync_user_to_crm( $user_id );
			return;
		}
		
		global $wpdb;
		$table = WPsCRM_TABLE . 'kunde';
		$user = get_userdata( $user_id );
		
		if ( ! $user ) {
			return;
		}
		
		$data = array(
			'name' => $user->first_name ?: $user->display_name,
			'nachname' => $user->last_name ?: '',
			'email' => $user->user_email,
			'telefono1' => get_user_meta( $user_id, 'billing_phone', true ),
			'adresse' => get_user_meta( $user_id, 'billing_address_1', true ),
			'cap' => get_user_meta( $user_id, 'billing_postcode', true ),
			'standort' => get_user_meta( $user_id, 'billing_city', true ),
			'nation' => get_user_meta( $user_id, 'billing_country', true ),
			'data_modifica' => current_time( 'mysql' ),
		);
		
		$wpdb->update( 
			$table, 
			$data, 
			array( 'ID_kunde' => $crm_id ),
			array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' ),
			array( '%d' )
		);
	}
	
	/**
	 * Synchronisiert einen Termin zum CRM
	 */
	public function sync_appointment_to_crm( $app_id ) {
		if ( ! $this->get_option( 'sync_appointments', true ) ) {
			return;
		}
		
		global $wpdb;
		$agenda_table = WPsCRM_TABLE . 'agenda';
		
		// Prüfen ob Termin bereits im CRM existiert
		$crm_agenda_id = get_post_meta( $app_id, '_app_crm_agenda_id', true );
		if ( $crm_agenda_id ) {
			return $crm_agenda_id;
		}
		
		$appointment = appointments_get_appointment( $app_id );
		if ( ! $appointment ) {
			return false;
		}
		
		// Sicherstellen dass Kunde im CRM existiert
		$crm_customer_id = null;
		if ( $appointment->user ) {
			$crm_customer_id = $this->get_crm_customer_id( $appointment->user );
			if ( ! $crm_customer_id ) {
				$crm_customer_id = $this->sync_user_to_crm( $appointment->user );
			}
		}
		
		// Service-Name holen
		$service = appointments_get_service( $appointment->service );
		$service_name = $service ? $service->name : __( 'Termin', 'appointments' );
		
		// Worker/Agent holen
		$worker_user_id = 0;
		if ( $appointment->worker ) {
			$worker = appointments_get_worker( $appointment->worker );
			if ( $worker ) {
				$worker_user_id = $worker->ID;
			}
		}
		
		// CRM Agenda-Daten vorbereiten
		$data = array(
			'fk_kunde' => $crm_customer_id ?: 0,
			'fk_utenti_des' => $worker_user_id,
			'oggetto' => $service_name,
			'start_date' => $appointment->start,
			'end_date' => $appointment->end,
			'data_agenda' => date( 'Y-m-d', strtotime( $appointment->start ) ),
			'ora_agenda' => date( 'H:i:s', strtotime( $appointment->start ) ),
			'annotazioni' => $this->build_scheduler_note( $app_id, $appointment->note ?: '' ),
			'einstiegsdatum' => current_time( 'mysql' ),
			'tipo_agenda' => 2, // 2 = Termin/Appointment
			'fatto' => $this->map_appointment_status( $appointment->status ),
			'priorita' => 1,
			'importante' => 'Si',
		);
		
		$inserted = $wpdb->insert( $agenda_table, $data );
		
		if ( $inserted ) {
			$crm_agenda_id = $wpdb->insert_id;
			update_post_meta( $app_id, '_app_crm_agenda_id', $crm_agenda_id );
			return $crm_agenda_id;
		}
		
		return false;
	}
	
	/**
	 * Aktualisiert CRM-Termin bei Änderungen
	 */
	public function update_crm_appointment( $app_id, $args ) {
		if ( ! $this->get_option( 'sync_appointments', true ) ) {
			return;
		}
		
		$crm_agenda_id = get_post_meta( $app_id, '_app_crm_agenda_id', true );
		
		if ( ! $crm_agenda_id ) {
			// Termin existiert noch nicht im CRM, erstellen
			$this->sync_appointment_to_crm( $app_id );
			return;
		}
		
		global $wpdb;
		$agenda_table = WPsCRM_TABLE . 'agenda';
		
		$appointment = appointments_get_appointment( $app_id );
		if ( ! $appointment ) {
			return;
		}
		
		$data = array(
			'start_date' => $appointment->start,
			'end_date' => $appointment->end,
			'data_agenda' => date( 'Y-m-d', strtotime( $appointment->start ) ),
			'ora_agenda' => date( 'H:i:s', strtotime( $appointment->start ) ),
			'annotazioni' => $this->build_scheduler_note( $app_id, $appointment->note ?: '' ),
			'fatto' => $this->map_appointment_status( $appointment->status ),
		);
		
		$wpdb->update(
			$agenda_table,
			$data,
			array( 'id_agenda' => $crm_agenda_id ),
			array( '%s', '%s', '%s', '%s', '%s', '%d' ),
			array( '%d' )
		);
	}
	
	/**
	 * Behandelt Status-Änderungen von Terminen
	 */
	public function handle_appointment_status_change( $app_id, $new_status ) {
		if ( ! $this->get_option( 'sync_appointments', true ) ) {
			return;
		}
		
		$crm_agenda_id = get_post_meta( $app_id, '_app_crm_agenda_id', true );
		
		if ( ! $crm_agenda_id ) {
			return;
		}
		
		global $wpdb;
		$agenda_table = WPsCRM_TABLE . 'agenda';
		
		$fatto_status = $this->map_appointment_status( $new_status );
		
		$wpdb->update(
			$agenda_table,
			array( 'fatto' => $fatto_status ),
			array( 'id_agenda' => $crm_agenda_id ),
			array( '%d' ),
			array( '%d' )
		);
	}
	
	/**
	 * Mappt Terminmanager-Status zu CRM-Status
	 */
	private function map_appointment_status( $app_status ) {
		// CRM fatto: 1=zu erledigen, 2=erledigt, 3=storniert
		$map = array(
			'pending'   => 1,
			'confirmed' => 1,
			'paid'      => 1,
			'completed' => 2,
			'removed'   => 3,
		);
		
		return isset( $map[ $app_status ] ) ? $map[ $app_status ] : 1;
	}
	
	/**
	 * Holt CRM Kunden-ID für WordPress User
	 */
	private function get_crm_customer_id( $user_id ) {
		$crm_id = get_user_meta( $user_id, '_app_crm_customer_id', true );
		
		if ( ! $crm_id ) {
			// Versuche über Email zu finden
			global $wpdb;
			$table = WPsCRM_TABLE . 'kunde';
			$user = get_userdata( $user_id );
			
			if ( $user ) {
				$crm_id = $wpdb->get_var( $wpdb->prepare(
					"SELECT ID_kunde FROM $table WHERE email = %s AND user_id = %d LIMIT 1",
					$user->user_email,
					$user_id
				) );
				
				if ( $crm_id ) {
					update_user_meta( $user_id, '_app_crm_customer_id', $crm_id );
				}
			}
		}
		
		return $crm_id;
	}
	
	/**
	 * AJAX: Manuelle Kunden-Synchronisation
	 */
	public function ajax_sync_customers() {
		check_ajax_referer( 'app_crm_sync', 'nonce' );
		
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Keine Berechtigung', 'appointments' ) ) );
		}
		
		$users = get_users( array( 'fields' => 'ID' ) );
		$synced = 0;
		$errors = 0;
		
		foreach ( $users as $user_id ) {
			if ( $this->sync_user_to_crm( $user_id ) ) {
				$synced++;
			} else {
				$errors++;
			}
		}
		
		wp_send_json_success( array(
			'message' => sprintf( __( '%d Kunden synchronisiert, %d Fehler', 'appointments' ), $synced, $errors ),
			'synced' => $synced,
			'errors' => $errors,
		) );
	}
	
	/**
	 * AJAX: Manuelle Termin-Synchronisation
	 */
	public function ajax_sync_appointments() {
		check_ajax_referer( 'app_crm_sync', 'nonce' );
		
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Keine Berechtigung', 'appointments' ) ) );
		}
		
		$appointments = appointments_get_appointments( array( 'per_page' => -1 ) );
		$synced = 0;
		$errors = 0;
		
		foreach ( $appointments as $appointment ) {
			if ( $this->sync_appointment_to_crm( $appointment->ID ) ) {
				$synced++;
			} else {
				$errors++;
			}
		}
		
		wp_send_json_success( array(
			'message' => sprintf( __( '%d Termine synchronisiert, %d Fehler', 'appointments' ), $synced, $errors ),
			'synced' => $synced,
			'errors' => $errors,
		) );
	}
	
	/**
	 * Admin-Hinweise
	 */
	public function admin_notices() {
		if ( ! $this->is_crm_active ) {
			return;
		}
		
		$screen = get_current_screen();
		if ( ! $screen || strpos( $screen->id, 'appointments' ) === false ) {
			return;
		}
		
		// Zeige Hinweis wenn Integration aktiv ist
		if ( $this->get_option( 'sync_customers', false ) || $this->get_option( 'sync_appointments', false ) ) {
			echo '<div class="notice notice-info"><p>';
			echo '<strong>' . __( 'PS Smart CRM Integration aktiv', 'appointments' ) . '</strong><br>';
			echo __( 'Kunden und Termine werden automatisch mit dem CRM synchronisiert.', 'appointments' );
			echo '</p></div>';
		}
	}
	
	/**
	 * Fügt Settings-Tab hinzu
	 */
	public function add_settings_tab( $tabs ) {
		$tabs['crm_integration'] = __( 'CRM Integration', 'appointments' );
		return $tabs;
	}

	/**
	 * Registriert Terminmanager im CRM Integrationen-Tab
	 */
	public function register_crm_accounting_integration( $integrations ) {
		$integrations['terminmanager'] = array(
			'name' => __( 'Terminmanager', 'appointments' ),
			'description' => __( 'Synchronisiert Termine und Kundendaten mit PS Smart CRM.', 'appointments' ),
			'plugin' => 'terminmanager/appointments.php',
			'icon' => 'glyphicon glyphicon-calendar',
			'status' => 'available',
			'fields' => array(),
		);

		return $integrations;
	}

	/**
	 * Zeigt Rücklinks aus dem CRM-Kundenformular in den Terminmanager
	 */
	public function render_crm_customer_backlinks( $email = '' ) {
		$crm_customer_id = isset( $_REQUEST['ID'] ) ? absint( $_REQUEST['ID'] ) : 0;
		if ( ! $crm_customer_id ) {
			return;
		}

		$wp_user_id = $this->get_wp_user_id_by_crm_customer( $crm_customer_id, $email );
		if ( ! $wp_user_id ) {
			return;
		}

		echo '<a href="' . esc_url( admin_url( 'user-edit.php?user_id=' . $wp_user_id ) ) . '" class="btn btn-default" style="margin-left:10px">';
		echo '<i class="glyphicon glyphicon-user"></i> ' . esc_html__( 'WP-Profil', 'appointments' );
		echo '</a>';
		echo '<a href="' . esc_url( admin_url( 'admin.php?page=appointments' ) ) . '" class="btn btn-default" style="margin-left:6px">';
		echo '<i class="glyphicon glyphicon-calendar"></i> ' . esc_html__( 'Terminmanager', 'appointments' );
		echo '</a>';
	}

	/**
	 * Baut eine markierte Scheduler-Notiz für Quellen-Filter im CRM
	 */
	private function build_scheduler_note( $app_id, $note = '' ) {
		$prefix = sprintf( '[Terminmanager #%d]', absint( $app_id ) );
		$note = trim( (string) $note );

		if ( '' === $note ) {
			return $prefix;
		}

		if ( false !== strpos( $note, '[Terminmanager #' ) ) {
			return $note;
		}

		return $prefix . "\n" . $note;
	}

	/**
	 * Ermittelt WordPress User-ID über CRM-Kunde
	 */
	private function get_wp_user_id_by_crm_customer( $crm_customer_id, $email = '' ) {
		$users = get_users( array(
			'fields' => 'ID',
			'number' => 1,
			'meta_key' => '_app_crm_customer_id',
			'meta_value' => $crm_customer_id,
		) );

		if ( ! empty( $users ) ) {
			return absint( $users[0] );
		}

		if ( ! empty( $email ) ) {
			$user = get_user_by( 'email', $email );
			if ( $user ) {
				return absint( $user->ID );
			}
		}

		return 0;
	}
	
	/**
	 * Registriert Settings
	 */
	public function register_settings() {
		register_setting( 'app_crm_integration', 'app_crm_integration' );
	}
	
	/**
	 * Holt Option-Wert
	 */
	private function get_option( $key, $default = false ) {
		return isset( $this->options[ $key ] ) ? $this->options[ $key ] : $default;
	}
	
	/**
	 * Prüft ob Integration aktiv ist
	 */
	public function is_active() {
		return $this->is_crm_active;
	}
	
	/**
	 * Gibt Statistiken zurück
	 */
	public function get_stats() {
		global $wpdb;
		
		$stats = array(
			'crm_customers' => 0,
			'synced_customers' => 0,
			'crm_appointments' => 0,
			'synced_appointments' => 0,
		);
		
		if ( ! $this->is_crm_active ) {
			return $stats;
		}
		
		// CRM Kunden zählen
		$kunde_table = WPsCRM_TABLE . 'kunde';
		$stats['crm_customers'] = $wpdb->get_var( "SELECT COUNT(*) FROM $kunde_table WHERE eliminato = 0" );
		$stats['synced_customers'] = $wpdb->get_var( "SELECT COUNT(*) FROM $kunde_table WHERE user_id > 0 AND eliminato = 0" );
		
		// CRM Termine zählen
		$agenda_table = WPsCRM_TABLE . 'agenda';
		$stats['crm_appointments'] = $wpdb->get_var( "SELECT COUNT(*) FROM $agenda_table WHERE tipo_agenda = 2 AND eliminato = 0" );
		
		// Synchronisierte Termine zählen
		$meta_table = $wpdb->postmeta;
		$stats['synced_appointments'] = $wpdb->get_var( "SELECT COUNT(*) FROM $meta_table WHERE meta_key = '_app_crm_agenda_id'" );
		
		return $stats;
	}
}

// Initialisiere Integration
function app_crm_integration_init() {
	return App_CRM_Integration::get_instance();
}
add_action( 'plugins_loaded', 'app_crm_integration_init', 20 );
