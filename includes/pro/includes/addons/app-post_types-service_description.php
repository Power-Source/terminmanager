<?php
/*
Plugin Name: Servicebeschreibung Beitragstyp
Description: Ermöglicht die Auswahl eines Beitragstyps für Deine Servicebeschreibungen (verfügbar unter Einstellungen &gt; Allgemein &gt; Erweiterte Einstellungen)
Plugin URI: https://cp-psource.github.io/terminmanager-pro/
Version: 1.1
AddonType: Post Types
Author: PSOURCE
*/

class App_PostTypes_ServiceDescription {

	const POST_TYPE = 'page';
	private $_data;

	private function __construct () {}

	public static function serve () {
		$me = new App_PostTypes_ServiceDescription;
		$me->_add_hooks();
	}

	private function _add_hooks () {
		add_action('plugins_loaded', array($this, 'initialize'));
		add_filter('app-service_description_pages-get_list', array($this, 'get_descriptions'));

		add_action('appointments_settings_tab-main-section-advanced', array($this, 'show_settings'));
		add_filter('app-options-before_save', array($this, 'save_settings'));

		add_filter( 'appointments_service_page_post_types_allowed', array( $this, 'allow_post_type_in_services' ) );
	}

	public function initialize () {
		global $appointments;
		$this->_data = $appointments->options;
	}

	public function get_descriptions () {
		$post_type = $this->_get_post_type();
		$query = new WP_Query(array(
			'post_type' => $post_type,
			'posts_per_page' => -1,
		));
		return $query->posts;
	}

	public function allow_post_type_in_services( $allowed ) {
		return array_merge( array( $this->_get_post_type() ), $allowed );
	}

	public function save_settings ($options) {
		if (!empty($_POST['service_description_post_type'])) $options['service_description_post_type'] = $_POST['service_description_post_type'];
		return $options;
	}

	public function show_settings () {
		$post_types = get_post_types(array(
			'public' => true,
		), 'objects');
		$bio = $this->_get_post_type();
		?>
		<h3><?php _e( 'Service Beschreibung Beitragstyp Einstellungen', 'appointments' ); ?></h3>
		<table class="form-table">
			<tr valign="top">
				<th scope="row" ><label for="service_description_post_type"><?php _e('Servicebeschreibung Beitragstyp', 'appointments')?></label></th>
				<td>
					<select name="service_description_post_type" id="service_description_post_type">
					<?php foreach ($post_types as $type => $obj) { ?>
						<option value="<?php esc_attr_e($type); ?>" <?php selected($type, $bio); ?> >
							<?php echo $obj->labels->singular_name; ?>
						</option>
					<?php } ?>
					</select>
					<p class="description"><?php _e('Dies ist der Beitragstyp, der als Beschreibung für Deine Dienste verwendet wird.', 'appointments') ?></p>
				</td>
			</tr>
		</table>
		<?php
	}

	private function _get_post_type () {
		return !empty($this->_data['service_description_post_type']) ? $this->_data['service_description_post_type'] : self::POST_TYPE;
	}
}
App_PostTypes_ServiceDescription::serve();