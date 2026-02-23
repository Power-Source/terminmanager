<?php


/**
 * Services dropdown list shortcode.
 */
class App_Shortcode_Services extends App_Shortcode {
	public function __construct() {
		$this->name = __( 'Services', 'appointments' );
	}

	public function get_defaults() {
		$_workers = appointments_get_workers();
		$workers = array(
			array( 'text' => __( 'Jeder Dienstleister', 'appointments' ), 'value' => 0 ),
		);
		foreach ( $_workers as $worker ) {
			/** @var Appointments_Worker $worker */
			$workers[] = array( 'text' => $worker->get_name(), 'value' => $worker->ID );
		}

		return array(
			'select' => array(
				'type' => 'text',
				'name' => __( 'Titel', 'appointments' ),
				'value' => __( 'Bitte wähle einen Service:', 'appointments' ),
				'help' => __( 'Text über dem Auswahlmenü. Standard: "Bitte wähle einen Service".', 'appointments' ),
			),
			'show' => array(
				'type' => 'text',
				'name' => __( 'Schaltflächentext anzeigen', 'appointments' ),
				'value' => __( 'Verfügbare Zeiten anzeigen', 'appointments' ),
				'help' => __( 'Schaltflächentext, um die Ergebnisse für die ausgewählten anzuzeigen. Standard: "Verfügbare Zeiten anzeigen".', 'appointments' ),
				'example' => __( 'Verfügbare Zeiten anzeigen', 'appointments' ),
			),
			'description' => array(
				'type' => 'select',
				'name' => __( 'Beschreibung', 'appointments' ),
				'value' => 'excerpt',
				'help' => __( 'Wählt aus, welcher Teil der Beschreibungsseite im Dropdown-Menü angezeigt wird, wenn ein Dienst ausgewählt wird . Wählbare Werte sind "none", "excerpt", "content". Standard: "excerpt".', 'appointments' ),
				'options' => array(
					array( 'text' => 'Excerpt', 'value' => 'excerpt' ),
					array( 'text' => 'None', 'value' => '' ),
					array( 'text' => 'Content', 'value' => 'content' ),
				),
			),
			'thumb_size' => array(
				'type' => 'text',
				'name' => __( 'Thumbnail Größe', 'appointments' ),
				'value' => '96,96',
				'help' => __( 'Fügt die Miniaturansicht des Beitrags ein, wenn die Seite ein Bild enthält. Wählbare Werte sind "none", "thumbnail", "medium", "full" oder 2 durch Komma getrennte Zahlen, die Breite und Höhe in Pixeln darstellen, z.B. 32,32. Standard: "96,96".', 'appointments' ),
			),
			'thumb_class' => array(
				'type' => 'text',
				'name' => __( 'Thumbnail Class', 'appointments' ),
				'value' => 'alignleft',
				'help' => __( 'CSS-Klasse, die auf das Miniaturbild angewendet wird. Standard: "alignleft".', 'appointments' ),
			),
			'autorefresh' => array(
				'type' => 'checkbox',
				'name' => __( 'Automatische Aktualisierung', 'appointments' ),
				'value' => 0,
				'help' => __( 'Wenn diese Option aktiviert ist, wird die Schaltfläche Anzeigen nicht angezeigt und die Seite wird automatisch aktualisiert, wenn der Client die Auswahl ändert. Hinweis: Der Client kann die Auswahl nicht durchsuchen und daher die Beschreibungen im laufenden Betrieb überprüfen (ohne dass die Seite aktualisiert wird). Standard: disabled.', 'appointments' ),
			),
			'order_by' => array(
				'type' => 'select',
				'name' => __( 'Sortieren nach', 'appointments' ),
				'value' => 'ID',
				'options' => array(
					array( 'text' => 'ID', 'value' => 'ID' ),
					array( 'text' => 'ID DESC', 'value' => 'ID ASC' ),
					array( 'text' => 'name', 'value' => 'name' ),
					array( 'text' => 'name DESC', 'value' => 'name DESC' ),
					array( 'text' => 'duration', 'value' => 'duration' ),
					array( 'text' => 'duration DESC', 'value' => 'duration DESC' ),
				),
				'help' => __( 'Sortierreihenfolge der Dienste. Mögliche Werte: ID, Name, Dauer, Preis. Optional kann DESC (absteigend) verwendet werden, z.B. "name DESC" kehrt die Reihenfolge um. Default: "ID".', 'appointments' ),
			),
			'worker' => array(
				'type' => 'select',
				'name' => __( 'Dienstleister', 'appointments' ),
				'value' => 0,
				'options' => $workers,
				'help' => __( 'In einigen Fällen möchtest Du möglicherweise Dienste anzeigen, die nur von einem bestimmten Anbieter bereitgestellt werden. In diesem Fall gib hier die Provider-ID ein. Standard: "0" (alle definierten Dienste). Hinweis: Mehrfachauswahl ist nicht zulässig.', 'appointments' ),
			),
			'ajax' => array(
				'type' => 'checkbox',
				'name' => __( 'AJAX', 'appointments' ),
				'value' => 0,
				'help' => __( 'Wenn diese Option aktiviert ist, werden die Miniaturansichten und Beschreibungen der Dienste von AJAX geladen. Empfohlen für Websites mit vielen Diensten.', 'appointments' ),
			),
			'_noscript' => array( 'value' => 0 ),

		);
	}

