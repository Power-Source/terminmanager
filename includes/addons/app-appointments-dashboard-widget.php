<?php
/*
Plugin Name: Dashboard-Widget Meine Termine
Description: Zeigt meine Termine im Dashboard-Widget an
Plugin URI: https://cp-psource.github.io/terminmanager-pro/
Version: 2.0
AddonType: Dashboard Widget
Author: PSOURCE
*/

class App_my_appointments_dashboard_widget {
	private $_data;
	private $_core;

	private function __construct () {
		
	}

	public static function serve () {
		$me = new App_my_appointments_dashboard_widget;
		$me->_add_hooks();
	}

	private function _add_hooks () {
		add_action('plugins_loaded', array($this, 'initialize'));
		add_action( 'wp_dashboard_setup', array( $this, 'app_dashboard_widgets' ) );
	}

	public function initialize () {
		global $appointments;
		$this->_core = $appointments;
		$this->_data = $appointments->options;
	}
	
	public function app_dashboard_widgets() {
		wp_add_dashboard_widget(
			'appointments_widget',
			'<span class="dashicons dashicons-calendar-alt"></span> ' . __( 'Meine Termine', 'appointments' ),
			array( $this, 'appointments_widget_cb' )
	       );
	}
	
	public function appointments_widget_cb() {
		global $current_user;
		
		// Prüfen ob User ein Worker/Admin ist
		$is_worker = appointments_is_worker( $current_user->ID );
		$is_admin = current_user_can( 'manage_options' );
		
		// System-weite Statistiken für Admins
		$system_counts = array();
		if ( $is_admin ) {
			$system_counts = appointments_count_appointments();
		}
		
		// Heute's Datum
		$today_start = date( 'Y-m-d 00:00:00' );
		$today_end = date( 'Y-m-d 23:59:59' );
		
		// Statistiken sammeln
		$upcoming_appointments = appointments_get_appointments( array(
			'user' => $current_user->ID,
			'status' => array( 'paid', 'confirmed', 'pending' ),
			'orderby' => 'start',
			'order' => 'ASC',
			'per_page' => -1
		) );
		
		// Heutige Termine
		$today_appointments = array_filter( $upcoming_appointments, function( $app ) use ( $today_start, $today_end ) {
			return $app->start >= $today_start && $app->start <= $today_end;
		} );
		
		// Zukünftige Termine (nach heute)
		$future_appointments = array_filter( $upcoming_appointments, function( $app ) use ( $today_end ) {
			return $app->start > $today_end;
		} );
		
		// Ausstehende Termine (pending)
		$pending_appointments = array_filter( $upcoming_appointments, function( $app ) {
			return $app->status === 'pending';
		} );
		
		// Worker Termine wenn zutreffend
		$worker_appointments = array();
		if ( $is_worker ) {
			$worker_appointments = appointments_get_appointments( array(
				'worker' => $current_user->ID,
				'status' => array( 'paid', 'confirmed', 'pending' ),
				'orderby' => 'start',
				'order' => 'ASC',
				'per_page' => -1
			) );
			
			$worker_appointments = array_filter( $worker_appointments, function( $app ) use ( $today_end ) {
				return $app->start > date( 'Y-m-d H:i:s' );
			} );
		}
		
		// Nächste Termine (max 5)
		$next_appointments = array_slice( $upcoming_appointments, 0, 5 );
		
		?>
		<div class="app-dashboard-widget">
			
			<?php if ( $is_admin && ! empty( $system_counts ) ): ?>
			<!-- Admin System-Übersicht -->
			<div class="app-admin-overview">
				<h4 class="app-section-title">
					<span class="dashicons dashicons-admin-settings"></span>
					<?php _e( 'System-Übersicht (Alle Benutzer)', 'appointments' ); ?>
				</h4>
				<div class="app-admin-stats">
					<div class="app-admin-stat">
						<span class="dashicons dashicons-yes-alt"></span>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=appointments&status=paid,confirmed' ) ); ?>">
							<?php printf( _n( '%d Aktiver Termin', '%d Aktive Termine', $system_counts['paid'] + $system_counts['confirmed'], 'appointments' ), $system_counts['paid'] + $system_counts['confirmed'] ); ?>
						</a>
					</div>
					<div class="app-admin-stat app-admin-stat-pending">
						<span class="dashicons dashicons-backup"></span>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=appointments&status=pending' ) ); ?>">
							<?php printf( _n( '%d Ausstehender Termin', '%d Ausstehende Termine', $system_counts['pending'], 'appointments' ), $system_counts['pending'] ); ?>
						</a>
					</div>
				</div>
			</div>
			<?php endif; ?>
			
			<!-- Statistiken -->
			<div class="app-stats-grid">
				<div class="app-stat-box app-stat-today">
					<span class="dashicons dashicons-calendar"></span>
					<div class="app-stat-content">
						<span class="app-stat-number"><?php echo count( $today_appointments ); ?></span>
						<span class="app-stat-label"><?php _e( 'Heute', 'appointments' ); ?></span>
					</div>
				</div>
				
				<div class="app-stat-box app-stat-upcoming">
					<span class="dashicons dashicons-clock"></span>
					<div class="app-stat-content">
						<span class="app-stat-number"><?php echo count( $future_appointments ); ?></span>
						<span class="app-stat-label"><?php _e( 'Kommende', 'appointments' ); ?></span>
					</div>
				</div>
				
				<?php if ( count( $pending_appointments ) > 0 ): ?>
				<div class="app-stat-box app-stat-pending">
					<span class="dashicons dashicons-warning"></span>
					<div class="app-stat-content">
						<span class="app-stat-number"><?php echo count( $pending_appointments ); ?></span>
						<span class="app-stat-label"><?php _e( 'Ausstehend', 'appointments' ); ?></span>
					</div>
				</div>
				<?php endif; ?>
				
				<?php if ( $is_worker && count( $worker_appointments ) > 0 ): ?>
				<div class="app-stat-box app-stat-worker">
					<span class="dashicons dashicons-groups"></span>
					<div class="app-stat-content">
						<span class="app-stat-number"><?php echo count( $worker_appointments ); ?></span>
						<span class="app-stat-label"><?php _e( 'Als Anbieter', 'appointments' ); ?></span>
					</div>
				</div>
				<?php endif; ?>
			</div>
			
			<!-- Nächste Termine -->
			<?php if ( ! empty( $next_appointments ) ): ?>
			<div class="app-next-appointments">
				<h4 class="app-section-title">
					<span class="dashicons dashicons-list-view"></span>
					<?php _e( 'Nächste Termine', 'appointments' ); ?>
				</h4>
				<ul class="app-appointments-list">
					<?php foreach ( $next_appointments as $app ): 
						$service = appointments_get_service( $app->service );
						$worker = appointments_get_worker( $app->worker );
						$status_class = 'app-status-' . $app->status;
					?>
					<li class="app-appointment-item <?php echo esc_attr( $status_class ); ?>">
						<div class="app-appointment-date">
							<span class="dashicons dashicons-calendar-alt"></span>
							<?php echo date_i18n( 'd.m.Y', strtotime( $app->start ) ); ?>
						</div>
						<div class="app-appointment-time">
							<span class="dashicons dashicons-clock"></span>
							<?php echo date_i18n( 'H:i', strtotime( $app->start ) ); ?> Uhr
						</div>
						<div class="app-appointment-service">
							<?php echo esc_html( $service ? $service->name : __( 'Service gelöscht', 'appointments' ) ); ?>
						</div>
						<div class="app-appointment-status">
							<span class="app-badge app-badge-<?php echo esc_attr( $app->status ); ?>">
								<?php echo App_Template::get_status_name( $app->status ); ?>
							</span>
						</div>
					</li>
					<?php endforeach; ?>
				</ul>
			</div>
			<?php else: ?>
			<div class="app-empty-state">
				<span class="dashicons dashicons-smiley"></span>
				<p><?php _e( 'Du hast derzeit keine Termine.', 'appointments' ); ?></p>
			</div>
			<?php endif; ?>
			
			<!-- Schnellzugriffe -->
			<div class="app-quick-links">
				<a href="<?php echo admin_url( 'admin.php?page=appointments' ); ?>" class="app-quick-link">
					<span class="dashicons dashicons-list-view"></span>
					<?php _e( 'Alle Termine', 'appointments' ); ?>
				</a>
				
				<?php if ( current_user_can( 'manage_options' ) ): ?>
				<a href="<?php echo admin_url( 'admin.php?page=app_settings' ); ?>" class="app-quick-link">
					<span class="dashicons dashicons-admin-settings"></span>
					<?php _e( 'Einstellungen', 'appointments' ); ?>
				</a>
				<?php endif; ?>
				
				<?php if ( $is_worker ): ?>
				<a href="<?php echo admin_url( 'admin.php?page=appointments&type=worker' ); ?>" class="app-quick-link">
					<span class="dashicons dashicons-businessman"></span>
					<?php _e( 'Meine Anbieter-Termine', 'appointments' ); ?>
				</a>
				<?php endif; ?>
			</div>
			
		</div>
		
		<style>
		.app-dashboard-widget {
			margin: -12px;
		}
		
		/* Admin System-Übersicht */
		.app-admin-overview {
			padding: 16px;
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
			color: white;
			border-bottom: 3px solid #5568d3;
		}
		
		.app-admin-overview .app-section-title {
			color: white;
			margin: 0 0 12px;
			opacity: 0.95;
		}
		
		.app-admin-overview .app-section-title .dashicons {
			color: rgba(255,255,255,0.9);
		}
		
		.app-admin-stats {
			display: grid;
			grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
			gap: 12px;
		}
		
		.app-admin-stat {
			display: flex;
			align-items: center;
			gap: 10px;
			padding: 14px;
			background: rgba(255, 255, 255, 0.15);
			border-radius: 8px;
			border: 1px solid rgba(255, 255, 255, 0.2);
			transition: all 0.3s;
			backdrop-filter: blur(10px);
		}
		
		.app-admin-stat:hover {
			background: rgba(255, 255, 255, 0.25);
			transform: translateY(-2px);
			box-shadow: 0 4px 12px rgba(0,0,0,0.15);
		}
		
		.app-admin-stat .dashicons {
			font-size: 32px;
			width: 32px;
			height: 32px;
			color: white;
			opacity: 0.9;
		}
		
		.app-admin-stat a {
			color: white;
			text-decoration: none;
			font-size: 15px;
			font-weight: 600;
			line-height: 1.3;
		}
		
		.app-admin-stat a:hover {
			text-decoration: underline;
		}
		
		/* Statistik-Grid */
		.app-stats-grid {
			display: grid;
			grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
			gap: 10px;
			padding: 16px;
			background: #f7f7f7;
			border-bottom: 1px solid #ddd;
		}
		
		.app-stat-box {
			display: flex;
			align-items: center;
			gap: 10px;
			padding: 12px;
			background: white;
			border-radius: 8px;
			border-left: 3px solid #2271b1;
			transition: transform 0.2s, box-shadow 0.2s;
		}
		
		.app-stat-box:hover {
			transform: translateY(-2px);
			box-shadow: 0 2px 8px rgba(0,0,0,0.1);
		}
		
		.app-stat-box .dashicons {
			font-size: 28px;
			width: 28px;
			height: 28px;
			color: #2271b1;
		}
		
		.app-stat-box.app-stat-today .dashicons {
			color: #00a32a;
		}
		
		.app-stat-box.app-stat-pending .dashicons,
		.app-stat-box.app-stat-pending {
			border-left-color: #dba617;
			color: #dba617;
		}
		
		.app-stat-box.app-stat-worker {
			border-left-color: #8c62b7;
		}
		
		.app-stat-box.app-stat-worker .dashicons {
			color: #8c62b7;
		}
		
		.app-stat-content {
			display: flex;
			flex-direction: column;
			line-height: 1.2;
		}
		
		.app-stat-number {
			font-size: 24px;
			font-weight: 600;
			color: #1d2327;
		}
		
		.app-stat-label {
			font-size: 12px;
			color: #646970;
			text-transform: uppercase;
			letter-spacing: 0.5px;
		}
		
		/* Nächste Termine */
		.app-next-appointments {
			padding: 16px;
		}
		
		.app-section-title {
			display: flex;
			align-items: center;
			gap: 8px;
			margin: 0 0 12px;
			font-size: 14px;
			font-weight: 600;
			color: #1d2327;
		}
		
		.app-section-title .dashicons {
			font-size: 18px;
			width: 18px;
			height: 18px;
			color: #2271b1;
		}
		
		.app-appointments-list {
			list-style: none;
			margin: 0;
			padding: 0;
		}
		
		.app-appointment-item {
			display: grid;
			grid-template-columns: auto auto 1fr auto;
			gap: 12px;
			align-items: center;
			padding: 10px;
			margin-bottom: 8px;
			background: #f7f7f7;
			border-radius: 6px;
			border-left: 3px solid #2271b1;
			font-size: 13px;
		}
		
		.app-appointment-item:hover {
			background: #f0f0f1;
		}
		
		.app-appointment-item.app-status-pending {
			border-left-color: #dba617;
		}
		
		.app-appointment-item.app-status-confirmed {
			border-left-color: #00a32a;
		}
		
		.app-appointment-item.app-status-paid {
			border-left-color: #00a32a;
		}
		
		.app-appointment-date,
		.app-appointment-time {
			display: flex;
			align-items: center;
			gap: 4px;
			color: #3c434a;
			font-weight: 500;
		}
		
		.app-appointment-date .dashicons,
		.app-appointment-time .dashicons {
			font-size: 16px;
			width: 16px;
			height: 16px;
			color: #646970;
		}
		
		.app-appointment-service {
			color: #1d2327;
			font-weight: 500;
		}
		
		.app-badge {
			display: inline-block;
			padding: 3px 8px;
			font-size: 11px;
			font-weight: 600;
			border-radius: 3px;
			text-transform: uppercase;
			letter-spacing: 0.5px;
		}
		
		.app-badge-pending {
			background: #fcf9e8;
			color: #8a6600;
			border: 1px solid #f0e6bf;
		}
		
		.app-badge-confirmed,
		.app-badge-paid {
			background: #edfaef;
			color: #00761f;
			border: 1px solid #00a32a33;
		}
		
		/* Leerer Zustand */
		.app-empty-state {
			text-align: center;
			padding: 40px 20px;
			color: #646970;
		}
		
		.app-empty-state .dashicons {
			font-size: 48px;
			width: 48px;
			height: 48px;
			color: #c3c4c7;
			margin-bottom: 12px;
		}
		
		.app-empty-state p {
			margin: 0;
			font-size: 15px;
		}
		
		/* Schnellzugriffe */
		.app-quick-links {
			display: flex;
			flex-wrap: wrap;
			gap: 8px;
			padding: 12px 16px 16px;
			border-top: 1px solid #ddd;
			background: #f7f7f7;
		}
		
		.app-quick-link {
			display: inline-flex;
			align-items: center;
			gap: 6px;
			padding: 8px 12px;
			background: white;
			border: 1px solid #c3c4c7;
			border-radius: 4px;
			color: #2271b1;
			text-decoration: none;
			font-size: 13px;
			font-weight: 500;
			transition: all 0.2s;
		}
		
		.app-quick-link:hover {
			background: #2271b1;
			color: white;
			border-color: #2271b1;
		}
		
		.app-quick-link .dashicons {
			font-size: 18px;
			width: 18px;
			height: 18px;
		}
		</style>
		
		<script type="text/javascript">
		(function($) {
			$(document).ready(function() {
				// Widget nach ganz oben verschieben
				var widget = $('#appointments_widget');
				if (widget.length) {
					var targetColumn = $('#dashboard-widgets .column-left');
					if (targetColumn.length) {
						widget.prependTo(targetColumn);
					} else {
						widget.parent().prepend(widget);
					}
				}
			});
		})(jQuery);
		</script>
		<?php
	}
	
}
App_my_appointments_dashboard_widget::serve();
