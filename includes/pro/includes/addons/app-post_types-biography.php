<?php
/*
Plugin Name: Biografie Beitragstyp
Description: Ermöglicht die Auswahl eines Beitragstyps für die Biografien Deiner Dienstanbieter (verfügbar unter Einstellungen &gt; Allgemein &gt; Erweiterte Einstellungen)
Plugin URI: https://cp-psource.github.io/terminmanager-pro/
Version: 1.1
AddonType: Post Types
Author: PSOURCE
*/

class App_PostTypes_Biography {

	const POST_TYPE = 'page';
	private $_data;

	private function __construct () {}

	public static function serve () {
		$me = new App_PostTypes_Biography;
		$me->_add_hooks();
	}

	private function _add_hooks () {
		add_action('plugins_loaded', array($this, 'initialize'));
		add_filter('app-biography_pages-get_list', array($this, 'get_biographies'));

		add_action('appointments_settings_tab-main-section-advanced', array($this, 'show_settings'));
		add_filter('app-options-before_save', array($this, 'save_settings'));
	}

	public function initialize () {
		global $appointments;
		$this->_data = $appointments->options;
	}

	public function get_biographies () {
		$post_type = $this->_get_post_type();
		$query = new WP_Query(array(
			'post_type' => $post_type,
			'posts_per_page' => -1,
		));
		return $query->posts;
	}

	public function save_settings ($options) {
		if (!empty($_POST['biography_post_type'])) $options['biography_post_type'] = $_POST['biography_post_type'];
		return $options;
	}

	public function show_settings () {
		$post_types = get_post_types(array(
			'public' => true,
		), 'objects');
		$bio = $this->_get_post_type();
		?>
		<h3><?php _e( 'Einstellungen für den Biografie-Beitragstyp', 'appointments' ); ?></h3>
		<table class="form-table">
			<tr>
				<th scope="row" ><label for="biography_post_type"><?php _e('Biografie Post Typ', 'appointments')?></label></th>
				<td>
					<select id="biography_post_type" name="biography_post_type">
					<?php foreach ($post_types as $type => $obj) { ?>
						<option value="<?php esc_attr_e($type); ?>" <?php selected($type, $bio); ?> >
							<?php echo $obj->labels->singular_name; ?>
						</option>
					<?php } ?>
					</select>
					<p class="description"><?php _e('Dies ist der Beitragstyp, der als Biografie für Deine Dienstanbieter verwendet wird.', 'appointments') ?></p>
				</td>
			</tr>
		</table>
		<?php
	}

	private function _get_post_type () {
		return !empty($this->_data['biography_post_type']) ? $this->_data['biography_post_type'] : self::POST_TYPE;
	}
}
App_PostTypes_Biography::serve();