	public function get_usage_info() {
		return __( 'Erstellt ein Dropdown-Menü mit verfügbaren Diensten.', 'appointments' );
	}

	public function process_shortcode( $args = array(), $content = '' ) {
		global $appointments;

		$args = wp_parse_args( $args, $this->_defaults_to_args() );
		extract( $args );

		if ( ! trim( $args['order_by'] ) ) {
			$args['order_by'] = 'ID';
		}

		if ( $args['worker'] && appointments_get_worker( $args['worker'] ) ) {
			$services = appointments_get_worker_services( $args['worker'] );

			// Re-sort worker services
			if ( ! empty( $services ) && ! empty( $args['order_by'] ) && 'ID' !== $args['order_by'] ) {
				$services = $this->_reorder_services( $services, $args['order_by'] );
			}
		} else {
			$services = appointments_get_services( array( 'orderby' => $args['order_by'] ) );
		}

		$services = apply_filters( 'app_services', $services );

		// If there are no workers do nothing
		if ( ! $services || empty( $services ) ) {
			return ''; }

		$selected_service = 0;
		if ( isset( $_REQUEST['app_service_id'] ) && appointments_get_service( $_REQUEST['app_service_id'] ) ) {
			$selected_service = absint( $_REQUEST['app_service_id'] );
		} elseif ( $args['worker'] && appointments_get_worker( $args['worker'] ) ) {
			$selected_service = $services[0]->ID;
		}

		$selected_service = apply_filters( 'appointments_services_shortcode_selected_service', $selected_service, $args, $services );

		ob_start();
		?>
		<div class="app_services">
			<div class="app_services_dropdown">
				<div class="app_services_dropdown_title" id="app_services_dropdown_title">
					<?php echo $args['select']; ?>
				</div>
				<div class="app_services_dropdown_select">
					<select id="app_select_services" name="app_select_services" class="app_select_services">
<?php
foreach ( $services as $service ) {
	if ( 0 === $selected_service ) {
		$selected_service = $service->ID;
	}
?>
					<option value="<?php echo $service->ID; ?>" <?php selected( $service->ID, $selected_service ); ?>><?php echo stripslashes( $service->name ); ?></option>
				<?php } ?>
					</select>
					<input type="button" class="app_services_button" value="<?php echo esc_attr( $args['show'] ); ?>">
				</div>
			</div>

			<div class="app_service_excerpts">
				<?php if ( $args['autorefresh'] ) :  // Only display the selected service ?>
					<?php
						$service = appointments_get_service( $selected_service );
					if ( $service ) {
						$page = apply_filters( 'app_service_page', $service->page, $service->ID );
						?>
							<div class="app_service_excerpt" id="app_service_excerpt_<?php echo $service->ID; ?>">
								<?php
								$service_description = '';
								switch ( $args['description'] ) {
									case 'none': {
										break;
									}
									case 'content': {
										$service_description = $appointments->get_content( $page, $args['thumb_size'], $args['thumb_class'], $service->ID );
										break;
									}
									default: {
										$service_description = $appointments->get_excerpt( $page, $args['thumb_size'], $args['thumb_class'], $service->ID );
										break;
									}
								}
								echo apply_filters( 'app-services-service_description', $service_description, $service, $args['description'] );
								?>
								</div>
							<?php
					}

					?>
				<?php else : ?>
					<?php foreach ( $services as $service ) :  ?>
						<?php $page = apply_filters( 'app_service_page', $service->page, $service->ID ); ?>
						<div <?php echo $service->ID != $selected_service ? 'style="display:none"' : ''; ?> class="app_service_excerpt" id="app_service_excerpt_<?php echo $service->ID; ?>">
							<?php
							$service_description = '';
							switch ( $args['description'] ) {
								case 'none': {
									break;
								}
								case 'content': {
									$service_description = $appointments->get_content( $page, $args['thumb_size'], $args['thumb_class'], $service->ID, absint( $args['ajax'] ) );
									break;
								}
								default: {
									$service_description = $appointments->get_excerpt( $page, $args['thumb_size'], $args['thumb_class'], $service->ID, absint( $args['ajax'] ) );
									break;
								}
							}
							echo apply_filters( 'app-services-service_description', $service_description, $service, $args['description'] );
							?>
						</div>
					<?php endforeach; ?>
				<?php endif; ?>

			</div>
		</div>
		<?php

		$s = ob_get_clean();

		$wcalendar = isset( $_GET['wcalendar'] ) && (int) $_GET['wcalendar']
			? (int) $_GET['wcalendar']
			: false
		;

		// First remove these parameters and add them again to make wcalendar appear before js variable
		$href = add_query_arg( array( 'wcalendar' => false, 'app_provider_id' => false, 'app_service_id' => false ) );
		$href = add_query_arg( array( 'wcalendar' => $wcalendar, 'app_service_id' => '__selected_service__' ), $href );
		if ( isset( $_GET['app_provider_id'] ) ) {
			$href = add_query_arg( 'app_provider_id', $_GET['app_provider_id'], $href );
		}

		$href = apply_filters( 'app_service_href', $href );
		$href = $this->_js_esc_url( $href ) . '#app_services_dropdown_title';

		if ( ! $args['_noscript'] ) {
			wp_enqueue_script( 'app-shortcode-services', appointments_plugin_url() . 'includes/shortcodes/js/app-services.js', array( 'jquery' ) );

			$ajax_url = admin_url( 'admin-ajax.php' );
			if ( ! is_ssl() && force_ssl_admin() ) {
				$ajax_url = admin_url( 'admin-ajax.php', 'http' );
			}

			$i10n = array(
				'size' => $args['thumb_size'],
				'worker' => $args['worker'],
				'ajaxurl' => $ajax_url,
				'thumbclass' => $args['thumb_class'],
				'autorefresh' => $args['autorefresh'],
				'ajax' => $args['ajax'],
				'first_service_id' => appointments_get_services_min_id(),
				'reload_url' => $href,
			);
			wp_localize_script( 'app-shortcode-services', 'appointmentsStrings', $i10n );
		}

		return $s;
	}

