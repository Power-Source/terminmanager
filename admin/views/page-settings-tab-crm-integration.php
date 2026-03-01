<?php
/**
 * PS Smart CRM Integration Settings
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$integration = App_CRM_Integration::get_instance();
$is_active = $integration->is_active();
$stats = $integration->get_stats();
$options = get_option( 'app_crm_integration', array() );

$sync_customers = isset( $options['sync_customers'] ) ? $options['sync_customers'] : false;
$sync_appointments = isset( $options['sync_appointments'] ) ? $options['sync_appointments'] : false;
$auto_create_invoices = isset( $options['auto_create_invoices'] ) ? $options['auto_create_invoices'] : false;
?>

<div class="app-crm-integration-settings">
	<?php if ( ! $is_active ): ?>
		<div class="notice notice-warning">
			<p>
				<strong><?php _e( 'PS Smart CRM ist nicht installiert oder aktiviert!', 'appointments' ); ?></strong><br>
				<?php _e( 'Um diese Integration zu nutzen, installiere und aktiviere bitte das PS Smart CRM Plugin.', 'appointments' ); ?>
			</p>
			<p>
				<a href="https://github.com/Power-Source/ps-smart-crm" target="_blank" class="button button-primary">
					<?php _e( 'PS Smart CRM herunterladen', 'appointments' ); ?>
				</a>
			</p>
		</div>
	<?php else: ?>
		<div class="notice notice-success">
			<p>
				<strong><?php _e( 'PS Smart CRM ist aktiv!', 'appointments' ); ?></strong><br>
				<?php _e( 'Die Integration steht zur Verfügung.', 'appointments' ); ?>
			</p>
		</div>
	<?php endif; ?>
	
	<?php if ( $is_active ): ?>
	
	<!-- Statistiken -->
	<div class="app-crm-stats-grid">
		<div class="app-crm-stat-card">
			<div class="app-crm-stat-icon">
				<span class="dashicons dashicons-groups"></span>
			</div>
			<div class="app-crm-stat-content">
				<h3><?php echo esc_html( $stats['synced_customers'] ); ?> / <?php echo esc_html( $stats['crm_customers'] ); ?></h3>
				<p><?php _e( 'Synchronisierte Kunden', 'appointments' ); ?></p>
			</div>
		</div>
		
		<div class="app-crm-stat-card">
			<div class="app-crm-stat-icon">
				<span class="dashicons dashicons-calendar-alt"></span>
			</div>
			<div class="app-crm-stat-content">
				<h3><?php echo esc_html( $stats['synced_appointments'] ); ?></h3>
				<p><?php _e( 'Synchronisierte Termine', 'appointments' ); ?></p>
			</div>
		</div>
		
		<div class="app-crm-stat-card">
			<div class="app-crm-stat-icon">
				<span class="dashicons dashicons-update"></span>
			</div>
			<div class="app-crm-stat-content">
				<h3><?php echo $sync_customers || $sync_appointments ? __( 'Aktiv', 'appointments' ) : __( 'Inaktiv', 'appointments' ); ?></h3>
				<p><?php _e( 'Sync-Status', 'appointments' ); ?></p>
			</div>
		</div>
	</div>
	
	<form method="post" action="options.php" class="app-crm-settings-form">
		<?php settings_fields( 'app_crm_integration' ); ?>
		
		<table class="form-table">
			<!-- Kunden-Synchronisation -->
			<tr>
				<th scope="row">
					<label for="sync_customers">
						<?php _e( 'Kunden synchronisieren', 'appointments' ); ?>
					</label>
				</th>
				<td>
					<label>
						<input type="checkbox" id="sync_customers" name="app_crm_integration[sync_customers]" value="1" <?php checked( $sync_customers, 1 ); ?>>
						<?php _e( 'WordPress-Benutzer automatisch als CRM-Kunden synchronisieren', 'appointments' ); ?>
					</label>
					<p class="description">
						<?php _e( 'Wenn aktiviert, werden neue Benutzer automatisch im CRM als Kunden angelegt und bei Änderungen aktualisiert.', 'appointments' ); ?>
					</p>
				</td>
			</tr>
			
			<!-- Termin-Synchronisation -->
			<tr>
				<th scope="row">
					<label for="sync_appointments">
						<?php _e( 'Termine synchronisieren', 'appointments' ); ?>
					</label>
				</th>
				<td>
					<label>
						<input type="checkbox" id="sync_appointments" name="app_crm_integration[sync_appointments]" value="1" <?php checked( $sync_appointments, 1 ); ?>>
						<?php _e( 'Termine automatisch ins CRM übertragen', 'appointments' ); ?>
					</label>
					<p class="description">
						<?php _e( 'Wenn aktiviert, werden gebuchte Termine automatisch in die CRM-Agenda eingetragen und bei Änderungen aktualisiert.', 'appointments' ); ?>
					</p>
				</td>
			</tr>
			
			<!-- Automatische Rechnungserstellung -->
			<tr>
				<th scope="row">
					<label for="auto_create_invoices">
						<?php _e( 'Automatische Rechnung', 'appointments' ); ?>
					</label>
				</th>
				<td>
					<label>
						<input type="checkbox" id="auto_create_invoices" name="app_crm_integration[auto_create_invoices]" value="1" <?php checked( $auto_create_invoices, 1 ); ?>>
						<?php _e( 'Automatisch Rechnung im CRM erstellen bei bezahlten Terminen', 'appointments' ); ?>
					</label>
					<p class="description">
						<?php _e( 'Wenn ein Termin als "bezahlt" markiert wird, wird automatisch eine Rechnung im CRM erstellt.', 'appointments' ); ?>
					</p>
					<p class="description" style="color: #856404;">
						<strong><?php _e( 'Hinweis:', 'appointments' ); ?></strong>
						<?php _e( 'Diese Funktion wird in Schritt 3 implementiert.', 'appointments' ); ?>
					</p>
				</td>
			</tr>
		</table>
		
		<?php submit_button( __( 'Einstellungen speichern', 'appointments' ) ); ?>
	</form>
	
	<!-- Manuelle Synchronisation -->
	<div class="app-crm-manual-sync">
		<h3><?php _e( 'Manuelle Synchronisation', 'appointments' ); ?></h3>
		<p class="description">
			<?php _e( 'Synchronisiere bestehende Daten einmalig. Dies kann bei großen Datenmengen einige Zeit dauern.', 'appointments' ); ?>
		</p>
		
		<div class="app-crm-sync-buttons">
			<button type="button" class="button button-secondary" id="app-crm-sync-customers">
				<span class="dashicons dashicons-groups"></span>
				<?php _e( 'Alle Kunden synchronisieren', 'appointments' ); ?>
			</button>
			
			<button type="button" class="button button-secondary" id="app-crm-sync-appointments">
				<span class="dashicons dashicons-calendar-alt"></span>
				<?php _e( 'Alle Termine synchronisieren', 'appointments' ); ?>
			</button>
		</div>
		
		<div id="app-crm-sync-result" style="margin-top: 15px;"></div>
	</div>
	
	<?php endif; ?>
	
</div>

<style>
.app-crm-stats-grid {
	display: grid;
	grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
	gap: 20px;
	margin: 30px 0;
}

.app-crm-stat-card {
	background: white;
	border: 1px solid #c3c4c7;
	border-radius: 8px;
	padding: 20px;
	display: flex;
	align-items: center;
	gap: 15px;
	box-shadow: 0 1px 3px rgba(0,0,0,0.05);
	transition: all 0.3s;
}

.app-crm-stat-card:hover {
	box-shadow: 0 2px 8px rgba(0,0,0,0.1);
	transform: translateY(-2px);
}

.app-crm-stat-icon {
	width: 60px;
	height: 60px;
	background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
	border-radius: 50%;
	display: flex;
	align-items: center;
	justify-content: center;
}

.app-crm-stat-icon .dashicons {
	font-size: 32px;
	width: 32px;
	height: 32px;
	color: white;
}

.app-crm-stat-content h3 {
	margin: 0;
	font-size: 28px;
	font-weight: 600;
	color: #1d2327;
}

.app-crm-stat-content p {
	margin: 5px 0 0;
	color: #646970;
	font-size: 14px;
}

.app-crm-settings-form {
	background: white;
	border: 1px solid #c3c4c7;
	border-radius: 8px;
	padding: 20px;
	margin: 20px 0;
}

.app-crm-manual-sync {
	background: white;
	border: 1px solid #c3c4c7;
	border-radius: 8px;
	padding: 20px;
	margin: 20px 0;
}

.app-crm-manual-sync h3 {
	margin-top: 0;
}

.app-crm-sync-buttons {
	display: flex;
	gap: 10px;
	flex-wrap: wrap;
	margin-top: 15px;
}

.app-crm-sync-buttons .button {
	display: inline-flex;
	align-items: center;
	gap: 8px;
}

.app-crm-sync-buttons .button .dashicons {
	font-size: 18px;
	width: 18px;
	height: 18px;
}

#app-crm-sync-result {
	padding: 12px;
	border-radius: 4px;
	display: none;
}

#app-crm-sync-result.success {
	display: block;
	background: #d4edda;
	border: 1px solid #c3e6cb;
	color: #155724;
}

#app-crm-sync-result.error {
	display: block;
	background: #f8d7da;
	border: 1px solid #f5c6cb;
	color: #721c24;
}

#app-crm-sync-result.loading {
	display: block;
	background: #d1ecf1;
	border: 1px solid #bee5eb;
	color: #0c5460;
}
</style>

<script>
jQuery(document).ready(function($) {
	// Kunden synchronisieren
	$('#app-crm-sync-customers').on('click', function() {
		var button = $(this);
		var result = $('#app-crm-sync-result');
		
		button.prop('disabled', true);
		result.removeClass('success error').addClass('loading').text('<?php _e( 'Synchronisiere Kunden...', 'appointments' ); ?>');
		
		$.ajax({
			url: ajaxurl,
			type: 'POST',
			data: {
				action: 'app_crm_sync_customers',
				nonce: '<?php echo wp_create_nonce( "app_crm_sync" ); ?>'
			},
			success: function(response) {
				if (response.success) {
					result.removeClass('loading').addClass('success').text(response.data.message);
					setTimeout(function() {
						location.reload();
					}, 2000);
				} else {
					result.removeClass('loading').addClass('error').text(response.data.message);
				}
			},
			error: function() {
				result.removeClass('loading').addClass('error').text('<?php _e( 'Ein Fehler ist aufgetreten.', 'appointments' ); ?>');
			},
			complete: function() {
				button.prop('disabled', false);
			}
		});
	});
	
	// Termine synchronisieren
	$('#app-crm-sync-appointments').on('click', function() {
		var button = $(this);
		var result = $('#app-crm-sync-result');
		
		button.prop('disabled', true);
		result.removeClass('success error').addClass('loading').text('<?php _e( 'Synchronisiere Termine...', 'appointments' ); ?>');
		
		$.ajax({
			url: ajaxurl,
			type: 'POST',
			data: {
				action: 'app_crm_sync_appointments',
				nonce: '<?php echo wp_create_nonce( "app_crm_sync" ); ?>'
			},
			success: function(response) {
				if (response.success) {
					result.removeClass('loading').addClass('success').text(response.data.message);
					setTimeout(function() {
						location.reload();
					}, 2000);
				} else {
					result.removeClass('loading').addClass('error').text(response.data.message);
				}
			},
			error: function() {
				result.removeClass('loading').addClass('error').text('<?php _e( 'Ein Fehler ist aufgetreten.', 'appointments' ); ?>');
			},
			complete: function() {
				button.prop('disabled', false);
			}
		});
	});
});
</script>
