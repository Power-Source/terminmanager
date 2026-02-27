<?php
/**
 * Welcome Notice System
 * Replacement for jQuery UI pointer-based tutorials
 */

class App_Welcome_Notice {

	private static $instance = null;

	public static function serve() {
		if (self::$instance === null) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		add_action('admin_notices', array($this, 'show_welcome_notice'));
		add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
		add_action('wp_ajax_app_dismiss_welcome_notice', array($this, 'ajax_dismiss_notice'));
	}

	/**
	 * Enqueue scripts and styles
	 */
	public function enqueue_scripts($hook) {
		$screen = get_current_screen();
		$title = sanitize_title(__('Terminmanager', 'appointments'));
		
		if (strpos($screen->base, 'appointments') !== false || strpos($screen->base, $title) !== false) {
			wp_enqueue_script(
				'app-welcome-notice',
				appointments_plugin_url() . 'admin/js/app-welcome-notice.js',
				array('jquery'),
				appointments_get_db_version(),
				true
			);
			
			wp_localize_script('app-welcome-notice', 'appWelcomeNotice', array(
				'nonce' => wp_create_nonce('app_welcome_notice_nonce')
			));
		}
	}

	/**
	 * Show welcome notice for new users
	 */
	public function show_welcome_notice() {
		if (!current_user_can('manage_options')) {
			return;
		}

		$screen = get_current_screen();
		$title = sanitize_title(__('Terminmanager', 'appointments'));
		
		// Only show on Appointments pages
		if (strpos($screen->base, 'appointments') === false && strpos($screen->base, $title) === false) {
			return;
		}

		// Check if user has dismissed the notice
		$dismissed = get_user_meta(get_current_user_id(), 'app_welcome_notice_dismissed', true);
		if ($dismissed) {
			return;
		}

		// Show the notice
		$this->render_welcome_notice();
	}

	/**
	 * Render the welcome notice HTML
	 */
	private function render_welcome_notice() {
		$settings_url = admin_url('admin.php?page=app_settings');
		?>
		<div class="notice notice-info is-dismissible app-welcome-notice" data-notice-id="welcome">
			<h2><?php _e('Willkommen beim PS Terminmanager!', 'appointments'); ?></h2>
			<p><?php _e('Vielen Dank, dass Du den Terminmanager verwendest. Hier sind einige schnelle Tipps für den Einstieg:', 'appointments'); ?></p>
			<ul style="list-style: disc; margin-left: 20px;">
				<li><strong><?php _e('Zeitbasis festlegen:', 'appointments'); ?></strong> <?php _e('Definiere die Mindestdauer für Termine in den Einstellungen.', 'appointments'); ?></li>
				<li><strong><?php _e('Dienste erstellen:', 'appointments'); ?></strong> <?php _e('Füge die Dienstleistungen hinzu, die Du anbietest.', 'appointments'); ?></li>
				<li><strong><?php _e('Mitarbeiter hinzufügen:', 'appointments'); ?></strong> <?php _e('Lege fest, wer Termine durchführen kann.', 'appointments'); ?></li>
				<li><strong><?php _e('Frontend-Seite erstellen:', 'appointments'); ?></strong> <?php _e('Erstelle eine Seite mit dem Terminbuchungs-Shortcode.', 'appointments'); ?></li>
			</ul>
			<p>
				<a href="<?php echo esc_url($settings_url); ?>" class="button button-primary app-quick-setup">
					<?php _e('Schnell-Einrichtung starten', 'appointments'); ?>
				</a>
				<a href="<?php echo esc_url(admin_url('admin.php?page=app_faq')); ?>" class="button button-secondary">
					<?php _e('FAQ anzeigen', 'appointments'); ?>
				</a>
			</p>
		</div>
		<style>
		.app-welcome-notice h2 {
			margin-top: 0.5em;
			color: #2271b1;
		}
		.app-welcome-notice ul {
			margin: 1em 0;
		}
		.app-welcome-notice li {
			margin-bottom: 0.5em;
		}
		</style>
		<?php
	}

	/**
	 * Handle AJAX request to dismiss notice
	 */
	public function ajax_dismiss_notice() {
		check_ajax_referer('app_welcome_notice_nonce', 'nonce');

		if (!current_user_can('manage_options')) {
			wp_send_json_error();
		}

		$notice_id = isset($_POST['notice_id']) ? sanitize_key($_POST['notice_id']) : '';
		
		if ($notice_id === 'welcome') {
			update_user_meta(get_current_user_id(), 'app_welcome_notice_dismissed', true);
			wp_send_json_success();
		}

		wp_send_json_error();
	}
}
