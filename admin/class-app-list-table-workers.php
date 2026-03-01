<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Appointments_WP_List_Table_Workers extends WP_List_Table {

	private $currency;
	private $services = array();

	public function __construct() {
		//Set parent defaults
		parent::__construct( array(
			'singular'  => 'worker',
			'plural'    => 'workers',
			'ajax'      => false,
		) );
		$this->currency = appointments_get_option( 'currency' );
	}

	/**
	 * Handle default column
	 *
	 * @since 2.4.0
	 */
	public function column_default( $item, $column_name ) {
		return apply_filters( 'appointments_list_column_'.$column_name, '', $item );
	}

	/**
	 * Handle dummy column
	 *
	 * @since 2.4.0
	 */
	public function column_dummy( $item ) {
		$is_dummy = $item->is_dummy();
		return sprintf(
			'<span data-state="%d">%s</span>',
			esc_attr( $is_dummy ),
			$is_dummy? esc_html_x( 'Ja', 'dummy worker', 'appointments' ):esc_html_x( 'Nein', 'dummy worker', 'appointments' )
		);
	}

	/**
	 * Column price.
	 *
	 * @since 2.3.1
	 */
	public function column_price( $item ) {
		$value = intval( $item->price );
		if ( empty( $value ) ) {
			return __( 'Frei', 'appointments' );
		}
		return $value;
	}

	public function column_services_provided( $item ) {
		if ( empty( $item->services_provided ) ) {
			return __( 'Keine Dienstanbieter ausgewählt.', 'appointments' );
		}
		$content = '';
		$ids = array();

		foreach ( $item->services_provided as $id ) {
			$name = sprintf( __( 'Fehlender Service: %d.', 'appointments' ), $id );
			$value = null;
			if ( isset( $this->services[ $id ] ) ) {
				$value = $this->services[ $id ];
			}
			if ( is_a( $value, 'Appointments_Service' ) ) {
				$name = $value->name;
				$ids[] = $id;
			}
			$content .= sprintf( '<li>%s</li>', $name );
		}
		return sprintf(
			'<ul data-services="%s">%s</ul>',
			implode( ',', $ids ),
			$content
		);
	}

	public function column_name( $item ) {
		$user = get_user_by( 'id', $item->ID );
		$edit_link = sprintf(
			'<a href="#section-edit-worker" data-id="%s" data-nonce="%s" class="edit">%%s</a>',
			esc_attr( $item->ID ),
			esc_attr( wp_create_nonce( 'worker-'.$item->ID ) )
		);
		$actions = array(
			'ID' => $item->ID,
			'edit'    => sprintf( $edit_link, __( 'Bearbeiten', 'appointments' ) ),
			'delete'    => sprintf(
				'<a href="#" data-id="%s" data-nonce="%s" class="delete">%s</a>',
				esc_attr( $item->ID ),
				esc_attr( wp_create_nonce( 'worker-'.$item->ID ) ),
				__( 'Löschen', 'appointments' )
			),
		);
		if ( false !== $user && current_user_can( 'edit_users', $user->ID ) ) {
			$actions['user_profile'] = sprintf(
				'<a href="%s">%s</a>',
				get_edit_user_link( $user->ID ),
				esc_html_x( 'Profil', 'user pfofile link on Service Providers screen', 'appointments' )
			);
		}
		$page = $this->get_worker_page_link( $item, false );
		if ( false !== $page ) {
			$actions['page_view'] = $page;
		}
		
		// CRM-Integration: PM Contact-Button für Agenten
		if ( class_exists( 'App_CRM_Integration' ) && false !== $user ) {
			$integration = App_CRM_Integration::get_instance();
			$current_user_id = get_current_user_id();
			
			// Zeige PM-Link wenn PM aktiv ist UND User nicht sich selbst kontaktiert
			if ( $integration->is_pm_active() && $current_user_id !== $item->ID ) {
				$mode = $integration->get_provider_agent_mode();
				
				// Modus 3: Agent kann seine zugewiesenen Provider kontaktieren
				if ( 'agents_manage_providers' === $mode ) {
					if ( $integration->is_crm_agent_user( $current_user_id ) ) {
						if ( $integration->can_manage_worker( $current_user_id, $item->ID ) ) {
							$actions['message'] = sprintf(
								'<span class="app-pm-contact">%s</span>',
								$integration->render_pm_contact_button(
									$item->ID,
									__( 'Nachricht', 'appointments' ),
									sprintf( __( 'Betreff: Dienstleister %s', 'appointments' ), $user->display_name ),
									''
								)
							);
						}
					}
				}
			}
		}
		
		$value = sprintf( $edit_link, esc_html( false === $user? __( '[wrong user]', 'appointments' ):$user->display_name ) );
		return sprintf( '<strong>%s</strong>%s', $value, $this->row_actions( $actions ) );
	}

	public function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="%1$s[]" value="%2$s" />',
			/*$1%s*/ $this->_args['singular'],
			/*$2%s*/ $item->ID
		);
	}

	private function get_worker_page_link( $item, $rich = true ) {
		if ( empty( $item->page ) ) {
			return false;
		}
		$page = get_page( $item->page );
		if ( empty( $page ) ) {
			return false;
		}
		if ( $rich ) {
			return sprintf(
				'%s<div class="row-actions"><a href="%s">%s</a></div>',
				$page->post_title,
				get_page_link( $page->ID ),
				__( 'Seite anzeigen', 'appointments' )
			);
		}
		return sprintf(
			'<a href="%s">%s</a>',
			get_page_link( $page->ID ),
			__( 'Seite anzeigen', 'appointments' )
		);
	}

	public function column_page( $item ) {
		$page = $this->get_worker_page_link( $item );
		if ( empty( $page ) ) {
			return '<span aria-hidden="true">&#8212;</span>';
		}
		return sprintf( '<span data-id="%d">%s</span>', $item->page, $page );
	}

	public function get_columns() {
		$columns = array(
			'cb'        => '<input type="checkbox" />', //Render a checkbox instead of text
			'name' => __( 'Dienstleister', 'appointments' ),
			'dummy' => __( 'Dummy', 'appointments' ),
			'price' => sprintf( __( 'Zusätzlicher Preis (%s)', 'appointments' ), $this->currency ),
			'services_provided' => __( 'Angebotene Dienstleistungen', 'appointments' ),
			'page' => __( 'Beschreibungsseite', 'appointments' ),
		);
		/**
		 * Allow to filter columns
		 *
		 * @since 2.4.0
		 */
		return apply_filters( 'manage_appointments_service_provider_columns', $columns );
	}

	public function get_bulk_actions() {
		$actions = array(
			'delete'    => 'Delete',
		);
		return $actions;
	}

	/**
	 * delete selected workers
	 *
	 * @since 2.3.0
	 */
	public function process_bulk_action() {
		$action = $this->current_action();
		$singular = $this->_args['singular'];
		if (
			'delete' === $action
			&& isset( $_POST['_wpnonce'] )
			&& isset( $_POST[ $singular ] )
			&& ! empty( $_POST[ $singular ] )
			&& is_array( $_POST[ $singular ] )
			&& wp_verify_nonce( $_POST['_wpnonce'], 'bulk-'.$this->_args['plural'] )
		) {
			// CRM-Integration: Berechtigungsprüfung
			$integration = null;
			if ( class_exists( 'App_CRM_Integration' ) ) {
				$integration = App_CRM_Integration::get_instance();
			}
			
			foreach ( $_POST[ $singular ] as $ID ) {
				$worker_id = absint( $ID );
				
				// Berechtigung prüfen
				if ( $integration && ! $integration->can_manage_worker( 0, $worker_id ) ) {
					continue; // Überspringen wenn keine Berechtigung
				}
				
				appointments_delete_worker( $worker_id );
			}
		}
	}

	public function prepare_items() {
		$per_page = $this->get_items_per_page( 'app_workers_per_page', 20 );;
		$columns = $this->get_columns();
		$hidden = get_hidden_columns( $this->screen );
		/**
		 * services
		 */
		$data = appointments_get_services();
		foreach ( $data as $service ) {
			$this->services[ $service->ID ] = $service;
		}
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );
		$this->process_bulk_action();
		
		// CRM-Integration: Worker nach Berechtigung filtern
		$worker_ids_filter = null;
		if ( class_exists( 'App_CRM_Integration' ) ) {
			$integration = App_CRM_Integration::get_instance();
			$manageable_ids = $integration->get_manageable_worker_ids();
			if ( is_array( $manageable_ids ) ) {
				$worker_ids_filter = $manageable_ids;
			}
		}
		
		$args = array(
			'orderby' => 'name',
		);
		
		// Wenn Filter aktiv, nur erlaubte Worker zählen/laden
		if ( is_array( $worker_ids_filter ) ) {
			if ( empty( $worker_ids_filter ) ) {
				// Agent hat keine Worker zugewiesen
				$total_items = 0;
				$this->items = array();
			} else {
				// Alle Worker mit IDs in Filter holen
				$all_workers = appointments_get_workers( $args );
				$filtered_workers = array();
				foreach ( $all_workers as $worker ) {
					if ( in_array( $worker->ID, $worker_ids_filter, true ) ) {
						$filtered_workers[] = $worker;
					}
				}
				
				$total_items = count( $filtered_workers );
				$current_page = $this->get_pagenum();
				$offset = ( $current_page - 1 ) * $per_page;
				
				// Manuell paginieren
				$this->items = array_slice( $filtered_workers, $offset, $per_page );
			}
		} else {
			// Admin: alle Worker
			$total_items = appointments_get_workers( array( 'count' => true ) );
			$current_page = $this->get_pagenum();
			$offset = ( $current_page - 1 ) * $per_page;
			$args['offset'] = $offset;
			$args['limit'] = $per_page;
			$data = appointments_get_workers( $args );
			$this->items = $data;
		}
		
		/**
		 * REQUIRED. We also have to register our pagination options & calculations.
		 */
		$this->set_pagination_args( array(
			'total_items' => $total_items,
			'per_page'    => $per_page,
			'total_pages' => ceil( $total_items / $per_page ),
		) );
	}
}

