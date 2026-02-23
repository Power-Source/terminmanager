<?php
/*
Plugin Name: Dashboard-Widget Meine Termine.
Description: Zeigt meine Termine im Dashboard-Widget an
Plugin URI: https://cp-psource.github.io/terminmanager-pro/
Version: 1.0
AddonType: Dashboard Widget
Author: PSOURCE
*/

class App_my_appointments_dashboard_widget {
	private $_data;
	private $_core;

	private function __construct () {
		
	}

	public static function serve () {
		$me = new App_my_appointments_dashboard_widget; // Korrektur: Richtige Klasse verwenden
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
			'appointments_widget',         // Widget slug.
			'My Appointments',         // Title.
			array( $this, 'appointments_widget_cb' ) // Display function.
	       );
		
	}
	
	public function appointments_widget_cb() {
		global $current_user;
		?>
		<?php echo do_shortcode("[app_my_appointments allow_cancel=0 client_id=".$current_user->ID."]") ?>
		<script type="text/javascript">
		jQuery(function($){
			$('#appointments_widget').prependTo($('#dashboard-widgets'));
		});
		</script>
		<style>
		#appointments_widget table.my-appointments{width: 100%;}
		#appointments_widget table.my-appointments th{text-align: left !important;}
		</style>
		<?php
	}
	
}
App_my_appointments_dashboard_widget::serve();