	/**
	 * Escape the URL, but convert back search query entities (i.e. ampersands)
	 *
	 * @param string $raw Raw URL to parse
	 *
	 * @return string Usable URL
	 */
	private function _js_esc_url( $raw = '' ) {
		$url = esc_url( $raw );
		$parts = explode( '?', $url );

		if ( empty( $parts[1] ) ) { return $url; }
		if ( false === strpos( $parts[1], '#038;' ) && false === strpos( $parts[1], '&amp;' ) ) { return $url; }

		$parts[1] = preg_replace( '/&(#038|amp);/', '&', $parts[1] );

		return join( '?', $parts );
	}

	/**
	 * Sort the services when we can't do so via SQL
	 */
	private function _reorder_services( $services, $order ) {
		if ( empty( $services ) ) { return $services; }
		list($by,$direction) = explode( ' ', trim( $order ), 2 );

		$by = trim( $by ) ? trim( $by ) : 'ID';
		$by = in_array( $by, array( 'ID', 'name', 'capacity', 'duration', 'price', 'page' ) )
			? $by
			: 'ID'
		;

		$direction = trim( $direction ) ? strtoupper( trim( $direction ) ) : 'ASC';
		$direction = in_array( $direction, array( 'ASC', 'DESC' ) )
			? $direction
			: 'ASC'
		;

		$comparator = 'ASC' === $direction
			? create_function( '$a, $b', "return strnatcasecmp(\$a->{$by}, \$b->{$by});" )
			: create_function( '$a, $b', "return strnatcasecmp(\$b->{$by}, \$a->{$by});" );
		usort( $services, $comparator );

		return $services;
	}
}
