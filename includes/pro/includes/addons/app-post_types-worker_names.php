<?php
/*
Plugin Name: Namen der Dienstanbieter
Description: Hier kannst Du auswählen, wie ein Dienstanbieter Deinen Clienten vorgestellt wird.
Plugin URI: https://cp-psource.github.io/terminmanager-pro/
Version: 1.1
AddonType: Post Types
Author: PSOURCE
*/

class App_PostTypes_ServiceProviderNames {

	private $_data;

	private function __construct () {}

	public static function serve () {
		$me = new App_PostTypes_ServiceProviderNames;
		$me->_add_hooks();
	}

	private function _add_hooks () {
		add_action('plugins_loaded', array($this, 'initialize'));
		
		add_filter('app_get_worker_name', array($this, 'filter_worker_names'), 10, 2);

		add_action('appointments_settings_tab-main-section-advanced', array($this, 'show_settings'));
		add_filter('app-options-before_save', array($this, 'save_settings'));
	}

	public function initialize () {
		global $appointments;
		$this->_data = $appointments->options;
	}

	public function filter_worker_names ($name, $worker_id) {
		if (!$worker_id) return $name;

		$default = !empty($this->_data['worker_name_format']) ? $this->_data['worker_name_format'] : false;
		$fallback = !empty($this->_data['worker_fallback_name_format']) ? $this->_data['worker_fallback_name_format'] : false;

		if (!$default && !$fallback) return $name;

		$new_name = $this->_format_to_name($default, $worker_id);
		if (empty($new_name)) $new_name = $this->_format_to_name($fallback, $worker_id);

		return !empty($new_name)
			? $new_name
			: $name
		;
	}

	private function _format_to_name ($format, $user_id) {
		$user = get_userdata($user_id);
		if (!is_object($user)) return false;

		$name = false;
		
		if ('display_name' == $format) {
			$name = !empty($user->display_name) ? $user->display_name : false;
		} else if ('nickname' == $format) {
			$name = !empty($user->nickname) ? $user->nickname : false;
		} else if ('first_last' == $format) {
			$name = sprintf(
				'%s %s',
				(!empty($user->first_name) ? $user->first_name : false),
				(!empty($user->last_name) ? $user->last_name : false)
			);
		} else if ('first_last_comma' == $format) {
			$name = sprintf(
				'%s, %s',
				(!empty($user->first_name) ? $user->first_name : false),
				(!empty($user->last_name) ? $user->last_name : false)
			);
		} else if ('last_first' == $format) {
			$name = sprintf(
				'%s %s',
				(!empty($user->last_name) ? $user->last_name : false),
				(!empty($user->first_name) ? $user->first_name : false)
			);
	} else if ('last_first_comma' == $format) {
			$name = sprintf(
				'%s, %s',
				(!empty($user->last_name) ? $user->last_name : false),
				(!empty($user->first_name) ? $user->first_name : false)
			);
		} else if ('appointments' == $format) {
			$name = !empty($user->app_name) ? $user->app_name : false;
		}

		return trim($name, ' ,');
	}

	public function save_settings ($options) {
		if (!empty($_POST['worker_name_format'])) $options['worker_name_format'] = sanitize_text_field($_POST['worker_name_format']);
		if (!empty($_POST['worker_fallback_name_format'])) $options['worker_fallback_name_format'] = sanitize_text_field($_POST['worker_fallback_name_format']);
		return $options;
	}

	public function show_settings () {
		$name_formats = array(
			'appointments' => __('Wie unter Terminmanager Einstellungen im Profil festgelegt (Standard)', 'appointments'),
			'display_name' => __('Anzeigename des Benutzers', 'appointments'),
			'nickname' => __('Nickname', 'appointments'),
			'first_last' => __('Vorname, gefolgt von Nachname, durch Leerzeichen getrennt', 'appointments'),
			'first_last_comma' => __('Vorname, gefolgt von Nachname, durch Kommas getrennt', 'appointments'),
			'last_first' => __('Nachname, gefolgt von Vorname, durch Leerzeichen getrennt', 'appointments'),
			'last_first_comma' => __('Nachname, gefolgt von Vorname, durch Kommas getrennt', 'appointments'),
		);
		$default = !empty($this->_data['worker_name_format']) ? $this->_data['worker_name_format'] : false;
		$fallback = !empty($this->_data['worker_fallback_name_format']) ? $this->_data['worker_fallback_name_format'] : false;
		?>
		<h3><?php _e( 'Anzeigenamen der Provider', 'appointments' ); ?></h3>
		<table class="form-table">
			<tr>
				<th scope="row" ><label for="worker_name_format"><?php _e('Standard', 'appointments'); ?></label></th>
				<td>
					<select name="worker_name_format" id="worker_name_format">
					<?php foreach ($name_formats as $format => $label) { ?>
						<option value="<?php esc_attr_e($format); ?>" <?php selected($format, $default); ?> >
							<?php echo $label; ?>
						</option>
					<?php } ?>
					</select>
					<p class="description"><?php _e('Dies ist das Namensformat, das standardmäßig für Deine Dienstanbieter verwendet wird.', 'appointments') ?></p>
				</td>
			</tr>
			<tr>
				<th scope="row" ><label for="worker_fallback_name_format"><?php _e('Fallback', 'appointments'); ?></label></th>
				<td>
					<select name="worker_fallback_name_format" id="worker_fallback_name_format">
					<?php foreach ($name_formats as $format => $label) { ?>
						<option value="<?php esc_attr_e($format); ?>" <?php selected($format, $fallback); ?> >
							<?php echo $label; ?>
						</option>
					<?php } ?>
					</select>
					<p class="description"><?php _e('Dies ist das Namensformat, das als Fallback verwendet wird, falls der Standardwert nicht festgelegt ist.', 'appointments') ?></p>
				</td>
			</tr>
		</table>

		<?php
	}
}
App_PostTypes_ServiceProviderNames::serve();