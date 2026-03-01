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
		
		// Agent/Provider Kopplung (Phase A)
		add_action( 'user_register', array( $this, 'maybe_sync_agent_provider_link' ), 20, 1 );
		add_action( 'profile_update', array( $this, 'maybe_sync_agent_provider_link' ), 20, 1 );
		add_action( 'set_user_role', array( $this, 'maybe_sync_agent_provider_link' ), 20, 1 );
		
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
	 * Synchronisiert einen ClassicPress User zum CRM
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
	 * Holt CRM Kunden-ID für ClassicPress User
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
		
		// PM-Integration: Inbox-Link für Agenten
		if ( $this->is_pm_active() ) {
			$inbox_url = $this->get_pm_inbox_url();
			if ( $inbox_url ) {
				echo '<a href="' . esc_url( $inbox_url ) . '" class="btn btn-default" style="margin-left:6px">';
				echo '<i class="glyphicon glyphicon-envelope"></i> ' . esc_html__( 'Nachrichten', 'appointments' );
				echo '</a>';
			}
		}
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
	 * Ermittelt ClassicPress User-ID über CRM-Kunde
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
	 * Modus-Definitionen für CRM-Agenten und Dienstleister
	 */
	public function get_provider_agent_modes() {
		return array(
			'independent' => __( 'Unabhängig (Status Quo)', 'appointments' ),
			'agents_are_providers' => __( 'CRM-Agents sind auch Dienstleister', 'appointments' ),
			'agents_manage_providers' => __( 'CRM-Agents verwalten Dienstleister', 'appointments' ),
		);
	}

	/**
	 * Liefert den aktiven Agent/Provider Modus
	 */
	public function get_provider_agent_mode() {
		$mode = (string) $this->get_option( 'provider_agent_mode', 'independent' );
		$allowed = array_keys( $this->get_provider_agent_modes() );

		if ( ! in_array( $mode, $allowed, true ) ) {
			$mode = 'independent';
		}

		return $mode;
	}

	/**
	 * Sorgt im Modus 2 dafür, dass CRM-Agents als Dienstleister existieren
	 */
	public function maybe_sync_agent_provider_link( $user_id ) {
		$user_id = absint( $user_id );
		if ( ! $user_id || 'agents_are_providers' !== $this->get_provider_agent_mode() ) {
			return;
		}

		if ( ! $this->is_crm_agent_user( $user_id ) ) {
			return;
		}

		$this->ensure_worker_for_user( $user_id );
	}

	/**
	 * Ermittelt CRM-Agenten (ohne Admins)
	 */
	public function get_crm_agents() {
		$users = get_users( array(
			'fields' => array( 'ID', 'display_name', 'user_login' ),
			'number' => -1,
		) );

		$agents = array();
		foreach ( $users as $user ) {
			if ( $this->is_crm_agent_user( $user->ID ) ) {
				$agents[] = array(
					'ID' => absint( $user->ID ),
					'label' => $user->display_name ? $user->display_name : $user->user_login,
				);
			}
		}

		return $agents;
	}

	/**
	 * Gibt Dienstleister für Mapping-UI zurück
	 */
	public function get_workers_for_mapping() {
		$workers = appointments_get_all_workers();
		$data = array();

		foreach ( $workers as $worker ) {
			$data[ $worker->ID ] = appointments_get_worker_name( $worker->ID );
		}

		return $data;
	}

	/**
	 * Liefert die Agent -> Dienstleister Zuordnung
	 */
	public function get_agent_worker_map() {
		$map = $this->get_option( 'agent_worker_map', array() );
		if ( ! is_array( $map ) ) {
			$map = array();
		}

		return $this->sanitize_agent_worker_map( $map );
	}

	/**
	 * Gibt verwaltete Dienstleister eines Agents zurück
	 */
	public function get_managed_workers_for_agent( $agent_id ) {
		$agent_id = absint( $agent_id );
		$map = $this->get_agent_worker_map();
		return isset( $map[ $agent_id ] ) ? $map[ $agent_id ] : array();
	}

	/**
	 * Prüft, ob ein User einen Worker verwalten darf
	 * 
	 * @param int $user_id User-ID (0 = current user)
	 * @param int $worker_id Worker-ID
	 * @return bool
	 */
	public function can_manage_worker( $user_id = 0, $worker_id = 0 ) {
		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}

		$user_id = absint( $user_id );
		$worker_id = absint( $worker_id );

		if ( ! $user_id || ! $worker_id ) {
			return false;
		}

		// Admins dürfen immer
		if ( user_can( $user_id, 'manage_options' ) ) {
			return true;
		}

		$mode = $this->get_provider_agent_mode();

		// Modus 1: Independent - nur Admins
		if ( 'independent' === $mode ) {
			return false;
		}

		// Modus 2: Agents sind Provider - Agent darf nur sich selbst verwalten
		if ( 'agents_are_providers' === $mode ) {
			return $this->is_crm_agent_user( $user_id ) && $user_id === $worker_id;
		}

		// Modus 3: Agents verwalten Provider - Agent darf zugewiesene Worker verwalten
		if ( 'agents_manage_providers' === $mode ) {
			if ( ! $this->is_crm_agent_user( $user_id ) ) {
				return false;
			}
			$managed_workers = $this->get_managed_workers_for_agent( $user_id );
			return in_array( $worker_id, $managed_workers, true );
		}

		return false;
	}

	/**
	 * Gibt Worker-IDs zurück, die der User verwalten darf
	 * 
	 * @param int $user_id User-ID (0 = current user)
	 * @return array Array of worker IDs or null if unrestricted
	 */
	public function get_manageable_worker_ids( $user_id = 0 ) {
		if ( ! $user_id ) {
			$user_id = get_current_user_id();
		}

		$user_id = absint( $user_id );

		if ( ! $user_id ) {
			return array();
		}

		// Admins haben keine Einschränkungen
		if ( user_can( $user_id, 'manage_options' ) ) {
			return null; // null = keine Einschränkung
		}

		$mode = $this->get_provider_agent_mode();

		// Modus 1: Independent - keine Worker für normale User
		if ( 'independent' === $mode ) {
			return array();
		}

		// Modus 2: Agents sind Provider - nur eigener Worker
		if ( 'agents_are_providers' === $mode ) {
			if ( $this->is_crm_agent_user( $user_id ) && appointments_is_worker( $user_id ) ) {
				return array( $user_id );
			}
			return array();
		}

		// Modus 3: Agents verwalten Provider
		if ( 'agents_manage_providers' === $mode ) {
			if ( $this->is_crm_agent_user( $user_id ) ) {
				return $this->get_managed_workers_for_agent( $user_id );
			}
			return array();
		}

		return array();
	}

	/**
	 * Registriert Settings
	 */
	public function register_settings() {
		register_setting( 'app_crm_integration', 'app_crm_integration', array( $this, 'sanitize_settings' ) );
	}

	/**
	 * Sanitizer für Integrationsoptionen
	 */
	public function sanitize_settings( $input ) {
		$input = is_array( $input ) ? $input : array();

		$clean = array(
			'sync_customers' => empty( $input['sync_customers'] ) ? 0 : 1,
			'sync_appointments' => empty( $input['sync_appointments'] ) ? 0 : 1,
			'auto_create_invoices' => empty( $input['auto_create_invoices'] ) ? 0 : 1,
			'provider_agent_mode' => 'independent',
			'agent_worker_map' => array(),
		);

		$mode = isset( $input['provider_agent_mode'] ) ? sanitize_key( $input['provider_agent_mode'] ) : 'independent';
		if ( array_key_exists( $mode, $this->get_provider_agent_modes() ) ) {
			$clean['provider_agent_mode'] = $mode;
		}

		if ( isset( $input['agent_worker_map'] ) ) {
			$clean['agent_worker_map'] = $this->sanitize_agent_worker_map( $input['agent_worker_map'] );
		}

		$this->options = $clean;

		if ( 'agents_are_providers' === $clean['provider_agent_mode'] ) {
			foreach ( $this->get_crm_agents() as $agent ) {
				$this->ensure_worker_for_user( $agent['ID'] );
			}
		}

		return $clean;
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
			'crm_agents' => 0,
			'providers' => 0,
			'mapped_agents' => 0,
			'pm_active' => $this->is_pm_active(),
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
		
		$stats['crm_agents'] = count( $this->get_crm_agents() );
		$stats['providers'] = count( appointments_get_all_workers() );
		$stats['mapped_agents'] = count( $this->get_agent_worker_map() );
		
		return $stats;
	}

	/**
	 * Prüft, ob ein User als CRM-Agent behandelt wird
	 */
	public function is_crm_agent_user( $user_id ) {
		$user_id = absint( $user_id );
		if ( ! $user_id ) {
			return false;
		}

		$is_agent = user_can( $user_id, 'manage_crm' ) && ! user_can( $user_id, 'manage_options' );

		return (bool) apply_filters( 'app_crm_is_agent_user', $is_agent, $user_id );
	}

	/**
	 * Stellt sicher, dass ein User als Dienstleister angelegt ist
	 */
	private function ensure_worker_for_user( $user_id ) {
		$user_id = absint( $user_id );
		if ( ! $user_id ) {
			return false;
		}

		if ( appointments_is_worker( $user_id ) ) {
			return true;
		}

		$created = appointments_insert_worker( array(
			'ID' => $user_id,
			'services_provided' => array(),
		) );

		if ( $created ) {
			appointments_delete_worker_cache( $user_id );
		}

		return (bool) $created;
	}

	/**
	 * Sanitizer für Agent -> Dienstleister Mapping
	 */
	private function sanitize_agent_worker_map( $map ) {
		if ( ! is_array( $map ) ) {
			return array();
		}

		$clean = array();
		foreach ( $map as $agent_id => $worker_ids ) {
			$agent_id = absint( $agent_id );
			if ( ! $agent_id || ! $this->is_crm_agent_user( $agent_id ) ) {
				continue;
			}

			if ( ! is_array( $worker_ids ) ) {
				$worker_ids = array( $worker_ids );
			}

			$worker_ids = array_unique( array_filter( array_map( 'absint', $worker_ids ) ) );
			$valid_workers = array();
			foreach ( $worker_ids as $worker_id ) {
				if ( appointments_is_worker( $worker_id ) ) {
					$valid_workers[] = $worker_id;
				}
			}

			$clean[ $agent_id ] = array_values( $valid_workers );
		}

		return $clean;
	}

	// ====================
	// PM-Integration Helpers
	// ====================

	/**
	 * Prüft, ob Private-Messaging Plugin aktiv ist
	 */
	public function is_pm_active() {
		return class_exists( 'MMessaging' ) && function_exists( 'mm_display_contact_button' );
	}

	/**
	 * Gibt URL zur PM-Inbox zurück
	 * 
	 * @param string $box inbox|sent|archive|setting
	 * @return string|false
	 */
	public function get_pm_inbox_url( $box = 'inbox' ) {
		if ( ! $this->is_pm_active() ) {
			return false;
		}

		// Suche Seite mit [message_inbox] Shortcode
		$pages = get_posts( array(
			'post_type' => 'page',
			'post_status' => 'publish',
			's' => '[message_inbox]',
			'posts_per_page' => 1,
		) );

		if ( empty( $pages ) ) {
			return false;
		}

		$url = get_permalink( $pages[0]->ID );
		if ( 'inbox' !== $box ) {
			$url = add_query_arg( 'box', $box, $url );
		}

		return $url;
	}

	/**
	 * Gibt verantwortlichen Agent für einen Worker zurück (Modus 3)
	 * 
	 * @param int $worker_id Worker-ID
	 * @return int|false Agent User-ID oder false
	 */
	public function get_agent_for_worker( $worker_id ) {
		$worker_id = absint( $worker_id );
		if ( ! $worker_id ) {
			return false;
		}

		$mode = $this->get_provider_agent_mode();

		// Modus 2: Worker ist sein eigener Agent
		if ( 'agents_are_providers' === $mode ) {
			if ( $this->is_crm_agent_user( $worker_id ) ) {
				return $worker_id;
			}
			return false;
		}

		// Modus 3: Suche Agent der diesen Worker verwaltet
		if ( 'agents_manage_providers' === $mode ) {
			$map = $this->get_agent_worker_map();
			foreach ( $map as $agent_id => $worker_ids ) {
				if ( in_array( $worker_id, $worker_ids, true ) ) {
					return absint( $agent_id );
				}
			}
		}

		return false;
	}

	/**
	 * Gibt alle Worker-IDs zurück, für die ein Agent verantwortlich ist
	 * 
	 * @param int $agent_id Agent User-ID
	 * @return array Worker-IDs
	 */
	public function get_workers_for_agent( $agent_id ) {
		$agent_id = absint( $agent_id );
		if ( ! $agent_id || ! $this->is_crm_agent_user( $agent_id ) ) {
			return array();
		}

		$mode = $this->get_provider_agent_mode();

		// Modus 2: Agent ist sein eigener Worker
		if ( 'agents_are_providers' === $mode ) {
			if ( appointments_is_worker( $agent_id ) ) {
				return array( $agent_id );
			}
			return array();
		}

		// Modus 3: Workers aus Mapping
		if ( 'agents_manage_providers' === $mode ) {
			return $this->get_managed_workers_for_agent( $agent_id );
		}

		return array();
	}

	/**
	 * Erstellt PM Contact-Button HTML für einen User
	 * 
	 * @param int $user_id Ziel User-ID
	 * @param string $text Button-Text
	 * @param string $subject Nachrichten-Betreff
	 * @param string $class CSS-Klassen
	 * @return string HTML oder leerer String
	 */
	public function render_pm_contact_button( $user_id, $text = '', $subject = '', $class = 'button' ) {
		if ( ! $this->is_pm_active() || ! $user_id ) {
			return '';
		}

		if ( empty( $text ) ) {
			$text = __( 'Nachricht senden', 'appointments' );
		}

		return mm_display_contact_button( $user_id, $class, $text, $subject, false );
	}
}

// Initialisiere Integration
function app_crm_integration_init() {
	return App_CRM_Integration::get_instance();
}
add_action( 'plugins_loaded', 'app_crm_integration_init', 20 );
